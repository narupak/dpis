<?	
	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004002_dopa_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if($search_per_type == 1 || $search_per_type==5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "e.PL_NAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "e.PN_NAME";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "e.EP_NAME";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=e.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "e.TP_NAME";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

/**	new 	**/
	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID_REF";

				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.DEPARTMENT_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.DEPARTMENT_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, a.LEVEL_NO, g.LEVEL_SEQ_NO";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.LEVEL_NO, g.LEVEL_SEQ_NO";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, g.LEVEL_SEQ_NO desc";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, g.LEVEL_SEQ_NO desc";

				$heading_name .= " ส่วนราชการ";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){
			$order_by = "d.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){
//			$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.DEPARTMENT_ID";
			if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.DEPARTMENT_ID";
			else if($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";	// ตามกฏหมาย
			else if($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";	// ตามมอบหมายงาน
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){
			$select_list = "d.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){
			if($select_org_structure==0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.DEPARTMENT_ID";
			else if($select_org_structure==1) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
			else if($select_org_structure==1) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
		}
	} // end if

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
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
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
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		$search_pl_code = trim($search_pl_code);
		$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
		$list_type_text .= "$search_pl_name";
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
/**	new (end)	**/
	if($MFA_FLAG == 1){
	$report_title = "บัญชีรายชื่อ$PERSON_TYPE[$search_per_type]ผู้มีสิทธิได้รับบำเหน็จบำนาญซึ่งจะมีอายุครบ ๖๐ ปีบริบูรณ์ ||ในปีงบประมาณ พ.ศ. $show_budget_year สังกัด$DEPARTMENT_NAME $MINISTRY_NAME";
		}else {
		$company_name = "รูปแบบการออกรายงาน : $list_type_text";
		$report_title = "บัญชีรายชื่อ$PERSON_TYPE[$search_per_type]ผู้มีสิทธิได้รับบำเหน็จบำนาญซึ่งจะมีอายุครบ ๖๐ ปีบริบูรณ์ ในปีงบประมาณ พ.ศ. $show_budget_year||สังกัด$DEPARTMENT_NAME $MINISTRY_NAME";
		}
	$show_budget_year = convert2thaidigit($search_budget_year);
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
	if ($MFA_FLAG == 1) {
		$ws_head_line1 = array("ลำดับที่","ชื่อ - สกุล","ตำแหน่ง/ส่วนราชการ","ระดับ", "วัน เดือน ปีเกิด", "หมายเหตุ");
		$ws_colmerge_line1 = array(0,0,0,0,0,0);
		$ws_border_line1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C");
		$ws_width = array(5,35,50,15,20,10);
	} else {
		$ws_head_line1 = array("ลำดับ","ชื่อ - สกุล","เลขประจำตัวประชาชน","ตำแหน่ง", "วัน เดือน ปีเกิด", "หมายเหตุ");
		$ws_colmerge_line1 = array(0,0,0,0,0,0);
		$ws_border_line1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C");
		$ws_width = array(5,35,20,100,20,20);
	}
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
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_colmerge_line1;
		global $ws_border_line1, $ws_fontfmt_line1;
		global $ws_headalign_line1, $ws_width;

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
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line1[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		
	if($MFA_FLAG==1) $DATE_DISPLAY=2;
	function list_person($search_condition, $addition_condition, $org_id, $org_tree){
		global $DPISDB, $db_dpis2, $db_dpis3, $position_table, $position_join,$select_org_structure;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $search_budget_year;
		global $days_per_year, $days_per_month, $seconds_per_day,$CARD_NO_DISPLAY,$DATE_DISPLAY,$MFA_FLAG;
		global $search_per_type, $position_table, $position_join, $line_table, $line_name, $line_join;
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if ($org_id) 
			if ($org_tree==1 || $org_tree==2) $search_condition .= " and b.DEPARTMENT_ID = $org_id ";
			else $search_condition .= " and b.ORG_ID = $org_id ";
//		echo "org_id=$org_id, search_condition=$search_condition<br>";

		if($search_per_type==1){ //****code ต่าง : select list และ join PER_MGT f เพิ่มมาด้วย
			if($DPISDB=="odbc"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, f.PM_NAME, g.POSITION_LEVEL,
													a.PER_CARDNO,	LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, c.ORG_NAME, 
													b.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2
								 from			(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join $position_table b on $position_join
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join $line_table e on ($line_join)
													) left join PER_MGT f on (b.PM_CODE=f.PM_CODE)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, f.PM_NAME, g.POSITION_LEVEL, 
													a.PER_CARDNO, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, c.ORG_NAME, 
													b.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_MGT f, PER_LEVEL g
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and b.PM_CODE=f.PM_CODE(+) and a.LEVEL_NO=g.LEVEL_NO(+)
													$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, f.PM_NAME, g.POSITION_LEVEL,
													a.PER_CARDNO,	LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, c.ORG_NAME, 
													b.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2
								 from			(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join $position_table b on $position_join
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join $line_table e on ($line_join)
													) left join PER_MGT f on (b.PM_CODE=f.PM_CODE)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			} // end if
		}else{	//==2 | 3 | 4
			if($DPISDB=="odbc"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, 
													a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_CARDNO,
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, 
													c.ORG_NAME, b.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2
								 from			(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO,g.LEVEL_NAME, 
													a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, a.PER_CARDNO,
													SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE, 
													c.ORG_NAME, b.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_LEVEL g
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=g.LEVEL_NO(+)
													$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, 
													a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_CARDNO,
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, 
													c.ORG_NAME, b.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2
								 from			(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("b.DEPARTMENT_ID", "a.DEPARTMENT_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
		//echo "$cmd;";
//		$db_dpis2->show_error();
		while($data = $db_dpis2->get_array()){
			$data_count++;
			$person_count++;
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PL_NAME = trim($data[PL_NAME]);
			$PM_NAME = trim($data[PM_NAME]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$CARD_NO=trim($data[PER_CARDNO]);
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_2 = $data[ORG_ID_2];
			$ORG_NAME_1 = $ORG_NAME_2 = "";
			if ($ORG_ID_2) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$ORG_NAME_2 = $data3[ORG_NAME];
			}
			if ($ORG_ID_1) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$ORG_NAME_1 = $data3[ORG_NAME];
			}
	
			if ($PM_NAME=="นายอำเภอ") $ORG_NAME_1 = str_replace("ที่ทำการปกครอง","",$ORG_NAME_1);
			$ORG_NAME = trim($ORG_NAME_2. " " . $ORG_NAME_1. " " . $ORG_NAME);
			if ($ORG_NAME=="ไม่สังกัดสำนัก-กอง") $ORG_NAME= "กรมการปกครอง";
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][person] = convert2thaidigit($person_count);	
			$arr_content[$data_count][name] = str_repeat(" ", 10) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			if ($PM_NAME)
				$arr_content[$data_count][position] = $PM_NAME . "\n(" . $PL_NAME . $POSITION_LEVEL . ")\n " . $ORG_NAME;
			else
			$arr_content[$data_count][position] = $PL_NAME."\n".$POSITION_LEVEL."\n".$ORG_NAME;
			$arr_content[$data_count][level] = convert2thaidigit($POSITION_LEVEL);	
			$arr_content[$data_count][birthdate] = convert2thaidigit($PER_BIRTHDATE);	
			$arr_content[$data_count][card_no] = convert2thaidigit(card_no_format($CARD_NO,$CARD_NO_DISPLAY));

//			$data_count++;
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type;
		global $LEVEL_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
//				case "ORG" :	
				case "LEVEL" :	
					if($LEVEL_NO) $arr_addition_condition[] = "(a.LEVEL_NO = '$LEVEL_NO')";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	if($search_per_type==1){ //****code ต่าง : select list และ join PER_MGT f เพิ่มมาด้วย
		if($DPISDB=="odbc"){
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from		(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (c.DEPARTMENT_ID=d.ORG_ID)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							order by $order_by, g.LEVEL_SEQ_NO desc  ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG d, PER_LEVEL g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and c.DEPARTMENT_ID=d.ORG_ID(+)
												and a.LEVEL_NO=g.LEVEL_NO(+)
												$search_condition
							 order by $order_by, g.LEVEL_SEQ_NO desc  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from		(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (c.DEPARTMENT_ID=d.ORG_ID)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							order by $order_by, g.LEVEL_SEQ_NO desc  ";
		} // end if
	}else{	//==2 | 3 | 4
		if($DPISDB=="odbc"){
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from	(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_ORG d on (c.DEPARTMENT_ID=d.ORG_ID)
										) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							order by $order_by, g.LEVEL_SEQ_NO desc  ";
		}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select		distinct $select_list, a.LEVEL_NO, g.LEVEL_SEQ_NO
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG d, PER_LEVEL g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and c.DEPARTMENT_ID=d.ORG_ID(+)
												and a.LEVEL_NO=g.LEVEL_NO(+)
												$search_condition
							order by  $order_by, g.LEVEL_SEQ_NO desc  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from		(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (c.DEPARTMENT_ID=d.ORG_ID)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							order by $order_by, g.LEVEL_SEQ_NO desc  ";
		}
	} // end if
	if($select_org_structure==1) { 	// 1 คือ มอบหมายงาน  0 คือ กฎหมาย
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("b.DEPARTMENT_ID", "a.DEPARTMENT_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
/*
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_PERSONAL a 
											) left join PER_LEVEL b on (a.LEVEL_NO=b.LEVEL_NO)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, PER_LEVEL b
						 where		a.LEVEL_NO=b.LEVEL_NO(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_PERSONAL a 
											) left join PER_LEVEL b on (a.LEVEL_NO=b.LEVEL_NO)
											$search_condition
						 order by		$order_by ";
	} // end if
*/
	$count_data = $db_dpis->send_cmd($cmd);
//	echo $cmd.";";
//	$db_dpis->show_error();
	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);
	$DEPARTMENT_ID = "";
	$MINISTRY_NAME = "";
	$ORG_NAME = "";
	$DEPARTMENT_NAME = "";
	$ORG_MIX = "";
	$ORG_IDX_ID = 0;
	$ORG_TREE = 0;
	$arr_rpt_order[] = "LEVEL";
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID] && $data[MINISTRY_ID] != $data[DEPARTMENT_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
//							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
							$MINISTRY_NAME = "";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							$ORG_TREE = 1;	// MINISTRY
						} // end if
					}
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
//							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
							$DEPARTMENT_NAME = "";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
							$ORG_TREE = 2;	// DEPARTMENT
						} // end if
					} // end if
				break;
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						
						$ORG_ID = $data[ORG_ID];
						$ORG_IDX_ID = $ORG_ID;
						
						if($ORG_ID == ""){
//							$ORG_NAME = "[ไม่ระบุ$ORG_TITLE]";
							$ORG_NAME = "";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_TREE = 3;	// ORG
						} // end if
					}
				break;
				case "LEVEL" :
				
					if ($ORG_MIX != trim($MINISTRY_NAME." ".$DEPARTMENT_NAME." ".$ORG_NAME))  {
						$ORG_MIX = trim($MINISTRY_NAME." ".$DEPARTMENT_NAME." ".$ORG_NAME);
						if (!$ORG_MIX) $ORG_MIX = "ไม่ระบุ สังกัด";
						$arr_content[$data_count][type] = "ORG";
						if($MFA_FLAG!=1){
						$arr_content[$data_count][name] = $ORG_MIX;	// str_repeat(" ", (($rpt_order_index-count($arr_rpt_order)) * 5)) . $ORG_MIX;
						$data_count++;
						}
					} // end if
					
					//if($LEVEL_NO != $data[LEVEL_NO]){
						$LEVEL_NO = $data[LEVEL_NO];
						$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$LEVEL_NAME = $data2[LEVEL_NAME];
						
						if (!$LEVEL_NAME) $LEVEL_NAME = "ไม่ระบุ ระดับ";
						$addition_condition = generate_condition($rpt_order_index);
						$arr_content[$data_count][type] = "LEVEL";
					    if($MFA_FLAG!=1)$arr_content[$data_count][name] = str_repeat(" ", 5) . $LEVEL_NAME;
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);		
						
						list_person($search_condition,$addition_condition,$ORG_IDX_ID,$ORG_TREE);
						if($MFA_FLAG!=1)$data_count++;
					//} // end if
				
				break;
				
				
/*
				case "ORG" :
					if($LEVEL_NO != $data[LEVEL_NO]){
						$LEVEL_NO = $data[LEVEL_NO];
							$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$LEVEL_NAME = $data2[LEVEL_NAME];

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $LEVEL_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
*/		
			} // end switch case
		} // end for
		
		if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition, $ORG_IDX_ID, $ORG_TREE);
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
		if($MFA_FLAG==1){
			$wsdata_fontfmt_1 = array("","","","","","");
		}else{
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B");
		}
			$wsdata_align_1 = $data_align;
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME_0 = $arr_content[$data_count][person];
			$NAME_1 = $arr_content[$data_count][name];
			if ($MFA_FLAG == 1) {
				$NAME_2 = $arr_content[$data_count][position];
				$NAME_3 = $arr_content[$data_count][level];
				$NAME_4 = $arr_content[$data_count][birthdate];
				$NAME_5 = $arr_content[$data_count][remark];
			} else {
				$NAME_2 = $arr_content[$data_count][card_no];
				$NAME_3 = $arr_content[$data_count][position];
				$NAME_4 = $arr_content[$data_count][birthdate];
				$NAME_5 = $arr_content[$data_count][remark];
			}

			$arr_data = (array) null;
			$arr_data[] = $NAME_0;
			$arr_data[] = $NAME_1;
			$arr_data[] = $NAME_2;
			$arr_data[] = $NAME_3;
			$arr_data[] = $NAME_4;
			$arr_data[] = $NAME_5;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if($REPORT_ORDER == "ORG" || $REPORT_ORDER == "LEVEL")
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