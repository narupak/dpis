<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004012_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
		if($search_edu) { $search_edu.= ' or '; }
			$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; } 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=i.PL_CODE";
		$i_name = "i.PL_NAME";
		$order_pl = "b.PL_CODE";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" สายงาน";
		$mgt_code ="b.PM_CODE";
		$select_mgt_code =",b.PM_CODE";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=i.PN_CODE";
		$i_name = "i.PN_NAME";
		$order_pl = "b.PN_CODE";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=i.EP_CODE";
		$i_name = "i.EP_NAME";
		$order_pl = "b.EP_CODE";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=i.TP_CODE";
		$i_name = "i.TP_NAME";
		$order_pl = "b.TP_CODE";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

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
			$arr_search_condition[] = "(trim($order_pl)='$line_search_code')";
			$list_type_text .= " $line_search_name";
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
	$report_title = "$DEPARTMENT_NAME||รายงานข้อมูล$PERSON_TYPE[$search_per_type] จำแนกตามวุฒิการศึกษา/วิชาเอก/สถานศึกษา";
	$report_code = "R0412";

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
		$ws_head_line1 = array("ลำดับที่", "ชื่อ - สกุล",	"ตำแหน่ง", "วุฒิการศึกษา", "วิชาเอก", "สถานศึกษา");
		$ws_colmerge_line1 = array(0,0,0,0,0,0);
		$ws_border_line1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(8,30,30,15,30,40);
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

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, h.PN_NAME, a.PER_NAME, a.PER_SURNAME, $i_name as PL_NAME, a.LEVEL_NO, k.POSITION_LEVEL, 
											e.EN_NAME, e.EN_SHORTNAME, f.EM_NAME, g.INS_NAME, d.EDU_INSTITUTE  $select_type_code  $select_mgt_code
						 from			(
												(
													(
														(
															(
																(
																	(
																		(	
																			PER_PERSONAL a 
																			left join $position_table b on $position_join 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
																) left join PER_EDUCNAME e on (d.EN_CODE=e.EN_CODE)
															) left join PER_EDUCMAJOR f on (d.EM_CODE=f.EM_CODE)
														) left join PER_INSTITUTE g on (d.INS_CODE=g.INS_CODE)
													) left join PER_PRENAME h on (a.PN_CODE=h.PN_CODE)
												) left join $line_table i on $line_join
											) left join PER_LEVEL k on (a.LEVEL_NO=k.LEVEL_NO)
											$search_condition and ($search_edu)
						 order by		a.PER_NAME, $order_pl, a.LEVEL_NO, a.PER_ID, d.EDU_SEQ ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, h.PN_NAME, a.PER_NAME, a.PER_SURNAME, $i_name as PL_NAME, a.LEVEL_NO, k.POSITION_LEVEL, 
											e.EN_NAME, e.EN_SHORTNAME, f.EM_NAME, g.INS_NAME, d.EDU_INSTITUTE  $select_type_code  $select_mgt_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_EDUCNAME e, PER_EDUCMAJOR f, PER_INSTITUTE g, PER_PRENAME h, $line_table i, PER_LEVEL k
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PER_ID=d.PER_ID(+) and d.EN_CODE=e.EN_CODE(+) and d.EM_CODE=f.EM_CODE(+) and ($search_edu) and d.INS_CODE=g.INS_CODE(+) and a.LEVEL_NO=k.LEVEL_NO(+)
											and a.PN_CODE=h.PN_CODE(+) and $line_join(+) 
											$search_condition
						 order by		a.PER_NAME, $order_pl, a.LEVEL_NO, a.PER_ID, d.EDU_SEQ ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, h.PN_NAME, a.PER_NAME, a.PER_SURNAME, $i_name as PL_NAME, a.LEVEL_NO, k.POSITION_LEVEL, 
											e.EN_NAME, e.EN_SHORTNAME, f.EM_NAME, g.INS_NAME, d.EDU_INSTITUTE  $select_type_code  $select_mgt_code
						 from			(
												(
													(
														(
															(
																(
																	(
																		(	
																			PER_PERSONAL a 
																			left join $position_table b on $position_join 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
																) left join PER_EDUCNAME e on (d.EN_CODE=e.EN_CODE)
															) left join PER_EDUCMAJOR f on (d.EM_CODE=f.EM_CODE)
														) left join PER_INSTITUTE g on (d.INS_CODE=g.INS_CODE)
													) left join PER_PRENAME h on (a.PN_CODE=h.PN_CODE)
												) left join $line_table i on $line_join
											) left join PER_LEVEL k on (a.LEVEL_NO=k.LEVEL_NO)
											$search_condition and ($search_edu)
						 order by		a.PER_NAME, $order_pl, a.LEVEL_NO, a.PER_ID, d.EDU_SEQ ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$PER_ID = -1;
	while($data = $db_dpis->get_array()){
		if($PER_ID != $data[PER_ID]){
			$data_row++;

			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PL_NAME = trim($data[PL_NAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
			if(trim($mgt_code)){
				$PM_CODE = trim($data[PM_CODE]);
				$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PM_NAME = trim($data2[PM_NAME]);
			}
			$TMP_ORG_NAME = $data[ORG_NAME];

			$ORG_ID_1 = $data[ORG_ID_1];
			$TMP_ORG_NAME_1 = "";
			if($ORG_ID_1){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			 $db_dpis2->send_cmd($cmd);
			 $data2 = $db_dpis2->get_array();
			 $TMP_ORG_NAME_1 = $data2[ORG_NAME];
			}
			if ($PM_NAME=="ผู้ว่าราชการจังหวัด" || $PM_NAME=="รองผู้ว่าราชการจังหวัด" || $PM_NAME=="ปลัดจังหวัด" || $PM_NAME=="หัวหน้าสำนักงานจังหวัด") {
				$PM_NAME .= $TMP_ORG_NAME;
				$PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $PM_NAME); 
				$PM_NAME = str_replace("สำนักงานจังหวัดสำนักงานจังหวัด", "สำนักงานจังหวัด", $PM_NAME); 
			} elseif ($PM_NAME=="นายอำเภอ") {
				$PM_NAME .= $TMP_ORG_NAME_1;
				$PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $PM_NAME); 
			}
			if(trim($type_code)){
				$PT_CODE = trim($data[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}

			$arr_content[$data_count][order] = "$data_row.";
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME ) . (trim($PM_NAME)?")":"");
		}else{
			$arr_content[$data_count][order] = "";
			$arr_content[$data_count][name] = "";
			$arr_content[$data_count][position] = "";
		} // end if

		$EN_NAME = trim($data[EN_SHORTNAME]);
		if (!$EN_NAME) $EN_NAME = trim($data[EN_NAME]);
		$EM_NAME = trim($data[EM_NAME]);
		$INS_NAME = trim($data[INS_NAME]);
		if (!$INS_NAME) $INS_NAME = trim($data[EDU_INSTITUTE]);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][educname] = $EN_NAME;
		$arr_content[$data_count][educmajor] = $EM_NAME;
		$arr_content[$data_count][institute] = $INS_NAME;

		$data_count++;
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B");
			$wsdata_align_1 = array("C","L", "L","L", "L", "L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][educname];
			$NAME_4 = $arr_content[$data_count][educmajor];
			$NAME_5 = $arr_content[$data_count][institute];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
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
					if($REPORT_ORDER=="COUNTRY")
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else if($REPORT_ORDER=="CONTENT")
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