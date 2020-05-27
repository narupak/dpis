<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004005_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$pl_code = "b.PL_CODE";
		$pl_name = "f.PL_NAME";
		$position_no= "b.POS_NO";
		$specific_field = "b.PT_CODE, g.PT_NAME,";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$pl_code = "b.PN_CODE";
		$pl_name = "f.PN_NAME";
		$position_no= "b.POEM_NO";
	}elseif($search_per_type == 3){		
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$pl_code = "b.EP_CODE";
		$pl_name = "f.EP_NAME";
		$position_no= "b.POEMS_NO";
	}elseif($search_per_type == 4){		
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$pl_code = "b.TP_CODE";
		$pl_name = "f.TP_NAME";
		$position_no= "b.POT_NO";
	} // end if
	
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("CO_LEVEL", "ORG");

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO";

				$heading_name .= " $CL_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2"; 	}
		else if($select_org_structure==1){	$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2"; }
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2"; } 
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_temp = explode("/", $search_date);
	$search_date = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	$show_date = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	$search_budget_year = $arr_temp[2];
	if($search_date >= (($arr_temp[2] - 543)."-10-01")) $search_budget_year = ($arr_temp[2] + 1);
	$arr_search_condition[] = "(trim(d.PRO_YEAR) = '$search_budget_year')";
	$arr_search_condition[] = "(d.PRO_TYPE = 2)";

	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]ผู้มีสิทธิ์เลื่อนตำแหน่งระดับควบตอนปลาย (ณ วันที่ $show_date)";
	$report_code = "R0405";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		$ws_head_line1 = array("หน่วยงาน/ชื่อ","ตำแหน่ง", "วุฒิ", "เลขที่", "วันบรรจุ", "วันเข้าสู่ระดับ", "เงินเดือน");
		$ws_head_line2 = array("", "", "", "ตำแหน่ง", "", "", "");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TLR");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C");
		$ws_width = array(42,35,25,8,12,12,8);
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	// คำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width ยังไม่ได้ บวก ความกว้าง ตัวที่ถูกตัดเข้าไป
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// บัญญัติไตรยางค์   ยอดรวมความกว้าง column ใน heading_width เทียบกับ ยอดรวมใน ws_width
		//                                แต่ละ column ใน ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// จบการเทียบค่าคำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2, $ws_fontfmt_line1, $ws_fontfmt_line2;
		global $ws_headalign_line1, $ws_headalign_line2, $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]]));
				$colseq++;
			}
		}
/*		
		$worksheet->set_column(0, 0, 42);
		$worksheet->set_column(1, 1, 35);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 8);
		$worksheet->set_column(4, 4, 12);
		$worksheet->set_column(5, 5, 12);
		$worksheet->set_column(6, 6, 8);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "หน่วยงาน/ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "วุฒิ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "วันบรรจุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "วันเข้าสู่ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
*/
	} // function		

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count,$DATE_DISPLAY,$select_org_structure;
		global $pl_name,$specific_field,$position_no,$position_table,$position_join,$line_table,$line_join;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, $pl_name as PL_NAME, a.LEVEL_NO,$specific_field
												$position_no, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_SALARY
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)	
															) inner join PER_PROMOTE_C d on (a.PER_ID=d.PER_ID)
														) left join PER_PRENAME e on (a.PN_CODE=e.PN_CODE)
													) left join $line_table on ($line_join)
												) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
							$search_condition
							 order by a.PER_NAME, a.PER_SURNAME ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, $pl_name as PL_NAME, a.LEVEL_NO,$specific_field
												$position_no, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, a.PER_SALARY
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PROMOTE_C d, PER_PRENAME e, $line_table f, PER_TYPE g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID
												and a.PN_CODE=e.PN_CODE(+) and $line_join(+) and b.PT_CODE=g.PT_CODE(+)
												$search_condition
							 order by a.PER_NAME, a.PER_SURNAME ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, $pl_name as PL_NAME, a.LEVEL_NO,$specific_field
												$position_no, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_SALARY
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)	
															) inner join PER_PROMOTE_C d on (a.PER_ID=d.PER_ID)
														) left join PER_PRENAME e on (a.PN_CODE=e.PN_CODE)
													) left join $line_table on ($line_join)
												) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
							$search_condition
							 order by a.PER_NAME, a.PER_SURNAME ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		while($data = $db_dpis2->get_array()){
			$data_count++;
			$person_count++;
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$POS_NO = trim($data[POS_NO]);
			$PL_NAME = trim($data[PL_NAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PT_CODE = trim($data[PT_CODE]);
			$PT_NAME = trim($data[PT_NAME]);
			$PER_SALARY = trim($data[PER_SALARY]);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
						
			if($DPISDB=="odbc")
				$cmd = " select 	min(LEFT(POH_EFFECTIVEDATE, 10)) as POH_EFFECTIVEDATE
								 from 		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
			elseif($DPISDB=="oci8")
				$cmd = " select 	min(SUBSTR(POH_EFFECTIVEDATE, 1, 10)) as POH_EFFECTIVEDATE
								 from 		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
			elseif($DPISDB=="mysql")
				$cmd = " select 	min(LEFT(POH_EFFECTIVEDATE, 10)) as POH_EFFECTIVEDATE
								 from 		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data2 = $db_dpis3->get_array();
			$LEVEL_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);

			$cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'
							 order by a.EN_CODE ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data2 = $db_dpis3->get_array();
			$EN_NAME = $data2[EN_NAME];

			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $person_count .". ". $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = $PL_NAME . " ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
			$arr_content[$data_count][educate] = $EN_NAME;	
			$arr_content[$data_count][posno] = $POS_NO;	
			$arr_content[$data_count][startdate] = $PER_STARTDATE;	
			$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;	
			$arr_content[$data_count][per_salary] = $PER_SALARY;

//			$data_count++;
		} // end while
		
		return;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $LEVEL_NO, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "CO_LEVEL" :	
					if(trim($LEVEL_NO)) $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO')";
					else $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO' or a.LEVEL_NO is null)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else if($select_org_structure==1){
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
		global $LEVEL_NO, $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "CO_LEVEL" :	
					$ORG_ID = -1;
				break;
				case "ORG" :
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join $position_table b on ($position_join) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_PROMOTE_C d on (a.PER_ID=d.PER_ID)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PROMOTE_C d
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join $position_table b on ($position_join) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_PROMOTE_C d on (a.PER_ID=d.PER_ID)
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
	$person_count = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "CO_LEVEL" :
					if($LEVEL_NO != trim($data[LEVEL_NO])){
						$LEVEL_NO = trim($data[LEVEL_NO]);

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "CO_LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) ."เลื่อนจากระดับ ". level_no_format($LEVEL_NO) ." เป็นระดับ ". level_no_format(($LEVEL_NO + 1));

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);

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
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		/**$temp_report_title = "$REF_NAME||$NAME||$report_title";
		$arr_title = explode("||", $temp_report_title);**/
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //if($company_name){
		
		print_header();

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","L","L","C","C","C","R");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][educate];
			$NAME_4 = $arr_content[$data_count][posno];
			$NAME_5 = $arr_content[$data_count][startdate];
			$NAME_6 = $arr_content[$data_count][level_effectivedate];
			$NAME_7 = ($arr_content[$data_count][per_salary])?number_format($arr_content[$data_count][per_salary]):"";

			$arr_data = (array) null;
			$arr_data[] = $NAME_1;	
			$arr_data[] = $NAME_2;	
			$arr_data[] = $NAME_3;					
			$arr_data[] = $NAME_4;			
			$arr_data[] = $NAME_5;			
			$arr_data[] = $NAME_6;
			$arr_data[] = $NAME_7;
	
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if($REPORT_ORDER == "ORG" || $REPORT_ORDER == "CO_LEVEL"){
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
/*
			if($REPORT_ORDER == "ORG" || $REPORT_ORDER == "CO_LEVEL"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_1):$NAME_1), set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_2):$NAME_2), set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_3):$NAME_3), set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_4):$NAME_4), set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_5):$NAME_5), set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_6):$NAME_6), set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_7):$NAME_7), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_1):$NAME_1), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_2):$NAME_2), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_3):$NAME_3), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_4):$NAME_4), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_5):$NAME_5), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_6):$NAME_6), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_7):$NAME_7), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			} // end if
*/
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