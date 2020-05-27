<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R006006_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select 	PV_CODE
					 from 		PER_ORG
					 where	ORG_ID=$DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PV_CODE = $data[PV_CODE];
	if($PV_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_PROVINCE = $data[PV_NAME];
	} // end if	
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$search_per_type = 1;
	$search_per_status = 1;
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

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

	$report_title = "บัญชีสรุปจำนวนข้าราชการ||สำหรับการเลื่อนเงินเดือน 1 ตุลาคม ". ($search_budget_year - 1) ."||$DEPARTMENT_NAME  $MINISTRY_NAME||ประจำปีงบประมาณ $search_budget_year||หน่วยงานผู้เบิก $DEPARTMENT_NAME  จังหวัด$DEPARTMENT_PROVINCE";
	$report_title  = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0606";

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
		$ws_head_line1 = array("กลุ่มข้าราชการ","จำนวนข้าราชการ","","หมายเหตุ");
		$ws_head_line2 = array("ระดับ","1 ต.ค.","ตัวอักษร","");
		$ws_colmerge_line1 = array(0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C");
		$ws_width = array(25,8,27,40);
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
	
	function print_header($search_level_min, $search_level_max){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_fontfmt_line1, $ws_fontfmt_line2;
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
		$worksheet->set_column(0, 0, 25);
		$worksheet->set_column(1, 1, 8);
		$worksheet->set_column(2, 2, 27);
		$worksheet->set_column(3, 3, 40);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "กลุ่มข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "จำนวนข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "1 ต.ค.", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ตัวอักษร", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
*/
	} // function		

	$cmd = " select PER_ID from PER_SALARYHIS where SAH_KF_YEAR='$search_budget_year' ";
	$count_data = $db_dpis->send_cmd($cmd);
	
	$data_count = 0;
	$search_level = array( "O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
	for ( $i=0; $i<count($search_level); $i++ ) { 
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$search_level[$i]' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$LEVEL_NAME = $data[LEVEL_NAME];
			$arr_content[$data_count][type] = "HEADER";
			$arr_content[$data_count][name] = $LEVEL_NAME;
			$data_count++;

			$cmd = " select count(a.PER_ID) as PROMOTED_PERSON 
							  from PER_PERSONAL a, PER_SALARYHIS b 
							  where a.PER_ID=b.PER_ID and a.LEVEL_NO = '$search_level[$i]' and 
							  b.SAH_KF_YEAR='$search_budget_year' and b.SAH_PERCENT_UP < 2 ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "น้อยกว่า 2%";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			$arr_content[$data_count][remark] = $remark[$search_level[$i]];
			$data_count++;

			$cmd = " select count(a.PER_ID) as PROMOTED_PERSON 
							  from PER_PERSONAL a, PER_SALARYHIS b 
							  where a.PER_ID=b.PER_ID and a.LEVEL_NO = '$search_level[$i]' and 
							  b.SAH_KF_YEAR='$search_budget_year' and b.SAH_PERCENT_UP >= 2 and b.SAH_PERCENT_UP < 4 ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "ตั้งแต่ 2% แต่ไม่ถึง 4%";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			$data_count++;

			$cmd = " select count(a.PER_ID) as PROMOTED_PERSON 
							  from PER_PERSONAL a, PER_SALARYHIS b 
							  where a.PER_ID=b.PER_ID and a.LEVEL_NO = '$search_level[$i]' and 
							  b.SAH_KF_YEAR='$search_budget_year' and b.SAH_PERCENT_UP >= 4 and b.SAH_PERCENT_UP < 6 ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "ตั้งแต่ 4% แต่ไม่ถึง 6%";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			$data_count++;

			$cmd = " select count(a.PER_ID) as PROMOTED_PERSON 
							  from PER_PERSONAL a, PER_SALARYHIS b 
							  where a.PER_ID=b.PER_ID and a.LEVEL_NO = '$search_level[$i]' and 
							  b.SAH_KF_YEAR='$search_budget_year' and b.SAH_PERCENT_UP = 6 ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "6%";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			$data_count++;	
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
		
		print_header();

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B");
			$wsdata_align_1 = array("L","C","L","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_PERSON = $arr_content[$data_count][count_person];
			$COUNT_SPEECH = $arr_content[$data_count][count_speech];
			$REMARK = $arr_content[$data_count][remark];

			$arr_data = (array) null;
			$arr_data[] = $NAME;
			$arr_data[] = $COUNT_PERSON;
			$arr_data[] =$COUNT_SPEECH;
			if(($data_count % 4) == 1){
		    	$arr_data[] = $REMARK;
			} else $arr_data[] ="";

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if($REPORT_ORDER == "DETAIL")
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
/*
			if($REPORT_ORDER == "DETAIL"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$COUNT_PERSON", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$COUNT_SPEECH", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				if(($data_count % 4) == 1) $worksheet->write_string($xlsRow, 3, "$REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				else $worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
			} // end if
*/
		} // end for				

		if($data_count > 0){ 
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "ได้ตรวจสอบถูกต้องแล้ว", set_format("xlsFmtTableDetail", "", "C", "", 0));

			for($i=0; $i<3; $i++){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			} // end for
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "( $confirm_name )", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "ตำแหน่ง  $confirm_position", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$show_budget_year = $search_budget_year - 1;
			$worksheet->write_string($xlsRow, 3, "วันที่                         ตุลาคม ". (($NUMBER_DISPLAY==2)?convert2thaidigit($show_budget_year):$show_budget_year), set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "กรมบัญชีกลางตรวจสอบแล้ว", set_format("xlsFmtTableDetail", "", "C", "", 0));

			for($i=0; $i<3; $i++){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			} // end for

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "( ............................................................ )", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "ตำแหน่ง ............................................................ ", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "วันที่ ............................................. ", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
		} // end if
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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