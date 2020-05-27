<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004007_survey_xls_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$order_by = "b.PL_CODE, a.PER_NAME, a.PER_SURNAME";

	$search_condition = "";
	if ($SURVEY_NO==3)
		$arr_search_condition[] = "(a.LEVEL_NO like 'K%' or a.LEVEL_NO like 'D%' or a.LEVEL_NO like 'M%')";
	elseif ($SURVEY_NO==4)
		$arr_search_condition[] = "(a.LEVEL_NO like 'O%')";
		
	$list_type_text = $ALL_REPORT_TITLE;
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(d.OT_CODE='01' or e.OT_CODE='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(d.OT_CODE='02' or e.OT_CODE='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(d.OT_CODE='03' or e.OT_CODE='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(d.OT_CODE='04' or e.OT_CODE='04')";
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(d.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	if ($SURVEY_NO==3)
		$report_title = "$DEPARTMENT_NAME||การสำรวจจำนวนตำแหน่งในปัจจุบันของ ตำแหน่งประเภทวิชาการ ที่เคยดำรงตำแหน่งในระดับ 7 มาก่อนวันที่ 11 ธันวาคม 2551";
	elseif ($SURVEY_NO==4)
		$report_title = "$DEPARTMENT_NAME||การสำรวจจำนวนตำแหน่งในปัจจุบันของ ตำแหน่งประเภททั่วไป ที่เคยดำรงตำแหน่งในระดับ 7 มาก่อนวันที่ 11 ธันวาคม 2551";
	$report_code = "R0407";

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
	$ws_head_line5 = (array) null;
	$ws_head_line6 = (array) null;
	$ws_head_line7 = (array) null;
	$ws_colmerge_line1 = (array) null;
	$ws_colmerge_line2 = (array) null;
	$ws_colmerge_line3 = (array) null;
	$ws_colmerge_line4 = (array) null;
	$ws_colmerge_line5 = (array) null;
	$ws_colmerge_line6 = (array) null;
	$ws_colmerge_line7 = (array) null;
	$ws_border_line1 = (array) null;
	$ws_border_line2 = (array) null;
	$ws_border_line3 = (array) null;
	$ws_border_line4 = (array) null;
	$ws_border_line5 = (array) null;
	$ws_border_line6 = (array) null;
	$ws_border_line7 = (array) null;
	$ws_fontfmt_line1 = (array) null;
	$ws_headalign_line1 = (array) null;
	for($i=0; $i < count($heading_text); $i++) {
		$buff = explode("|",$heading_text[$i]);
		$ws_head_line1[] = $buff[0];
		$ws_head_line2[] = $buff[1];
		$ws_head_line3[] = $buff[2];
		$ws_head_line4[] = $buff[3];
		$ws_head_line5[] = $buff[4];
		$ws_head_line6[] = $buff[5];
		$ws_head_line7[] = $buff[6];
		$ws_fontfmt_line1[] = "B";
		$ws_headalign_line1[] = "C";
	}
	if ($SURVEY_NO==3) {
		$ws_width = array(25,25,8,25,8,5,10,5,10,10,12,12,8,8);
		$ws_colmerge_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line3 = array(0,0,0,1,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line4 = array(0,0,0,0,0,1,1,1,0,0,0,0,1,1);
		$ws_colmerge_line5 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line6 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line7 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TL","T","TR","TL","T","T","T","T","T","T","T","T","T","TR");
		$ws_border_line2 = array("L","","R","L","","","","","","","","","","R");
		$ws_border_line3 = array("TLR","TLR","TLR","TL","T","T","T","T","T","T","T","T","T","TR");
		$ws_border_line4 = array("LR","LR","LR","TLR","TLR","TL","T","TR","TL","TL","TL","TL","TL","TR");
		$ws_border_line5 = array("LR","LR","LR","LR","LR","TLR","TLR","TLR","LR","LR","LR","LR","TLR","TLR");
		$ws_border_line6 = array("LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR");
		$ws_border_line7 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
	} elseif ($SURVEY_NO==4) {
		$ws_width = array(25,25,8,25,8,5,10,10,10,12,12,8,8);
		$ws_colmerge_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line3 = array(0,0,0,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line4 = array(0,0,0,0,0,1,1,1,0,0,0,0,1);
		$ws_colmerge_line5 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line6 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line7 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TL","T","TR","TL","T","T","T","T","T","T","T","T","TR");
		$ws_border_line2 = array("L","","R","L","","","","","","","","","R");
		$ws_border_line3 = array("TLR","TLR","TLR","TL","T","T","T","T","T","T","T","T","TR");
		$ws_border_line4 = array("LR","LR","LR","TLR","TLR","TL","TR","TL","TL","TL","TL","TL","TR");
		$ws_border_line5 = array("LR","LR","LR","LR","LR","TLR","TLR","LR","LR","LR","LR","TLR","TLR");
		$ws_border_line6 = array("LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR");
		$ws_border_line7 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
	}
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
		global $heading_name, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2, $SESS_DEPARTMENT_NAME;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_head_line4, $ws_head_line5, $ws_head_line6, $ws_head_line7;
		global $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_colmerge_line4, $ws_colmerge_line5, $ws_colmerge_line6, $ws_colmerge_line7;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_border_line4, $ws_border_line5, $ws_border_line6, $ws_border_line7;
		global $ws_fontfmt_line1;
		global $ws_headalign_line1, $ws_width;

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
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line3[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 4
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line4[$arr_column_map[$i]], $ws_border_line4[$arr_column_map[$i]], $ws_colmerge_line4[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 5
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line5[$arr_column_map[$i]], $ws_border_line5[$arr_column_map[$i]], $ws_colmerge_line5[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 6
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line6[$arr_column_map[$i]], $ws_border_line6[$arr_column_map[$i]], $ws_colmerge_line6[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 7
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line7[$arr_column_map[$i]], $ws_border_line7[$arr_column_map[$i]], $ws_colmerge_line7[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select	a.PER_ID, a.POS_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PL_CODE, a.PER_BIRTHDATE, 
										PER_STATUS, max(b.POH_EFFECTIVEDATE)	as POH_EFFECTIVEDATE 									
						 from		PER_PERSONAL a 
										left join PER_POSITIONHIS b on (a.PER_ID=b.PER_ID) 
						 where	a.PER_TYPE= 1 and b.LEVEL_NO = '07' and b.POH_EFFECTIVEDATE < '2008-12-11' and a.PER_ID not in
														(select c.PER_ID from PER_POSITIONHIS c where a.PER_ID=c.PER_ID and c.LEVEL_NO > '07' and c.POH_EFFECTIVEDATE < '2008-12-11')
										$search_condition
						 group by   a.PER_ID, a.POS_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PL_CODE, a.PER_BIRTHDATE, PER_STATUS 
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select	a.PER_ID, a.POS_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PL_CODE, a.PER_BIRTHDATE, 
										PER_STATUS, max(b.POH_EFFECTIVEDATE)	as POH_EFFECTIVEDATE
						 from		PER_PERSONAL a, PER_POSITIONHIS b
						 where	a.PER_ID=b.PER_ID and a.PER_TYPE= 1 and b.LEVEL_NO = '07' and b.POH_EFFECTIVEDATE < '2008-12-11' and a.PER_ID not in
														(select c.PER_ID from PER_POSITIONHIS c where a.PER_ID=c.PER_ID and c.LEVEL_NO > '07' and c.POH_EFFECTIVEDATE < '2008-12-11')
										$search_condition
						 group by   a.PER_ID, a.POS_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PL_CODE, a.PER_BIRTHDATE, PER_STATUS 
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select	a.PER_ID, a.POS_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PL_CODE, a.PER_BIRTHDATE, PER_STATUS, 
										max(b.POH_EFFECTIVEDATE)	as POH_EFFECTIVEDATE										
						 from		PER_PERSONAL a 
										left join PER_POSITIONHIS b on (a.PER_ID=b.PER_ID) 
						 where	a.PER_TYPE= 1 and b.LEVEL_NO = '07' and b.POH_EFFECTIVEDATE < '2008-12-11' and a.PER_ID not in
														(select c.PER_ID from PER_POSITIONHIS c where a.PER_ID=c.PER_ID and c.LEVEL_NO > '07' and c.POH_EFFECTIVEDATE < '2008-12-11')
										$search_condition
						 group by   a.PER_ID, a.POS_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PL_CODE, a.PER_BIRTHDATE, PER_STATUS 
						 order by		$order_by ";
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;

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
		$wsdata_fontfmt_1 = (array) null;
		$wsdata_align_1 = (array) null;
		$wsdata_border_1 = (array) null;
		$wsdata_colmerge_1 = (array) null;
		$wsdata_fontfmt_2 = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$wsdata_fontfmt_1[] = "B";
			if ($i==0 || $i==1 || $i==3)
				$wsdata_align_1[] = "L";
			else
				$wsdata_align_1[] = "C";
			$wsdata_border_1[] = "TLRB";
			$wsdata_colmerge_1[] = 0;
			$wsdata_fontfmt_2[] = "";
		}
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$O3_FLAG = $K3_FLAG = $K2_FLAG = $OT_FLAG1 = $OT_FLAG2 = $PV_NAME = $CT_NAME = $OT_NAME3 = $OT_NAME4 = $RETIRE_YEAR = $RETIRE_FLAG = ""; 
			$PER_ID = $data[PER_ID];
			$POS_ID = $data[POS_ID];
			$LEVEL_NO = trim($data[LEVEL_NO]);
			if ($LEVEL_NO=="K2") $K2_FLAG = "X";
			if ($LEVEL_NO=="K3") $K3_FLAG = "X";
			if ($LEVEL_NO=="O3") $O3_FLAG = "X";
			$PER_STATUS = trim($data[PER_STATUS]);
			if ($PER_STATUS==2) $RETIRE_FLAG = "X"; 
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE && $PER_STATUS==1){
				$arr_temp = explode("-", $PER_BIRTHDATE);
				$RETIRE_YEAR = ($arr_temp[0] + 543) + (($arr_temp[1] >= 10)?61:60);
			}
			$POS_NO = $data[POS_NO_NAME].' '.$data[POS_NO];
			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$PL_CODE = trim($data[PL_CODE]);
			
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);

			$cmd = " select POH_POS_NO from PER_POSITIONHIS 
							where PER_ID = $PER_ID and POH_EFFECTIVEDATE='$POH_EFFECTIVEDATE' and trim(PL_CODE) = '$PL_CODE' and LEVEL_NO = '07' ";
			$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = trim($data2[POH_POS_NO]);

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE) = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";

			$cmd = "	select		a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, c.OT_CODE, c.CT_CODE, c.PV_CODE, c.AP_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);
			$NEW_OT_CODE = trim($data2[OT_CODE]);
			$NEW_CT_CODE = trim($data2[CT_CODE]);
			$NEW_PV_CODE = trim($data2[PV_CODE]);
			$NEW_AP_CODE = trim($data2[AP_CODE]);

			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$NEW_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);
			
			if (!$NEW_PL_NAME) {
				$cmd = " select	POH_POS_NO, POH_PL_NAME, POH_PM_NAME, b.OT_CODE, b.CT_CODE, b.PV_CODE, b.AP_CODE
								from		PER_POSITIONHIS a, PER_ORG b
								where	a.PER_ID=$PER_ID and a.ORG_ID_3=b.ORG_ID 
								order by POH_EFFECTIVEDATE desc ";
				$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$NEW_POS_NO = trim($data2[POH_POS_NO]);
				$NEW_PL_NAME = trim($data2[POH_PL_NAME]); 
				$NEW_PM_NAME = trim($data2[POH_PM_NAME]); 
				$NEW_CT_CODE = trim($data2[CT_CODE]); 
				$NEW_OT_CODE = trim($data2[OT_CODE]); 
				$NEW_PV_CODE = trim($data2[PV_CODE]); 
				$NEW_AP_CODE = trim($data2[AP_CODE]); 
			}

			$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$NEW_AP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$AP_NAME = trim($data2[AP_NAME]);
			
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$NEW_PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_NAME = trim($data2[PV_NAME]);
					
			$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$NEW_CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME = trim($data2[CT_NAME]);
			if ($AP_NAME) $CT_NAME .= "/".$AP_NAME;
					
			if ($NEW_PL_NAME==$NEW_PM_NAME) $NEW_PM_NAME = "";
			$NEW_PL_NAME = (trim($NEW_PM_NAME)?"$NEW_PM_NAME (":"") . (trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO) . (($NEW_PT_NAME != "ทั่วไป" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"") . (trim($NEW_PM_NAME)?")":"");
			if ($NEW_OT_CODE=="01") $OT_FLAG1 = "X";
			elseif ($NEW_OT_CODE=="02") $OT_FLAG2 = "X";
			elseif ($NEW_OT_CODE=="03") $OT_NAME3 = $PV_NAME;
			elseif ($NEW_OT_CODE=="04") $OT_NAME4 = $CT_NAME;

			if ($SURVEY_NO==3)
                            /*เดิม*/
				//$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='K3' ";
                            
                            /*Release 5.2.1.16
                         * http://dpis.ocsc.go.th/Service/node/1700
                         */
                            $cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE 
                                from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='K3' 
                                    and trim(mov_code) not IN(SELECT trim(mov_code) FROM per_movment WHERE mov_sub_type IN(7)) ";
			elseif ($SURVEY_NO==4)
                            /*เดิม*/
				//$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='O3' ";
                            /*Release 5.2.1.16
                         * http://dpis.ocsc.go.th/Service/node/1700
                         */
                            $cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE 
                                from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='O3' 
                            and trim(mov_code) not IN(SELECT trim(mov_code) FROM per_movment WHERE mov_sub_type IN(7)) ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);

			$arr_data = (array) null;
			$arr_data[] = $PL_NAME;
			$arr_data[] = $data_count.". ".$FULLNAME; 
			$arr_data[] = $POH_POS_NO;
			$arr_data[] = $NEW_PL_NAME;
			$arr_data[] = $NEW_POS_NO;
			if ($SURVEY_NO==3)	$arr_data[] = $K3_FLAG;
			elseif ($SURVEY_NO==4)	$arr_data[] = $O3_FLAG;
			$arr_data[] = $LEVEL_EFFECTIVEDATE;
			if ($SURVEY_NO==3)	$arr_data[] = $K2_FLAG;
			$arr_data[] = $OT_FLAG1;
			$arr_data[] = $OT_FLAG2;
			$arr_data[] = $OT_NAME3;
			$arr_data[] = $OT_NAME4;
			$arr_data[] = $RETIRE_YEAR;
			$arr_data[] = $RETIRE_FLAG;
	
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
		} // end while
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
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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