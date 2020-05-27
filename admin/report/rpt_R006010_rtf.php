<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// ��˹�����ä���� set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "e.PL_NAME";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$pername_type = "����Ҫ���";
		$select_type_code = ", b.PT_CODE";
		$select_mgt_code = ", b.PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "e.PN_NAME";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
		$pername_type = "�١��ҧ��Ш�";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "e.EP_NAME";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
		$pername_type = "��ѡ�ҹ�Ҫ���";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=e.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "e.TP_NAME";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
		$pername_type = "�١��ҧ���Ǥ���";
	} // end if	
	
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
				$select_list .= "f.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "f.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" : 
				if($select_list) $select_list .= ", ";
				$select_list .= "f.ORG_SEQ_NO, f.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "f.ORG_SEQ_NO, f.ORG_CODE, a.DEPARTMENT_ID";

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
		if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
	}
	
//	$search_per_type = 1;
	$search_per_status[] = 1;

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(g.SAH_KF_YEAR='$search_budget_year')";
	if ($search_promote_level=="0") {
		$arr_search_condition[] = "(MOV_SUB_TYPE in ('49') or SM_CODE = '10' or (SAH_PERCENT_UP = 0 and SAH_SALARY_EXTRA = 0))";
		$search_promote_name = "���������͹�Թ��͹";
	} elseif ($search_promote_level=="0.5") {
		$arr_search_condition[] = "(MOV_SUB_TYPE in ('45') or SM_CODE = '1' or (SAH_PERCENT_UP > 0 and SAH_PERCENT_UP <= 1))";
		$search_promote_name = "����͹ 0.5 ��� ��������Թ 1 %";
	} elseif ($search_promote_level=="1") {
		$arr_search_condition[] = "(MOV_SUB_TYPE in ('46') or SM_CODE = '2' or (SAH_PERCENT_UP > 1 and SAH_PERCENT_UP <= 2))";
		$search_promote_name = "����͹ 1.0 ��� ��������Թ 2 %";
	} elseif ($search_promote_level=="1.5") {
		$arr_search_condition[] = "(MOV_SUB_TYPE in ('47') or SM_CODE = '3' or (SAH_PERCENT_UP > 2 and SAH_PERCENT_UP <= 3))";
		$search_promote_name = "����͹ 1.5 ��� ��������Թ 3 %";
	} elseif ($search_promote_level=="2") {
		$arr_search_condition[] = "(MOV_SUB_TYPE in ('48') or SM_CODE = '4' or (SAH_PERCENT_UP > 3 and SAH_PERCENT_UP <= 4))";
		$search_promote_name = "����͹ 2.0 ��� ��������Թ 4 %";
	} elseif ($search_promote_level=="3") {
		$arr_search_condition[] = "(SAH_PERCENT_UP > 4 and SAH_PERCENT_UP <= 5)";
		$search_promote_name = "����͹ ����Թ 5 %";
	} elseif ($search_promote_level=="4") {
		$arr_search_condition[] = "(SAH_PERCENT_UP > 5 and SAH_PERCENT_UP <= 6)";
		$search_promote_name = "����͹ ����Թ 6 %";
	}
	
	for($i=0; $i<count($search_salq_type); $i++){
		if($search_salq_type[$i] > 0){
			if($search_per_type==2) $search_salq_type[$i] += 2;
			elseif($search_per_type==3) $search_salq_type[$i] += 4;
			//elseif($search_per_type==4) 
		} // end if
	} // end for
	$arr_search_condition[] = "(g.SAH_KF_CYCLE in (". implode(", ", $search_salq_type) ."))";

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

	include ("rpt_R006010_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R006010_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "�ѭ����ª���$PERSON_TYPE[$search_per_type]��� ". $search_promote_name ." �է�����ҳ $search_budget_year";
	$report_code = "R0610";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(f.ORG_ID_REF = $MINISTRY_ID)";
					break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
					break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

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
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, h.MOV_SUB_TYPE, g.SAH_SALARY $select_type_code $select_mgt_code
						 from		(
											(
												(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
											) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
										) left join PER_MOVMENT h on (g.MOV_CODE=h.MOV_CODE)
											$search_condition
						 order by		h.MOV_SUB_TYPE, $order_by, $line_code, a.LEVEL_NO, $position_no_name, $position_no, a.PER_NAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, TO_NUMBER($position_no) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, h.MOV_SUB_TYPE, g.SAH_SALARY $select_type_code $select_mgt_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_ORG f, PER_SALARYHIS g, PER_MOVMENT h
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=f.ORG_ID(+)
											and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and a.PER_ID=g.PER_ID and g.MOV_CODE=h.MOV_CODE(+)
											$search_condition
						 order by		h.MOV_SUB_TYPE, $order_by, $line_code, a.LEVEL_NO, $position_no_name, $position_no, a.PER_NAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, h.MOV_SUB_TYPE, g.SAH_SALARY $select_type_code $select_mgt_code
						 from		(
											(
												(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
											) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
										) left join PER_MOVMENT h on (g.MOV_CODE=h.MOV_CODE)
											$search_condition
						 order by		h.MOV_SUB_TYPE, $order_by, $line_code, a.LEVEL_NO, $position_no_name, $position_no, a.PER_NAME ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd ($count_data) [".implode(",",$arr_rpt_order)."]<br>";
	$data_count = $data_row = $REASON_COUNT = 0;
	$MOV_SUB_TYPE = -1;
	initialize_parameter(0);
//	$pdf->AutoPageBreak = false;
//	print_header();
//	$RTF->open_section(1); 
//	$RTF->set_font($font, 20);
//	$RTF->color("0");	// 0=BLACK
		
	$RTF->add_header("", 0, false);	// header default
	$RTF->add_footer("", 0, false);		// footer default
		
	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "$head_text1<br>";
	$tab_align = "center";
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, true, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";
		
	if ($count_data) {
		while($data = $db_dpis->get_array()){
			if($MOV_SUB_TYPE != trim($data[MOV_SUB_TYPE])){
				$MOV_SUB_TYPE = trim($data[MOV_SUB_TYPE]);
				
				$arr_content[$data_count][type] = "PROMOTELEVEL";
				$arr_content[$data_count][name] = $MOV_SUB_TYPE;
				
				$data_count++;
				initialize_parameter(0);
			} // end if
			
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
				$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
//				echo "rpt_order_index($rpt_order_index), REPORT_ORDER($REPORT_ORDER)<br>";
				switch($REPORT_ORDER){
					case "MINISTRY" :
						if($MINISTRY_ID != trim($data[MINISTRY_ID])){
							$MINISTRY_ID = trim($data[MINISTRY_ID]);
							if($MINISTRY_ID != ""){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$MINISTRY_NAME = $data2[ORG_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
	
							$arr_content[$data_count][type] = "MINISTRY";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
							
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
	
							$data_count++;
						} // end if
					break;
					case "DEPARTMENT" :
						if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
							$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
							if($DEPARTMENT_ID != ""){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$DEPARTMENT_NAME = $data2[ORG_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
	
							$arr_content[$data_count][type] = "DEPARTMENT";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
							
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
	
							$data_count++;
						} // end if
					break;
					case "ORG" :
//						echo "ORG_ID($ORG_ID) != data[ORG_ID]=".trim($data[ORG_ID])."<br>";
						if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != ""){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
	
							$arr_content[$data_count][type] = "ORG";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
							
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
	
							$data_count++;
						} // end if
//						echo "rpt_order_index=$rpt_order_index, count (arr_rpt_order)=".count($arr_rpt_order)."<br>";
						if($rpt_order_index == (count($arr_rpt_order) - 1)){
							$data_row++;
							
							$PER_ID = $data[PER_ID];
							$PN_NAME = trim($data[PN_NAME]);
							$PER_NAME = trim($data[PER_NAME]);
							$PER_SURNAME = trim($data[PER_SURNAME]);
							$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
							$PL_CODE = trim($data[PL_CODE]);
							$PL_NAME = trim($data[PL_NAME]);
							$LEVEL_NO = $data[LEVEL_NO];
	//						$PER_SALARY = $data[PER_SALARY];
							$PER_SALARY = $data[SAH_SALARY];
							if(trim($PT_CODE)){
								$PT_CODE = trim($data[PT_CODE]);
								$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
								$db_dpis3->send_cmd($cmd);
								$data3 = $db_dpis3->get_array();
								$PT_NAME = trim($data3[PT_NAME]);
							}
							if(trim($PM_CODE)){
								$PM_CODE = trim($data[PM_CODE]);
								$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
								$db_dpis3->send_cmd($cmd);
								$data3 = $db_dpis3->get_array();
								$PM_NAME = trim($data3[PM_NAME]);		
							}
							$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
							$db_dpis3->send_cmd($cmd);
	//						$db_dpis->show_error();
							$data3 = $db_dpis3->get_array();
							$LEVEL_NAME=$data3[LEVEL_NAME];
							$POSITION_LEVEL = $data3[POSITION_LEVEL];
							if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
							$arr_content[$data_count][type] = "DETAIL";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . "$data_row.$PN_NAME" . "$PER_NAME $PER_SURNAME";
							$arr_content[$data_count][pos_no] = $POS_NO;
							$arr_content[$data_count][position] = ($PM_CODE?"$PM_NAME ( ":"") . $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"") . ($PM_CODE?" )":"");
							$arr_content[$data_count][salary] = $PER_SALARY;
	
							$data_count++;
						} // end if
					break;
				} // end switch case
			} // end for
		} // end while
	
//		echo "<pre>"; print_r($arr_content); echo "</pre>";
//		echo "count_data=$count_data<br>";

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$PER_SALARY = $arr_content[$data_count][salary];
			
			if($REPORT_ORDER == "PROMOTELEVEL"){
				$border = "";
//				$pdf->SetFont($font,'b',14);
//				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//				$RTF->ln();
//				$RTF->set_table_font($font, 14);
				
//				$pdf->report_title = "�ѭ����ª���$PERSON_TYPE[$search_per_type]�������͹ $NAME ��� �է�����ҳ $search_budget_year";
				$report_title = "�ѭ����ª���$PERSON_TYPE[$search_per_type]�������͹ ". number_format($search_promote_level, 1) ." ��� �է�����ҳ $search_budget_year";
				$RTF->set_report_title($report_title);
				$RTF->new_page();
				$RTF->print_tab_header();
//				$pdf->AddPage();
//				print_header();
//				$head_text1 = implode(",", $heading_text);
//				$head_width1 = implode(",", $heading_width);
//				$head_align1 = implode(",", $heading_align);
//				$col_function = implode(",", $column_function);
		//		echo "$head_text1<br>";
//				$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function); 
				// not print first head
//				if (!$result) echo "****** error ****** on open table for $table<br>";
			}else{
				$border = "";
				
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $POSITION;
				$arr_data[] = $POS_NO;
				$arr_data[] = $PER_SALARY;
				
				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if
		} // end for				
	}else{
//		echo "count_data=$count_data<br>";
		$result = $RTF->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
	
?>