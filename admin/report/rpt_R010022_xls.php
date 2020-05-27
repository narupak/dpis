<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R010022_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_field = "";
	if ($search_per_type == 1) {
		$position = "POS_ID";
		$search_field = ", b.PM_CODE, b.PT_CODE, b.PL_CODE, b.POS_NO ";
		$search_from = "PER_POSITION";
	} elseif ($search_per_type == 2) {
		$position = "POEM_ID";
		$search_field = ", b.PN_CODE, b.POEM_NO as POS_NO ";
		$search_from = "PER_POS_EMP";
	} elseif ($search_per_type == 3) {
		$position = "POEMS_ID";
		$search_field = ", b.EP_CODE, b.POEMS_NO as POS_NO ";
		$search_from = "PER_POS_EMPSER";
	}
	if ($search_budget_year){
		$search_birthdate = date_adjust((($search_budget_year - 544)."-10-02"), "y", -60);		
		$search_end_birthdate = date_adjust((($search_budget_year - 543)."-10-01"), "y", -60);
		if($DPISDB=="odbc") 
			$arr_search_condition[] = "(LEFT(trim(PER_BIRTHDATE), 10) >= '$search_birthdate') and 
									    (LEFT(trim(PER_BIRTHDATE), 10) <= '$search_end_birthdate') ";
		elseif($DPISDB=="oci8") 
			$arr_search_condition[] = "(SUBSTR(trim(PER_BIRTHDATE), 1, 10) >= '$search_birthdate') and 
									    (SUBSTR(trim(PER_BIRTHDATE), 1, 10) <= '$search_end_birthdate') ";
		elseif($DPISDB=="mysql")
			$arr_search_condition[] = "(LEFT(trim(PER_BIRTHDATE), 10) >= '$search_birthdate') and 
									    (LEFT(trim(PER_BIRTHDATE), 10) <= '$search_end_birthdate') ";
	} // end if
		
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type)  && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type)  && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type)  && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type)  && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("MINISTRY", "DEPARTMENT", "ORG", "LINE"); 

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
				$select_list .= "f.ORG_SEQ_NO, f.ORG_CODE, b.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "f.ORG_SEQ_NO, f.ORG_CODE, b.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "e.ORG_SEQ_NO, e.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "e.ORG_SEQ_NO, e.ORG_CODE, a.ORG_ID";

//				if($order_by) $order_by .= ", ";
//				if($select_org_structure == 0) $order_by .= "e.ORG_SEQ_NO, e.ORG_CODE, b.ORG_ID";
//				else if($select_org_structure == 1) $order_by .= "e.ORG_SEQ_NO, e.ORG_CODE, a.ORG_ID";

//				$heading_name .= " $ORG_TITLE";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.PL_SEQ_NO, b.PL_CODE";

				$heading_name .= " $PL_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "f.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "f.ORG_SEQ_NO, f.ORG_CODE, b.DEPARTMENT_ID";
		}else{
			 if($select_org_structure == 0) $order_by = "e.ORG_SEQ_NO, e.ORG_CODE, b.ORG_ID";
			 else if($select_org_structure == 1) $order_by = "e.ORG_SEQ_NO, e.ORG_CODE, a.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "f.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "f.ORG_SEQ_NO, f.ORG_CODE, b.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure == 0) $select_list = "e.ORG_SEQ_NO, e.ORG_CODE, b.ORG_ID";
			else if($select_org_structure == 1) $select_list = "e.ORG_SEQ_NO, e.ORG_CODE, a.ORG_ID";
		}
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	//$arr_search_condition[] = "(a.PER_STATUS= $search_per_status)";
	
	$list_type_text = $ALL_REPORT_TITLE;
	
	if(in_array("PER_ORG_TYPE_1", $list_type) ){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(f.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type) ){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(f.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(e.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(e.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		$list_type_text = "";
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(f.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		$arr_level_no_condi = (array) null;
		foreach ($LEVEL_NO as $search_level_no)
		{
			if ($search_level_no) $arr_level_no_condi[] = "'$search_level_no'";
	//		echo "search_level_no=$search_level_no<br>";
		}
		if (count($arr_level_no_condi) > 0) $arr_search_condition[] = "(trim(d.LEVEL_NO) in (".implode(",",$arr_level_no_condi)."))";

		//ตำแหน่งในสายงาน
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= " $PL_TITLE $search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= " $POS_EMP_TITLE $search_pn_name";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "รายชื่อข้าราชการซึ่งจะมีอายุครบ 60 ปีบริบูรณ์";
//	$report_title .= " $list_type_text";
  $report_title .= " ประจำปีงบประมาณ  พ.ศ. ".$search_budget_year;   
   $report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
		
	$report_code = "H22";
	
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
		$ws_head_line1 = array("ลำดับที่","ชื่อ-นามสกุล","$PM_TITLE","$PL_TITLE","ตำแหน่งประเภท","ระดับตำแหน่ง","ปีเกษียณ");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C");
		$ws_width = array(10,40,30,30,20,20,15);
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
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		
		
	function list_position($search_condition, $addition_condition){
		global $DPISDB, $db_dpis, $db_dpis2, $db_dpis3;
		global $arr_rpt_order, $rpt_order_index, $arr_content, $data_count;
		global $select_org_structure, $search_from, $search_field,	$select_list, $position, $order_by;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		
	if($DPISDB=="odbc"){
		$cmd = "select a.PER_ID, a.PN_CODE as PREN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_RETIREDATE, a.LEVEL_NO, d.LEVEL_NAME,
						d.POSITION_TYPE, d.POSITION_LEVEL, b.ORG_ID, b.ORG_ID_1, b.DEPARTMENT_ID, e.ORG_NAME, f.ORG_NAME as DEPARTMENT_NAME
								$search_field,	$select_list
						from	(
										( 
											(  
												(
													(
													PER_PERSONAL a 
													left join $search_from b on (a.$position=b.$position) 
													) left join PER_LINE c on(b.PL_CODE=c.PL_CODE)
												) left join PER_LEVEL d on (a.LEVEL_NO=d.LEVEL_NO) 
										  ) left join PER_ORG e on (b.ORG_ID=e.ORG_ID)
										) left join PER_ORG f on (b.DEPARTMENT_ID=f.ORG_ID)
									) left join PER_MGT g on (b.PM_CODE=g.PM_CODE)
						$search_condition
						order by $order_by, d.LEVEL_SEQ_NO DESC, g.PM_SEQ_NO, a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select a.PER_ID, a.PN_CODE as PREN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_RETIREDATE, a.LEVEL_NO, d.LEVEL_NAME,
						d.POSITION_TYPE, d.POSITION_LEVEL, b.ORG_ID, b.ORG_ID_1, b.DEPARTMENT_ID, e.ORG_NAME, f.ORG_NAME as DEPARTMENT_NAME
								$search_field,	$select_list
					from PER_PERSONAL a,$search_from b,PER_LINE c,PER_LEVEL d,PER_ORG e,PER_ORG f, PER_MGT g
					where (a.$position=b.$position) and (b.PL_CODE=c.PL_CODE) and (a.LEVEL_NO=d.LEVEL_NO) 
								and (b.ORG_ID=e.ORG_ID(+)) and (b.DEPARTMENT_ID=f.ORG_ID(+)) and (b.PM_CODE=g.PM_CODE(+))
								$search_condition
					order by $order_by, d.LEVEL_SEQ_NO DESC, g.PM_SEQ_NO, NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY') ";
	}elseif($DPISDB=="mysql"){
		$cmd = "select a.PER_ID, a.PN_CODE as PREN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_RETIREDATE, a.LEVEL_NO, d.LEVEL_NAME,
						d.POSITION_TYPE, d.POSITION_LEVEL, b.ORG_ID, b.ORG_ID_1, b.DEPARTMENT_ID, e.ORG_NAME, f.ORG_NAME as DEPARTMENT_NAME
								$search_field,	$select_list
						from	(
										( 
											(  
												(
													(
													PER_PERSONAL a 
													left join $search_from b on (a.$position=b.$position) 
													) left join PER_LINE c on(b.PL_CODE=c.PL_CODE)
												) left join PER_LEVEL d on (a.LEVEL_NO=d.LEVEL_NO) 
										  ) left join PER_ORG e on (b.ORG_ID=e.ORG_ID)
										) left join PER_ORG f on (b.DEPARTMENT_ID=f.ORG_ID)
									) left join PER_MGT g on (b.PM_CODE=g.PM_CODE)
						$search_condition
						order by $order_by, d.LEVEL_SEQ_NO DESC, g.PM_SEQ_NO, a.PER_NAME, a.PER_SURNAME ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$db_dpis2->send_cmd($cmd);
	//echo "$cmd<br>";
	//$db_dpis->show_error();

	while($data1 = $db_dpis2->get_array()){
		$data_row++;
	
		$TMP_PER_ID = $data1[PER_ID];
		$TMP_PER_NAME = trim($data1[PER_NAME]);
		$TMP_PER_SURNAME = trim($data1[PER_SURNAME]);
		$TMP_LEVEL_NO = trim($data1[LEVEL_NO]);
		$TMP_POSITION_TYPE = trim($data1[POSITION_TYPE]);
		$TMP_LEVEL_NAME =  trim($data1[POSITION_LEVEL]);
		
		$TMP_ORG_ID = $data1[ORG_ID];
		$TMP_MINISTRY_ID = $data1[MINISTRY_ID]; 
		
		$TMP_PER_STARTDATE = show_date_format($data1[PER_STARTDATE],$DATE_DISPLAY);
		$TMP_PER_RETIREDATE= substr($data1[PER_RETIREDATE],0,4)+543;
		$TMP_POS_NO = $data1[POS_NO];
		
		$TMP_PM_NAME = $TMP_PL_NAME = $TMP_PN_NAME = $TMP_EP_NAME = $TMP_ORG_NAME = "";

		$TMP_PREN_CODE = trim($data1[PREN_CODE]);
		if($TMP_PREN_CODE){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PREN_CODE' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$TMP_PREN_NAME = $data2[PN_NAME];
		} // end if		
		
		$TMP_PM_CODE = trim($data1[PM_CODE]);
		if($TMP_PM_CODE){
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='$TMP_PM_CODE' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$TMP_PM_NAME = $data2[PM_NAME];
		} // end if
		$TMP_ORG_NAME = $data1[ORG_NAME];
	
		$ORG_ID_1 = $data1[ORG_ID_1];
		$TMP_ORG_NAME_1 = "";
		if($ORG_ID_1){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$TMP_ORG_NAME_1 = $data2[ORG_NAME];
		}
	
		if ($TMP_PM_NAME=="ผู้ว่าราชการจังหวัด" || $TMP_PM_NAME=="รองผู้ว่าราชการจังหวัด" || $TMP_PM_NAME=="ปลัดจังหวัด" || $TMP_PM_NAME=="หัวหน้าสำนักงานจังหวัด") {
			$TMP_PM_NAME .= $TMP_ORG_NAME;
			$TMP_PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $TMP_PM_NAME); 
			$TMP_PM_NAME = str_replace("สำนักงานจังหวัดสำนักงานจังหวัด", "สำนักงานจังหวัด", $TMP_PM_NAME); 
		} elseif ($TMP_PM_NAME=="นายอำเภอ") {
			$TMP_PM_NAME .= $TMP_ORG_NAME_1;
			$TMP_PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $TMP_PM_NAME); 
		}
		
		$TMP_PL_CODE = $data1[PL_CODE];
		$TMP_PT_CODE = trim($data1[PT_CODE]);
		if($TMP_PL_CODE){
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$TMP_PL_NAME = $data2[PL_NAME];
	
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$TMP_PT_CODE' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$TMP_PT_NAME = $data2[PT_NAME];
	
			$TMP_POS_NAME = $TMP_PL_NAME;
		} // end if
	
		$TMP_PN_CODE = $data1[PN_CODE];
		if($TMP_PN_CODE){									//คำนำหน้า
			$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PN_CODE' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$TMP_POS_NAME = $data2[PN_NAME];
		} // end if
	
		$TMP_EP_CODE = $data1[EP_CODE];
		if($TMP_EP_CODE){
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_EP_CODE' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$TMP_POS_NAME = $data2[EP_NAME];
		} // end if
		
		if($TMP_ORG_ID){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$TMP_ORG_ID' ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];
		} // end if
			
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = "$TMP_PREN_NAME$TMP_PER_NAME $TMP_PER_SURNAME";
		$arr_content[$data_count][pl_name] = $TMP_POS_NAME;
		$arr_content[$data_count][pm_name] = $TMP_PM_NAME;
		$arr_content[$data_count][level_name] = $TMP_LEVEL_NAME;
		$arr_content[$data_count][position_type] = "$TMP_POSITION_TYPE";
		$arr_content[$data_count][per_retiredate] = "$TMP_PER_RETIREDATE";
		
		$data_count++;			
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2;
		global $line_code;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "DEPARTMENT" :
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
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
				case "DEPARTMENT" :
					$DEPARTMENT_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($DPISDB=="odbc"){
		$cmd = "select a.PER_ID, a.PN_CODE as PREN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_RETIREDATE, a.LEVEL_NO, d.LEVEL_NAME,
						d.POSITION_TYPE, d.POSITION_LEVEL, b.ORG_ID, b.ORG_ID_1, b.DEPARTMENT_ID, f.ORG_NAME as DEPARTMENT_NAME
								$search_field,	$select_list
						from (
								 	( 
										(  
											(
											PER_PERSONAL a 
											left join $search_from b on (a.$position=b.$position) 
											) left join PER_LINE c on(b.PL_CODE=c.PL_CODE)
										) left join PER_LEVEL d on (a.LEVEL_NO=d.LEVEL_NO) 
					          	  ) left join PER_ORG e on (b.ORG_ID=e.ORG_ID)
								) left join PER_ORG f on (b.DEPARTMENT_ID=f.ORG_ID)
						$search_condition
						order by $order_by, d.LEVEL_SEQ_NO DESC, c.PL_SEQ_NO, a.PER_SEQ_NO, a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select a.PER_ID, a.PN_CODE as PREN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_RETIREDATE, a.LEVEL_NO, d.LEVEL_NAME,
						d.POSITION_TYPE, d.POSITION_LEVEL, b.ORG_ID, b.ORG_ID_1, b.DEPARTMENT_ID, f.ORG_NAME as DEPARTMENT_NAME
								$search_field,	$select_list
					from PER_PERSONAL a,$search_from b,PER_LINE c,PER_LEVEL d,PER_ORG e,PER_ORG f
					where (a.$position=b.$position) and (b.PL_CODE=c.PL_CODE) and (a.LEVEL_NO=d.LEVEL_NO) 
								and (b.ORG_ID=e.ORG_ID(+)) and (b.DEPARTMENT_ID=f.ORG_ID(+))
								$search_condition
					order by $order_by, d.LEVEL_SEQ_NO DESC, c.PL_SEQ_NO, a.PER_SEQ_NO, a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = "select a.PER_ID, a.PN_CODE as PREN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_RETIREDATE, a.LEVEL_NO, d.LEVEL_NAME,
						d.POSITION_TYPE, d.POSITION_LEVEL, b.ORG_ID, b.ORG_ID_1, b.DEPARTMENT_ID, f.ORG_NAME as DEPARTMENT_NAME
								$search_field,	$select_list
						from (
								 	( 
										(  
											(
											PER_PERSONAL a 
											left join $search_from b on (a.$position=b.$position) 
											) left join PER_LINE c on(b.PL_CODE=c.PL_CODE)
										) left join PER_LEVEL d on (a.LEVEL_NO=d.LEVEL_NO) 
					          	  ) left join PER_ORG e on (b.ORG_ID=e.ORG_ID)
								) left join PER_ORG f on (b.DEPARTMENT_ID=f.ORG_ID)
						$search_condition
						order by $order_by, d.LEVEL_SEQ_NO DESC, c.PL_SEQ_NO, a.PER_SEQ_NO, a.PER_NAME, a.PER_SURNAME ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	$data_count = $data_row = 0;
	
	$arr_content[$data_count][type] = $PERSON_TYPE[$search_per_type];
	$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 6)) .$PERSON_TYPE[$search_per_type] ;
	$data_count++;
	
	initialize_parameter(0);
	$first_order = 1;	// order ที  0 = COUNTRY ยังไม่ได้คำนวณ เริ่ม order ที่ 1 (MINISTRY) ก่อน
	while($data = $db_dpis->get_array()){
	
	//	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
		for($rpt_order_index=$first_order; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			// echo "REPORT_ORDER=$REPORT_ORDER<br>";
			switch($REPORT_ORDER){
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
						$TMP_DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
						if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){
						
							$addition_condition = generate_condition($rpt_order_index);
							
							$arr_content[$data_count][type] = "DEPARTMENT";
							$arr_content[$data_count][order] = "";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $TMP_DEPARTMENT_NAME;
							$arr_content[$data_count][pl_name] = "";
							$arr_content[$data_count][pm_name] = "";
							$arr_content[$data_count][level_name] = "";
							$arr_content[$data_count][position_type] = "";
							$arr_content[$data_count][per_retiredate] = "";

							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
	
							//if($rpt_order_index == (count($arr_rpt_order) - 1)) 
							list_position($search_condition, $addition_condition);
						}
					}
				break;
			} //end switch
		} //end for
//	} // end if 
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C","L","L","L","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$ORDER = $arr_content[$data_count][order];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$POSITION_TYPE = $arr_content[$data_count][position_type];
			$PER_RETIREDATE = $arr_content[$data_count][per_retiredate];

			if($REPORT_ORDER == "DEPARTMENT"){
         		$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] = "$NAME";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
            	$arr_data[] = "";
	
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
			}elseif($REPORT_ORDER == "CONTENT"){ 
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $NAME;		//PER_NAME
				$arr_data[] = $PM_NAME;
				$arr_data[] = $PL_NAME;
				$arr_data[] = $POSITION_TYPE;
				$arr_data[] = $LEVEL_NAME;
				$arr_data[] = $PER_RETIREDATE;
	
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
			}
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
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