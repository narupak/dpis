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
	$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $LEVEL_TITLE, $ACTIVE_TITLE;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 50);
		$worksheet->set_column(2, 2, 65);
		$worksheet->set_column(3, 3, 10);
		
		$worksheet->write($xlsRow, 0, "$LEVEL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "$ACTIVE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if

  	if(trim($search_level_no_min)) $arr_search_condition[] = "(LEVEL_NO >= '$search_level_no_min')";
  	if(trim($search_level_no_max)) $arr_search_condition[] = "(LEVEL_NO <= '$search_level_no_max')";
  	if(trim($search_level_name)) $arr_search_condition[] = "(LEVEL_NAME like '%$search_level_name%')";
  	if(trim($search_per_type)) $arr_search_condition[] = "(PER_TYPE = $search_per_type)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[4], $arr_fields[5] 
							from		$table
							$search_condition
							order by $arr_fields[5], $arr_fields[0] 
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[4], $arr_fields[5] 
							from		$table
							$search_condition
							order by $arr_fields[5], $arr_fields[0] 
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[4], $arr_fields[5] 
							from		$table
							$search_condition
							order by $arr_fields[5], $arr_fields[0] 
					   ";
	} // end if

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

			$$arr_fields[0] = $data[$arr_fields[0]];
			$$arr_fields[1] = ($data[$arr_fields[1]]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
			$$arr_fields[4] = $data[$arr_fields[4]];
			$$arr_fields[5] = $data[$arr_fields[5]];
			$$arr_fields[5] = $PERSON_TYPE[$$arr_fields[5]];

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $$arr_fields[0], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $$arr_fields[4], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $$arr_fields[5], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 3, $$arr_fields[1], 35, 4, 1, 0.8);
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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