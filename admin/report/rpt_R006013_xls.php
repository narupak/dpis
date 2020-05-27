<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R006013_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$report_title = "$DEPARTMENT_NAME||การจัดสรรโควตาเลื่อนขั้นเงินเดือนประจำปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type|| ".(($search_salq_type==1)?"1 เมษายน $search_budget_year":"1 ตุลาคม $search_budget_year")." จำแนกตาม ".(($select_org_structure==1)?"โครงสร้างตามมอบหมายงาน":"โครงสร้างตามกฎหมาย")." ระดับ 8 ลงมา";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$report_title = "$DEPARTMENT_NAME||การจัดสรรโควตาเลื่อนขั้นเงินเดือนประจำปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type|| ".(($search_salq_type==1)?"1 เมษายน $search_budget_year":"1 ตุลาคม $search_budget_year")." จำแนกตาม ".(($select_org_structure==1)?"โครงสร้างตามมอบหมายงาน":"โครงสร้างตามกฎหมาย")." ระดับ 8 ลงมา";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$report_title = "$DEPARTMENT_NAME||การจัดสรรโควตาเลื่อนขั้นเงินเดือนประจำปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type|| ".(($search_salq_type==1)?"1 เมษายน $search_budget_year":"1 ตุลาคม $search_budget_year")." จำแนกตาม ".(($select_org_structure==1)?"โครงสร้างตามมอบหมายงาน":"โครงสร้างตามกฎหมาย")." ระดับ 8 ลงมา";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		//$report_title = "$DEPARTMENT_NAME||การจัดสรรโควตาเลื่อนขั้นเงินเดือนประจำปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type|| ".(($search_salq_type==1)?"1 เมษายน $search_budget_year":"1 ตุลาคม $search_budget_year")." จำแนกตาม ".(($select_org_structure==1)?"โครงสร้างตามมอบหมายงาน":"โครงสร้างตามกฎหมาย")." ระดับ 8 ลงมา";
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

	$search_per_type = 1;
	$search_per_status[] = 1;

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

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_code = "R0613_$search_salq_type";

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
		$ws_head_line1 = array("ลำดับ","สังกัด","<**1**>ระดับ","<**1**>ระดับ","<**1**>ระดับ","<**1**>ระดับ","<**1**>ระดับ","<**1**>ระดับ","<**1**>ระดับ","<**1**>ระดับ","รวมทั้งสิ้น","จำนวนคนที่","คงเหลือ","โควตา","จัดสรร");
		$ws_head_line2 = array("ที่","","1","2","3","4","5","6","7","8","(คน)","ไม่ได้เลื่อน","(คน)","ร้อยละ $SALQ_PERCENT","(ร้อยละ)");
		$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TL","T","T","T","T","T","T","TR","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(7,45,6,6,6,6,6,6,6,6,10,10,10,10,10);
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
		global $heading_name, $SALQ_PERCENT;
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
		$worksheet->set_column(0, 0, 7);
		$worksheet->set_column(1, 1, 45);
		$worksheet->set_column(2, 9, 6);
		$worksheet->set_column(10, 10, 10);
		$worksheet->set_column(11, 11, 10);
		$worksheet->set_column(12, 12, 10);
		$worksheet->set_column(13, 13, 10);
		$worksheet->set_column(14, 14, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "รวมทั้งสิ้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "จำนวนคนที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "คงเหลือ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "โควตา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 14, "จัดสรร", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=1; $i<=8; $i++) $worksheet->write($xlsRow, ($i + 1), "$i", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 10, "(คน)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "ไม่ได้เลื่อน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "(คน)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "ร้อยละ $SALQ_PERCENT", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "(ร้อยละ)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
*/
	} // function		

	function count_person($level_no, $salp_yn, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2,$position_table,$position_join;
		global $search_per_type, $search_salq_type, $search_budget_year, $select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($level_no){
			$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
			if($DPISDB=="odbc") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="oci8") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="mysql") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
		} // end if
		
		if($salp_yn !== "") $search_condition .= (trim($search_condition)?" and ":" where ") . "(d.SALQ_YEAR='$search_budget_year' and d.SALQ_TYPE=$search_salq_type and d.SALP_YN=$salp_yn)";
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														PER_PERSONAL a 
														left join $position_table b on ($position_join) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
							$search_condition
							group by		a.PER_ID ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SALPROMOTE d
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+)
												$search_condition
							 group by 	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														PER_PERSONAL a 
														left join $position_table b on ($position_join) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
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
		
		return $count_person;
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
	
	$cmd = " select SALQ_PERCENT from PER_SALQUOTA where SALQ_YEAR='$search_budget_year' and SALQ_TYPE=$search_salq_type ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$SALQ_PERCENT = $data[SALQ_PERCENT] + 0;
	
	if($select_org_structure==0)
		$cmd = " select ORG_ID, SALQD_QTY1, SALQD_QTY2 from PER_SALQUOTADTL1 where SALQ_YEAR='$search_budget_year' and SALQ_TYPE=$search_salq_type ";
	elseif($select_org_structure==1)
		$cmd = " select ORG_ID, SALQD_QTY1, SALQD_QTY2 from PER_SALQUOTADTL2 where SALQ_YEAR='$search_budget_year' and SALQ_TYPE=$search_salq_type ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){ 
		$arr_quota[$data[ORG_ID]][QTY1] = $data[SALQD_QTY1];
		$arr_quota[$data[ORG_ID]][QTY2] = $data[SALQD_QTY2];
	} // end while
	
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_PERSONAL a 
												left join $position_table b on ($position_join)
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_PERSONAL a 
												left join $position_table b on ($position_join)
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	unset($GRAND_LEVEL);
	$GRAND_ALL = $GRAND_UNPROMOTED = $GRAND_DIFF = $GRAND_QUOTA = $GRAND_ASSIGN = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
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

						$data_row++;

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][order] = $data_row;
						$arr_content[$data_count][name] = $ORG_NAME;
						for($i=1; $i<=8; $i++){
							$arr_content[$data_count]["count_".$i] = count_person($i, "", $search_condition, $addition_condition);
							$GRAND_LEVEL[$i] += $arr_content[$data_count]["count_".$i];
						} // end for
						$arr_content[$data_count][count_all] = count_person(0, "", $search_condition, $addition_condition);
						$arr_content[$data_count][count_unpromoted] = count_person(0, 0, $search_condition, $addition_condition);
						$arr_content[$data_count][count_diff] = $arr_content[$data_count][count_all] - $arr_content[$data_count][count_unpromoted];
						$arr_content[$data_count][count_quota] = $arr_quota[$ORG_ID][QTY1];
						$arr_content[$data_count][count_assign] = $arr_quota[$ORG_ID][QTY2];

						$GRAND_ALL += $arr_content[$data_count][count_all];
						$GRAND_UNPROMOTED += $arr_content[$data_count][count_unpromoted];
						$GRAND_DIFF += $arr_content[$data_count][count_diff];
						$GRAND_QUOTA += $arr_content[$data_count][count_quota];
						$GRAND_ASSIGN += $arr_content[$data_count][count_assign];

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
			if ($search_salq_type==2)
					$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C", "L", "R", "R", "R", "R", "R", "R", "R", "R", "R", "R", "R", "R", "R");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			for($i=1; $i<=8; $i++) ${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
			$COUNT_ALL = $arr_content[$data_count][count_all];
			$COUNT_UNPROMOTED = $arr_content[$data_count][count_unpromoted];
			$COUNT_DIFF = $arr_content[$data_count][count_diff];
			$COUNT_QUOTA = $arr_content[$data_count][count_quota];
			$COUNT_ASSIGN = $arr_content[$data_count][count_assign];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME;
			for($i=1; $i<=8; $i++) $arr_data[] = ${"COUNT_".$i};
			$arr_data[] = $COUNT_ALL;
			$arr_data[] = $COUNT_UNPROMOTED;
			$arr_data[] = $COUNT_DIFF;
			$arr_data[] = $COUNT_QUOTA;
			$arr_data[] = $COUNT_ASSIGN;

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
/*
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			for($i=1; $i<=8; $i++) $worksheet->write($xlsRow, ($i + 1), (${"COUNT_".$i}?number_format(${"COUNT_".$i}):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 10, ($COUNT_ALL?number_format($COUNT_ALL):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 11, ($COUNT_UNPROMOTED?number_format($COUNT_UNPROMOTED):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 12, ($COUNT_DIFF?number_format($COUNT_DIFF):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 13, ($COUNT_QUOTA?number_format($COUNT_QUOTA, 2):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			if($search_salq_type==1)
				$worksheet->write($xlsRow, 14, ($COUNT_ASSIGN?number_format($COUNT_ASSIGN):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			elseif($search_salq_type==2)
				$worksheet->write($xlsRow, 14, ($COUNT_ASSIGN?number_format($COUNT_ASSIGN, 2):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
*/
		} // end for				

		$arr_data = (array) null;
		$arr_data[] = "";
		$arr_data[] = "รวมทั้งสิ้น";
		for($i=1; $i<=8; $i++) $arr_data[] = $GRAND_LEVEL[$i];
		$arr_data[] = $GRAND_ALL;
		$arr_data[] = $GRAND_UNPROMOTED;
		$arr_data[] = $GRAND_DIFF;
		$arr_data[] = $GRAND_QUOTA;
		$arr_data[] = $GRAND_ASSIGN;

		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
				else $ndata = $arr_data[$arr_column_map[$i]];
				$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
/*
		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
		$worksheet->write_string($xlsRow, 1, "รวมทั้งสิ้น", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		for($i=1; $i<=8; $i++) $worksheet->write($xlsRow, ($i + 1), ($GRAND_LEVEL[$i]?number_format($GRAND_LEVEL[$i]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 10, ($GRAND_ALL?number_format($GRAND_ALL):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 11, ($GRAND_UNPROMOTED?number_format($GRAND_UNPROMOTED):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 12, ($GRAND_DIFF?number_format($GRAND_DIFF):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 13, ($GRAND_QUOTA?number_format($GRAND_QUOTA, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		if($search_salq_type==1)
			$worksheet->write($xlsRow, 14, ($GRAND_ASSIGN?number_format($GRAND_ASSIGN):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		elseif($search_salq_type==2)
			$worksheet->write($xlsRow, 14, ($GRAND_ASSIGN?number_format($GRAND_ASSIGN, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
*/
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
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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