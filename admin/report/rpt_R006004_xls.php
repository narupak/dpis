<?
	$time1 = time();

	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R006004_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

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
		$line_code = "b.PL_CODE";
		$line_name = "f.PL_NAME";
		$position_no= "b.POS_NO";
		$position_no_name= "b.POS_NO_NAME";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_code = "b.PN_CODE";
                 $position_no_name= "b.POEM_NO_NAME";
		$line_name = "f.PN_NAME";
		$position_no= "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_code = "b.EP_CODE";
                $position_no_name= "b.POEMS_NO_NAME";
		$line_name = "f.EP_NAME";
		$position_no= "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_code = "b.TP_CODE";
                $position_no_name= "b.POT_NO_NAME";
		$line_name = "f.TP_NAME";
		$position_no= "b.POT_NO";
	} // end if	
	
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

			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1";
		else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1";
		else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1";
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($DPISDB=="odbc"){
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-12-31')";
	}elseif($DPISDB=="oci8"){
		$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) < '". ($search_budget_year - 543) ."-12-31')";
	}elseif($DPISDB=="mysql"){
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-12-31')";
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
        if($select_org_structure==0){
           if($search_org_id)  $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
        }elseif($select_org_structure==1){
            if($search_org_ass_id)  $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
        }
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "บัญชีรายละเอียดการเลื่อนขั้นเงินเดือน $PERSON_TYPE[$search_per_type] ย้อนหลัง 5 ปี||กรม/ส่วนราชการที่เทียบเท่า $DEPARTMENT_NAME";
    $report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0604";
/*
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
*/
	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		$ws_head_line1 = array("","","","","","<**1**>ปีงบประมาณ","<**1**>ปีงบประมาณ","<**1**>ปีงบประมาณ","<**1**>ปีงบประมาณ","<**1**>ปีงบประมาณ","<**1**>ปีงบประมาณ","<**1**>ปีงบประมาณ","<**1**>ปีงบประมาณ","<**1**>ปีงบประมาณ","<**1**>ปีงบประมาณ");
		$ws_head_line2 = array("ลำดับ","ชื่อ - สกุล","เลขที่","ตำแหน่ง","เงินเดือน","<**2**>$search_budget_year","<**2**>$search_budget_year","<**3**>$search_budget_year_1","<**3**>$search_budget_year_1","<**4**>$search_budget_year_2","<**4**>$search_budget_year_2","<**5**>$search_budget_year_3","<**5**>$search_budget_year_3","<**6**>$search_budget_year_4","<**6**>$search_budget_year_4");
		$ws_head_line3 = array("ที่","","ตำแหน่ง","","ปัจจุบัน","ครั้งที่ 2","ครั้งที่ 1","ครั้งที่ 2","ครั้งที่ 1","ครั้งที่ 2","ครั้งที่ 1","ครั้งที่ 2","ครั้งที่ 1","ครั้งที่ 2","ครั้งที่ 1");
		$ws_colmerge_line1 = array(0,0,0,0,0,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line2 = array(0,0,0,0,0,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TL","T","T","T","T","T","T","T","T","TR");
		$ws_border_line2 = array("LR","RL","RL","RL","RL","TL","TR","TL","TR","TL","TR","TL","TR","TL","TR");
		$ws_border_line3 = array("LBR","RBL","RBL","RBL","RBL","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(8,30,8,40,10,8,8,8,8,8,8,8,8,8,8);
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
		global $heading_name,$NUMBER_DISPLAY;
		global $search_budget_year;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_fontfmt_line1, $ws_fontfmt_line2;
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
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(g.ORG_ID_REF= $MINISTRY_ID)";
					break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
					break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1)  $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1)  $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_1" :
					if($select_org_structure==0){
						if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
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
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1;
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
				case "ORG_1" :	
					$ORG_ID_1 = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
//	if ($search_condition) $search_condition = $search_condition." and a.PER_ID < 1000"; else $search_condition = "a.PER_ID < 1000";
	if($DPISDB=="odbc"){
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											$position_no_name as POS_NO_NAME, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP, e.SM_CODE $select_type_code
						 from		(
											(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on ($line_join)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
						 order by		$order_by, IIf(IsNull($position_no), 0, CLng($position_no)), LEFT(trim(e.SAH_EFFECTIVEDATE), 10) desc, LEFT(trim(e.SAH_DOCDATE), 10) desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, to_number(replace($position_no,'-','')) as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP, e.SM_CODE $select_type_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_SALARYHIS e, $line_table f, PER_ORG g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID and $line_join(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
											$search_condition
						 order by		$order_by, to_number(replace($position_no,'-','')), SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) desc, SUBSTR(trim(e.SAH_DOCDATE), 1, 10) desc ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP, e.SM_CODE $select_type_code
						 from		(
											(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on ($line_join)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
						 order by		$order_by, $position_no, e.SAH_EFFECTIVEDATE desc, e.SAH_DOCDATE desc ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "<pre>".$cmd."|.$count_data.|";//die();

//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";
	
	$arr_file = (array) null;
	$f_new = false;
	$fname= "../../Excel/tmp/rpt_R006004_xls";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnumtext="";
	$workbook = new writeexcel_workbook($fname1);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	while($data = $db_dpis->get_array()){
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
				$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
				switch($REPORT_ORDER){
					case "MINISTRY" :
						if($MINISTRY_ID != trim($data[MINISTRY_ID])){
							$MINISTRY_ID = trim($data[MINISTRY_ID]);
							if($MINISTRY_ID != "" && $MINISTRY_ID != 0 && $MINISTRY_ID != -1){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$MINISTRY_NAME = $data2[ORG_NAME];
							} // end if

							if($MINISTRY_NAME) {
								$addition_condition = generate_condition($rpt_order_index);

								$arr_content[$data_count][type] = "MINISTRY";
		//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
								$arr_content[$data_count][name] = $MINISTRY_NAME;
								
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

								$data_count++;
							}
						} // end if
					break;
					case "DEPARTMENT" : 
						if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
							$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
							if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$DEPARTMENT_NAME = $data2[ORG_NAME];
							} // end if

							if($DEPARTMENT_NAME) {
								$addition_condition = generate_condition($rpt_order_index);

								$arr_content[$data_count][type] = "DEPARTMENT";
								$arr_content[$data_count][name] = $DEPARTMENT_NAME;
								
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

								$data_count++;
							}
						} // end if
					break;
					case "ORG" :
						if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
							} // end if
	//                       if($ORG_NAME=="" || $ORG_NAME=="NULL")	$ORG_NAME="[ไม่ระบุ $ORG_TITLE]";
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
							if($ORG_NAME) {
								$addition_condition = generate_condition($rpt_order_index);
		
								$arr_content[$data_count][type] = "ORG";
		//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
								$arr_content[$data_count][name] = $ORG_NAME;
								
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
		
								$data_count++;
							}
						} // end if
					break;
	
					case "ORG_1" :
						if($ORG_ID_1 != trim($data[ORG_ID_1])){
							$ORG_ID_1 = trim($data[ORG_ID_1]);
							$ORG_NAME_1 = $ORG_SHORT_1 = "";
							if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_1 = $data2[ORG_NAME];
							} // end if
	//	              if($ORG_NAME_1=="" || $ORG_NAME_1=="NULL")	$ORG_NAME_1="[ไม่ระบุ$ORG_TITLE1]";
							if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
							if($ORG_NAME_1) {
								$addition_condition = generate_condition($rpt_order_index);
		
								$arr_content[$data_count][type] = "ORG_1";
		//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
								$arr_content[$data_count][name] = $ORG_NAME_1;
								
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
		
								$data_count++;
							}
						} // end if
					break;
				} // end switch case
	
				if($rpt_order_index == (count($arr_rpt_order) - 1)){
					if($PER_ID != $data[PER_ID]){
						$data_row++;
								
						$PER_ID = $data[PER_ID];
						$PN_NAME = trim($data[PN_NAME]);
						$PER_NAME = trim($data[PER_NAME]);
						$PER_SURNAME = trim($data[PER_SURNAME]);
						//$POS_NO = $data[POS_NO];
						$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);
						$LEVEL_NO = $data[LEVEL_NO];
						if(trim($type_code)){
							$PT_CODE = trim($data[PT_CODE]);
							$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PT_NAME = trim($data3[PT_NAME]);
						}
						$PER_SALARY = $data[PER_SALARY];
						
						$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
						$db_dpis3->send_cmd($cmd);
	//					$db_dpis->show_error();
						$data3 = $db_dpis3->get_array();
						$LEVEL_NAME=$data3[LEVEL_NAME];
						$POSITION_LEVEL = $data3[POSITION_LEVEL];
						if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
			
						$arr_content[$data_count][type] = "DETAIL";
						$arr_content[$data_count][order] = $data_row;
	//					$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
						$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
						$arr_content[$data_count][pos_no] = $POS_NO;
						$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
						$arr_content[$data_count][salary] = $PER_SALARY;
						for($i=1; $i<=10; $i++){ 
							$arr_content[$data_count]["count_".$i] = 0;
							$arr_content[($data_count + 1)]["count_".$i] = 0;
						} // end for
		
						$data_count++;
							
						$arr_content[$data_count][type] = "DETAIL2";
						$data_count++;
                        
                                                if($search_per_type!=2){
                                                    $arr_content[$data_count][type] = "DETAIL3";
                                                    $data_count++;
                                                }
                                                
					} // end if
	
					$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
					$arr_temp = explode("-", $SAH_EFFECTIVEDATE);
					$SAH_YEAR = $arr_temp[0] + 543;
					$SAH_MONTH = $arr_temp[1] + 0;
					$SAH_DATE = $arr_temp[2] + 0;
								
					$SM_CODE = trim($data[SM_CODE]);
					$MOV_CODE = trim($data[MOV_CODE]);
					$cmd = " select MOV_SUB_TYPE,MOV_NAME from PER_MOVMENT where	MOV_CODE='$MOV_CODE' ";
					$db_dpis3->send_cmd($cmd);
					$data3 = $db_dpis3->get_array();
					$MOV_SUB_TYPE = trim($data3[MOV_SUB_TYPE]);
                                        $MOV_NAME = trim($data3[MOV_NAME]);
					$PR_LEVEL = 0;
                                        
					if (($MOV_SUB_TYPE == '45') || ($BKK_FLAG==1 && $SM_CODE == '2') || ($BKK_FLAG!=1 && $SM_CODE == '1')) {
						$PR_LEVEL = 0.5;
					} elseif (($MOV_SUB_TYPE == '46') || ($BKK_FLAG==1 && $SM_CODE == '3') || ($BKK_FLAG!=1 && $SM_CODE == '2')) {
						$PR_LEVEL = 1.0;
					} elseif (($MOV_SUB_TYPE == '47') || ($BKK_FLAG==1 && $SM_CODE == '4') || ($BKK_FLAG!=1 && $SM_CODE == '3')) {
						$PR_LEVEL = 1.5;
					} elseif (($MOV_SUB_TYPE == '48') || ($BKK_FLAG==1 && $SM_CODE == '5') || ($BKK_FLAG!=1 && $SM_CODE == '4')) {
						$PR_LEVEL = 2.0;
					} else {
						$PR_LEVEL = 0;
					}
                                        
                                        if(!$PR_LEVEL){
                                            if(strpos($MOV_NAME, "0.5 ขั้น") == TRUE){ $PR_LEVEL = 0.5;}
                                            if(strpos($MOV_NAME, "1 ขั้น") == TRUE){ $PR_LEVEL = 1.0;}
                                            if(strpos($MOV_NAME, "1.5 ขั้น") == TRUE){ $PR_LEVEL = 1.5;}
                                            if(strpos($MOV_NAME, "2 ขั้น") == TRUE){ $PR_LEVEL = 2.0;}
                                        }
                                        
					$SAH_SALARY = $data[SAH_SALARY];
					$SAH_PERCENT_UP = $data[SAH_PERCENT_UP];
					if ($SAH_PERCENT_UP) $PR_LEVEL = $SAH_PERCENT_UP;
					
					if($SAH_MONTH == 10){
                                            if ($SAH_PERCENT_UP || $PR_LEVEL) { //if ($SAH_PERCENT_UP) {
                                                if($search_per_type!=2){
                                                    if (!$arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)]){
                                                        $arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] = $PR_LEVEL;
                                                        $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] =$MOV_NAME;
                                                    }	
                                                }else{
                                                    if (!$arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)])
                                                        $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] = $PR_LEVEL;
                                                }    
                                            } else {
                                                if($search_per_type!=2){
                                                    $arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] += $PR_LEVEL;
                                                    $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] +=$MOV_NAME; 
                                                }else{
                                                    $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] += $PR_LEVEL;
                                                }    
                                            }
                                            if($SAH_SALARY > $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)]) $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] = $SAH_SALARY;
					
					}elseif($SAH_MONTH == 4){
                                            if ($SAH_PERCENT_UP || $PR_LEVEL) { //if ($SAH_PERCENT_UP) {
                                                if($search_per_type!=2){
                                                    if (!$arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)]){
                                                        $arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] = $PR_LEVEL;
                                                        $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] =$MOV_NAME;  
                                                    }
                                                }else{
                                                    if (!$arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)])
                                                        $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] = $PR_LEVEL;
                                                }    
                                            } else {
                                                if($search_per_type!=2){
                                                    $arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] += $PR_LEVEL;
                                                    $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] +=$MOV_NAME;
                                                }else{
                                                    $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] += $PR_LEVEL;
                                                }    
                                            }
                                            if($SAH_SALARY > $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)]) $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] = $SAH_SALARY;
					
					}
				} // end if
			} // end for
		}
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("R","L","C","L","R","C","C","C","C","C","C","C","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		$f_new = true;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			// เช็คจบที่ข้อมูล $data_limit
			if ($data_count > 0 && ($data_count % $file_limit) == 0) {
			
				$workbook->close();
				$arr_file[] = $fname1;
	
				$fnum++;
				$fname1=$fname."_$fnum.xls";
				$workbook = new writeexcel_workbook($fname1);
	
				//====================== SET FORMAT ======================//
				require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
				//====================== SET FORMAT ======================//
	
				$f_new = true;
			};
			// เช็คจบที่ข้อมูล $data_limit
			if(($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
				if ($f_new) {
					$sheet_no=0; $sheet_no_text="";
					if($data_count > 0) $count_org_ref++;
					$f_new = false;
				} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
					$sheet_no++;
					$sheet_no_text = "_$sheet_no";
				}

				$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text);
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);

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

			} // end if

			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$PER_SALARY = $arr_content[$data_count][salary];
			for($i=1; $i<=10; $i++) ${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME;
			$arr_data[] = $POS_NO;
			$arr_data[] = $POSITION;
			if($REPORT_ORDER == "DETAIL"){
				$arr_data[] = $PER_SALARY;
				for($i=1; $i<=10; $i++) $arr_data[] = ${"COUNT_".$i};
                
			}elseif($REPORT_ORDER == "DETAIL2"){
				$arr_data[] = "";
				for($i=1; $i<=10; $i++) $arr_data[] = ${"COUNT_".$i};
                
			}else if($REPORT_ORDER == "DETAIL3"){
                                if($search_per_type!=2){
                                    $arr_data[] = "";
                                    for($i=1; $i<=10; $i++) $arr_data[] = ${"COUNT_".$i};
                                } 
                
			}else{
                $arr_data[] = "";
                for($i=1; $i<=10; $i++) $arr_data[] = "";
			} // end if

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if($REPORT_ORDER == "DETAIL" || $REPORT_ORDER == "DETAIL2" || $REPORT_ORDER == "DETAIL3")
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
		} // end for				
	}else{
            $workbook = new writeexcel_workbook($fname1);
            $worksheet = &$workbook->addworksheet("$report_code");
            $worksheet->set_margin_right(0.50);
            $worksheet->set_margin_bottom(1.10);
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
		$worksheet->write($xlsRow, 10,"", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11,"", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12,"", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13,"", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14,"", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();
	$arr_file[] = $fname1;

	ini_set("max_execution_time", 30);
?>
<html>
<head>
<title><?=$webpage_title?> - <?=$MENU_TITLE_LV0?><?if($MENU_ID_LV1){?> - <?=$MENU_TITLE_LV1?><?}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-874">
<link rel="stylesheet" href="<?="../$cssfileselected";?>" type="text/css">
<link rel="alternate stylesheet" type="text/css" media="all" href="../stylesheets/calendar-blue.css" title="winter"/>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="label_normal">
    <tr> 
	  <td align="left" valign="top">
<!--// copy from current_location.html -->
		    <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="28"><table border="0" cellspacing="0" cellpadding="0" class="header_current_location_table">
                  <? if(!$HIDE_HEADER){ ?>
		    <tr>
                    <td width="10" height="">&nbsp;</td>
                    <td class="header_current_location">&reg;&nbsp;<?=$MENU_TITLE_LV0?><? if($MENU_ID_LV1>0){ ?> &gt; <?=$MENU_TITLE_LV1?><? } ?><? if($MENU_ID_LV2>0){ ?> &gt; <?=$MENU_TITLE_LV2?><? } ?><? if($MENU_ID_LV3>0){ ?> &gt; <?=$MENU_TITLE_LV3?><? } ?><?=$OPTIONAL_TITLE?> </td>
					<td width="40" class="header_current_location_right">&nbsp;</td>
					<td align="right" valign="middle" style="background:#FFF;" nowrap>&nbsp;
                    <?
             			// หา จำนวน user ที่ยังไม่มีการ logout
						$cmd = " select distinct user_id, from_ip from user_last_access where f_logout != '1' or f_logout is null ";
						$cnt = $db->send_cmd($cmd);
                        echo "<font size=\"+1\" color=\"#0000FF\"><B>$cnt</B>&nbsp;<img src=\"../images/man_small.gif\" height=\"18\" width=\"20\">&nbsp;online</font>";
                    ?>
                    </td>
                  </tr>
		    <? }else{ ?>
		    <tr>
                    <td width="10" height="28">&nbsp;</td>
                    <td class="header_current_location">&reg;&nbsp;<?=$OPTIONAL_TITLE?> </td>
					<td width="40" class="header_current_location_right">&nbsp;</td>
					<td align="right">&nbsp;</td>
                  </tr>
		    <? } // end if ?>
                </table></td>
              </tr>
            </table>
<!--// end copy from current_location.html -->
	  </td>	
	</tr>
<tr><td>
   	<div style=" margin-top:5px; margin-bottom:5px; width:100%"><table style="border: 1px solid #666666;" width="100%">
<?
	ini_set("max_execution_time", 30);

	if (count($arr_file) > 0) {
		echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
		echo "<tr><td width='3%'>&nbsp;</td><td>แฟ้ม Excel ที่สร้าง จำนวน ".count($arr_file)." แฟ้ม</td></tr>";
		echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
		$grp = "";
		echo "<tr><td width='3%'>&nbsp;</td><td>";
		echo "<table width='95%' class='table_body'>";
		$num=0;
		for($i_file = 0; $i_file < count($arr_file); $i_file++) {
			echo "<tr><td width='25%'></td>";
			echo "<td><font size='-1' color='#CC7733'><B>".$num."</B></font> : <a href=\"".$arr_file[$i_file]."\">".$arr_file[$i_file]."</a></td></tr>";
			$num++;
		}
		echo "</table>";
		echo "</td></tr>";
	}
	echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	$time2 = time();
	$tdiff = $time2 - $time1;
	$s = $tdiff % 60;	// วินาที
	$m = floor($tdiff / 60);	// นาที
	$h = floor($m / 60);	// ชม.
	$m = $m % 60;	// นาที
	$show_lap = ($h?"$h ชม. ":"").($m?"$m นาที ":"")."$s วินาที";
	echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	echo "<tr><td width='3%'>&nbsp;</td><td>เริ่ม : <font color='#FF0000'>".date("d-m-Y h:i:s",$time1)."</font><font color='#000000'> จบ : </font><font color='#FF0000'>".date("d-m-Y h:i:s",$time2)."</font><font color='#000000'> ใช้เวลา </font><font color='#FF0000'>$show_lap</font> <font color='#333333'>[</font><font color='#000000'>$tdiff วินาที</font><font color='#333333'>]</font></td></tr>";
	echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	echo "<tr><td width='3%'>&nbsp;</td><td>จบรายงาน </td></tr>";
?>
	</table></div>
    </td></tr>
</table>
</body>
</html>
