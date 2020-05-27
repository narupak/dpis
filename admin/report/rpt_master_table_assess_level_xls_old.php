<?
	include("../../php_scripts/connect_database.php");
	// include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", 1800);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = &new writeexcel_workbook($fname);
	//echo $report_title;
	$worksheet = &$workbook->addworksheet('$report_title');
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 45);
		$worksheet->set_column(3, 3, 45);
		$worksheet->set_column(4, 4, 45);
		$worksheet->set_column(5, 5, 45);
		$worksheet->set_column(6, 6, 45);
		$worksheet->set_column(7, 7, 10);
		
		$worksheet->write($xlsRow, 0, "สำนัก/กอง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ช่วงคะแนน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ประเภทบุคลากร", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ระดับผลการประเมินหลัก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "เลือนเงินเดือน (%)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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
  	if(trim($search_name)) $arr_search_condition[] = "(a.$arr_fields[1] like '%$search_name%')";
  	if(trim($search_line_code)) $arr_search_condition[] = "(a.$arr_fields[2] = '$search_line_code')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){ 
		$cmd = "	select		top $data_per_page 
											AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, AM_CODE, AL_PERCENT, AL_ACTIVE, ORG_ID, PER_TYPE
							from		$table
							$search_condition
							order by PER_TYPE, ORG_ID, AL_CODE ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		*
							from (
								select		AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, AM_CODE, AL_PERCENT, AL_ACTIVE, ORG_ID, PER_TYPE
								from		$table
								$search_condition
								order by PER_TYPE, ORG_ID, AL_CODE 
							) where rownum <= $data_per_page ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, AM_CODE, AL_PERCENT, AL_ACTIVE, ORG_ID, PER_TYPE
							from		$table
							$search_condition
							order by PER_TYPE, ORG_ID, AL_CODE ";
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
		$data_count = $data_row = 0;
		
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

		$AL_CODE = $data[AL_CODE];
		$AL_NAME = $data[AL_NAME];
		$AL_POINT_MIN = $data[AL_POINT_MIN];
		$AL_POINT_MAX = $data[AL_POINT_MAX];
		//$AL_ACTIVE = $data[AL_ACTIVE];
		$AM_CODE = $data[AM_CODE];
		$AL_PERCENT = $data[AL_PERCENT];
		
		$cmd = " select AM_NAME from PER_ASSESS_MAIN where AM_CODE='$AM_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$AM_NAME = trim($data1[AM_NAME]);
        
		$PER_TYPE = $data[PER_TYPE];
		if ($PER_TYPE == 1)		$PER_TYPE = "ข้าราชการ";
		if ($PER_TYPE == 2)		$PER_TYPE = "ลูกจ้างประจำ";
		if ($PER_TYPE == 3)		$PER_TYPE = "พนักงานราชการ";		

		$TMP_ORG_ID = $data[ORG_ID];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$ORG_NAME = trim($data1[ORG_NAME]);
		$AL_ACTIVE = ($data[AL_ACTIVE]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0,  $ORG_NAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $AL_CODE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $AL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$AL_POINT_MIN - $AL_POINT_MAX", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $PER_TYPE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5,$AM_NAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6,$AL_PERCENT, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 7, $$arr_fields[4], 35, 4, 1, 0.8);
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