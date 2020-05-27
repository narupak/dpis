<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R002006_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	for ($i=0;$i<count($EDU_TYPE);$i++) {
	if($search_edu) { $search_edu.= ' or '; }
	$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; } 
	
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_seq="f.PL_SEQ_NO";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_seq="f.PN_SEQ_NO";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_seq="f.EP_SEQ_NO";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_seq="f.TP_SEQ_NO";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if

//	include ("../report/rpt_condition2.php");
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
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "CO_LEVEL", "PROVINCE"); 

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
				elseif($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
                        case "GENDER" :
                                if($select_list) $select_list .= ", ";
				$select_list .= "a.PER_GENDER";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_GENDER";
                                
				$heading_name .= " เพศ";
				break;            
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				
				$select_list .= "$line_seq, $line_code as PL_CODE";
				
				$order_by .= "$line_seq, $line_code";
				
				$heading_name .=$line_title;
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.PV_CODE";

				$heading_name .= " $PV_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		elseif($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	} // end if
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		elseif($select_org_structure==1) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	} // end if

	$search_condition = "";
	
	$arr_search_condition[] = "(a.PER_TYPE=$search_per_type and a.PER_STATUS=1)"; 

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
			} 
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
			} 		
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				$arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		
		if($line_search_code){
				$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
				$list_type_text .= "$line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE_EDU) = '$search_ct_code')";
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
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]จำแนกตามวุฒิการศึกษา";
	$report_code = "R0206";

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
	$ws_head_line1 = array("$heading_name","<**1**>วุฒิอื่น ๆ","<**1**>วุฒิอื่น ๆ","<**2**>ต่ำกว่าป.ตรี","<**2**>ต่ำกว่าป.ตรี","<**3**>ป.ตรี","<**3**>ป.ตรี","<**4**>ป.โท","<**4**>ป.โท","<**5**>ป.เอก","<**5**>ป.เอก","<**6**>รวม","<**6**>รวม");
	$ws_head_line2 = array("","จำนวน","ร้อยละ","จำนวน","ร้อยละ","จำนวน","ร้อยละ","จำนวน","ร้อยละ","จำนวน","ร้อยละ","จำนวน","ร้อยละ");
	$ws_colmerge_line1 = array(0,1,1,1,1,1,1,1,1,1,1,1,1);
	$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
	$ws_border_line1 = array("TLR","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL");
	$ws_border_line2 = array("RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL");
	$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
	$ws_fontfmt_line2 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
	$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
	$ws_headalign_line2 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
	$ws_width = array(45,8,8,8,8,8,8,8,8,8,8,8,8);
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
		$colshow_cnt = $colseq;
		
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
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

	function count_person($education_level, $PER_GENDER, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type, $select_org_structure, $search_edu;
		
//		echo "addition :: $addition_condition<br>";
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($education_level) $search_condition .= (trim($search_condition)?" and ":" where ") . "(f.EL_TYPE='$education_level')";

		if($PER_GENDER){ 
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$search_condition = " where a.PER_GENDER=$PER_GENDER " . $search_condition;
		} // end if
		
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from	(
											(	
							 					(
													(
														PER_PERSONAL a 
														inner join $position_table b on $position_join
													) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
									) left join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
							 $search_condition  and ($search_edu)
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_ORG e, PER_EDUCLEVEL f
							 where		$position_join and a.DEPARTMENT_ID=c.ORG_ID
												and a.PER_ID=d.PER_ID and a.DEPARTMENT_ID=e.ORG_ID(+) and trim(d.EL_CODE)=trim(f.EL_CODE(+)) and ($search_edu)
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from	(
											(	
							 					(
													(
														PER_PERSONAL a 
														inner join $position_table b on $position_join
													) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
												) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
											) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
										) left join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
							 $search_condition  and ($search_edu)
							 group by	a.PER_ID ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo "cmd=$cmd  ($count_person)<br>";
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

	return $count_person;
	} // function

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID,$DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2;
				
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
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
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
				case "ORG_2" :
					if($select_org_structure==0){
						if($ORG_ID_2 && $ORG_ID_2!=-1) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
					}else if($select_org_structure==1){
						if($ORG_ID_2 && $ORG_ID_2!=-1) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
					}
				break;
			} // end switch case
		} // end for
		
//		echo "<pre>"; print_r($arr_addition_condition); echo "</pre>";

		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

	return $addition_condition;
	} // function  

	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID,$DEPARTMENT_ID,$ORG_ID, $ORG_ID_1, $ORG_ID_2;
		
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
                                case "GENDER" :
                                        $PER_GENDER = -1;
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
												(
													(
														PER_PERSONAL a 
														inner join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
										) left join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											$search_condition  and ($search_edu)
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_ORG e, PER_EDUCLEVEL f
						 where		$position_join and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID and trim(d.EL_CODE)=trim(f.EL_CODE(+))
											and a.PER_ID=d.PER_ID and ($search_edu)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(
											(
												(
													(
														PER_PERSONAL a 
														inner join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
										) left join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											$search_condition  and ($search_edu)
						 order by		$order_by ";
	}
	if($select_org_structure==1) {	
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$GRAND_TOTAL_9 = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = $GRAND_TOTAL_4 = 0;
	initialize_parameter(0);
	$first_order = 1;	// order ที  0 = COUNTRY ยังไม่ได้คำนวณ เริ่ม order ที่ 1 (MINISTRY) ก่อน
	while($data = $db_dpis->get_array()){
		//$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
 
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
				$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
				switch($REPORT_ORDER){
					case "MINISTRY" :
						if ($CTRL_TYPE < 3) {
							if($MINISTRY_ID != trim($data[MINISTRY_ID])){
								$MINISTRY_ID =  trim($data[MINISTRY_ID]);
								if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1){
									$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$MINISTRY_NAME = $data2[ORG_NAME];
									$MINISTRY_SHORT = $data2[ORG_SHORT];

									if ($f_all) {
										if ($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1) {
											$arr_content[$data_count][type] = "COUNTRY";
											//$rpt_order_start_index = 0;
											//$addition_condition = "";
										} else {
											$arr_content[$data_count][type] = "MINISTRY";
											//$rpt_order_start_index = 1;
											//$addition_condition = generate_condition(1);
										}
										
										$addition_condition = generate_condition($rpt_order_index);
									
										$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) .$MINISTRY_NAME;
										// ช + ญ
										$arr_content[$data_count][count_9] = count_person(9, 1, $search_condition, "") + count_person(9, 2, $search_condition, "");
										$arr_content[$data_count][count_1] = count_person(1, 1, $search_condition, "") + count_person(1, 2, $search_condition, "");
										$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, "") + count_person(2, 2, $search_condition, "");
										$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, "") + count_person(3, 2, $search_condition, ""); 
										$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, "") + count_person(4, 2, $search_condition, "");
										if($rpt_order_index==$first_order)	for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".(($i==5)?$i=9:$i)} += $arr_content[$data_count]["count_".$i];
									
										if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
										$data_count++;
									} // end if ($f_all)
								} //end if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1)
							} // end if
						} // end if
					break;
					
					case "DEPARTMENT" :
						if ($CTRL_TYPE < 5) {
							if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
								$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
								if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){
									$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$DEPARTMENT_NAME = $data2[ORG_NAME];
									$DEPARTMENT_SHORT = $data2[ORG_SHORT];
									
									$addition_condition = generate_condition($rpt_order_index);
									
									$arr_content[$data_count][type] = "DEPARTMENT";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) .$DEPARTMENT_NAME;
								
									// ช + ญ
									$arr_content[$data_count][count_9] = count_person(9, 1, $search_condition, $addition_condition) + count_person(9, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_1] = count_person(1, 1, $search_condition, $addition_condition) + count_person(1, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition) + count_person(2, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition) + count_person(3, 2, $search_condition, $addition_condition); 
									$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition) + count_person(4, 2, $search_condition, $addition_condition);
									if($rpt_order_index==$first_order)	for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".(($i==5)?$i=9:$i)} += $arr_content[$data_count]["count_".$i];
					
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
									$data_count++;
								} // end if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1)
							} // end if
						} // end if
					break;
					
					case "ORG" :
						if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
								 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
								$ORG_SHORT = $data2[ORG_SHORT];
								if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;

								 if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
									$addition_condition = generate_condition($rpt_order_index);
									
									$arr_content[$data_count][type] = "ORG";
									$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
									// ช + ญ
									$arr_content[$data_count][count_9] = count_person(9, 1, $search_condition, $addition_condition) + count_person(9, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_1] = count_person(1, 1, $search_condition, $addition_condition) + count_person(1, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition) + count_person(2, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition) + count_person(3, 2, $search_condition, $addition_condition); 
									$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition) + count_person(4, 2, $search_condition, $addition_condition);
									if($rpt_order_index==$first_order)	for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".(($i==5)?$i=9:$i)} += $arr_content[$data_count]["count_".$i];
									
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);	
									$data_count++;
								} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
							} // end if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1)
						} // end if
					break;
                                        case "GENDER" :
						if($PER_GENDER != trim($data[PER_GENDER])){
                                                    $PER_GENDER = trim($data[PER_GENDER]);
                                                    
                                                    if($PER_GENDER==1) $GENDER_NAME = "ชาย";
                                                    elseif($PER_GENDER==2) $GENDER_NAME = "หญิง";

                                                    $arr_content[$data_count][type] = "SEX";
                                                    $arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $GENDER_NAME;
                                                    $arr_content[$data_count][count_9] = count_person(9, $PER_GENDER, $search_condition, $addition_condition);
                                                    $arr_content[$data_count][count_1] = count_person(1, $PER_GENDER, $search_condition, $addition_condition);
                                                    $arr_content[$data_count][count_2] = count_person(2, $PER_GENDER, $search_condition, $addition_condition);
                                                    $arr_content[$data_count][count_3] = count_person(3, $PER_GENDER, $search_condition, $addition_condition);
                                                    $arr_content[$data_count][count_4] = count_person(4, $PER_GENDER, $search_condition, $addition_condition);
                                                    if($rpt_order_index==$first_order)	for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".(($i==5)?$i=9:$i)} += $arr_content[$data_count]["count_".$i];
									
                                                    if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
                                                    $data_count++;
                                                    
						} // end if
					break;
					case "ORG_1" :
						if($ORG_ID_1 != trim($data[ORG_ID_1])){
							$ORG_ID_1 = trim($data[ORG_ID_1]);
							$ORG_NAME_1 = $ORG_SHORT_1 = "ไม่ระบุ";
							if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_1 = $data2[ORG_NAME];
								$ORG_SHORT_1 = $data2[ORG_SHORT];
								if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
							} //end if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1)
							
								if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-")){
									$addition_condition = generate_condition($rpt_order_index);
									
									$arr_content[$data_count][type] = "ORG_1";
									$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
									// ช + ญ
									$arr_content[$data_count][count_9] = count_person(9, 1, $search_condition, $addition_condition) + count_person(9, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_1] = count_person(1, 1, $search_condition, $addition_condition) + count_person(1, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition) + count_person(2, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition) + count_person(3, 2, $search_condition, $addition_condition); 
									$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition) + count_person(4, 2, $search_condition, $addition_condition);
									if($rpt_order_index==$first_order)	for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".(($i==5)?$i=9:$i)} += $arr_content[$data_count]["count_".$i];
									
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);	
									$data_count++;

									$arr_content[$data_count][type] = "SEX";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . "ชาย";
									$arr_content[$data_count][count_9] = count_person(9, 1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_1] = count_person(1, 1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
									$data_count++;
									
									$arr_content[$data_count][type] = "SEX";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . "หญิง";
									$arr_content[$data_count][count_9] = count_person(9, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_1] = count_person(1, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = count_person(4, 2, $search_condition, $addition_condition);
									
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
									$data_count++;
								} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
						} // end if
					break;
			
					case "ORG_2" :
						if($ORG_ID_2 != trim($data[ORG_ID_2])){
							$ORG_ID_2 = trim($data[ORG_ID_2]);
							$ORG_NAME_2 = $ORG_SHORT_2 = "ไม่ระบุ";
							if($ORG_ID_2!= "" && $ORG_ID_2 != 0 && $ORG_ID_2 != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_2 = $data2[ORG_NAME];
								$ORG_SHORT_2 = $data2[ORG_SHORT];
								if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
							} // end if($ORG_ID_2!= "" && $ORG_ID_2 != 0 && $ORG_ID_2 != -1)
								
								 if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-")){
									$addition_condition = generate_condition($rpt_order_index);
									
									$arr_content[$data_count][type] = "ORG_2";
									$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
									// ช + ญ
									$arr_content[$data_count][count_9] = count_person(9, 1, $search_condition, $addition_condition) + count_person(9, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_1] = count_person(1, 1, $search_condition, $addition_condition) + count_person(1, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition) + count_person(2, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition) + count_person(3, 2, $search_condition, $addition_condition); 
									$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition) + count_person(4, 2, $search_condition, $addition_condition);
									if($rpt_order_index==$first_order)	for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".(($i==5)?$i=9:$i)} += $arr_content[$data_count]["count_".$i];
									
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);	
									$data_count++;

									$arr_content[$data_count][type] = "SEX";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . "ชาย";
									$arr_content[$data_count][count_9] = count_person(9, 1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_1] = count_person(1, 1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
									$data_count++;
									
									$arr_content[$data_count][type] = "SEX";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . "หญิง";
									$arr_content[$data_count][count_9] = count_person(9, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_1] = count_person(1, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, 2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = count_person(4, 2, $search_condition, $addition_condition);
									if($rpt_order_index==$first_order)	for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".(($i==5)?$i=9:$i)} += $arr_content[$data_count]["count_".$i];
									
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
									$data_count++;
								} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
						} // end if
					break;	
				} // end switch case
			} // end for
		}	//	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) 
	} // end while
	
	$arr_column_count_map = array("","9","","1","","2","","3","","4","","");
	
	$GRAND_TOTAL = 0;
	for($i=0; $i < count($heading_text); $i++) {
		if ($arr_column_count_map[$i])
			if ($arr_column_sel[$arr_column_map[$i]]==1) 
				$GRAND_TOTAL += ${"GRAND_TOTAL_".$arr_column_count_map[$i]};
	}

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
		$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
		$wsdata_align_1 = array("L","R","R","R","R","R","R","R","R","R","R","R","R");
		$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
		$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$wsdata_fontfmt_2 = array("","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_TOTAL = 0;
			for($i=0; $i < count($heading_text); $i++) {
				if ($arr_column_count_map[$i]) {
					${"COUNT_".$arr_column_count_map[$i]} = $arr_content[$data_count]["count_".$arr_column_count_map[$i]];
					if ($arr_column_sel[$arr_column_map[$i]]==1) 
						$COUNT_TOTAL += ${"COUNT_".$arr_column_count_map[$i]};
				}
			}
			
			$PERCENT_1 = $PERCENT_2 = $PERCENT_3 = $PERCENT_4 = $PERCENT_TOTAL = 0;
			if($COUNT_TOTAL){ 
				for($i=0; $i < count($heading_text); $i++) {
					if ($arr_column_count_map[$i]) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) 
							${"PERCENT_".$arr_column_count_map[$i]} = (${"COUNT_".$arr_column_count_map[$i]} / $COUNT_TOTAL) * 100;
					}
				}
			} // end if
			if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;
			$arr_data = (array) null;
			$arr_data[] = $NAME;
			$arr_data[] = $COUNT_9;
			$arr_data[] = $PERCENT_9;
			$arr_data[] = $COUNT_1;
			$arr_data[] = $PERCENT_1;
			$arr_data[] = $COUNT_2;
			$arr_data[] = $PERCENT_2;
			$arr_data[] = $COUNT_3;
			$arr_data[] = $PERCENT_3;
			$arr_data[] = $COUNT_4;
			$arr_data[] = $PERCENT_4;
			$arr_data[] = $COUNT_TOTAL;
			$arr_data[] = ($PERCENT_TOTAL?$PERCENT_TOTAL:"");

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if($REPORT_ORDER=="SEX")
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
		} // end for
				
		$PERCENT_TOTAL_9 = $PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = $PERCENT_TOTAL_4 = 0;
		if($GRAND_TOTAL){ 
			for($i=0; $i < count($heading_text); $i++) {
				if ($arr_column_count_map[$i]) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) 
						${"PERCENT_TOTAL_".$arr_column_count_map[$i]} = (${"GRAND_TOTAL_".$arr_column_count_map[$i]} / $GRAND_TOTAL) * 100;
				}
			}
		} // end if
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$arr_data = (array) null;

		$arr_data[] = "รวม";
		$arr_data[] = $GRAND_TOTAL_9;
		$arr_data[] = $PERCENT_TOTAL_9;
		$arr_data[] = $GRAND_TOTAL_1;
		$arr_data[] = $PERCENT_TOTAL_1;
		$arr_data[] = $GRAND_TOTAL_2;
		$arr_data[] = $PERCENT_TOTAL_2;
		$arr_data[] = $GRAND_TOTAL_3;
		$arr_data[] = $PERCENT_TOTAL_3;
		$arr_data[] = $GRAND_TOTAL_4;
		$arr_data[] = $PERCENT_TOTAL_4;
		$arr_data[] = $GRAND_TOTAL;
		$arr_data[] = ($PERCENT_TOTAL?$PERCENT_TOTAL:"");

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
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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