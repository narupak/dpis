<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");

	$NUMBER_DISPLAY = $NUMBER_DISPLAY1;

	include ("rpt_R00test_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);

//	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "���ͺ��§ҹ";
	$report_code = "R00test_xls";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// ��˹���ҵ�������;���� ��ǹ�����§ҹ
		$ws_head_line1 = array("�ӴѺ���","��ͤ���","�ӹǹ","����ҳ");
		$ws_colmerge_line1 = array(0,0,0,0);
		$ws_border_line1 = array("TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line1 = array("B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C");
		$ws_width = array(10,50,20,20);
	// ����á�˹���ҵ�������;���� ��ǹ�����§ҹ	

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align;
		global $ws_head_line1, $ws_colmerge_line1, $ws_border_line1;
		global $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

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
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode(",",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		

	$arr_content[0][seq] = 1;
	$arr_content[0][name] = "��� 1";
	$arr_content[0][num] = 12;
	$arr_content[0][val] = 200.34;
	$arr_content[1][seq] = 2;
	$arr_content[1][name] = "��� 2";
	$arr_content[1][num] = 15.032;
	$arr_content[1][val] = 130.25;
	$arr_content[2][seq] = 3;
	$arr_content[2][name] = "��� 3";
	$arr_content[2][num] = 7.2;
	$arr_content[2][val] = 325.00;
	$arr_content[3][seq] = 4;
	$arr_content[3][name] = "��� 4";
	$arr_content[3][num] = 3012.2;
	$arr_content[3][val] = 7522.10;
	$arr_content[4][seq] = 5;
	$arr_content[4][name] = "��� 5";
	$arr_content[4][num] = 8;
	$arr_content[4][val] = 412.50;
	$arr_content[5][seq] = 6;
	$arr_content[5][name] = "��� 6";
	$arr_content[5][num] = 14.2000;
	$arr_content[5][val] = 125.67;
	$count_data = 6;

	if($count_data){
		$colshow_cnt=0;		// �Ҩӹǹ column ����ʴ���ԧ
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
		
		// ��˹���ҵ�������;���� ��ǹ������
			$wsdata_fontfmt_1 = array("B","B","B","B");
			$wsdata_align_1 = array("C","L","R","R");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","");
		// ����˹���ҵ�������;���� ��ǹ������

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$r_seq = $arr_content[$data_count][seq];
			$r_name = $arr_content[$data_count][name];
			$r_num = $arr_content[$data_count][num];
			$r_val = $arr_content[$data_count][val];

			$arr_data = (array) null;
			$arr_data[] = $r_seq;
			$arr_data[] = $r_name;
			$arr_data[] = $r_num;
			$arr_data[] = $r_val;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
					else $ndata = $arr_data[$arr_column_map[$i]];
					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			} // end if	
		} // end for
	}else{
		$arr_data = (array) null;
		$arr_data[] = "<**1**>********** ����բ����� **********";
		$arr_data[] = "<**1**>********** ����բ����� **********";
		$arr_data[] = "<**1**>********** ����բ����� **********";
		$arr_data[] = "<**1**>********** ����բ����� **********";

		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
				else $ndata = $arr_data[$arr_column_map[$i]];
				$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
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