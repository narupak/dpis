<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R006015_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "a.PL_CODE";
		$line_join = "a.PL_CODE=e.PL_CODE";
		$line_name = "e.PL_NAME";
		$position_no_name = "a.POS_NO_NAME";
		$position_no = "a.POS_NO";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);
		 $line_title = " สายงาน";
		 $type_code = "a.PT_CODE";
		 $select_type_code = ", a.PT_CODE";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "a.PN_CODE";
		$line_join = "a.PN_CODE=e.PN_CODE";
		$line_name = "e.PN_NAME";
		$position_no_name = "a.POEM_NO_NAME";
		$position_no = "a.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "a.EP_CODE";
		$line_join = "a.EP_CODE=e.EP_CODE";
		$line_name = "e.EP_NAME";
		$position_no_name = "a.POEMS_NO_NAME";
		$position_no = "a.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "a.TP_CODE";
		$line_join = "a.TP_CODE=e.TP_CODE";
		$line_name = "e.TP_NAME";
		$position_no_name = "a.POT_NO_NAME";
		$position_no = "a.POT_NO";
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
		if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
	}

	$search_per_type = 1;
	$search_per_status[] = 1;

	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	
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
	$report_title = "บัญชีถือจ่ายเงินเดือน$PERSON_TYPE[$search_per_type]||$DEPARTMENT_NAME||ประจำปีงบประมาณ ". ($search_budget_year?(($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year):"0");
	$report_code = "R0615";

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
	    $ws_head_line1 = array("เลขประจำตัว","สังกัด/ชื่อ","ตำแหน่ง","เลขที่","<**1**>ถือจ่ายปีที่แล้ว","<**1**>ถือจ่ายปีที่แล้ว","<**2**>ถือจ่ายปีนี้","<**2**>ถือจ่ายปีนี้","<**2**>ถือจ่ายปีนี้","ปรับ","<**3**>จำนวนเงิน","<**3**>จำนวนเงิน","ปรับ","อัตรา");
	    $ws_head_line2 = array("ข้าราชการ","","","ตำแหน่ง","ระดับ","อัตรา","ระดับ","ขั้น","อัตรา","ลด","เลื่อนขั้น","ปรับวุฒิ","อัตรา","ตั้งใหม่");
		$ws_colmerge_line1 = array(0,0,0,0,1,1,1,1,1,0,1,1,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TL","TR","TL","T","TR","TRL","TL","TR","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","TRBL","TRBL","TRBL","TRBL","TRBL","RBL","TRBL","TRBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(12,25,30,7,25,7,25,7,7,8,7,7,7,7);
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
		global $ws_head_line1, $ws_head_line2,  $ws_colmerge_line1, $ws_colmerge_line2;
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
		$xlsRow++;
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
		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 7);
		$worksheet->set_column(4, 4, 7);
		$worksheet->set_column(5, 5, 7);
		$worksheet->set_column(6, 6, 25);
		$worksheet->set_column(7, 7, 7);
		$worksheet->set_column(8, 8, 7);
		$worksheet->set_column(9, 9, 8);
		$worksheet->set_column(10, 10, 7);
		$worksheet->set_column(11, 11, 7);
		$worksheet->set_column(12, 12, 7);
		$worksheet->set_column(13, 13, 7);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "เลขประจำตัว", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "สังกัด/ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ถือจ่ายปีที่แล้ว", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "ถือจ่ายปีนี้", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "ปรับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "จำนวนเงิน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 12, "ปรับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 9, "ลด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "เลื่อนขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 11, "ปรับวุฒิ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "ตั้งใหม่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
*/
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
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
	
	if($DPISDB=="odbc"){
		$cmd = " select			$select_list, b.PER_ID, d.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_OFFNO, b.PER_CARDNO, b.PER_SALARY, $position_no_name as POS_NO_NAME, 
											IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, $line_code as PL_CODE, $line_name as PL_NAME, b.LEVEL_NO $select_type_code
						 from		(
											(
												(
													(
														$position_table a 
														left join PER_PERSONAL b on ($position_join) 
													) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_PRENAME d on (b.PN_CODE=d.PN_CODE)
											) left join $line_table e on ($line_join)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
						 order by		$order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			$select_list, b.PER_ID, d.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_OFFNO, b.PER_CARDNO, b.PER_SALARY, $position_no_name as POS_NO_NAME, 
											to_number(replace($position_no,'-','')) as POS_NO, $line_code as PL_CODE, $line_name as PL_NAME, b.LEVEL_NO $select_type_code
						 from			$position_table a, PER_PERSONAL b, PER_ORG c, PER_PRENAME d, $line_table e, PER_ORG g
						 where		$position_join(+) and a.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
											and b.PN_CODE=d.PN_CODE(+) and $line_join(+)
											$search_condition
						 order by		$order_by, $position_no_name, to_number(replace($position_no,'-','')) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			$select_list, b.PER_ID, d.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_OFFNO, b.PER_CARDNO, b.PER_SALARY, $position_no_name as POS_NO_NAME, 
											IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, $line_code as PL_CODE, $line_name as PL_NAME, b.LEVEL_NO $select_type_code
						 from		(
											(
												(
													(
														$position_table a 
														left join PER_PERSONAL b on ($position_join) 
													) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_PRENAME d on (b.PN_CODE=d.PN_CODE)
											) left join $line_table e on ($line_join)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
						 order by		$order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)) ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}	
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){
						$MINISTRY_ID = trim($data[MINISTRY_ID]);
						if($MINISTRY_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$data_row++;

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][order] = $data_row;
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $MINISTRY_NAME;

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
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$data_row++;

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][order] = $data_row;
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $DEPARTMENT_NAME;

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

						$addition_condition = generate_condition($rpt_order_index);

						$data_row++;

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][order] = $data_row;
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if

					if($rpt_order_index == (count($arr_rpt_order) - 1)){
						$data_row++;
						
						$PER_ID = $data[PER_ID];
						$PN_NAME = trim($data[PN_NAME]);
						$PER_NAME = trim($data[PER_NAME]);
						$PER_SURNAME = trim($data[PER_SURNAME]);
						$PER_OFFNO = trim($data[PER_OFFNO]);
						if (!$PER_OFFNO) $PER_OFFNO = trim($data[PER_CARDNO]);
						$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);
						$LEVEL_NO = $data[LEVEL_NO];
						$PER_SALARY = $data[PER_SALARY];
						$PROMOTE_SALARY = $PER_SALARY - $PREV_PER_SALARY;
						if(trim($type_code)){
							$PT_CODE = trim($data[PT_CODE]);
							$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PT_NAME = trim($data3[PT_NAME]);	
						}
						
						$cmd=" select LAYER_NO from PER_LAYER where LAYER_TYPE=1 and LEVEL_NO='$LEVEL_NO' and LAYER_SALARY=$PER_SALARY ";
						$db_dpis3->send_cmd($cmd);
						$data_name=$db_dpis3->get_array();
						$LAYER_NO=$data_name[LAYER_NO];

						//หาอัตราเงินเดือนถือจ่ายปีที่แล้ว
						if($DPISDB=="odbc"){
							$cmd = "select PER_ID, LEFT(SAH_ENDDATE,10), LEVEL_NO, SAH_SALARY  
											from PER_SALARYHIS  
											where PER_ID=$PER_ID and LEFT(SAH_ENDDATE,10)<='".($search_budget_year-543-1)."-09-30'  
											order by LEFT(SAH_ENDDATE,10) DESC, LEVEL_NO DESC";
						}elseif($DPISDB=="oci8"){
							$cmd = "select PER_ID, SUBSTR(SAH_ENDDATE,1,10), LEVEL_NO, SAH_SALARY  
											from PER_SALARYHIS 
											where PER_ID=$PER_ID and SUBSTR(SAH_ENDDATE,1,10)<='".($search_budget_year-543-1)."-09-30'  
											order by SUBSTR(SAH_ENDDATE,1,10) DESC, LEVEL_NO DESC";
						}elseif($DPISDB=="mysql"){
							$cmd = "select PER_ID, LEFT(SAH_ENDDATE,10), LEVEL_NO, SAH_SALARY  
											from PER_SALARYHIS 
											where PER_ID=$PER_ID and LEFT(SAH_ENDDATE,10)<='".($search_budget_year-543-1)."-09-30'  
											order by LEFT(SAH_ENDDATE,10) DESC, LEVEL_NO DESC";
						}
						//echo "<br>$cmd<br>";
						$db_dpis3->send_cmd($cmd);
						$data_name=$db_dpis3->get_array();
						$PER_SALARY_OLD=$data_name[SAH_SALARY];	
						$LEVEL_NO_OLD=$data_name[LEVEL_NO];

						$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO_OLD'";
						$db_dpis3->send_cmd($cmd);
						$data_name=$db_dpis3->get_array();
						$LEVEL_NAME_OLD=$data_name[LEVEL_NAME];
						$POSITION_LEVEL_OLD = $data_name[POSITION_LEVEL];
						if (!$POSITION_LEVEL_OLD) $POSITION_LEVEL_OLD = $LEVEL_NAME_OLD;
					
						$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
						$db_dpis3->send_cmd($cmd);
						$data_name=$db_dpis3->get_array();
						$LEVEL_NAME=$data_name[LEVEL_NAME];
						$POSITION_LEVEL = $data_name[POSITION_LEVEL];
						if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
	
						$arr_content[$data_count][type] = "DETAIL";
						$arr_content[$data_count][name] = "$PN_NAME" . "$PER_NAME $PER_SURNAME";
						$arr_content[$data_count][per_offno] = $PER_OFFNO;
						$arr_content[$data_count][pos_no] = $POS_NO;
						$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
						$arr_content[$data_count][level_no_old] = $LEVEL_NAME_OLD;
						$arr_content[$data_count][per_salary_old] = $PER_SALARY_OLD;
						$arr_content[$data_count][level_no] = $LEVEL_NAME;
						$arr_content[$data_count][per_salary] = $PER_SALARY;
						$arr_content[$data_count][prev_level_no] = level_no_format($PREV_LEVEL_NO);
						$arr_content[$data_count][prev_per_salary] = $PREV_PER_SALARY;
						$arr_content[$data_count][promote_salary] = $PROMOTE_SALARY;
						$arr_content[$data_count][layer_no] = $LAYER_NO;

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
//			$xlsRow++;
		} //if($company_name){

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C","L","L","C","L","C","C","C","R","R","R","R","R","R");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_border_2 = array("TLB","TB","TB","TB","TB","TB","TB","TB","TB","TB","TB","TB","TB","TRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
//			echo "|$NAME|<br>";
			$PER_OFFNO = $arr_content[$data_count][per_offno];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL_NO_OLD = $arr_content[$data_count][level_no_old];
			$PER_SALARY_OLD = $arr_content[$data_count][per_salary_old];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$PREV_LEVEL_NO = $arr_content[$data_count][prev_level_no];
			$PREV_PER_SALARY = $arr_content[$data_count][prev_per_salary];
			$PROMOTE_SALARY = $arr_content[$data_count][promote_salary];
//			$LAYER_NO = $arr_content[$data_count][layer_no];
			$LAYER_NO = "";

			if($REPORT_ORDER == "DETAIL"){
				$arr_data = (array) null;
				$arr_data[] = $PER_OFFNO;
				$arr_data[] = $NAME;
				$arr_data[] = $POSITION;
				$arr_data[] = $POS_NO;
				$arr_data[] = $LEVEL_NO_OLD;
				$arr_data[] = $PER_SALARY_OLD;
				$arr_data[] = $LEVEL_NO;
				$arr_data[] = $LAYER_NO;
				$arr_data[] = $PER_SALARY;
				$arr_data[] = "";
				$arr_data[] = $PROMOTE_SALARY;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";

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
			}else{
				if (trim($NAME)) {
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "TLB", 0));
					$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
					$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "B", "L", "TRB", 0));
				}
			}
/*
			if($REPORT_ORDER == "DETAIL"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_OFFNO):$PER_OFFNO), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION):$POSITION), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NO_OLD):$LEVEL_NO_OLD), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 5, ($PER_SALARY_OLD?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY_OLD)): number_format($PER_SALARY_OLD)):(($REPORT_ORDER=="DETAIL")?"-":"")), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 6, (($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NO):$LEVEL_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, ($LAYER_NO?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($LAYER_NO)): number_format($LAYER_NO)):(($REPORT_ORDER=="DETAIL")?"-":"")), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 8, ($PER_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)): number_format($PER_SALARY)):(($REPORT_ORDER=="DETAIL")?"-":"")), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 10, ($PROMOTE_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PROMOTE_SALARY)): number_format($PROMOTE_SALARY)):(($REPORT_ORDER=="DETAIL")?"-":"")), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
			}
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