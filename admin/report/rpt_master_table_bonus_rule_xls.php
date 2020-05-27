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
	$worksheet = &$workbook->addworksheet("ระดับผลการประเมินย่อย");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $ORG_TITLE, $ACTIVE_TITLE;

		$worksheet->set_column(0, 0, 15);
		$worksheet->set_column(1, 1, 15);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 20);
		$worksheet->set_column(4, 4, 20);
		$worksheet->set_column(5, 5, 15);
		$worksheet->set_column(6, 6, 15);
		$worksheet->set_column(7, 7, 10);
		
		$worksheet->write($xlsRow, 0, "ปีงบประมาณ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ช่วงคะแนนระดับหน่วยงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ช่วงคะแนนระดับบุคคล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "จำนวน (เท่า)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "$ACTIVE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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

	//if(trim($search_org_id)) $arr_search_condition[] = "(ORG_ID=$search_org_id OR ORG_ID IS NULL)";
	if(trim($search_year)) $arr_search_condition[] = "(BR_YEAR like '$search_year%')";
  	if(trim($search_code)) $arr_search_condition[] = "(BR_CODE like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(BR_NAME like '%$search_name%')";
  	if(trim($search_per_type)) $arr_search_condition[] = "(BR_TYPE = $search_per_type)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){ 
		$cmd = "	select		top $data_per_page 
											BR_YEAR, BR_CODE, BR_NAME, BR_ORG_POINT_MIN, BR_ORG_POINT_MAX, BR_PER_POINT_MIN, BR_PER_POINT_MAX, BR_TIMES, BR_ACTIVE, BR_TYPE
							from		$table
							$search_condition
							order by BR_TYPE, BR_YEAR, BR_CODE ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		*
							from (
								select		BR_YEAR, BR_CODE, BR_NAME, BR_ORG_POINT_MIN, BR_ORG_POINT_MAX, BR_PER_POINT_MIN, BR_PER_POINT_MAX, BR_TIMES, BR_ACTIVE, BR_TYPE
								from		$table
								$search_condition
								order by BR_TYPE, BR_YEAR, BR_CODE 
							) where rownum <= $data_per_page ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		BR_YEAR, BR_CODE, BR_NAME, BR_ORG_POINT_MIN, BR_ORG_POINT_MAX, BR_PER_POINT_MIN, BR_PER_POINT_MAX, BR_TIMES, BR_ACTIVE, BR_TYPE
							from		$table
							$search_condition
							order by BR_TYPE, BR_YEAR, BR_CODE ";
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

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$BR_YEAR = $data[BR_YEAR];
			$BR_CODE = $data[BR_CODE];
			$BR_NAME = $data[BR_NAME];
			$BR_ORG_POINT_MIN = $data[BR_ORG_POINT_MIN];
			$BR_ORG_POINT_MAX = $data[BR_ORG_POINT_MAX];
			$BR_PER_POINT_MIN = $data[BR_PER_POINT_MIN];
			$BR_PER_POINT_MAX = $data[BR_PER_POINT_MAX];
			$BR_TYPE = $data[BR_TYPE];
			$BR_TYPE = $PERSON_TYPE[$BR_TYPE];
			$BR_TIMES = $data[BR_TIMES];
			$BR_ACTIVE = ($data[BR_ACTIVE]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0,  $BR_YEAR, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $BR_CODE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $BR_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$BR_ORG_POINT_MIN - $BR_ORG_POINT_MAX", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$BR_PER_POINT_MIN - $BR_PER_POINT_MAX", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $BR_TYPE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6,$BR_TIMES, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 7, $BR_ACTIVE, 35, 4, 1, 0.8);
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