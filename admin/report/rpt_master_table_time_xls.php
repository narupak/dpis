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

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 50);
		$worksheet->set_column(2, 4, 20);
		$worksheet->set_column(5, 5, 10);
		
		$worksheet->write($xlsRow, 0, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "วันที่เริ่มต้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "วันที่สิ้นสุด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ระยะเวลา(วัน)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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

  	if(trim($search_code)) $arr_search_condition[] = "($arr_fields[0] like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "($arr_fields[1] like '%$search_name%')";
  	if(trim($search_date_min)){ 
		$search_date_min =  save_date($search_date_min);
		if($DPISDB=="oci8") $arr_search_condition[] = "(TO_CHAR(TO_DATE($arr_fields[2], 'DD/MM/YYYY'), 'YYYY-MM-DD') >= '$search_date_min')";
		elseif($DPISDB=="odbc") $arr_search_condition[] = "(TO_CHAR(TO_DATE($arr_fields[2], 'DD/MM/YYYY'), 'YYYY-MM-DD') >= '$search_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(TO_CHAR(TO_DATE($arr_fields[2], 'DD/MM/YYYY'), 'YYYY-MM-DD') >= '$search_date_min')";
		$search_date_min = show_date_format($search_date_min, 1);
	}
  	if(trim($search_date_max)){ 
		$search_date_max =  save_date($search_date_max);
		if($DPISDB=="oci8") $arr_search_condition[] = "(TO_CHAR(TO_DATE($arr_fields[3], 'DD/MM/YYYY'), 'YYYY-MM-DD') <= '$search_date_max')";
		elseif($DPISDB=="odbc") $arr_search_condition[] = "(TO_CHAR(TO_DATE($arr_fields[3], 'DD/MM/YYYY'), 'YYYY-MM-DD') <= '$search_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(TO_CHAR(TO_DATE($arr_fields[3], 'DD/MM/YYYY'), 'YYYY-MM-DD') <= '$search_date_max')";
		$search_date_max = show_date_format($search_date_max, 1);
	}
  	if(trim($search_day_min)) $arr_search_condition[] = "($arr_fields[4] >= $search_day_min)";
  	if(trim($search_day_max)) $arr_search_condition[] = "($arr_fields[4] <= $search_day_max)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5]
							from		$table
							$search_condition
							order by IIF(ISNULL(TIME_SEQ_NO), 9999, TIME_SEQ_NO), $arr_fields[0] 
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5] 
							from		$table
							$search_condition
							order by TIME_SEQ_NO, $arr_fields[0] 
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5]
							from		$table
							$search_condition
							order by TIME_SEQ_NO, $arr_fields[0] 
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

			$$arr_fields[0] = $data[$arr_fields[0]];
			$$arr_fields[1] = $data[$arr_fields[1]];
			$$arr_fields[2] = $data[$arr_fields[2]];
			$$arr_fields[3] = $data[$arr_fields[3]];
			$$arr_fields[4] = $data[$arr_fields[4]];
			$$arr_fields[5] = ($data[$arr_fields[5]]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
	
			if(trim($$arr_fields[2])){
				$$arr_fields[2] = substr($$arr_fields[2], 0, 10);
				if(strpos($$arr_fields[2], "/") !== false){
					$arr_temp = explode("/", $$arr_fields[2]);
					if(($arr_temp[2] + 543) > 1000)
						$$arr_fields[2] = $arr_temp[0] ." ". $month_abbr[$arr_temp[1]+0][TH] ." ". ($arr_temp[2] + 543) ;
					else
						$$arr_fields[2] = $arr_temp[2] ." ". $month_abbr[$arr_temp[1]+0][TH] ." ". ($arr_temp[0] + 543) ;
				}elseif(strpos($$arr_fields[2], "-") !== false){
					$arr_temp = explode("-", $$arr_fields[2]);
					if(($arr_temp[2] + 543) > 1000)
						$$arr_fields[2] = $arr_temp[0] ." ". $month_abbr[$arr_temp[1]+0][TH] ." ". ($arr_temp[2] + 543) ;
					else
						$$arr_fields[2] = $arr_temp[2] ." ". $month_abbr[$arr_temp[1]+0][TH] ." ". ($arr_temp[0] + 543) ;
				}
			} // end if	
			
			if(trim($$arr_fields[3])){
				$$arr_fields[3] = substr($$arr_fields[3], 0, 10);
				if(strpos($$arr_fields[3], "/") !== false){
					$arr_temp = explode("/", $$arr_fields[3]);
					if(($arr_temp[2] + 543) > 1000)
						$$arr_fields[3] = $arr_temp[0] ." ". $month_abbr[$arr_temp[1]+0][TH] ." ". ($arr_temp[2] + 543) ;
					else
						$$arr_fields[3] = $arr_temp[2] ." ". $month_abbr[$arr_temp[1]+0][TH] ." ". ($arr_temp[0] + 543) ;
				}elseif(strpos($$arr_fields[3], "-") !== false){
					$arr_temp = explode("-", $$arr_fields[3]);
					if(($arr_temp[2] + 543) > 1000)
						$$arr_fields[3] = $arr_temp[0] ." ". $month_abbr[$arr_temp[1]+0][TH] ." ". ($arr_temp[2] + 543) ;
					else
						$$arr_fields[3] = $arr_temp[2] ." ". $month_abbr[$arr_temp[1]+0][TH] ." ". ($arr_temp[0] + 543) ;
				}
			} // end if

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $$arr_fields[0], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $$arr_fields[1], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $$arr_fields[2], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $$arr_fields[3], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
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