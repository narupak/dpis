<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004017_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		$ws_head_line1 = array("", "", "", "", "", "วันเข้าสู่", "วันเข้าสู่", "", "", "", "", "", "");
		$ws_head_line2 = array("ลำดับ", "เลขที่", "ชื่อ / สังกัด", "ตำแหน่ง", "วุฒิ", "ระดับ", "ระดับ", "เงินเดือน", "วันบรรจุ", "เครื่อง", "วันเดือนปี", "เกษียณ", "การดำรงตำแหน่ง");
		$ws_head_line3 = array("ที่", "ตำแหน่ง", "", "", "", "ปัจจุบัน", "ก่อนปัจจุบัน", "", "", "ราชฯ", "เกิด", "อายุ", "ที่สำคัญ");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
		$ws_border_line2 = array("LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR");
		$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(6,8,20,25,12,12,12,8,12,10,12,12,35);
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
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_fontfmt_line1, $ws_fontfmt_line2, $ws_fontfmt_line3;
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
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 3
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line3[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]]));
				$colseq++;
			}
		}
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
			$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
												$pl_seq_no, $pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, a.PER_SALARY, 
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
												$pl_seq_no, $pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, a.PER_SALARY, 
												SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
												MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG d, PER_PRENAME e, PER_POSITIONHIS f, $line_table g, PER_LEVEL h
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and b.ORG_ID_1=d.ORG_ID(+) and a.PN_CODE=e.PN_CODE(+)
												and a.PER_ID=f.PER_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+) and $line_join(+) and a.LEVEL_NO=h.LEVEL_NO(+)
												$search_condition
							 group by	a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $position_no, c.ORG_NAME, d.ORG_NAME, $pl_seq_no, 
												$pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, a.PER_SALARY, 
												SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
							 order by	c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $pl_seq_no, $pl_code, h.LEVEL_SEQ_NO desc, a.LEVEL_NO, 
												MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)), SUBSTR(trim(a.PER_STARTDATE), 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, c.ORG_NAME, d.ORG_NAME as ORG_NAME_1,  $position_no,
												$pl_seq_no, $pl_code, h.LEVEL_SEQ_NO, h.LEVEL_NAME, $g_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL, b.PT_CODE, a.PER_SALARY, 
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
							 order by		c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, $pl_seq_no, $pl_code, h.LEVEL_SEQ_NO desc, a.LEVEL_NO, 
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
		$LEVEL_NAME = $data[LEVEL_NAME];
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = "select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
		$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
		$data3 = $db_dpis3->get_array();
		$PT_NAME = trim($data3[PT_NAME]);

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
        
		 $cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE 
                            from PER_POSITIONHIS where PER_ID=$PER_ID and trim(POH_LEVEL_NO)='$LEVEL_NO'
							 and trim(mov_code) not IN(SELECT trim(mov_code) FROM per_movment WHERE mov_code IN(110,11010,11020)) ";
        $db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
        //$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE], $DATE_DISPLAY);//ของเดิม
        $POH_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE], $DATE_DISPLAY);/*20181019*/

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
				$cmd = " select 	distinct $a_code, $b_name, a.LEVEL_NO, a.PT_CODE
								 from 		(
													PER_POSITIONHIS a
													left join $line_table b on ($a_code=$pl_code)
												) left join PER_MOVMENT d on (a.MOV_CODE=d.MOV_CODE)
								 where 	a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
												and d.MOV_SUB_TYPE in ($PREV_SUB_TYPE)
								 order by a.LEVEL_NO desc, $a_code ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	distinct $a_code, $b_name, a.LEVEL_NO, a.PT_CODE
								 from 		PER_POSITIONHIS a, $line_table b, PER_MOVMENT d
								 where 	$a_code=$pl_code(+) and a.MOV_CODE=d.MOV_CODE(+)
												and a.PER_ID=$PER_ID and trim(a.LEVEL_NO) < '$PREV_LEVEL_NO'
												and d.MOV_SUB_TYPE in ($PREV_SUB_TYPE)
								order by	a.LEVEL_NO desc, $a_code ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	distinct $a_code, $b_name, a.LEVEL_NO, a.PT_CODE
								 from 		(
													PER_POSITIONHIS a
													left join $line_table b on ($a_code=$pl_code)
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
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n". $ORG_NAME_1;
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
//			$db_dpis3->show_error();
			$data3 = $db_dpis3->get_array();
			if($POSITIONHIS) $POSITIONHIS .= "\n";
			$POSITIONHIS .= trim($data2[PL_NAME]) . $data3[POSITION_LEVEL] . ((trim($data2[PT_NAME]) != "ทั่วไป" && $data2[LEVEL_NO] >= 6)?$data2[PT_NAME]:"");
		} // end while
		$arr_content[$data_count][positionhis] = "$POSITIONHIS";

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
//		print_header();

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("R","C","L","L","L","C","C","R","C","L","C","C","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		$ORG_ID = $PL_CODE = $LEVEL_NO = -1;
		$data_row = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			//if($PL_CODE && $LEVEL_NO){ 
				if($ORG_ID != $arr_content[$data_count][org_id] || $PL_CODE != $arr_content[$data_count][pl_code] || $LEVEL_NAME != $arr_content[$data_count][level]){
					$ORG_ID = $arr_content[$data_count][org_id];
					$ORG_NAME = $arr_content[$data_count][org_name];
					$PL_CODE = $arr_content[$data_count][pl_code];
					$PL_NAME = $arr_content[$data_count][pl_name];
					$LEVEL_NAME = $arr_content[$data_count][level];
					
					//if($POSITION_LEVEL && $PL_NAME && $ORG_NAME){
						$report_title = "$DEPARTMENT_NAME||บัญชีรายชื่อ$PERSON_TYPE[$search_per_type] ". $LEVEL_NAME ." สายงาน $PL_NAME||ในสังกัด$ORG_NAME เรียงตามอาวุโส";
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
//			echo "ORDER:$ORDER, NAME:$NAME, POSITIONHIS:$POSITIONHIS<br>";

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
//			$arr_data[] = "$ORDER--$PL_CODE//$LEVEL_NO";
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

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
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