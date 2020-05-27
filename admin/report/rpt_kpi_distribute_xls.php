<?	
	$time1 = time();
	ini_set("max_execution_time", $max_execution_time);

	include("../../php_scripts/connect_database.php");
	include("../php_scripts/session_start.php");
	include("../php_scripts/function_share.php");
	
	include ("rpt_function.php");
	include("../php_scripts/kpi_distribute_function.php");

	include("rpt_kpi_distribute_data.php");
	include("rpt_kpi_distribute_format.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_kpi_ref = 0;
	$sheet_no = 0; $sheet_no_text="";
	
	$company_name = "";
	if ($tree_depth > 2) $step = $tree_depth-1;	// คือ depth 3 คือ ขั้น 2 และ 4 คือ ขั้น 3
	else $step = $tree_depth;
	$report_title = "ขั้นตอนที่ ".trim($step)." ตารางการกระจายตัวชี้วัดตามแผนปฏิบัติราชการประจำปี ".$KPI_YEAR."||ของ ".implode(" ",$arr_head_org);
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "xls_KPI_DISTRIBUTE";
	
	$token = md5(uniqid(rand(), true)); 
//	$fname= "../../Excel/tmp/dpis_$token.xls";
	$fname= "../../Excel/tmp/rpt_kpi_distribute";
	$fname1=$fname.".xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$arr_file = (array) null;
	$f_new = false;

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
	$ws_head_line1 = (array) null;
	$ws_head_line2 = (array) null;
	$ws_colmerge_line1 = (array) null;
	$ws_colmerge_line2 = (array) null;
	$ws_border_line1 = (array) null;
	$ws_border_line2 = (array) null;
	$ws_wraptext_line1 = (array) null;
	$ws_rotate_line1 = (array) null;
	$ws_fontfmt_line1 = (array) null;
	$ws_headalign_line1 = (array) null;
	$ws_width = (array) null;
	$colshow_cnt = 0;
	if (strpos($arr_head_table[0],"|")!==false) $f_head2line = true; else $f_head2line = false;
	for($col_count = 0;  $col_count < count($arr_head_table); $col_count++) {
		if ($arr_column_sel[$arr_column_map[$col_count]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
			$colshow_cnt++;		// นับเฉพาะ column ที่แสดง
		}
		$arr_buff = explode("|",$arr_head_table[$col_count]);
		if ($f_head2line) {
			$ws_head_line1[] = $arr_buff[0];
			$ws_head_line2[] = $arr_buff[1];
			if (strpos($arr_buff[0],"<**")!==false)	$f_grp=true; // เป็นกลุ่ม
			else $f_grp=false;
			if ($f_grp) {
				$ws_colmerge_line1[] = 1;	// column กลุ่ม ให้เป็น merge 
				$ws_border_line1[] = "TLBR";
				$ws_border_line2[] = "TLBR";
			} else {
				$ws_colmerge_line1[] = 0;	// ไม่กลุ่ม ไม่ merge 
				if (!$arr_buff[1]) {
					$ws_border_line1[] = "TLR";
					$ws_border_line2[] = "LBR";
				} else {
					$ws_border_line1[] = "TLBR";
					$ws_border_line2[] = "TLBR";
				}
			}
			$ws_colmerge_line2[] = 0;
		} else {
			$ws_head_line1[] = $arr_buff[0];
			$ws_colmerge_line1[] = 0;
			$ws_border_line1[] = "TLBR";
		}
		$ws_wraptext_line1[] = 1;
		$ws_rotate_line1[] = 0;
		$ws_fontfmt_line1[] = "B";
		if ($col_count==0) {
			$ws_headalign_line1[] = "C";
			$ws_width[] = 50;
		} else {
			$ws_headalign_line1[] = "C";
			$ws_width[] = 20;
		}
	}
//	echo "ws_head_line1=".implode(",", $ws_head_line1)."<br>";
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
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_border_line1, $ws_border_line2, $ws_border_line3;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_rotate_line1, $ws_rotate_line2, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

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
		if (count($ws_head_line2) > 0) {
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
		}
	} // function		

	if ($xlsRow) $xlsRow++;
	$arr_title = explode("||", $report_title);
	for($i=0; $i<count($arr_title); $i++){
		$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
		for($j=0; $j < $colshow_cnt-1; $j++) 
			$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$xlsRow++;
	} // end if
		
	if($company_name){
		$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
		for($j=0; $j < $colshow_cnt-1; $j++) 
			$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$xlsRow++;
	} // end if

	print_header();
	
		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		$wsdata_fontfmt_1 = (array) null;
		$wsdata_align_1 = (array) null;
		$wsdata_border_1 = (array) null;
		$wsdata_colmerge_1 = (array) null;
		$wsdata_wraptext = (array) null;
		$wsdata_rotate = (array) null;
		for($k=0; $k<count($ws_head_line1); $k++) {
			$wsdata_fontfmt_1[] = "";
			if ($k==0)
				$wsdata_align_1[] = "L";
			else
				$wsdata_align_1[] = "C";
			$wsdata_border_1[] = "LBR";
			$wsdata_colmerge_1[] = 0;
			$wsdata_wraptext[] = 1;
			$wsdata_rotate[] = 0;
		}
	// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

	$arr_data = (array) null;
	//$data_count = 0;
	$count_data = count($arr_content);
//	echo "count_data=$count_data<br>";
	if ($count_data) {
		for($data_count=0; $data_count < count($arr_content); $data_count++) {
			// เช็คจบแต่ละ file ตาม $file_limit
//			echo "($data_count) ".explode(",",$arr_content[$data_count])."<br>";
			if ($data_count > 0 && ($data_count % $file_limit) == 0) {
				$workbook->close();
				$arr_file[] = $fname1;

				$fnum++; $fnum_text="_$fnum";
				$fname1=$fname.$fnum_text.".xls";
				$workbook = new writeexcel_workbook($fname1);

			//====================== SET FORMAT ======================//
				require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
			//====================== SET FORMAT ======================//

				$f_new = true;
			};
				
			// เช็คจบที่ข้อมูลแต่ละ sheet ตาม $data_limit
			if(($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
				if ($f_new) {
					$sheet_no=0; $sheet_no_text="";
					$f_new = false;
				} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
					$sheet_no++;
					$sheet_no_text = "_$sheet_no";
				}

				$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);

				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
			
				print_header();
			}
			
			$arr_data = $arr_content[$data_count];
//			echo "arr_data=".implode(",",$arr_data)."<br>";
			$xlsRow++;
			$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1, $wsdata_wraptext, $wsdata_rotate, $ws_font_color, $ws_fill_color);
//			$colseq=0;
//			for($i=0; $i < count($arr_column_map); $i++) {
//				if ($arr_column_sel[$arr_column_map[$i]]==1) {
//					$ndata = $arr_data[$arr_column_map[$i]];
//					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
//					$colseq++;
//				}
//			}
		} // end for
	} else {
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($col_count = 1;  $col_count < count($arr_head_table); $col_count++) {
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
	}


	$workbook->close();

	$arr_file[] = $fname1;

	require_once("excel_tailpart_subrtn.php");

?>