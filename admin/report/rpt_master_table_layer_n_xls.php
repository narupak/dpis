<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	// ==== use for testing phase =====
	$DPISDB = "mysql";
	$db_dpis = $db;
	// ==========================

	ini_set("max_execution_time", 1800);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("ข้อมูลบัญชีอัตราเงินเดือน(ใหม่)");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 1, 35);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 20);
		$worksheet->set_column(5, 5, 10);
		
		$worksheet->write($xlsRow, 0, "ระดับตำแหน่ง (ใหม่)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ประเภทตำแหน่ง (ใหม่)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ประเภทเงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ลำดับขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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

  	if(trim($search_type_code)) $arr_search_condition[] = "(pln.$arr_fields[1] = '$search_type_code')";
  	if(trim($search_group)) $arr_search_condition[] = "(pln.$arr_fields[0] = '$search_group')";
  	if(trim($search_layer)) $arr_search_condition[] = "(pln.$arr_fields[2] = '$search_layer')";
  	if(trim($search_layer_no_min)) $arr_search_condition[] = "(pln.$arr_fields[3] >= $search_layer_no_min)";
  	if(trim($search_layer_no_max)) $arr_search_condition[] = "(pln.$arr_fields[3] <= $search_layer_no_max)";
  	if(trim($search_salary_min)) $arr_search_condition[] = "((pln.$arr_fields[2] = 1 and pln.$arr_fields[4] >= $search_salary_min) or (pln.$arr_fields[2] = 2 and pln.$arr_fields[5] >= $search_salary_min))";
  	if(trim($search_salary_max)) $arr_search_condition[] = "((pln.$arr_fields[2] = 1 and pln.$arr_fields[4] <= $search_salary_max) or (pln.$arr_fields[2] = 2 and pln.$arr_fields[6] <= $search_salary_max))";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "  select		pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], $arr_fields[6], $arr_fields[7], pgn.PT_GROUP_NAME, ptn.PT_NAME_N 
						   from 		$table pln, PER_GROUP_N pgn, PER_TYPE_N ptn		
						   where 		pln.PT_GROUP_N = pgn.PT_GROUP_N and pln.PT_CODE_N = ptn.PT_CODE_N 
						   					$search_condition 						   
						   order by 	pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2] desc, $arr_fields[3]
					   ";					   
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], $arr_fields[6], $arr_fields[7], pgn.PT_GROUP_NAME, ptn.PT_NAME_N 
						  from 			$table pln, PER_GROUP_N pgn, PER_TYPE_N ptn 
						  where 		pln.PT_GROUP_N = pgn.PT_GROUP_N and pln.PT_CODE_N = ptn.PT_CODE_N 
											$search_condition
						  order by 	pln.$arr_fields[0], TO_CHAR(pln.$arr_fields[1], '99'), $arr_fields[2] desc, $arr_fields[3] 
					  "; 
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], $arr_fields[6], $arr_fields[7], pgn.PT_GROUP_NAME, ptn.PT_NAME_N 
						   	from 		$table pln, PER_GROUP_N pgn, PER_TYPE_N ptn		
						   	where 	pln.PT_GROUP_N = pgn.PT_GROUP_N and pln.PT_CODE_N = ptn.PT_CODE_N  
											$search_condition
							order by 	pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2] desc, $arr_fields[3]
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
			$$arr_fields[2] = ($data[$arr_fields[2]]==1)?"ขั้นเงินเดือน":(($data[$arr_fields[2]]==2)?"ช่วงเงินเดือน":"");
			if ($data[$arr_fields[2]] == 1) {
				$temp_show_layer = (($data[$arr_fields[3]] - floor($data[$arr_fields[3]]))==0)?number_format($data[$arr_fields[3]], 0):number_format($data[$arr_fields[3]], 1, ".", ",");;
				$temp_show_salary = number_format($data[$arr_fields[4]], 2, ".", ",");
			} elseif ($data[$arr_fields[2]] == 2) {
				$temp_show_layer = "";
				$temp_show_salary = number_format($data[$arr_fields[5]], 2, ".", ",") ." - ". $show_arr_fields[6] = number_format($data[$arr_fields[6]], 2, ".", ",");		
			}
			$$arr_fields[3] = $data[$arr_fields[3]];
			$PT_GROUP_NAME = $data[PT_GROUP_NAME];
			$PT_NAME_N = $data[PT_NAME_N];
			$$arr_fields[7] = ($data[$arr_fields[7]]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, $PT_NAME_N, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $PT_GROUP_NAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $$arr_fields[2], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $temp_show_layer, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $temp_show_salary, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 5, $$arr_fields[7], 35, 4, 1, 0.8);
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