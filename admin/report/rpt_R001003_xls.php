<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R001003_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_select = "a.POS_ID";
		$position_join = "a.POS_ID=b.POS_ID"; 
		$position_no= "a.POS_NO";
		$position_no_name= "a.POS_NO_NAME";
		$position_status= "a.POS_STATUS";
		$line_table = "PER_LINE";
		$line_join = "a.PL_CODE=e.PL_CODE";
		$line_code = "a.PL_CODE";
		$line_sort = "e.PL_SEQ_NO";
		$line_name = "e.PL_NAME as PL_NAME, e.PL_SHORTNAME as PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title= " สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_select = "a.POEM_ID";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$position_no= "a.POEM_NO";
		$position_no_name= "a.POEM_NO_NAME";
		$position_status= "a.POEM_STATUS";
		$line_table = "PER_POS_NAME";
		$line_join = "a.PN_CODE=e.PN_CODE";
		$line_code = "a.PN_CODE";
		$line_sort = "e.PN_SEQ_NO";
		$line_name = "e.PN_NAME as PL_NAME";
		$position_level_name= "f.PG_NAME as CL_NAME";
		$line_level ="and e.PG_CODE = f.PG_CODE";
		$position_level = ", PER_POS_GROUP f";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_select = "a.POEMS_ID";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$position_no= "a.POEMS_NO";
		$position_no_name= "a.POEMS_NO_NAME";
		$position_level_name= "f.LEVEL_NAME as CL_NAME";
		$line_level ="and e.LEVEL_NO = f.LEVEL_NO";
		$position_level = ", PER_LEVEL f";
		$position_status= "a.POEM_STATUS";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "a.EP_CODE=e.EP_CODE";
		$line_code = "a.EP_CODE";
		$line_sort = "e.EP_SEQ_NO";
		$line_name = "e.EP_NAME as PL_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_select = "a.POT_ID";
		$position_join = "a.POT_ID=b.POT_ID";
		$position_no= "a.POT_NO";
		$position_no_name= "a.POT_NO_NAME";
		$position_status= "a.POT_STATUS";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "a.TP_CODE=e.TP_CODE";
		$line_code = "a.TP_CODE";
		$line_sort = "e.TP_SEQ_NO";
		$line_name = "e.TP_NAME as PL_NAME";
		$position_level_name= "e.TP_NAME as CL_NAME";
		$line_level ="";
		$position_level = "";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);
		$line_title= " ชื่อตำแหน่ง";
	} // end if	
	
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "CO_LEVEL", "PROVINCE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_ID as MINISTRY_ID";		//หรือ d.ORG_ID_REF ก็ได้
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "g.ORG_ID";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_ID as DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_SEQ_NO, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_SEQ_NO, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.CL_NAME, a.LEVEL_NO";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO";

				$heading_name .= " $CL_TITLE";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.PV_CODE";

				$heading_name .= " $PV_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		$order_by = "b.ORG_SEQ_NO, a.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		$select_list = "b.ORG_SEQ_NO, a.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "($position_status=1 and b.ORG_ACTIVE=1 and d.ORG_ACTIVE=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(b.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(b.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(b.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(b.OT_CODE)='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if				
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			} // end if
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			} // end if
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if(trim($line_search_code)){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= $line_search_name;
		} // end if
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
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	if($DPISDB=="odbc"){
		$search_condition .= (trim($search_condition)?" and ":" where ") . " $position_select not in ( select $position_select from $position_table a, PER_PERSONAL b 
												where $position_join and PER_TYPE=$search_per_type and b.PER_STATUS=1 and $position_status=1 ) ";
	}elseif($DPISDB=="oci8"){ 
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$search_condition .= " and $position_select not in ( select $position_select from $position_table a, PER_PERSONAL b 
													where $position_join and PER_TYPE=$search_per_type and b.PER_STATUS=1 and $position_status=1 ) ";
	}elseif($DPISDB=="mysql"){
		$search_condition .= (trim($search_condition)?" and ":" where ") . " $position_select not in ( select $position_select from $position_table a, PER_PERSONAL b 
												where $position_join and PER_TYPE=$search_per_type and b.PER_STATUS=1 and $position_status=1 ) ";
	} // end if
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อตำแหน่งว่าง";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0103";

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
	if($search_per_type == 2){
		$title_position_r1 ="หมวด";
		$title_position_r2 ="";
	}elseif($search_per_type == 3){
		$title_position_r1 ="กลุ่มงาน";
		$title_position_r2 ="";
	}elseif($search_per_type == 4){
		$title_position_r1 ="ชื่อตำแหน่ง";
		$title_position_r2 ="";
	}else{
		$title_position_r1 ="ช่วงระดับ";
		$title_position_r2 ="ตำแหน่ง";
	}

		$ws_head_line0 = array("","","","<**1**>","<**1**>","อัตรา","");

		$ws_head_line1 = array((($NUMBER_DISPLAY==2)?convert2thaidigit("$heading_name"):"$heading_name"),$title_position_r1,"เลขที่","<**1**>$PT_TITLE","<**1**>$PT_TITLE","เงินเดือน","วันที่");
		$ws_head_line2 = array("",$title_position_r2,"ตำแหน่ง",(($NUMBER_DISPLAY==2)?convert2thaidigit("$PT_TITLE1"):"$PT_TITLE1"),(($NUMBER_DISPLAY==2)?convert2thaidigit("$PT_TITLE2"):"$PT_TITLE2"),"ถือจ่าย","ตำแหน่งว่าง");
		$ws_colmerge_line0 = array(0,0,0,1,1,0,0);
		$ws_colmerge_line1 = array(0,0,0,1,1,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0);
		$ws_border_line0 = array("TLR","TLR","TLR","TL","TR","TLR","TLR");
		$ws_border_line1 = array("LR","LR","LR","LB","LB","LR","LR");
		$ws_border_line2 = array("RBL","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line0 = array("B","B","B","B","B","B","B");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B");
		$ws_fontfmt_line2 = array("B","B","B","B","B","B","B");
		$ws_headalign_line0 = array("C","C","C","C","C","C","C");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C");
		$ws_headalign_line2 = array("C","C","C","C","C","C","C");
		$ws_width = array(50, 30,10,25,25,10,15);
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
	
//	echo "COLUMN_FORMAT=$COLUMN_FORMAT<br>";

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name, $NUMBER_DISPLAY, $PT_TITLE, $PT_TITLE1, $PT_TITLE2;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line0, $ws_head_line1, $ws_head_line2, $ws_colmerge_line0, $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line0, $ws_border_line1, $ws_border_line2, $ws_fontfmt_line0, $ws_fontfmt_line1, $ws_fontfmt_line2;
		global $ws_headalign_line0, $ws_headalign_line1, $ws_headalign_line2, $ws_width;
		
		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		$colshow_cnt = $colseq;

		// loop พิมพ์ head บรรทัดที่ 0
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line0[$arr_column_map[$i]], $ws_border_line0[$arr_column_map[$i]], $ws_colmerge_line0[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line0[$arr_column_map[$i]], $ws_headalign_line0[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
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
				$worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line2[$arr_column_map[$i]], $ws_headalign_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		

	function list_position($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $tab_deduct, $rpt_order_index, $arr_content, $data_count, $position_count,$DATE_DISPLAY,$position_level_name, $line_level, $position_level;
		global $select_org_structure, $position_table, $position_no,$position_no_name, $line_table, $line_join, $line_code, $line_sort, $line_name, $search_per_type;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		// รายชื่อตำแหน่งว่าง
		if($search_per_type == 1 || $search_per_type == 5){
			if($DPISDB=="odbc"){
				$cmd = " select			$line_name, a.CL_NAME, $position_no as POS_NO, $position_no_name as POS_NO_NAME, h.PT_NAME, a.POS_SALARY, a.POS_VACANT_DATE, f.POSITION_TYPE
						   from	(	
										(
											(
												(
													(
														(
															$position_table a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
													) left join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
												) left join PER_TYPE h on (a.PT_CODE=h.PT_CODE)
											) inner join $line_table e on ($line_join)
										) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						   $search_condition
						   order by		$line_sort, $line_code, f.LEVEL_SEQ_NO desc, a.LEVEL_NO, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)) ";
			}elseif($DPISDB=="oci8"){
	//			$search_condition = str_replace(" where ", " and ", $search_condition);	
				$cmd = " select			$line_name, a.CL_NAME, $position_no as POS_NO, $position_no_name as POS_NO_NAME, h.PT_NAME, a.POS_SALARY, a.POS_VACANT_DATE, f.POSITION_TYPE
						   from			$position_table a, PER_ORG b, PER_CO_LEVEL c, PER_ORG d, PER_TYPE h, $line_table e, PER_LEVEL f, PER_ORG g
						   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME(+) and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID
											and a.PT_CODE=h.PT_CODE(+) and $line_join and a.LEVEL_NO=f.LEVEL_NO(+)  			
											$search_condition
						   order by	$line_sort, $line_code, f.LEVEL_SEQ_NO desc, a.LEVEL_NO, $position_no_name, to_number(replace($position_no,'-','')) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			$line_name, a.CL_NAME, $position_no as POS_NO, $position_no_name as POS_NO_NAME, h.PT_NAME, a.POS_SALARY, a.POS_VACANT_DATE, f.POSITION_TYPE
						   from	 (	
										(
											(
												(
													(
														(
															$position_table a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
													) left join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
												) left join PER_TYPE h on (a.PT_CODE=h.PT_CODE)
											) inner join $line_table e on ($line_join)
										) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						   $search_condition
						   order by		$line_sort, $line_code, f.LEVEL_SEQ_NO desc, a.LEVEL_NO, $position_no_name, $position_no ";
			} // end if
		} else {
			if($DPISDB=="odbc"){
				$cmd = " select			$line_name, $position_no as POS_NO, $position_no_name as POS_NO_NAME
						   from	(	
										(
											(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
										) inner join $line_table e on ($line_join)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						   $search_condition
						   order by		$line_sort, $line_code, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)) ";
			}elseif($DPISDB=="oci8"){
	//			$search_condition = str_replace(" where ", " and ", $search_condition);	
					$cmd = " select			$line_name, $position_no as POS_NO, $position_no_name as POS_NO_NAME,$position_level_name
					from			$position_table a, PER_ORG b, PER_ORG d, $line_table e, PER_ORG g $position_level
					where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID	and $line_join   $line_level			
									$search_condition
					order by	$line_sort, $line_code, $position_no_name, to_number(replace($position_no,'-','')) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			$line_name, $position_no as POS_NO, $position_no_name as POS_NO_NAME
						   from	 (	
										(
											(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
										) inner join $line_table e on ($line_join)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						   $search_condition
						   order by		$line_sort, $line_code, $position_no_name, $position_no ";
			} // end if
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
 		//echo "<pre>$cmd<br>";
		//die();
		while($data = $db_dpis2->get_array()){
			$position_count++;
//			$PL_NAME = trim($data[PL_SHORTNAME])?$data[PL_SHORTNAME]:$data[PL_NAME];
			$PL_NAME = trim($data[PL_NAME])?$data[PL_NAME]:$data[PL_SHORTNAME];
			$CL_NAME = trim($data[CL_NAME]);
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			if (substr($POS_NO_NAME,0,4)=="กปด.")
				$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
			else
				$POS_NO = $POS_NO_NAME.trim($data[POS_NO]);
			$PT_NAME = trim($data[PT_NAME]);
			$POSITION_TYPE = trim($data[POSITION_TYPE]); 
			$POS_SALARY = number_format($data[POS_SALARY]);
			$POS_VACANT_DATE = show_date_format($data[POS_VACANT_DATE],$DATE_DISPLAY);
			
			$arr_content[$data_count][type] = "CONTENT";
//			echo "rpt_order_index=$rpt_order_index - tab_deduct=$tab_deduct<br>";
			$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $tab_deduct+1) * 5)) . $position_count .". ". $PL_NAME;
			$arr_content[$data_count][cl_name] = $CL_NAME;
			$arr_content[$data_count][pos_no] = $POS_NO;
			$arr_content[$data_count][pt_name] = $PT_NAME;
			$arr_content[$data_count][pos_salary] = $POS_SALARY;
			$arr_content[$data_count][POS_VACANT_DATE] = $POS_VACANT_DATE;	
			$arr_content[$data_count][cmd_position_type] = $POSITION_TYPE;	
			
			$data_count++;			
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(g.ORG_ID = $MINISTRY_ID)";	//หรือ d.ORG_ID_REF ก็ได้
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
					else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
					else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
				break;
				case "ORG_2" :
					if($ORG_ID_2 && $ORG_ID_2!=-1) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
					else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
				break;
				case "CO_LEVEL" :
					if($CL_NAME) $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME')";
					else $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME' or a.CL_NAME is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE' or b.PV_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
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
				case "CO_LEVEL" :
					$CL_NAME = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($search_per_type == 1 || $search_per_type == 5){
		if($DPISDB=="odbc"){
			$cmd = " 	select		distinct $select_list
						from	(
										(
											(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						$search_condition
						order by	$order_by ";
		}elseif($DPISDB=="oci8"){
	//		$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " 	select		distinct $select_list
						from		$position_table a, PER_ORG b, PER_CO_LEVEL c, PER_ORG d, PER_ORG g, PER_LEVEL h
						where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME(+) and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID and a.LEVEL_NO=h.LEVEL_NO(+)
									$search_condition
						order by	$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " 	select		distinct $select_list
						from	(
										(
											(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						$search_condition
						order by	$order_by ";
		}
	} else {
		if($DPISDB=="odbc"){
			$cmd = " 	select		distinct $select_list
						from	(
										(
											$position_table a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						$search_condition
						order by	$order_by ";
		}elseif($DPISDB=="oci8"){
	//		$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " 	select		distinct $select_list
						from		$position_table a, PER_ORG b, PER_ORG d, PER_ORG g
						where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID
									$search_condition
						order by	$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " 	select		distinct $select_list
						from	(
										(
											$position_table a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						$search_condition
						order by	$order_by ";
		}
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$position_count = 0;
	initialize_parameter(0);
	$MINISTRY_ID = -1;		$DEPARTMENT_ID_INI = -1;
	if (in_array("COUNTRY", $arr_rpt_order)) $tab_deduct = 1;
	else  $tab_deduct = 0;	// เอาไว้ลบ กรณีเป็น country เพื่อแสดง tab หน้ารายการโดยไม่นับ COUNTRY จึงลบ 1
	while($data = $db_dpis->get_array()){
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
				$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
				switch($REPORT_ORDER){
					case "MINISTRY" :
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
//						echo "0....DEPARTMENT_ID=$DEPARTMENT_ID<br>";
						if($data[MINISTRY_ID] && $MINISTRY_ID != $data[MINISTRY_ID]){
							$MINISTRY_ID =  trim($data[MINISTRY_ID]);
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							$MINISTRY_SHORT = $data2[ORG_SHORT];

//							echo "..........MINISTRY_NAME=$MINISTRY_NAME (data_count=$data_count)<br>";
							$arr_content[$data_count][type] = "MINISTRY";
//							$rpt_order_start_index = 0;
							$addition_condition = "";

//							$rpt_order_index = $rpt_order_start_index;
//							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "MINISTRY";
//							echo "MINISTRY-($MINISTRY_NAME)..rpt_order_index ($rpt_order_index)-rpt_order_start_index ($rpt_order_start_index)<br>";
//							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$rpt_order_start_index) * 5)) . $MINISTRY_NAME;
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$tab_deduct) * 5)) . $MINISTRY_NAME;
							$arr_content[$data_count][cl_name] = "";
							$arr_content[$data_count][pos_no] = "";
							$arr_content[$data_count][pt_name] = "";
							$arr_content[$data_count][pos_salary] = "";
							$arr_content[$data_count][POS_VACANT_DATE] = "";
							$arr_content[$data_count][cmd_position_type] = "";
				
//							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
							$data_count++;
						} // end if\
					break;

					case "DEPARTMENT" :
						if($DEPARTMENT_ID && $DEPARTMENT_ID_INI != $DEPARTMENT_ID){
							$DEPARTMENT_ID_INI = trim($DEPARTMENT_ID);
							if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
								$addition_condition = generate_condition($rpt_order_index);
				
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$DEPARTMENT_NAME = $data2[ORG_NAME];
								$DEPARTMENT_SHORT = $data2[ORG_SHORT];
				
//								echo "..........DEPARTMENT_NAME=$DEPARTMENT_NAME ($cmd) (data_count=$data_count)<br>";
								$addition_condition = generate_condition($rpt_order_index);
					
								$arr_content[$data_count][type] = "DEPARTMENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$tab_deduct) * 5)) . $DEPARTMENT_NAME;
								$arr_content[$data_count][cl_name] = "";
								$arr_content[$data_count][pos_no] = "";
								$arr_content[$data_count][pt_name] = "";
								$arr_content[$data_count][pos_salary] = "";
								$arr_content[$data_count][POS_VACANT_DATE] = "";
								$arr_content[$data_count][cmd_position_type] = "";
					
								$data_count++;
							}
						} // end if
					break;

					case "ORG" :
						if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != ""){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
								if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
//								echo "..........ORG_NAME=$ORG_NAME<br>";
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
	
							$arr_content[$data_count][type] = "ORG";
//							echo "ORG-($ORG_NAME)..rpt_order_index ($rpt_order_index)-rpt_order_start_index ($rpt_order_start_index)<br>";
//							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$rpt_order_start_index) * 5)) . $ORG_NAME;
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$tab_deduct) * 5)) . $ORG_NAME;
							$arr_content[$data_count][cl_name] = "";
							$arr_content[$data_count][pos_no] = "";
							$arr_content[$data_count][pt_name] = "";
							$arr_content[$data_count][pos_salary] = "";
							$arr_content[$data_count][POS_VACANT_DATE] = "";
							$arr_content[$data_count][cmd_position_type] = "";
				
							$data_count++;
	
							if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
						} // end if
					break;
	
					case "ORG_1" :
						if($ORG_ID_1 != trim($data[ORG_ID_1])){
							$ORG_ID_1 = trim($data[ORG_ID_1]);
							$ORG_NAME_1 = $ORG_SHORT_1 = "ไม่ระบุ";
							if($ORG_ID_1 != ""){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_1 = $data2[ORG_NAME];
								if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "ORG_1";
//							echo "ORG_1-($ORG_NAME_1)..rpt_order_index ($rpt_order_index)-rpt_order_start_index ($rpt_order_start_index)<br>";
//							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$rpt_order_start_index) * 5)) . $ORG_NAME_1;
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$tab_deduct) * 5)) . $ORG_NAME_1;
							$arr_content[$data_count][cl_name] = "";
							$arr_content[$data_count][pos_no] = "";
							$arr_content[$data_count][pt_name] = "";
							$arr_content[$data_count][pos_salary] = "";
							$arr_content[$data_count][POS_VACANT_DATE] = "";
							$arr_content[$data_count][cmd_position_type] = "";
							
//							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
	
							if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
						} // end if
					break;
			
					case "ORG_2" :
						if($ORG_ID_2 != trim($data[ORG_ID_2])){
							$ORG_ID_2 = trim($data[ORG_ID_2]);
							$ORG_NAME_2 = $ORG_SHORT_2 = "ไม่ระบุ";
							if($ORG_ID_2 != ""){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_2 = $data2[ORG_NAME];
								if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "ORG_2";
//							echo "ORG_2-($ORG_NAME_2)..rpt_order_index ($rpt_order_index)-rpt_order_start_index ($rpt_order_start_index)<br>";
//							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$rpt_order_start_index) * 5)) . $ORG_NAME_2;
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$tab_deduct) * 5)) . $ORG_NAME_2;
							$arr_content[$data_count][cl_name] = "";
							$arr_content[$data_count][pos_no] = "";
							$arr_content[$data_count][pt_name] = "";
							$arr_content[$data_count][pos_salary] = "";
							$arr_content[$data_count][POS_VACANT_DATE] = "";
							$arr_content[$data_count][cmd_position_type] = "";				
				
//							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
	
							if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
						} // end if
					break;
			
					case "CO_LEVEL" :
						if($CL_NAME != trim($data[CL_NAME])){
							$CL_NAME = trim($data[CL_NAME]);
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "CO_LEVEL";
//							echo "CO_LEVEL-($CL_NAME)..rpt_order_index ($rpt_order_index)-rpt_order_start_index ($rpt_order_start_index)<br>";
//							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$rpt_order_start_index) * 5)) . (trim($CL_NAME)?"ระดับ $CL_NAME":"[ไม่ระบุช่วงระดับตำแหน่ง]");
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$tab_deduct) * 5)) . (trim($CL_NAME)?"ระดับ $CL_NAME":"[ไม่ระบุช่วงระดับตำแหน่ง]");
							$arr_content[$data_count][cl_name] = "";
							$arr_content[$data_count][pos_no] = "";
							$arr_content[$data_count][pt_name] = "";
							$arr_content[$data_count][pos_salary] = "";
							$arr_content[$data_count][POS_VACANT_DATE] = "";
							$arr_content[$data_count][cmd_position_type] = "";				
				
//							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
	
							if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
						} // end if
					break;
			
					case "PROVINCE" :
						if($PV_CODE != trim($data[PV_CODE])){
							$PV_CODE = trim($data[PV_CODE]);
							if($PV_CODE != ""){
								$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PV_NAME = $data2[PV_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "PROVINCE";
//							echo "PV-($PV_NAME)..rpt_order_index ($rpt_order_index)-rpt_order_start_index ($rpt_order_start_index)<br>";
//							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$rpt_order_start_index) * 5)) . $PV_NAME;
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$tab_deduct) * 5)) . $PV_NAME;
							$arr_content[$data_count][cl_name] = "";
							$arr_content[$data_count][pos_no] = "";
							$arr_content[$data_count][pt_name] = "";
							$arr_content[$data_count][pos_salary] = "";
							$arr_content[$data_count][POS_VACANT_DATE] = "";
							$arr_content[$data_count][cmd_position_type] = "";			
				
//							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
	
							if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
						} // end if
					break;
				} // end switch case
			} // end for
		} // end if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1))
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B");
		$wsdata_align_1 = array("L","L","C","C","C","R","C");
		$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
		$wsdata_colmerge_1 = array(0,0,0,0,0,0,0);
		$wsdata_fontfmt_2 = array("","","","","","","");
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

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
		} // end if

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$CL_NAME = $arr_content[$data_count][cl_name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$POS_SALARY = $arr_content[$data_count][pos_salary];
			$POS_VACANT_DATE = $arr_content[$data_count][POS_VACANT_DATE];
			$CMD_POSITION_TYPE=$arr_content[$data_count][cmd_position_type];
			
			$arr_data = (array) null;
			$arr_data[] = $NAME;
			$arr_data[] = $CL_NAME;
			$arr_data[] = $POS_NO;
			$arr_data[] = $PT_NAME;
			$arr_data[] = $CMD_POSITION_TYPE;
			$arr_data[] = $POS_SALARY;
			$arr_data[] = $POS_VACANT_DATE;

//.			echo "column_function=".implode(",",$column_function)."<br>";
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if($REPORT_ORDER != "CONTENT")	// ถ้าไม่ใช่ content อักษรตัวหนา
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
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