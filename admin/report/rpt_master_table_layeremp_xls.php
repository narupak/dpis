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
	$worksheet = &$workbook->addworksheet("บัญชีอัตราเงินเดือนลูกจ้าง");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 20);
		$worksheet->set_column(1, 1, 10);
		$worksheet->set_column(2, 4, 20);
		$worksheet->set_column(5, 5, 10);
		
		$worksheet->write($xlsRow, 0, "หมวดตำแหน่งลูกจ้าง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "อัตราค่าจ้างรายเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "อัตราค่าจ้างรายวัน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "อัตราค่าจ้างรายชั่วโมง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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

  	if(trim($search_group)) $arr_search_condition[] = "(a.$arr_fields[0] = '$search_group')";
  	if(trim($search_layer_no_min)) $arr_search_condition[] = "(a.$arr_fields[1] >= $search_layer_no_min)";
  	if(trim($search_layer_no_max)) $arr_search_condition[] = "(a.$arr_fields[1] <= $search_layer_no_max)";
  	if(trim($search_salary_m_min)) $arr_search_condition[] = "(a.$arr_fields[2] >= $search_salary_m_min)";
  	if(trim($search_salary_m_max)) $arr_search_condition[] = "(a.$arr_fields[2] <= $search_salary_m_max)";
  	if(trim($search_salary_d_min)) $arr_search_condition[] = "(a.$arr_fields[3] >= $search_salary_d_min)";
  	if(trim($search_salary_d_max)) $arr_search_condition[] = "(a.$arr_fields[3] <= $search_salary_d_max)";
  	if(trim($search_salary_h_min)) $arr_search_condition[] = "(a.$arr_fields[4] >= $search_salary_h_min)";
  	if(trim($search_salary_h_max)) $arr_search_condition[] = "(a.$arr_fields[4] <= $search_salary_h_max)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		a.$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], PG_NAME
							from  		$table a, PER_POS_GROUP b  
							where		a.$arr_fields[0] = b.$arr_fields[0]
											$search_condition
							order by PG_NAME, $arr_fields[1]  
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], PG_NAME
							from  		$table a, PER_POS_GROUP b
							where		a.$arr_fields[0] = b.$arr_fields[0]
											$search_condition
							order by PG_NAME, $arr_fields[1]
					  "; 					   
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], PG_NAME
							from  		$table a, PER_POS_GROUP b  
							where		a.$arr_fields[0] = b.$arr_fields[0]
											$search_condition
							order by PG_NAME, $arr_fields[1]  
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
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$$arr_fields[1] = $data[$arr_fields[1]];
			$PG_NAME = $data[PG_NAME];
			$$arr_fields[2] = number_format($data[$arr_fields[2]], 2, ".", ",");
			$$arr_fields[3] = number_format($data[$arr_fields[3]], 2, ".", ",");
			$$arr_fields[4] = number_format($data[$arr_fields[4]], 2, ".", ",");
			$$arr_fields[5] = ($data[$arr_fields[5]]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, $PG_NAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $$arr_fields[1], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $$arr_fields[2], set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $$arr_fields[3], set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $$arr_fields[4], set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 5, $$arr_fields[5], 35, 4, 1, 0.8);
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