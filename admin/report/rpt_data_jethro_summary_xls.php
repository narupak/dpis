<? 
	include("../../php_scripts/connect_database.php");
	//include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("rpt_function.php");
	
	if(!$year_10){
		if(date("Y-m-d") <= date("Y")."-10-01") 
		$year_10 = date("Y") + 543;
		else 
		$year_10 = (date("Y") + 543) + 1;
	} // end if
	
	include ("rpt_data_jethro_summary_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	//$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$company_name = "";
	$report_title = "สรุปจำนวนครั้งการประชุมคณะกรรมการการกำหนดตำแหน่งระดับสูงของกระทรวง||ย้อนหลังนับจาก ปีงบประมาณ $year_10";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "jethro";

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
	$ws_border_line1 = (array) null;
	$ws_border_line2 = (array) null;
	$ws_fontfmt_line1 = (array) null;
	$ws_headalign_line1 = (array) null;
	for($i=0; $i < count($heading_text); $i++) {
		$buff = explode("|",$heading_text[$i]);
		$ws_head_line1[] = $buff[0];
		$ws_head_line2[] = $buff[1];
		$ws_colmerge_line1[] = 0;
		$ws_colmerge_line2[] = 0;
		$ws_border_line1[] = "TLR";
		$ws_border_line2[] = "LBR";
		$ws_fontfmt_line1[] = "B";
		$ws_headalign_line1[] = "C";
	}
	$ws_width = array(43,7,7,7,7,7,7,7,7,7,7,7,25);
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	function print_header(){
		global $worksheet, $xlsRow;
		//global $heading_name, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2, $SESS_DEPARTMENT_NAME;
		global $arr_column_map, $arr_column_sel, $arr_column_align;
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2, $ws_fontfmt_line1, $ws_fontfmt_line2;
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
		$cmd = " select distinct(a.DEPARTMENT_ID),b.ORG_NAME as DEPARTMENT_NAME   
						 from PER_JETHRO  a, PER_ORG b 
						where a.DEPARTMENT_ID=b.ORG_ID(+)		
						";
	}elseif($DPISDB=="oci8"){
		$cmd = " select distinct(a.DEPARTMENT_ID),b.ORG_NAME as DEPARTMENT_NAME   
						 from PER_JETHRO  a, PER_ORG b 
						where a.DEPARTMENT_ID=b.ORG_ID(+)		
						";						 
	}elseif($DPISDB=="mysql"){
		$cmd = " select distinct(a.DEPARTMENT_ID),b.ORG_NAME as DEPARTMENT_NAME   
						 from PER_JETHRO  a, PER_ORG b 
						where a.DEPARTMENT_ID=b.ORG_ID(+)		
						";
	} // end if

	$count_page_data = $db_dpis->send_cmd($cmd);
	
	if($count_page_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}
		$xlsRow = 0;
		/**$temp_report_title = "$REF_NAME||$NAME||$report_title";
		$arr_title = explode("||", $temp_report_title);**/
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
		$wsdata_colmerge_1 = (array) null;
		$wsdata_fontfmt_2 = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$wsdata_fontfmt_1[] = "B";
			//$wsdata_align_1[] = "L";
			$wsdata_border_1[] = "TLRB";
			$wsdata_colmerge_1[] = 0;
			$wsdata_fontfmt_2[] = "";
		}
		$wsdata_align_1 = array("L","C","C","C","C","C","C","C","C","C","C","C","L");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
	
	
		$data_count = 0;
        while ($data = $db_dpis->get_array()) {
				$data_count++;
				// $temp_JETHRO_ID = $data[JETHRO_ID];
				//$current_list .= ((trim($current_list))?", ":"") . "'" . $temp_JETHRO_ID ."'";
				$DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$TMP_DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
				$arr_data = (array) null;
				$year_10 = $year_10-10;
				$total = 0;
				$arr_data[] = $data_count."  ".$TMP_DEPARTMENT_NAME;
				for ($i = 0; $i < 10; $i++) {
				$year_10++;
				$cmd = "select count(MEETING_YEAR) as MEETING_YEAR from PER_JETHRO where MEETING_YEAR='$year_10' and DEPARTMENT_ID=$DEPARTMENT_ID";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$DIS_DEPARTMENT_YEAR =  $data1[MEETING_YEAR];
				$arr_data[] = $DIS_DEPARTMENT_YEAR;
				$total += $DIS_DEPARTMENT_YEAR;
				}
				$arr_data[] = $total;
				$arr_data[] = "";
				
			$arr_aggreg_data = explode("|",do_aggregate($arr_data,$column_function,$arr_column_sel,$arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
        } // while
		
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
	








