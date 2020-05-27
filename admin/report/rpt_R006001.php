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
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "g.PL_NAME";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "g.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "g.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "g.TP_NAME";
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
				$select_list .= "i.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				$order_by .= "i.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "i.ORG_SEQ_NO, i.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "i.ORG_SEQ_NO, i.ORG_CODE, a.DEPARTMENT_ID";

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

	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	if ($search_layer_no)
	$report_title = "$DEPARTMENT_NAME||��ª���$PERSON_TYPE[$search_per_type] �ա $search_layer_no ��� �ж֧����٧�ش�ͧ�Թ��͹";
		$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	if ($search_layer_salary)
	$report_title = "$DEPARTMENT_NAME||��ª���$PERSON_TYPE[$search_per_type] �Թ��͹��ҧ�ҡ����٧����Թ $search_layer_salary �ҷ";
		$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0601";
	include ("rpt_R006001_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
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

		$fname= "rpt_R006001_rtf.rtf";

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
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" : 
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
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

	if($search_per_type==1 || $search_per_type==3 || $search_per_type==5){	
		if ($search_layer_no) {
			if($DPISDB=="odbc"){
				$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
												$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY,
												d.LAYER_NO, MAX(e.LAYER_NO) as MAX_LAYER_NO
								  from	(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_LAYER d on (a.LEVEL_NO=d.LEVEL_NO and a.PER_SALARY=d.LAYER_SALARY)
															) inner join PER_LAYER e on (a.LEVEL_NO=e.LEVEL_NO)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
											) left join PER_ORG i on (a.DEPARTMENT_ID=i.ORG_ID)
								 $search_condition
								 group by $order_by, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
												$line_code, $line_name, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.LAYER_NO
								 having	IIf(IsNull(MAX(e.LAYER_NO)), 0, MAX(e.LAYER_NO)) - IIf(IsNull(d.LAYER_NO), 0, d.LAYER_NO) = $search_layer_no
								 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
												$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY,
												d.LAYER_NO, MAX(e.LAYER_NO) as MAX_LAYER_NO
								  from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_LAYER d, PER_LAYER e, PER_PRENAME f, $line_table g, PER_TYPE h, PER_ORG i
								  where	$position_join(+) and a.LEVEL_NO=d.LEVEL_NO and a.PER_SALARY=d.LAYER_SALARY
												and a.LEVEL_NO=e.LEVEL_NO and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.ORG_ID=c.ORG_ID(+)  and 
												a.DEPARTMENT_ID=i.ORG_ID(+)and b.PT_CODE=h.PT_CODE(+)
												$search_condition
								 group by $order_by, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
												$line_code, $line_name, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.LAYER_NO
								 having	(NVL(MAX(e.LAYER_NO),0) - NVL(d.LAYER_NO, 0)) = $search_layer_no
								 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
												$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY,
												d.LAYER_NO, MAX(e.LAYER_NO) as MAX_LAYER_NO
								  from	(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_LAYER d on (a.LEVEL_NO=d.LEVEL_NO and a.PER_SALARY=d.LAYER_SALARY)
															) inner join PER_LAYER e on (a.LEVEL_NO=e.LEVEL_NO)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
											) left join PER_ORG i on (a.DEPARTMENT_ID=i.ORG_ID)
								 $search_condition
								 group by $order_by, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
												$line_code, $line_name, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.LAYER_NO
								 having	IIf(IsNull(MAX(e.LAYER_NO)), 0, MAX(e.LAYER_NO)) - IIf(IsNull(d.LAYER_NO), 0, d.LAYER_NO) = $search_layer_no
								 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
			} // end if
		}
		if ($search_layer_salary) {
			if($DPISDB=="odbc"){
				$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
												$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY,
												d.LAYER_SALARY_MAX
								  from	(
												(
													(
														(
															(
																(
																	PER_PERSONAL a
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_LAYER d on (a.LEVEL_NO=d.LEVEL_NO and d.LAYER_SALARY_MAX - a.PER_SALARY <= $search_layer_salary and 
												d.LAYER_SALARY_MAX - a.PER_SALARY > 0 )
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
											) left join PER_ORG i on (a.DEPARTMENT_ID=i.ORG_ID)
								 $search_condition
								 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
												$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY,
												d.LAYER_SALARY_MAX
								  from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_LAYER d, PER_PRENAME f, $line_table g, PER_TYPE h, PER_ORG i
								  where	$position_join(+) and a.LEVEL_NO=d.LEVEL_NO and d.LAYER_SALARY_MAX - a.PER_SALARY <= $search_layer_salary and 
												d.LAYER_SALARY_MAX - a.PER_SALARY > 0 and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.ORG_ID=c.ORG_ID(+)  and 
												a.DEPARTMENT_ID=i.ORG_ID(+)and b.PT_CODE=h.PT_CODE(+)
												$search_condition
								 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
												$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY,
												d.LAYER_SALARY_MAX
								  from	(
												(
													(
														(
															(
																(
																	PER_PERSONAL a
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_LAYER d on (a.LEVEL_NO=d.LEVEL_NO and d.LAYER_SALARY_MAX - a.PER_SALARY <= $search_layer_salary and 
												d.LAYER_SALARY_MAX - a.PER_SALARY > 0 )
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
											) left join PER_ORG i on (a.DEPARTMENT_ID=i.ORG_ID)
								 $search_condition
								 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
			} // end if
		}
	}else{	// 2 || 4
		if($DPISDB=="odbc"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY,
											d.LAYERE_NO, MAX(e.LAYERE_NO) as MAX_LAYER_NO
							  from	(
											(
												(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_LAYEREMP d on (trim(b.PG_CODE_SALARY)=trim(d.PG_CODE) and a.PER_SALARY=d.LAYERE_SALARY)
													) inner join PER_LAYEREMP e on (trim(b.PG_CODE_SALARY)=trim(e.PG_CODE))
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
										) left join PER_ORG i on (a.DEPARTMENT_ID=i.ORG_ID)
							 $search_condition
							 group by $order_by, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code, $line_name, a.PER_SALARY, d.LAYERE_NO
							 having	IIf(IsNull(MAX(e.LAYERE_NO)), 0, MAX(e.LAYERE_NO)) - IIf(IsNull(d.LAYERE_NO), 0, d.LAYERE_NO) = $search_layer_no
							 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY,
											d.LAYERE_NO, MAX(e.LAYERE_NO) as MAX_LAYER_NO
							  from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_LAYEREMP d, PER_LAYEREMP e, PER_PRENAME f, $line_table g, PER_ORG i
							  where	$position_join(+) and trim(b.PG_CODE_SALARY)=trim(d.PG_CODE) and a.PER_SALARY=d.LAYERE_SALARY
											and trim(b.PG_CODE_SALARY)=trim(e.PG_CODE) and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and 
											b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=i.ORG_ID(+)
											$search_condition
							 group by $order_by, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code, $line_name, a.PER_SALARY, d.LAYERE_NO
							 having	(NVL(MAX(e.LAYERE_NO),0) - NVL(d.LAYERE_NO, 0)) = $search_layer_no
							 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY,
											d.LAYERE_NO, MAX(e.LAYERE_NO) as MAX_LAYER_NO
							  from	(
											(
												(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_LAYEREMP d on (trim(b.PG_CODE_SALARY)=trim(d.PG_CODE) and a.PER_SALARY=d.LAYERE_SALARY)
													) inner join PER_LAYEREMP e on (trim(b.PG_CODE_SALARY)=trim(e.PG_CODE))
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
										) left join PER_ORG i on (a.DEPARTMENT_ID=i.ORG_ID)
							 $search_condition
							 group by $order_by, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code, $line_name, a.PER_SALARY, d.LAYERE_NO
							 having	IIf(IsNull(MAX(e.LAYERE_NO)), 0, MAX(e.LAYERE_NO)) - IIf(IsNull(d.LAYERE_NO), 0, d.LAYERE_NO) = $search_layer_no
							 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
		} // end if
	} 
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;
	$data_count = $data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
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
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
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
//						echo "rpt_order_index=$rpt_order_index  ORG  ".$arr_content[$data_count][name]."<br>";

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
		
		$data_row++;
		$PER_ID = $data[PER_ID];
		$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POSITION_LEVEL = trim($data2[POSITION_LEVEL]);

		$PL_NAME = trim($data[PL_NAME]);
		$PER_SALARY = $data[PER_SALARY];
		$LAYER_SALARY_MAX = $data[LAYER_SALARY_MAX];

		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);
	
		if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4

		$arr_content[$data_count][type] = "CONTENT";
		if ($have_pic && $img_file){
				if ($FLAG_RTF)
				$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
				else
				$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
			}
		$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5))."$data_row. $PER_NAME";
//		echo "rpt_order_index=$rpt_order_index  |".$arr_content[$data_count][name]."|<br>";
		$arr_content[$data_count][position] = (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"�дѺ ".level_no_format($LEVEL_NO));
		$arr_content[$data_count][per_salary] = $PER_SALARY;
		$arr_content[$data_count][layer_salary_max] = $LAYER_SALARY_MAX;

		$data_count++;
	} // end while
	
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
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$LAYER_SALARY_MAX = $arr_content[$data_count][layer_salary_max];

			$arr_data = (array) null;
			if ($have_pic && $img_file) $arr_data[] = $IMAGE;
			$arr_data[] = $NAME;
			$arr_data[] = $POSITION;
			$arr_data[] = $PER_SALARY;
			if ($search_layer_salary && $LAYER_SALARY_MAX > $PER_SALARY) {
				$arr_data[] = $LAYER_SALARY_MAX;
				$arr_data[] = $LAYER_SALARY_MAX - $PER_SALARY;
			}
	
			if($REPORT_ORDER == "ORG") {
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			} else {
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			}
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for				
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
?>