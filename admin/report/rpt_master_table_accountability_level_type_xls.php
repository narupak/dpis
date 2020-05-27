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
	$worksheet = &$workbook->addworksheet("กลุ่มประเภท/ระดับตำแหน่ง(ใหม่)");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $PL_TITLE, $LEVEL_TITLE;

		$worksheet->set_column(0, 0, 50);
		$worksheet->set_column(1, 1, 50);
		$worksheet->set_column(2, 2, 50);
		
		$worksheet->write($xlsRow, 0, "$PL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "$LEVEL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ประเภทหน้าที่ความรับผิดชอบ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		
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
  		if(trim($search_acc_type_id)) $arr_search_condition[] = "(a.$arr_fields[1] = $search_acc_type_id)";
		$search_condition = "";
		if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0], a.$arr_fields[1], b.ACC_TYPE_NAME
							from  		$table a, ACCOUNTABILITY_TYPE b
							where		a.$arr_fields[2] = b.$arr_fields[2]
											$search_condition
											order by $arr_fields[0], $arr_fields[1] ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	
								select		$arr_fields[0], a.$arr_fields[1], b.ACC_TYPE_NAME
								from  		$table a, ACCOUNTABILITY_TYPE b
								where		a.$arr_fields[2] = b.$arr_fields[2]
												$search_condition
												order by $arr_fields[0], $arr_fields[1] ";
	}elseif($DPISDB=="mssql"){
		$cmd = "	select		$arr_fields[0], a.$arr_fields[1], b.ACC_TYPE_NAME
							from  		$table a, ACCOUNTABILITY_TYPE b
							where		a.$arr_fields[2] = b.$arr_fields[2]
											$search_condition
											order by $arr_fields[0], $arr_fields[1] ";
	} // end if
	
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$PL_CODE = $data[PL_CODE];
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$PL_NAME = trim($data1[PL_NAME]);

			$LEVEL_NO = $data[LEVEL_NO];
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$LEVEL_NAME = trim($data1[LEVEL_NAME]);
			$ACC_TYPE_NAME = $data[ACC_TYPE_NAME];

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, $PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $LEVEL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $ACC_TYPE_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		
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