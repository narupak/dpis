<?
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// ��˹�����ä���� set_of_colors
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
	if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
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

				$heading_name .= " ��ǹ�Ҫ���";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " ��ǹ�Ҫ���";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				$heading_name .= " ��ǹ�Ҫ���";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.PL_SEQ_NO, a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.PL_SEQ_NO, a.PL_CODE";

				$heading_name .= " $PL_TITLE";
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
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
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
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_LINE", $list_type)){
		// ��§ҹ
		$list_type_text = "";
		$search_pl_code = trim($search_pl_code);
		$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
		$list_type_text .= "$search_pl_name";
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ����� , �ѧ��Ѵ
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
	if(in_array("ALL", $list_type) || !isset($list_type)){	//�óշ����� ������������͡ check box list_type ���
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
	if ($BKK_FLAG==1)
		$report_title = "$DEPARTMENT_NAME||�ӹǹ����Ҫ��á�ا෾��ҹ�� ��ṡ�����§ҹ �� ����дѺ���˹�";
	else
		$report_title = "$DEPARTMENT_NAME||�ӹǹ����Ҫ��þ����͹ ��ṡ�����§ҹ �� ����дѺ���˹�";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R1034";
	include ("rpt_R010034_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
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

		$fname= "rpt_R010034_rtf.rtf";

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

	function count_person($gender, $budget_year, $level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $select_org_structure;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		
		$gender_condition = "d.PER_GENDER='$gender'";

		$search_condition = str_replace(" where ", " and ", $search_condition);

		if($DPISDB=="odbc"){
			$cmd = " select			count(d.PER_ID) as count_person
							from				(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
							 where			d.LEVEL_NO='$level_no' and $gender_condition and a.POS_STATUS=1 
													$search_condition
							 group by		d.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(d.PER_ID) as count_person
								 from			PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d
								 where		a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1
													and d.LEVEL_NO='$level_no' and $gender_condition and a.POS_STATUS=1 
													$search_condition
								 group by	d.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(d.PER_ID) as count_person
							from				(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
							 where			d.LEVEL_NO='$level_no' and $gender_condition and a.POS_STATUS=1 
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
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order,$select_org_structure;
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
					if($ORG_ID && $ORG_ID!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = $ORG_ID)";
					}else{ 
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = 0 or d.ORG_ID is null)";
					}
				break;
				case "LINE" :
						$arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
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

	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_POSITION a
												left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
						$search_condition and POS_STATUS=1
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_POSITION a, PER_ORG b, PER_LINE e, PER_ORG c
						 where			a.ORG_ID=b.ORG_ID(+) and a.PL_CODE=e.PL_CODE(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_STATUS=1
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_POSITION a
												left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
						 $search_condition and POS_STATUS=1
						 order by		$order_by ";
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
			$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] = 0;
			$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] = 0;
		} // loop for
	} // loop for
	initialize_parameter(0);
	unset($display_order_number);

	$arr_content = (array) null;

	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[����к�$MINISTRY_TITLE]";
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						$data_ln = (array) null;
						$col_i = 2;
						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								$data_ln[] = $arr_content[$data_count][("count_m_".$LEVEL_NO)]."|".$arr_content[$data_count][("count_f_".$LEVEL_NO)];

								$col_i++;
								if($rpt_order_index == 0){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

//						echo "$data_count=".$arr_content[$data_count][type].", name=".trim($arr_content[$data_count][name])." [".implode(",",$data_ln)."] [".$arr_content[$data_count][total_m].",".$arr_content[$data_count][total_f]."]<br>";
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
							$DEPARTMENT_NAME = "[����к�$DEPARTMENT_TITLE]";
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						$data_ln = (array) null;
						$col_i = 2;
						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								$data_ln[] = $arr_content[$data_count][("count_m_".$LEVEL_NO)]."|".$arr_content[$data_count][("count_f_".$LEVEL_NO)];

								$col_i++;
								if($rpt_order_index == 0){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

//						echo "$data_count=".$arr_content[$data_count][type].", name=".trim($arr_content[$data_count][name])." [".implode(",",$data_ln)."] [".$arr_content[$data_count][total_m].",".$arr_content[$data_count][total_f]."]<br>";
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
							$ORG_NAME = "[����к�$ORG_TITLE]";
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						$data_ln = (array) null;
						$col_i = 2;
						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								$data_ln[] = $arr_content[$data_count][("count_m_".$LEVEL_NO)]."|".$arr_content[$data_count][("count_f_".$LEVEL_NO)];

								$col_i++;
								if($rpt_order_index == 0){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

//						echo "$data_count=".$arr_content[$data_count][type].", name=".trim($arr_content[$data_count][name])." [".implode(",",$data_ln)."] [".$arr_content[$data_count][total_m].",".$arr_content[$data_count][total_f]."]<br>";
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE == ""){
							$PL_NAME = "[����кص��˹����§ҹ]";
						}else{
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_NAME]);
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;

						$data_ln = (array) null;
						$col_i = 2;
						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								$data_ln[] = $arr_content[$data_count][("count_m_".$LEVEL_NO)]."|".$arr_content[$data_count][("count_f_".$LEVEL_NO)];

								$col_i++;
								if($rpt_order_index == 0){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

//						echo "$data_count=".$arr_content[$data_count][type].", name=".trim($arr_content[$data_count][name])." [".implode(",",$data_ln)."] [".$arr_content[$data_count][total_m].",".$arr_content[$data_count][total_f]."]<br>";
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$GRAND_TOTAL_M = array_sum($LEVEL_GRAND_TOTAL["M"]);
	$GRAND_TOTAL_F = array_sum($LEVEL_GRAND_TOTAL["F"]);
	$GRAND_TOTAL = $GRAND_TOTAL_M + $GRAND_TOTAL_F;

	if($export_type=="report"){	
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
	//			echo "$head_text1<br>";
				$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
			}
			if (!$result) echo "****** error ****** on open table for $table<br>";
			
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$DISPLAY_ORDER = $arr_content[$data_count][order];
				$NAME = $arr_content[$data_count][name];
				for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
					$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
					for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
						$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
						${"COUNT_M_".$LEVEL_NO} = $arr_content[$data_count][("count_m_".$LEVEL_NO)];
						${"COUNT_F_".$LEVEL_NO} = $arr_content[$data_count][("count_f_".$LEVEL_NO)];
					} // loop for
				} // loop for
				$TOTAL_M = $arr_content[$data_count][total_m];
				$TOTAL_F = $arr_content[$data_count][total_f];
	
				$arr_data = (array) null;
				$arr_data[] = $DISPLAY_ORDER;
				$arr_data[] = $NAME;
				for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
					$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
					for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
						$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
						$arr_data[] = ${"COUNT_M_".$LEVEL_NO};
						$arr_data[] = ${"COUNT_F_".$LEVEL_NO};
					} // loop for
				} // loop for
				$arr_data[] = $TOTAL_M;
				$arr_data[] = $TOTAL_F;
//				echo "data-".implode(",",$arr_data)."<br>";
	
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
			$arr_data[] = "<**1**>���������";	$data_align[0]="C";
			$arr_data[] = "<**1**>���������";	$data_align[1]="C";
			$cnt = 0;
			for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
				$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
				for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
					$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
					$arr_data[] = $LEVEL_GRAND_TOTAL["M"][$LEVEL_NO];
					$cnt++;
					$arr_data[] = $LEVEL_GRAND_TOTAL["F"][$LEVEL_NO];
					$cnt++;
				} // loop for
			} // loop for
//			$arr_data[] = "<**2**>".$GRAND_TOTAL; $data_align[($cnt+2)]="C";
//			$arr_data[] = "<**2**>".$GRAND_TOTAL; $data_align[($cnt+3)]="C";
			$arr_data[] = $GRAND_TOTAL_M;
			$arr_data[] = $GRAND_TOTAL_F;
//			echo "���-".implode(",",$arr_data)." , align-".implode(",",$data_align)." (cnt=$cnt)<br>";
	
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("********** ����բ����� **********", 7, "", "L", "", "14", "b", 0, 0);
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
		//$series_caption_list = "��ͤ�ͧ;��ҧ���Թ;��ҧ������Թ";
		$series_caption_list = "���;˭ԧ";
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