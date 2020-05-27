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
	$worksheet = &$workbook->addworksheet("");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 40);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 15);
                $worksheet->set_column(6, 6, 20);
                $worksheet->set_column(7, 7, 50);
		
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "อัตราค่าจ้างขั้นต่ำ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "อัตราค่าจ้างขั้นสูง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow, 5, "กลุ่มบัญชี", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow, 6, "อัตราค่าจ้างขั้นสูงกว่าขั้นสูง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "คุณสมบัติเฉพาะตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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

  	if(trim($search_pn_code)) $arr_search_condition[] = "(a.$arr_fields[0] = trim('$search_pn_code'))";
  	if(trim($search_level_no)) $arr_search_condition[] = "(a.$arr_fields[1] = '$search_level_no')";
	if(trim($search_min_salary)) $arr_search_condition[] = "($arr_fields[2] = '$search_min_salary')";
	if(trim($search_max_salary)) $arr_search_condition[] = "($arr_fields[3] = '$search_max_salary')";
	$search_condition = "";
	$search_condition_count = "";
	if(count($arr_search_condition)){
		$search_condition = " and " . implode(" and ", $arr_search_condition);
		$search_condition_count = " where " . implode(" and ", $arr_search_condition);
	}

	if($DPISDB=="odbc"){
		$cmd = "	select		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], a.$arr_fields[3], $arr_fields[4], PN_NAME, LEVEL_NAME 
					from		$table a, PER_POS_NAME b, PER_LEVEL c 
					where 		a.$arr_fields[0]=b.$arr_fields[0] and 
								a.$arr_fields[1]=c.$arr_fields[1]
								$search_condition 
					order by 	a.$arr_fields[0], a.$arr_fields[1] ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.$arr_fields[0], a.$arr_fields[1], a.$arr_fields[2], a.$arr_fields[3], a.$arr_fields[4], b.PN_NAME, c.LEVEL_NAME ,a.$arr_fields[6],a.$arr_fields[7]
					from		$table a, PER_POS_NAME b, PER_LEVEL c 
					where 		a.$arr_fields[0]=trim(b.$arr_fields[0]) and 
								a.$arr_fields[1]=c.$arr_fields[1]
								$search_condition 
					order by 	a.$arr_fields[0], a.$arr_fields[1] ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], a.$arr_fields[3], $arr_fields[4], PN_NAME, LEVEL_NAME 
					from		$table a, PER_POS_NAME b, PER_LEVEL c 
					where 		a.$arr_fields[0]=b.$arr_fields[0] and 
								a.$arr_fields[1]=c.$arr_fields[1]
								$search_condition 
					order by 	a.$arr_fields[0], a.$arr_fields[1] ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
        //die("<pre>".$cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;

			$$arr_fields[0] = $data[$arr_fields[0]];
			$$arr_fields[1] = $data[$arr_fields[1]];
			$$arr_fields[2] = number_format($data[$arr_fields[2]]);
			$$arr_fields[3] = number_format($data[$arr_fields[3]]);
			$$arr_fields[4] = $data[$arr_fields[4]];
                        $$arr_fields[6] = $data[$arr_fields[6]];
                        $$arr_fields[7] = number_format($data[$arr_fields[7]]);
			$PN_NAME = $data[PN_NAME];
			$LEVEL_NAME = $data[LEVEL_NAME];		

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_row, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $PN_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $LEVEL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $$arr_fields[2], set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $$arr_fields[3], set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $$arr_fields[6], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        $worksheet->write($xlsRow, 6, $$arr_fields[7], set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                        $worksheet->write($xlsRow, 7, $$arr_fields[4], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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