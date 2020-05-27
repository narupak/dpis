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
	$report_title = "สรุปการเทียบเคียงข้อมูลประเภทตำแหน่ง";

	ini_set("max_execution_time", 1800);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = &new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("เทียบเคียงข้อมูลประเภทตำแหน่ง");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 30);
		$worksheet->set_column(1, 12, 8);
		
		$worksheet->write($xlsRow, 0, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		for($i=1; $i<11; $i++) $worksheet->write($xlsRow, (1+$i), "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 12, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, "$i", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // end if

	$cmd = " select PT_GROUP_N, PT_GROUP_NAME from PER_GROUP_N order by PT_GROUP_N ";
	$count_data = $db_dpis->send_cmd($cmd);
	$group_count = 0;
	while($data_dpis = $db_dpis->get_array()){
		$group_count++;
		$PT_GROUP_N = trim($data_dpis[PT_GROUP_N]);
		$PT_GROUP_NAME = trim($data_dpis[PT_GROUP_NAME]);

		$arr_group[$group_count][code] = $PT_GROUP_N;
		$arr_group[$group_count][name] = $PT_GROUP_NAME;
		for($i=1; $i<=11; $i++) $arr_group[$group_count]["level$i"] = 0;
		
		$cmd = " select PT_CODE_N, PT_NAME_N from PER_TYPE_N where trim(PT_GROUP_N)='$PT_GROUP_N' ";
		$db_dpis2->send_cmd($cmd);
		$type_count = 0;
		while($data_dpis2 = $db_dpis2->get_array()){
			$type_count++;
			$PT_CODE_N = trim($data_dpis2[PT_CODE_N]);
			$PT_NAME_N = trim($data_dpis2[PT_NAME_N]);

			$arr_type[$PT_GROUP_N][$type_count][code] = $PT_CODE_N;
			$arr_type[$PT_GROUP_N][$type_count][name] = $PT_NAME_N;
			for($i=1; $i<=11; $i++){ 
				$cmd = " select count(PER_ID) from PER_FORMULA where LEVEL_NO=$i and PT_CODE_N='$PT_CODE_N' ";
				$db->send_cmd($cmd);
				$data = $db->get_data();
				$count_per_id = $data[0];
				
				${"total_level".$i} += $count_per_id;
				$arr_group[$group_count]["level$i"] += $count_per_id;
				$arr_type[$PT_GROUP_N][$type_count]["level$i"] = $count_per_id;				
			} // end for
		} // end while
	} // end while

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 2;

		for($i=1; $i<=count($arr_group); $i++){
			$data_count++;
			$PT_GROUP_N = $arr_group[$i][code];
			$PT_GROUP_NAME = $arr_group[$i][name];
			
			$row_total = 0;
			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, "$i. $PT_GROUP_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
			for($level=1; $level<=11; $level++){ 
				$show_score = ($arr_group[$i]["level$level"])?$arr_group[$i]["level$level"]:"0";
				$row_total += $arr_group[$i]["level$level"];
				$worksheet->write($xlsRow, $level, $show_score, set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			} // end for
			$worksheet->write($xlsRow, 12, $row_total, set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
			
			for($j=1; $j<=count($arr_type[$PT_GROUP_N]); $j++){
				$data_count++;
				$PT_CODE_N = $arr_type[$PT_GROUP_N][$j][code];
				$PT_NAME_N = $arr_type[$PT_GROUP_N][$j][name];

				$row_total = 0;
				$xlsRow = $data_count;
				$worksheet->write($xlsRow, 0, "      $i.$j $PT_NAME_N", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				for($level=1; $level<=11; $level++){ 
					$show_score = ($arr_type[$PT_GROUP_N][$j]["level$level"])?$arr_type[$PT_GROUP_N][$j]["level$level"]:"";
					$row_total += $arr_type[$PT_GROUP_N][$j]["level$level"];
					$worksheet->write($xlsRow, $level, $show_score, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				} // end for
				$worksheet->write($xlsRow, 12, $row_total, set_format("xlsFmtTableHeader", "", "R", "TLRB", 0));
			} // end for
		} // end for

		$grand_total = 0;
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "รวม", set_format("xlsFmtTableHeader", "", "C", "TLRB", 0));
		for($i=1; $i<=11; $i++){ 
			$grand_total += ${"total_level".$i};
			$worksheet->write($xlsRow, $i, ((${"total_level".$i})?${"total_level".$i}:"0"), set_format("xlsFmtTableHeader", "", "R", "TLRB", 0));
		} // end for
		$worksheet->write($xlsRow, $i, $grand_total, set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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