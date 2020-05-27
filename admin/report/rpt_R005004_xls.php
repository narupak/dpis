<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R005004_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=i.PL_CODE";
		$pl_code = "b.PL_CODE";
		$pl_name = "i.PL_NAME";
		$type_code ="a.PT_CODE";
		$select_type_code =",a.PT_CODE";
		$mgt_code ="a.PM_CODE";
		$select_mgt_code =",a.PM_CODE";
		$type_code_b ="b.PT_CODE";
		$select_type_code_b =",b.PT_CODE";
		$mgt_code_b ="b.PM_CODE";
		$select_mgt_code_b =",b.PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=i.PN_CODE";
		$pl_code = "b.PN_CODE";
		$pl_name = "i.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=i.EP_CODE";
		$pl_code = "b.EP_CODE";
		$pl_name = "i.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=i.TP_CODE";
		$pl_code = "b.TP_CODE";
		$pl_name = "i.TP_NAME";
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 
	$search_per_type = 1;

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
//	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(f.DE_YEAR) = '$search_year')";
	if(trim($search_dc_type==1)){ 
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "ชั้นสายสะพาย";
	}elseif(trim($search_dc_type==2)){
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "ชั้นต่ำกว่าสายสะพาย";
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
	if($select_org_structure==0) {
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			 $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}else if($select_org_structure==1) {
		if(trim($search_org_ass_id)){ 
			 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
			$list_type_text .= "$search_org_ass_name";
		} // end if
		if(trim($search_org_ass_id_1)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
			$list_type_text .= " - $search_org_ass_name_1";
		} // end if
		if(trim($search_org_ass_id_2)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
			$list_type_text .= " - $search_org_ass_name_2";
		} // end if
	}
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "แบบ ขร 4/" . substr($search_year, -2);
	$report_title = "บัญชีแสดงคุณสมบัติของข้าราชการ ซึ่งขอพระราชทานเครื่องราชอิสริยาภรณ์ $search_dc_name ปี พ.ศ.$search_year||$MINISTRY_NAME||$DEPARTMENT_NAME" . ($search_org_id?"||$search_org_name":"");
	$report_code = "R0504";

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
		$ws_head_line1 = array("(1)","(2)","<**1**>(3)","<**1**>(3)","<**1**>(3)","(4)","<**2**>(5)","<**2**>(5)","<**2**>(5)","(6)");
		$ws_head_line2 = array("","","<**1**>เป็นข้าราชการ","<**1**>เป็นข้าราชการ","<**1**>เป็นข้าราชการ","ตำแหน่ง","<**2**>เครื่องราชอิสริยาภรณ์","<**2**>เครื่องราชอิสริยาภรณ์","<**2**>เครื่องราชอิสริยาภรณ์","");
		$ws_head_line3 = array("ลำดับ","ชื่อตัว - ชื่อสกุล","ชั้น","ตั้งแต่","เงินเดือน","ปัจจุบัน และอดีตเฉพาะปีที่ได้รับ","ได้รับ","วัน เดือน ปี","ขอครั้งนี้","หมายเหตุ");
		$ws_head_line4 = array("","","(ปัจจุบัน)","วัน เดือน ปี","(ปัจจุบัน)","พระราชทานเครื่องราชอิสริยาภรณ์","(จากชั้นสูง","(5 ธ.ค. ...)"," "," ");
		$ws_head_line5 = array("","","","","","","ไปชั้นรอง)","","","");
		$ws_colmerge_line1 = array(0,0,1,1,1,0,1,1,1,0);
		$ws_colmerge_line2 = array(0,0,1,1,1,0,1,1,1,0);
		$ws_colmerge_line3 = array(0,0,1,1,1,0,1,1,1,0);
		$ws_colmerge_line4 = array(0,0,1,1,1,0,1,1,1,0);
		$ws_colmerge_line5 = array(0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TL","T","TR","TLR","TL","T","TR","TLR");
		$ws_border_line2 = array("LR","LR","LB","B","B","LR","B","B","BR","LR");
		$ws_border_line3 = array("LR","LR","TLR","TLR","TLR","LR","TLR","TLR","TLR","LR");
		$ws_border_line4 = array("LR","LR","LR","LR","LR","LR","LR","LR","LR","LR");
		$ws_border_line5 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(6,35,12,15,10,25,10,15,10,35);
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
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_head_line4, $ws_head_line5;
		global $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_colmerge_line4, $ws_colmerge_line5;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_border_line4, $ws_border_line5, $ws_fontfmt_line1, $ws_fontfmt_line2;
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
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line3[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 4
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line4[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line4[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 5
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line5[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line5[$arr_column_map[$i]], $ws_colmerge_line5[$arr_column_map[$i]]));
				$colseq++;
			}
		} 
/*		
		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 8);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 8);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 10);
		$worksheet->set_column(9, 9, 25);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "(1)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "(2)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "(3)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 5, "(4)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 6, "(5)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 9, "(6)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "เป็นข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 5, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "เครื่องราชอิสริยาภรณ์", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 9, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อตัว - ชื่อสกุล", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "ชั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ตั้งแต่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ปัจจุบัน และอดีตเฉพาะปีที่ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ขอครั้งนี้", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "(ปัจจุบัน)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "(ปัจจุบัน)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "พระราชทานเครื่องราชอิสริยาภรณ์", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "(จากชั้นสูง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, "(5 ธ.ค. ...)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "ไปชั้นรอง)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
*/
	} // function		

	function list_decoratehis($PER_ID, $LEVEL_NO){
		global $DPISDB, $db_dpis2, $db_dpis3;
		global $search_per_type, $arr_content, $data_count, $DATE_DISPLAY;
		global $pl_code,$pl_name,$line_table,$line_join;
		
		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											inner join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, SUBSTR(trim(a.DEH_DATE), 1, 10) as DEH_DATE
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
							 order by SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											inner join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		while($data2 = $db_dpis2->get_array()){
			$GET_DC_NAME = trim($data2[DC_SHORTNAME]);
			$GET_DEH_DATE = trim($data2[DEH_DATE]);
			if($GET_DEH_DATE){
				$SHOW_DEH_DATE = show_date_format($GET_DEH_DATE,$DATE_DISPLAY);
			} // end if

			if($DPISDB=="odbc"){
				$cmd = " select			$pl_code as PL_CODE, $pl_name as PL_CODE, a.LEVEL_NO, e.POSITION_LEVEL $select_type_code $select_mgt_code
								 from		(
													PER_POSITIONHIS a
													left join $line_table b on ($line_join)
												) left join PER_LEVEL e on (a.LEVEL_NO=e.LEVEL_NO)
								 where		a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$GET_DEH_DATE'
								 order by		LEFT(trim(a.POH_EFFECTIVEDATE), 10) desc ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			$pl_code as PL_CODE, $pl_name as PL_CODE, a.LEVEL_NO, e.POSITION_LEVEL $select_type_code $select_mgt_code
								 from			PER_POSITIONHIS a, $line_table b, PER_LEVEL e
								 where		$line_join(+)
													and a.PER_ID=$PER_ID and SUBSTR(trim(a.POH_EFFECTIVEDATE), 1, 10) < '$GET_DEH_DATE' and a.LEVEL_NO=e.LEVEL_NO(+)
								 order by		SUBSTR(trim(a.POH_EFFECTIVEDATE), 1, 10) desc ";
			}elseif($DPISDB=="mysql"){
			$cmd = " select			$pl_code as PL_CODE, $pl_name as PL_CODE, a.LEVEL_NO, e.POSITION_LEVEL $select_type_code $select_mgt_code
								 from		(
													PER_POSITIONHIS a
													left join $line_table b on ($line_join)
												) left join PER_LEVEL e on (a.LEVEL_NO=e.LEVEL_NO)
								 where		a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$GET_DEH_DATE'
								 order by		LEFT(trim(a.POH_EFFECTIVEDATE), 10) desc ";
			}
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data3 = $db_dpis3->get_array();
			$PL_CODE = trim($data3[PL_CODE]);
			$PL_NAME = trim($data3[PL_NAME]);
			$LEVEL_NO = trim($data3[LEVEL_NO]);
			$POSITION_LEVEL = trim($data3[POSITION_LEVEL]);
			$PM_CODE = trim($data3[PM_CODE]);	$PT_CODE = trim($data3[PT_CODE]);
		
			if(trim($PM_CODE)){
				$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PM_NAME = trim($data3[PM_NAME]);
			}
			if(trim($PT_CODE)){
				$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PT_NAME = trim($data3[PT_NAME]);
			}

			if($DPISDB=="odbc"){
				$cmd = " select		MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
								 from		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO) = '$LEVEL_NO' ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select		MIN(SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO) = '$LEVEL_NO' ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
								 from		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO) = '$LEVEL_NO' ";
			} // end if
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data3 = $db_dpis3->get_array();
			$POH_EFFECTIVEDATE =  show_date_format($data3[POH_EFFECTIVEDATE],$DATE_DISPLAY);
			$REMARK = "ระดับ ". $POSITION_LEVEL ." เมื่อ $POH_EFFECTIVEDATE";

			$data_count++;			
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "";
			$arr_content[$data_count][name] = "";
			$arr_content[$data_count][level] = "";
			$arr_content[$data_count][effectivedate] = "";
			$arr_content[$data_count][salary] = "";
			$arr_content[$data_count][last_dc_name] = $GET_DC_NAME;
			$arr_content[$data_count][last_deh_date] = $SHOW_DEH_DATE;
			$arr_content[$data_count][dc_name] = "";
			$arr_content[$data_count][remark] = $REMARK;

			if($PM_CODE){
				$arr_content[$data_count][position] = $PM_NAME;
				
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][order] = "";
				$arr_content[$data_count][name] = "";
				$arr_content[$data_count][level] = "";
				$arr_content[$data_count][effectivedate] = "";
				$arr_content[$data_count][salary] = "";
				$arr_content[$data_count][last_dc_name] = "";
				$arr_content[$data_count][last_deh_date] = "";
				$arr_content[$data_count][dc_name] = "";
				$arr_content[$data_count][remark] = "";
				$arr_content[$data_count][position] = "(". $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") .")";
			}else{
				$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
			} // end if
		} // end while		
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
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code as PL_CODE, $pl_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE $select_type_code_b $select_mgt_code_b
						 from			(
												(
													(
														(
															(
																(
																	(
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
														) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
													) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												) left join PER_POSITIONHIS h on (a.PER_ID=h.PER_ID and a.LEVEL_NO=h.LEVEL_NO)
											) left join $line_table i on ($line_join)
										) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
											$search_condition
						  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code, $pl_name, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME $select_type_code_b $select_mgt_code_b
						 order by		a.LEVEL_NO desc, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code as PL_CODE, $pl_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME, MIN(SUBSTR(trim(h.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE $select_type_code_b $select_mgt_code_b
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, 
											PER_DECORDTL e, PER_DECOR f, PER_DECORATION g, PER_POSITIONHIS h, $line_table i, PER_LEVEL m
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
											and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
											and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=h.LEVEL_NO(+) and $line_join(+) and a.LEVEL_NO=m.LEVEL_NO(+)
											$search_condition
						 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code, $pl_name, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME $select_type_code_b $select_mgt_code_b
						 order by		a.LEVEL_NO desc, MIN(SUBSTR(trim(h.POH_EFFECTIVEDATE), 1, 10)) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code as PL_CODE, $pl_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE $select_type_code_b $select_mgt_code_b
						 from			(
												(
													(
														(
															(
																(
																	(
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
														) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
													) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												) left join PER_POSITIONHIS h on (a.PER_ID=h.PER_ID and a.LEVEL_NO=h.LEVEL_NO)
											) left join $line_table i on ($line_join)
										) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
											$search_condition
						  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code, $pl_name, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME $select_type_code_b $select_mgt_code_b
						 order by		a.LEVEL_NO desc, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = trim($data[PL_NAME]);
		$LEVEL_NO = $data[LEVEL_NO];
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		$PER_SALARY = $data[PER_SALARY];
		$DC_CODE = trim($data[DC_CODE]);
		$DC_NAME = trim($data[DC_SHORTNAME]);
		if(trim($type_code_b)){
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);
		}
		if(trim($mgt_code_b)){
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
		}
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE], $DATE_DISPLAY);
		$REMARK = "ระดับ ". $POSITION_LEVEL ." เมื่อ $POH_EFFECTIVEDATE";

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][level] = $POSITION_LEVEL;
		$arr_content[$data_count][effectivedate] = "$POH_EFFECTIVEDATE";
		$arr_content[$data_count][salary] = "$PER_SALARY";
		$arr_content[$data_count][last_dc_name] = "-";
		$arr_content[$data_count][last_deh_date] = "";
		$arr_content[$data_count][dc_name] = "$DC_NAME";
		$arr_content[$data_count][remark] = "$REMARK";
		if($PM_CODE){
			$arr_content[$data_count][position] = $PM_NAME;
			
			$data_count++;
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "";
			$arr_content[$data_count][name] = "";
			$arr_content[$data_count][level] = "";
			$arr_content[$data_count][effectivedate] = "";
			$arr_content[$data_count][salary] = "";
			$arr_content[$data_count][last_dc_name] = "";
			$arr_content[$data_count][last_deh_date] = "";
			$arr_content[$data_count][dc_name] = "";
			$arr_content[$data_count][remark] = "";
			$arr_content[$data_count][position] = "(". $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") .")";
		}else{
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		} // end if
		
		list_decoratehis($PER_ID, $LEVEL_NO);
		
		$data_count++;
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C", "L", "L", "C", "R", "L", "C", "C", "C", "L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$LEVEL_NO = $arr_content[$data_count][level];
			$EFFECTIVEDATE = $arr_content[$data_count][effectivedate];
			$SALARY = $arr_content[$data_count][salary];
			$LAST_DC_NAME = $arr_content[$data_count][last_dc_name];
			$LAST_DEH_DATE = $arr_content[$data_count][last_deh_date];
			$DC_NAME = $arr_content[$data_count][dc_name];
			$REMARK = $arr_content[$data_count][remark];
			$POSITION = $arr_content[$data_count][position];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME;
			$arr_data[] = level_no_format($LEVEL_NO);
			$arr_data[] = $EFFECTIVEDATE;
			$arr_data[] = $SALARY;
			$arr_data[] = $POSITION;
			$arr_data[] = $LAST_DC_NAME;
			$arr_data[] = $LAST_DEH_DATE;
			$arr_data[] = $DC_NAME;
			$arr_data[] = $REMARK;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
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