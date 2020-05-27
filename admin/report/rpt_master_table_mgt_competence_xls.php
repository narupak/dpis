<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $DEPARTMENT_TITLE, $ACTIVE_TITLE;

		$worksheet->set_column(0, 0, 50);
		$worksheet->set_column(1, 1, 12);
		$worksheet->set_column(2, 2, 50);
		$worksheet->set_column(3, 3, 20);
		$worksheet->set_column(4, 4, 10);
		
		$worksheet->write($xlsRow, 0, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อสมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "โมเดล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "$ACTIVE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

  	if(trim($search_department_id)) $arr_search_condition[] = "(DEPARTMENT_ID = '$search_department_id')";
  	if(trim($search_code)) $arr_search_condition[] = "(CP_CODE like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(CP_NAME like '%$search_name%')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	$cmd = "	select		CP_CODE, CP_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, DEPARTMENT_ID  
					from		PER_COMPETENCE
					$search_condition
					order by 	DEPARTMENT_ID, CP_CODE ";

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$CP_CODE = $data[CP_CODE];
			$CP_NAME = $data[CP_NAME];
			$CP_MEANING = $data[CP_MEANING];
			$CP_MODEL = $data[CP_MODEL];
			$CP_ACTIVE = ($data[CP_ACTIVE]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";

			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$TMP_DEPARTMENT_NAME = $data1[ORG_NAME];

			if ($CP_MODEL == 1)			$CP_MODEL = "สมรรถนะหลัก";
			elseif ($CP_MODEL == 2)		$CP_MODEL = "สมรรถนะผู้บริหาร";
			elseif ($CP_MODEL == 3)		$CP_MODEL = "สมรรถนะประจำสายงาน";

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $TMP_DEPARTMENT_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, $CP_CODE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $CP_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $CP_MODEL, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 4, $CP_ACTIVE, 35, 4, 1, 0.8);
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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