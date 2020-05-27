<?
	//include("../../php_scripts/connect_database.php");
	//include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
    $company_name = "????????????????? : $list_type_text";
	$report_title = "";
	$report_code = $report_name;

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = &new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	
	//====================== SET FORMAT ======================//

	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	// print header
	$xlsRow = 0;
	$arr_title = explode("||", $report_title);
	$worksheet->set_column(0, 0, 60);
	$worksheet->set_column(1, 1, 8);
	$worksheet->set_column(2, 2, 8);
	$worksheet->set_column(3, 3, 8);
	$worksheet->set_column(4, 4, 8);
	$worksheet->set_column(5, 5, 8);
	$worksheet->set_column(6, 6, 8);
	$worksheet->set_column(7, 7, 8);
	$worksheet->set_column(8, 8, 8);

	$worksheet->write($xlsRow, 0, "$heading_name", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
	$worksheet->write($xlsRow, 1, '1', set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	$worksheet->write($xlsRow, 2, '2', set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	$worksheet->write($xlsRow, 3, '3', set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	$worksheet->write($xlsRow, 4, '4', set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	$worksheet->write($xlsRow, 5, '5', set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	$worksheet->write($xlsRow, 6, '6', set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	$worksheet->write($xlsRow, 7, '7', set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	$worksheet->write($xlsRow, 8, '8', set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

	$count_org_ref = 0;
	// print data 
	for($data_count=0; $data_count<count($arr_content); $data_count++){
		$REPORT_ORDER = $arr_content[$data_count][type];
		$NAME = $arr_content[$data_count][name];
		$DEPARTMENT_ID = $arr_content[$data_count][id];

		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTitle", "B", "L", "TLRB", 0));
		$worksheet->write($xlsRow, 1, '1', set_format("xlsFmtTitle", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 2, '2', set_format("xlsFmtTitle", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 3, '3', set_format("xlsFmtTitle", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 4, '4', set_format("xlsFmtTitle", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 5, '5', set_format("xlsFmtTitle", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 6, '6', set_format("xlsFmtTitle", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 7, '7', set_format("xlsFmtTitle", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 8, '8', set_format("xlsFmtTitle", "B", "R", "TLRB", 0));

		$count_org_ref++;

	} // end for
	$workbook->close();

	ini_set("max_execution_time", 30);
	/*
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);*/
    
?>