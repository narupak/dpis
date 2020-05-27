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
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_name = "e.PL_NAME";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);
		$line_title = " สายงาน";
		$type_code = "b.PT_CODE";
		$select_type_code = ", b.PT_CODE";
		$mgt_code = "b.PM_CODE";
		$select_mgt_code = ", b.PM_CODE";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_name = "e.PN_NAME";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_name = "e.EP_NAME";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_join = "b.TP_CODE=e.TP_CODE";
		$line_name = "e.TP_NAME";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);
		$line_title = " ชื่อตำแหน่ง";
	} // end if
	
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
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($DPISDB=="odbc"){ 
		$arr_search_condition[] = "(LEFT(g.SAH_EFFECTIVEDATE, 10) < '".($search_budget_year - 543)."-10-01')";
		$arr_search_condition[] = "(LEFT(g.SAH_EFFECTIVEDATE, 10) >= '".(($search_budget_year - 5) - 543)."-10-01')";
	}elseif($DPISDB=="oci8"){
		$arr_search_condition[] = "(SUBSTR(g.SAH_EFFECTIVEDATE, 1, 10) < '".($search_budget_year - 543)."-10-01')";
		$arr_search_condition[] = "(SUBSTR(g.SAH_EFFECTIVEDATE, 1, 10) >= '".(($search_budget_year - 5) - 543)."-10-01')";
	}elseif($DPISDB=="mysql"){
		$arr_search_condition[] = "(LEFT(g.SAH_EFFECTIVEDATE, 10) < '".($search_budget_year - 543)."-10-01')";
		$arr_search_condition[] = "(LEFT(g.SAH_EFFECTIVEDATE, 10) >= '".(($search_budget_year - 5) - 543)."-10-01')";
	}

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

	include ("rpt_R006014_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R006014_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$year="5";
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$company_name = "";
	$report_title = "บัญชีรายละเอียดการเลื่อนขั้นเงินเดือน $PERSON_TYPE[$search_per_type]ย้อนหลัง ". ($year?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($year)):number_format($year)):"0")." ปี||$DEPARTMENT_NAME";
	$report_code = "R0614";
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
	
	if ($BKK_FLAG==1) {
		if ($search_promote_level == 0.0) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('49') or g.SM_CODE = '1')";
		} elseif ($search_promote_level == 0.5) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('45') or g.SM_CODE = '2')";
		} elseif ($search_promote_level == 1.0) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('46') or g.SM_CODE = '3')";
		} elseif ($search_promote_level == 1.5) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('47') or g.SM_CODE = '4')";
		} elseif ($search_promote_level == 2.0) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('48') or g.SM_CODE = '5')";
		} else {
		}
	} else {
		if ($search_promote_level == 0.0) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('49') or g.SM_CODE = '10')";
		} elseif ($search_promote_level == 0.5) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('45') or g.SM_CODE = '1')";
		} elseif ($search_promote_level == 1.0) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('46') or g.SM_CODE = '2')";
		} elseif ($search_promote_level == 1.5) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('47') or g.SM_CODE = '3')";
		} elseif ($search_promote_level == 2.0) {
			$search_mov_code = "(h.MOV_SUB_TYPE in ('48') or g.SM_CODE = '4')";
		}
	}

	if($DPISDB=="odbc"){
		$cmd = " select		PER_ID, g.MOV_CODE, LEFT(SAH_EFFECTIVEDATE,10) as EFFECTIVE_DATE
						 from		PER_SALARYHIS g, PER_MOVMENT h
						 where	g.MOV_CODE=h.MOV_CODE and (LEFT(SAH_EFFECTIVEDATE, 10) < '". ($search_budget_year - 543) ."-10-01') 
						 				and (LEFT(SAH_EFFECTIVEDATE, 10) >= '". (($search_budget_year - 5) - 543) ."-10-01')
										and $search_mov_code
						 order by LEFT(SAH_EFFECTIVEDATE, 10), g.MOV_CODE, PER_ID ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		PER_ID, g.MOV_CODE, SUBSTR(SAH_EFFECTIVEDATE, 1, 10) as EFFECTIVE_DATE
						 from		PER_SALARYHIS g, PER_MOVMENT h
						 where	g.MOV_CODE=h.MOV_CODE and (SUBSTR(SAH_EFFECTIVEDATE, 1, 10) < '". ($search_budget_year - 543) ."-10-01') 
						 				and (SUBSTR(SAH_EFFECTIVEDATE, 1, 10) >= '". (($search_budget_year - 5) - 543) ."-10-01')
										and $search_mov_code
						 order by SUBSTR(SAH_EFFECTIVEDATE, 1, 10), g.MOV_CODE, PER_ID ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		PER_ID, g.MOV_CODE, LEFT(SAH_EFFECTIVEDATE,10) as EFFECTIVE_DATE
						 from		PER_SALARYHIS g, PER_MOVMENT h
						 where	g.MOV_CODE=h.MOV_CODE and (LEFT(SAH_EFFECTIVEDATE, 10) < '". ($search_budget_year - 543) ."-10-01') 
						 				and (LEFT(SAH_EFFECTIVEDATE, 10) >= '". (($search_budget_year - 5) - 543) ."-10-01')
										and $search_mov_code
						 order by LEFT(SAH_EFFECTIVEDATE, 10), g.MOV_CODE, PER_ID ";
	} // end if
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$PER_ID = trim($data[PER_ID]);
		$MOV_CODE = trim($data[MOV_CODE]);
		$EFFECTIVE_DATE = trim($data[EFFECTIVE_DATE]);
		$EFFECTIVE_YEAR = substr($EFFECTIVE_DATE, 0, 4);
		if($EFFECTIVE_DATE >= "$EFFECTIVE_YEAR-10-01") $EFFECTIVE_YEAR += 1;
		
		$arr_promoted[($EFFECTIVE_YEAR + 543)][$MOV_CODE][] = $PER_ID;
	} // end if
	
//	echo "<pre>"; print_r($arr_promoted); echo "</pre>";
	if($DPISDB=="odbc"){
		$cmd = " select			distinct
											$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.MOV_CODE, g.SM_CODE $select_type_code $select_mgt_code
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
											$search_condition and $search_mov_code
						 order by		g.MOV_CODE, $order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)), a.PER_NAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct
											$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, TO_NUMBER($position_no) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.MOV_CODE, g.SM_CODE $select_type_code $select_mgt_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_ORG f, PER_SALARYHIS g, PER_MOVMENT h
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and 
											a.DEPARTMENT_ID=f.ORG_ID(+) and a.PER_ID=g.PER_ID and g.MOV_CODE=h.MOV_CODE(+) and $search_mov_code
											$search_condition
						 order by		g.MOV_CODE, $order_by, $position_no_name, TO_NUMBER($position_no), a.PER_NAME ";
// 		$cmd = "select * from (select rownum rnum, q1.* from (".$cmd.")  q1	) where rnum between 1 and 100";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct
											$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.MOV_CODE, g.SM_CODE $select_type_code $select_mgt_code
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
											$search_condition and $search_mov_code
						 order by		g.MOV_CODE, $order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)), a.PER_NAME ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd<br>";
	$data_count = $data_row = 0;
	$MOV_CODE = -1;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){		
	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
		if($MOV_CODE != trim($data[MOV_CODE])){
			$SM_CODE = trim($data[SM_CODE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$cmd = " select MOV_SUB_TYPE from PER_MOVMENT where	MOV_CODE='$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MOV_SUB_TYPE = trim($data2[MOV_SUB_TYPE]);
			$MOV_NAME = "";
			if (($MOV_SUB_TYPE == '45') || ($BKK_FLAG==1 && $SM_CODE == '2') || ($BKK_FLAG!=1 && $SM_CODE == '1')) {
				$MOV_NAME = "0.5 ขั้น";
			} elseif (($MOV_SUB_TYPE == '46') || ($BKK_FLAG==1 && $SM_CODE == '3') || ($BKK_FLAG!=1 && $SM_CODE == '2')) {
				$MOV_NAME = "1.0 ขั้น";
			} elseif (($MOV_SUB_TYPE == '47') || ($BKK_FLAG==1 && $SM_CODE == '4') || ($BKK_FLAG!=1 && $SM_CODE == '3')) {
				$MOV_NAME = "1.5 ขั้น";
			} elseif (($MOV_SUB_TYPE == '48') || ($BKK_FLAG==1 && $SM_CODE == '5') || ($BKK_FLAG!=1 && $SM_CODE == '4')) {
				$MOV_NAME = "2.0 ขั้น";
			} elseif (($MOV_SUB_TYPE == '49') || ($BKK_FLAG==1 && $SM_CODE == '1') || ($BKK_FLAG!=1 && $SM_CODE == '10')) {
				$MOV_NAME = "ไม่ได้เลื่อนเงินเดือน";
			}
			
			$arr_content[$data_count][type] = "PROMOTELEVEL";
			$arr_content[$data_count][name] = $MOV_NAME;
			
			$data_count++;
			initialize_parameter(0);
			$data_row = 0;
		} // end if
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" : 
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){
						$MINISTRY_ID = trim($data[MINISTRY_ID]);
						if($MINISTRY_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
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
						} // end if
						if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if

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
						if(trim($type_code)){
							$PT_CODE = trim($data[PT_CODE]);
							$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PT_NAME = trim($data3[PT_NAME]);	
						}
						if(trim($mgt_code)){
							$PM_CODE = trim($data[PM_CODE]);
							$cmd = " select PM_NAME from PER_MGT where	PM_CODE=".$PM_CODE;
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
						$arr_content[$data_count][name] = ($data_row?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($data_row)):number_format($data_row)):"-").".$PN_NAME" . "$PER_NAME $PER_SURNAME";
						$arr_content[$data_count][pos_no] =  (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
						$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
						for($i=0; $i<5; $i++) 
							$arr_content[$data_count][($search_budget_year - $i)] = in_array($PER_ID, $arr_promoted[($search_budget_year - $i)][$MOV_CODE])?"X":"";

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
		}
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
//		$pdf->AutoPageBreak = false;
//		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			for($i=0; $i<5; $i++) ${"PROMOTED_".($search_budget_year - $i)} = $arr_content[$data_count][($search_budget_year - $i)];
			
			if($REPORT_ORDER == "PROMOTELEVEL"){
				$MOV_NAME =$NAME ;
				$border = "";
//				$pdf->SetFont($fontb,'',14);
//				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

//				$pdf->AddPage();
//				$pdf->Cell(287, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), $border, 1, 'L', 0);
//				print_header();
				$RTF->paragraph();
				$RTF->new_page();
				$result = $RTF->add_text_line($NAME, 7, "", "L", "", "14", "b", 0, 0);
				if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
				$RTF->paragraph();
				$RTF->print_tab_header();
			}else{			
				$border = "";

				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $POS_NO;
				$arr_data[] = $POSITION;
				for($i=0; $i<5; $i++) $arr_data[] = ${"PROMOTED_".($search_budget_year - $i)};

				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if
		} // end for				
	}else{
//		$pdf->AddPage();

//		$pdf->SetFont($fontb,'',16);
//		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
	
?>