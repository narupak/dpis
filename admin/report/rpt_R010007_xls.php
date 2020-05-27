<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
		
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R010007_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

//--------------------------------------------------------------------------------------------------------
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

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
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);

	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID_REF";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

//				$heading_name .= " สายงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "d.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "d.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";
		}else{
			if($select_org_structure==0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		}
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_TYPE", $list_type)){	//จะต้องมีค่าส่งมาถึงจะสร้างรายงานได้
		$list_type_text = "";
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " $PROVINCE_NAME";
		} // end if

		if($search_pt_name){ $list_type_text .=" ตำแหน่งประเภท : $search_pt_name"; }
		if($search_per_type==1){
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= " / $PL_TITLE : $search_pl_name";
			}
		}elseif($search_per_type==2){
			if(trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
				$list_type_text .= " / $POS_EMP_TITLE : $search_pn_name";
			}
		}elseif($search_per_type==3){
			if(trim($search_ep_code)){
				$search_ep_code = trim($search_ep_code);
				$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
				$list_type_text .= " / $POS_EMPSER_TITLE : $search_ep_name";
			}
		} // end if
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(a.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";

	$report_title = "จำนวน$PERSON_TYPE[$search_per_type] แยกตามเพศ $heading_name";
	$report_title .= " ประจำปีงบประมาณ  พ.ศ. $search_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "H07";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$ws_head_line1[0] = "";
	$ws_head_line2[0] = "ส่วนราชการ";
	$ws_head_line3[0] = "";
	$ws_colmerge_line1[0] = 0;
	$ws_colmerge_line2[0] = 0;
	$ws_colmerge_line3[0] = 0;
	$ws_border_line1[0] = "TLR";
	$ws_border_line2[0] = "LR";
	$ws_border_line3[0] = "LBR";
	$ws_fontfmt_line1[0] = "B";
	$ws_fontfmt_line2[0] = "B";
	$ws_fontfmt_line3[0] = "B";
	$ws_headalign_line1[0] = "C";
	$ws_headalign_line2[0] = "C";
	$ws_headalign_line3[0] = "C";
	$ws_width[0] = 50;
	$cnt = 0;
	for($k=0;$k<count($ARR_pt_name);$k++) {
		$count_position_type=count($ARR_POSITION_TYPE[$ARR_pt_name[$k]]);
		$cnt1 = 0;
		for($i=0;$i<$count_position_type;$i++) {
			$tmp_position_type=$ARR_POSITION_TYPE[$ARR_pt_name[$k]][$i];
			$count=count($ARR_GENDER[$ARR_pt_name[$k]][$i]);
			for($j=0;$j<$count;$j++) {
				$tmp_gender = $ARR_GENDER[$ARR_pt_name[$k]][$i][$j];
				$cnt++; $cnt1++;
				$ws_head_line1[$cnt] = "<**".($k+1)."**>".$ARR_pt_name[$k];
				$ws_head_line2[$cnt] = "<**".($i+1)."**>$tmp_position_type";
				$ws_head_line3[$cnt] = "$tmp_gender";
				$ws_colmerge_line1[$cnt] = 1;
				$ws_colmerge_line2[$cnt] = 1;
				$ws_colmerge_line3[$cnt] = 0;
				$ws_border_line1[$cnt] = "TLR";
				$ws_border_line2[$cnt] = "TLR";
				$ws_border_line3[$cnt] = "TLBR";
				$ws_fontfmt_line1[$cnt] = "B";
				$ws_fontfmt_line2[$cnt] = "B";
				$ws_fontfmt_line3[$cnt] = "B";
				$ws_headalign_line1[$cnt] = "C";
				$ws_headalign_line2[$cnt] = "C";
				$ws_headalign_line3[$cnt] = "C";
				$ws_width[$cnt] = 5;
			}
		}
	}
	$ws_head_line1[$cnt+1] = "<**".($k+2)."**>";
	$ws_head_line2[$cnt+1] = "<**".($cnt1+2)."^**>จำนวนรวม";
	$ws_head_line3[$cnt+1] = "ชาย";
	$ws_head_line1[$cnt+2] = "<**".($k+2)."**>";
	$ws_head_line2[$cnt+2] = "<**".($cnt1+2)."^**>จำนวนรวม";
	$ws_head_line3[$cnt+2] = "หญิง";
	$ws_head_line1[$cnt+3] = "<**".($k+2)."**>";
	$ws_head_line2[$cnt+3] = "<**".($cnt1+2)."^**>จำนวนรวม";
	$ws_head_line3[$cnt+3] = "รวม";
	$ws_colmerge_line1[$cnt+1] = 1;
	$ws_colmerge_line2[$cnt+1] = 1;
	$ws_colmerge_line3[$cnt+1] = 0;
	$ws_colmerge_line1[$cnt+2] = 1;
	$ws_colmerge_line2[$cnt+2] = 1;
	$ws_colmerge_line3[$cnt+2] = 0;
	$ws_colmerge_line1[$cnt+3] = 1;
	$ws_colmerge_line2[$cnt+3] = 1;
	$ws_colmerge_line3[$cnt+3] = 0;
	$ws_border_line1[$cnt+1] = "TL";
	$ws_border_line2[$cnt+1] = "L";
	$ws_border_line3[$cnt+1] = "TLBR";
	$ws_border_line1[$cnt+2] = "T";
	$ws_border_line2[$cnt+2] = "";
	$ws_border_line3[$cnt+2] = "TLBR";
	$ws_border_line1[$cnt+3] = "TR";
	$ws_border_line2[$cnt+3] = "R";
	$ws_border_line3[$cnt+3] = "TLBR";
	$ws_fontfmt_line1[$cnt+1] = "B";
	$ws_fontfmt_line2[$cnt+1] = "B";
	$ws_fontfmt_line3[$cnt+1] = "B";
	$ws_fontfmt_line1[$cnt+2] = "B";
	$ws_fontfmt_line2[$cnt+2] = "B";
	$ws_fontfmt_line3[$cnt+2] = "B";
	$ws_fontfmt_line1[$cnt+3] = "B";
	$ws_fontfmt_line2[$cnt+3] = "B";
	$ws_fontfmt_line3[$cnt+3] = "B";
	$ws_headalign_line1[$cnt+1] = "C";
	$ws_headalign_line2[$cnt+1] = "C";
	$ws_headalign_line3[$cnt+1] = "C";
	$ws_headalign_line1[$cnt+2] = "C";
	$ws_headalign_line2[$cnt+2] = "C";
	$ws_headalign_line3[$cnt+2] = "C";
	$ws_headalign_line1[$cnt+3] = "C";
	$ws_headalign_line2[$cnt+3] = "C";
	$ws_headalign_line3[$cnt+3] = "C";
	$ws_width[$cnt+1] = 6;
	$ws_width[$cnt+2] = 6;
	$ws_width[$cnt+3] = 6;

	$count_position_type=count($ARR_POSITION_TYPE[$search_pt_name]);
	$count_column=$count_position_type*count($ARR_COL);

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
		global $heading_width, $heading_name;
		global $count_position_type,$search_pt_name;
		global $ARR_POSITION_TYPE,$ARR_GENDER;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_fontfmt_line1, $ws_fontfmt_line2, $ws_fontfmt_line3;
		global $ws_headalign_line1, $ws_headalign_line2, $ws_headalign_line3, $ws_width;
		
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
				$worksheet->write($xlsRow, $colseq, $ws_head_line3[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line3[$arr_column_map[$i]], $ws_headalign_line3[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		

	function count_person($level_no,$gender, $search_condition, $addition_condition){ 
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type, $select_org_structure;
	
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
		
		if($gender=="ชาย"){				$search_condition .= (trim($search_condition)?" and ":" where ") . " (b.PER_GENDER=1) ";		}
		elseif($gender=="หญิง"){		$search_condition .= (trim($search_condition)?" and ":" where ") . " (b.PER_GENDER=2) ";		}
		elseif($gender=="รวม"){		$search_condition .= (trim($search_condition)?" and ":" where ") . " (b.PER_GENDER=1 or b.PER_GENDER=2)";		}

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);

		//หาจำนวน
		if($search_per_type==1){
			$pos_tb="PER_POSITION";
			$join_tb="a.POS_ID=b.POS_ID";
		}elseif($search_per_type==2){	
			$pos_tb="PER_POS_EMP";
			$join_tb="a.POEM_ID=b.POEM_ID";
		}elseif($search_per_type==3){ 
			$pos_tb="PER_POS_EMPSER";
			$join_tb="a.POEMS_ID=b.POEMS_ID";
		}

		if($DPISDB=="odbc"){
			$cmd = " select			count(b.PER_ID) as count_person
							from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													)	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
								 where		(b.LEVEL_NO='$level_no') 
													$search_condition
							 group by		b.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		count(b.PER_ID) as count_person
							from			$pos_tb a, PER_PERSONAL b,PER_ORG c, PER_ORG d
							where		$join_tb(+) and	a.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=d.ORG_ID(+) 
							and 			(b.LEVEL_NO='$level_no') 
												$search_condition
							group by		b.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(b.PER_ID) as count_person
							from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													)	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
								 where		(b.LEVEL_NO='$level_no') 
													$search_condition
							 group by		b.PER_ID ";
		} // end if

		if($select_org_structure==1){ 
			$cmd = str_replace("a.ORG_ID", "b.ORG_ID", $cmd);
			 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
		//echo "<br>$cmd<br>";
		//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if
		return $count_person;
	} // function

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
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
	
	//หาชื่อส่วนราชการ 
	if($search_per_type==1){
		$pos_tb="PER_POSITION";
		$join_tb="a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){	
		$pos_tb="PER_POS_EMP";
		$join_tb="a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		$pos_tb="PER_POS_EMPSER";
		$join_tb="a.POEMS_ID=b.POEMS_ID";
	}
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
							 from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													) 	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
												$search_condition
							 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
							 from			$pos_tb a,PER_PERSONAL  b, PER_ORG c, PER_ORG  d
							 where		$join_tb(+) and a.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=d.ORG_ID(+)
												$search_condition
							 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
							 from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													) 	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
												$search_condition
							 order by		$order_by ";
	}
	if($select_org_structure==1){
		 $cmd = str_replace("a.ORG_ID", "b.ORG_ID", $cmd);	
		 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
// echo "<br>$cmd<br>";
//	$db_dpis->show_error();

	$data_count = 0;
	for($k=0;$k<count($ARR_pt_name); $k++){
		$pt_name = $ARR_pt_name[$k];
		$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
		for($i=0;$i<$count_position_type; $i++){
			$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
			$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
			for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
				$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
				
				if($tmp_gender=="ชาย"){	$label_gender="M"; }
				elseif($tmp_gender=="หญิง"){	$label_gender="F"; }
				elseif($tmp_gender=="รวม"){		$label_gender="S"; }
				
				$LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] = 0;
				$LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] = 0;
				$LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] = 0;
			}//end for
		}//end for
	} // end for $k
	initialize_parameter(0);
	$first_order = 1;	// order ที  0 = COUNTRY ยังไม่ได้คำนวณ เริ่ม order ที่ 1 (MINISTRY) ก่อน
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
				//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							$MINISTRY_SHORT = $data2[ORG_SHORT];
				
					//if($f_all){
					if(($MINISTRY_NAME !="" && $MINISTRY_NAME !="-") || ($BKK_FLAG==1 && $MINISTRY_NAME !="" && $MINISTRY_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);
				
						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
						$arr_content[$data_count][short_name] = $MINISTRY_SHORT;
				
						for($k=0;$k<count($ARR_pt_name); $k++){
							$pt_name = $ARR_pt_name[$k];
							$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
							for($i=0;$i<$count_position_type; $i++){
								$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
								$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
								for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
									$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
									//--set gender label	
									if($tmp_gender=="ชาย"){				$label_gender="M"; }
									elseif($tmp_gender=="หญิง"){	$label_gender="F";	}
									elseif($tmp_gender=="รวม"){	 	$label_gender="S";	}
									//---------------------------------------------------------------------------
				
									$arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender] = count_person($tmp_level_no,$tmp_gender,$search_condition, $addition_condition);
									//รวมจำนวนทั้งหมดแยก			
									if($tmp_gender=="ชาย"){	
										$arr_content[$data_count][total_m] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];	//รวมชาย
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดชาย
									}elseif($tmp_gender=="หญิง"){	
										$arr_content[$data_count][total_f] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมหญิง
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดหญิง
									 }elseif($tmp_gender=="รวม"){		
										$arr_content[$data_count][total_s] = ($arr_content[$data_count][total_m]+$arr_content[$data_count][total_f]);		//รวมทั้งหมด ช+ญ
									}
				
									//รวมจำนวนทั้งหมดจากคอลัมน์  แถวแรกจนแถวสุดท้าย
									if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
								} // end for
							} // end for
						} // end for $k
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
							} // end if(($MINISTRY_NAME !="" && $MINISTRY_NAME !="-") || ($BKK_FLAG==1 && $MINISTRY_NAME !="" && $MINISTRY_NAME !="-"))
						//} // end if ($f_all)
					} // end if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1)
				} // end if
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
							$DEPARTMENT_ORG_SHORT = $data2[ORG_SHORT];

							if(($DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-") || ($BKK_FLAG==1 && $DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
							
								$arr_content[$data_count][type] = "DEPARTMENT";
								$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)).$DEPARTMENT_NAME;
								$arr_content[$data_count][short_name] = $DEPARTMENT_ORG_SHORT;
							
								for($k=0;$k<count($ARR_pt_name); $k++){
									$pt_name = $ARR_pt_name[$k];
									$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
									for($i=0;$i<$count_position_type; $i++){
										$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
										$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
										for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
											$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
											//--set gender label	
											if($tmp_gender=="ชาย"){				$label_gender="M"; }
											elseif($tmp_gender=="หญิง"){	$label_gender="F";	}
											elseif($tmp_gender=="รวม"){	 	$label_gender="S";	}
											//---------------------------------------------------------------------------
							
											$arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender] = count_person($tmp_level_no,$tmp_gender,$search_condition, $addition_condition);
											//รวมจำนวนทั้งหมดแยก			
											if($tmp_gender=="ชาย"){	
												$arr_content[$data_count][total_m] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];	//รวมชาย
												if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดชาย
											}elseif($tmp_gender=="หญิง"){	
												$arr_content[$data_count][total_f] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมหญิง
												if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดหญิง
											 }elseif($tmp_gender=="รวม"){		
												$arr_content[$data_count][total_s] = ($arr_content[$data_count][total_m]+$arr_content[$data_count][total_f]);		//รวมทั้งหมด ช+ญ
											}
							
											//รวมจำนวนทั้งหมดจากคอลัมน์  แถวแรกจนแถวสุดท้าย
											if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
										} // end for
									} // end for
								} // end for $k
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
									} // end if(($DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-") || ($BKK_FLAG==1 && $DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-"))
								} // end if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1)
							} // end if
				break;
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;

					if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][short_name] = $ORG_SHORT;

						for($k=0;$k<count($ARR_pt_name); $k++){
							$pt_name = $ARR_pt_name[$k];
							$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
							for($i=0;$i<$count_position_type; $i++){
								$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
								$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
								for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
									$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
									//--set gender label	
									if($tmp_gender=="ชาย"){				$label_gender="M"; }
									elseif($tmp_gender=="หญิง"){	$label_gender="F";	}
									elseif($tmp_gender=="รวม"){	 	$label_gender="S";	}
									//---------------------------------------------------------------------------
	
									$arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender] = count_person($tmp_level_no,$tmp_gender,$search_condition, $addition_condition);
									//รวมจำนวนทั้งหมดแยก			
									if($tmp_gender=="ชาย"){	
										$arr_content[$data_count][total_m] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];	//รวมชาย
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดชาย
									}elseif($tmp_gender=="หญิง"){	
										$arr_content[$data_count][total_f] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมหญิง
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดหญิง
									 }elseif($tmp_gender=="รวม"){		
										$arr_content[$data_count][total_s] = ($arr_content[$data_count][total_m]+$arr_content[$data_count][total_f]);		//รวมทั้งหมด ช+ญ
									}
	
									//รวมจำนวนทั้งหมดจากคอลัมน์  แถวแรกจนแถวสุดท้าย
									if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
								} // end for
							} // end for
						} // end for $k
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
						} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
					} // end if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1)
				} // end if
				break;
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";

						for($k=0;$k<count($ARR_pt_name); $k++){
							$pt_name = $ARR_pt_name[$k];
							$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
							for($i=0;$i<$count_position_type; $i++){
								$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
								$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
								for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
									$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
									//--set gender label	
									if($tmp_gender=="ชาย"){				$label_gender="M"; }
									elseif($tmp_gender=="หญิง"){	$label_gender="F";	}
									elseif($tmp_gender=="รวม"){	 	$label_gender="S";	}
									//---------------------------------------------------------------------------
	
									$arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender] = count_person($tmp_level_no,$tmp_gender,$search_condition, $addition_condition);
									//รวมจำนวนทั้งหมดแยก			
									if($tmp_gender=="ชาย"){	
										$arr_content[$data_count][total_m] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];	//รวมชาย
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดชาย
									}elseif($tmp_gender=="หญิง"){	
										$arr_content[$data_count][total_f] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมหญิง
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดหญิง
									 }elseif($tmp_gender=="รวม"){		
										$arr_content[$data_count][total_s] = ($arr_content[$data_count][total_m]+$arr_content[$data_count][total_f]);		//รวมทั้งหมด ช+ญ
									}
	
									//รวมจำนวนทั้งหมดจากคอลัมน์  แถวแรกจนแถวสุดท้าย
									if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
								} // end for
							} // end for
						} // end for $k
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	$GRAND_TOTAL = array_sum($LEVEL_GRAND_TOTAL);
	$GRAND_TOTAL_M = array_sum($LEVEL_GRAND_TOTAL_M);
	$GRAND_TOTAL_F = array_sum($LEVEL_GRAND_TOTAL_F);
	$GRAND_TOTAL_S = ($GRAND_TOTAL_M+$GRAND_TOTAL_F);

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
		} // end if
		
		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

		print_header();

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		$wsdata_fontfmt_1[0] = "B";
		$wsdata_fontfmt_2[0] = "";
		$wsdata_align_1[0] = "L";
		$wsdata_border_1[0] = "TRBL";
		$wsdata_colmerge_1[0] = 0;
		$cnt = 0;
		for($k=0;$k<count($ARR_pt_name);$k++) {
			$count_position_type=count($ARR_POSITION_TYPE[$ARR_pt_name[$k]]);
			$cnt1 = 0;
			for($i=0;$i<$count_position_type;$i++) {
				$tmp_position_type=$ARR_POSITION_TYPE[$ARR_pt_name[$k]][$i];
				$count=count($ARR_GENDER[$ARR_pt_name[$k]][$i]);
				for($j=0;$j<$count;$j++) {
					$tmp_gender = $ARR_GENDER[$ARR_pt_name[$k]][$i][$j];
					$cnt++; $cnt1++;
					$wsdata_fontfmt_1[$cnt] = "B";
					$wsdata_fontfmt_2[$cnt] = "";
					$wsdata_align_1[$cnt] = "R";
					$wsdata_border_1[$cnt] = "TRBL";
					$wsdata_colmerge_1[$cnt] = 0;
				}
			}
		}
		$wsdata_fontfmt_1[$cnt+1] = "B";
		$wsdata_fontfmt_1[$cnt+2] = "B";
		$wsdata_fontfmt_1[$cnt+3] = "B";
		$wsdata_fontfmt_2[$cnt+1] = "";
		$wsdata_fontfmt_2[$cnt+2] = "";
		$wsdata_fontfmt_2[$cnt+3] = "";
		$wsdata_align_1[$cnt+1] = "R";
		$wsdata_align_1[$cnt+2] = "R";
		$wsdata_align_1[$cnt+3] = "R";
		$wsdata_border_1[$cnt+1] = "TRBL";
		$wsdata_border_1[$cnt+2] = "TRBL";
		$wsdata_border_1[$cnt+3] = "TRBL";
		$wsdata_colmerge_1[$cnt+1] = 0;
		$wsdata_colmerge_1[$cnt+2] = 0;
		$wsdata_colmerge_1[$cnt+3] = 0;
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			
			for($k=0;$k<count($ARR_pt_name); $k++){
				$pt_name = $ARR_pt_name[$k];
				$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
				for($i=0;$i<$count_position_type; $i++){
					$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
					$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
					for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
						$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
						//--set gender label
						if($tmp_gender=="ชาย"){				$label_gender="M"; }
						elseif($tmp_gender=="หญิง"){	$label_gender="F";  }
						elseif($tmp_gender=="รวม"){		$label_gender="S";  }
						//-----------------------------------------------------------------------------
						${"COUNT_".$tmp_level_no."_".$label_gender} = $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
					} //end for
				} //end for
			} // end for $k
			//$COUNT_TOTAL = $arr_content[$data_count][total];
			$COUNT_TOTAL_M = $arr_content[$data_count][total_m];
			$COUNT_TOTAL_F = $arr_content[$data_count][total_f];
			$COUNT_TOTAL_S= $arr_content[$data_count][total_s];

			$arr_data = (array) null;
			$arr_data[] ="$NAME";
			for($k=0;$k<count($ARR_pt_name); $k++){
				$pt_name = $ARR_pt_name[$k];
				$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
				for($i=0;$i<$count_position_type; $i++){
					$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
					$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
					for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
						$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
						//--set gender label
						if($tmp_gender=="ชาย"){	$label_gender="M"; }
						elseif($tmp_gender=="หญิง"){	$label_gender="F"; }
						elseif($tmp_gender=="รวม"){		$label_gender="S"; }
						//-----------------------------------------------------------------------------
						$arr_data[] = ${"COUNT_".$tmp_level_no."_".$label_gender};
					} //end for
				} //end for
			}	// end for $k
			$arr_data[] = $COUNT_TOTAL_M;
			$arr_data[] = $COUNT_TOTAL_F;
			$arr_data[] = $COUNT_TOTAL_S;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if($REPORT_ORDER=="MINISTRY" || $REPORT_ORDER=="DEPARTMENT" || (array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1))
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
		} // end for
		//$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$arr_data = (array) null;
		$arr_data[] ="รวม";
		for($k=0;$k<count($ARR_pt_name); $k++){
			$pt_name = $ARR_pt_name[$k];
			$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
			for($i=0;$i<$count_position_type; $i++){
				$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
				$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
				for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
					$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
					//--set gender label
					if($tmp_gender=="ชาย"){	$label_gender="M"; }
					elseif($tmp_gender=="หญิง"){	$label_gender="F"; }
					elseif($tmp_gender=="รวม"){		$label_gender="S"; }
					//-----------------------------------------------------------------------------
					$arr_data[] = $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender];
				} //end for
			} //end for
		}	// end for $k
		$arr_data[] = $GRAND_TOTAL_M;
		$arr_data[] = $GRAND_TOTAL_F;
		$arr_data[] = $GRAND_TOTAL_S;

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
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format_new("xlsFmtTitle", "fontFormat=B&alignment=&border=&isMerge=1&fgColor=&bgColor=&setRotation=0&valignment=&fontSize=&wrapText=1"));
		for($j=1; $j<=($count_column+3); $j++) $worksheet->write($xlsRow, $j, "", set_format_new("xlsFmtTitle", "fontFormat=B&alignment=&border=&isMerge=1&fgColor=&bgColor=&setRotation=0&valignment=&fontSize=&wrapText=1"));
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