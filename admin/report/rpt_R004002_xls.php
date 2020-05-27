<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004002_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
//die('>>>'.$have_cardno);
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$b_code = "b.PL_CODE";
		$f_name = "f.PL_NAME";
		$f_position = "POS_NO_NAME||POS_NO";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$b_code = "b.PN_CODE";
		$f_name = "f.PN_NAME";
		$f_position = "POEM_NO_NAME||POEM_NO";
		 $line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$b_code = "b.EP_CODE";
		$f_name = "f.EP_NAME";
		$f_position = "POEMS_NO_NAME||POEMS_NO";
		 $line_search_code=trim($search_ep_code);
		 $line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$b_code = "b.TP_CODE";
		$f_name = "f.TP_NAME";
		$f_position = "POT_NO_NAME||POT_NO";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if

	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure==1)  $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";
				else if($select_org_structure==1)  $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
		case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$b_code as PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= $b_code;

				$heading_name .= $line_title;
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2"; 	}
		else if($select_org_structure==1){	$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2"; }
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2"; } 
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_RETIREDATE), 1, 10) > '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(a.PER_RETIREDATE), 1, 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".($search_budget_year - 543)."-10-01')";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			} // end if
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($b_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
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
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$show_budget_year = (($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year);
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]ที่เกษียณอายุในสิ้นปีงบประมาณ $show_budget_year";
	$report_code = "R0402";

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
                /*เดิม*/
		/*$ws_head_line1 = array("$heading_name","ตำแหน่ง/ระดับตำแหน่ง","เลขที่","เงินเดือน", "วันเข้ารับราชการ", "วันเกิด", "<**6**>อายุราชการ", "<**6**>อายุราชการ", "<**6**>อายุราชการ");
		$ws_head_line2 = array("", "", "ตำแหน่ง", "", "", "", "ปกติ", "ทวีคูณ", "รวม");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,1,1,1);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TL","T","TR");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL","TRBL","TLBR","TRBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C");
		$ws_width = array(35,40,10,10,12,12,17,17,17);//$ws_width = array(35,18,40,10,10,12,12,17,17,17);*/
                /*Release 5.1.0.8 */
                if($have_cardno=='1'){
                    $ws_head_line1 = array("$heading_name","ตำแหน่ง/ระดับตำแหน่ง","เลขที่","เงินเดือน", "วันเข้ารับราชการ", "วันเกิด", "<**6**>อายุราชการ", "<**6**>อายุราชการ", "<**6**>อายุราชการ","เลขบัตรประชาชน");
                    $ws_head_line2 = array("", "", "ตำแหน่ง", "", "", "", "ปกติ", "ทวีคูณ", "รวม","");
                    $ws_colmerge_line1 = array(0,0,0,0,0,0,1,1,1,0);
                    $ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
                    $ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TL","T","TR","TLR");
                    $ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL","TRBL","TLBR","TRBL","RBL");
                    $ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
                    $ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
                    $ws_width = array(35,40,10,10,12,12,17,17,17);
                }else{
                    $ws_head_line1 = array("$heading_name","ตำแหน่ง/ระดับตำแหน่ง","เลขที่","เงินเดือน", "วันเข้ารับราชการ", "วันเกิด", "<**6**>อายุราชการ", "<**6**>อายุราชการ", "<**6**>อายุราชการ");
                    $ws_head_line2 = array("", "", "ตำแหน่ง", "", "", "", "ปกติ", "ทวีคูณ", "รวม");
                    $ws_colmerge_line1 = array(0,0,0,0,0,0,1,1,1);
                    $ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0);
                    $ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TL","T","TR");
                    $ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL","TRBL","TLBR","TRBL");
                    $ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B");
                    $ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C");
                    $ws_width = array(35,40,10,10,12,12,17,17,17);
                }
                /*Release 5.1.0.8 */
                
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
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2;
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

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3, $position_table, $position_join, $line_table, $line_join, $f_name, $f_position;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $search_budget_year,$DATE_DISPLAY;
		global $days_per_year, $days_per_month, $seconds_per_day,$select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $f_name as PL_NAME, a.LEVEL_NO, g.POSITION_LEVEL,
												a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, PER_CARDNO, b.ORG_ID_1,
												LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, $f_position as POS_NO
							 from		(
												(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
												) left join $line_table f on $line_join
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							 order by a.PER_NAME, a.PER_SURNAME ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $f_name as PL_NAME, a.LEVEL_NO, g.POSITION_LEVEL,
												a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, PER_CARDNO, b.ORG_ID_1,
												SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE, $f_position as POS_NO
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_ORG e, $line_table f, PER_LEVEL g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.PN_CODE=d.PN_CODE(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and $line_join(+) and a.LEVEL_NO=g.LEVEL_NO(+)
												$search_condition
							 order by a.PER_NAME, a.PER_SURNAME ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $f_name as PL_NAME, a.LEVEL_NO, g.POSITION_LEVEL,
												a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, PER_CARDNO, b.ORG_ID_1, 
												LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, $f_position as POS_NO
							 from		(
												(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
												) left join $line_table f on $line_join
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							 order by a.PER_NAME, a.PER_SURNAME ";
		} // end if

		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
                //die('<pre>'.$cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		while($data = $db_dpis2->get_array()){
			$data_count++;
			$person_count++;
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PL_NAME = trim($data[PL_NAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
			$PT_CODE = trim($data[PT_CODE]);
			$PT_NAME = trim($data[PT_NAME]);
			$PER_SALARY = trim($data[PER_SALARY]);
			$CARD_NO=trim($data[PER_CARDNO]);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
//			$WORK_DURATION = date_difference($data[PER_RETIREDATE], $data[PER_STARTDATE], "ymd");
			
                        /*เดิม*/
                        //$WORK_DURATION = date_difference((($search_budget_year - 543)."-10-01"), $data[PER_STARTDATE], "ymd");
                        $WORK_DURATION = date_difference((($search_budget_year - 543)."-09-30"), $data[PER_STARTDATE], "full");
			$POS_NO = trim($data[POS_NO]);
			$ORG_ID_1 = $data[ORG_ID_1];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$ORG_NAME_1 = $data3[ORG_NAME];
			
			$time_temp = 0;
			$cmd = " select 	a.TIME_CODE, b.TIME_DAY, a.TIMEH_MINUS
							 from 		PER_TIMEHIS a, PER_TIME b
							 where	a.TIME_CODE=b.TIME_CODE and a.PER_ID=$PER_ID
							 order by a.TIMEH_ID ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			while($data2 = $db_dpis3->get_array()){
				$TIME_DAY = $data2[TIME_DAY] + 0;
				$TIMEH_MINUS = $data2[TIMEH_MINUS] + 0;
				
				$time_temp += ($TIME_DAY - $TIMEH_MINUS);
			} // end while

			if($time_temp){
				$total_year = $total_month = $total_day = 0;
				
				if($time_temp > $days_per_month){
					$total_month = floor($time_temp / $days_per_month);
//					$total_day = floor((($time_temp / $days_per_month) - floor($time_temp / $days_per_month)) * $days_per_month);
					$total_day = floor(fmod($time_temp, $days_per_month));

					if($total_month >= 12){ 
						$total_year = floor($total_month / 12);
						$total_month = floor($total_month % 12);
					} // end if
				}else{
					$total_day = $time_temp;
				} // end if
				
				$TIME_DURATION = "$total_year ปี $total_month เดือน $total_day วัน";
				
				$time_temp = str_replace(" ปี", "", $TIME_DURATION);
				$time_temp = str_replace(" เดือน", "", $time_temp);
				$time_temp = str_replace(" วัน", "", $time_temp);

				list($year1, $month1, $date1) = split(" ", $time_temp, 3);
                                
                                /* ทำใหม่ */
                                if(strpos($WORK_DURATION," ปี") && strpos($WORK_DURATION," เดือน") && strpos($WORK_DURATION," วัน")){
                                    $work_temp = str_replace(" ปี", "", $WORK_DURATION);
                                    $work_temp = str_replace(" เดือน", "", $work_temp);
                                    $work_temp = str_replace(" วัน", "", $work_temp);
                                } else {
                                    if(strpos($WORK_DURATION," ปี") && !strpos($WORK_DURATION," เดือน") && !strpos($WORK_DURATION," วัน")){
                                        $work_temp = str_replace(" ปี", " 0 0", $WORK_DURATION);
                                        $work_temp = str_replace(" เดือน", "", $work_temp);
                                        $work_temp = str_replace(" วัน", "", $work_temp);
                                    }else if (strpos($WORK_DURATION," ปี") && strpos($WORK_DURATION," เดือน") && !strpos($WORK_DURATION," วัน")) {
                                        $work_temp = str_replace(" ปี", "", $WORK_DURATION);
                                        $work_temp = str_replace(" เดือน", " 0", $work_temp);
                                        $work_temp = str_replace(" วัน", "", $work_temp);
                                    }else if (strpos($WORK_DURATION," ปี") && !strpos($WORK_DURATION," เดือน") && strpos($WORK_DURATION," วัน")) {
                                        $work_temp = str_replace(" ปี", " 0", $WORK_DURATION);
                                        $work_temp = str_replace(" เดือน", "", $work_temp);
                                        $work_temp = str_replace(" วัน", "", $work_temp);
                                    }else if (!strpos($WORK_DURATION," ปี") && strpos($WORK_DURATION," เดือน") && !strpos($WORK_DURATION," วัน")) {
                                        $work_temp = str_replace(" ปี", "", $WORK_DURATION);
                                        $work_temp = "0 ".str_replace(" เดือน", " 0", $work_temp);
                                        $work_temp = str_replace(" วัน", "", $work_temp);
                                    }else if (!strpos($WORK_DURATION," ปี") && strpos($WORK_DURATION," เดือน") && strpos($WORK_DURATION," วัน")) {
                                        $work_temp = str_replace(" ปี", "", $WORK_DURATION);
                                        $work_temp = "0 ".str_replace(" เดือน", "", $work_temp);
                                        $work_temp = str_replace(" วัน", "", $work_temp);
                                    }else if (!strpos($WORK_DURATION," ปี") && !strpos($WORK_DURATION," เดือน") && strpos($WORK_DURATION," วัน")) {
                                        $work_temp = str_replace(" ปี", "", $WORK_DURATION);
                                        $work_temp = str_replace(" เดือน", "", $work_temp);
                                        $work_temp = "0 0 ".str_replace(" วัน", "", $work_temp);
                                    }else if (!strpos($WORK_DURATION," ปี") && !strpos($WORK_DURATION," เดือน") && !strpos($WORK_DURATION," วัน")) {
                                        $work_temp = str_replace(" ปี", "", $WORK_DURATION);
                                        $work_temp = str_replace(" เดือน", "", $work_temp);
                                        $work_temp = "0 0 0".str_replace(" วัน", "", $work_temp);
                                    }else {
                                        $work_temp = str_replace(" ปี", "", $WORK_DURATION);
                                        $work_temp = str_replace(" เดือน", "", $work_temp);
                                        $work_temp = str_replace(" วัน", "", $work_temp);
                                    }
                                }
                                /* เก่า $work_temp = str_replace(" ปี", "", $WORK_DURATION);
                                $work_temp = str_replace(" เดือน", "", $work_temp);
                                $work_temp = str_replace(" วัน", "", $work_temp);*/
                                
				list($year2, $month2, $date2) = split(" ", $work_temp, 3);
				
				$total_day = $date1 + $date2;
				$total_month = $month1 + $month2;
				$total_year = $year1 + $year2;
				
				if($total_day > $days_per_month){
					$total_month += floor($total_day / $days_per_month);
//					$total_day = floor((($total_day / $days_per_month) - floor($total_day / $days_per_month)) * $days_per_month);
					$total_day = floor(fmod($total_day, $days_per_month));
				} // end if
				
				if($total_month >= 12){ 
					$total_year += floor($total_month / 12);
					$total_month = floor($total_month % 12);
				} // end if

				$TOTAL_DURATION = "$total_year ปี $total_month เดือน $total_day วัน";
			}else{
				$TIME_DURATION = "-";
				$TOTAL_DURATION = $WORK_DURATION;
			} // end if
			
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $person_count .". ". $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			//$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") . "*Enter*" . $ORG_NAME_1;/*เดิม*/
                        /**/
                        $arr_content[$data_count][position] = $PL_NAME .$POSITION_LEVEL ;/*Release 5.1.0.4 */
			$arr_content[$data_count][pos_no] = $POS_NO;
			$arr_content[$data_count][salary] = $PER_SALARY;
			$arr_content[$data_count][startdate] = $PER_STARTDATE;	
			$arr_content[$data_count][birthdate] = $PER_BIRTHDATE;	
			$arr_content[$data_count][work_duration] = $WORK_DURATION;	
			$arr_content[$data_count][time_duration] = $TIME_DURATION;
			$arr_content[$data_count][total_duration] = $TOTAL_DURATION;
                        
                        $arr_content[$data_count][card_no] = $CARD_NO;
                        
			

//			$data_count++;
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($ORG_ID && $ORG_ID!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
					}
				break;
				case "ORG_1" :
					if($ORG_ID_1 && $ORG_ID_1!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
					}
				break;
				case "ORG_2" :
					if($ORG_ID_2 && $ORG_ID_2!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
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
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2;
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
				case "ORG_2" :
					$ORG_ID_2 = -1;
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
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		/*$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG e
						 where			$position_join and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) 
											$search_condition
						 order by		$order_by ";*/
                $cmd = " select distinct $select_list  
                        from PER_PERSONAL a
                        left join $position_table b ON($position_join)
                        left join PER_ORG C ON(b.ORG_ID=c.ORG_ID)
                        left join PER_ORG E ON(a.DEPARTMENT_ID=e.ORG_ID)
                        where 1=1 $search_condition 
                        order by $order_by ";                                 
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(
											(
												PER_PERSONAL a 
												left join $position_table b on ($position_join) 
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											$search_condition
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$person_count = 0;
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
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
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
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
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
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
							$ORG_NAME_1 = "[ไม่ระบุ$ORG_TITLE1]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
							$ORG_NAME_2 = "[ไม่ระบุ$ORG_TITLE2]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
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
                        /*เดิม*/
			/*$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","L","C","R","C","C","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","");*/
                        /*Release 5.1.0.8 */
                        if($have_cardno=='1'){
                            $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B");
                            $wsdata_align_1 = array("L","L","C","R","C","C","C","C","C","L");
                            $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
                            $wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0);
                            $wsdata_fontfmt_2 = array("","","","","","","","","","");
                        }else{
                            $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B");
                            $wsdata_align_1 = array("L","L","C","R","C","C","C","C","C");
                            $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
                            $wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0);
                            $wsdata_fontfmt_2 = array("","","","","","","","","");
                        }
                        /*Release 5.1.0.8 */
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][pos_no];
			$NAME_4 = $arr_content[$data_count][salary];
			$NAME_5 = $arr_content[$data_count][startdate];
			$NAME_6 = $arr_content[$data_count][birthdate];
			$COUNT_1 = $arr_content[$data_count][work_duration];
			$COUNT_2 = $arr_content[$data_count][time_duration];
			$COUNT_3 = $arr_content[$data_count][total_duration];
                        /*Release 5.1.0.8 */
                        if($have_cardno=='1'){
                            $NAME_7 = $arr_content[$data_count][card_no];
                        }
                        /*Release 5.1.0.8 */
//			$NAME_7 = $arr_content[$data_count][card_no];

			$arr_data = (array) null;
			$arr_data[] = $NAME_1;
//			$arr_data[] = $NAME_7;
			$arr_data[] = $NAME_2;
			$arr_data[] = $NAME_3;
			$arr_data[] = $NAME_4;
			$arr_data[] = $NAME_5;
			$arr_data[] = $NAME_6;
			$arr_data[] = $COUNT_1;
			$arr_data[] = $COUNT_2;
			$arr_data[] = $COUNT_3;
                         if($have_cardno=='1'){
                            $arr_data[] = $NAME_7;
                        }

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if($REPORT_ORDER == "ORG")
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
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
//		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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