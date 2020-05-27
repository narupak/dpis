<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("rpt_function.php");

	$report_title = stripslashes($report_title);
	$sel_cmd= stripslashes($sel_cmd);
//	echo "report_title=$report_title , sel_cmd=$sel_cmd<br>";

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", 0);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$company_name = "";
	$report_code = "rpt_sqltest_xls";

//	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/rpt_sqltest_xls.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	$count_data = $db_dpis->send_cmd($sel_cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	
//	begin set format
	include ("rpt_sqltest_format.php");
//	end set format

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		$ws_width = $heading_width;
//		echo "ws_width=".implode(",",$ws_width)."<br>";
		$ws_head_line = $heading_text;
		$ws_colmerge_line = (array) null;
		$ws_border_line = (array) null;
		$ws_fontfmt_line = (array) null;
		$ws_headalign_line = (array) null;
		$ws_wraptext_line = (array) null;
		$ws_rotate_line = (array) null;
		$ws_fill_color = (array) null;
		$ws_font_color = (array) null;	//array("black","black","black","black","black","black","black","black","black","black","black","black","black","black","black","black","black","black");
		for($i=0; $i < count($ws_width); $i++) {
			$ws_colmerge_line[] = 0;
			$ws_border_line[] = "TRBL";
			$ws_fontfmt_line[] = "B";
			$ws_headalign_line[] = "C";
			$ws_wraptext_line[] = 1;
			$ws_rotate_line[] = 0;
			$ws_fill_color[] = 0;
			$ws_font_color[] = "black";
		}
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
		global $workbook, $worksheet, $xlsRow;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line;
		global $ws_colmerge_line;
		global $ws_border_line;
		global $ws_fontfmt_line;
		global $ws_headalign_line, $ws_width;
		global $ws_wraptext_line, $ws_rotate_line, $ws_fill_color, $ws_font_color;
		
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
		$xlsRow++;
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line, $ws_head_line, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line, $ws_colmerge_line, $ws_headalign_line, $ws_wraptext_line, $ws_rotate_line, $ws_font_color, $ws_fill_color);
	} // function		
		
	$count_data = $db_dpis->send_cmd($sel_cmd);
	if($count_data){		
		$arr_title = explode("||", "$report_title");
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			for($j=0; $j < count($ws_width); $j++) {
				if ($j==0)
					$worksheet->write($xlsRow, $j, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
				else
					$worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
		} // end if
		
		if($company_name){
			$xlsRow++;
			for($j=0; $j < count($ws_width); $j++) {
				if ($j==0)
					$worksheet->write($xlsRow, $j, $company_name, set_format("xlsFmtTitle", "B", "L", "", 1));
				else
					$worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			}
		} // end if
		
		print_header();
		
		//	data
		$wsdata_colmerge = (array) null;
		$wsdata_border = (array) null;
		$wsdata_fontfmt = (array) null;
		$wsdata_align = (array) null;
		$wsdata_wraptext = (array) null;
		$wsdata_rotate = (array) null;
		$wsdata_fill_color = (array) null;
		$wsdata_font_color = (array) null;	//array("black","black","black","black","black","black","black","black","black","black","black","black","black","black","black","black","black","black");
		for($i=0; $i < count($ws_width); $i++) {
			$wsdata_colmerge[] = 0;
			$wsdata_border[] = "TRBL";
			$wsdata_fontfmt[] = "";
			$wsdata_align[] = "C";
			$wsdata_wraptext[] = 1;
			$wsdata_rotate[] = 0;
			$wsdata_fill_color[] = 0;
			$wsdata_font_color[] = "black";
		}
		// end data format
		
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data = array_change_key_case($data, CASE_LOWER);
			$data_count++;
			$data_row++;			

			$data_align = (array) null;
			$arr_data = (array) null;			
			foreach($data as $key => $val) {
				$arr_data[] = $val;
				$data_align[] = "C";
			}

			// พิมพ์
			$xlsRow++;
			$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border, $wsdata_colmerge, $wsdata_align, $wsdata_wraptext, $wsdata_rotate, $wsdata_font_color, $wsdata_fill_color);
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 0);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"epis.xls\"");
	header("Content-Disposition: inline; filename=\"epis.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>