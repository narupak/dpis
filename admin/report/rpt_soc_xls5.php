<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");
	include ("rpt_soc_xls5_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$pl_code = "b.PL_CODE";
		$pl_name = "f.PL_NAME";
		 $type_code ="b.PT_CODE";
		 $select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$pl_code = "b.PN_CODE";
		$pl_name = "f.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$pl_code = "b.EP_CODE";
		$pl_name = "f.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$pl_code = "b.TP_CODE";
		$pl_name = "f.TP_NAME";
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
//	$search_per_type = 1;

	$search_condition = "";

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
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
	if(trim($search_org_id)){ 
		if($select_org_structure==0){
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
		}else if($select_org_structure==1){
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}
		$list_type_text = "$search_org_name";
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา";
	
	$report_code = "RPT_XLS5";
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/rpt_soc_xls5";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
	//	$ws_head_line1 = array("วันเดือนปี","ตำแหน่ง","หน่วยงาน","อายุ","เงินเดือน","หมายเหตุ");
		$ws_head_line1 = array("DEP_NAME","IDCARD","BEG_LIST_DATE","RANK","POS_NAME","DEP_NAME_DETAIL","AGE","SALARY","REMARK","OUT_DETIAL");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
		$ws_border_line2 = array("RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(25,15,15,7,25,25,5,10,15,20);
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
                $xlsRow++;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line1[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		
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
		global $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
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
		global $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	if ($BKK_FLAG==1) $DC_CODE_COND = "17";
	else $DC_CODE_COND = "61";
	$level = array("O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2");
	$data_count = 0;
	for ( $i=0; $i<count($level); $i++ ) { 
		unset($arr_addition_condition);
		unset($arr_include_person);

		$search_level_no = trim($level[$i]);
		$cmd = " select a.PER_ID from PER_PERSONAL a, PER_DECORATEHIS b 
						where a.PER_ID=b.PER_ID and trim(DC_CODE)='$DC_CODE_COND' and a.LEVEL_NO='$search_level_no' $search_condition 
						order by a.PER_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_exclude_person[] = $data[PER_ID];
	
		$input_date = ($search_year - 543) . "-07-28"; //"-12-05";
		$adjust_date = date_adjust($input_date, "year", -25);
		if($DPISDB=="odbc") $arr_addition_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$adjust_date')";
		elseif($DPISDB=="oci8") $arr_addition_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 10) <= '$adjust_date')";
		elseif($DPISDB=="mysql") $arr_addition_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$adjust_date')";
		if(count($arr_exclude_person)) $arr_addition_condition[] = "(a.PER_ID not in (". implode(",", $arr_exclude_person) ."))";

		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);
		
		$addition_condition = $search_condition . (($addition_condition)?" and $addition_condition ":"");

		if($DPISDB=="odbc"){
			$cmd = "	select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name as PL_CODE, $pl_name as PL_NAME $select_type_code
												,LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE
								from		(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table f on ($line_join)
								where		a.LEVEL_NO='$search_level_no'
												$addition_condition
								group by a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name,
												LEFT(trim(a.PER_STARTDATE), 10) $select_type_code
								order by a.DEPARTMENT_ID, LEFT(trim(a.PER_STARTDATE), 10), a.LEVEL_NO ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name as PL_CODE, $pl_name as PL_NAME $select_type_code
												,SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE
								from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table f
								where		a.LEVEL_NO='$search_level_no'
												and $position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) and $line_join(+)
												$addition_condition
								group by a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name,
												SUBSTR(trim(a.PER_STARTDATE), 1, 10) $select_type_code
								order by a.DEPARTMENT_ID, SUBSTR(trim(a.PER_STARTDATE), 1, 10), a.LEVEL_NO ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name as PL_CODE, $pl_name as PL_NAME $select_type_code
												,LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE
								from		(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table f on ($line_join)
								where		a.LEVEL_NO='$search_level_no'
												$addition_condition
								group by a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name,
												LEFT(trim(a.PER_STARTDATE), 10) $select_type_code
								order by a.DEPARTMENT_ID, LEFT(trim(a.PER_STARTDATE), 10), a.LEVEL_NO ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$ORG_NAME = $data[ORG_NAME];
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$COMPLETE_CONDDATE = date_adjust($PER_STARTDATE, "year", 25);
			$PER_STARTDATE = show_date_format(trim($PER_STARTDATE),$DATE_DISPLAY);
			$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION_LEVEL = $data2[POSITION_LEVEL];
			if($ORG_ID_REF != $data[ORG_ID_REF]){
				$ORG_ID_REF = $data[ORG_ID_REF];
				$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DEPARTMENT_NAME = $data2[ORG_NAME];
				
				$REF_ORG_ID_REF = $data2[ORG_ID_REF];
				$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REF_ORG_ID_REF ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MINISTRY_NAME = $data2[ORG_NAME];
			} // end if
			
			$PL_CODE = trim($data[PL_CODE]);
			$PL_NAME = trim($data[PL_NAME]);
			$PL_CODE = trim($data[PL_CODE]);
			$PL_NAME = (trim($PL_NAME))? "$PL_NAME". $POSITION_LEVEL : "";
			if(trim($type_code)){
				$PT_CODE = trim($data[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
				$PL_NAME .= (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
			}
			
			$arr_person[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_person[$data_count][ministry_name] = $MINISTRY_NAME;
			$arr_person[$data_count][department_name] = $DEPARTMENT_NAME;
			$arr_person[$data_count][org_name] = $ORG_NAME;
			$arr_person[$data_count][per_id] = $PER_ID;
			$arr_person[$data_count][per_name] = $PER_NAME;
			$arr_person[$data_count][per_birthdate] = $PER_BIRTHDATE;
			$arr_person[$data_count][level_no] = $LEVEL_NO;
			$arr_person[$data_count][per_startdate] = $PER_STARTDATE;
			$arr_person[$data_count][complete_conddate] = $COMPLETE_CONDDATE;
			$arr_person[$data_count][pl_name] = $PL_NAME;

			$data_count++;
		} // end while
	} // end for
	
//	echo "<pre>"; print_r($arr_person); echo "</pre>";

	$sort_arr[0]['name'] = "org_id_ref";
	$sort_arr[0]['sort'] = "ASC";
	$sort_arr[0]['case'] = FALSE; //  Case sensitive
			
	$sort_arr[1]['name'] = "level_no";
	$sort_arr[1]['sort'] = "DESC";
	$sort_arr[1]['case'] = FALSE;

	$sort_arr[2]['name'] = "per_startdate";
	$sort_arr[2]['sort'] = "ASC";
	$sort_arr[2]['case'] = FALSE;
			
	array_sort($arr_person, $sort_arr);		

//	echo "<pre>"; print_r($arr_person); echo "</pre>";
	$count_data = count($arr_person);
	$data_count = 0;
	for($i=0; $i<$count_data; $i++){
		$ORG_ID_REF = $arr_person[$i][org_id_ref];
		$MINISTRY_NAME = $arr_person[$i][ministry_name];
		$DEPARTMENT_NAME = $arr_person[$i][department_name];
		$ORG_NAME = $arr_person[$i][org_name];
		$PER_ID = $arr_person[$i][per_id];
		$PER_NAME = $arr_person[$i][per_name];
		$PER_BIRTHDATE = $arr_person[$i][per_birthdate];
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_BIRTHDATE_D = $arr_temp[2] + 0;
			$PER_BIRTHDATE_M = $month_full[($arr_temp[1] + 0)][TH];
			$PER_BIRTHDATE_Y = $arr_temp[0] + 543;
		} // end if
		$LEVEL_NO = $arr_person[$i][level_no];
		$PER_STARTDATE = $arr_person[$i][per_startdate];
		$COMPLETE_CONDDATE = $arr_person[$i][complete_conddate];
		if($COMPLETE_CONDDATE){
			$arr_temp = explode("-", $COMPLETE_CONDDATE);
			$COMPLETE_CONDDATE_D = $arr_temp[2] + 0;
			$COMPLETE_CONDDATE_M = $month_full[($arr_temp[1] + 0)][TH];
			$COMPLETE_CONDDATE_Y = $arr_temp[0] + 543;
		} // end if
		$PL_NAME = $arr_person[$i][pl_name];
		
		$arr_content[$data_count][type] = "PERSON";
		$arr_content[$data_count][name] = $PER_NAME;
		$arr_content[$data_count][position] = $PL_NAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][department_name] = $DEPARTMENT_NAME;
		$arr_content[$data_count][ministry_name] = $MINISTRY_NAME;
		$arr_content[$data_count][per_birthdate_d] = $PER_BIRTHDATE_D;
		$arr_content[$data_count][per_birthdate_m] = $PER_BIRTHDATE_M;
		$arr_content[$data_count][per_birthdate_y] = $PER_BIRTHDATE_Y;
		$arr_content[$data_count][complete_conddate_d] = $COMPLETE_CONDDATE_D;
		$arr_content[$data_count][complete_conddate_m] = $COMPLETE_CONDDATE_M;
		$arr_content[$data_count][complete_conddate_y] = $COMPLETE_CONDDATE_Y;
		
		$data_count++;
		
		unset($arr_careerhis);
		unset($sort_arr);
		
		if($DPISDB=="odbc"){
			$cmd = " select 	LEFT(a.POH_EFFECTIVEDATE, 10) as POH_EFFECTIVEDATE, a.LEVEL_NO, a.PL_CODE, a.PT_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.POH_SALARY, a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, a.MOV_CODE, b.MOV_NAME, a.POH_PL_NAME, a.POH_ORG
							 from		(
												PER_POSITIONHIS a
												inner join PER_MOVMENT b on (a.MOV_CODE=b.MOV_CODE)
											)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(POH_EFFECTIVEDATE, 10) ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	SUBSTR(a.POH_EFFECTIVEDATE, 1, 10) as POH_EFFECTIVEDATE, a.LEVEL_NO, a.PL_CODE, a.PT_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.POH_SALARY, a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, a.MOV_CODE, b.MOV_NAME, a.POH_PL_NAME, a.POH_ORG
							 from		PER_POSITIONHIS a, PER_MOVMENT b
							 where	a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE
							 order by SUBSTR(POH_EFFECTIVEDATE, 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	LEFT(a.POH_EFFECTIVEDATE, 10) as POH_EFFECTIVEDATE, a.LEVEL_NO, a.PL_CODE, a.PT_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.POH_SALARY, a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, a.MOV_CODE, b.MOV_NAME, a.POH_PL_NAME, a.POH_ORG
							 from		(
												PER_POSITIONHIS a
												inner join PER_MOVMENT b on (a.MOV_CODE=b.MOV_CODE)
											)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(POH_EFFECTIVEDATE, 10) ";
		} // end if
		$count_hist = $db_dpis->send_cmd($cmd);
		$hist_count = 0;
		while($data = $db_dpis->get_array()){
			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$POH_AGE = floor(date_difference($POH_EFFECTIVEDATE, $PER_BIRTHDATE, "year"));
			
			$POH_LEVEL_NO = trim($data[LEVEL_NO]);
			$POH_SALARY = trim($data[POH_SALARY]);
			$POH_MOV_NAME = trim($data[MOV_NAME]);
			$POH_PL_NAME = trim($data[POH_PL_NAME]);
			$POH_ORG = trim($data[POH_ORG]);
			if($search_per_type == 1 || $search_per_type == 5) $POH_PL_CODE = trim($data[PL_CODE]);
			elseif($search_per_type == 2) $POH_PL_CODE = trim($data[PN_CODE]);
			elseif($search_per_type == 3) $POH_PL_CODE = trim($data[EP_CODE]);
			elseif($search_per_type == 4) $POH_PL_CODE = trim($data[TP_CODE]);

			$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$POH_LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION_LEVEL = $data2[POSITION_LEVEL];

			if (!$POH_PL_NAME) {
				$cmd = " select $pl_name as PL_NAME from $line_table b where trim($pl_code)='$POH_PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_PL_NAME = trim($data2[PL_NAME]);
			}

//			$POH_POSITION = (trim($POH_PL_NAME))? "$POH_PL_NAME". $POSITION_LEVEL : "";
			$POH_POSITION = (trim($POH_PL_NAME))? "$POH_PL_NAME" : "";
			if(trim($type_code)){
				$POH_PT_CODE = trim($data[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$POH_PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_PT_NAME = trim($data2[PT_NAME]);

				$POH_POSITION .= (($POH_PT_NAME != "ทั่วไป" && $POH_LEVEL_NO >= 6)?"$POH_PT_NAME":"");
			}
						
			$POH_ORG_ID_1 = trim($data[ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POH_ORG_ID_1 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ORG_NAME = $data2[ORG_NAME];

			$POH_ORG_ID_2 = trim($data[ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POH_ORG_ID_2 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ORG_NAME = $data2[ORG_NAME];
			
			$POH_ORG_ID_3 = trim($data[ORG_ID_3]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POH_ORG_ID_3 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ORG_NAME = $data2[ORG_NAME];

			$arr_careerhis[$hist_count][type] = "POSITION";
			$arr_careerhis[$hist_count][effectivedate] = $POH_EFFECTIVEDATE;
			$arr_careerhis[$hist_count][position] = $POH_POSITION;
			$arr_careerhis[$hist_count][org_name] = $POH_ORG;
			$arr_careerhis[$hist_count][age] = $POH_AGE;
			$arr_careerhis[$hist_count][salary] = $POH_SALARY;
			$arr_careerhis[$hist_count][note] = $POH_MOV_NAME;

			$hist_count++;
		} // end while		

		if($DPISDB=="odbc"){
			$cmd = " select 	LEFT(a.SAH_EFFECTIVEDATE, 10) as SAH_EFFECTIVEDATE, a.SAH_SALARY, a.MOV_CODE, b.MOV_NAME, SAH_POSITION, SAH_ORG
							 from		(
												PER_SALARYHIS a
												inner join PER_MOVMENT b on (a.MOV_CODE=b.MOV_CODE)
											)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(SAH_EFFECTIVEDATE, 10) ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	SUBSTR(a.SAH_EFFECTIVEDATE, 1, 10) as SAH_EFFECTIVEDATE, a.SAH_SALARY, a.MOV_CODE, b.MOV_NAME, SAH_POSITION, SAH_ORG
							 from		PER_SALARYHIS a, PER_MOVMENT b
							 where	a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE
							 order by SUBSTR(SAH_EFFECTIVEDATE, 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	LEFT(a.SAH_EFFECTIVEDATE, 10) as SAH_EFFECTIVEDATE, a.SAH_SALARY, a.MOV_CODE, b.MOV_NAME, SAH_POSITION, SAH_ORG
							 from		(
												PER_SALARYHIS a
												inner join PER_MOVMENT b on (a.MOV_CODE=b.MOV_CODE)
											)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(SAH_EFFECTIVEDATE, 10) ";
		} // end if
		$count_hist = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$SAH_AGE = floor(date_difference($SAH_EFFECTIVEDATE, $PER_BIRTHDATE, "year"));

			$SAH_SALARY = trim($data[SAH_SALARY]);
			$SAH_MOV_NAME = trim($data[MOV_NAME]);
			$SAH_POSITION = trim($data[SAH_POSITION]);
			$SAH_ORG = trim($data[SAH_ORG]);

			$arr_careerhis[$hist_count][type] = "SALARY";
			$arr_careerhis[$hist_count][effectivedate] = $SAH_EFFECTIVEDATE;
			$arr_careerhis[$hist_count][position] = $SAH_POSITION;
			$arr_careerhis[$hist_count][org_name] = $SAH_ORG;
			$arr_careerhis[$hist_count][age] = $SAH_AGE;
			$arr_careerhis[$hist_count][salary] = $SAH_SALARY;
			$arr_careerhis[$hist_count][note] = $SAH_MOV_NAME;

			$hist_count++;
		} // end while	
		
//		echo "<pre>"; print_r($arr_careerhis); echo "</pre>";
				
		$sort_arr[0]['name'] = "effectivedate";
		$sort_arr[0]['sort'] = "ASC";
		$sort_arr[0]['case'] = FALSE; //  Case sensitive
					
		array_sort($arr_careerhis, $sort_arr);

//		echo "<pre>"; print_r($arr_careerhis); echo "</pre>";

		for($x=0; $x<count($arr_careerhis); $x++){
			$HIST_TYPE = $arr_careerhis[$x][type];
			$EFFECTIVEDATE = show_date_format($arr_careerhis[$x][effectivedate],$DATE_DISPLAY);
			$AGE = $arr_careerhis[$x][age];
			$SALARY = $arr_careerhis[$x][salary];
			$NOTE = $arr_careerhis[$x][note];
			if($HIST_TYPE=="POSITION"){
				$POSITION = $arr_careerhis[$x][position];
				$ORG_NAME = $arr_careerhis[$x][org_name];
			} // end if
			
			$arr_content[$data_count][type] = "HISTORY";
			$arr_content[$data_count][hist_effectivedate] = $EFFECTIVEDATE;
			$arr_content[$data_count][hist_position] = $POSITION;
			$arr_content[$data_count][hist_org] = $ORG_NAME;
			$arr_content[$data_count][hist_age] = $AGE;
			$arr_content[$data_count][hist_salary] = number_format($SALARY);
			$arr_content[$data_count][hist_note] = $NOTE;
			
			$data_count++;
		} // end for

	} // end for
	
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
		
//		print_header();

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C", "L", "L", "C", "R", "L", "R", "R", "R", "R");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			if($REPORT_ORDER == "PERSON"){
				if($data_count > 0){
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "C", "", 1));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				} // end if
				
				$NAME = $arr_content[$data_count][name];
				$POSITION = $arr_content[$data_count][position];
				$ORG_NAME = $arr_content[$data_count][org_name];
				$DEPARTMENT_NAME = $arr_content[$data_count][department_name];
				$MINISTRY_NAME = $arr_content[$data_count][ministry_name];
				$PER_BIRTHDATE_D = $arr_content[$data_count][per_birthdate_d];
				$PER_BIRTHDATE_M = $arr_content[$data_count][per_birthdate_m];
				$PER_BIRTHDATE_Y = $arr_content[$data_count][per_birthdate_y];
				$COMPLETE_CONDDATE_D = $arr_content[$data_count][complete_conddate_d];
				$COMPLETE_CONDDATE_M = $arr_content[$data_count][complete_conddate_m];
				$COMPLETE_CONDDATE_Y = $arr_content[$data_count][complete_conddate_y];

				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
				$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
			
					
				print_header();
			}elseif($REPORT_ORDER == "HISTORY"){
				$EFFECTIVEDATE = $arr_content[$data_count][hist_effectivedate];
				$POSITION = $arr_content[$data_count][hist_position];
				$ORG_NAME = $arr_content[$data_count][hist_org];
				$AGE = $arr_content[$data_count][hist_age];
				$SALARY = $arr_content[$data_count][hist_salary];
				$NOTE = $arr_content[$data_count][hist_note];

				$arr_data = (array) null;
				$arr_data[] = $DEPARTMENT_NAME;
				$arr_data[] = "ไอดีกาด";
				$arr_data[] = $EFFECTIVEDATE;
				$arr_data[] = "ยศ";
				$arr_data[] = $POH_PL_NAME;
				$arr_data[] = $ORG_NAME;
				$arr_data[] = $AGE;
				$arr_data[] = $SALARY;
				$arr_data[] = $REMARK;
				$arr_data[] = $NOTE;
		
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
			} // end if
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
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"rpt_soc_xls5.xls\"");
	header("Content-Disposition: inline; filename=\"rpt_soc_xls5.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>