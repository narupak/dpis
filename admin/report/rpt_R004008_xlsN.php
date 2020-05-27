<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004008_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($search_en_ct_code) $arr_search_condition[] = "(trim(d.CT_CODE)='$search_en_ct_code')";

	$arr_temp = explode("/", $search_date_min);
	$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	$show_date_min = show_date_format($search_date_min, $DATE_DISPLAY);
	$show_date_min = (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date_min):$show_date_min);
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(d.TRN_STARTDATE), 10) >= '$search_date_min')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(d.TRN_STARTDATE), 1, 10) >= '$search_date_min')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(d.TRN_STARTDATE), 10) >= '$search_date_min')";

	$arr_temp = explode("/", $search_date_max);
	$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	$show_date_max = show_date_format($search_date_max, $DATE_DISPLAY);
	$show_date_max = (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date_max):$show_date_max);
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(d.TRN_ENDDATE), 10) <= '$search_date_max')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(d.TRN_ENDDATE), 1, 10) <= '$search_date_max')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(d.TRN_ENDDATE), 10) <= '$search_date_max')";

	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ลาไปศึกษา ฝึกอบรม สัมมนา ดูงาน".($search_en_ct_code?" ณ ประเทศ$search_en_ct_name":"")."||ตั้งแต่วันที่ $show_date_min ถึงวันที่ $show_date_max";
	$report_code = "R0408";

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
		$ws_head_line1[0] = "ประเทศ";
		$ws_head_line2[0] = "";
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$tmp_level_shortname = trim(str_replace("*Enter*","",$ARR_LEVEL_SHORTNAME[$i]));
			$ws_head_line1[$i+1] = "<**1**>ระดับตำแหน่ง";
			$ws_head_line2[$i+1] = "$tmp_level_shortname";
		} // end for
		$ws_head_line1[] = "รวม";
		$ws_head_line2[] = "";
		if ($ISCS_FLAG==1)
			$ws_colmerge_line1 = array(0,1,1,1,1,1,1,0);
		else
			$ws_colmerge_line1 = array(0,1,1,1,1,1,1,1,1,1,1,1,1,1,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		if ($ISCS_FLAG==1) {
			$ws_border_line1 = array("TLR","TLB","TB","TB","TB","TB","TBR","TLR");
			$ws_border_line2 = array("LBR","BL","BL","BL","BL","BL","LBR","RBL");
		} else {
			$ws_border_line1 = array("TLR","TLB","TB","TB","TB","TB","TB","TB","TB","TB","TB","TB","TB","TBR","TLR");
			$ws_border_line2 = array("LBR","BL","BL","BL","BL","BL","BL","BL","BL","BL","BL","BL","BL","LBR","RBL");
		}
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C");
		if ($ISCS_FLAG==1)
			$ws_width = array(80,10,10,10,10,10,10,12);
		else
			$ws_width = array(80,8,8,8,8,8,8,8,8,8,8,8,8,8,10);
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
		global $ARR_LEVEL_NO,$ARR_LEVEL_SHORTNAME;
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
/*		
		$worksheet->set_column(0, 0, 50);
		$worksheet->set_column(1, count($ARR_LEVEL_NO), 8);
		$worksheet->set_column(count($ARR_LEVEL_NO)+1, count($ARR_LEVEL_NO)+1, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ประเทศ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		for($i=2; $i<=count($ARR_LEVEL_NO); $i++){
			$worksheet->write($xlsRow, $i, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		} // loop for
		$worksheet->write($xlsRow, (count($ARR_LEVEL_NO)+1), "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
			$worksheet->write($xlsRow,$i+1 , "$tmp_level_shortname", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		}
		$worksheet->write($xlsRow, (count($ARR_LEVEL_NO)+1), "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
*/
	} // function		

	function count_scholar($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type,$select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
							 where		d.SC_TYPE=1 and trim(a.LEVEL_NO) = '$level_no'
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
												and a.PER_ID=d.PER_ID and d.INS_CODE=e.INS_CODE(+) and d.SC_TYPE=1 and trim(a.LEVEL_NO) = '$level_no'
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
							 where		d.SC_TYPE=1 and trim(a.LEVEL_NO) = '$level_no'
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
							 group by	a.PER_ID ";
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
	
	function count_train($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type,$select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
							 where		trim(a.LEVEL_NO) = '$level_no'
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
												and a.PER_ID=d.PER_ID and trim(a.LEVEL_NO) = '$level_no'
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
							 where		trim(a.LEVEL_NO) = '$level_no'
												$search_condition
							 group by	a.PER_ID ";
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

	if($DPISDB=="odbc"){
		$cmd = " select			distinct d.CT_CODE
						 from			(
												(	
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
											$search_condition
						 order by		d.CT_CODE ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct d.CT_CODE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PER_ID=d.PER_ID
											$search_condition
						 order by		d.CT_CODE ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct d.CT_CODE
						 from			(
												(	
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
											$search_condition
						 order by		d.CT_CODE ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$CT_CODE = trim($data[CT_CODE]);
		$arr_country[] = $CT_CODE;
	} // end while

//	echo "<pre>"; print_r($arr_country); echo "</pre>";

	$search_condition = str_replace(" where ", " and ", $search_condition);
	if($DPISDB=="odbc"){
		$cmd = " select			distinct e.CT_CODE
						 from			(
												(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
											) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
						where			d.SC_TYPE=1
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
						 order by		e.CT_CODE ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			distinct e.CT_CODE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											and a.PER_ID=d.PER_ID and d.SC_TYPE=1 and d.INS_CODE=e.INS_CODE(+)
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
						 order by		e.CT_CODE ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct e.CT_CODE
						 from			(
												(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
											) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
						where			d.SC_TYPE=1
											". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
						 order by		e.CT_CODE ";
	} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$CT_CODE = trim($data[CT_CODE]);
		$arr_country[] = $CT_CODE;
	} // end while

//	echo "<pre>"; print_r($arr_country); echo "</pre>";

	array_unique($arr_country);
	$count_data = count($arr_country);
//	echo "<pre>"; print_r($arr_country); echo "</pre>";

	$data_count = 0;
	foreach($arr_country as $CT_CODE){
//		$CT_CODE = trim($data[CT_CODE]);
		if($CT_CODE == ""){
			$CT_NAME = "[ไม่ระบุประเทศ]";

			$addition_condition = "(trim(e.CT_CODE) = '$CT_CODE' or e.CT_CODE is null)";
		}else{
			$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$CT_NAME = $data2[CT_NAME];

			$addition_condition = "(trim(e.CT_CODE) = '$CT_CODE')";
		} // end if

		$arr_content[$data_count][type] = "COUNTRY";
		$arr_content[$data_count][name] = "ประเทศ$CT_NAME";
//		$arr_content[$data_count][count_1] = count_person($search_condition, $addition_condition);
//		$GRAND_TOTAL += $arr_content[$data_count][count_1];

		$search_condition = str_replace("d.CT_CODE", "e.CT_CODE", $search_condition);
		$search_condition = str_replace("d.TRN_STARTDATE", "d.SC_STARTDATE", $search_condition);
		$search_condition = str_replace("d.TRN_ENDDATE", "d.SC_ENDDATE", $search_condition);

		$data_count++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "- ศึกษา";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content[$data_count]["count_".$tmp_level_no] = count_scholar(str_pad($tmp_level_no, 2, "0", STR_PAD_LEFT), $search_condition, $addition_condition);
		} // end for
		$addition_condition = str_replace("e.CT_CODE", "d.CT_CODE", $addition_condition);
		$search_condition = str_replace("e.CT_CODE", "d.CT_CODE", $search_condition);
		$search_condition = str_replace("d.SC_STARTDATE", "d.TRN_STARTDATE", $search_condition);
		$search_condition = str_replace("d.SC_ENDDATE", "d.TRN_ENDDATE", $search_condition);

		$data_count++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "- ฝึกอบรม / สัมมนา";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content[$data_count]["count_".$tmp_level_no] = count_train(str_pad($tmp_level_no, 2, "0", STR_PAD_LEFT), $search_condition, ($addition_condition . " and (d.TRN_TYPE in (1, 3))"));
		} // end for

		$data_count++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "- ดูงาน";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content[$data_count]["count_".$tmp_level_no] = count_train(str_pad($tmp_level_no, 2, "0", STR_PAD_LEFT), $search_condition, ($addition_condition . " and (d.TRN_TYPE in (2))"));
		} // end for

		$data_count++;
	} // end foreach
	
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","L","C","L","L","C","L","C","C","C","C","C","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_TOTAL = 0;
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				${"COUNT_".$tmp_level_no} = $arr_content[$data_count]["count_".$tmp_level_no];
				$COUNT_TOTAL += ${"COUNT_".$tmp_level_no};
			} // end for

			$arr_data = (array) null;
			if($REPORT_ORDER=="COUNTRY"){
				$arr_data[] = $NAME;
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$arr_data[] = "";
				} // end for
				$arr_data[] = "";
			}elseif($REPORT_ORDER=="CONTENT"){
				$arr_data[] = $NAME;
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$arr_data[] = ${"COUNT_".$tmp_level_no};
				} // end for
				$arr_data[] =  $COUNT_TOTAL;
			} // end if

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
		for($i=1; $i<=count($ARR_LEVEL_NO)+1; $i++) $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		//$worksheet->write($xlsRow, (count($ARR_LEVEL_NO)+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
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