<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$company_name = "";
	$report_title = "แสดงรายละเอียดกลุ่มผู้ใช้งาน";
	$report_code = "Ruser_group_detail";
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	function print_header(){
		global $worksheet, $xlsRow, $COM_LEVEL_SALP;
		global $COM_PER_TYPE;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 50);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 30);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "หน่วยงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "รหัสผู้ใช้", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "รหัสผ่าน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
	} // function		

		$cmd = " select a.name_th,b.username,b.password from user_group a,user_detail b
						where a.id=b.group_id
						order by a.code ";
		$count_data = $db->send_cmd($cmd);
//		$db->show_error();
	$data_count = $data_row = 0;
	while($data = $db->get_array()){
		$data_row++;		
		$NAME_TH = trim($data[name_th]);
		$USERNAME = trim($data[username]);
		$PASSWORD= trim($data[password]);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][nameth] = $NAME_TH; 
		$arr_content[$data_count][username] = $USERNAME; 
		$arr_content[$data_count][password] = $PASSWORD;
	
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$NAME_TH = $arr_content[$data_count][nameth]; 
			$USERNAME = $arr_content[$data_count][username]; 
			$PASSWORD = $arr_content[$data_count][password];

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
			$worksheet->write_string($xlsRow, 1, "$NAME_TH", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
			$worksheet->write_string($xlsRow, 2, "$USERNAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
			$worksheet->write_string($xlsRow, 3, "$PASSWORD", set_format("xlsFmtTableDetail", "", "L", "LTRB", 1));
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"แสดงรายละเอียดกลุ่มผู้ใช้งาน$type_name.xls\"");
	header("Content-Disposition: inline; filename=\"แสดงรายละเอียดกลุ่มผู้ใช้งาน$type_name.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>