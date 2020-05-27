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
	$worksheet = &$workbook->addworksheet("M1201");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//	
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;		

		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 50);
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 10);

		$worksheet->write($xlsRow, 0, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "สถานที่ปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ชื่ออื่นๆ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // end if

	

  	if(trim($search_code)) $arr_search_condition[] = "(WL_CODE like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(WL_NAME like '%$search_name%')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	
	$cmd = "	select		WL_CODE, WL_NAME, WL_ACTIVE, WL_SEQ_NO, WL_OTHERNAME
							from		PER_WORK_LOCATION
							$search_condition
							order by WL_SEQ_NO,WL_CODE, WL_NAME  ";
	

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;		
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$WL_CODE = $data[WL_CODE];
			$WL_NAME = $data[WL_NAME];
			$WL_OTHERNAME = $data[WL_OTHERNAME];
			$WL_ACTIVE = ($data[WL_ACTIVE]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $WL_CODE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $WL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $WL_OTHERNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 3, $WL_ACTIVE, 35, 4, 1, 0.8);			
		} // end while

	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"M1201.xls\"");
	header("Content-Disposition: inline; filename=\"M1201.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh); 	
	unlink($fname);	
?>