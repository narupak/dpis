<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$NUMBER_DISPLAY = 1;
	include ("rpt_function.php");

	include ("rpt_data_mgt_competency_assessment_002_xls_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

//	echo "$search_test_date_from-$search_test_date_to<br>";
	$arr_monstr = array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฏาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
	if ($search_test_date_from) {
		$s_date_from = explode("/",$search_test_date_from);
		$s_date_from_str = "ตั้งแต่ ".$arr_monstr[(int)$s_date_from[1]-1]." พ.ศ.".$s_date_from[2];
	} else $s_date_from_str = "";
	if ($search_test_date_to) {
		$s_date_to = explode("/",$search_test_date_to);
		$s_date_to_str = "ถึง ".$arr_monstr[(int)$s_date_to[1]-1]." พ.ศ.".$s_date_to[2];
	} else $s_date_to_str = "";

	if ($search_org_code) $group_text = "กลุ่ม ".$search_org_code; 
	else {
		$search_org_code = "IT";
		$group_text = "กลุ่ม ".$search_org_code;
	}
	
//	echo "group_text=$group_text<br>";

	$company_name = "";
	$report_title = "ข้อมูลการประเมินสมรรถนะหลักทางการบริหารของผู้เข้ารับการประเมิน ".$group_text." ".($s_date_from_str ? " " : "").$s_date_from_str.($s_date_from_str && $s_date_to_str ? " " : "").$s_date_to_str;
	$report_code = "mgt_competency_assessment";
	
//	echo "report_title=$report_title<br>";
	
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
		$ws_colmerge_line1 = (array) null;
		$ws_colmerge_line2 = (array) null;
		$ws_fontfmt_line1 = (array) null;
		$ws_fontfmt_line2 = (array) null;
		$ws_headfontSize = (array) null;
		$ws_headfontcolor = (array) null;
		$ws_headfillcolor = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$buff = explode("|",$heading_text[$i]);
			$ws_head_line1[] = $buff[0];
			if ($i==1 && strtolower($buff[1])=="code") $ws_head_line2[] = "Name";
			else		$ws_head_line2[] = $buff[1];
			$ws_colmerge_line1[] = 1;
			$ws_colmerge_line2[] = 0;
			$buff = explode("|",$heading_font_style[$i]);
			$ws_fontfmt_line1[] = $buff[0];
			$ws_fontfmt_line2[] = $buff[1];
			$ws_headfontSize[] = $heading_font_size[$i];
			$ws_headfontcolor[] = $heading_font_color[$i];
			$ws_headfillcolor1[] = $heading_fill_color1[$i];
			$ws_headfillcolor2[] = $heading_fill_color2[$i];
		}
		$ws_colmerge_line1[count($ws_colmerge_line1)-1] = 0; // ตัวสุดท้าย บรรทัดแรก merge = 0
		
		$ws_border_line1 = array("","","TLB","TB","TB","TLB","TB","TB","TLB","TB","TB","TLB","TB","TBR","");
		$ws_border_line2 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_headalign_line2 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_headvalign_line2 = array("b","b","b","b","b","b","b","b","b","b","b","b","b","b","b");
		$ws_headrotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_headrotate_line2 = array(0,0,90,90,90,90,90,90,90,90,90,90,90,90,0);
		$ws_width = 	$heading_width;
//		$ws_width = 	array("10","30","10","10","10","10","10","10","10","10","10","10","10","10","15");

	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	function print_header(){
		global $workbook, $worksheet, $xlsRow, $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align;
		global $ws_head_line1, $ws_head_line2;
		global $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2;
		global $ws_fontfmt_line1, $ws_fontfmt_line2;
		global $ws_headrotate_line1, $ws_headrotate_line2;
		global $ws_headalign_line1, $ws_headalign_line2, $ws_headvalign_line2, $ws_width;
		global	$ws_headfontSize, $ws_headfontcolor, 	$ws_headfillcolor1, $ws_headfillcolor2;
		global $font;

//		print_r($xlsFmtTableHeader);
		
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
		$xlsfmt_cnt=0;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];

				$xlsfmt_cnt++;
				$para_format = "alignment=".$ws_headalign_line1[$arr_column_map[$i]]."&border=$border&isMerge=$merge";
//				$para_format .= "&fontSize=16&fontStyle=".$ws_fontfmt_line1[$arr_column_map[$i]]."&wrapText=1&fgColor=48&bgColor=31";
				$para_format .= "&fontSize=".$ws_headfontSize[$arr_column_map[$i]]."&fontStyle=".$ws_fontfmt_line1[$arr_column_map[$i]]."&wrapText=1";
				$para_format .= "&fgColor=".$ws_headfontcolor[$arr_column_map[$i]]."&bgColor=".$ws_headfillcolor1[$arr_column_map[$i]]."";
//				$para_format .= "&fgColor=48&bgColor=31";
				$para_format .= "&valignment=vcenter";
				${"thisFormat".$xlsfmt_cnt} = set_free_format_new( $workbook, $para_format );
				$worksheet->write($xlsRow, $colseq, $ndata, ${"thisFormat".$xlsfmt_cnt});
//				echo "line1 col-$i-map(".$arr_column_map[$i].")-data=$ndata format=".$para_format."<br>";

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
				
				$xlsfmt_cnt++;
				$para_format = "alignment=".$ws_headalign_line2[$arr_column_map[$i]]."&border=$border&isMerge=$merge";
//				$para_format .= "&fontSize=16&fontStyle=".$ws_fontfmt_line2[$arr_column_map[$i]]."&wrapText=1&fgColor=48&bgColor=31";
				$para_format .= "&fontSize=".$ws_headfontSize[$arr_column_map[$i]]."&fontStyle=".$ws_fontfmt_line2[$arr_column_map[$i]]."&wrapText=1";
				$para_format .= "&fgColor=".$ws_headfontcolor[$arr_column_map[$i]]."&bgColor=".$ws_headfillcolor2[$arr_column_map[$i]]."";
//				$para_format .= "&fgColor=48&bgColor=31";
				$para_format .= ($ws_headrotate_line2[$arr_column_map[$i]] ? "&setRotation=2" : "");
				$para_format .= "&valignment=".$ws_headvalign_line2[$arr_column_map[$i]];
				${"thisFormat".$xlsfmt_cnt} = set_free_format_new( $workbook, $para_format );
				$worksheet->write($xlsRow, $colseq, $ndata, ${"thisFormat".$xlsfmt_cnt});
//				echo "line2 col-$i-map(".$arr_column_map[$i].")-data=$ndata format=".$para_format."<br>";

				$colseq++;
			}
		}
	} // function		

	$search_condition = substr($search_condition,4);
	$search_condition = str_replace("a.","",$search_condition);
	$temp_start =  save_date($search_test_date_from);
	$temp_end =  save_date($search_test_date_to);
	if ($temp_start) $arr_condi[] = "CA_TEST_DATE>='$temp_start'";
	if ($temp_end) $arr_condi[] = "CA_TEST_DATE<='$temp_end'";
	if (count($arr_condi) > 0)  $where = "where ".implode(" and ", $arr_condi); else $where = "";
	
	$cmd = " select 	*
					 from 		PER_MGT_COMPETENCY_ASSESSMENT
					 $where
					 order by CA_NAME, CA_SURNAME, CA_TEST_DATE, ORG_CODE ";
	$cmd = stripslashes($cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";		// exit;

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
			$wsdata_fontfmt_1 = (array) null;
			$wsdata_align_1 = (array) null;
			$wsdata_border_1 = (array) null;
			$wsdata_border_2 = (array) null;
			$wsdata_colmerge_1 = (array) null;
			$wsdata_fontfmt_2 = (array) null;
			$wsdata_fontfmt_3 = (array) null;
			for($k=0; $k<count($ws_head_line1); $k++) {
				$wsdata_fontfmt_1[] = "B";
				if ($k==1)	$wsdata_align_1[] = "L";
				else		$wsdata_align_1[] = "C";
				$wsdata_border_1[] = "TLRB";
				$wsdata_border_2[] = "";
				$wsdata_colmerge_1[] = 0;
				$wsdata_fontfmt_2[] = "";
				$wsdata_fontfmt_3[] = "BI";
			}
			$wsdata_border_3 = array("","TLRB","TLB","TB","TB","TLB","TB","TB","TLB","TB","TB","TLB","TB","TBR","");
			$wsdata_colmerge_3 = array(0,0,1,1,1,1,1,1,1,1,1,1,1,1,0);
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		$data_count = $data_row = 0;
		$arr_content= (array) null;
		$cnt = 0;
		 while ($data = $db_dpis->get_array()) {
				$data_row++;
	
				$PN_CODE = trim($data[PN_CODE]);
				$CA_NAME = trim($data[CA_NAME]);		
				$CA_SURNAME = trim($data[CA_SURNAME]);
				$CA_TEST_DATE = trim($data[CA_TEST_DATE]);
				$ORG_CODE = trim($data[ORG_CODE]);
				
				$TMP_CA_NAME = "$CA_NAME $CA_SURNAME($CA_TEST_DATE)";
				$TMP_CA_CODE = trim($data[CA_CODE]);
				$TMP_CA_CONSISTENCY = $data[CA_CONSISTENCY];
				$TMP_CA_SCORE_1 = $data[CA_SCORE_1];
				$TMP_CA_SCORE_2 = $data[CA_SCORE_2];
				$TMP_CA_SCORE_3 = $data[CA_SCORE_3];
				$TMP_CA_SCORE_4 = $data[CA_SCORE_4];
				$TMP_CA_SCORE_5 = $data[CA_SCORE_5];
				$TMP_CA_SCORE_6 = $data[CA_SCORE_6];
				$TMP_CA_SCORE_7 = $data[CA_SCORE_7];
				$TMP_CA_SCORE_8 = $data[CA_SCORE_8];
				$TMP_CA_SCORE_9 = $data[CA_SCORE_9];
				$TMP_CA_SCORE_10 = $data[CA_SCORE_10];
				$TMP_CA_SCORE_11 = $data[CA_SCORE_11];
				$TMP_CA_SCORE_12 = $data[CA_SCORE_12];
				$TMP_CA_MEAN = $data[CA_MEAN];
/*					
				$arr_content[$data_count][cnt] = $cnt;
				$arr_content[$data_count][ca_name] = "$CA_NAME $CA_SURNAME($CA_TEST_DATE)";
				$arr_content_sum[$data_count][ca_cons] = $TMP_CA_CONSISTENCY;
				$arr_content_sum[$data_count][ca_score_1] = $TMP_CA_SCORE_1;
				$arr_content_sum[$data_count][ca_score_2] = $TMP_CA_SCORE_2;
				$arr_content_sum[$data_count][ca_score_3] = $TMP_CA_SCORE_3;
				$arr_content_sum[$data_count][ca_score_4] = $TMP_CA_SCORE_4;
				$arr_content_sum[$data_count][ca_score_5] = $TMP_CA_SCORE_5;
				$arr_content_sum[$data_count][ca_score_6] = $TMP_CA_SCORE_6;
				$arr_content_sum[$data_count][ca_score_7] = $TMP_CA_SCORE_7;
				$arr_content_sum[$data_count][ca_score_8] = $TMP_CA_SCORE_8;
				$arr_content_sum[$data_count][ca_score_9] = $TMP_CA_SCORE_9;
				$arr_content_sum[$data_count][ca_score_10] = $TMP_CA_SCORE_10;
				$arr_content_sum[$data_count][ca_score_11] = $TMP_CA_SCORE_11;
				$arr_content_sum[$data_count][ca_score_12] = $TMP_CA_SCORE_12;
				$arr_content_sum[$data_count][ca_mean] = $TMP_CA_MEAN;
*/				
				$arr_data = (array) null;
				$arr_data[] = $TMP_CA_CONSISTENCY;	// avg line
				$arr_data[] = $TMP_CA_NAME;
				$arr_data[] = $TMP_CA_SCORE_1;
				$arr_data[] = $TMP_CA_SCORE_2;
				$arr_data[] = $TMP_CA_SCORE_3;
				$arr_data[] = $TMP_CA_SCORE_4;
				$arr_data[] = $TMP_CA_SCORE_5;
				$arr_data[] = $TMP_CA_SCORE_6;
				$arr_data[] = $TMP_CA_SCORE_7;
				$arr_data[] = $TMP_CA_SCORE_8;
				$arr_data[] = $TMP_CA_SCORE_9;
				$arr_data[] = $TMP_CA_SCORE_10;
				$arr_data[] = $TMP_CA_SCORE_11;
				$arr_data[] = $TMP_CA_SCORE_12;
				$arr_data[] = $TMP_CA_MEAN;
	 
				$wsdata_align = $wsdata_align_1;
	
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
						$colseq++;
					}
				}
			
				$data_count++;
		} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
/*
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
			$wsdata_fontfmt_1 = (array) null;
			$wsdata_align_1 = (array) null;
			$wsdata_border_1 = (array) null;
			$wsdata_border_2 = (array) null;
			$wsdata_colmerge_1 = (array) null;
			$wsdata_fontfmt_2 = (array) null;
			$wsdata_fontfmt_3 = (array) null;
			for($k=0; $k<count($ws_head_line1); $k++) {
				$wsdata_fontfmt_1[] = "B";
				$wsdata_align_1[] = "C";
				$wsdata_border_1[] = "TLRB";
				$wsdata_border_2[] = "";
				$wsdata_colmerge_1[] = 0;
				$wsdata_fontfmt_2[] = "";
				$wsdata_fontfmt_3[] = "BI";
			}
			$wsdata_border_3 = array("","TLRB","TLB","TB","TB","TLB","TB","TB","TLB","TB","TB","TLB","TB","TBR","");
			$wsdata_colmerge_3 = array(0,0,1,1,1,1,1,1,1,1,1,1,1,1,0);
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$TMP_CA_NAME = $arr_content[$data_count][ca_name];
			
			$TMP_CA_CONSISTENCY = $arr_content[$data_count][ca_cons];
			$TMP_CA_SCORE_1 = $arr_content[$data_count][ca_score_1];
			$TMP_CA_SCORE_2 = $arr_content[$data_count][ca_score_2];
			$TMP_CA_SCORE_3 = $arr_content[$data_count][ca_score_3];
			$TMP_CA_SCORE_4 = $arr_content[$data_count][ca_score_4];
			$TMP_CA_SCORE_5 = $arr_content[$data_count][ca_score_5];
			$TMP_CA_SCORE_6 = $arr_content[$data_count][ca_score_6];
			$TMP_CA_SCORE_7 = $arr_content[$data_count][ca_score_7];
			$TMP_CA_SCORE_8 = $arr_content[$data_count][ca_score_8];
			$TMP_CA_SCORE_9 = $arr_content[$data_count][ca_score_9];
			$TMP_CA_SCORE_10 = $arr_content[$data_count][ca_score_10];
			$TMP_CA_SCORE_11 = $arr_content[$data_count][ca_score_11];
			$TMP_CA_SCORE_12 = $arr_content[$data_count][ca_score_12];
			$TMP_CA_MEAN = $arr_content[$data_count][ca_mean];

			$arr_data = (array) null;
			$arr_data[] = $TMP_CA_CONSISTENCY;	// avg line
			$arr_data[] = $TMP_CA_NAME;
			$arr_data[] = $TMP_CA_SCORE_1;
			$arr_data[] = $TMP_CA_SCORE_2;
			$arr_data[] = $TMP_CA_SCORE_3;
			$arr_data[] = $TMP_CA_SCORE_4;
			$arr_data[] = $TMP_CA_SCORE_5;
			$arr_data[] = $TMP_CA_SCORE_6;
			$arr_data[] = $TMP_CA_SCORE_7;
			$arr_data[] = $TMP_CA_SCORE_8;
			$arr_data[] = $TMP_CA_SCORE_9;
			$arr_data[] = $TMP_CA_SCORE_10;
			$arr_data[] = $TMP_CA_SCORE_11;
			$arr_data[] = $TMP_CA_SCORE_12;
			$arr_data[] = $TMP_CA_MEAN;
 
			$wsdata_align = $wsdata_align1;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			}			
		} // end for				
*/
	} else {	// else if($count_data)
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
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if ($count_data)

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