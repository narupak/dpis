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
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$g_name = "g.PL_NAME";
		$pl_code= "b.PL_CODE";
		$a_code= "a.PL_CODE";
		$b_name= "b.PL_NAME";
		$position_no= "b.POS_NO_NAME, b.POS_NO";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$g_name = "g.PN_NAME";
		$pl_code= "b.PN_CODE";
		$a_code= "a.PN_CODE";
		$b_name= "b.PN_NAME";
		$position_no= "b.POEM_NO_NAME, b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$g_name = "g.EP_NAME";
		$pl_code= "b.EP_CODE";
		$a_code= "a.EP_CODE";
		$b_name= "b.EP_NAME";
		$position_no= "b.POEMS_NO_NAME, b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$g_name = "g.TP_NAME";
		$pl_code= "b.TP_CODE";
		$a_code= "a.TP_CODE";
		$b_name= "b.TP_NAME";
		$position_no= "b.POT_NO_NAME, b.POT_NO";
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
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

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||บัญชีรายชื่อ$PERSON_TYPE[$search_per_type] เรียงตามอาวุโส";
	$report_code = "R0417";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 8);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 25);
		$worksheet->set_column(4, 4, 12);
		$worksheet->set_column(5, 5, 12);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 8);
		$worksheet->set_column(8, 8, 12);
		$worksheet->set_column(9, 9, 10);
		$worksheet->set_column(10, 10, 12);
		$worksheet->set_column(11, 11, 12);
		$worksheet->set_column(12, 12, 22);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "วันเข้าสู่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "วันเข้าสู่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ / สังกัด", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "วุฒิ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, "วันบรรจุ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, "เครื่อง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, "วันเดือนปี", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 11, "เกษียณ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, "การดำรงตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "ปัจจุบัน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "ก่อนปัจจุบัน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "ราชฯ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "เกิด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "อายุ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "ที่สำคัญ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
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
	
		if($search_per_type == 1){
			if($DPISDB=="odbc"){
				//1						b.PT_CODE, i.PT_NAME, 
				//1						) left join PER_TYPE i on (b.PT_CODE=i.PT_CODE)			
				$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
													$pl_code, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
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
								 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID,  $position_no,c.ORG_NAME, d.ORG_NAME, $pl_code, $g_name, 
													a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
													LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10)
								 order by		b.ORG_ID, $pl_code, a.LEVEL_NO, 
													MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)), LEFT(trim(a.PER_STARTDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1, $position_no,
													$pl_code, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
													SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
													MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG d, PER_PRENAME e, PER_POSITIONHIS f, $line_table g, PER_LEVEL h, PER_TYPE i
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and b.ORG_ID_1=d.ORG_ID(+) and a.PN_CODE=e.PN_CODE(+)
													and a.PER_ID=f.PER_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+) and $line_join(+) and b.PT_CODE=i.PT_CODE(+) and a.LEVEL_NO=h.LEVEL_NO(+)
													$search_condition
								 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, $position_no, c.ORG_NAME, d.ORG_NAME, $pl_code, $g_name, 
													a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
													SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
								 order by		b.ORG_ID, $pl_code, a.LEVEL_NO, 
													MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)), SUBSTR(trim(a.PER_STARTDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
													$pl_code, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
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
								 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, $position_no, c.ORG_NAME, d.ORG_NAME, $pl_code, $g_name, 
													a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, i.PT_NAME, a.PER_SALARY, 
													LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10)
								 order by		b.ORG_ID, $pl_code, a.LEVEL_NO, 
													MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)), LEFT(trim(a.PER_STARTDATE), 10) ";
			}
		}else{ // 2 || 3 || 4
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
													$pl_code, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, a.PER_SALARY, 
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
								 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID,  $position_no,c.ORG_NAME, d.ORG_NAME, $pl_code, $g_name, 
													a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, a.PER_SALARY, 
													LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10)
								 order by		b.ORG_ID, $pl_code, a.LEVEL_NO, 
													MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)), LEFT(trim(a.PER_STARTDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1, $position_no,
													$pl_code, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, a.PER_SALARY, 
													SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
													MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG d, PER_PRENAME e, PER_POSITIONHIS f, $line_table g, PER_LEVEL h
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and b.ORG_ID_1=d.ORG_ID(+) and a.PN_CODE=e.PN_CODE(+)
													and a.PER_ID=f.PER_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+) and $line_join(+) and a.LEVEL_NO=h.LEVEL_NO(+)
													$search_condition
								 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, $position_no, c.ORG_NAME, d.ORG_NAME, $pl_code, $g_name, 
													a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE,a.PER_SALARY, 
													SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
								 order by		b.ORG_ID, $pl_code, a.LEVEL_NO, 
													MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)), SUBSTR(trim(a.PER_STARTDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
													$pl_code, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL,  a.PER_SALARY, 
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
								 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, $position_no, c.ORG_NAME, d.ORG_NAME, $pl_code, $g_name, 
													a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, a.PER_SALARY, 
													LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10)
								 order by		b.ORG_ID, $pl_code, a.LEVEL_NO, 
													MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)), LEFT(trim(a.PER_STARTDATE), 10) ";
			}
		} //end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
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
			if($EDUCATE) $EDUCATE .= "\n";
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
							 where	a.PER_ID=$PER_ID and b.DC_TYPE in(1,2) and b.DC_ORDER < 99
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.DC_CODE=b.DC_CODE(+) and a.PER_ID=$PER_ID and b.DC_TYPE in(1,2) and b.DC_ORDER < 99
							 order by SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							 from		PER_DECORATEHIS a
											left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID and b.DC_TYPE in(1,2) and b.DC_ORDER < 99
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$DECORATE = trim($data2[DC_SHORTNAME]);
		$PREV_LEVEL_NO = str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT);
		if ($BKK_FLAG==1)
			$PREV_MOV_CODE = " '1', '23', '41', '58', '054', '102', '103', '104', '20001', '32', '33', '4', '105', '106', '18', '2', '107', '108', '109', '110', '10', '55', '3', '34', '36' ";
		else
			$PREV_MOV_CODE = " '10110', '10120', '10130', '10140', '10310', '10320', '10330', '10340', '10350', '10360', '10410', '10420', '10430', '10440', '10450', '10460', '10510', '10520' ";
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select 	distinct $a_code, $b_name, a.LEVEL_NO, a.PT_CODE, c.PT_NAME
								 from 		(
													PER_POSITIONHIS a
													left join $line_table b on ($a_code=$pl_code)
												) left join PER_TYPE c on (a.PT_CODE=c.PT_CODE)
								 where 	a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
												and a.MOV_CODE in ($PREV_MOV_CODE)
								 order by a.LEVEL_NO desc, $a_code ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	distinct $a_code, $b_name, a.LEVEL_NO, a.PT_CODE, c.PT_NAME
								 from 		PER_POSITIONHIS a, $line_table b, PER_TYPE c
								 where 	$a_code=$pl_code(+) and a.PT_CODE=c.PT_CODE(+)
												and a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
												and a.MOV_CODE in ($PREV_MOV_CODE)
								order by	a.LEVEL_NO desc, $a_code ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	distinct $a_code, $b_name, a.LEVEL_NO, a.PT_CODE, c.PT_NAME
								 from 		(
													PER_POSITIONHIS a
													left join $line_table b on ($a_code=$pl_code)
												) left join PER_TYPE c on (a.PT_CODE=c.PT_CODE)
								 where 	a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
												and a.MOV_CODE in ($PREV_MOV_CODE)
								 order by a.LEVEL_NO desc, $a_code ";
			} // end if
		}else{ // 2 || 3 || 4
				if($DPISDB=="odbc"){
					$cmd = " select 	$a_code as PL_CODE, $b_name as PL_NAME, a.LEVEL_NO
									 from 		PER_POSITIONHIS a
													left join $line_table b on ($a_code=$pl_code)
									 where 	a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
													and a.MOV_CODE in ($PREV_MOV_CODE)
									 order by a.LEVEL_NO desc, $a_code ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select 	$a_code as PL_CODE, $b_name as PL_NAME, a.LEVEL_NO
									 from 		PER_POSITIONHIS a, $line_table b
									 where 	$a_code=$pl_code(+)
													and a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
													and a.MOV_CODE in ($PREV_MOV_CODE)
									order by	a.LEVEL_NO desc, $a_code ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select 	$a_code as PL_CODE, $b_name as PL_NAME, a.LEVEL_NO
									 from 		PER_POSITIONHIS a
													left join $line_table b on ($a_code=$pl_code)
									 where 	a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
													and a.MOV_CODE in ($PREV_MOV_CODE)
									 order by a.LEVEL_NO desc, $a_code ";
				} // end if
		} //end if
		$count_positionhis = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = "$data_row.";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n". $ORG_NAME_1;
		$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][org_id] = $ORG_ID;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
		$arr_content[$data_count][pl_code] = $PL_CODE;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][level] = $POSITION_LEVEL;
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
			if(!$POSITIONHIS){ 
				$arr_content[$data_count][positionhis] = trim($data2[PL_NAME]) . $data3[POSITION_LEVEL] . ((trim($data2[PT_NAME]) != "ทั่วไป" && $data2[LEVEL_NO] >= 6)?$data2[PT_NAME]:"");
			}else{
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][org_id] = $ORG_ID;
				$arr_content[$data_count][pl_code] = $PL_CODE;
				$arr_content[$data_count][level] = $LEVEL_NO;
				$arr_content[$data_count][positionhis] = trim($data2[PL_NAME]) . $data3[POSITION_LEVEL] . ((trim($data2[PT_NAME]) != "ทั่วไป" && $data2[LEVEL_NO] >= 6)?$data2[PT_NAME]:"");
			} // end if

			if($POSITIONHIS) $POSITIONHIS .= "\n";
			$POSITIONHIS .= trim($data2[PL_NAME]) . $data3[POSITION_LEVEL] . ((trim($data2[PT_NAME]) != "ทั่วไป" && $data2[LEVEL_NO] >= 6)?$data2[PT_NAME]:"");
		} // end while
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
//		print_header();
		$ORG_ID = $PL_CODE = $LEVEL_NO = -1;
		$data_row = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			//if($PL_CODE && $LEVEL_NO){ 
				if($ORG_ID != $arr_content[$data_count][org_id] || $PL_CODE != $arr_content[$data_count][pl_code] || $LEVEL_NO != $arr_content[$data_count][level]){
					$ORG_ID = $arr_content[$data_count][org_id];
					$ORG_NAME = $arr_content[$data_count][org_name];
					$PL_CODE = $arr_content[$data_count][pl_code];
					$PL_NAME = $arr_content[$data_count][pl_name];
					$LEVEL_NO = $arr_content[$data_count][level];
					$cmd = "select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
					$db_dpis3->send_cmd($cmd);
	//				$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$POSITION_LEVEL=$data3[POSITION_LEVEL];
					
					//if($POSITION_LEVEL && $PL_NAME && $ORG_NAME){
						$report_title = "$DEPARTMENT_NAME||บัญชีรายชื่อ$PERSON_TYPE[$search_per_type]ระดับ ". $POSITION_LEVEL ." สายงาน $PL_NAME||ในสังกัด$ORG_NAME เรียงตามอาวุโส";
						$arr_title = explode("||", $report_title);
						for($i=0; $i<count($arr_title); $i++){
							if($xlsRow > 0 || ($xlsRow==0 && $i > 0)) $xlsRow++;
							$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
							$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
							$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
							$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
							$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
							$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
						} // end if
		
						print_header();
					//}
				} // end if if($ORG_ID != $arr_content[$data_count][org_id] || $PL_CODE != $arr_content[$data_count][pl_code] || $LEVEL_NO != $arr_content[$data_count][level])
			//}

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
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER)."--$PL_CODE//$LEVEL_NO", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION):$POSITION), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit($EDUCATE):$EDUCATE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, (($NUMBER_DISPLAY==2)?convert2thaidigit($EFFECTIVEDATE_CURRENT_LEVEL):$EFFECTIVEDATE_CURRENT_LEVEL), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, (($NUMBER_DISPLAY==2)?convert2thaidigit($BEG_C_DATE):$BEG_C_DATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, ($SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($SALARY)):number_format($SALARY)):""), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE):$STARTDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, (($NUMBER_DISPLAY==2)?convert2thaidigit($DECORATE):$DECORATE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 10, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE):$BIRTHDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 11, (($NUMBER_DISPLAY==2)?convert2thaidigit($RETIREDATE):$RETIREDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 12, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITIONHIS):$POSITIONHIS), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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