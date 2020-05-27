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
	$worksheet = &$workbook->addworksheet("���͵��˹�㹡�ú����çҹ");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 3, 40);
		$worksheet->set_column(4, 4, 10);
		
		$worksheet->write($xlsRow, 0, "����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "�������", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "���͵��˹�㹡�ú����çҹ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "�ҹТͧ���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "��ҹ/¡��ԡ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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

  	if(trim($search_code)) $arr_search_condition[] = "(a.$arr_fields[0] like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "((a.$arr_fields[1] like '%$search_name%') or (a.$arr_fields[2] like '%$search_name%'))";
  	if(trim($search_status)) $arr_search_condition[] = "(a.$arr_fields[3] = '$search_status')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], a.$arr_fields[3], $arr_fields[4], PS_NAME 
							from		$table a
											left join PER_STATUS b on a.$arr_fields[3] = b.$arr_fields[3]
							$search_condition
							order by IIF(ISNULL(PM_SEQ_NO), 9999, PM_SEQ_NO), $arr_fields[0] 
					   ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], a.$arr_fields[3], $arr_fields[4], PS_NAME
							from		$table a, PER_STATUS b
							where		a.$arr_fields[3] = b.$arr_fields[3](+)
											$search_condition
							order by PM_SEQ_NO, $arr_fields[0] 
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], a.$arr_fields[3], $arr_fields[4], PS_NAME 
							from		$table a
											left join PER_STATUS b on a.$arr_fields[3] = b.$arr_fields[3]
							$search_condition
							order by PM_SEQ_NO, $arr_fields[0] 
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
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$$arr_fields[0] = $data[$arr_fields[0]];
			$$arr_fields[1] = $data[$arr_fields[1]];
			$$arr_fields[2] = $data[$arr_fields[2]];
			$$arr_fields[4] = ($data[$arr_fields[4]]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
			$ps_name = $data[PS_NAME];

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $$arr_fields[0], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $$arr_fields[2], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $$arr_fields[1], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $ps_name, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 4, $$arr_fields[4], 35, 4, 1, 0.8);
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
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