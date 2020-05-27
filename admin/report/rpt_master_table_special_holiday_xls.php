<?
	include("../../php_scripts/connect_database.php");
	// include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	//echo $report_title;
	$worksheet = &$workbook->addworksheet("�дѺ�š�û����Թ����");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 50);
		$worksheet->set_column(2, 2, 50);
		
		
		$worksheet->write($xlsRow, 0, "����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "�����˵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		
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

	if($DPISDB=="odbc"){
		if($yrtype==0) $arr_search_condition[] = "(trim(HOLS_DATE) >= '$yr_perholiday_min-10-01' AND trim(HOLS_DATE) <= '$yr_perholiday-09-30')";
		else $arr_search_condition[] = "(LEFT(HOLS_DATE, 4) = '$yr_perholiday')";
	}elseif($DPISDB=="oci8"){
		if($yrtype==0) $arr_search_condition[] = "(trim(HOLS_DATE) >= '$yr_perholiday_min-10-01' AND trim(HOLS_DATE) <= '$yr_perholiday-09-30')";
		else $arr_search_condition[] = "(TO_CHAR(TO_DATE(SUBSTR(HOLS_DATE, 1, 10), 'YYYY-MM-DD'), 'YYYY') = '$yr_perholiday')";
	}elseif($DPISDB=="mysql"){
		if($yrtype==0) $arr_search_condition[] = "(trim(HOLS_DATE) >= '$yr_perholiday_min-10-01' AND trim(HOLS_DATE) <= '$yr_perholiday-09-30')";
		else $arr_search_condition[] = "(LEFT(HOLS_DATE, 4) = '$yr_perholiday')";
	} // end if
  	if(trim($search_date_min)){ 
		$search_date_min =  save_date($search_date_min);
		$arr_search_condition[] = "(trim($arr_fields[1]) >= '$search_date_min')";
		$search_date_min = show_date_format($data[search_date_min], 1);
	}
  	if(trim($search_date_max)){ 
		$search_date_max =  save_date($search_date_max);
		$arr_search_condition[] = "(trim($arr_fields[1]) <= '$search_date_max')";
		$search_date_max = show_date_format($data[search_date_max], 1);
	}
  	if(trim($search_name)) $arr_search_condition[] = "($arr_fields[2] like '%$search_name%')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0],$arr_fields[1], $arr_fields[2], HOLS_SEQ_NO
							from  		$table
								$search_condition
							order by IIF(ISNULL(HOLS_SEQ_NO), 9999, HOLS_SEQ_NO),$arr_fields[0] 
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		$arr_fields[0],$arr_fields[1], $arr_fields[2], HOLS_SEQ_NO
							from  		$table
							$search_condition
							order by HOLS_SEQ_NO, $arr_fields[0] 
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		$arr_fields[0],$arr_fields[1], $arr_fields[2], HOLS_SEQ_NO
							from  		$table
							$search_condition
							order by HOLS_SEQ_NO, $arr_fields[0] 
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
				$cmd1 = "select HG_NAME from PER_HOLIDAY_GROUP where HG_ID='".$$arr_fields[0]."'";
				$db_dpis1->send_cmd($cmd1);
				$data1 = $db_dpis1->get_array();
				$HG_NAME=$data1[HG_NAME];
			
				$$arr_fields[1] = $data[$arr_fields[1]];
				$$arr_fields[2] = $data[$arr_fields[2]];
				$data[$arr_fields[1]] = substr($data[$arr_fields[1]], 0, 10);
				$show_date = show_date_format($data[HOLS_DATE], $DATE_DISPLAY);
				$temp_HOLS_DATE = show_date_format($data[HOLS_DATE], 1);
			

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0,  $show_date, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $HG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $$arr_fields[2], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			
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