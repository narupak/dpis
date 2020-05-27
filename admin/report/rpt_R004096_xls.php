<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004096_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("POS_NO", "NAME", "LINE", "LEVEL", "ORG", "ORG_1", "ORG_2"); 
	$order_by = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POS_NO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))";
				elseif($DPISDB=="oci8") $order_by .= "to_number(replace(b.POS_NO,'-',''))";
				elseif($DPISDB=="mysql") $order_by .= "b.POS_NO+0";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="oci8") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="mysql") $order_by .= "a.LEVEL_NO desc";
				break;
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

    if($search_budget_year){
    $search_year = ($search_budget_year-543);
  }
    $search_year      = $search_budget_year - 543; 
	$budget_year_from = $search_year - 1; 
	$budget_year_from = $budget_year_from.'-10-01'; 
	$budget_year_to   = $search_year.'-09-30';

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type) and (a.PER_STATUS = 1) and (b.POS_ID >= 0)";
    if($search_budget_year && $select_year==1){
	$arr_search_condition[] = "(substr(k.ACTH_EFFECTIVEDATE,1,10) > '$budget_year_from') and (substr(k.ACTH_EFFECTIVEDATE,1,10) <= '$budget_year_to')";	
    }
	
	$list_type_text = $ALL_REPORT_TITLE;
/*	if($select_org_structure==0){ $list_type_text .= " โครงสร้างตามกฏหมาย"; 	}
	elseif($select_org_structure==1){ $list_type_text .= " โครงสร้างตามมอบหมายงาน"; }  */		
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
		$arr_search_condition[] = "(d.OT_CODE='01')";
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
		$arr_search_condition[] = "(d.OT_CODE='02')";
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
		$arr_search_condition[] = "(d.OT_CODE='03')";
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
		$arr_search_condition[] = "(d.OT_CODE='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
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
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if(trim($search_pl_code) && trim($search_pn_code)  && trim($search_ep_code) ){ 
			$search_pl_code = trim($search_pl_code);
			$search_pn_code = trim($search_pn_code);
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code'))";
			$list_type_text .= "$search_pl_name, $search_pn_name,$search_ep_code";
		}elseif(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code'))";
			$list_type_text .= "$search_pl_name";
		}elseif(trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%'))";
			$list_type_text .= "$search_pn_name";
		}elseif(trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%'))";
			$list_type_text .= "$search_ep_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$search_pv_code')";
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
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	
	if ($search_date != "" && $select_year==0){
		$arr_temp = explode("/", $search_date);
		$search_date = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];

		//ค้นหาสำหรับคำสั่งช่วยราชการที่ยังไม่ถึงวันสิ้นสุด ณ วันที่ระบุ
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(k.ACTH_ENDDATE), 10) >= '$search_date' or k.ACTH_ENDDATE is null) and LEFT(trim(k.ACTH_EFFECTIVEDATE), 10) <= '$search_date' ";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(k.ACTH_ENDDATE), 1, 10) >= '$search_date' or k.ACTH_ENDDATE is null) and SUBSTR(trim(k.ACTH_EFFECTIVEDATE), 1, 10) <= '$search_date' ";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(k.ACTH_ENDDATE), 10) >= '$search_date' or k.ACTH_ENDDATE is null) and LEFT(trim(k.ACTH_EFFECTIVEDATE), 10)<= '$search_date' ";
	}

	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
    $report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]  ตำแหน่ง สังกัด ที่ไปช่วยราชการ";
	if ($search_date != "") $report_title .= "||(ประมวลผลข้อมูล ณ วันที่ $show_date)";
    $report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0496";

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
		$ws_head_line1 = array("ลำดับที่","เลขที่","ชื่อ","สกุล","<**1**>ข้อมูลตำแหน่งที่ได้รับแต่งตั้ง","<**1**>ข้อมูลตำแหน่งที่ได้รับแต่งตั้ง","<**1**>ข้อมูลตำแหน่งที่ได้รับแต่งตั้ง","<**1**>ข้อมูลตำแหน่งที่ได้รับแต่งตั้ง","<**1**>ข้อมูลตำแหน่งที่ได้รับแต่งตั้ง","<**1**>ข้อมูลตำแหน่งที่ได้รับแต่งตั้ง","<**1**>ข้อมูลตำแหน่งที่ได้รับแต่งตั้ง","<**1**>ข้อมูลตำแหน่งที่ได้รับแต่งตั้ง","<**2**>ข้อมูลการไปช่วยราชการของข้าราชการ","<**2**>ข้อมูลการไปช่วยราชการของข้าราชการ","<**2**>ข้อมูลการไปช่วยราชการของข้าราชการ","<**2**>ข้อมูลการไปช่วยราชการของข้าราชการ","<**2**>ข้อมูลการไปช่วยราชการของข้าราชการ","<**2**>ข้อมูลการไปช่วยราชการของข้าราชการ","<**2**>ข้อมูลการไปช่วยราชการของข้าราชการ","<**2**>ข้อมูลการไปช่วยราชการของข้าราชการ","<**2**>ข้อมูลการไปช่วยราชการของข้าราชการ");
		$ws_head_line2 = array("","ตำแหน่ง","","","ตำแหน่ง","ประเภท","ระดับ","เงินเดือน","กลุ่ม/ฝ่าย/สำนัก","จังหวัด","สังกัด","$DEPARTMENT_TITLE","ตำแหน่ง","ประเภท","ระดับ","กลุ่ม/ฝ่าย/สำนัก","จังหวัด","สังกัด","$DEPARTMENT_TITLE","กรมเดียวกัน/ต่างกรม","ตั้งแต่วันที่","ถึงวันที่","เลขที่คำสั่ง");
		$ws_colmerge_line1 = array(0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","T","TR");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(6,7,15,13,22,10,12,7,25,12,10,18,22,10,12,25,12,10,18,15,10,15,10);
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

	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_CHANGE_DATE, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, d.ORG_NAME, a.PER_CARDNO, a.PER_BIRTHDATE, k.ACTH_PL_NAME_ASSIGN,k.acth_org2_AssIGN, k.ACTH_ORG3_ASSIGN, 
										p.LEVEL_NAME AS LEVEL_NAME_ASSIGN, k.ACTH_EFFECTIVEDATE, k.ACTH_ENDDATE 
						 from	(
										(
											(
												(
													( 	
														(
															(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_ORG c on (d.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
											) left join PER_PROVINCE i on (d.PV_CODE=i.PV_CODE)											
										) left join PER_ORG_TYPE j on (d.OT_CODE=j.OT_CODE)
									) left join PER_ACTINGHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_LEVEL p on (k.LEVEL_NO_ASSIGN=p.LEVEL_NO)
								$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_CHANGE_DATE, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, d.ORG_NAME, a.PER_CARDNO, a.PER_BIRTHDATE, k.ACTH_PL_NAME_ASSIGN,k.acth_org2_AssIGN, k.ACTH_ORG3_ASSIGN, 
										p.LEVEL_NAME AS LEVEL_NAME_ASSIGN, k.ACTH_EFFECTIVEDATE, k.ACTH_ENDDATE, k.ACTH_DOCNO  
										from PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d , PER_LEVEL h, PER_PROVINCE i, PER_ORG_TYPE j, PER_ACTINGHIS k, PER_LEVEL p 
										where a.POS_ID=b.POS_ID (+) and b.ORG_ID=d.ORG_ID (+) and a.LEVEL_NO=h.LEVEL_NO (+) and d.PV_CODE=i.PV_CODE (+) 
										and d.OT_CODE=j.OT_CODE (+) and a.PER_ID=k.PER_ID (+) and d.department_id=c.org_id (+) and k.LEVEL_NO_ASSIGN=p.LEVEL_NO
										$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_CHANGE_DATE, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, d.ORG_NAME, a.PER_CARDNO, a.PER_BIRTHDATE, k.ACTH_PL_NAME_ASSIGN,k.acth_org2_AssIGN, k.ACTH_ORG3_ASSIGN, 
										p.LEVEL_NAME AS LEVEL_NAME_ASSIGN, k.ACTH_EFFECTIVEDATE, k.ACTH_ENDDATE 
						 from	(
										(
											(
												(
													( 	
														(
															(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_ORG c on (d.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
											) left join PER_PROVINCE i on (d.PV_CODE=i.PV_CODE)											
										) left join PER_ORG_TYPE j on (d.OT_CODE=j.OT_CODE)
									) left join PER_ACTINGHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_LEVEL p on (k.LEVEL_NO_ASSIGN=p.LEVEL_NO)
								$search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("R","C","L","L","L","L","L","R","L","C","L","L","L","L","L","L","C","L","L","L","R","R","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];

			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", $PER_BIRTHDATE);
				$PER_BIRTHDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			}
			
			$POS_NO = $data[POS_NO];
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE) = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$NAME = ($PN_NAME)."$PER_NAME";
			$SURNAME = trim($data[PER_SURNAME]);

			$ORG_ID = $data[ORG_ID];

			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);

			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);

			$PL_NAME = trim($PL_NAME) . " " . $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"");
			$PL_NAME_ASSIGN =trim($data[ACTH_PL_NAME_ASSIGN]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$level=trim($data[LEVEL_NAME]);
			$LEVEL_NAME1 = substr($level,strpos($level,"ประเภท")+6);
			$LEVEL_NAME1 = substr($LEVEL_NAME1,0,strlen($LEVEL_NAME1)-strlen(substr($LEVEL_NAME1,strpos($LEVEL_NAME1,"ระดับ")-1)));
			$LEVEL_NAME2 = substr($level,strpos($level,"ระดับ")+5);
			
			$level=trim($data[LEVEL_NAME_ASSIGN]);
			$LEVEL_NAME1_ASSIGN = substr($level,strpos($level,"ประเภท")+6);
			$LEVEL_NAME1_ASSIGN = substr($LEVEL_NAME1_ASSIGN, 0, strlen($LEVEL_NAME1_ASSIGN) - strlen(substr($LEVEL_NAME1_ASSIGN, strpos($LEVEL_NAME1_ASSIGN, "ระดับ") - 1)));
			$LEVEL_NAME2_ASSIGN = substr($level,strpos($level,"ระดับ")+5);

			$PER_SALARY = $data[PER_SALARY];

			$ORG_NAME = $data[ORG_NAME];
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			$ORG_NAME_ASSIGN = $data[ACTH_ORG3_ASSIGN];
			$DEPARTMENT_NAME_ASSIGN = $data[ACTH_ORG2_ASSIGN];

			$PV_NAME=trim($data[PV_NAME]);
			$cmd = " SELECT PV_NAME FROM PER_PROVINCE a, PER_ORG b WHERE a.PV_CODE=b.PV_CODE and b.ORG_NAME='$ORG_NAME_ASSIGN' and b.DEPARTMENT_ID = (SELECT ORG_ID FROM PER_ORG WHERE ORG_NAME='$DEPARTMENT_NAME_ASSIGN')";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_NAME_ASSIGN=trim($data[PV_NAME]);

			$OT_NAME=trim($data[OT_NAME]);
			$cmd = " SELECT OT_NAME FROM PER_ORG_TYPE a, PER_ORG b WHERE a.OT_CODE=b.OT_CODE and b.ORG_NAME='$ORG_NAME_ASSIGN' and b.DEPARTMENT_ID = (SELECT ORG_ID FROM PER_ORG WHERE ORG_NAME='$DEPARTMENT_NAME_ASSIGN')";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OT_NAME_ASSIGN=trim($data[OT_NAME]);

			if ($DEPARTMENT_NAME == $DEPARTMENT_NAME_ASSIGN) $DEPARTMENT_DIFF="กรมเดียวกัน";
			else  $DEPARTMENT_DIFF="ต่างกรม";

			$ACTH_EFFECTIVEDATE = trim($data[ACTH_EFFECTIVEDATE]);
			if($ACTH_EFFECTIVEDATE){
				$arr_temp = explode("-", substr($ACTH_EFFECTIVEDATE, 0, 10));
				$ACTH_EFFECTIVEDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if
            
            $ACTH_ENDDATE = trim($data[ACTH_ENDDATE]);            
            if($ACTH_ENDDATE){
				$arr_temp = explode("-", substr($ACTH_ENDDATE, 0, 10));
				$ACTH_ENDDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if
            $ACTH_ENDDATE = ($ACTH_ENDDATE)?$ACTH_ENDDATE:"-";
            $ACTH_DOCNO   = trim($data[ACTH_DOCNO]);

			$arr_data = (array) null;
			$arr_data[] = $data_count;	
			$arr_data[] = $POS_NO;	
			$arr_data[] = $NAME;					
			$arr_data[] = $SURNAME;			
			$arr_data[] = $PL_NAME;			
			$arr_data[] = $LEVEL_NAME1;
			$arr_data[] = $LEVEL_NAME2;
			$arr_data[] = $PER_SALARY;
			$arr_data[] = $ORG_NAME;
			$arr_data[] = $PV_NAME;
			$arr_data[] = $OT_NAME;
			$arr_data[] = $DEPARTMENT_NAME;
			$arr_data[] = $PL_NAME_ASSIGN;
			$arr_data[] = $LEVEL_NAME1_ASSIGN;
			$arr_data[] = $LEVEL_NAME2_ASSIGN;
			$arr_data[] = $ORG_NAME_ASSIGN;
			$arr_data[] = $PV_NAME_ASSIGN;
			$arr_data[] = $OT_NAME_ASSIGN;
			$arr_data[] = $DEPARTMENT_NAME_ASSIGN;
			$arr_data[] = $DEPARTMENT_DIFF;
			$arr_data[] = $ACTH_EFFECTIVEDATE;
            $arr_data[] = $ACTH_ENDDATE;
            $arr_data[] = $ACTH_DOCNO;
            
                
	
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
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
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