<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("rpt_function.php");

	include ("0_rpttest_excel_color_format.php");	// ��੾�����ǹ����ŧ COLUMN_FORMAT ��ҹ��
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$company_name = "���ͺ";
	$report_title = "��§ҹ��÷��ͺ excel";
	$report_code = "RtestColor";

//	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_testexcel.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// ��˹���ҵ�������;���� ��ǹ�����§ҹ
		$ws_head_line1 = array('1.�� �� ��� aqua','2.�� �� ��� cyan','3.�� �� ��� black','4.�� �� ��� blue','5.�� �� ��� brown','6.�� �� ��� magenta','7.�� �� ��� fuchsia','8.�� �� ��� gray','9.�� �� ��� grey','10.�� �� ��� green','11.�� �� ��� lime','12.�� �� ��� navy','13.�� �� ��� orange','14.�� �� ��� purple','15.�� �� ��� red','16.�� �� ��� silver','17.�� �� ��� white','18.�� �� ��� yellow');
		$ws_head_line2 = array('1.�� ��� ��� aqua','2.�� ��� ��� cyan','3.�� ��� ��� black','4.�� ��� ��� blue','5.�� ��� ��� brown','6.�� ��� ��� magenta','7.�� ��� ��� fuchsia','8.�� ��� ��� gray','9.�� ��� ��� grey','10.�� ��� ��� green','11.�� ��� ��� lime','12.�� ��� ��� navy','13.�� ��� ��� orange','14.�� ��� ��� purple','15.�� ��� ��� red','16.�� ��� ��� silver','17.�� ��� ��� white','18.�� ��� ��� yellow');
		$ws_head_line3 = array('1.�� ����ͧ ��� aqua','2.�� ����ͧ ��� cyan','3.�� ����ͧ ��� black','4.�� ����ͧ ��� blue','5.�� ����ͧ ��� brown','6.�� ����ͧ ��� magenta','7.�� ����ͧ ��� fuchsia','8.�� ����ͧ ��� gray','9.�� ����ͧ ��� grey','10.�� ����ͧ ��� green','11.�� ����ͧ ��� lime','12.�� ����ͧ ��� navy','13.�� ����ͧ ��� orange','14.�� ����ͧ ��� purple','15.�� ����ͧ ��� red','16.�� ����ͧ ��� silver','17.�� ����ͧ ��� white','18.�� ����ͧ ��� yellow');
		$ws_head_line4 = array('1.�� ᴧ ��� aqua','2.�� ᴧ ��� cyan','3.�� ᴧ ��� black','4.�� ᴧ ��� blue','5.�� ᴧ ��� brown','6.�� ᴧ ��� magenta','7.�� ᴧ ��� fuchsia','8.�� ᴧ ��� gray','9.�� ᴧ ��� grey','10.�� ᴧ ��� green','11.�� ᴧ ��� lime','12.�� ᴧ ��� navy','13.�� ᴧ ��� orange','14.�� ᴧ ��� purple','15.�� ᴧ ��� red','16.�� ᴧ ��� silver','17.�� ᴧ ��� white','18.�� ᴧ ��� yellow');
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line4 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TRBL","TRLB","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_border_line2 = array("TRBL","TRLB","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_border_line3 = array("TRBL","TRLB","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_border_line4 = array("TRBL","TRLB","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20);
		$ws_wraptext_line = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_rotate_line = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
//		$ws_fill_color = array("EEEEFF","AAAAAA","EEEEFF","999999","EEEEFF","999999","EEEEFF","999999","EEEEFF","999999","EEEEFF","999999","EEEEFF","999999","EEEEFF","999999","AAAAAA","EEEEFF");
//		$ws_font_color = array("0066CC","555500","0066CC","555555","0066CC","555555","555500","0066CC");
		$ws_fill_color = array('aqua','cyan','black','blue','brown','magenta','fuchsia','gray','grey','green','lime','navy','orange','purple','red','silver','white','yellow');
		$ws_font_color1 = array("black","black","black","black","black","black","black","black","black","black","black","black","black","black","black","black","black","black");
		$ws_font_color2 = array("white","white","white","white","white","white","white","white","white","white","white","white","white","white","white","white","white","white");
		$ws_font_color3 = array("yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow","yellow");
		$ws_font_color4 = array("red","red","red","red","red","red","red","red","red","red","red","red","red","red","red","red","red","red");
	// ����á�˹���ҵ�������;���� ��ǹ�����§ҹ	

	function print_header(){
		global $workbook, $worksheet, $xlsRow;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_head_line4;
		global $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_colmerge_line4;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_border_line4;
		global $ws_fontfmt_line;
		global $ws_headalign_line, $ws_width;
		global $ws_wraptext_line, $ws_rotate_line, $ws_fill_color, $ws_font_color1, $ws_font_color2, $ws_font_color3, $ws_font_color4;
		
		// loop ��˹��������ҧ�ͧ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}

		// loop ����� head ��÷Ѵ��� 1
		$xlsRow=0;
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $ws_fontfmt_line, $ws_head_line1, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line1, $ws_colmerge_line1, $ws_headalign_line, $ws_wraptext_line, $ws_rotate_line, $ws_font_color1, $ws_fill_color);
		// loop ����� head ��÷Ѵ��� 2
		$xlsRow++;
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $ws_fontfmt_line, $ws_head_line2, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line, $ws_wraptext_line, $ws_rotate_line, $ws_font_color2, $ws_fill_color);
		// loop ����� head ��÷Ѵ��� 3
		$xlsRow++;
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $ws_fontfmt_line, $ws_head_line3, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line3, $ws_colmerge_line3, $ws_headalign_line, $ws_wraptext_line, $ws_rotate_line, $ws_font_color3, $ws_fill_color);
		// loop ����� head ��÷Ѵ��� 4
		$xlsRow++;
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $ws_fontfmt_line, $ws_head_line4, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line4, $ws_colmerge_line4, $ws_headalign_line, $ws_wraptext_line, $ws_rotate_line, $ws_font_color4, $ws_fill_color);

		// loop ����� head ��÷Ѵ��� 1
/*		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $ws_fontfmt_line[$arr_column_map[$i]], $ws_headalign_line[$arr_column_map[$i]], $border, $merge, $ws_wraptext_line[$arr_column_map[$i]], $ws_rotate_line[$arr_column_map[$i]], $ws_font_color1[$arr_column_map[$i]], $ws_fill_color[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop ����� head ��÷Ѵ��� 2
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $ws_fontfmt_line[$arr_column_map[$i]], $ws_headalign_line[$arr_column_map[$i]], $border, $merge, $ws_wraptext_line[$arr_column_map[$i]], $ws_rotate_line[$arr_column_map[$i]], $ws_font_color2[$arr_column_map[$i]], $ws_fill_color[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop ����� head ��÷Ѵ��� 3
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line3[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $ws_fontfmt_line[$arr_column_map[$i]], $ws_headalign_line[$arr_column_map[$i]], $border, $merge, $ws_wraptext_line[$arr_column_map[$i]], $ws_rotate_line[$arr_column_map[$i]], $ws_font_color3[$arr_column_map[$i]], $ws_fill_color[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop ����� head ��÷Ѵ��� 4
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line4[$arr_column_map[$i]], $ws_border_line4[$arr_column_map[$i]], $ws_colmerge_line4[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $ws_fontfmt_line[$arr_column_map[$i]], $ws_headalign_line[$arr_column_map[$i]], $border, $merge, $ws_wraptext_line[$arr_column_map[$i]], $ws_rotate_line[$arr_column_map[$i]], $ws_font_color4[$arr_column_map[$i]], $ws_fill_color[$arr_column_map[$i]]));
				$colseq++;
			}
		}*/
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