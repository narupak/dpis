<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if	
	
	//มาจาก M0305 ระดับตำแหน่งที่ใช้งานทั้งหมดจะขึ้นมา (ใช้ชื่อย่อ)
/*
	$cmd = "select LEVEL_NO, LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) order by  LEVEL_SEQ_NO,LEVEL_NO";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){ 
		$ARR_LEVEL_NO[] = $data[LEVEL_NO];		
		if(trim($data[LEVEL_SHORTNAME])){	$LNAME=str_replace("ระดับ","",$data[LEVEL_SHORTNAME]); }
		else{	$LNAME=str_replace("กลุ่มงาน","",$data[LEVEL_NAME]); }
		$ARR_LEVEL_SHORTNAME[] = $LNAME;
	}
*/	
	if(!trim($RPTORD_LIST)){ 
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
				$select_list .= "g.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "g.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" : 
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

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
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; 	}
		else if($select_org_structure==1){	$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; }
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; } 
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

	if($search_year_min && $search_year_max) $show_year = "||ตั้งแต่ปี $search_year_min - $search_year_max";
	elseif($search_year_min) $show_year = "||ตั้งแต่ปี $search_year_min";
	elseif($search_year_max) $show_year = "||ถึงปี $search_year_max";

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานข้อมู$PERSON_TYPE[$search_per_type]ที่ลาไปศึกษาในประเทศ จำแนกตาม$ORG_TITLE". $show_year;
	$report_code = "R0416";
    include ("rpt_R004016_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
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
		$ws_head_line1 = (array) null;
		$ws_head_line2 = (array) null;
		$ws_head_line3 = (array) null;
		$ws_head_line1[0] = "";
		$ws_head_line2[0] = "$heading_name";
		$ws_head_line3[0] = "";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
			$ws_head_line1[$i+1] = "<**1**>";
			$ws_head_line2[$i+1] = "<**1**>ระดับตำแหน่ง";
			$ws_head_line3[$i+1] = "$tmp_level_shortname";
		} // end for
		$ws_head_line1[14] = "";
		$ws_head_line2[14] = "จำนวน";
		$ws_head_line3[14] = "";
		$ws_head_line1[15] = "รวม";
		if($search_per_type==1) { $ws_head_line2[15] = "ขรก."; }
		else if($search_per_type==2) { $ws_head_line2[15] = "ลูกจ้าง"; }
		else if($search_per_type==3) { $ws_head_line2[15] = "พนักงาน"; }
		else if($search_per_type==4) { $ws_head_line2[15] = "ลูกจ้างชั่วคราว"; }
		$ws_head_line3[15] = "ร้อยละ";
		$ws_head_line1[16] = "";
		$ws_head_line2[16] = "ทั้งหมด";
		$ws_head_line3[16] = "";
		$ws_colmerge_line1 = array(0,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0);
		$ws_colmerge_line2 = array(0,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0);
		$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TL","T","T","T","T","T","T","T","T","T","T","T","TR","TLR","TLR","TLR");
		$ws_border_line2 = array("LR","BL","B","B","B","B","B","B","B","B","B","B","B","BR","RL","RL","RL");
		$ws_border_line3 = array("LBR","BL","BL","BL","BL","BL","BL","BL","BL","BL","BL","BL","BL","BL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(45,6,6,6,6,6,6,6,6,6,6,6,6,6,10,10,10);
		$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_fill_color = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_font_color = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
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
		global $workbook, $worksheet, $xlsRow, $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_fontfmt_line1, $ws_fontfmt_line2, $ws_fontfmt_line3;
		global $ws_headalign_line1, $ws_headalign_line2, $ws_width;
		global $ws_wraptext_line1, $ws_rotate_line1, $ws_fill_color, $ws_font_color;

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
/*		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}*/
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line1, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line1, $ws_colmerge_line1, $ws_headalign_line1, $ws_wraptext_line1, $ws_rotate_line1, $ws_font_color, $ws_fill_color);
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
/*		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}*/
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line2, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line1, $ws_wraptext_line1, $ws_rotate_line1, $ws_font_color, $ws_fill_color);
		// loop พิมพ์ head บรรทัดที่ 3
		$xlsRow++;
/*		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line3[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
//				$worksheet->write($xlsRow, $colseq, $ws_head_line3[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]]));
				$colseq++;
			}
		}*/
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line3, $ws_head_line3, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line3, $ws_colmerge_line3, $ws_headalign_line1, $ws_wraptext_line1, $ws_rotate_line1, $ws_font_color, $ws_fill_color);
/*		
		$worksheet->set_column(0, 0, 45);
		$worksheet->set_column(1, 11, 6);
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$x=$i+1;
			$worksheet->set_column($x, 11, 6);
		}
		$worksheet->set_column(count($ARR_LEVEL_SHORTNAME)+1, 14, 10);
		$worksheet->set_column(count($ARR_LEVEL_SHORTNAME)+2, 14, 10);
		$worksheet->set_column(count($ARR_LEVEL_SHORTNAME)+3, 14, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$x=$i+1;
			$worksheet->write($xlsRow,$x, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));
		}
		$worksheet->write($xlsRow,count($ARR_LEVEL_SHORTNAME)+1, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow,count($ARR_LEVEL_SHORTNAME)+2, "จำนวน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow,count($ARR_LEVEL_SHORTNAME)+3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "$heading_name", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$x=$i+1;
			if($x==1){ 	
				$worksheet->write($xlsRow,$x, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
			}else{
				$worksheet->write($xlsRow, $x, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
			}
		}		
		$worksheet->write($xlsRow, count($ARR_LEVEL_SHORTNAME)+1, "รวม", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		if($search_per_type==1) { $worksheet->write($xlsRow, count($ARR_LEVEL_SHORTNAME)+2, "ขรก.", set_format("xlsFmtTableHeader", "B", "C", "LR", 0)); }
		else if($search_per_type==2) { $worksheet->write($xlsRow, count($ARR_LEVEL_SHORTNAME)+2, "ลูกจ้าง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0)); }
		else if($search_per_type==3) { $worksheet->write($xlsRow,count($ARR_LEVEL_SHORTNAME)+2, "พนักงาน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0)); }
		else if($search_per_type==4) { $worksheet->write($xlsRow,count($ARR_LEVEL_SHORTNAME)+2, "ลูกจ้างชั่วคราว", set_format("xlsFmtTableHeader", "B", "C", "LR", 0)); }
		$worksheet->write($xlsRow, count($ARR_LEVEL_SHORTNAME)+3, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$x=$i+1;
			$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
			$worksheet->write($xlsRow, $x, "$tmp_level_shortname", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		}
		$worksheet->write($xlsRow, count($ARR_LEVEL_SHORTNAME)+1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, count($ARR_LEVEL_SHORTNAME)+2, "ทั้งหมด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, count($ARR_LEVEL_SHORTNAME)+3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
*/
	} // function		

	function count_person($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type, $search_year_min, $search_year_max, $select_org_structure;
/*
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.CT_CODE) = '140')";	//ศึกษาในประเทศ***
		if($DPISDB=="odbc"){ 
			if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) >= '".($search_year_min - 543)."')";
			if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) <= '".($search_year_max - 543)."')";
		}elseif($DPISDB=="oci8"){
			if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(SUBSTR(trim(d.SC_STARTDATE), 1, 4) >= '".($search_year_min - 543)."')";
			if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(SUBSTR(trim(d.SC_STARTDATE), 1, 4) <= '".($search_year_max - 543)."')";
		}elseif($DPISDB=="mysql"){
			if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) >= '".($search_year_min - 543)."')";
			if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) <= '".($search_year_max - 543)."')";
		} // end if
		if($level_no){
			$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
			$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
		} // end if
*/
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($level_no){
			$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
			$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.CT_CODE) = '140')";	//ศึกษาในประเทศ***
			if($DPISDB=="odbc"){ 
				if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) >= '".($search_year_min - 543)."')";
				if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) <= '".($search_year_max - 543)."')";
			}elseif($DPISDB=="oci8"){
				if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(SUBSTR(trim(d.SC_STARTDATE), 1, 4) >= '".($search_year_min - 543)."')";
				if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(SUBSTR(trim(d.SC_STARTDATE), 1, 4) <= '".($search_year_max - 543)."')";
			}elseif($DPISDB=="mysql"){
				if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) >= '".($search_year_min - 543)."')";
				if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) <= '".($search_year_max - 543)."')";
			} // end if
		} // end if

		// นับการลาศึกษาจาก PER_SCHOLAR
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from		(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on $position_join 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							group by		a.PER_ID ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e, PER_ORG g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.PER_ID=d.PER_ID(+) and d.INS_CODE=e.INS_CODE(+) and a.DEPARTMENT_ID=g.ORG_ID
												$search_condition
							 group by 	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from		(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on $position_join 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							group by		a.PER_ID ";
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
//		echo "count cmd=$cmd ($count_person)<br>";

/*****************
		//ไม่นับรวม*********	
		if($level_no){
			// นับการลาฝึกอบรมจาก PER_TRAIN
			$search_condition = str_replace("d.SC_STARTDATE", "d.TRN_STARTDATE", $search_condition);
			$search_condition = str_replace("e.CT_CODE", "d.CT_CODE", $search_condition);
			$search_condition .= (trim($search_condition)?" and ":" where ") . "(e.TR_TYPE in (1))";

			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_TRAINING d on (a.PER_ID=d.PER_ID)
													) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
								$search_condition
								group by		a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
													and a.PER_ID=d.PER_ID(+) and d.TR_CODE=e.TR_CODE(+)
													$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_TRAINING d on (a.PER_ID=d.PER_ID)
													) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
								$search_condition
								group by		a.PER_ID ";
			} // end if	
			if($select_org_structure==1) { 
				$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
				$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			}
			$count_person_2 = $db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			//echo $cmd."<hr>";
			if($count_person_2==1){
				$data = $db_dpis2->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if($data[count_person] == 0) $count_person += 0;
			}else{
				$count_person += $count_person_2;
			} // end if
		} // end if
************/
		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
//				case "MINISTRY" :
//					if($MINISTRY_ID && $MINISTRY_ID!=-1 && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
//					break;
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
		$cmd = " select			distinct $select_list
						 from		(
											(
												PER_PERSONAL a 
												left join $position_table b on ($position_join) 
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(
											(
												PER_PERSONAL a 
												left join $position_table b on ($position_join) 
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
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
	unset($GRAND_LEVEL);
	$GRAND_ALL = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["count_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_LEVEL[$tmp_level_no] += $arr_content[$data_count]["count_".$tmp_level_no];
//							echo "MINISTRY==>GRAND_LEVEL($tmp_level_no)=".$GRAND_LEVEL[$tmp_level_no]."<br>";
							//$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							//if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for

						$arr_content[$data_count][count_all] = count_person(0, $search_condition, $addition_condition);
						if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_ALL += $arr_content[$data_count][count_all];

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
					break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
							if($DEPARTMENT_NAME=="-")	$DEPARTMENT_NAME = $DEPARTMENT_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["count_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_LEVEL[$tmp_level_no] += $arr_content[$data_count]["count_".$tmp_level_no];
							//$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							//if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for

						$arr_content[$data_count][count_all] = count_person(0, $search_condition, $addition_condition);
						if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_ALL += $arr_content[$data_count][count_all];
//						echo "DEPARTMENT==>GRAND_LEVEL($tmp_level_no)=".$GRAND_LEVEL[$tmp_level_no]."<br>";

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

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
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["count_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_LEVEL[$tmp_level_no] += $arr_content[$data_count]["count_".$tmp_level_no];
//							echo "ORG==>GRAND_LEVEL($tmp_level_no)=".$GRAND_LEVEL[$tmp_level_no]."<br>";
							//$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
							//if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for

						$arr_content[$data_count][count_all] = count_person(0, $search_condition, $addition_condition);
						if ($rpt_order_index==count($arr_rpt_order)-1) $GRAND_ALL += $arr_content[$data_count][count_all];
//						echo "data_count=$data_count, count_all=".$arr_content[$data_count][count_all]." , GRAND_ALL=$GRAND_ALL<br>";

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_TOTAL = 0;
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				${"COUNT_".$tmp_level_no} = $arr_content[$data_count]["count_".$tmp_level_no];
				$COUNT_TOTAL += ${"COUNT_".$tmp_level_no};
//				echo ">>$NAME--tmp_level_no=$tmp_level_no(".${"COUNT_".$tmp_level_no}.") (COUNT_TOTAL=$COUNT_TOTAL)<br>";
			} // end for
			$COUNT_ALL = $arr_content[$data_count][count_all];
			$PERCENT_TOTAL = "";
			if($COUNT_ALL) $PERCENT_TOTAL = ($COUNT_TOTAL / $COUNT_ALL) * 100;
//			echo "COUNT_ALL=$COUNT_ALL , COUNT_TOTAL=$COUNT_TOTAL , PERCENT_TOTAL=$PERCENT_TOTAL<br>";

			$arr_data = (array) null;
			$arr_data[] = $NAME;
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				$arr_data[] = ${"COUNT_".$tmp_level_no};
			} // end for
			$arr_data[] =  $COUNT_TOTAL;
			$arr_data[] = $COUNT_ALL;
			$arr_data[] =  $PERCENT_TOTAL;

//			echo "0..col_function(".implode("|",$column_function).")<br>";
//			echo "1..".implode("|",$arr_data)."<br>";
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
//			echo "2..".implode("|",$arr_aggreg_data)."<br>";
				
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
/*
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$x=$i+1;
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				$worksheet->write($xlsRow, $x, (${"COUNT_".$tmp_level_no}?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(${"COUNT_".$tmp_level_no})):number_format(${"COUNT_".$tmp_level_no})):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			}
			$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+1, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+2, ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_ALL)):number_format($COUNT_ALL)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+3, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
*/
		} // end for				

		$GRAND_TOTAL = array_sum($GRAND_LEVEL);
		$PERCENT_TOTAL = "";
		if($GRAND_ALL) $PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_ALL) * 100;

		$arr_data = (array) null;
		$arr_data[] = "รวม";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_data[] = $GRAND_LEVEL[$tmp_level_no];
		} // end for
		$arr_data[] =  $GRAND_TOTAL;
		$arr_data[] =  $GRAND_ALL;
		$arr_data[] =  $PERCENT_TOTAL;

		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
				else $ndata = $arr_data[$arr_column_map[$i]];
				$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
/*
		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$x=$i+1;
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$worksheet->write($xlsRow, $x, ($GRAND_LEVEL[$tmp_level_no]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_LEVEL[$tmp_level_no])):number_format($GRAND_LEVEL[$tmp_level_no])):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		}
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+1, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+2, ($GRAND_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_ALL)):number_format($GRAND_ALL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+3, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
*/
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$x=$i+1;
			$worksheet->write($xlsRow, $x, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
		$worksheet->write($xlsRow,count($ARR_LEVEL_NO)+1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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