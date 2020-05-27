<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("../report/rpt_function.php");

	include ("rpt_data_mgt_competency_assessment_001_format.php");

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

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";

	$report_title = "บัญชีแนบท้ายคำสั่ง$COM_DESC || แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";  
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "";

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
		$ws_width = array(20,15,15,15,15,15,15,15,15,20,20,20);
	
		$ws_head_line1 = array("ครั้งที่","","","","","","","","","คะแนน","Mean","จำนวน");
		$ws_head_line2 = array("ส่วนราชการ","2","%","3","%","4","%","5","%","รวม".($dup_way==1 ? "สูงขึ้น" : "ต่ำลง"),"%","คนซ้ำ");
	
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
	
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
		$ws_border_line2 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
		
		$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
	
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C");
	
//		$ws_font_style = array("B|","B|B","B|B","B|B","B|B","B|B","B|B","B|B","B|B","B|","|B","B|");
//		$ws_font_size = array("14","16","16","16","16","16","16","16","16","14","16","16");
//		$ws_fill_color = array("EEEEFF","AAAAAA","EEEEFF","999999","EEEEFF","999999","EEEEFF","999999","EEEEFF","AAAAAA","EEEEFF","EEEEFF");
//		$ws_font_color = array("0066CC","555500","0066CC","555555","0066CC","555555","0066CC","555555","0066CC","555500","0066CC","0066CC");
		$ws_fill_color = array("cyan","magenta","brown","magenta","cyan","magenta","cyan","magenta","brown","cyan","magenta","cyan");
		$ws_font_color = array("silver","yellow","silver","white","silver","white","yellow","white","yellow","white","yellow","orange");
//		$ws_border = array("TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR");
	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน

	function print_header(){
		global $worksheet, $xlsRow;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2, $ws_border_line1, $ws_border_line2;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_rotate_line1, $ws_rotate_line2, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;
		global $ws_font_color, $ws_fill_color;

		// loop กำหนดความกว้างของ column
		$colshow_cnt = 0;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
				$colshow_cnt++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge, $ws_wraptext_line1[$arr_column_map[$i]], $ws_rotate_line1[$arr_column_map[$i]], $ws_font_color[$arr_column_map[$i]], $ws_fill_color[$arr_column_map[$i]]));
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
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge, $ws_wraptext_line1[$arr_column_map[$i]], $ws_rotate_line1[$arr_column_map[$i]], $ws_font_color[$arr_column_map[$i]], $ws_fill_color[$arr_column_map[$i]]));
				$colseq++;
			}
		}
/*
		// loop พิมพ์ head บรรทัดที่ 1
		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line1, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line1, $ws_colmerge_line1, $ws_headalign_line1, $ws_wraptext_line1, $ws_rotate_line1, $ws_font_color, $ws_fill_color);
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line2, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line2, $ws_wraptext_line2, $ws_rotate_line2, $ws_font_color, $ws_fill_color);
*/
	} // function		

	$search_condition = substr($search_condition,4);
	$search_condition = str_replace("a.","",$search_condition);
	$temp_start =  save_date($search_test_date_from);
	$temp_end =  save_date($search_test_date_to);
	if ($temp_start) $arr_condi[] = "CA_TEST_DATE>='$temp_start'";
	if ($temp_end) $arr_condi[] = "CA_TEST_DATE<='$temp_end'";
	if (count($arr_condi) > 0)  $where = "where ".implode(" and ", $arr_condi); else $where = "";
	
	$cmd = " select 	PN_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE, ORG_CODE, CA_MEAN
					 from 		PER_MGT_COMPETENCY_ASSESSMENT
					 $where
					 order by CA_NAME, CA_SURNAME, CA_TEST_DATE, ORG_CODE";
	$cmd = stripslashes($cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "cmd=$cmd ($count_data)<br>";		//	exit;
	$data_count = $data_row = 0;
	$arr_cnt = (array) null;
	$cnt = 0;
	$tot_target = (array) null;
	$tot_all = (array) null;
	$LAST_PERSON_KEY = "";
     while ($data = $db_dpis->get_array()) {
            $data_row++;

            $PN_CODE = trim($data[PN_CODE]);
            $CA_NAME = trim($data[CA_NAME]);		
            $CA_SURNAME = trim($data[CA_SURNAME]);
			$CA_TEST_DATE = trim($data[CA_TEST_DATE]);
            $ORG_CODE = trim($data[ORG_CODE]);
            $TMP_CA_MEAN = trim($data[CA_MEAN]);
			
			$cnt++;
			$PERSON_KEY = $PN_CODE.$CA_NAME." ".$CA_SURNAME;
			if ($LAST_PERSON_KEY && $LAST_PERSON_KEY!=$PERSON_KEY) {
				// 	ประมวลผลจำนวนคน
				if ($cnt >= 2 && $cnt <= 5) {
					if ($dup_way==1) {	// หาจำนวนที่สูงขึ้น
						if ($TMP_CA_MEAN > $LAST_CA_MEAN)  { // กรณึ คะแนนสูงขึ้น
							$arr_cnt[$LAST_ORG_CODE][$cnt]++;
							$tot_target[$LAST_ORG_CODE]++;
							if ($cnt==2) $tot_col2++;
							else if ($cnt==3) $tot_col3++;
							else if ($cnt==4) $tot_col4++;
							else $tot_col5++;
						} else {
							$arr_cnt[$LAST_ORG_CODE][0]++;
							$tot_col0++;
						}
						$tot_all[$LAST_ORG_CODE]++;
						$tot_colall++;
					} else { // กรณี คะแนนลดลง
						if ($TMP_CA_MEAN < $LAST_CA_MEAN)  { // กรณึ คะแนนลดลง
							$arr_cnt[$LAST_ORG_CODE][$cnt]++;
							$tot_target[$LAST_ORG_CODE]++;
							if ($cnt==2) $tot_col2++;
							else if ($cnt==3) $tot_col3++;
							else if ($cnt==4) $tot_col4++;
							else $tot_col5++;
						} else {
							$arr_cnt[$LAST_ORG_CODE][0]++;
							$tot_col0++;
						}
						$tot_all[$LAST_ORG_CODE]++;
						$tot_colall++;
					}
				} else if ($cnt > 5) {
					if ($dup_way==1) {	// หาจำนวนที่สูงขึ้น
						if ($TMP_CA_MEAN > $LAST_CA_MEAN) {   // กรณึ คะแนนสูงขึ้น
							$arr_cnt[$LAST_ORG_CODE][0]++;
							$tot_col0++;
						}
						$tot_all[$LAST_ORG_CODE]++;
						$tot_colall++;
					} else {
						if ($TMP_CA_MEAN < $LAST_CA_MEAN) {   // กรณึ คะแนนลดลง
							$arr_cnt[$LAST_ORG_CODE][0]++;
							$tot_col0++;
						}
						$tot_all[$LAST_ORG_CODE]++;
						$tot_colall++;
					}
				} else {	// $cnt <= 1 คือไม่อยู่ในเงื่อนไขที่ต้องการแต่มีค่าเพราะต้องการแสดงค่าในตาราง
					$arr_cnt[$LAST_ORG_CODE][0]++;
				}
				//  reset ค่า สำหรับคนต่อไป
				$LAST_PERSON_KEY = $PERSON_KEY;
				$LAST_CA_MEAN = $TMP_CA_MEAN;
				$LAST_ORG_CODE = $ORG_CODE;
				$cnt = 0;
			} else if (!$LAST_PERSON_KEY) {
				//  reset ค่า สำหรับคนแรก
				$LAST_PERSON_KEY = $PERSON_KEY;
				$LAST_CA_MEAN = $TMP_CA_MEAN;
				$LAST_ORG_CODE = $ORG_CODE;
			} else {	// ชื่อเป็นคนเดิม
				if ($LAST_ORG_CODE!= $ORG_CODE) { // ถ้า รหัสส่วนราชการเปลี่่ยน
					// 	ประมวลผลจำนวนคน
					if ($cnt >= 2 && $cnt <= 5) {
						if ($dup_way==1) {	// หาจำนวนที่สูงขึ้น
							if ($TMP_CA_MEAN > $LAST_CA_MEAN) {   // กรณึ คะแนนสูงขึ้น
								$arr_cnt[$LAST_ORG_CODE][$cnt]++;
								$tot_col[$cnt]++;
								$tot_target[$LAST_ORG_CODE]++;
								if ($cnt==2) $tot_col2++;
								else if ($cnt==3) $tot_col3++;
								else if ($cnt==4) $tot_col4++;
								else $tot_col5++;
							} else {
								$arr_cnt[$LAST_ORG_CODE][0]++;
								$tot_col[0]++;
							}
							$tot_all[$LAST_ORG_CODE]++;
						} else {	// หาจำนวนที่ลดลง
							if ($TMP_CA_MEAN < $LAST_CA_MEAN) {   // กรณึ คะแนนลดลง
								$arr_cnt[$LAST_ORG_CODE][$cnt]++;
								$tot_col[$cnt]++;
								$tot_target[$LAST_ORG_CODE]++;
								if ($cnt==2) $tot_col2++;
								else if ($cnt==3) $tot_col3++;
								else if ($cnt==4) $tot_col4++;
								else $tot_col5++;
							} else {
								$arr_cnt[$LAST_ORG_CODE][0]++;
								$tot_col[0]++;
							}
							$tot_all[$LAST_ORG_CODE]++;
						}
					} else if ($cnt > 5) {
						if ($dup_way==1) {	// หาจำนวนที่สูงขึ้น
							if ($TMP_CA_MEAN > $LAST_CA_MEAN) {   // กรณึ คะแนนสูงขึ้น
								$arr_cnt[$LAST_ORG_CODE][0]++;
								$tot_col0++;
							}
							$tot_all[$LAST_ORG_CODE]++;
							$tot_colall++;
						} else {
							if ($TMP_CA_MEAN < $LAST_CA_MEAN) {   // กรณึ คะแนนลดลง
								$arr_cnt[$LAST_ORG_CODE][0]++;
								$tot_col0++;
							}
							$tot_all[$LAST_ORG_CODE]++;
							$tot_colall++;
						}
					} else {	// $cnt <= 1 คือไม่อยู่ในเงื่อนไขที่ต้องการแต่มีค่าเพราะต้องการแสดงค่าในตาราง
						$arr_cnt[$LAST_ORG_CODE][0]++;
					}
					$LAST_CA_MEAN = $TMP_CA_MEAN;
					$LAST_ORG_CODE = $ORG_CODE;
					$cnt = 0;
				}
			}
			$data_count++;
	} // end while
	
	$msort_result = array_multisort($arr_cnt, SORT_ASC, SORT_STRING, $tot_target, SORT_ASC, SORT_STRING, $tot_all, SORT_ASC, SORT_STRING);
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
			$wsdata_border_3 = (array) null;
			$wsdata_colmerge_1 = (array) null;
			$wsdata_fontfmt_2 = (array) null;
			$wsdata_wraptext = (array) null;
			$wsdata_rotate = (array) null;
			for($k=0; $k<count($ws_head_line1); $k++) {
				$wsdata_fontfmt_1[] = "";
				$wsdata_align_1[] = "C";
				$wsdata_border_1[] = "LR";
				$wsdata_border_2[] = "";
				$wsdata_border_3[] = "LBR";
				$wsdata_colmerge_1[] = 0;
				$wsdata_fontfmt_2[] = "";
				$wsdata_wraptext[] = 1;
				$wsdata_rotate[] = 0;
			}
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		$tot_font_size1 = $tot_font_size;
		$arr_data = array("",0,0,0,0,0,0,0,0,0,0,0);
		$arr_data[0] = "ทุกส่วนราชการ";	$tot_font_size1[0] = 12;
		$arr_data[1] = $tot_col2;
		$arr_data[2] = $tot_col2 / $tot_colall * 100;
		$arr_data[3] = $tot_col3;
		$arr_data[4] = $tot_col3 / $tot_colall * 100;
		$arr_data[5] = $tot_col4;
		$arr_data[6] = $tot_col4 / $tot_colall * 100;
		$arr_data[7] = $tot_col5;
		$arr_data[8] = $tot_col5 / $tot_colall * 100;
		$arr_data[9] = ($tot_col2+$tot_col3+$tot_col4+$tot_col5);
		$arr_data[10] = ($tot_col2+$tot_col3+$tot_col4+$tot_col5) / $tot_colall * 100;
		$arr_data[11] = $tot_colall;

		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1, $wsdata_wraptext, $wsdata_rotate, $ws_font_color, $ws_fill_color);
//		$result = $pdf->add_data_tab($arr_data, 7, $data_border, $data_align, "", $tot_font_size1, $tot_font_style, $tot_font_color, $tot_fill_color);
//		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		$key_index = array(-1,-1,1,3,5,7);		// map key ตามตาราง
		$arr_tot_col = array("รวม",0,0,0,0,0,0,0,0,0,0,0);
		foreach($arr_cnt as $key => $arr_cnt2) {
			$arr_data = array("",0,0,0,0,0,0,0,0,0,0,0);
			$arr_data[0] = "$key";
			$tot_target1= $tot_target[$key];
			$tot_all1 = $tot_all[$key];
//			echo "key=$key tot_target=$tot_target1 tot_all=$tot_all1";
			foreach($arr_cnt2 as $key1 => $val) {
				if ($key1 >= 2) {
					$arr_data[$key_index[$key1]] = $val;
					$arr_data[$key_index[$key1]+1] = $val / $tot_all1 * 100;
					$arr_tot_col[$key_index[$key1]] += $val;
//					echo " (1) key1=$key1 val=$val";
//				} else {
//					echo " (2) key1=$key1 val=$val";
				}
			}
//			echo "<br>";
			$arr_data[9] = $tot_target1;
			$arr_tot_col[9] += $tot_target1;
			$arr_data[10] = $tot_target1 / $tot_all1 * 100;
			$arr_data[11] = $tot_all1;
			$arr_tot_col[11] += $tot_all1;

			$xlsRow++;
			$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1, $wsdata_wraptext, $wsdata_rotate, $ws_font_color, $ws_fill_color);
//			$result = $pdf->add_data_tab($arr_data, 7, $data_border, $data_align, "", $data_font_size, $data_font_style, $data_font_color, $data_fill_color);
//			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
//			echo "$data_count $ORDER $PER_NAME $POSITION<br>";
		} // end for

		$arr_data = array("",0,0,0,0,0,0,0,0,0,0,0);
		$arr_data[0] = $arr_tot_col[0];
		$arr_data[1] = $arr_tot_col[1];
		$arr_data[2] = $arr_tot_col[1] / $arr_tot_col[11] * 100;
		$arr_data[3] = $arr_tot_col[3];
		$arr_data[4] = $arr_tot_col[3] / $arr_tot_col[11] * 100;
		$arr_data[5] = $arr_tot_col[5];
		$arr_data[6] = $arr_tot_col[5] / $arr_tot_col[11] * 100;
		$arr_data[7] = $arr_tot_col[7];
		$arr_data[8] = $arr_tot_col[7] / $arr_tot_col[11] * 100;
		$arr_data[9] = $arr_tot_col[9];
		$arr_data[10] = $arr_tot_col[9] / $arr_tot_col[11] * 100;
		$arr_data[11] = $arr_tot_col[11];

		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1, $wsdata_wraptext, $wsdata_rotate, $ws_font_color, $ws_fill_color);
//		$result = $pdf->add_data_tab($arr_data, 7, $data_border, $data_align, "", $tot_font_size, $tot_font_style, $tot_font_color, $tot_fill_color);
//		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

//		$pdf->close_tab(""); 
	}else{
//		$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
//		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งบรรจุรับโอน.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งบรรจุรับโอน.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>