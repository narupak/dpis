<?	
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/session_start.php");
	include("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 100; $header_text[0] = "ตัวชี้วัด"; 				

	require_once("excel_headpart_subrtn.php");

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_kpi_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_KPI";

//	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/$report_code";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnum_text="";

	$arr_file = (array) null;
	$f_new = false;

	$workbook = new writeexcel_workbook($fname1);

//	echo "$data_count>>fname=$fname1<br>";

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$head = "ตัวชี้วัด";

//	$UPDATE_DATE = date("Y-m-d H:i:s");

	$arr_data = (array) null;
	//$data_count = 0;
	$count_data = get_kpi($KPI_YEAR, 0);		//$count_data = get_kpi($KPI_ID, 0);
	if ($count_data) {
		$cmd = " select KPI_ID,KPI_NAME from PER_PROJECT where KPI_YEAR='$KPI_YEAR' ";		//$cmd = " select KPI_NAME from PER_PROJECT where KPI_ID=$KPI_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();

		$data_count = 0;
		call_header($data_count, $head);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, $data[KPI_ID]."-".$data[KPI_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		for($data_count=0; $data_count < count($arr_data); $data_count++) {
			// เช็คจบแต่ละ file ตาม $file_limit
			if ($data_count > 0 && ($data_count % $file_limit) == 0) {
				$workbook->close();
				$arr_file[] = $fname1;

				$fnum++; $fnum_text="_$fnum";
				$fname1=$fname.$fnum_text.".xls";
				$workbook = new writeexcel_workbook($fname1);

			//====================== SET FORMAT ======================//
				require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
			//====================== SET FORMAT ======================//

				$f_new = true;
			};
				
			// เช็คจบที่ข้อมูลแต่ละ sheet ตาม $data_limit
			if(($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
				if ($f_new) {
					$sheet_no=0; $sheet_no_text="";
					$f_new = false;
				} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
					$sheet_no++;
					$sheet_no_text = "_$sheet_no";
				}

				$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);

				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
			
				call_header($data_count, $head);
			}
				
			$pre_fix=str_repeat(" ", ($arr_data[$data_count][level]+1)*5);
			
			$xlsRow++;

			$worksheet->write($xlsRow, 0, $pre_fix.$arr_data[$data_count][kpi_parent].":".$arr_data[$data_count][kpi_id]."-".$arr_data[$data_count][kpi_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		} // end for
	} else {
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "***** ไม่พบข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 0));
	}

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "จำนวนข้อมูล $count_data รายการ", set_format("xlsFmtTitle", "B", "L", "", 0));

	$workbook->close();

	$arr_file[] = $fname1;

	require_once("excel_tailpart_subrtn.php");

	function get_kpi ($year_parent, $level) {		//function get_kpi ($kpi_parent, $level) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $arr_data, $data_count;

		$count_all = 0;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		//$cmd = " select KPI_ID, KPI_NAME from PER_PROJECT where KPI_ID_REF=$kpi_parent ";
		$cmd = " select KPI_ID, KPI_NAME,KPI_YEAR from PER_PROJECT where KPI_YEAR='$year_parent' ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			while($data = $db_dpis->get_array()){
				$arr_data[$data_count][kpi_id] = $data[KPI_ID];
				$arr_data[$data_count][kpi_parent] = $year_parent;//$data[KPI_ID];
				$arr_data[$data_count][kpi_name] = $data[KPI_NAME];
				$arr_data[$data_count][level] = $level;
				$data_count++;
				$count_all += get_kpi($data[KPI_YEAR], $level+1)+1;		//$count_all += get_kpi($data[KPI_ID], $level+1)+1;
			} // end while
		} // end if
		
		return $count_all;
	} // function count_all
?>