<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$NUMBER_DISPLAY = 1;
	include ("rpt_function.php");

	include ("0_rpttest_excel_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
	
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
		$ws_head_line2 = array("","คอลัมน์ 1 นี้ตัดอัตโนมัติตามธรรมชาติ","คอลัมน์ 2 นี้\nตัดไปแล้วหลังคำนี้","คอลัมน์ 3 นี้ จะตัดตรงนี้\nและก็ อีกที่ ตรงนี้\nก็แล้วกัน","คอลัมน์ 4","คอลัมน์ 5","คอลัมน์ 6","");
		$ws_colmerge_line1 = array(0,1,1,1,1,1,1,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TRL","TLB","TBR","TLB","TB","TB","TBR","TRL");
		$ws_border_line2 = array("RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C");
		$ws_width = array(50,15,15,15,15,15,15,20);
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

	print_header();
	
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