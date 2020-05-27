<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R001001_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_select = "a.POS_ID";
		$position_join = "a.POS_ID=d.POS_ID";
		$position_status = "a.POS_STATUS";
		$position2 = "a.POS_STATUS=1";
		$line_table = "PER_LINE";
		$line_join = "a.PL_CODE=f.PL_CODE";
		$line_code = "a.PL_CODE";
		$line_name = "PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_seq="f.PL_SEQ_NO";		
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title= " สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_select = "a.POEM_ID";
		$position_join = "a.POEM_ID=d.POEM_ID";
		$position_status = "a.POEM_STATUS";
		$position2 = "a.POEM_STATUS=1";
		$line_table = "PER_POS_NAME";
		$line_join = "a.PN_CODE=f.PN_CODE";
		$line_code = "a.PN_CODE";
		$line_name = "PN_NAME";	
		$line_seq="f.PN_SEQ_NO";
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_select = "a.POEMS_ID";
		$position_join = "a.POEMS_ID=d.POEMS_ID";
		$position_status = "a.POEM_STATUS";
		$position2 = "a.POEM_STATUS=1";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "a.EP_CODE=f.EP_CODE";
		$line_code = "a.EP_CODE";
		$line_name = "EP_NAME";	
		$line_seq="f.EP_SEQ_NO";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_select = "a.POT_ID";
		$position_join = "a.POT_ID=d.POT_ID";
		$position_status = "a.POT_STATUS";
		$position2 = "a.POT_STATUS=1";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "a.TP_CODE=f.TP_CODE";
		$line_code = "a.TP_CODE";
		$line_name = "TP_NAME";	
		$line_seq="f.TP_SEQ_NO";
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

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, g.ORG_ID as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, g.ORG_ID";

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
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";

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
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_seq, $line_code as PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "$line_seq, $line_code";

				$heading_name .= " $line_title";
				break;
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.CL_NAME, a.LEVEL_NO";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.CL_NAME, a.LEVEL_NO";

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
		$order_by = "g.ORG_SEQ_NO, g.ORG_CODE, g.ORG_ID, c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID, b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	} // end if
	if(!trim($select_list)){ 
		$select_list = "g.ORG_SEQ_NO, g.ORG_CODE, g.ORG_ID as MINISTRY_ID, c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID, b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "($position_status=1 and b.ORG_ACTIVE=1 and c.ORG_ACTIVE=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
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

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
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

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(b.OT_CODE)='03')";

		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(b.OT_CODE)='04')";

		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
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

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : โครงสร้างตามกฎหมาย - $list_type_text";
	$report_title = "จำนวนตำแหน่งจำแนกตามสถานภาพและลักษณะของตำแหน่ง";
	$report_code = "R0101";

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
	if ($BKK_FLAG==1) 
		$ws_head_line1 = array("$heading_name","<**1**>ถือครอง","<**1**>ถือครอง","<**2**>ว่าง","<**2**>ว่าง","<**3**>กรอบอัตรากำลัง","<**3**>กรอบอัตรากำลัง");
	else 
		$ws_head_line1 = array("$heading_name","<**1**>ถือครอง","<**1**>ถือครอง","<**2**>ว่าง","<**2**>ว่าง","<**3**>รวม","<**3**>รวม");
	$ws_head_line2 = array("","ตำแหน่ง","ร้อยละ","ตำแหน่ง","ร้อยละ","ตำแหน่ง","ร้อยละ");
	$ws_colmerge_line1 = array(0,1,1,1,1,1,1);
	$ws_colmerge_line2 = array(0,0,0,0,0,0,0);
	$ws_border_line1 = array("TLR","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL");
	$ws_border_line2 = array("RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL");
	$ws_fontfmt_line1 = array("B","B","B","B","B","B","B");
	$ws_fontfmt_line2 = array("B","B","B","B","B","B","B");
	$ws_headalign_line1 = array("C","C","C","C","C","C","C");
	$ws_headalign_line2 = array("C","C","C","C","C","C","C");
	$ws_width = array(80,10,10,10,10,10,10,10,10);
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

	function count_position($position_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $DEPARTMENT_ID;
		global $search_per_type, $position_table, $position_select, $position_join, $position_status, $position2;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($position_type == 1){
			// ตำแหน่งมีผู้ถือครอง		
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if($DPISDB=="odbc"){
				$cmd = " select			count($position_select) as count_position
						   from		(
											(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
											) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
										) inner join PER_PERSONAL d on ($position_join)
						   where		d.PER_STATUS=1 and d.PER_TYPE=$search_per_type
										$search_condition
						   group by		$position_select ";
			}elseif($DPISDB=="oci8"){				
				$cmd = " select			count($position_select) as count_position
						   from			$position_table a, PER_ORG b, PER_ORG c, PER_PERSONAL d
						   where		a.ORG_ID=b.ORG_ID and a.DEPARTMENT_ID=c.ORG_ID and $position_join and d.PER_STATUS=1 and d.PER_TYPE=$search_per_type
										$search_condition
						   group by		$position_select ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count($position_select) as count_position
						   from		(
											(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
											) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)														
										) inner join PER_PERSONAL d on ($position_join)
						   where		d.PER_STATUS=1  and d.PER_TYPE=$search_per_type
										$search_condition
						   group by		$position_select ";
			} // end if
		}elseif($position_type == 2){
			// ตำแหน่งว่างมีเงิน
			$cmd = " select 		count(d.PER_ID) as count_person, $position_select 
							 from 			$position_table a, PER_PERSONAL d 
							 where 		$position_join and d.PER_STATUS=1 and d.PER_TYPE=$search_per_type and $position_status=1
							 group by 	$position_select 
							 having 		count(d.PER_ID) > 0 ";
			$db_dpis2->send_cmd($cmd);
			while($data = $db_dpis2->get_array()) $arr_exclude[] = $data[POS_ID];
			
			$search_condition = str_replace(" where ", " and ", $search_condition);
//				if(count($arr_exclude)) $search_condition .= " and $position_select not in (". implode(",", $arr_exclude) .")";
			if(count($arr_exclude)) $search_condition .= " and $position_select not in ( select distinct $position_select from $position_table a, PER_PERSONAL d where $position_join and d.PER_STATUS=1  and d.PER_TYPE=$search_per_type and $position_status=1 )";

			if($DPISDB=="odbc"){
				$cmd = " select			count($position_select) as count_position
								   from		(
													(
														$position_table a
													) inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
								 where		$position2 
													$search_condition
								 group by	$position_select ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			count($position_select) as count_position
								 from			$position_table a, PER_ORG b, PER_ORG c
								 where		a.ORG_ID=b.ORG_ID and a.DEPARTMENT_ID=c.ORG_ID and $position2
													$search_condition
								 group by	$position_select ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count($position_select) as count_position
								   from		(
													(
														$position_table a
													) inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
								 where		$position2
													$search_condition
								 group by	$position_select ";
			} // end if	
		} // end if

		$count_position = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error(); 
		$count_position = 0;
		while ($data = $db_dpis2->get_array()) {
			$data = array_change_key_case($data, CASE_LOWER);
			$count_position = $count_position+$data[count_position];
		}
//echo "$count_position :: $position_type---- $cmd<br>";
		return $count_position;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $line_code;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
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
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
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
				case "LINE" :
					$PL_CODE = -1;
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

		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
					   from	 (
									(
										(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
									) left join $line_table f on ($line_join)
								) inner join PER_ORG g on (c.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
					   from			$position_table a, PER_ORG b, PER_ORG c, $line_table f, PER_ORG g
					   where		a.ORG_ID=b.ORG_ID and a.DEPARTMENT_ID=c.ORG_ID and c.ORG_ID_REF=g.ORG_ID and $line_join(+)
									$search_condition
					   order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
					   from	 (
									(
										(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
									) left join $line_table f on ($line_join)
								) inner join PER_ORG g on (c.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		$order_by ";
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	initialize_parameter(0);
	$MINISTRY_ID = -1;		$DEPARTMENT_ID_INI = -1;
	$rpt_order_start_index = 0;
	while($data = $db_dpis->get_array()){
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
				$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
//				echo "$rpt_order_index====>$REPORT_ORDER<br>";
				switch($REPORT_ORDER){
					case "MINISTRY" :
					if ($CTRL_TYPE < 3) {
						if($MINISTRY_ID != trim($data[MINISTRY_ID])){
							$MINISTRY_ID =  trim($data[MINISTRY_ID]);
							if($MINISTRY_ID != ""){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$MINISTRY_NAME = $data2[ORG_NAME];
								$MINISTRY_SHORT = $data2[ORG_SHORT];
							}

							if ($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1) {
								$arr_content[$data_count][type] = "COUNTRY";
								$rpt_order_start_index = 0;
								$addition_condition = "";
							} else {
								$arr_content[$data_count][type] = "MINISTRY";
								$rpt_order_start_index = 1;
								$addition_condition = generate_condition(1);
							}
				//			echo "".$arr_content[$data_count][type]."-$MINISTRY_ID-$MINISTRY_NAME<br>";
							//if($rpt_order_index == 0){
								$GRAND_TOTAL1[$DEPARTMENT_ID] = count_position(1, $search_condition, $addition_condition);
								$GRAND_TOTAL2[$DEPARTMENT_ID] = count_position(2, $search_condition, $addition_condition);
							//}
							$GRAND_TOTAL[$DEPARTMENT_ID] = ($GRAND_TOTAL1[$DEPARTMENT_ID] + $GRAND_TOTAL2[$DEPARTMENT_ID]);
					
							if ($f_all) {   //เพิ่มใหม่
								$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) .$MINISTRY_NAME;
								$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) .$MINISTRY_SHORT;
								$arr_content[$data_count][id] = $DEPARTMENT_ID;
								$arr_content[$data_count][count_1] = $GRAND_TOTAL1[$DEPARTMENT_ID];
								$arr_content[$data_count][count_2] = $GRAND_TOTAL2[$DEPARTMENT_ID];

								$data_count++;
							} // if f_all
						} // end if
					} // end if
					break;

					case "DEPARTMENT" :
					if ($CTRL_TYPE < 5) {
						if($DEPARTMENT_ID && $DEPARTMENT_ID_INI != $DEPARTMENT_ID){
							if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
								$DEPARTMENT_ID_INI = trim($DEPARTMENT_ID);
								$addition_condition = generate_condition($rpt_order_index);
								$GRAND_TOTAL1[$DEPARTMENT_ID] = count_position(1, $search_condition, $addition_condition);
								$GRAND_TOTAL2[$DEPARTMENT_ID] = count_position(2, $search_condition, $addition_condition);
								$GRAND_TOTAL[$DEPARTMENT_ID] = ($GRAND_TOTAL1[$DEPARTMENT_ID] + $GRAND_TOTAL2[$DEPARTMENT_ID]);
					
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$DEPARTMENT_NAME = $data2[ORG_NAME];
								$DEPARTMENT_SHORT = $data2[ORG_SHORT];

								$arr_content[$data_count][type] = "DEPARTMENT";
								$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) .$DEPARTMENT_NAME;
								$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) .$DEPARTMENT_SHORT;
								$arr_content[$data_count][id] = $DEPARTMENT_ID;
								$arr_content[$data_count][count_1] = $GRAND_TOTAL1[$DEPARTMENT_ID];
								$arr_content[$data_count][count_2] = $GRAND_TOTAL2[$DEPARTMENT_ID];

								$data_count++;
							}
						} // end if
					} // end if
					break;
// เพิ่มใหม่
					case "ORG" :
						if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != ""){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
								$ORG_SHORT = $data2[ORG_SHORT];
								if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
							} // end if

							if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
	
								$arr_content[$data_count][type] = "ORG";
								$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) .$ORG_NAME;
								$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) .$ORG_SHORT;
								$arr_content[$data_count][id] = $DEPARTMENT_ID;
								$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
								if($rpt_order_index == 0){
									$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
									$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
								} // end if
	
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
								$data_count++;
							} //------------
						} // end if
					break;
		
					case "ORG_1" :
						if($ORG_ID_1 != trim($data[ORG_ID_1])){
							$ORG_ID_1 = trim($data[ORG_ID_1]);
							$ORG_NAME_1 = $ORG_SHORT_1 = "ไม่ระบุ";
							if($ORG_ID_1 != ""){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_1 = $data2[ORG_NAME];
								$ORG_SHORT_1 = $data2[ORG_SHORT];
								if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
							} // end if

							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "ORG_1";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
							$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_SHORT_1;
							$arr_content[$data_count][id] = $DEPARTMENT_ID;
							$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
							if($rpt_order_index == 0){
								$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							} // end if
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					break;
			
					case "ORG_2" :
						if($ORG_ID_2 != trim($data[ORG_ID_2])){
							$ORG_ID_2 = trim($data[ORG_ID_2]);
							$ORG_NAME_2 = $ORG_SHORT_2 = "ไม่ระบุ";
							if($ORG_ID_2 != ""){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_2 = $data2[ORG_NAME];
								$ORG_SHORT_2 = $data2[ORG_SHORT];
								if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
							} // end if

							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "ORG_2";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
							$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_SHORT_2;
							$arr_content[$data_count][id] = $DEPARTMENT_ID;
							$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
							if($rpt_order_index == 0){
								$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							} // end if
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					break;
			
					case "LINE" :
						if($PL_CODE != trim($data[PL_CODE])){
							$PL_CODE = trim($data[PL_CODE]);
							if($PL_CODE != ""){
								if(trim($line_short_name))	$line_s_name = ", ".$line_short_name;
								else $line_s_name="";
								$cmd = " select $line_name $line_s_name from $line_table a where trim($line_code)='$PL_CODE' ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PL_NAME = $data2[$line_name];
								if(trim($line_short_name)) $PL_SHORT_NAME = $data2[$line_short_name];
								else $PL_SHORT_NAME = "";
							} // end if

							$addition_condition = generate_condition($rpt_order_index);
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
							$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_SHORT_NAME;
							$arr_content[$data_count][id] = $DEPARTMENT_ID;
							$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
							if($rpt_order_index == 0){
								$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							} // end if
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					break;
			
					case "CO_LEVEL" :
						if($CL_NAME != trim($data[CL_NAME])){
							$CL_NAME = trim($data[CL_NAME]);

							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "CO_LEVEL";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($CL_NAME)?"ระดับ $CL_NAME":"[ไม่ระบุช่วงระดับตำแหน่ง]");
							$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($CL_NAME)?"ระดับ $CL_NAME":"[ไม่ระบุช่วงระดับตำแหน่ง]");
							$arr_content[$data_count][id] = $DEPARTMENT_ID;
							$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
							if($rpt_order_index == 0){
								$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							} // end if
					
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
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
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
							$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
							$arr_content[$data_count][id] = $DEPARTMENT_ID;
							$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
							if($rpt_order_index == 0){
								$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							} // end if
					
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					break;
				} // end switch case
			} // end for
		} // end if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1))
	} // end while
	
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
		$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B");
		$wsdata_align_1 = array("L","R","R","R","R","R","R");
		$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
		$wsdata_colmerge_1 = array(0,0,0,0,0,0,0);
		$wsdata_fontfmt_2 = array("","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		$count_org_ref = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];	
			$DEPARTMENT_ID = $arr_content[$data_count][id];	

			if($REPORT_ORDER == "COUNTRY" || $REPORT_ORDER == "MINISTRY" || $REPORT_ORDER == "DEPARTMENT"){ 
				$COUNT_1 = $arr_content[$data_count][count_1];
				$COUNT_2 = $arr_content[$data_count][count_2];

				if($DEPARTMENT_ID!=""){	
					$PERCENT_TOTAL1 = $PERCENT_TOTAL2 = 0;
					if($GRAND_TOTAL[$DEPARTMENT_ID]){ 
						$PERCENT_TOTAL1 = ($GRAND_TOTAL1[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$PERCENT_TOTAL2 = ($GRAND_TOTAL2[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$GRAND_TOTAL[$DEPARTMENT_ID] = ($GRAND_TOTAL1[$DEPARTMENT_ID]+$GRAND_TOTAL2[$DEPARTMENT_ID]); //MMMMM
						$PERCENT_TOTAL = ($GRAND_TOTAL[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
					} // end if
				}
			}else{
				$COUNT_1 = $arr_content[$data_count][count_1];
				$COUNT_2 = $arr_content[$data_count][count_2];
			}
			$COUNT_TOTAL = ($COUNT_1 + $COUNT_2);
			$PERCENT_1 = $PERCENT_2 = 0;
			if($COUNT_TOTAL){ 
				$PERCENT_1 = ($COUNT_1 / $COUNT_TOTAL) * 100;
				$PERCENT_2 = ($COUNT_2 / $COUNT_TOTAL) * 100;
				if($GRAND_TOTAL[$DEPARTMENT_ID]) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
			} // end if
			//-------------------------------------------------------------------------------------------------------

			if($REPORT_ORDER == "COUNTRY" || $REPORT_ORDER == "MINISTRY" || $REPORT_ORDER == "DEPARTMENT"){ 
			
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $PERCENT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $PERCENT_2;
				$arr_data[] = $COUNT_TOTAL;
				$arr_data[] = $PERCENT_TOTAL;

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
				$count_org_ref++;
			}else{ 
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $PERCENT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $PERCENT_2;
				$arr_data[] = $COUNT_TOTAL;
				$arr_data[] = $PERCENT_TOTAL;

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1)
							$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						else
							$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
				}
			} //end if 
		} // end for

		if(count($GRAND_TOTAL) > 1){
			$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = 0;
			if(array_sum($GRAND_TOTAL)){ 
				if(array_search("COUNTRY", $arr_rpt_order) !== false) { 	// ถ้ามีเลือกทั้งส่วนราชการ
					$NET1 = array_sum($GRAND_TOTAL1)-$GRAND_TOTAL1[1];		// ลบผลรวมของทั้งส่วนราชการออก
					$NET2 = array_sum($GRAND_TOTAL2)-$GRAND_TOTAL2[1];		// ลบผลรวมของทั้งส่วนราชการออก
					$NET = array_sum($GRAND_TOTAL)-$GRAND_TOTAL[1];		// ลบผลรวมของทั้งส่วนราชการออก
				} else {
					$NET1 = array_sum($GRAND_TOTAL1);
					$NET2 = array_sum($GRAND_TOTAL2);
					$NET = array_sum($GRAND_TOTAL);
				}
				$PERCENT_TOTAL_1 = ($NET1 / $NET) * 100;
				$PERCENT_TOTAL_2 = ($NET2 / $NET) * 100;
				$PERCENT_TOTAL = ($NET / $NET) * 100;
			} // end if

			$arr_data = (array) null;
			$arr_data[] = "รวมทั้งสิ้น";
			$arr_data[] = $NET1;
			$arr_data[] = $PERCENT_TOTAL_1;
			$arr_data[] = $NET2;
			$arr_data[] = $PERCENT_TOTAL_2;
			$arr_data[] = $NET;
			$arr_data[] = $PERCENT_TOTAL;

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
		} // end if
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