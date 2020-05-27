<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$g_name = "g.PL_NAME";
		$pl_code = "b.PL_CODE";
		$a_code = "a.PL_CODE";
		$b_name = "b.PL_NAME";
		$position_no = "b.POS_NO_NAME, b.POS_NO";
		$pl_seq_no = "g.PL_SEQ_NO";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$g_name = "g.PN_NAME";
		$pl_code = "b.PN_CODE";
		$a_code = "a.PN_CODE";
		$b_name = "b.PN_NAME";
		$position_no = "b.POEM_NO_NAME, b.POEM_NO";
		$pl_seq_no = "g.PN_SEQ_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$g_name = "g.EP_NAME";
		$pl_code = "b.EP_CODE";
		$a_code = "a.EP_CODE";
		$b_name = "b.EP_NAME";
		$position_no = "b.POEMS_NO_NAME, b.POEMS_NO";
		$pl_seq_no = "g.EP_SEQ_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$g_name = "g.TP_NAME";
		$pl_code = "b.TP_CODE";
		$a_code = "a.TP_CODE";
		$b_name = "b.TP_NAME";
		$position_no = "b.POT_NO_NAME, b.POT_NO";
		$pl_seq_no = "g.TP_SEQ_NO";
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if(trim($search_org_id)) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
	if(trim($search_pl_code)) $arr_search_condition[] = "(trim(b.PL_CODE)=trim('$search_pl_code'))";
	if(trim($search_level_no)){
		$search_level_no = trim($search_level_no);	
		if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) = '$search_level_no')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) = '$search_level_no')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) = '$search_level_no')";
	} // end if

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

	include ("rpt_R004017_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R004017_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||บัญชีรายชื่อ$PERSON_TYPE[$search_per_type] เรียงตามอาวุโส";
	$report_code = "R0417";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
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
		global $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	if($search_per_type == 1){
		if($DPISDB=="odbc"){
			//1						b.PT_CODE, i.PT_NAME, 
			//1						) left join PER_TYPE i on (b.PT_CODE=i.PT_CODE)			
			$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
												$pl_seq_no, $pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
							 from			(
													(
														(
															(
																(
																	(
																		(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join)
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) left join PER_ORG d on (b.ORG_ID_1=d.ORG_ID)
																) left join PER_PRENAME e on (a.PN_CODE=e.PN_CODE)
															) left join PER_POSITIONHIS f on (a.PER_ID=f.PER_ID and a.LEVEL_NO=f.LEVEL_NO)
														) left join $line_table g on ($line_join)
													) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
												) left join PER_TYPE i on (b.PT_CODE=i.PT_CODE)
												$search_condition
							 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID,  $position_no,c.ORG_NAME, d.ORG_NAME, $pl_seq_no, 
												$pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10)
							 order by	c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $pl_seq_no, $pl_code, h.LEVEL_SEQ_NO desc, a.LEVEL_NO, 
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)), LEFT(trim(a.PER_STARTDATE), 10) ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1, $position_no,
												$pl_seq_no, $pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
												SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
												MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG d, PER_PRENAME e, PER_POSITIONHIS f, $line_table g, PER_LEVEL h, PER_TYPE i
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and b.ORG_ID_1=d.ORG_ID(+) and a.PN_CODE=e.PN_CODE(+)
												and a.PER_ID=f.PER_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+) and $line_join(+) and b.PT_CODE=i.PT_CODE(+) and a.LEVEL_NO=h.LEVEL_NO(+)
												$search_condition
							 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $position_no, c.ORG_NAME, d.ORG_NAME, $pl_seq_no, 
												$pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
												SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
							 order by	c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $pl_seq_no, $pl_code, h.LEVEL_SEQ_NO desc, a.LEVEL_NO, 
												MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)), SUBSTR(trim(a.PER_STARTDATE), 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
												$pl_seq_no, $pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
							 from			(
													(
														(
															(
																(
																	(
																		(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join)
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) left join PER_ORG d on (b.ORG_ID_1=d.ORG_ID)
																) left join PER_PRENAME e on (a.PN_CODE=e.PN_CODE)
															) left join PER_POSITIONHIS f on (a.PER_ID=f.PER_ID and a.LEVEL_NO=f.LEVEL_NO)
														) left join $line_table g on ($line_join)
													) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
												) left join PER_TYPE i on (b.PT_CODE=i.PT_CODE)
												$search_condition
							 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $position_no, c.ORG_NAME, d.ORG_NAME, $pl_seq_no, 
												$pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10)
							 order by	c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $pl_seq_no, $pl_code, h.LEVEL_SEQ_NO desc, a.LEVEL_NO, 
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)), LEFT(trim(a.PER_STARTDATE), 10) ";
		}
	}else{ // 2 || 3 || 4
		if($DPISDB=="odbc"){
			$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
												$pl_seq_no, $pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
							 from			(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_ORG d on (b.ORG_ID_1=d.ORG_ID)
															) left join PER_PRENAME e on (a.PN_CODE=e.PN_CODE)
														) left join PER_POSITIONHIS f on (a.PER_ID=f.PER_ID and a.LEVEL_NO=f.LEVEL_NO)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
												$search_condition
							 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID,  $position_no,c.ORG_NAME, d.ORG_NAME, $pl_seq_no,	
												$pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10)
							 order by	c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $pl_seq_no, $pl_code, h.LEVEL_SEQ_NO desc, a.LEVEL_NO, 
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)), LEFT(trim(a.PER_STARTDATE), 10) ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1, $position_no,
												$pl_seq_no, $pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, a.PER_SALARY, 
												SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
												MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG d, PER_PRENAME e, PER_POSITIONHIS f, $line_table g, PER_LEVEL h
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and b.ORG_ID_1=d.ORG_ID(+) and a.PN_CODE=e.PN_CODE(+)
												and a.PER_ID=f.PER_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+) and $line_join(+) and a.LEVEL_NO=h.LEVEL_NO(+)
												$search_condition
							 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $position_no, c.ORG_NAME, d.ORG_NAME, $pl_seq_no, 
												$pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE,a.PER_SALARY, 
												SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
							 order by	c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $pl_seq_no, $pl_code, h.LEVEL_SEQ_NO desc, a.LEVEL_NO, 
												MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)), SUBSTR(trim(a.PER_STARTDATE), 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
												$pl_seq_no, $pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL,  a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
							 from			(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_ORG d on (b.ORG_ID_1=d.ORG_ID)
															) left join PER_PRENAME e on (a.PN_CODE=e.PN_CODE)
														) left join PER_POSITIONHIS f on (a.PER_ID=f.PER_ID and a.LEVEL_NO=f.LEVEL_NO)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
												$search_condition
							 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $position_no, c.ORG_NAME, d.ORG_NAME, $pl_seq_no, 
												$pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10)
							 order by	c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $pl_seq_no, $pl_code, h.LEVEL_SEQ_NO desc, a.LEVEL_NO, 
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)), LEFT(trim(a.PER_STARTDATE), 10) ";
		}
	} //end if

	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$ORG_ID = $PL_CODE = $LEVEL_NO = -1;
//	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		if($ORG_ID != $data[ORG_ID] || $PL_CODE != trim($data[PL_CODE]) || $LEVEL_NO != $data[LEVEL_NO]){ 
			$arr_count["$ORG_ID:$PL_CODE:$LEVEL_NO"] = $data_row;
			$data_row = 0;
		} // end if
	
		$data_row++;
		$PER_ID = $data[PER_ID];
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$ORG_ID = $data[ORG_ID];
		$ORG_NAME = trim($data[ORG_NAME]);
		$ORG_NAME_1 = trim($data[ORG_NAME_1]);
		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = trim($data[PL_NAME]);

		$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
		$POEM_NO = trim($data[POEM_NO_NAME]).trim($data[POEM_NO]);
		$POEMS_NO = trim($data[POEMS_NO_NAME]).trim($data[POEMS_NO]);
		$POT_NO = trim($data[POT_NO_NAME]).trim($data[POT_NO]);
		$LEVEL_NO = $data[LEVEL_NO];
		$LEVEL_NAME = $data[LEVEL_NAME];
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = trim($data[PT_NAME]);
		$PER_SALARY = $data[PER_SALARY];
		$PER_STARTDATE = show_date_format($data[PER_STARTDATE], $DATE_DISPLAY);
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$PER_RETIREDATE = "";
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);	

			$PER_RETIREDATE = ($arr_temp[0] + 60) ."-10-01";
			if($PER_BIRTHDATE >= ($arr_temp[0] ."-10-01")) $PER_RETIREDATE = ($arr_temp[0] + 60 + 1) ."-10-01";

			$PER_BIRTHDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if
		if($PER_RETIREDATE){
			$arr_temp = explode("-", $PER_RETIREDATE);
			$PER_RETIREDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE], $DATE_DISPLAY);
		
		// === BEG_C_DATE วันเข้าสู่ระดับก่อนปัจจุบัน
		if(trim($data[POH_EFFECTIVEDATE]) && $data[POH_EFFECTIVEDATE]!="-"){
			if($DPISDB=="odbc" || $DPISDB=="mysql"){
				$cmd = " select LEFT(trim(POH_EFFECTIVEDATE), 10) as POH_EFFECTIVEDATE, LEVEL_NO
						from   PER_POSITIONHIS
						where PER_ID=$PER_ID and LEFT(trim(POH_EFFECTIVEDATE), 10) < '".$data[POH_EFFECTIVEDATE]."'
						order by LEFT(trim(POH_EFFECTIVEDATE), 10) desc ,LEVEL_NO desc ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10) as POH_EFFECTIVEDATE, LEVEL_NO
						from   PER_POSITIONHIS
						where PER_ID=$PER_ID and SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10) < '".$data[POH_EFFECTIVEDATE]."'
						order by SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10) desc ,LEVEL_NO desc ";
			}
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$BEG_C_DATE = show_date_format($data2[POH_EFFECTIVEDATE], $DATE_DISPLAY);
			$BEG_LEVEL_NO = trim($data2[LEVEL_NO]);
			//echo $BEG_C_DATE."+++".$cmd."<br>";
		}
		
		if($DPISDB=="odbc"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME
							 from		PER_EDUCATE a
											left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
							 where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'
							 order by a.EDU_SEQ ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME
							 from		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE(+) and a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' 
							 order by a.EDU_SEQ ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME
							 from		PER_EDUCATE a
											left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
							 where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'
							 order by a.EDU_SEQ ";
		} // end if
		$count_educate = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$EDUCATE = "";
		while($data2 = $db_dpis2->get_array()){
			if($EDUCATE) $EDUCATE .= "*Enter*";
			$EDUCATE .= trim($data2[EN_SHORTNAME]);
		} // end while

		$BEG_LEVEL_NO = str_pad(($LEVEL_NO - 1), 2, "0", STR_PAD_LEFT);
		if($DPISDB=="odbc"){
			$cmd = " select 	MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE2
							 from 		PER_POSITIONHIS 
							 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='$BEG_LEVEL_NO' ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	MIN(SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE2
							 from 		PER_POSITIONHIS 
							 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='$BEG_LEVEL_NO' ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE2
							 from 		PER_POSITIONHIS 
							 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='$BEG_LEVEL_NO' ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$POH_EFFECTIVEDATE2 = show_date_format($data[POH_EFFECTIVEDATE2], $DATE_DISPLAY);
		
		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							 from		PER_DECORATEHIS a
											left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID and b.DC_TYPE in(1,2) 
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.DC_CODE=b.DC_CODE(+) and a.PER_ID=$PER_ID and b.DC_TYPE in(1,2) 
							 order by SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							 from		PER_DECORATEHIS a
											left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID and b.DC_TYPE in(1,2) 
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$DECORATE = trim($data2[DC_SHORTNAME]);
		$PREV_LEVEL_NO = str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT);
		$PREV_SUB_TYPE = " 1, 10, 11, 2, 3 ";
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select 	distinct $a_code, $b_name, a.LEVEL_NO, a.PT_CODE, c.PT_NAME
								 from 		(
													(
														PER_POSITIONHIS a
														left join $line_table b on ($a_code=$pl_code)
													) left join PER_TYPE c on (a.PT_CODE=c.PT_CODE)
												) left join PER_MOVMENT d on (a.MOV_CODE=d.MOV_CODE)
								 where 	a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
												and d.MOV_SUB_TYPE in ($PREV_SUB_TYPE)
								 order by a.LEVEL_NO desc, $a_code ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	distinct $a_code, $b_name, a.LEVEL_NO, a.PT_CODE, c.PT_NAME
								 from 		PER_POSITIONHIS a, $line_table b, PER_TYPE c, PER_MOVMENT d
								 where 	$a_code=$pl_code(+) and a.PT_CODE=c.PT_CODE(+) and a.MOV_CODE=d.MOV_CODE(+)
												and a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
												and d.MOV_SUB_TYPE in ($PREV_SUB_TYPE)
								order by	a.LEVEL_NO desc, $a_code ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	distinct $a_code, $b_name, a.LEVEL_NO, a.PT_CODE, c.PT_NAME
								 from 		(
													(
														PER_POSITIONHIS a
														left join $line_table b on ($a_code=$pl_code)
													) left join PER_TYPE c on (a.PT_CODE=c.PT_CODE)
												) left join PER_MOVMENT d on (a.MOV_CODE=d.MOV_CODE)
								 where 	a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
												and d.MOV_SUB_TYPE in ($PREV_SUB_TYPE)
								 order by a.LEVEL_NO desc, $a_code ";
			} // end if
		}else{ // 2 || 3 || 4
				if($DPISDB=="odbc"){
					$cmd = " select 	$a_code as PL_CODE, $b_name as PL_NAME, a.LEVEL_NO
									 from 		PER_POSITIONHIS a
													left join $line_table b on ($a_code=$pl_code)
													left join PER_MOVMENT d on (a.MOV_CODE=d.MOV_CODE)
									 where 	a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
													and d.MOV_SUB_TYPE in ($PREV_SUB_TYPE)
									 order by a.LEVEL_NO desc, $a_code ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select 	$a_code as PL_CODE, $b_name as PL_NAME, a.LEVEL_NO
									 from 		PER_POSITIONHIS a, $line_table b, PER_MOVMENT d
									 where 	$a_code=$pl_code(+) and a.MOV_CODE=d.MOV_CODE(+)
													and a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
													and d.MOV_SUB_TYPE in ($PREV_SUB_TYPE)
									order by	a.LEVEL_NO desc, $a_code ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select 	$a_code as PL_CODE, $b_name as PL_NAME, a.LEVEL_NO
									 from 		PER_POSITIONHIS a
													left join $line_table b on ($a_code=$pl_code)
													left join PER_MOVMENT d on (a.MOV_CODE=d.MOV_CODE)
									 where 	a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
													and d.MOV_SUB_TYPE in ($PREV_SUB_TYPE)
									 order by a.LEVEL_NO desc, $a_code ";
				} // end if
		} //end if
		$count_positionhis = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = "$data_row.";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."*Enter*". $ORG_NAME_1;
		$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		$arr_content[$data_count][pos_no] = $POS_NO; 
		$arr_content[$data_count][org_id] = $ORG_ID;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
		$arr_content[$data_count][pl_code] = $PL_CODE;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][level] = $LEVEL_NAME;
		$arr_content[$data_count][educate] = "$EDUCATE";
		$arr_content[$data_count][effectivedate_current_level] = "$POH_EFFECTIVEDATE";
		$arr_content[$data_count][effectivedate_previous_level] = "$POH_EFFECTIVEDATE2";
		$arr_content[$data_count][salary] = "$PER_SALARY";
		$arr_content[$data_count][startdate] = "$PER_STARTDATE";
		$arr_content[$data_count][decorate] = "$DECORATE";
		$arr_content[$data_count][birthdate] = "$PER_BIRTHDATE";
		$arr_content[$data_count][retiredate] = "$PER_RETIREDATE";
		$arr_content[$data_count][count_positionhis] = "$count_positionhis";
		$arr_content[$data_count][beg_c_date] = "$BEG_C_DATE";
		
		$POSITIONHIS = "";
		$tmp_count = 0;
		while($data2 = $db_dpis2->get_array()){
			$tmp_count++;
			if($tmp_count > 3) break;
			$cmd = "select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$data2[LEVEL_NO]' ";
			$db_dpis3->send_cmd($cmd);
//				$db_dpis3->show_error();
			$data3 = $db_dpis3->get_array();
			if($POSITIONHIS) $POSITIONHIS .= "\\par";		//*Enter*";
			$POSITIONHIS .= trim($data2[PL_NAME]) . $data3[POSITION_LEVEL] . ((trim($data2[PT_NAME]) != "ทั่วไป" && $data2[LEVEL_NO] >= 6)?$data2[PT_NAME]:"");
		} // end while
		$arr_content[$data_count][positionhis] = "$POSITIONHIS";

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
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

	if($count_data){
		$ORG_ID = $PL_CODE = $LEVEL_NO = -1;
		$data_row = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			if($ORG_ID != $arr_content[$data_count][org_id] || $PL_CODE != $arr_content[$data_count][pl_code] || $LEVEL_NAME != $arr_content[$data_count][level]){
				$ORG_ID = $arr_content[$data_count][org_id];
				$ORG_NAME = $arr_content[$data_count][org_name];
				$PL_CODE = $arr_content[$data_count][pl_code];
				$PL_NAME = $arr_content[$data_count][pl_name];
				$LEVEL_NAME = $arr_content[$data_count][level];
				
				$RTF->close_tab(); 

				if ($data_count > 0) $RTF->new_page(); 

				$report_title = "$DEPARTMENT_NAME||บัญชีรายชื่อ$PERSON_TYPE[$search_per_type] ". $LEVEL_NAME ." สายงาน $PL_NAME||ในสังกัด$ORG_NAME เรียงตามอาวุโส";
				$RTF->set_report_title($report_title);

				$RTF->add_header("", 0, false);	// header default
				$RTF->add_footer("", 0, false);		// footer default
				$RTF->paragraph();

				$head_text1 = implode(",", $heading_text);
				$head_width1 = implode(",", $heading_width);
				$head_align1 = implode(",", $heading_align);
				$col_function = implode(",", $column_function);
//				echo "$head_text1<br>";
				$tab_align = "center";
				$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
				if (!$result) echo "****** error ****** on open table for $table<br>";
			} // end if

			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$EDUCATE = $arr_content[$data_count][educate];
			$EFFECTIVEDATE_CURRENT_LEVEL = $arr_content[$data_count][effectivedate_current_level];
			$EFFECTIVEDATE_PREVIOUS_LEVEL = $arr_content[$data_count][effectivedate_previous_level];
			$SALARY = $arr_content[$data_count][salary];
			$STARTDATE = $arr_content[$data_count][startdate];
			$DECORATE = $arr_content[$data_count][decorate];
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			$RETIREDATE = $arr_content[$data_count][retiredate];
			$POSITIONHIS = $arr_content[$data_count][positionhis];
			$BEG_C_DATE = $arr_content[$data_count][beg_c_date];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $POS_NO;
			$arr_data[] = $NAME;
			$arr_data[] = $POSITION;
			$arr_data[] = $EDUCATE;
			$arr_data[] = $EFFECTIVEDATE_CURRENT_LEVEL;
			$arr_data[] = $BEG_C_DATE;
			$arr_data[] = $SALARY;
			$arr_data[] = $STARTDATE;
			$arr_data[] = $DECORATE;
			$arr_data[] = $BIRTHDATE;
			$arr_data[] = $RETIREDATE;
			$arr_data[] = $POSITIONHIS;
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for				
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
?>