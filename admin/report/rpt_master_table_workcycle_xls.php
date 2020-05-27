<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$worksheet = &$workbook->addworksheet("M1203");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 10);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 10);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 10);
		
		$worksheet->write($xlsRow, 0, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อรอบ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เวลาเข้า", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เวลาออก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "เวลาที่ไม่สาย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "สิ้นสุดเวลาสาย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "เวลาเข้า (กรณีทำงานครึ่งวันหลัง)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "เวลาออก (กรณีทำงานครึ่งวันแรก)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if



  	if(trim($search_code)) $arr_search_condition[] = "(a.WC_CODE like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(a.WC_NAME like '%$search_name%')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	
		$cmd = "	select		a.WC_CODE, a.WC_NAME, a.WC_START, a.WC_END, a.WC_ACTIVE,
                                a.WC_SEQ_NO,a.TIME_LEAVEEARLY,a.TIME_LEAVEAFTER,a.ON_TIME,
                                a.END_LATETIME
								from 		PER_WORK_CYCLE a
								WHERE a.WORKCYCLE_TYPE = 1 
												$search_condition
												
								order by 	a.WC_SEQ_NO,a.WC_CODE, a.WC_NAME ";
	
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$DATA_WC_CODE = $data[WC_CODE];
			$DATA_WC_NAME = $data[WC_NAME];
			$DATA_WC_START = substr($data[WC_START],0,2).":".substr($data[WC_START],2,2)." น.";
			$DATA_WC_END = substr($data[WC_END],0,2).":".substr($data[WC_END],2,2)." น.";
			
			$DATA_TIME_LEAVEEARLY ="";
			if($data[TIME_LEAVEEARLY] != "0000"){
				$DATA_TIME_LEAVEEARLY = substr($data[TIME_LEAVEEARLY],0,2).":".substr($data[TIME_LEAVEEARLY],2,2)." น.";
			}
			
			$DATA_TIME_LEAVEAFTER ="";
			if($data[TIME_LEAVEEARLY] != "0000"){
				$DATA_TIME_LEAVEAFTER = substr($data[TIME_LEAVEAFTER],0,2).":".substr($data[TIME_LEAVEAFTER],2,2)." น.";
			}
			
			$DATA_P_EXTRATIME_SHOW ="";
			if($data[ON_TIME]){
				$DATA_P_EXTRATIME_SHOW = substr($data[ON_TIME],0,2).':'.substr($data[ON_TIME],2,2)." น.";
			}
			
			$DATA_P_TIMEOVERLATE_SHOW = "";
			if($data[END_LATETIME]){
				$DATA_P_TIMEOVERLATE_SHOW = substr($data[END_LATETIME],0,2).':'.substr($data[END_LATETIME],2,2)." น.";
			}
			
			$WC_ACTIVE = ($data[WC_ACTIVE]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $DATA_WC_CODE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $DATA_WC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_WC_START, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $DATA_WC_END, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $DATA_P_EXTRATIME_SHOW, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $DATA_P_TIMEOVERLATE_SHOW, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $DATA_TIME_LEAVEEARLY, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $DATA_TIME_LEAVEAFTER, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 8, $WC_ACTIVE, 35, 4, 1, 0.8);
		} // end while
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
		
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"M1203.xls\"");
	header("Content-Disposition: inline; filename=\"M1203.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>