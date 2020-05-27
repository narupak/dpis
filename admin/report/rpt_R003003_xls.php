<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");
	
	include ("rpt_R003003_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if

	if(!trim($RPTORD_LIST)) $RPTORD_LIST = "ORG";
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	$arr_search_condition[] = "(f.MOV_SUB_TYPE = 2)";

	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ที่ย้ายระหว่าง$ORG_TITLE ในปีงบประมาณ $search_budget_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0303";

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
		$ws_head_line1 = array("$ORG_TITLE เดิม","$ORG_TITLE ที่ย้าย","<**1**>จำนวน","<**1**>","<**1**>");
		$ws_head_line2 = array("","","ชาย","หญิง","รวม");
		$ws_colmerge_line1 = array(0,0,1,1,1);
		$ws_colmerge_line2 = array(0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRBL","TRBL","TRBL");
		$ws_border_line2 = array("LBR","RBL","TRBL","TRBL","TRBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C");
		$ws_width = array(40,40,5,5,5);
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
		global $heading_name, $ORG_TITLE;
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
				$worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, e.ORG_ID_3, e.POH_ORG3, e.POH_EFFECTIVEDATE
						 from		(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
										) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
											$search_condition
						 order by		a.PER_ID, e.POH_ORG3 ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, e.ORG_ID_3, e.POH_ORG3, e.POH_EFFECTIVEDATE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e, PER_MOVMENT f
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=e.PER_ID(+) and e.MOV_CODE=f.MOV_CODE(+)
											$search_condition
						 order by		a.PER_ID, e.POH_ORG3 ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, e.ORG_ID_3, e.POH_ORG3, e.POH_EFFECTIVEDATE
						 from		(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
										) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
											$search_condition
						 order by		a.PER_ID, e.POH_ORG3 ";
	} // end if
	if($select_org_structure==1){ 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = 0;
	while($data = $db_dpis->get_array()){
		$PER_ID = $data[PER_ID];
		$PER_GENDER = $data[PER_GENDER];
		$POH_EFFECTIVEDATE = substr($data[POH_EFFECTIVEDATE], 0, 10);
		$NEW_ORG_ID = $data[ORG_ID_3];
		$NEW_ORG_NAME = $data[POH_ORG3];
		
		if($DPISDB=="odbc")
			$cmd = " select 	 a.ORG_ID_3, a.POH_ORG3
							 from 		 PER_POSITIONHIS a
							 				 left join PER_ORG b on (a.POH_ORG3=b.ORG_NAME)
							 where 	 a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
							 order by a.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="oci8")
			$cmd = " select 	 a.ORG_ID_3, a.POH_ORG3
							 from 		 PER_POSITIONHIS a, PER_ORG b
							 where 	 a.POH_ORG3=b.ORG_NAME(+) and a.PER_ID=$PER_ID and SUBSTR(trim(a.POH_EFFECTIVEDATE), 1, 10) < '$POH_EFFECTIVEDATE' 
							 order by a.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="mysql")
			$cmd = " select 	 a.ORG_ID_3, a.POH_ORG3
					 from 		 PER_POSITIONHIS a
									 left join PER_ORG b on (a.POH_ORG3=b.ORG_NAME)
					 where 	 a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
					 order by a.POH_EFFECTIVEDATE desc ";
		if($select_org_structure==1){ 
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_ID = $data2[ORG_ID_3];
		$OLD_ORG_NAME = $data2[POH_ORG3];

		$POH_MOV_CODE = trim($data[MOV_CODE]);
		if(($OLD_ORG_NAME && $OLD_ORG_NAME != $NEW_ORG_NAME) && ((in_array("PER_ORG", $list_type) && $search_org_name==$OLD_ORG_NAME) || !in_array("PER_ORG", $list_type))){
			$key = "$OLD_ORG_NAME:$NEW_ORG_NAME";
			if(!array_key_exists($key, $arr_content)){ 
				$arr_content[$key][old_org] = $OLD_ORG_NAME;
				$arr_content[$key][new_org] = $NEW_ORG_NAME;
				$arr_content[$key][count_1] = 0;
				$arr_content[$key][count_2] = 0;
			} // end if

			if($PER_GENDER==1){ 
				$arr_content[$key][count_1]++;
				$GRAND_TOTAL_1++;
			}elseif($PER_GENDER==2){ 
				$arr_content[$key][count_2]++;
				$GRAND_TOTAL_2++;
			} // end if
		} // end if
	} // end while

	ksort($arr_content);
	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2;
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$count_data = count($arr_content);
	
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B");
			$wsdata_align_1 = array("L","R","R","R","R");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
//		for($data_count=0; $data_count<count($arr_content); $data_count++){
		$data_count = 0;
		foreach($arr_content as $key => $value){
			$NAME_1 = $arr_content[$key][old_org];
			$NAME_2 = $arr_content[$key][new_org];
			$COUNT_1 = $arr_content[$key][count_1];
			$COUNT_2 = $arr_content[$key][count_2];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_2;

			$arr_data = (array) null;
			$arr_data[] = $NAME_1;
			$arr_data[] = $NAME_2;
			$arr_data[] = $COUNT_1;
			$arr_data[] = $COUNT_2;
			$arr_data[] = $COUNT_TOTAL;

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
			$data_count++;
		} // end for

		$arr_data = (array) null;
		$arr_data[] = "<**1**>รวม";
		$arr_data[] = "<**1**>รวม";
		$arr_data[] = $GRAND_TOTAL_1;
		$arr_data[] = $GRAND_TOTAL_2;
		$arr_data[] = $GRAND_TOTAL;

		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
				else $ndata = $arr_data[$arr_column_map[$i]];
				$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border, $merge));
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