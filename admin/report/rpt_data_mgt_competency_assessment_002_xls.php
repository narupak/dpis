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
			$ws_head_line2[] = $buff[1];
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
		global $workbook, $worksheet, $xlsRow, $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
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
					 order by CA_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE, ORG_CODE ";
	$cmd = stripslashes($cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";		// exit;
	$data_count = $data_row = 0;
	$arr_content= (array) null;
	$arr_content_sum = (array) null;
	$arr_content_min = (array) null;
	$arr_content_max = (array) null;
	$TMP_CA_CODE = "";
	$cnt = 0;
     while ($data = $db_dpis->get_array()) {
            $data_row++;

			if ($TMP_CA_CODE != trim($data[CA_CODE])) {
				if ($TMP_CA_CODE) {	// ถ้าไม่ใช่ ROW แรก
					$arr_content[$data_count][cnt] = $cnt;
					$arr_content[$data_count][ca_code] = $TMP_CA_CODE;
					$arr_content_sum[$data_count][ca_cons] = $SUM_CA_CONSISTENCY;
					$arr_content_sum[$data_count][ca_score_1] = $SUM_CA_SCORE_1;
					$arr_content_sum[$data_count][ca_score_2] = $SUM_CA_SCORE_2;
					$arr_content_sum[$data_count][ca_score_3] = $SUM_CA_SCORE_3;
					$arr_content_sum[$data_count][ca_score_4] = $SUM_CA_SCORE_4;
					$arr_content_sum[$data_count][ca_score_5] = $SUM_CA_SCORE_5;
					$arr_content_sum[$data_count][ca_score_6] = $SUM_CA_SCORE_6;
					$arr_content_sum[$data_count][ca_score_7] = $SUM_CA_SCORE_7;
					$arr_content_sum[$data_count][ca_score_8] = $SUM_CA_SCORE_8;
					$arr_content_sum[$data_count][ca_score_9] = $SUM_CA_SCORE_9;
					$arr_content_sum[$data_count][ca_score_10] = $SUM_CA_SCORE_10;
					$arr_content_sum[$data_count][ca_score_11] = $SUM_CA_SCORE_11;
					$arr_content_sum[$data_count][ca_score_12] = $SUM_CA_SCORE_12;
					$arr_content_sum[$data_count][ca_mean] = $SUM_CA_MEAN;

					$arr_content_min[$data_count][ca_cons] = $MIN_CA_CONSISTENCY;
					$arr_content_min[$data_count][ca_score_1] = $MIN_CA_SCORE_1;
					$arr_content_min[$data_count][ca_score_2] = $MIN_CA_SCORE_2;
					$arr_content_min[$data_count][ca_score_3] = $MIN_CA_SCORE_3;
					$arr_content_min[$data_count][ca_score_4] = $MIN_CA_SCORE_4;
					$arr_content_min[$data_count][ca_score_5] = $MIN_CA_SCORE_5;
					$arr_content_min[$data_count][ca_score_6] = $MIN_CA_SCORE_6;
					$arr_content_min[$data_count][ca_score_7] = $MIN_CA_SCORE_7;
					$arr_content_min[$data_count][ca_score_8] = $MIN_CA_SCORE_8;
					$arr_content_min[$data_count][ca_score_9] = $MIN_CA_SCORE_9;
					$arr_content_min[$data_count][ca_score_10] = $MIN_CA_SCORE_10;
					$arr_content_min[$data_count][ca_score_11] = $MIN_CA_SCORE_11;
					$arr_content_min[$data_count][ca_score_12] = $MIN_CA_SCORE_12;
					$arr_content_min[$data_count][ca_mean] = $MIN_CA_MEAN;

					$arr_content_max[$data_count][ca_cons] = $MAX_CA_CONSISTENCY;
					$arr_content_max[$data_count][ca_score_1] = $MAX_CA_SCORE_1;
					$arr_content_max[$data_count][ca_score_2] = $MAX_CA_SCORE_2;
					$arr_content_max[$data_count][ca_score_3] = $MAX_CA_SCORE_3;
					$arr_content_max[$data_count][ca_score_4] = $MAX_CA_SCORE_4;
					$arr_content_max[$data_count][ca_score_5] = $MAX_CA_SCORE_5;
					$arr_content_max[$data_count][ca_score_6] = $MAX_CA_SCORE_6;
					$arr_content_max[$data_count][ca_score_7] = $MAX_CA_SCORE_7;
					$arr_content_max[$data_count][ca_score_8] = $MAX_CA_SCORE_8;
					$arr_content_max[$data_count][ca_score_9] = $MAX_CA_SCORE_9;
					$arr_content_max[$data_count][ca_score_10] = $MAX_CA_SCORE_10;
					$arr_content_max[$data_count][ca_score_11] = $MAX_CA_SCORE_11;
					$arr_content_max[$data_count][ca_score_12] = $MAX_CA_SCORE_12;
					$arr_content_max[$data_count][ca_mean] = $MAX_CA_MEAN;

					$data_count++;
				}
				$TMP_CA_CODE = trim($data[CA_CODE]);
				$PN_CODE = trim($data[PN_CODE]);
				$CA_NAME = trim($data[CA_NAME]);		
				$CA_SURNAME = trim($data[CA_SURNAME]);
				$CA_TEST_DATE = trim($data[CA_TEST_DATE]);
				$ORG_CODE = trim($data[ORG_CODE]);
				
				$cnt = 1;

				$SUM_CA_CONSISTENCY = $data[CA_CONSISTENCY];
				$SUM_CA_SCORE_1 = $data[CA_SCORE_1];
				$SUM_CA_SCORE_2 = $data[CA_SCORE_2];
				$SUM_CA_SCORE_3 = $data[CA_SCORE_3];
				$SUM_CA_SCORE_4 = $data[CA_SCORE_4];
				$SUM_CA_SCORE_5 = $data[CA_SCORE_5];
				$SUM_CA_SCORE_6 = $data[CA_SCORE_6];
				$SUM_CA_SCORE_7 = $data[CA_SCORE_7];
				$SUM_CA_SCORE_8 = $data[CA_SCORE_8];
				$SUM_CA_SCORE_9 = $data[CA_SCORE_9];
				$SUM_CA_SCORE_10 = $data[CA_SCORE_10];
				$SUM_CA_SCORE_11 = $data[CA_SCORE_11];
				$SUM_CA_SCORE_12 = $data[CA_SCORE_12];
				$SUM_CA_MEAN = $data[CA_MEAN];

				$MIN_CA_CONSISTENCY = $data[CA_CONSISTENCY];
				$MIN_CA_SCORE_1 = $data[CA_SCORE_1];
				$MIN_CA_SCORE_2 = $data[CA_SCORE_2];
				$MIN_CA_SCORE_3 = $data[CA_SCORE_3];
				$MIN_CA_SCORE_4 = $data[CA_SCORE_4];
				$MIN_CA_SCORE_5 = $data[CA_SCORE_5];
				$MIN_CA_SCORE_6 = $data[CA_SCORE_6];
				$MIN_CA_SCORE_7 = $data[CA_SCORE_7];
				$MIN_CA_SCORE_8 = $data[CA_SCORE_8];
				$MIN_CA_SCORE_9 = $data[CA_SCORE_9];
				$MIN_CA_SCORE_10 = $data[CA_SCORE_10];
				$MIN_CA_SCORE_11 = $data[CA_SCORE_11];
				$MIN_CA_SCORE_12 = $data[CA_SCORE_12];
				$MIN_CA_MEAN = $data[CA_MEAN];

				$MAX_CA_CONSISTENCY = $data[CA_CONSISTENCY];
				$MAX_CA_SCORE_1 = $data[CA_SCORE_1];
				$MAX_CA_SCORE_2 = $data[CA_SCORE_2];
				$MAX_CA_SCORE_3 = $data[CA_SCORE_3];
				$MAX_CA_SCORE_4 = $data[CA_SCORE_4];
				$MAX_CA_SCORE_5 = $data[CA_SCORE_5];
				$MAX_CA_SCORE_6 = $data[CA_SCORE_6];
				$MAX_CA_SCORE_7 = $data[CA_SCORE_7];
				$MAX_CA_SCORE_8 = $data[CA_SCORE_8];
				$MAX_CA_SCORE_9 = $data[CA_SCORE_9];
				$MAX_CA_SCORE_10 = $data[CA_SCORE_10];
				$MAX_CA_SCORE_11 = $data[CA_SCORE_11];
				$MAX_CA_SCORE_12 = $data[CA_SCORE_12];
				$MAX_CA_MEAN = $data[CA_MEAN];

			} else {
				
				$cnt++;
				
				$SUM_CA_CONSISTENCY += $data[CA_CONSISTENCY];
				$SUM_CA_SCORE_1 += $data[CA_SCORE_1];
				$SUM_CA_SCORE_2 += $data[CA_SCORE_2];
				$SUM_CA_SCORE_3 += $data[CA_SCORE_3];
				$SUM_CA_SCORE_4 += $data[CA_SCORE_4];
				$SUM_CA_SCORE_5 += $data[CA_SCORE_5];
				$SUM_CA_SCORE_6 += $data[CA_SCORE_6];
				$SUM_CA_SCORE_7 += $data[CA_SCORE_7];
				$SUM_CA_SCORE_8 += $data[CA_SCORE_8];
				$SUM_CA_SCORE_9 += $data[CA_SCORE_9];
				$SUM_CA_SCORE_10 += $data[CA_SCORE_10];
				$SUM_CA_SCORE_11 += $data[CA_SCORE_11];
				$SUM_CA_SCORE_12 += $data[CA_SCORE_12];
				$SUM_CA_MEAN += $data[CA_MEAN];

				if ($MIN_CA_CONSISTENCY > $data[CA_CONSISTENCY])	$MIN_CA_CONSISTENCY = $data[CA_CONSISTENCY];
				if ($MIN_CA_SCORE_1 > $data[CA_SCORE_1])	$MIN_CA_SCORE_1 = $data[CA_SCORE_1];
				if ($MIN_CA_SCORE_2 > $data[CA_SCORE_2])	$MIN_CA_SCORE_2 = $data[CA_SCORE_2];
				if ($MIN_CA_SCORE_3 > $data[CA_SCORE_3])	$MIN_CA_SCORE_3 = $data[CA_SCORE_3];
				if ($MIN_CA_SCORE_4 > $data[CA_SCORE_4])	$MIN_CA_SCORE_4 = $data[CA_SCORE_4];
				if ($MIN_CA_SCORE_5 > $data[CA_SCORE_5])	$MIN_CA_SCORE_5 = $data[CA_SCORE_5];
				if ($MIN_CA_SCORE_6 > $data[CA_SCORE_6])	$MIN_CA_SCORE_6 = $data[CA_SCORE_6];
				if ($MIN_CA_SCORE_7 > $data[CA_SCORE_7])	$MIN_CA_SCORE_7 = $data[CA_SCORE_7];
				if ($MIN_CA_SCORE_8 > $data[CA_SCORE_8])	$MIN_CA_SCORE_8 = $data[CA_SCORE_8];
				if ($MIN_CA_SCORE_9 > $data[CA_SCORE_9])	$MIN_CA_SCORE_9 = $data[CA_SCORE_9];
				if ($MIN_CA_SCORE_10 > $data[CA_SCORE_10])	$MIN_CA_SCORE_10 = $data[CA_SCORE_10];
				if ($MIN_CA_SCORE_11 > $data[CA_SCORE_11])	$MIN_CA_SCORE_11 = $data[CA_SCORE_11];
				if ($MIN_CA_SCORE_12 > $data[CA_SCORE_12])	$MIN_CA_SCORE_12 = $data[CA_SCORE_12];
				if ($MIN_CA_MEAN > $data[CA_MEAN])	$MIN_CA_MEAN = $data[CA_MEAN];

				if ($MAX_CA_CONSISTENCY < $data[CA_CONSISTENCY])	$MAX_CA_CONSISTENCY = $data[CA_CONSISTENCY];
				if ($MAX_CA_SCORE_1 < $data[CA_SCORE_1])	$MAX_CA_SCORE_1 = $data[CA_SCORE_1];
				if ($MAX_CA_SCORE_2 < $data[CA_SCORE_2])	$MAX_CA_SCORE_2 = $data[CA_SCORE_2];
				if ($MAX_CA_SCORE_3 < $data[CA_SCORE_3])	$MAX_CA_SCORE_3 = $data[CA_SCORE_3];
				if ($MAX_CA_SCORE_4 < $data[CA_SCORE_4])	$MAX_CA_SCORE_4 = $data[CA_SCORE_4];
				if ($MAX_CA_SCORE_5 < $data[CA_SCORE_5])	$MAX_CA_SCORE_5 = $data[CA_SCORE_5];
				if ($MAX_CA_SCORE_6 < $data[CA_SCORE_6])	$MAX_CA_SCORE_6 = $data[CA_SCORE_6];
				if ($MAX_CA_SCORE_7 < $data[CA_SCORE_7])	$MAX_CA_SCORE_7 = $data[CA_SCORE_7];
				if ($MAX_CA_SCORE_8 < $data[CA_SCORE_8])	$MAX_CA_SCORE_8 = $data[CA_SCORE_8];
				if ($MAX_CA_SCORE_9 < $data[CA_SCORE_9])	$MAX_CA_SCORE_9 = $data[CA_SCORE_9];
				if ($MAX_CA_SCORE_10 < $data[CA_SCORE_10])	$MAX_CA_SCORE_10 = $data[CA_SCORE_10];
				if ($MAX_CA_SCORE_11 < $data[CA_SCORE_11])	$MAX_CA_SCORE_11 = $data[CA_SCORE_11];
				if ($MAX_CA_SCORE_12 < $data[CA_SCORE_12])	$MAX_CA_SCORE_12 = $data[CA_SCORE_12];
				if ($MAX_CA_MEAN < $data[CA_MEAN])	$MAX_CA_MEAN = $data[CA_MEAN];
			}
	} // end while
	
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
			$cnt = $arr_content[$data_count][cnt];
			$TMP_CA_CODE = $arr_content[$data_count][ca_code];
			
			$SUM_CA_CONSISTENCY = $arr_content_sum[$data_count][ca_cons];
			$SUM_CA_SCORE_1 = $arr_content_sum[$data_count][ca_score_1];
			$SUM_CA_SCORE_2 = $arr_content_sum[$data_count][ca_score_2];
			$SUM_CA_SCORE_3 = $arr_content_sum[$data_count][ca_score_3];
			$SUM_CA_SCORE_4 = $arr_content_sum[$data_count][ca_score_4];
			$SUM_CA_SCORE_5 = $arr_content_sum[$data_count][ca_score_5];
			$SUM_CA_SCORE_6 = $arr_content_sum[$data_count][ca_score_6];
			$SUM_CA_SCORE_7 = $arr_content_sum[$data_count][ca_score_7];
			$SUM_CA_SCORE_8 = $arr_content_sum[$data_count][ca_score_8];
			$SUM_CA_SCORE_9 = $arr_content_sum[$data_count][ca_score_9];
			$SUM_CA_SCORE_10 = $arr_content_sum[$data_count][ca_score_10];
			$SUM_CA_SCORE_11 = $arr_content_sum[$data_count][ca_score_11];
			$SUM_CA_SCORE_12 = $arr_content_sum[$data_count][ca_score_12];
			$SUM_CA_MEAN = $arr_content_sum[$data_count][ca_mean];

			$AVG_CA_CONSISTENCY = round($SUM_CA_CONSISTENCY / $cnt , 2);	// avg line
			$AVG_CA_SCORE_1 = round($SUM_CA_SCORE_1 / $cnt , 2);
			$AVG_CA_SCORE_2 = round($SUM_CA_SCORE_2 / $cnt , 2);
			$AVG_CA_SCORE_3 = round($SUM_CA_SCORE_3 / $cnt , 2);
			$AVG_CA_SCORE_4 = round($SUM_CA_SCORE_4 / $cnt , 2);
			$AVG_CA_SCORE_5 = round($SUM_CA_SCORE_5 / $cnt , 2);
			$AVG_CA_SCORE_6 = round($SUM_CA_SCORE_6 / $cnt , 2);
			$AVG_CA_SCORE_7 = round($SUM_CA_SCORE_7 / $cnt , 2);
			$AVG_CA_SCORE_8 = round($SUM_CA_SCORE_8 / $cnt , 2);
			$AVG_CA_SCORE_9 = round($SUM_CA_SCORE_9 / $cnt , 2);
			$AVG_CA_SCORE_10 = round($SUM_CA_SCORE_10 / $cnt , 2);
			$AVG_CA_SCORE_11 = round($SUM_CA_SCORE_11 / $cnt , 2);
			$AVG_CA_SCORE_12 = round($SUM_CA_SCORE_12 / $cnt , 2);
//			$AVG_CA_MEAN = $SUM_CA_MEAN / $cnt;
			$AVG_CA_MEAN = round(($AVG_CA_SCORE_1 + $AVG_CA_SCORE_2 + $AVG_CA_SCORE_3 + $AVG_CA_SCORE_4 + $AVG_CA_SCORE_5 + $AVG_CA_SCORE_6 + $AVG_CA_SCORE_7 + $AVG_CA_SCORE_8 + $AVG_CA_SCORE_9 + $AVG_CA_SCORE_10 + $AVG_CA_SCORE_11 + $AVG_CA_SCORE_12) / 12 , 2);
			$AVG_CA_S_MEAN1 = round(($AVG_CA_SCORE_1 + $AVG_CA_SCORE_2 + $AVG_CA_SCORE_3) / 3 , 2);
			$AVG_CA_S_MEAN2 = round(($AVG_CA_SCORE_4 + $AVG_CA_SCORE_5 + $AVG_CA_SCORE_6) / 3 , 2);
			$AVG_CA_S_MEAN3 = round(($AVG_CA_SCORE_7 + $AVG_CA_SCORE_8 + $AVG_CA_SCORE_9) / 3 , 2);
			$AVG_CA_S_MEAN4 = round(($AVG_CA_SCORE_10 + $AVG_CA_SCORE_11 + $AVG_CA_SCORE_12) / 3 , 2);

			$arr_data = (array) null;
			$arr_data[] = $AVG_CA_CONSISTENCY;	// avg line
			$arr_data[] = "Mean".$TMP_CA_CODE."($cnt)";
			$arr_data[] = $AVG_CA_SCORE_1;
			$arr_data[] = $AVG_CA_SCORE_2;
			$arr_data[] = $AVG_CA_SCORE_3;
			$arr_data[] = $AVG_CA_SCORE_4;
			$arr_data[] = $AVG_CA_SCORE_5;
			$arr_data[] = $AVG_CA_SCORE_6;
			$arr_data[] = $AVG_CA_SCORE_7;
			$arr_data[] = $AVG_CA_SCORE_8;
			$arr_data[] = $AVG_CA_SCORE_9;
			$arr_data[] = $AVG_CA_SCORE_10;
			$arr_data[] = $AVG_CA_SCORE_11;
			$arr_data[] = $AVG_CA_SCORE_12;
			$arr_data[] = $AVG_CA_MEAN;
 
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
			
			$MAX_CA_CONSISTENCY = $arr_content_max[$data_count][ca_cons];
			$MAX_CA_SCORE_1 = $arr_content_max[$data_count][ca_score_1];
			$MAX_CA_SCORE_2 = $arr_content_max[$data_count][ca_score_2];
			$MAX_CA_SCORE_3 = $arr_content_max[$data_count][ca_score_3];
			$MAX_CA_SCORE_4 = $arr_content_max[$data_count][ca_score_4];
			$MAX_CA_SCORE_5 = $arr_content_max[$data_count][ca_score_5];
			$MAX_CA_SCORE_6 = $arr_content_max[$data_count][ca_score_6];
			$MAX_CA_SCORE_7 = $arr_content_max[$data_count][ca_score_7];
			$MAX_CA_SCORE_8 = $arr_content_max[$data_count][ca_score_8];
			$MAX_CA_SCORE_9 = $arr_content_max[$data_count][ca_score_9];
			$MAX_CA_SCORE_10 = $arr_content_max[$data_count][ca_score_10];
			$MAX_CA_SCORE_11 = $arr_content_max[$data_count][ca_score_11];
			$MAX_CA_SCORE_12 = $arr_content_max[$data_count][ca_score_12];
			$MAX_CA_MEAN = $arr_content_max[$data_count][ca_mean];
			
			$arr_data = (array) null;
			$arr_data[] = $MAX_CA_CONSISTENCY;
			$arr_data[] = "Max".$TMP_CA_CODE;
			$arr_data[] = $MAX_CA_SCORE_1;
			$arr_data[] = $MAX_CA_SCORE_2;
			$arr_data[] = $MAX_CA_SCORE_3;
			$arr_data[] = $MAX_CA_SCORE_4;
			$arr_data[] = $MAX_CA_SCORE_5;
			$arr_data[] = $MAX_CA_SCORE_6;
			$arr_data[] = $MAX_CA_SCORE_7;
			$arr_data[] = $MAX_CA_SCORE_8;
			$arr_data[] = $MAX_CA_SCORE_9;
			$arr_data[] = $MAX_CA_SCORE_10;
			$arr_data[] = $MAX_CA_SCORE_11;
			$arr_data[] = $MAX_CA_SCORE_12;
			$arr_data[] = $MAX_CA_MEAN;
 
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
			
			$MIN_CA_CONSISTENCY = $arr_content_min[$data_count][ca_cons];
			$MIN_CA_SCORE_1 = $arr_content_min[$data_count][ca_score_1];
			$MIN_CA_SCORE_2 = $arr_content_min[$data_count][ca_score_2];
			$MIN_CA_SCORE_3 = $arr_content_min[$data_count][ca_score_3];
			$MIN_CA_SCORE_4 = $arr_content_min[$data_count][ca_score_4];
			$MIN_CA_SCORE_5 = $arr_content_min[$data_count][ca_score_5];
			$MIN_CA_SCORE_6 = $arr_content_min[$data_count][ca_score_6];
			$MIN_CA_SCORE_7 = $arr_content_min[$data_count][ca_score_7];
			$MIN_CA_SCORE_8 = $arr_content_min[$data_count][ca_score_8];
			$MIN_CA_SCORE_9 = $arr_content_min[$data_count][ca_score_9];
			$MIN_CA_SCORE_10 = $arr_content_min[$data_count][ca_score_10];
			$MIN_CA_SCORE_11 = $arr_content_min[$data_count][ca_score_11];
			$MIN_CA_SCORE_12 = $arr_content_min[$data_count][ca_score_12];
			$MIN_CA_MEAN = $arr_content_min[$data_count][ca_mean];

			$arr_data = (array) null;
			$arr_data[] = $MIN_CA_CONSISTENCY;	// avg line
			$arr_data[] = "Min".$TMP_CA_CODE;
			$arr_data[] = $MIN_CA_SCORE_1;
			$arr_data[] = $MIN_CA_SCORE_2;
			$arr_data[] = $MIN_CA_SCORE_3;
			$arr_data[] = $MIN_CA_SCORE_4;
			$arr_data[] = $MIN_CA_SCORE_5;
			$arr_data[] = $MIN_CA_SCORE_6;
			$arr_data[] = $MIN_CA_SCORE_7;
			$arr_data[] = $MIN_CA_SCORE_8;
			$arr_data[] = $MIN_CA_SCORE_9;
			$arr_data[] = $MIN_CA_SCORE_10;
			$arr_data[] = $MIN_CA_SCORE_11;
			$arr_data[] = $MIN_CA_SCORE_12;
			$arr_data[] = $MIN_CA_MEAN;
 
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

			$arr_data = (array) null;
			$arr_data[] = "";	// mean group line
			$arr_data[] = "MEAN GROUP";
			$arr_data[] = "<**1**>".$AVG_CA_S_MEAN1;
			$arr_data[] = "<**1**>".$AVG_CA_S_MEAN1;
			$arr_data[] = "<**1**>".$AVG_CA_S_MEAN1;
			$arr_data[] = "<**2**>".$AVG_CA_S_MEAN2;
			$arr_data[] = "<**2**>".$AVG_CA_S_MEAN2;
			$arr_data[] = "<**2**>".$AVG_CA_S_MEAN2;
			$arr_data[] = "<**3**>".$AVG_CA_S_MEAN3;
			$arr_data[] = "<**3**>".$AVG_CA_S_MEAN3;
			$arr_data[] = "<**3**>".$AVG_CA_S_MEAN3;
			$arr_data[] = "<**4**>".$AVG_CA_S_MEAN4;
			$arr_data[] = "<**4**>".$AVG_CA_S_MEAN4;
			$arr_data[] = "<**4**>".$AVG_CA_S_MEAN4;
			$arr_data[] = "";
 
			$wsdata_align = $wsdata_align_1;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_3[$arr_column_map[$i]], $wsdata_colmerge_3[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_3[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
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