<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("Sheet1");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 80);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 20);
		$worksheet->set_column(4, 4, 20);
		$worksheet->set_column(5, 5, 20);
		
		
		$worksheet->write($xlsRow, 0, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตนเอง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ผู้บังคับบัญชา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "เพื่อนร่วมงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ผู้ใต้บังคับบัญชา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		
	} // end if
	
  	//if(trim($search_POS_TYPE)) $arr_search_condition[] = "(POS_TYPE LIKE '$search_POS_TYPE%')";
  	if(trim($search_POS_NAME)) $arr_search_condition[] = "(POS_NAME LIKE '%$search_POS_NAME%')";
	$search_condition = "";
	$search_condition_count = "";
	if(count($arr_search_condition)) {
		$search_condition = " and " . implode(" and ", $arr_search_condition);
		$search_condition_count = " where " . implode(" and ", $arr_search_condition);
	}
	if($DPISDB=="odbc"){
		$cmd = "	select	POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO 
									from	PER_POS_TYPE ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO
								  from 		PER_POS_TYPE ";				
	}elseif($DPISDB=="mysql"){
		$cmd ="select POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO
								from PER_POS_TYPE ";
	} // end if

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
		

		foreach($print_search_condition as $show_condition){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$show_condition", set_format("xlsFmtTitle", "B", "L", "", 0));
		} // end foreach

		$xlsRow++;
		print_header($xlsRow);
		$data_count = $xlsRow;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$temp_primary = $data[POS_TYPE];
		$temp_POS_TYPE = trim($data[POS_TYPE]);		
		$temp_POS_NAME = trim($data[POS_NAME]);		
		$SEFT_RATIO = $data[SEFT_RATIO];
		$CHIEF_RATIO = $data[CHIEF_RATIO];
		$FRIEND_RATIO = $data[FRIEND_RATIO];
		$SUB_RATIO = $data[SUB_RATIO];

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, "$temp_POS_TYPE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$temp_POS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$SEFT_RATIO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, " $CHIEF_RATIO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$FRIEND_RATIO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$SUB_RATIO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			
		} // end while
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
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>