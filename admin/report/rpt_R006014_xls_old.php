<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

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
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

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

	if ($BKK_FLAG==1) {
		if ($search_promote_level == 0.0) {
			$search_mov_code = "(g.MOV_CODE in ('51', '20009', '20022') or (g.MOV_CODE in ('14', '25', '52', '53', '044', '050', '059', '20020', '20021', '20027') and g.SM_CODE = '1'))";
		} elseif ($search_promote_level == 0.5) {
			$search_mov_code = "(g.MOV_CODE in ('030', '052') or (g.MOV_CODE in ('14', '25', '52', '53', '044', '050', '059', '20020', '20021', '20027') and g.SM_CODE = '2'))";
		} elseif ($search_promote_level == 1.0) {
			$search_mov_code = "(g.MOV_CODE in ('013', '032') or (g.MOV_CODE in ('14', '25', '52', '53', '044', '050', '059', '20020', '20021', '20027') and g.SM_CODE = '3'))";
		} elseif ($search_promote_level == 1.5) {
			$search_mov_code = "(g.MOV_CODE in ('031', '033') or (g.MOV_CODE in ('14', '25', '52', '53', '044', '050', '059', '20020', '20021', '20027') and g.SM_CODE = '4'))";
		} elseif ($search_promote_level == 2.0) {
			$search_mov_code = "(g.MOV_CODE in ('014', '034') or (g.MOV_CODE in ('14', '25', '52', '53', '044', '050', '059', '20020', '20021', '20027') and g.SM_CODE = '5'))";
		} else {
		}
	} else {
		if ($search_promote_level == 0.0) {
			$search_mov_code = "(g.MOV_CODE in ('21370', '21375'))";
		} elseif ($search_promote_level == 0.5) {
			$search_mov_code = "(g.MOV_CODE in ('21310', '21351'))";
		} elseif ($search_promote_level == 1.0) {
			$search_mov_code = "(g.MOV_CODE in ('21320', '21352'))";
		} elseif ($search_promote_level == 1.5) {
			$search_mov_code = "(g.MOV_CODE in ('21330', '21353'))";
		} elseif ($search_promote_level == 2.0) {
			$search_mov_code = "(g.MOV_CODE in ('21340', '21354'))";
		}
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

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$year="5";
	$report_title = "บัญชีรายละเอียดการเลื่อนขั้นเงินเดือน $PERSON_TYPE[$search_per_type]ย้อนหลัง". ($year?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($year)):number_format($year)):"0")." ปี||$DEPARTMENT_NAME";
	$report_code = "R0614";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
/*
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
*/
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name, $search_budget_year;
		
		$worksheet->set_column(0, 0, 40);
		$worksheet->set_column(1, 1, 15);
		$worksheet->set_column(2, 2, 50);
		$worksheet->set_column(3, 7, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "หน่วยงาน/ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ปีงบประมาณ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=0; $i<5; $i++) $worksheet->write($xlsRow, ($i + 3), ($search_budget_year - $i), set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

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
	
	if ($BKK_FLAG==1) {
		$search_mov_code = "(MOV_CODE in ('030', '052', '013', '032', '031', '033', '014', '034') or (MOV_CODE in ('14', '25', '52', '53', '044', '050', '059', '20020', '20021', '20027') and SM_CODE in ('2', '3', '4', '5')))";
	} else {
		$search_mov_code = "(trim(MOV_CODE) in ('21310', '21320', '21330', '21340', '21351', '21352', '21353', '21354'))";
	}

	if($DPISDB=="odbc"){
		$cmd = " select		PER_ID, MOV_CODE, LEFT(SAH_EFFECTIVEDATE,10) as EFFECTIVE_DATE
						 from		PER_SALARYHIS
						 where	(LEFT(SAH_EFFECTIVEDATE, 10) < '". ($search_budget_year - 543) ."-10-01') 
						 				and (LEFT(SAH_EFFECTIVEDATE, 10) >= '". (($search_budget_year - 5) - 543) ."-10-01')
										and $search_mov_code
						 order by LEFT(SAH_EFFECTIVEDATE, 10), MOV_CODE, PER_ID ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		PER_ID, MOV_CODE, SUBSTR(SAH_EFFECTIVEDATE, 1, 10) as EFFECTIVE_DATE
						 from		PER_SALARYHIS
						 where	(SUBSTR(SAH_EFFECTIVEDATE, 1, 10) < '". ($search_budget_year - 543) ."-10-01') 
						 				and (SUBSTR(SAH_EFFECTIVEDATE, 1, 10) >= '". (($search_budget_year - 5) - 543) ."-10-01')
										and $search_mov_code
						 order by SUBSTR(SAH_EFFECTIVEDATE, 1, 10), MOV_CODE, PER_ID ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		PER_ID, MOV_CODE, LEFT(SAH_EFFECTIVEDATE,10) as EFFECTIVE_DATE
						 from		PER_SALARYHIS
						 where	(LEFT(SAH_EFFECTIVEDATE, 10) < '". ($search_budget_year - 543) ."-10-01') 
						 				and (LEFT(SAH_EFFECTIVEDATE, 10) >= '". (($search_budget_year - 5) - 543) ."-10-01')
										and $search_mov_code
						 order by LEFT(SAH_EFFECTIVEDATE, 10), MOV_CODE, PER_ID ";
	} // end if
	$db_dpis->send_cmd($cmd);
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
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.MOV_CODE $select_type_code $select_mgt_code
						 from			(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
											$search_condition
						 order by		g.MOV_CODE, $order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)), a.PER_NAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct
											$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, TO_NUMBER($position_no) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.MOV_CODE $select_type_code $select_mgt_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_SALARYHIS g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and a.PER_ID=g.PER_ID
											$search_condition
						 order by		g.MOV_CODE, $order_by, $position_no_name, TO_NUMBER($position_no), a.PER_NAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct
											$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.MOV_CODE $select_type_code $select_mgt_code
						 from			(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
											$search_condition
						 order by		g.MOV_CODE, $order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)), a.PER_NAME ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd<br>";
	$data_count = $data_row = 0;
	$MOV_CODE = -1;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){		
		if($MOV_CODE != trim($data[MOV_CODE])){
			$MOV_CODE = trim($data[MOV_CODE]);
			
			if ($BKK_FLAG==1) {
				if ($MOV_CODE == '030' || $MOV_CODE == '052') {
					$MOV_NAME = "0.5 ขั้น";
				} elseif ($MOV_CODE == '013' || $MOV_CODE == '032') {
					$MOV_NAME = "1.0 ขั้น";
				} elseif ($MOV_CODE == '031' || $MOV_CODE == '033') {
					$MOV_NAME = "1.5 ขั้น";
				} elseif ($MOV_CODE == '014' || $MOV_CODE == '034') {
					$MOV_NAME = "2.0 ขั้น";
				} elseif ($MOV_CODE == '14' || $MOV_CODE == '25' || $MOV_CODE == '52' || $MOV_CODE == '53' || $MOV_CODE == '044' || $MOV_CODE == '050' || 
					$MOV_CODE == '059' || $MOV_CODE == '20020' || $MOV_CODE == '20021' || $MOV_CODE == '20027') {
					if ($SM_CODE == '2') {
						$MOV_NAME = "0.5 ขั้น";
					} elseif ($SM_CODE == '3') {
						$MOV_NAME = "1.0 ขั้น";
					} elseif ($SM_CODE == '4') {
						$MOV_NAME = "1.5 ขั้น";
					} elseif ($SM_CODE == '5') {
						$MOV_NAME = "2.0 ขั้น";
					}
				}
			} else {
			/*
				// ของอันอื่น ที่ไม่ใช่ กทม ถ้าเปิด comment มันจะรัน excell ไม่ออก 
				if ($MOV_CODE == '21310' || $MOV_CODE == '21351') {
					$MOV_NAME = "0.5 ขั้น";
				} elseif ($MOV_CODE == '21320' || $MOV_CODE == '21352') {
					$MOV_NAME = "1.0 ขั้น";
				} elseif ($MOV_CODE == '21330' || $MOV_CODE == '21353') {
					$MOV_NAME = "1.5 ขั้น";
				} elseif ($MOV_CODE == '21340' || $MOV_CODE == '21354') {
					$MOV_NAME = "2.0 ขั้น";
				}
			*/
			} // end if
			
			$arr_content[$data_count][type] = "PROMOTELEVEL";
			$arr_content[$data_count][name] = $MOV_NAME;
			
			$data_count++;
			initialize_parameter(0);
			$data_row = 0;
		} // end if($MOV_CODE != trim($data[MOV_CODE]))
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
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
					} // end if

					if($rpt_order_index == (count($arr_rpt_order) - 1)){
						$data_row++;
						
						$PER_ID = $data[PER_ID];
						$PN_NAME = trim($data[PN_NAME]);
						$PER_NAME = trim($data[PER_NAME]);
						$PER_SURNAME = trim($data[PER_SURNAME]);
						$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);
						$LEVEL_NO = $data[LEVEL_NO];
						if(trim($type_code)){
							$PT_CODE = trim($data[PT_CODE]);
							$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
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
						$arr_content[$data_count][name] = ($data_row?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($data_row)):number_format($data_row)):"").".$PN_NAME" . "$PER_NAME $PER_SURNAME";
						$arr_content[$data_count][pos_no] = (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
						$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
						for($i=0; $i<5; $i++) {
							$arr_content[$data_count][($search_budget_year - $i)] = in_array($PER_ID, $arr_promoted[($search_budget_year - $i)][$MOV_CODE])?"X":"";
						}

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			for($i=0; $i<5; $i++) ${"PROMOTED_".($search_budget_year - $i)} = $arr_content[$data_count][($search_budget_year - $i)];
			
			if($REPORT_ORDER == "PROMOTELEVEL"){
				$worksheet = &$workbook->addworksheet("$NAME");
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
	
				//====================== SET FORMAT ======================//
				require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
				//====================== SET FORMAT ======================//

				$xlsRow = 0;
				$arr_title = explode("||", $report_title);
				for($i=0; $i<count($arr_title); $i++){
					$xlsRow = $i;
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				} // end for
		
				if($company_name){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				} // end if
		
				print_header();
			}else{
				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0){
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				}else{
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, "$POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write($xlsRow, 2, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					for($i=0; $i<5; $i++) {
						$worksheet->write($xlsRow, ($i+3), ${"PROMOTED_".($search_budget_year - $i)}, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					}
				} // end if
			} // end if
		} // end for				
	}else{
		$worksheet = &$workbook->addworksheet("เลื่อน $NAME ขั้น");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
	
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>