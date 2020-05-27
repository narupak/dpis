<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R006008_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "e.PL_NAME";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$type_code = "b.PT_CODE";
		$select_type_code = ", b.PT_CODE";
		$mgt_code = "b.PM_CODE";
		$select_mgt_code = ", b.PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "e.PN_NAME";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "e.EP_NAME";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=e.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "e.TP_NAME";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
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
				$select_list .= "f.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "f.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" : 
				if($select_list) $select_list .= ", ";
				$select_list .= "f.ORG_SEQ_NO, f.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "f.ORG_SEQ_NO, f.ORG_CODE, a.DEPARTMENT_ID";

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
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(g.SAH_KF_YEAR='$search_budget_year' and h.MOV_SUB_TYPE in ('49'))";

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

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "บัญชีรายชื่อข้าราชการที่ไม่ได้เลื่อนขั้นเงินเดือน ปีงบประมาณ $search_budget_year";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0608";

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
    	$ws_head_line1 = array("ลำดับ","ส่วนราชการ/ชื่อ - สกุล","ตำแหน่ง","เลขที่","เงินเดือน","หมายเหตุ");
    	$ws_head_line2 = array("ที่","","","","","");
		$ws_colmerge_line1 = array(0,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C");
		$ws_width = array(8,32,30,8,10,12);
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
		global $search_budget_year;
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
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" : 
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(f.ORG_ID_REF = $MINISTRY_ID)";
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
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.SAH_REMARK, h.MOV_NAME $select_type_code $select_mgt_code
						 from		(
											(
												(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
											) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
										) left join PER_MOVMENT h on (g.MOV_CODE=h.MOV_CODE)
											$search_condition
						 group by	$order_by, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)), 
											a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, g.SAH_REMARK, h.MOV_NAME $select_type_code $select_mgt_code
						 order by		g.SAH_REMARK, h.MOV_NAME, $order_by, a.PER_NAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, to_number(replace($position_no,'-','')) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.SAH_REMARK, h.MOV_NAME $select_type_code $select_mgt_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_ORG f, PER_SALARYHIS g, PER_MOVMENT h
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=f.ORG_ID(+) and 
											a.PN_CODE=d.PN_CODE(+) and $line_join(+) and a.PER_ID=g.PER_ID and g.MOV_CODE=h.MOV_CODE(+) 
											$search_condition
						 group by	$order_by, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name, to_number(replace($position_no,'-','')), 
											a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, g.SAH_REMARK, h.MOV_NAME $select_type_code $select_mgt_code
						 order by		g.SAH_REMARK, h.MOV_NAME, $order_by, a.PER_NAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.SAH_REMARK, h.MOV_NAME $select_type_code $select_mgt_code
						 from		(
											(
												(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
											) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
										) left join PER_MOVMENT h on (g.MOV_CODE=h.MOV_CODE)
											$search_condition
						 group by	$order_by, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)), 
											a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, g.SAH_REMARK, h.MOV_NAME $select_type_code $select_mgt_code
						 order by		g.SAH_REMARK, h.MOV_NAME, $order_by, a.PER_NAME ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd<br>";
	$data_count = $data_row = $REASON_COUNT = 0;
	$SAH_REMARK = -1;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		if($SAH_REMARK != trim($data[SAH_REMARK])){
			$SAH_REMARK = trim($data[SAH_REMARK]);
			$REASON_NAME = "";
			switch($SAH_REMARK){
				case 1 :
					$REASON_NAME = "ลาติดตามคู่สมรส";
					break;
				case 2 :
					$REASON_NAME = "เงินเดือนเต็มขั้น";
					break;
				case 3 :
					$REASON_NAME = "ลาฝึกอบรม/ดูงาน";
					break;
				case 4 :
					$REASON_NAME = "บรรจุใหม่";
					break;
			} // end switch case
			if (!$REASON_NAME) $REASON_NAME = trim($SAH_REMARK);
			if (!$REASON_NAME) $REASON_NAME = $data[MOV_NAME];
//			echo "REASON :: $SAH_REMARK :: $REASON_NAME<br>";
			
			$REASON_COUNT++;
			$arr_content[$data_count][type] = "REASON";
			$arr_content[$data_count][name] = $REASON_COUNT .".". $REASON_NAME;
			
			$data_count++;
			initialize_parameter(0);
		} // end if
		
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

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
						
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

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
						
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

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if

					if($rpt_order_index == (count($arr_rpt_order) - 1)){
						$data_row++;
						
						$PER_ID = $data[PER_ID];
						$PN_NAME = trim($data[PN_NAME]);
						$PER_NAME = trim($data[PER_NAME]);
						$PER_SURNAME = trim($data[PER_SURNAME]);
						$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);
						$LEVEL_NO = $data[LEVEL_NO];
						$PER_SALARY = $data[PER_SALARY];
						if(trim( $type_code)){
							$PT_CODE = trim($data[PT_CODE]);
							$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PT_NAME = trim($data3[PT_NAME]);	
						}
						if(trim($mgt_code)){
							$PM_CODE = trim($data[PM_CODE]);
							$cmd = " select PM_NAME from PER_MGT where	PM_CODE=".$PM_CODE;
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PM_NAME = trim($data3[PM_NAME]);
						}
						$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
						$db_dpis3->send_cmd($cmd);
//						$db_dpis->show_error();
						$data3 = $db_dpis3->get_array();
						$LEVEL_NAME=$data3[LEVEL_NAME];
						$POSITION_LEVEL = $data3[POSITION_LEVEL];
						if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
	
						$arr_content[$data_count][type] = "DETAIL";
						$arr_content[$data_count][order] = $data_row;
						$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
						$arr_content[$data_count][pos_no] = $POS_NO;
						$arr_content[$data_count][position] = ($PM_CODE?"$PM_NAME ( ":"") . $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") . ($PM_CODE?" )":"");
						$arr_content[$data_count][salary] = $PER_SALARY;

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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B");
			$wsdata_align_1 = array("R","L","L","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$PER_SALARY = $arr_content[$data_count][salary];

			if($REPORT_ORDER == "REASON"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));

//				print_header();
			}else{			
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $NAME;
				$arr_data[] = $POSITION;
				$arr_data[] = $POS_NO;
				$arr_data[] = $PER_SALARY;
				$arr_data[] = "";

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0)
							$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						else
							$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
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