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
	$worksheet = &$workbook->addworksheet("Sheet1");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 15);
		$worksheet->set_column(1, 1, 15);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 10);
		
		$worksheet->write($xlsRow, 0, "���������˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "�дѺ���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "�Թ��Шӵ��˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "��ҹ/¡��ԡ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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

  	if(!$search_level_no_min) $search_level_no_min = 1;
  	if(!$search_level_no_max) $search_level_no_max = 11;
	
	if(trim($search_type_code)) $arr_search_condition[] = "(a.$arr_fields[0] = '$search_type_code')";
  	if(trim($search_salary_min)) $arr_search_condition[] = "(a.$arr_fields[2] >= $search_salary_min)";
  	if(trim($search_salary_max)) $arr_search_condition[] = "(a.$arr_fields[2] <= $search_salary_max)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		a.$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], PT_NAME , MS_SEQ_NO
							from  		$table a, PER_TYPE b  
							where		a.$arr_fields[0] = b.$arr_fields[0]
											$search_condition
						order by IIF(ISNULL(MS_SEQ_NO), 9999, MS_SEQ_NO),a.PT_CODE, $arr_fields[1]
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], PT_NAME, MS_SEQ_NO
									from  		$table a, PER_TYPE b
									where		a.$arr_fields[0] = b.$arr_fields[0]
													$search_condition
						order by MS_SEQ_NO, PT_CODE, $arr_fields[1]
					   ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		a.$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], PT_NAME, MS_SEQ_NO
									from  		$table a, PER_TYPE b
									where		a.$arr_fields[0] = b.$arr_fields[0]
													$search_condition
									order by MS_SEQ_NO,PT_CODE, $arr_fields[1]
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
			$$arr_fields[1] = $data[$arr_fields[1]];
			$PT_NAME = $data[PT_NAME];
			$$arr_fields[2] = number_format($data[$arr_fields[2]], 2, ".", ",");
			$$arr_fields[3] = ($data[$arr_fields[3]]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $PT_NAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, level_no_format($$arr_fields[1]), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $$arr_fields[2], set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 3, $$arr_fields[3], 35, 4, 1, 0.8);
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
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