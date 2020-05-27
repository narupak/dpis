<?
	include("../../php_scripts/connect_database.php");
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
		global $worksheet, $DEPARTMENT_TITLE;

		$worksheet->set_column(0, 0, 30);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 10);
		$worksheet->set_column(3, 3, 100);
		$worksheet->set_column(4, 4, 200);
		
		$worksheet->write($xlsRow, 0, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ระดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ชื่อระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "รายละเอียด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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

  	if(trim($search_department_id)) $arr_search_condition[] = "(a.DEPARTMENT_ID = '$search_department_id')";
  	if(trim($search_cp_code)) $arr_search_condition[] = "(a.$arr_fields[0] LIKE '$search_cp_code%')";
  	if(trim($search_cl_no)) $arr_search_condition[] = "($arr_fields[1] LIKE '%$search_cl_no%')";
  	if(trim($search_cl_name)) $arr_search_condition[] = "($arr_fields[2] LIKE '%$search_cl_name%')";
	$search_condition = "";
	$search_condition_count = "";
	if(count($arr_search_condition)) {
		$search_condition = " and " . implode(" and ", $arr_search_condition);
		$search_condition_count = " where " . implode(" and ", $arr_search_condition);
	}

	if($DPISDB=="odbc"){
		$cmd = "	select 		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], $arr_fields[3], CP_NAME, a.DEPARTMENT_ID 
				 	from 		$table a, PER_COMPETENCE b  
					where		a.$arr_fields[0]=b.$arr_fields[0] and a.DEPARTMENT_ID=b.DEPARTMENT_ID 
								$search_condition 
					order by 	$order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select 		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], $arr_fields[3], CP_NAME, a.DEPARTMENT_ID 
				 	from 		$table a, PER_COMPETENCE b  
					where		a.$arr_fields[0]=b.$arr_fields[0] and a.DEPARTMENT_ID=b.DEPARTMENT_ID 
								$search_condition 
					order by 	$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select 		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], $arr_fields[3], CP_NAME, a.DEPARTMENT_ID 
				 	from 		$table a, PER_COMPETENCE b  
					where		a.$arr_fields[0]=b.$arr_fields[0] and a.DEPARTMENT_ID=b.DEPARTMENT_ID 
								$search_condition 
					order by 	$order_str ";
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
			$data_row++;

			$$arr_fields[1] = $data[$arr_fields[1]];
			$$arr_fields[2] = $data[$arr_fields[2]];
			$$arr_fields[3] = $data[$arr_fields[3]];
			$CP_NAME = $data[CP_NAME];

			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$TMP_DEPARTMENT_NAME = $data1[ORG_NAME];

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $TMP_DEPARTMENT_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $CP_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $$arr_fields[1], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $$arr_fields[2], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $$arr_fields[3], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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