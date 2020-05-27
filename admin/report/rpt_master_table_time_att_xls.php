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
	$worksheet = &$workbook->addworksheet("M1202");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 20);
		$worksheet->set_column(1, 1, 55);
		$worksheet->set_column(2, 2, 50);
		$worksheet->set_column(3, 3, 10);
		
		$worksheet->write($xlsRow, 0, "หมายเลขเครื่อง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อเครื่องบันทึกเวลา", set_format("xlsFmtTableHeader", "B", "L", "TLR", 0));
		$worksheet->write($xlsRow, 2, "สถานที่ปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "L", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

	

  	if(trim($search_code)) $arr_search_condition[] = "(a.TA_CODE like '".trim($search_code)."%')";
  	if(trim($search_name)) $arr_search_condition[] = "(a.TA_NAME like '%".trim($search_name)."%')";
  	if(trim($search_wl_code)) $arr_search_condition[] = "(a.WL_CODE = '".trim($search_wl_code)."')";
  	
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="oci8"){
		$cmd = "	select		a.TA_CODE, a.TA_NAME, a.WL_CODE, a.TA_ACTIVE, b.WL_NAME
								from  		PER_TIME_ATT a
								 left join PER_WORK_LOCATION b on(b.WL_CODE=a.WL_CODE)
								where		1=1
												$search_condition
									order by  a.TA_CODE ASC, a.TA_NAME ASC  ";
	}

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$TA_CODE = $data[TA_CODE];
			$TA_NAME = $data[TA_NAME];
			$WL_NAME = $data[WL_NAME];
			$TA_ACTIVE = ($data[TA_ACTIVE]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
	
			

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $TA_CODE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $TA_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $WL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 3, $TA_ACTIVE, 35, 4, 1, 0.8);
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"M1202.xls\"");
	header("Content-Disposition: inline; filename=\"M1202.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>