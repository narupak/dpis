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
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if
	
	$cmd = "select LEVEL_NO, LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO,LEVEL_NO";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){ 
		$ARR_LEVEL_NO[] = $data[LEVEL_NO];		
		$ARR_LEVEL_SHORTNAME[] = $data[LEVEL_SHORTNAME];
	}
	
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
//	$arr_rpt_order = array("ORG"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "h.ORG_ID_REF as MINISTRY_ID";	
			
				if($order_by) $order_by .= ", ";
				$order_by .= "h.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "h.ORG_SEQ_NO, h.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "h.ORG_SEQ_NO, h.ORG_CODE, a.DEPARTMENT_ID ";

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
		if($select_org_structure == 0) $order_by .= "b.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "b.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "a.ORG_ID";
	}

	$search_per_status[] = 1;

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(d.PUN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(d.PUN_STARTDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(d.PUN_STARTDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(d.PUN_STARTDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(d.PUN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(d.PUN_STARTDATE), 10) < '".($search_budget_year - 543)."-10-01')";

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
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||ʶԵ԰ҹ�����Դ�ҧ�Թ�� ��ṡ����дѺ���˹�||㹻է�����ҳ $search_budget_year";
	$report_code = "R0806";
	include ("rpt_R008006_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
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

		$fname= "rpt_R008006_rtf.rtf";

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

	if($search_per_type==2){  $heading_width[0] = "80"; } else { $heading_width[0] = "127"; }
	$heading_width[1] = "10";
	$heading_width[2] = "15";
	$heading_width[3] = "15";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $ARR_LEVEL_NO,$ARR_LEVEL_SHORTNAME;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,$heading_name,'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] * count($ARR_LEVEL_NO)) ,7,"�дѺ",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"���",'LTBR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
			$pdf->Cell($heading_width[1] ,7,"$tmp_level_shortname",'LTBR',0,'C',1);
		} // end for
		$pdf->Cell($heading_width[2] ,7,"��",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"������",'LTBR',1,'C',1);
	} // function		

	function count_person($cr_code, $level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type, $select_org_structure, $position_table, $position_join;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);

		
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(	
								 					(
														(
															(
																PER_PERSONAL a 
																inner join $position_table b on ($position_join) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e, PER_ORG h
								 where		$position_join and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) and d.CRD_CODE=e.CRD_CODE(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
													and trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(	
								 					(
														(
															(
																PER_PERSONAL a 
																inner join $position_table b on ($position_join) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			} // end if
		
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(h.ORG_ID_REF = $MINISTRY_ID)";
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
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from		(	
												(
													(
														(
															(
																PER_PERSONAL a 
																inner join $position_table b on ($position_join) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
											) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e, PER_CRIME f, PER_ORG h
							 where		$position_join and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) 
							 					and d.CRD_CODE=e.CRD_CODE(+) and e.CR_CODE=f.CR_CODE(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from		(	
												(
													(
														(
															(
																PER_PERSONAL a 
																inner join $position_table b on ($position_join) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
											) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		} // end if
	
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	unset($LEVEL_TOTAL);
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
						
						if($rpt_order_index == (count($arr_rpt_order) - 1)){
							if($CR_CODE != trim($data[CR_CODE])){
								$CR_CODE = trim($data[CR_CODE]);
								$CR_NAME = trim($data[CR_NAME]);
								
								$arr_content[$data_count][type] = "CONTENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $CR_NAME;
								for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++){ 
									$arr_content[$data_count]["level_".$i] = count_person("$CR_CODE", $i, $search_condition, $addition_condition);
									if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
								} // end for
								
								$data_count++;
							} // end if
						} // end if
					} // end if
				break;			
				
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
						
						if($rpt_order_index == (count($arr_rpt_order) - 1)){
							if($CR_CODE != trim($data[CR_CODE])){
								$CR_CODE = trim($data[CR_CODE]);
								$CR_NAME = trim($data[CR_NAME]);
								
								$arr_content[$data_count][type] = "CONTENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $CR_NAME;
								for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++){ 
									$arr_content[$data_count]["level_".$i] = count_person("$CR_CODE", $i, $search_condition, $addition_condition);
									if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
								} // end for
								
								$data_count++;
							} // end if
						} // end if
					} // end if
				break;
				
				case "ORG" :
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
						
						if($rpt_order_index == (count($arr_rpt_order) - 1)){
							if($CR_CODE != trim($data[CR_CODE])){
								$CR_CODE = trim($data[CR_CODE]);
								$CR_NAME = trim($data[CR_NAME]);
								
								$arr_content[$data_count][type] = "CONTENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $CR_NAME;
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person("$CR_CODE",$tmp_level_no, $search_condition, $addition_condition);
									if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
								} // end for
								$data_count++;
							} // end if
						} // end if
					} // end if
				break;		
			} // end switch case
		} // end for
	} // end while
	for($ii=1; $ii <=count($ARR_LEVEL_SHORTNAME); $ii++) {
		if ($arr_column_sel[$arr_column_map[$ii]]==1) // 1 = �ʴ� column ���
			$GRAND_TOTAL += $LEVEL_TOTAL[$ii];
	}
//	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			unset($COUNT_LEVEL);
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				$COUNT_LEVEL[$tmp_level_no] = $arr_content[$data_count]["level_".$tmp_level_no];
			}
			$COUNT_TOTAL = array_sum($COUNT_LEVEL);
			
			if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

			if($REPORT_ORDER == "ORG"){
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++)
					$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
/*				$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$pdf->Cell($heading_width[1], 7, "", $border, 0, 'R', 0);
					$pdf->x = $start_x + $heading_width[0] + ($heading_width[1] * $tmp_level_no);
				} // end for
				$pdf->Cell($heading_width[2], 7, "", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[3], 7, "", $border, 0, 'R', 0); */
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++)
					$arr_data[] = $COUNT_LEVEL[$i];
				$arr_data[] = $COUNT_TOTAL;
				$arr_data[] = $PERCENT_TOTAL;
/*				$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$pdf->Cell($heading_width[1], 7, ($COUNT_LEVEL[$tmp_level_no]?number_format($COUNT_LEVEL[$tmp_level_no]):"-"), $border, 0, 'R', 0);
					$pdf->x = $start_x + $heading_width[0] + ($heading_width[1] * $tmp_level_no);
				} // end if
				$pdf->Cell($heading_width[2], 7, ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[3], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 0, 'R', 0); */
			} // end if

			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1)
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL			
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
				
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$arr_data = (array) null;
		$arr_data[] = "���";
		$COUNT_GRAND_TOTAL = 0;
		for($ii=1; $ii <= count($ARR_LEVEL_SHORTNAME); $ii++) {
			if ($arr_column_sel[$arr_column_map[$ii]]==1) {// 1 = �ʴ� column ���
				$COUNT_GRAND_TOTAL += $LEVEL_TOTAL[$ii];
				$arr_data[] = $LEVEL_TOTAL[$ii];
			}
		}
		$arr_data[] = $COUNT_GRAND_TOTAL;
		$arr_data[] = $COUNT_GRAND_TOTAL/$GRAND_TOTAL*100;

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");		//TRHBL
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

/*		$border = "LTBR";
		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->MultiCell($heading_width[0], 7, "���", $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0];
		$pdf->y = $start_y;
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$pdf->Cell($heading_width[1], 7, ($LEVEL_TOTAL[$tmp_level_no]?number_format($LEVEL_TOTAL[$tmp_level_no]):"-"), $border, 0, 'R', 0);
		}
		$pdf->Cell($heading_width[2], 7, ($GRAND_TOTAL?number_format($GRAND_TOTAL):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[3], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 1, 'R', 0); */
	}else{
		$result = $pdf->add_text_line("********** ����բ����� **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$pdf->close_tab(""); 

	$pdf->close();
	$pdf->Output();		

?>