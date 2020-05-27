<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$NUMBER_DISPLAY = 1;
	include ("rpt_function.php");

	include ("0_rpttest_excel1_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$company_name = "ทดสอบ";
	$report_title = "รายงานการทดสอบ excel";
	$report_code = "Rtest";

//	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_testexcel.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		$ws_head_line1 = array("คำอธิบาย","<**1**>ชุด 1","<**1**>ชุด 1","<**2**>ชุด 2","<**2**>ชุด 2","<**2**>ชุด 2","<**2**>ชุด 2","รวม");
		$ws_head_line2 = array("","คอลัมน์ 1","คอลัมน์ 2","คอลัมน์ 3","คอลัมน์ 4","คอลัมน์ 5","คอลัมน์ 6","");
		$ws_colmerge_line1 = array(0,1,1,1,1,1,1,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TRL","TLB","TBR","TLB","TB","TB","TBR","TRL");
		$ws_border_line2 = array("RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C");
		$ws_width = array(50,15,15,15,15,15,15,20);
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	function print_header(){
		global $worksheet, $xlsRow, $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3;
		global $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3;
		global $ws_fontfmt_line1, $ws_fontfmt_line2, $ws_fontfmt_line3;
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
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line1, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line1, $ws_colmerge_line1, $ws_headalign_line1);
/*		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}*/
		
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line2, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line1);
/*		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}*/
	} // function		

	$count_data = 4;
	$arr_data1[] = array("บรรทัดที่ 1",230,310,240,210,130,255);
	$arr_data1[] = array("บรรทัดที่ 2",110,150,320,280,400,165);
	$arr_data1[] = array("บรรทัดที่ 3",540,250,420,280,200,265);
	$arr_data1[] = array("บรรทัดที่ 4",260,350,120,380,100,465);	

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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B");
//			$wsdata_align_1 = array("L","C","C","C","C","C","C","C");
			$wsdata_align_1 = array("L","R","R","R","R","R","R","R");
			$wsdata_border_1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
			$wsdata_border_2 = array("","","","","","","","");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0);
			$wsdata_colmerge_2 = array(1,1,1,1,1,1,1,1);
			$wsdata_fontfmt_2 = array("","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		$arr_t = array("รวม",0,0,0,0,0,0);
		$gs = 0;
		for($ii = 0; $ii < $count_data; $ii++) {
			$s = 0;
			$arr_data = (array) null;
			for($jj = 0; $jj < count($arr_data1[$ii]); $jj++) {
				$arr_data[] = $arr_data1[$ii][$jj];
				if ($jj > 0) {
					$s += $arr_data1[$ii][$jj];
					$arr_t[$jj] += $arr_data1[$ii][$jj];
				}
			}
			$arr_data[] = $s;
//			echo "$ii--sum=$s<br>";

//			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
//			echo "aggreg=".implode(",",$arr_aggreg_data)."<br>";
				
			$xlsRow++;
			$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1);
/*
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
//					echo "ndata=$ndata<br>";
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata1 = $buff[0]; $border1 = $buff[1]; $colmerge1 = $buff[2]; $pgrp = $buff[3];
//					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$ndata1 = str_replace(",","",$ndata1);
//					$worksheet->write($xlsRow, $colseq, $ndata1, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border1, $colmerge1));
//					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					if ($i==0) {
						$spec_format = set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border1, $colmerge1);
						$spec_format->set_rotation(0); // 1.แนวตั้ง ตัวอักษรปกติ 2. แนวตั้งตัวอักษรนอนซ้าย
						$worksheet->write($xlsRow, $colseq, $ndata, $spec_format);
					} else 
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border1, $colmerge1));
					$colseq++;
				}
			}
*/
			$gs += $s;
		}
		$arr_t[] = $gs;

		$arr_data = $arr_t;
/*
		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
				else $ndata = $arr_data[$arr_column_map[$i]];
				$ndata = str_replace(",","",$ndata);
				$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1), $wsdata_align_1[$arr_column_map[$i]]));
				$ndata1 = $buff[0]; $border1 = $buff[1]; $colmerge1 = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata1, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border1, $colmerge1));
				$colseq++;
			}
		}
*/
		$arr_rotate = array(0,0,0,0,0,0,0,0);
		$arr_wraptext = array(0,0,0,0,0,0,0,0);

		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1, $arr_wraptext, $arr_rotate);

//		$arr_data = array("สรุป","<**1**>243","<**1**>243","<**2**>567","<**2**>567","<**3**>192","<**3**>192","777");
		$arr_data = array("สรุป","<**1**>243","<**1**>243","<**1**>243","<**2**>567","<**2**>567","<**2**>567","777");

		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1, $arr_wraptext, $arr_rotate);
		
		$arr_data = array("สรุป 2","<**1**>1235","<**1**>1235","<**2**>7231","<**2**>7231","<**3**>7822","<**3**>7822","563823");
		$arr_border = array("TLBR","TLB","TB","TLB","TB","TLB","TB","TLBR");
		$arr_merge = array(0,0,0,0,0,0,0,0);
		$arr_align = array("L","R","R","R","R","R","R","R");

		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $arr_border, $arr_merge, $arr_align, $arr_wraptext, $arr_rotate);

		$arr_data = array("สรุป 3","<**1**>3333","<**1**>3333","<**2**>4444","<**2**>4444","<**3**>5555","<**3**>5555","123446");
		$arr_border = array("TLBR","TLB","TB","TLB","TB","TLB","TB","TLBR");
		$arr_merge = array(0,0,0,0,0,0,0,0);
		$arr_align = array("R","L","L","R","R","C","C","R");

		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $arr_border, $arr_merge, $arr_align, $arr_wraptext, $arr_rotate);

		$arr_data = array("สรุป 4 แนวตั้ง","<**1**>7007","<**1**>7007","<**2**>6006","<**2**>6006","<**3**>5005","<**3**>5005","9009");
		$arr_border = array("TLBR","TLB","TB","TLB","TB","TLB","TB","TLBR");
		$arr_merge = array(0,0,0,0,0,0,0,0);
//		$arr_rotate = array(0,2,2,2,2,2,2,0);
		$arr_rotate = array(0,0,0,0,0,0,0,0);
		$arr_align = array("R","L","J","R","J","C","C","J");

		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $arr_border, $arr_merge, $arr_align, $arr_wraptext, $arr_rotate);

		$arr_data = array("สรุป 5 wrap text มาดูการตัดคำกันหน่อย\nบังคับตัดไปที่ล่ะ","การตัด\nคำ ทำการตัดคำนี้ excel จะตัดอัตโนมัติ","ลองดูซะหน่อยว่าจะตัดตรงไหนดี","พงษ์ศักดิ์ กุลประฑีปัญญา","พูดไปสองไฟเบี้ย นิ่งเสียตำลึกทอง","i am a boy who just in love","let the game begin in zone","สามสิ่งสู้ศึกฮึกหาญ หนึ่งมารสิ่งสู่อยู่หัตถา");
		$arr_border = array("TLBR","TLB","TLB","TLB","TLB","TLB","TLB","TLBR");
		$arr_merge = array(0,0,0,0,0,0,0,0);
		$arr_wraptext = array(1,1,1,1,1,1,1,1);
		$arr_rotate = array(0,0,0,0,0,0,0,0);
		$arr_align = array("R","L","L","R","R","C","C","R");

		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_1, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $arr_border, $arr_merge, $arr_align, $arr_wraptext, $arr_rotate);
/*		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				$ndata = $arr_data[$arr_column_map[$i]];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $arr_border[$arr_column_map[$i]], $arr_merge[$arr_column_map[$i]]));
				$colseq++;
			}
		}
*/
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "", "", "1"));
		for($j=1; $j<=(($TOTAL_LEVEL * 2) + 3); $j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "", "", "1"));
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