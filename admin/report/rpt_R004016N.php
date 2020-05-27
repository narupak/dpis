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
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "g.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" : 
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break; 
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; 	}
		else if($select_org_structure==1){	$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; }
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; } 
	}

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

	if($search_year_min && $search_year_max) $show_year = "||ตั้งแต่ปี $search_year_min - $search_year_max";
	elseif($search_year_min) $show_year = "||ตั้งแต่ปี $search_year_min";
	elseif($search_year_max) $show_year = "||ถึงปี $search_year_max";

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานข้อมูล$PERSON_TYPE[$search_per_type]ที่ลาไปศึกษาในประเทศ จำแนกตาม$ORG_TITLE". $show_year;
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0416";
	include ("rpt_R004016_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R004016_rtf.rtf";

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

	function count_person($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type, $search_year_min, $search_year_max, $select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($level_no){
			$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
			$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.CT_CODE) = '140')";	//ศึกษาในประเทศ***
			if($DPISDB=="odbc"){ 
				if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) >= '".($search_year_min - 543)."')";
				if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) <= '".($search_year_max - 543)."')";
			}elseif($DPISDB=="oci8"){
				if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(SUBSTR(trim(d.SC_STARTDATE), 1, 4) >= '".($search_year_min - 543)."')";
				if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(SUBSTR(trim(d.SC_STARTDATE), 1, 4) <= '".($search_year_max - 543)."')";
			}elseif($DPISDB=="mysql"){
				if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) >= '".($search_year_min - 543)."')";
				if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) <= '".($search_year_max - 543)."')";
			} // end if
		} // end if

		// นับการลาศึกษาจาก PER_SCHOLAR
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from		(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							group by		a.PER_ID ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e, PER_ORG g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.PER_ID=d.PER_ID(+) and d.INS_CODE=e.INS_CODE(+) and a.DEPARTMENT_ID=g.ORG_ID
												$search_condition
							 group by 	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from		(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							group by		a.PER_ID ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo "count cmd=$cmd ($count_person)<br>";
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

/*****************
		//ไม่นับรวม*********	
		if($level_no){
			// นับการลาฝึกอบรมจาก PER_TRAIN
			$search_condition = str_replace("d.SC_STARTDATE", "d.TRN_STARTDATE", $search_condition);
			$search_condition = str_replace("e.CT_CODE", "d.CT_CODE", $search_condition);
			$search_condition .= (trim($search_condition)?" and ":" where ") . "(e.TR_TYPE in (1))";

			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_TRAINING d on (a.PER_ID=d.PER_ID)
													) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
								$search_condition
								group by		a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
													and a.PER_ID=d.PER_ID(+) and d.TR_CODE=e.TR_CODE(+)
													$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_TRAINING d on (a.PER_ID=d.PER_ID)
													) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
								$search_condition
								group by		a.PER_ID ";
			} // end if	
			if($select_org_structure==1) { 
				$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
				$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			}
			$count_person_2 = $db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			//echo $cmd."<hr>";
			if($count_person_2==1){
				$data = $db_dpis2->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if($data[count_person] == 0) $count_person += 0;
			}else{
				$count_person += $count_person_2;
			} // end if
		} // end if
************/
		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
//				case "MINISTRY" :
//					if($MINISTRY_ID && $MINISTRY_ID!=-1 && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
//					break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
					break;
				case "ORG" :	
					if($select_org_structure==0){	// โครงสร้างตามกฏหมาย
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){	// โครงสร้างตามมอบหมายงาน
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

//		$addition_condition = "";
		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
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
			} // end switch case
		} // end for
	} // function
	
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from		(
											(
												PER_PERSONAL a 
												left join $position_table b on ($position_join) 
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(
											(
												PER_PERSONAL a 
												left join $position_table b on ($position_join) 
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	unset($GRAND_LEVEL);
	$GRAND_ALL = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["count_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_LEVEL[$tmp_level_no] += $arr_content[$data_count]["count_".$tmp_level_no];
							//$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							//if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for

						$arr_content[$data_count][count_all] = count_person(0, $search_condition, $addition_condition);
						if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_ALL += $arr_content[$data_count][count_all];
//						echo "1..rpt_order_index($rpt_order_index)==".(count($arr_rpt_order)-1)." (GRAND_ALL=$GRAND_ALL)<br>";

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
					break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
							if($DEPARTMENT_NAME=="-")	$DEPARTMENT_NAME = $DEPARTMENT_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["count_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_LEVEL[$tmp_level_no] += $arr_content[$data_count]["count_".$tmp_level_no];
							//$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							//if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for

						$arr_content[$data_count][count_all] = count_person(0, $search_condition, $addition_condition);
						if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_ALL += $arr_content[$data_count][count_all];
//						echo "2..rpt_order_index($rpt_order_index)==".(count($arr_rpt_order)-1)." (GRAND_ALL=$GRAND_ALL)<br>";

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
					break;
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["count_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_LEVEL[$tmp_level_no] += $arr_content[$data_count]["count_".$tmp_level_no];
							//$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							//if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for

						$arr_content[$data_count][count_all] = count_person(0, $search_condition, $addition_condition);
						if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_ALL += $arr_content[$data_count][count_all];
//						echo "3..rpt_order_index($rpt_order_index)==".(count($arr_rpt_order)-1)." (GRAND_ALL=$GRAND_ALL)<br>";

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for		
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
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
	//	echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
	}
	if (!$result) echo "****** error ****** on open table for $table<br>";
		
	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
//			echo "data_count($data_count)==>[type]=".$arr_content[$data_count][type]."<br>";
			$COUNT_TOTAL = 0;
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				${"COUNT_".$tmp_level_no} = $arr_content[$data_count]["count_".$tmp_level_no];
				$COUNT_TOTAL += ${"COUNT_".$tmp_level_no};
			}
			$COUNT_ALL = $arr_content[$data_count][count_all];
			$PERCENT_TOTAL = "";
			if($COUNT_ALL) $PERCENT_TOTAL = ($COUNT_TOTAL / $COUNT_ALL) * 100;

			$arr_data = (array) null;
			$arr_data[] = $NAME;
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				$arr_data[] = ${"COUNT_".$tmp_level_no};
			} // end for
			$arr_data[] = $COUNT_TOTAL;
			$arr_data[] = $COUNT_ALL;
			$arr_data[] = $PERCENT_TOTAL;
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for

		$GRAND_TOTAL = array_sum($GRAND_LEVEL);
		$PERCENT_TOTAL = "";
		if($GRAND_ALL) $PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_ALL) * 100;

		$arr_data = (array) null;
		$arr_data[] = "รวม";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_data[] = $GRAND_LEVEL[$tmp_level_no];
		} // end for
		$arr_data[] = $GRAND_TOTAL;
		$arr_data[] = $GRAND_ALL;
		$arr_data[] = $PERCENT_TOTAL;
		if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
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