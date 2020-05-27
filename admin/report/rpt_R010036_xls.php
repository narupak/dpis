<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R010036_xls_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("MINISTRY", "DEPARTMENT", "ORG", "LINE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF";

				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.PL_SEQ_NO, a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.PL_SEQ_NO, a.PL_CODE";

				$heading_name .= " $PL_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){
			$order_by = "c.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){
			$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){
			$select_list = "c.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){
			$select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if

	$search_condition = "";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		$search_pl_code = trim($search_pl_code);
		$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
		$list_type_text .= "$search_pl_name";
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "$list_type_text";
	$report_title = "$DEPARTMENT_NAME||กรอบอัตรากำลังข้าราชการ ประจำปีงบประมาณ พ.ศ. $search_budget_year||จำแนกตามสายงาน ระดับตำแหน่ง คนครอง และอัตราว่าง (ฐานข้อมูลจากอัตราเงินเดือนตั้งจ่าย)";
	
	if($export_type=="report")	
    $report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);

	$report_code = "R1036";

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
		$ws_head_line4 = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$buff = explode("|", $heading_text[$i]);
			$ws_head_line1[$i] = $buff[0];
			$ws_head_line2[$i] = $buff[1];
			$ws_head_line3[$i] = $buff[2];
			$ws_head_line4[$i] = $buff[3];
		}
//		echo "line1:".implode(",",$ws_head_line1)."<br>";
//		echo "line2:".implode(",",$ws_head_line2)."<br>";
//		echo "line3:".implode(",",$ws_head_line3)."<br>";
//		echo "line4:".implode(",",$ws_head_line4)."<br>";
		$ws_colmerge_line1[0] = 0;
		$ws_colmerge_line2[0] = 0;
		$ws_colmerge_line3[0] = 0;
		$ws_colmerge_line4[0] = 0;
		$ws_colmerge_line1[1] = 0;
		$ws_colmerge_line2[1] = 0;
		$ws_colmerge_line3[1] = 0;
		$ws_colmerge_line4[1] = 0;
		$ws_border_line1[0] = "TLR";
		$ws_border_line2[0] = "LR";
		$ws_border_line3[0] = "LR";
		$ws_border_line4[0] = "LBR";
		$ws_border_line1[1] = "TLR";
		$ws_border_line2[1] = "LR";
		$ws_border_line3[1] = "LR";
		$ws_border_line4[1] = "LBR";
		$ws_fontfmt_line1[0] = "B";
		$ws_fontfmt_line1[1] = "B";
		$ws_headalign_line1[0] = "C";
		$ws_headalign_line1[1] = "C";
		$ws_width[0] = 5;
		$ws_width[1] = 25;
		$cnt_level_grp = count($ARR_LEVEL_GROUP);
		$cnt_level_det = 0;
		for($i=0; $i<$cnt_level_grp; $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				$ws_colmerge_line1[$cnt_level_det*3+2] = 1;
				$ws_colmerge_line2[$cnt_level_det*3+2] = 1;
				$ws_colmerge_line3[$cnt_level_det*3+2] = 1;
				$ws_colmerge_line4[$cnt_level_det*3+2] = 0;
				$ws_colmerge_line1[$cnt_level_det*3+3] = 1;
				$ws_colmerge_line2[$cnt_level_det*3+3] = 1;
				$ws_colmerge_line3[$cnt_level_det*3+3] = 1;
				$ws_colmerge_line4[$cnt_level_det*3+3] = 0;
				$ws_colmerge_line1[$cnt_level_det*3+4] = 1;
				$ws_colmerge_line2[$cnt_level_det*3+4] = 1;
				$ws_colmerge_line3[$cnt_level_det*3+4] = 1;
				$ws_colmerge_line4[$cnt_level_det*3+4] = 0;
				$ws_border_line1[$cnt_level_det*3+2] = "TL";
				$ws_border_line2[$cnt_level_det*3+2] = "TL";
				$ws_border_line3[$cnt_level_det*3+2] = "TL";
				$ws_border_line4[$cnt_level_det*3+2] = "TLBR";
				$ws_border_line1[$cnt_level_det*3+3] = "TL";
				$ws_border_line2[$cnt_level_det*3+3] = "T";
				$ws_border_line3[$cnt_level_det*3+3] = "T";
				$ws_border_line4[$cnt_level_det*3+3] = "TLBR";
				$ws_border_line1[$cnt_level_det*3+4] = "TL";
				$ws_border_line2[$cnt_level_det*3+4] = "TL";
				$ws_border_line3[$cnt_level_det*3+4] = "TL";
				$ws_border_line4[$cnt_level_det*3+4] = "TLBR";
				$ws_fontfmt_line1[$cnt_level_det*3+2] = "B";
				$ws_fontfmt_line1[$cnt_level_det*3+3] = "B";
				$ws_fontfmt_line1[$cnt_level_det*3+4] = "B";
				$ws_headalign_line1[$cnt_level_det*3+2] = "C";
				$ws_headalign_line1[$cnt_level_det*3+3] = "C";
				$ws_headalign_line1[$cnt_level_det*3+4] = "C";
				$ws_width[$cnt_level_det*3+2] = 5;
				$ws_width[$cnt_level_det*3+3] = 5;
				$ws_width[$cnt_level_det*3+4] = 5;
				$cnt_level_det++;
			}
		} // loop for
		$ws_colmerge_line1[$cnt_level_det*3+2] = 1;
		$ws_colmerge_line2[$cnt_level_det*3+2] = 1;
		$ws_colmerge_line3[$cnt_level_det*3+2] = 1;
		$ws_colmerge_line4[$cnt_level_det*3+2] = 0;
		$ws_colmerge_line1[$cnt_level_det*3+3] = 1;
		$ws_colmerge_line2[$cnt_level_det*3+3] = 1;
		$ws_colmerge_line3[$cnt_level_det*3+3] = 1;
		$ws_colmerge_line4[$cnt_level_det*3+3] = 0;
		$ws_colmerge_line1[$cnt_level_det*3+4] = 1;
		$ws_colmerge_line2[$cnt_level_det*3+4] = 1;
		$ws_colmerge_line3[$cnt_level_det*3+4] = 1;
		$ws_colmerge_line4[$cnt_level_det*3+4] = 0;
		$ws_border_line1[$cnt_level_det*3+2] = "TL";
		$ws_border_line2[$cnt_level_det*3+2] = "L";
		$ws_border_line3[$cnt_level_det*3+2] = "L";
		$ws_border_line4[$cnt_level_det*3+2] = "TLBR";
		$ws_border_line1[$cnt_level_det*3+3] = "T";
		$ws_border_line2[$cnt_level_det*3+3] = "";
		$ws_border_line3[$cnt_level_det*3+3] = "";
		$ws_border_line4[$cnt_level_det*3+3] = "TLBR";
		$ws_border_line1[$cnt_level_det*3+4] = "TR";
		$ws_border_line2[$cnt_level_det*3+4] = "R";
		$ws_border_line3[$cnt_level_det*3+4] = "R";
		$ws_border_line4[$cnt_level_det*3+4] = "TLBR";
		$ws_fontfmt_line1[$cnt_level_det*3+2] = "B";
		$ws_fontfmt_line1[$cnt_level_det*3+3] = "B";
		$ws_fontfmt_line1[$cnt_level_det*3+4] = "B";
		$ws_headalign_line1[$cnt_level_det*3+2] = "C";
		$ws_headalign_line1[$cnt_level_det*3+3] = "C";
		$ws_headalign_line1[$cnt_level_det*3+4] = "C";
		$ws_width[$cnt_level_det*3+2] = 5;
		$ws_width[$cnt_level_det*3+3] = 5;
		$ws_width[$cnt_level_det*3+4] = 5;
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
	
	function set_cell_width(){
		global $worksheet, $TOTAL_LEVEL;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
//		$worksheet->set_column(0, 0, 5);
//		$worksheet->set_column(1, 1, 25);
//		$worksheet->set_column(2, (($TOTAL_LEVEL * 3)+3), 5);
	} // function
	
	function print_header(){
		global $workbook, $worksheet, $xlsRow, $heading_name;
		global $ARR_LEVEL_GROUP, $ARR_LEVEL_GROUP_NAME, $ARR_LEVEL, $ARR_LEVEL_NAME, $TOTAL_LEVEL;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_head_line4; 
		global $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_colmerge_line4;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_border_line4;
		global $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

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
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line1, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line1, $ws_colmerge_line1, $ws_headalign_line1);

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
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line2, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line1);
		
		// loop พิมพ์ head บรรทัดที่ 3
		$xlsRow++;
/*		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line3[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}*/
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line3, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line3, $ws_colmerge_line3, $ws_headalign_line1);

		// loop พิมพ์ head บรรทัดที่ 4
		$xlsRow++;
/*		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line4[$arr_column_map[$i]], $ws_border_line4[$arr_column_map[$i]], $ws_colmerge_line4[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}*/
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line4, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line4, $ws_colmerge_line4, $ws_headalign_line1);

	} // function		

	function count_person($gender, $budget_year, $level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $select_org_structure;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		

		$search_condition = str_replace(" where ", " and ", $search_condition);

		if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")	$position_join = "a.POS_ID=d.PAY_ID";
		else $position_join = "a.POS_ID=d.POS_ID";
		if ($gender==1) {
			if($DPISDB=="odbc"){
				$cmd = " select			count(d.PER_ID) as count_person
								from				(
															(
																PER_POSITION a
																left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
															) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
														) left join PER_PERSONAL d on ($position_join)
								 where			a.LEVEL_NO='$level_no' and a.POS_STATUS=1
														$search_condition
								 group by		d.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(d.PER_ID) as count_person
								 from				PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d
								 where			a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and $position_join and d.PER_STATUS=1 and d.PER_TYPE=1
									 					and a.LEVEL_NO='$level_no' and a.POS_STATUS=1 
														$search_condition
								 group by		d.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(d.PER_ID) as count_person
								from				(
															(
																PER_POSITION a
																left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
															) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
														) left join PER_PERSONAL d on ($position_join)
								 where			a.LEVEL_NO='$level_no' and a.POS_STATUS=1 
														$search_condition
								 group by		d.PER_ID ";
			} // end if
		} else {
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.POS_ID) as count_person
								from				(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
								 where			a.LEVEL_NO='$level_no' and a.POS_STATUS=1 
														$search_condition
								 group by		a.POS_ID ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.POS_ID) as count_person
								 from				PER_POSITION a, PER_ORG b, PER_ORG c
								 where			a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+)
									 					and a.LEVEL_NO='$level_no' and a.POS_STATUS=1 
														$search_condition
								 group by		a.POS_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.POS_ID) as count_person
								from				(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
								 where			a.LEVEL_NO='$level_no' and a.POS_STATUS=1 
														$search_condition
								 group by		a.POS_ID ";
			} // end if
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("a.ORG_ID", "d.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($ORG_ID && $ORG_ID!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = $ORG_ID)";
					}else{ 
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = 0 or d.ORG_ID is null)";
					}
				break;
				case "LINE" :
						$arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
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
				case "LINE" :
					$PL_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	function clear_display_order($req_index){
		global $arr_rpt_order, $display_order_number;
		
		$current_index = $req_index + 1;
		for($i=$current_index; $i<count($arr_rpt_order); $i++){
			$display_order_number[$current_index] = 0;
		} // loop for
	} // function

	function display_order($req_index){
		global $display_order_number;
		
		$return_display_order = "";
		$current_index = $req_index;
		while($current_index >= 0){
			if($current_index == $req_index){
				$return_display_order = $display_order_number[$current_index];
			}else{
				$return_display_order = $display_order_number[$current_index].".".$return_display_order;
			} // if else
			$current_index--;
		} // loop while
		
		return $return_display_order;
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_POSITION a
												left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_POSITION a, PER_ORG b, PER_LINE e, PER_ORG c
						 where			a.ORG_ID=b.ORG_ID(+) and a.PL_CODE=e.PL_CODE(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_STATUS=1
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_POSITION a
												left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
						 $search_condition
						 order by		$order_by ";
	} // end if
 	if($select_org_structure==1) { 
		$cmd = str_replace("a.ORG_ID", "d.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
		$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
		for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
			$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
			$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] = 0;
			$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] = 0;
		} // loop for
	} // loop for
	initialize_parameter(0);
	unset($display_order_number);

	$arr_content = (array) null;

	$last_ord_idx = count($arr_rpt_order)-1;
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
//			echo "REPORT_ORDER=$REPORT_ORDER, arr_rpt_order=".implode("|",$arr_rpt_order)."<br>";
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						$data_ln = (array) null;
						$col_i = 2;
						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								$data_ln[] = $arr_content[$data_count][("count_m_".$LEVEL_NO)]."|".$arr_content[$data_count][("count_f_".$LEVEL_NO)];

								$col_i++;
								if($rpt_order_index == $last_ord_idx){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						$data_ln = (array) null;
						$col_i = 2;
						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								$data_ln[] = $arr_content[$data_count][("count_m_".$LEVEL_NO)]."|".$arr_content[$data_count][("count_f_".$LEVEL_NO)];
								
								$col_i++;
								if($rpt_order_index == $last_ord_idx){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
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
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						$data_ln = (array) null;
						$col_i = 2;
						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								$data_ln[] = $arr_content[$data_count][("count_m_".$LEVEL_NO)]."|".$arr_content[$data_count][("count_f_".$LEVEL_NO)];
								
								$col_i++;
								if($rpt_order_index == $last_ord_idx){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE == ""){
							$PL_NAME = "[ไม่ระบุ$PL_TITLE]";
						}else{
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_NAME]);
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;

						$data_ln = (array) null;
						$col_i = 2;
						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								if ($arr_column_sel[$col_i]==1)
									$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								$data_ln[] = $arr_content[$data_count][("count_m_".$LEVEL_NO)]."|".$arr_content[$data_count][("count_f_".$LEVEL_NO)];

								$col_i++;
//								echo "LINE ($data_count)($LEVEL_NO)-cnt_M=".$arr_content[$data_count]["count_m_".$LEVEL_NO]." || cnt_F=".$arr_content[$data_count]["count_f_".$LEVEL_NO]."<br>";
								if($rpt_order_index == $last_ord_idx){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
//									echo " sum M=".$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO]." F=".$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO]."<br>";
								} // end if
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$GRAND_TOTAL_M = array_sum($LEVEL_GRAND_TOTAL["M"]);
	$GRAND_TOTAL_F = array_sum($LEVEL_GRAND_TOTAL["F"]);
	$GRAND_TOTAL = $GRAND_TOTAL_M + $GRAND_TOTAL_F;
	
	set_cell_width();
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
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
			$wsdata_fontfmt_1[0] = "B";
			$wsdata_fontfmt_1[1] = "B";
			$wsdata_fontfmt_2[0] = "";
			$wsdata_fontfmt_2[1] = "";
			$wsdata_align_1[0] = "C";
			$wsdata_align_1[1] = "L";
			$wsdata_border_1[0] = "TLBR";
			$wsdata_border_1[1] = "TLBR";
			$wsdata_colmerge_1[0] = 0;
			$wsdata_colmerge_1[1] = 0;
			$cnt_level_grp = count($ARR_LEVEL_GROUP);
			$cnt_level_det = 0;
			for($i=0; $i<$cnt_level_grp; $i++){
				$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
				for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
					$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
					$wsdata_fontfmt_1[$cnt_level_det*3+2] = "B";
					$wsdata_fontfmt_1[$cnt_level_det*3+3] = "B";
					$wsdata_fontfmt_1[$cnt_level_det*3+4] = "B";
					$wsdata_fontfmt_2[$cnt_level_det*3+2] = "";
					$wsdata_fontfmt_2[$cnt_level_det*3+3] = "";
					$wsdata_fontfmt_2[$cnt_level_det*3+4] = "";
					$wsdata_align_1[$cnt_level_det*3+2] = "R";
					$wsdata_align_1[$cnt_level_det*3+3] = "R";
					$wsdata_align_1[$cnt_level_det*3+4] = "R";
					$wsdata_border_1[$cnt_level_det*3+2] = "TLBR";
					$wsdata_border_1[$cnt_level_det*3+3] = "TLBR";
					$wsdata_border_1[$cnt_level_det*3+4] = "TLBR";
					$wsdata_colmerge_1[$cnt_level_det*3+2] = 0;
					$wsdata_colmerge_1[$cnt_level_det*3+3] = 0;
					$wsdata_colmerge_1[$cnt_level_det*3+4] = 0;
					$cnt_level_det++;
				}
			}
			$wsdata_fontfmt_1[$cnt_level_det*3+2] = "B";
			$wsdata_fontfmt_1[$cnt_level_det*3+3] = "B";
			$wsdata_fontfmt_1[$cnt_level_det*3+4] = "B";
			$wsdata_fontfmt_2[$cnt_level_det*3+2] = "";
			$wsdata_fontfmt_2[$cnt_level_det*3+3] = "";
			$wsdata_fontfmt_2[$cnt_level_det*3+4] = "";
			$wsdata_align_1[$cnt_level_det*3+2] = "R";
			$wsdata_align_1[$cnt_level_det*3+3] = "R";
			$wsdata_align_1[$cnt_level_det*3+4] = "R";
			$wsdata_border_1[$cnt_level_det*3+2] = "TLBR";
			$wsdata_border_1[$cnt_level_det*3+3] = "TLBR";
			$wsdata_border_1[$cnt_level_det*3+4] = "TLBR";
			$wsdata_colmerge_1[$cnt_level_det*3+2] = 0;
			$wsdata_colmerge_1[$cnt_level_det*3+3] = 0;
			$wsdata_colmerge_1[$cnt_level_det*3+4] = 0;
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$DISPLAY_ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
				$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
				for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
					$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
					${"COUNT_M_".$LEVEL_NO} = $arr_content[$data_count][("count_m_".$LEVEL_NO)];
					${"COUNT_F_".$LEVEL_NO} = $arr_content[$data_count][("count_f_".$LEVEL_NO)];
				} // loop for
			} // loop for
			$TOTAL_M = $arr_content[$data_count][total_m];
			$TOTAL_F = $arr_content[$data_count][total_f];

			$arr_data = (array) null;
			$arr_data[] = $DISPLAY_ORDER;
			$arr_data[] = $NAME;
			for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
				$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
				for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
					$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
					$arr_data[] = ${"COUNT_M_".$LEVEL_NO};
					$arr_data[] = ${"COUNT_F_".$LEVEL_NO} - ${"COUNT_M_".$LEVEL_NO};
					$arr_data[] = ${"COUNT_F_".$LEVEL_NO};
				} // loop for
			} // loop for
			$arr_data[] = $TOTAL_M;
			$arr_data[] = $TOTAL_F - $TOTAL_M;
			$arr_data[] = $TOTAL_F;

			$xlsRow++;
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1)
//				$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
//				function wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, $xlsFmt, $arr_fontFmt, $arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map, $arr_border, $arr_merge, $arr_align, $arr_wraptext, $arr_rotate, $arr_color, $arr_bgcolor) {
				$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1);
			else
//				$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
				$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_2, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1);
/*
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));

			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1)
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}*/
		} // end for

		$arr_data = (array) null;
		$arr_data[] = "<**1**>รวมทั้งหมด";	$wsdata_align_1[0]="C";
		$arr_data[] = "<**1**>รวมทั้งหมด";	$wsdata_align_1[1]="C";
		$cnt = 0;
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				$arr_data[] = $LEVEL_GRAND_TOTAL["M"][$LEVEL_NO];
				$cnt++;
				$arr_data[] = $LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] - $LEVEL_GRAND_TOTAL["M"][$LEVEL_NO];
				$cnt++;
				$arr_data[] = $LEVEL_GRAND_TOTAL["F"][$LEVEL_NO];
				$cnt++;
			} // loop for
		} // loop for
		$arr_data[] = $GRAND_TOTAL_M;
		$arr_data[] = $GRAND_TOTAL_F;

		// ทำการ merge cell
/*		$colseq=0;
		$pgrp="";
		$arr_data1 = (array) null;
		$arr_border1 = (array) null;
		$arr_colmerge1 = (array) null;
		for($i=0; $i < count($ws_width); $i++) {
			$buff = explode("|",doo_merge_cell($arr_data[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
			$arr_data1[] = $buff[0]; $arr_border1[] = $buff[1]; $arr_colmerge1[] = $buff[2]; $pgrp = $buff[3];
			$colseq++;
		}
//		echo "new data=".implode("|",$arr_data1)."<br>";
		// จบทำ merge cell
*/
//		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
//		echo "aggreg data=".implode("|",$arr_aggreg_data).", colmerge1:".implode("|",$arr_colmerge1).", border1:".implode("|",$arr_border1).", align1:".implode("|",$wsdata_align_1)."<br>";

//		echo "total =".implode("|",$arr_data)."<br>";
		$xlsRow++;
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1);
/*		
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
				else $ndata = $arr_data1[$arr_column_map[$i]];
				$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata1 = $buff[0]; $border1 = $buff[1]; $colmerge1 = $buff[2]; $pgrp = $buff[3];
//				$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
				$worksheet->write($xlsRow, $colseq, $ndata1, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border1, $colmerge1));
//				echo "$i:".$arr_column_map[$i]." - colseq=$colseq, data=$ndata($ndata1), align=".$wsdata_align_1[$arr_column_map[$i]].", border=".$border1.", merge=".$colmerge1."<br>";
				$colseq++;
			}
		}*/
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "", "", "1"));
		for($j=1; $j<=(($TOTAL_LEVEL * 3) + 3); $j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "", "", "1"));
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