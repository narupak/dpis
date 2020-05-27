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
		global $worksheet;

		$worksheet->set_column(0, 0, 20);
		$worksheet->set_column(1, 1, 80);
		
		$worksheet->write($xlsRow, 0, "วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "วันหยุด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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
	
	$yr_perholiday_min= $yr_perholiday-1;
	if($DPISDB=="odbc"){
		if($yrtype==0) $arr_search_condition[] = "(trim(hol_date) >= '$yr_perholiday_min-10-01' AND trim(hol_date) <= '$yr_perholiday-09-30')";
		else $arr_search_condition[] = "(LEFT(hol_date, 4) = '$yr_perholiday')";
	}elseif($DPISDB=="oci8"){
		if($yrtype==0) $arr_search_condition[] = "(trim(hol_date) >= '$yr_perholiday_min-10-01' AND trim(hol_date) <= '$yr_perholiday-09-30')";
		else $arr_search_condition[] = "(TO_CHAR(TO_DATE(SUBSTR(hol_date, 1, 10), 'YYYY-MM-DD'), 'YYYY') = '$yr_perholiday')";
	}elseif($DPISDB=="mysql"){
		if($yrtype==0) $arr_search_condition[] = "(trim(hol_date) >= '$yr_perholiday_min-10-01' AND trim(hol_date) <= '$yr_perholiday-09-30')";
		else $arr_search_condition[] = "(LEFT(hol_date, 4) = '$yr_perholiday')";
	} // end if
  	if(trim($search_date_min)){ 
		$search_date_min =  save_date($search_date_min);
		$arr_search_condition[] = "(trim($arr_fields[0]) >= '$search_date_min')";
		$search_date_min = show_date_format($search_date_min, 1);
	}
  	if(trim($search_date_max)){ 
		$search_date_max =  save_date($search_date_max);
		$arr_search_condition[] = "(trim($arr_fields[0]) <= '$search_date_max')";
		$search_date_max = show_date_format($search_date_max, 1);
	}
	
	if(trim($search_name)) $arr_search_condition[] = "($arr_fields[1] like '%$search_name%')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
	
	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1]
							from		$table
											$search_condition
							order by IIF(ISNULL(HOL_SEQ_NO), 9999, HOL_SEQ_NO), $arr_fields[0] ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select 		$arr_fields[0], $arr_fields[1]
							from		$table
							$search_condition
							order by HOL_SEQ_NO, $arr_fields[0] ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1]
							from		$table
											$search_condition
							order by HOL_SEQ_NO, $arr_fields[0] ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$$arr_fields[0] = show_date_format($data[$arr_fields[0]],$DATE_DISPLAY);
			$$arr_fields[1] = $data[$arr_fields[1]];

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $$arr_fields[0], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $$arr_fields[1], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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