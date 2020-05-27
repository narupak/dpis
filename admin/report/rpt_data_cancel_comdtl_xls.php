<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("../report/rpt_function.php");

	include ("rpt_data_cancel_comdtl_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$company_name = "";
	$report_title = "สอบถามข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม";
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
		$ws_width = array(10,20,50,18,30,20);
		$ws_head_line1 = array("$SEQ_NO_TITLE","$COM_NO_TITLE","$COM_NAME_TITLE","$COM_DATE_TITLE","$COM_TYPE_TITLE","$CONFIRM_TITLE");
		$ws_colmerge_line1 = array(0,0,0,0,0,0);
		$ws_border_line1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_wraptext_line1 = array(1,1,1,1,1,1);
		$ws_rotate_line1 = array(0,0,0,0,0,0);
		$ws_fontfmt_line1 = array("B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C");
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
		global $SEQ_NO_TITLE, $COM_NO_TITLE, $COM_NAME_TITLE, $COM_DATE_TITLE, $COM_TYPE_TITLE, $CONFIRM_TITLE, $INQ_TITLE;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_colmerge_line1, $ws_border_line1;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_rotate_line1, $ws_rotate_line2, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

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
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		

	if($DPISDB=="odbc"){	
		$cmd = "	select	distinct COM_ID, COM_NO, a.COM_NAME, COM_DATE, a.COM_TYPE, COM_CONFIRM 
						from		PER_COMMAND a, PER_COMTYPE b 
						where COM_GROUP in ('513') and a.COM_TYPE=b.COM_TYPE
									$search_condition 
						order by 	COM_DATE desc, COM_NO 	";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 	distinct COM_ID, COM_NO, a.COM_NAME, COM_DATE, a.COM_TYPE, COM_CONFIRM
								  from 		PER_COMMAND a, PER_COMTYPE b 
								  where 		COM_GROUP in ('513') and a.COM_TYPE=b.COM_TYPE
												$search_condition
								  order by 	COM_DATE desc, COM_NO ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select	distinct	COM_ID, COM_NO, a.COM_NAME, COM_DATE, a.COM_TYPE, COM_CONFIRM 
						from		PER_COMMAND a, PER_COMTYPE b 
						where COM_GROUP in ('513') and a.COM_TYPE=b.COM_TYPE
									$search_condition 
						order by 	COM_DATE desc, COM_NO ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_COM_ID = $data[COM_ID];
		$TMP_COM_NO = trim($data[COM_NO]);
		$TMP_COM_NAME = trim($data[COM_NAME]);
		$TMP_COM_DATE = show_date_format($data[COM_DATE], 5);
		$TMP_COM_CONFIRM = trim($data[COM_CONFIRM]);
		
		$TMP_COM_TYPE = trim($data[COM_TYPE]);
		$TMP_COM_TYPE_NAME = "";
		$cmd = "select COM_NAME from PER_COMTYPE where COM_TYPE='$TMP_COM_TYPE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_COM_TYPE_NAME = trim($data2[COM_NAME]);

		if ($TMP_COM_CONFIRM==1) { 
			$confirm = "ยืนยัน";
		} else {
			$confirm = "---";
		}

		$arr_content[$data_count][num] = $data_row;
		$arr_content[$data_count][com_no] = $TMP_COM_NO;
		$arr_content[$data_count][com_name] = $TMP_COM_NAME;
		$arr_content[$data_count][com_date] = $TMP_COM_DATE;
		$arr_content[$data_count][com_type] = $TMP_COM_TYPE_NAME;
		$arr_content[$data_count][com_confirm] = $confirm;
				
		$data_count++;
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
			$wsdata_border_3 = (array) null;
			$wsdata_colmerge_1 = (array) null;
			$wsdata_wraptext = (array) null;
			for($k=0; $k<count($ws_head_line1); $k++) {
				$wsdata_fontfmt_1[] = "";
				$wsdata_align_1[] = "C";
				$wsdata_border_1[] = "LR";
				$wsdata_border_3[] = "LRB";
				$wsdata_colmerge_1[] = 0;
				$wsdata_wraptext[] = 1;
			}
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$data_num = $arr_content[$data_count][num];
			$TMP_COM_NO = $arr_content[$data_count][com_no];
			$TMP_COM_NAME = $arr_content[$data_count][com_name];
			$TMP_COM_DATE = $arr_content[$data_count][com_date];
			$TMP_COM_TYPE_NAME = $arr_content[$data_count][com_type];
			$confirm = $arr_content[$data_count][com_confirm];

			$arr_data = (array) null;
			$arr_data[] = $data_num;
			$arr_data[] = $TMP_COM_NO;
			$arr_data[] = $TMP_COM_NAME;
			$arr_data[] = $TMP_COM_DATE;
			$arr_data[] = $TMP_COM_TYPE_NAME;
			$arr_data[] = $confirm;
	
			$wsdata_align = array("C", "C", "L", "C", "L", "C");

			if ($data_count==count($arr_content)-1) $wsdata_border = $wsdata_border_3; else $wsdata_border = $wsdata_border_1;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
	
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == $colshow_cnt-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"สอบถามข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม.xls\"");
	header("Content-Disposition: inline; filename=\"สอบถามข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>