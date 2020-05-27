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
	if (strlen($report_title) >= 32) 
		$worksheet = &$workbook->addworksheet("Sheet1");
	else
		$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//	
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $ACTIVE_TITLE;		

		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 90);
		$worksheet->set_column(2, 2, 10);

		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "$ACTIVE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
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

  	 if(trim($search_PL_CODE)) $arr_search_condition[] = "(a.PL_CODE like '$search_PL_CODE%')";
  	if(trim($search_ORG_ID)) $arr_search_condition[] = "(a.ORG_ID like '$search_ORG_ID%')";
  	if(trim($search_cp_code)) $arr_search_condition[] = "(a.CP_CODE like '%$search_cp_code%')";
	$search_condition = "";
	$search_condition_count = "";
	if(count($arr_search_condition)){
		$search_condition = " and " . implode(" and ", $arr_search_condition);
		$search_condition_count = " where " . implode(" and ", $arr_search_condition);
	}

	if($DPISDB=="odbc"){
		$cmd = "	select	a.PL_CODE, a.ORG_ID, a.CP_CODE, LC_ACTIVE, PL_NAME, CP_NAME
									from	PER_LINE_COMPETENCE a, PER_LINE b, PER_COMPETENCE c 
									where 	a.PL_CODE=b.PL_CODE and 
												a.CP_CODE=c.CP_CODE and
												a.PL_CODE=$PL_CODE and a.ORG_ID = $ORG_ID
											$search_condition 
									order by a.PL_CODE, a.ORG_ID, a.CP_CODE ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select 		a.PL_CODE, a.ORG_ID, a.CP_CODE, LC_ACTIVE, PL_NAME, CP_NAME 
								  from 		PER_LINE_COMPETENCE a, PER_LINE b, PER_COMPETENCE c 
								  where 	trim(a.PL_CODE)=trim(b.PL_CODE) and 
												a.CP_CODE=c.CP_CODE and
												trim(a.PL_CODE)=$PL_CODE and a.ORG_ID = $ORG_ID
											$search_condition 
								  order by 	a.PL_CODE, a.ORG_ID, a.CP_CODE ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select 		a.PL_CODE, a.ORG_ID, a.CP_CODE, LC_ACTIVE, PL_NAME, CP_NAME 
					from 		PER_LINE_COMPETENCE a, PER_LINE b, PER_COMPETENCE c 
					where 		a.PL_CODE=b.PL_CODE and 
									a.CP_CODE=c.CP_CODE and
									a.PL_CODE=$PL_CODE and a.ORG_ID = $ORG_ID
								$search_condition 
					order by 	a.PL_CODE, a.ORG_ID, a.CP_CODE ";
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
			$data_num++;	

		$temp_cp_code = $data[CP_CODE];		
		$temp_org_id = $data[ORG_ID];
		$CP_NAME = $data[CP_NAME];		
		$LC_ACTIVE = ($data[LC_ACTIVE]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_num, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $CP_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 2, $LC_ACTIVE, 35, 4, 1, 0.8);			
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