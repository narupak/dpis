<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("เงื่อนไขการรับเครื่องราชฯ");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 10);
		$worksheet->set_column(2, 2, 50);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 50);
		
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "เครื่องราชฯ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "อายุราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "ระยะเวลาในระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "เครื่องราชฯ เดิม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
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

 	if(trim($search_type)) $arr_search_condition[] = "($arr_fields[0] = $search_type)";
  	if(trim($search_level)) $arr_search_condition[] = "($arr_fields[1] = '$search_level')";
  	if(trim($search_code)) $arr_search_condition[] = "($arr_fields[2] = '$search_code')";
  	if(trim($search_time1)) $arr_search_condition[] = "($arr_fields[3] = $search_time1)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5]
							from 		$table
											$search_condition							
							order by 	$arr_fields[0], CLng($arr_fields[1]), $arr_fields[2] 
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5]
							from 		$table
											$search_condition							
							order by 	$arr_fields[0], TO_NUMBER($arr_fields[1]), $arr_fields[2] 
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5]
							from 		$table
											$search_condition							
							order by 	$arr_fields[0], $arr_fields[1], $arr_fields[2] 
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

			${"temp_".$arr_fields[0]} = trim($data[$arr_fields[0]]);
			${"temp_".$arr_fields[1]} = trim($data[$arr_fields[1]]) + 0;
			${"temp_".$arr_fields[2]} = trim($data[$arr_fields[2]]);
	
			$DCON_TYPE_NAME = ($data[$arr_fields[0]]  == 1)? "ข้าราชการ" : "ลูกจ้างประจำ";	// ข้าราชการ=1, ลูกจ้างประจำ=2
			$DC_CODE_NEW = $data[$arr_fields[2]];		
			$$arr_fields[3] = (trim($data[$arr_fields[3]]))? $data[$arr_fields[3]] : "-" ;
			$$arr_fields[4] = (trim($data[$arr_fields[4]]))? $data[$arr_fields[4]] : "-" ;
			$DC_CODE_OLD = $data[$arr_fields[5]];		
	
			$DC_NAME = $DC_NAME_OLD = "-";
			$cmd = "select DC_NAME, DC_CODE from PER_DECORATION where DC_CODE='$DC_CODE_NEW' or DC_CODE='$DC_CODE_OLD'";
			$db_dpis2->send_cmd($cmd);
			while ($data_dpis2 = $db_dpis2->get_array()) {
				if ($data_dpis2[DC_CODE] == $DC_CODE_NEW)	$DC_NAME = $data_dpis2[DC_NAME];
				else																						$DC_NAME_OLD = (trim($data_dpis2[DC_NAME]))? $data_dpis2[DC_NAME] : "-";
			}

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $DCON_TYPE_NAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, level_no_format(${"temp_".$arr_fields[1]}), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $$arr_fields[3], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $$arr_fields[4], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $DC_NAME_OLD, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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