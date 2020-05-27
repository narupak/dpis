<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
//	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis = $db;
	$db_dpis2 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================
		
	$db2 = new connect_db($db_host, $db_name, $db_user, $db_pwd);

	$report_title = "สรุปการเทียบเคียงข้อมูลตำแหน่งว่าง";

	ini_set("max_execution_time", 1800);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = &new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("เทียบเคียงข้อมูลตำแหน่งว่าง");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$cmd = " select PT_GROUP_N, PT_GROUP_NAME from PER_GROUP_N order by PT_GROUP_N ";
	$count_data = $db_dpis->send_cmd($cmd);
	while($data_dpis = $db_dpis->get_array()){
		$PT_GROUP_N = trim($data_dpis[PT_GROUP_N]);
		$PT_GROUP_NAME = trim($data_dpis[PT_GROUP_NAME]);

		$arr_group[$PT_GROUP_N] = $PT_GROUP_NAME;
		
		$cmd = " select PT_CODE_N, PT_NAME_N from PER_TYPE_N where trim(PT_GROUP_N)='$PT_GROUP_N' ";
		$db_dpis2->send_cmd($cmd);
		while($data_dpis2 = $db_dpis2->get_array()){
			$PT_CODE_N = trim($data_dpis2[PT_CODE_N]);
			$PT_NAME_N = trim($data_dpis2[PT_NAME_N]);

			$arr_type[$PT_GROUP_N][$PT_CODE_N] = $PT_NAME_N;
			$arr_type_count[$PT_GROUP_N][$PT_CODE_N] = 0;
		} // end while

		$arr_type[$PT_GROUP_N][TOTAL] = "รวม";
		$arr_type_count[$PT_GROUP_N][TOTAL] = 0;		
	} // end while	

	function print_header($xlsRow){
		global $worksheet;
		global $arr_group, $arr_type;

		$worksheet->set_column(0, 0, 30);
		$worksheet->set_column(1, 20, 6);
		
		$worksheet->write($xlsRow, 0, "ระดับควบ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$count_temp = 0;
		foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){
			foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
				$count_temp++;
				if($count_temp==1) $worksheet->write($xlsRow, $count_temp, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
				else $worksheet->write($xlsRow, $count_temp, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
			} // end foreach
		} // end foreach
		$count_temp++;
		$worksheet->write($xlsRow, $count_temp, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$count_temp = 0;
		foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){
			$count_temp_2 = 0;
			foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
				$count_temp++;
				$count_temp_2++;
				if($count_temp_2==1) $worksheet->write($xlsRow, $count_temp, "$PT_GROUP_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
				else $worksheet->write($xlsRow, $count_temp, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
			} // end foreach
		} // end foreach
		$count_temp++;
		$worksheet->write($xlsRow, $count_temp, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$count_temp = 0;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){
			foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
				$count_temp++;
				if($PT_CODE_N == "TOTAL") $PT_CODE_N = "รวม";
				$worksheet->write($xlsRow, $count_temp, "$PT_CODE_N", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			} // end foreach
		} // end foreach
		$count_temp++;
		$worksheet->write($xlsRow, $count_temp, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // end function

	$cmd = " select distinct CL_NAME from PER_MAP_POSITION where PER_ID=0 order by CONVERT(LEVEL_NO, UNSIGNED) ";
	$count_data = $db->send_cmd($cmd);
	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$count_temp = 0;
		foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){
			foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
				$count_temp++;
				$worksheet->write($xlsRow, $count_temp, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} // end foreach
		} // end foreach
		$count_temp++;
		$worksheet->write($xlsRow, $count_temp, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 3;

		$GRAND_TOTAL = 0;
		while($data = $db->get_array()){
			$data_count++;
			$CL_NAME = $data[CL_NAME];
			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, "$CL_NAME", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));

			$count_co_level_total = 0;
			$count_temp = 0;
			foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){

				$count_type_total = 0;
				foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
					$count_temp++;
					if($PT_CODE_N != "TOTAL"){
						$cmd = " select POS_ID from PER_MAP_POSITION where PER_ID=0 and trim(CL_NAME)='$CL_NAME' and trim(PT_CODE_N)='$PT_CODE_N' ";
						$count_position = $db2->send_cmd($cmd);
						$count_type_total += $count_position;
						$count_co_level_total += $count_position;	
	
						$arr_type_count[$PT_GROUP_N][$PT_CODE_N] += $count_position;
						$arr_type_count[$PT_GROUP_N][TOTAL] += $count_position;

						$show_score = (($count_position)?$count_position:"");
						$worksheet->write($xlsRow, $count_temp, $show_score, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					}else{
						$show_score = (($count_type_total)?$count_type_total:"0");
						$worksheet->write($xlsRow, $count_temp, $show_score, set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					} // end if
				} // end foreach
			} // end foreach
			
			$count_temp++;
			$GRAND_TOTAL += $count_co_level_total;
			$show_score = (($count_co_level_total)?$count_co_level_total:"0");
			$worksheet->write($xlsRow, $count_temp, $show_score, set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
		} // end while

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$count_temp = 0;
		foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){
			foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
				$count_temp++;
				if($PT_CODE_N != "TOTAL"){
					$show_score = (($arr_type_count[$PT_GROUP_N][$PT_CODE_N])?$arr_type_count[$PT_GROUP_N][$PT_CODE_N]:"0");
					$worksheet->write($xlsRow, $count_temp, $show_score, set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
				}else{
					$show_score = (($arr_type_count[$PT_GROUP_N][TOTAL])?$arr_type_count[$PT_GROUP_N][TOTAL]:"0");
					$worksheet->write($xlsRow, $count_temp, $show_score, set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
				} // end if
			} // end foreach
		} // end foreach

		$count_temp++;
		$worksheet->write($xlsRow, $count_temp, $GRAND_TOTAL, set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$count_temp = 0;
		foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){
			foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
				$count_temp++;
				$worksheet->write($xlsRow, $count_temp, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} // end foreach
		} // end foreach
		$count_temp++;
		$worksheet->write($xlsRow, $count_temp, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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