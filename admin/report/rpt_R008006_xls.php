<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");

	include ("rpt_R008006_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
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
				$select_list .= "h.ORG_ID_REF as MINISTRY_ID";	
			
				if($order_by) $order_by .= ", ";
				$order_by .= "h.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "h.ORG_SEQ_NO, h.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "h.ORG_SEQ_NO, h.ORG_CODE, a.DEPARTMENT_ID ";

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

	//$search_per_status[] = 1; /*เดิม*/
        /*Release 5.1.0.3 Begin*/
        if(count($search_per_status)==0){
            $search_per_status[] = -1; //ไม่มีการเลือก
        }
        /*Release 5.1.0.3 End*/

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(d.PUN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(d.PUN_STARTDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(d.PUN_STARTDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(d.PUN_STARTDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(d.PUN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(d.PUN_STARTDATE), 10) < '".($search_budget_year - 543)."-10-01')";

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
	$report_title = "$DEPARTMENT_NAME||สถิติฐานความผิดทางวินัย จำแนกตามระดับตำแหน่ง||ในปีงบประมาณ $search_budget_year";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0806";

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
		$ws_head_line1 = (array) null;
		$ws_head_line2 = (array) null;
		$ws_head_line1[0] = "$heading_name";
		$ws_head_line2[0] = "";
		$ws_colmerge_line1[0] = 0;
		$ws_colmerge_line2[0] = 0;
		$ws_border_line1[0] = "TLBR";
		$ws_fontfmt_line1[0] = "B";
		$ws_headalign_line1[0] = "C";
		$ws_width[0] = 70;
		$cnt_level = count($ARR_LEVEL_SHORTNAME);
		for($i=0; $i<cnt_level; $i++){ 
			$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
			$ws_head_line1[$i+1] = "<**1**>ระดับ";
			$ws_head_line2[$i+1] = "$tmp_level_shortname";
			$ws_colmerge_line1[$i+1] = 1;
			$ws_colmerge_line2[$i+1] = 0;
			$ws_border_line1[$i+1] = "T";
			$ws_border_line2[$i+1] = "TLBR";
			$ws_fontfmt_line1[$i+1] = "B";
			$ws_headalign_line1[$i+1] = "C";
			$ws_width[$i+1] = 5;
		} // end for	
		$ws_head_line1[$cnt_level+1] = "<**2**>รวม";
		$ws_head_line1[$cnt_level+2] = "<**2**>รวม";
		$ws_head_line2[$cnt_level+1] = "คน";
		$ws_head_line2[$cnt_level+2] = "ร้อยละ";
		$ws_colmerge_line1[$cnt_level+1] = 1;
		$ws_colmerge_line2[$cnt_level+1] = 0;
		$ws_colmerge_line1[$cnt_level+2] = 1;
		$ws_colmerge_line2[$cnt_level+2] = 0;
		$ws_border_line1[$cnt_level+1] = "TLR";
		$ws_border_line1[$cnt_level+2] = "LBR";
		$ws_border_line2[$cnt_level+1] = "TLR";
		$ws_border_line2[$cnt_level+2] = "LBR";
		$ws_fontfmt_line1[$cnt_level+1] = "B";
		$ws_fontfmt_line1[$cnt_level+2] = "B";
		$ws_headalign_line1[$cnt_level+1] = "C";
		$ws_headalign_line1[$cnt_level+2] = "C";
		$ws_width[$cnt_level+1] = 8;
		$ws_width[$cnt_level+2] = 8;
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
		global $ws_head_line1, $ws_head_line2;
		global $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2;
		global $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

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
/*		
		$worksheet->set_column(0, 0, 70);
		$worksheet->set_column(1, 11, 5);
		$worksheet->set_column(12, 12, 8);
		$worksheet->set_column(13, 13, 8);

		$worksheet->write($xlsRow, 0, "$heading_name", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 12, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, "$i", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "คน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 13, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
*/
	} // function		

	function count_person($cr_code, $level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type, $select_org_structure;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);

		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(	
								 					(
														(
															(
																PER_PERSONAL a 
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e, PER_ORG h
								 where		a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) and d.CRD_CODE=e.CRD_CODE(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
													and trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(	
								 					(
														(
															(
																PER_PERSONAL a 
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			} // end if
		}elseif($search_per_type==2){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(	
								 					(
														(
															(
																PER_PERSONAL a 
																inner join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e, PER_ORG h
								 where		a.POEM_ID=b.POEM_ID and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) and d.CRD_CODE=e.CRD_CODE(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
													and trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(	
								 					(
														(
															(
																PER_PERSONAL a 
																inner join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			} // end if
		} elseif($search_per_type==3){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(	
								 					(
														(
															(
																PER_PERSONAL a 
																inner join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e, PER_ORG h
								 where		a.POEMS_ID=b.POEMS_ID and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) and d.CRD_CODE=e.CRD_CODE(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
													and trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(	
								 					(
														(
															(
																PER_PERSONAL a 
																inner join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			} // end if
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(h.ORG_ID_REF = $MINISTRY_ID)";
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
	
	if($search_per_type==1){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from		(	
												(
													(
														(
															(
																PER_PERSONAL a 
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
											) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e, PER_CRIME f, PER_ORG h
							 where		a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) 
							 					and d.CRD_CODE=e.CRD_CODE(+) and e.CR_CODE=f.CR_CODE(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from		(	
												(
													(
														(
															(
																PER_PERSONAL a 
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
											) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		} // end if
	}elseif($search_per_type==2){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																inner join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e, PER_CRIME f, PER_ORG h
							 where		a.POEM_ID=b.POEM_ID and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) 
							 					and d.CRD_CODE=e.CRD_CODE(+) and e.CR_CODE=f.CR_CODE(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																inner join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}
	} elseif($search_per_type==3){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																inner join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e, PER_CRIME f, PER_ORG h
							 where		a.POEMS_ID=b.POEMS_ID and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) 
							 					and d.CRD_CODE=e.CRD_CODE(+) and e.CR_CODE=f.CR_CODE(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																inner join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	unset($LEVEL_TOTAL);
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
						
						if($rpt_order_index == (count($arr_rpt_order) - 1)){
							if($CR_CODE != trim($data[CR_CODE])){
								$CR_CODE = trim($data[CR_CODE]);
								$CR_NAME = trim($data[CR_NAME]);
								
								$arr_content[$data_count][type] = "CONTENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $CR_NAME;
								for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++){ 
									$arr_content[$data_count]["level_".$i] = count_person("$CR_CODE", $i, $search_condition, $addition_condition);
									if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
								} // end for
								
								$data_count++;
							} // end if
						} // end if
					} // end if
				break;			
				
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
						
						if($rpt_order_index == (count($arr_rpt_order) - 1)){
							if($CR_CODE != trim($data[CR_CODE])){
								$CR_CODE = trim($data[CR_CODE]);
								$CR_NAME = trim($data[CR_NAME]);
								
								$arr_content[$data_count][type] = "CONTENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $CR_NAME;
								for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++){ 
									$arr_content[$data_count]["level_".$i] = count_person("$CR_CODE", $i, $search_condition, $addition_condition);
									if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
								} // end for
								
								$data_count++;
							} // end if
						} // end if
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
						$data_count++;
						
						if($rpt_order_index == (count($arr_rpt_order) - 1)){
							if($CR_CODE != trim($data[CR_CODE])){
								$CR_CODE = trim($data[CR_CODE]);
								$CR_NAME = trim($data[CR_NAME]);
								
								$arr_content[$data_count][type] = "CONTENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $CR_NAME;
								for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++){ 
									$arr_content[$data_count]["level_".$i] = count_person("$CR_CODE", $i, $search_condition, $addition_condition);
									if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
								} // end for
								
								$data_count++;
							} // end if
						} // end if
					} // end if
				break;		
			} // end switch case
		} // end for
	} // end while
	for($ii=1; $ii <=count($ARR_LEVEL_SHORTNAME); $ii++) {
		if ($arr_column_sel[$arr_column_map[$ii]]==1) // 1 = แสดง column นี้
			$GRAND_TOTAL += $LEVEL_TOTAL[$ii];
	}
//	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
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
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //if($company_name){

		print_header();		

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1[0] = "B";
			$wsdata_fontfmt_2[0] = "";
			$wsdata_align_1[0] = "C";
			$wsdata_border_1[0] = "TLBR";
			$wsdata_colmerge_1[0] = 0;
			$cnt_level = count($ARR_LEVEL_SHORTNAME);
			for($i=0; $i<cnt_level; $i++){ 
				$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
				$wsdata_fontfmt_1[$i+1] = "B";
				$wsdata_fontfmt_2[$i+1] = "";
				$wsdata_align_1[$i+1] = "C";
				$wsdata_border_1[$i+1] = "TLBR";
				$wsdata_colmerge_1[$i+1] = 0;
			} // end for	
			$wsdata_fontfmt_1[$cnt_level+1] = "B";
			$wsdata_fontfmt_1[$cnt_level+2] = "B";
			$wsdata_fontfmt_2[$cnt_level+1] = "";
			$wsdata_fontfmt_2[$cnt_level+2] = "";
			$wsdata_align_1[$cnt_level+1] = "C";
			$wsdata_align_1[$cnt_level+2] = "C";
			$wsdata_border_1[$cnt_level+1] = "TLBR";
			$wsdata_border_1[$cnt_level+2] = "TLBR";
			$wsdata_colmerge_1[$cnt_level+1] = 0;
			$wsdata_colmerge_1[$cnt_level+2] = 0;
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			unset($COUNT_LEVEL);
			$COUNT_TOTAL = 0;
			for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++) {
				$COUNT_LEVEL[$i] = $arr_content[$data_count]["level_".$i];
				if ($arr_column_sel[$arr_column_map[$i]]==1) // 1 = แสดง column นี้
					$COUNT_TOTAL += $COUNT_LEVEL[$i];
			}
//			$COUNT_TOTAL = array_sum($COUNT_LEVEL);
			if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

			if($REPORT_ORDER == "ORG"){
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++)
					$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				for($i=1; $i<=count($ARR_LEVEL_SHORTNAME); $i++)
					$arr_data[] = $COUNT_LEVEL[$i];
				$arr_data[] = $COUNT_TOTAL;
				$arr_data[] = $PERCENT_TOTAL;
			} // end if

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
/*
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				for($i=1; $i<=11; $i++) 
					$worksheet->write($xlsRow, $i, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}else{
				if($REPORT_ORDER == "ORG"){
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					for($i=1; $i<=11; $i++) 
						$worksheet->write($xlsRow, $i, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				}elseif($REPORT_ORDER == "CONTENT"){
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					for($i=1; $i<=11; $i++) 
						$worksheet->write($xlsRow, $i, ($COUNT_LEVEL[$i]?number_format($COUNT_LEVEL[$i]):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 12, ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 13, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				} // end if
			} // end if
*/
		} // end for
				
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$arr_data = (array) null;
		$arr_data[] = "รวม";
		$COUNT_GRAND_TOTAL = 0;
		for($ii=1; $ii <= count($ARR_LEVEL_SHORTNAME); $ii++) {
			if ($arr_column_sel[$arr_column_map[$ii]]==1) {// 1 = แสดง column นี้
				$COUNT_GRAND_TOTAL += $LEVEL_TOTAL[$ii];
				$arr_data[] = $LEVEL_TOTAL[$ii];
			}
		}
		$arr_data[] = $COUNT_GRAND_TOTAL;
		$arr_data[] = $COUNT_GRAND_TOTAL/$GRAND_TOTAL*100;

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
/*
		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		for($i=1; $i<=11; $i++) 
			$worksheet->write($xlsRow, $i, ($LEVEL_TOTAL[$i]?number_format($LEVEL_TOTAL[$i]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 12, ($GRAND_TOTAL?number_format($GRAND_TOTAL):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 13, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
*/
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
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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