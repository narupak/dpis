<?
	//include("../../php_scripts/connect_database.php");
	//include("../../php_scripts/calendar_data.php");
	require_once "../php_scripts/function_share.php";
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$report_title = "";
	$report_code = $report_name;

	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	echo $fname;
	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	
	//====================== SET FORMAT ======================//
	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	// print header
	
	$_col = 0;
	// init colunm width
	foreach($col_array as $header_key => $header_value) {
		$worksheet->set_column($_col, $_col, 20);
		$_col++;
	}
	
	// create header
	foreach($col_array as $header_key => $header_value) {
		$worksheet->write_string(0, $header_key, $header_value, set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	}

	// create content
	foreach($array_content as $content_key => $content_row){
		foreach($content_row as $content_col => $content_value) {
			$worksheet->write_string($content_key, $content_col, $content_value, set_format("xlsFmtTitle", "B", "L", "TLRB", 0));
		}
	} // end for
	$workbook->close();

	ini_set("max_execution_time", 30);

?>