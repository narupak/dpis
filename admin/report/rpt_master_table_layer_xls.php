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
	$worksheet = &$workbook->addworksheet("บัญชีอัตราเงินเดือน ");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 25);
		$worksheet->set_column(1, 1, 15);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 20);
		$worksheet->set_column(5, 5, 20);
		$worksheet->set_column(6, 6, 20);
		$worksheet->set_column(7, 7, 20);
		$worksheet->set_column(8, 8, 10);
		
		$worksheet->write($xlsRow, 0, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ประเภทข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ค่ากลาง/0.5 ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ค่ากลาง/1 ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "ค่ากลาง/1.5 ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "เงินเดือนพิเศษ/เต็มขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

//  if(!$search_level_no_min) $search_level_no_min = 1;
// 	if(!$search_level_no_max) $search_level_no_max = 11;

	if ($ISCS_FLAG==1) $arr_search_condition[] = "(a.LEVEL_NO in $LIST_LEVEL_NO)"; 
	if(trim($search_type)) $arr_search_condition[] = "(LAYER_TYPE = $search_type)";
	if(trim($search_per_type)) $arr_search_condition[] = "(PER_TYPE = $search_per_type)";
  	if(trim($search_level_no_min)){ 
		if($DPISDB=="oci8") $arr_search_condition[] = "(a.LEVEL_NO >= '$search_level_no_min')";
		elseif($DPISDB=="odbc") $arr_search_condition[] = "(a.LEVEL_NO >= '$search_level_no_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(a.LEVEL_NO >= '$search_level_no_min')";
	}
  	if(trim($search_level_no_max)){ 
		if($DPISDB=="oci8") $arr_search_condition[] = "(a.LEVEL_NO <= '$search_level_no_max')";
		elseif($DPISDB=="odbc") $arr_search_condition[] = "(a.LEVEL_NO <= '$search_level_no_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(a.LEVEL_NO <= '$search_level_no_max')";
	}
  	if(trim($search_layer_no_min)) $arr_search_condition[] = "(LAYER_NO >= $search_layer_no_min)";
  	if(trim($search_layer_no_max)) $arr_search_condition[] = "(LAYER_NO <= $search_layer_no_max)";
  	if(trim($search_salary_min)) $arr_search_condition[] = "(LAYER_SALARY >= $search_salary_min)";
  	if(trim($search_salary_max)) $arr_search_condition[] = "(LAYER_SALARY <= $search_salary_max)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		LAYER_TYPE, a.LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
							LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT,	LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
							LAYER_SALARY_FULL, b.LEVEL_NAME, b.PER_TYPE 
							from		$table a, PER_LEVEL b
							where		a.LEVEL_NO=b.LEVEL_NO
											$search_condition
							order by a.LEVEL_NO, a.LAYER_TYPE, a.LAYER_NO ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		LAYER_TYPE, a.LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
							LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT,	LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
							LAYER_SALARY_FULL, b.LEVEL_NAME, b.PER_TYPE
							from		$table a, PER_LEVEL b
							where		a.LEVEL_NO=b.LEVEL_NO
											$search_condition
							order by 	a.LEVEL_NO, a.LAYER_TYPE, a.LAYER_NO ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		LAYER_TYPE, a.LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
							LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT,	LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
							LAYER_SALARY_FULL, b.LEVEL_NAME, b.PER_TYPE 
							from		$table a, PER_LEVEL b
							where		a.LEVEL_NO=b.LEVEL_NO
											$search_condition
							order by a.LEVEL_NO, a.LAYER_TYPE, a.LAYER_NO ";
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
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			if($data[LAYER_TYPE] == 1) $LAYER_TYPE = "ทั่วไป (ท)";
			elseif($data[LAYER_TYPE] == 2) $LAYER_TYPE = "ผู้บริหารระดับสูง (บ)";
			elseif ($data[LAYER_TYPE] == 0) $LAYER_TYPE = $LAYER_TYPE_TITLE; 
			$LEVEL_NO = level_no_format($data[LEVEL_NO]);
			$LEVEL_NAME = $data[LEVEL_NAME];
			$PER_TYPE = $data[PER_TYPE];
			$LAYER_NO = $data[LAYER_NO];
			$LAYER_SALARY = number_format($data[LAYER_SALARY], 2, ".", ",");
			$LAYER_ACTIVE = ($data[LAYER_ACTIVE]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
			$LAYER_SALARY_MIN = number_format($data[LAYER_SALARY_MIN], 2, ".", ",");
			$LAYER_SALARY_MAX = number_format($data[LAYER_SALARY_MAX], 2, ".", ",");
			$LAYER_SALARY_MIDPOINT = number_format($data[LAYER_SALARY_MIDPOINT], 2, ".", ",");
			$LAYER_SALARY_MIDPOINT1 = number_format($data[LAYER_SALARY_MIDPOINT1], 2, ".", ",");
			$LAYER_SALARY_MIDPOINT2 = number_format($data[LAYER_SALARY_MIDPOINT2], 2, ".", ",");
			$LAYER_SALARY_FULL = number_format($data[LAYER_SALARY_FULL], 2, ".", ",");

			if($LAYER_NO==0) $LAYER_SALARY = $LAYER_SALARY_MIN ." - ". $LAYER_SALARY_MAX;

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, $LEVEL_NAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $LAYER_NO, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $LAYER_SALARY, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $LAYER_TYPE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $LAYER_SALARY_MIDPOINT, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $LAYER_SALARY_MIDPOINT1, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $LAYER_SALARY_MIDPOINT2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $LAYER_SALARY_FULL, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 8, $LAYER_ACTIVE, 35, 4, 1, 0.8);
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
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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