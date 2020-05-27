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
		global $worksheet, $PER_TYPE_TITLE, $COMPETENCE_TITLE, $ACTIVE_TITLE;

		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 45);
		$worksheet->set_column(3, 3, 10);
		
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, $PER_TYPE_TITLE, set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, $COMPETENCE_TITLE, set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		//$worksheet->write($xlsRow, 3, "$ACTIVE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

	if(trim($search_PER_TYPE)) $arr_search_condition[] = "(a.PER_TYPE = '$search_PER_TYPE')";
  	if(trim($search_cp_code)) $arr_search_condition[] = "(a.CP_CODE like '%$search_cp_code%')";
	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		 a.PER_TYPE, a.CP_CODE,   CP_NAME 
					from		PER_TYPE_COMPETENCE a,  PER_COMPETENCE c 
					where 		 a.CP_CODE=trim(c.CP_CODE)
								$search_condition 
					order a.PER_TYPE, a.CP_CODE,   CP_NAME  ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.PER_TYPE,  a.CP_CODE,   CP_NAME 
					from				PER_TYPE_COMPETENCE a,  PER_COMPETENCE c 
					where 		 a.CP_CODE=trim(c.CP_CODE)
								$search_condition 
					order by 	 a.PER_TYPE, a.CP_CODE,   CP_NAME 	";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.PER_TYPE,  a.CP_CODE,   CP_NAME 
					from		PER_TYPE_COMPETENCE a,  PER_COMPETENCE c 
					where 		 a.CP_CODE=trim(c.CP_CODE)
								$search_condition 
					order by 	 a.PER_TYPE, a.CP_CODE,   CP_NAME  ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		//$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;
			$temp_PER_TYPE = $data[PER_TYPE];
			if($temp_PER_TYPE==1)	$temp_PER_TYPE_NAME= "ข้าราชการ";
			else if($temp_PER_TYPE==3)	$temp_PER_TYPE_NAME= "พนักงานราชการ";
			$CP_CODE = $data[CP_CODE];
			$CP_NAME = $data[CP_NAME];		

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0,(($NUMBER_DISPLAY==2)?convert2thaidigit($data_row):$data_row), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $temp_PER_TYPE_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $CP_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			//$worksheet->insert_bitmap($xlsRow, 3, $LC_ACTIVE, 35, 4, 1, 0.8);
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		//$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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