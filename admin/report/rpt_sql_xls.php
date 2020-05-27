<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");
	
	$table = "PER_SQL";
	
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

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
/*
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
*/
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 120);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "คำสั่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function

	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;
  	if(trim($search_code)) $arr_search_condition[] = "($arr_fields[0] like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "($arr_fields[1] like '%$search_name%')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
  	if($order_by==1){	//รหัส
		$order_str = "$arr_fields[0] ".$SortType[$order_by];
  	}elseif($order_by==2) {	//ชื่อ
		$order_str = "$arr_fields[1] ".$SortType[$order_by];
  	} elseif($order_by==3) {	//คำสั่ง
		$order_str = "$arr_fields[2] ".$SortType[$order_by];
	}
	//------------------------------------------------------------------------------------------------------//

	$cmd =" select count($arr_fields[0]) as count_data from $table $search_condition ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$count_data = $data[count_data];	
//	echo "$count_data";

	if($DPISDB=="odbc"){
		$cmd = "	select $arr_fields[0], $arr_fields[1], $arr_fields[2] 
							from	$table
							$search_condition
							$limit_data
							order by $order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2] 
						  	from		$table
						  	$search_condition
							$limit_data
							order by $order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select $arr_fields[0], $arr_fields[1], $arr_fields[2]
							from		$table
							$search_condition
							order by $order_str ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";

	if($count_data){
		$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":""));
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
				
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

		$arr_title = explode("||", "$ORG_NAME_REF||$report_title");
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
		
		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if
		
		print_header();
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			${"temp_".$arr_fields[0]} = $data[$arr_fields[0]];
			$$arr_fields[1] = $data[$arr_fields[1]];
			$$arr_fields[2] = $data[$arr_fields[2]];

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, ${"temp_".$arr_fields[0]}, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, $$arr_fields[1], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, $$arr_fields[2], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

		} // end while
	}else{
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

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
	header("Content-Type: application/x-msexcel; name=\"epis.xls\"");
	header("Content-Disposition: inline; filename=\"epis.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>