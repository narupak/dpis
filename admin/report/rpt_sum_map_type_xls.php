<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	// ==========================
	$report_title = "สรุปโครงสร้างอัตรากำลังตามพระราชบัญญัติระเบียบข้าราชการพลเรือน พ.ศ.2551  บัญชี 4";

	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("บัญชี 4");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $DEPARTMENT_NAME, $MINISTRY_NAME;

		$worksheet->set_column(0, 0, 30);
		$worksheet->set_column(1, 11, 6);
		$worksheet->set_column(12, 12, 12);
		$worksheet->set_column(13, 13, 12);
		$worksheet->set_column(14, 14, 12);
		$worksheet->set_column(15, 15, 10);
		
		$worksheet->write($xlsRow, 0, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 1, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		for($i=1; $i<11; $i++) $worksheet->write($xlsRow, (1+$i), "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 12, "จำนวนตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 13, "จำนวนตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 14, "จำนวนตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 15, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, "$i", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "ทั้งหมด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "มีคนครอง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "ว่าง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // end if

	$group_count = 0;
	$arr_group[4][name] = "ทั่วไป";
	$arr_group[3][name] = "วิชาการ";
	$arr_group[2][name] = "อำนวยการ";
	$arr_group[1][name] = "บริหาร";
	for ($PT_GROUP_N=1;$PT_GROUP_N<=4;$PT_GROUP_N++){
		$group_count++;
		$arr_group[$group_count][code] = $PT_GROUP_N;
		for($i=1; $i<=11; $i++) $arr_group[$group_count]["level$i"] = 0;
		if ($PT_GROUP_N==4) $PT_GROUP = "O";
		elseif ($PT_GROUP_N==3) $PT_GROUP = "K";
		elseif ($PT_GROUP_N==2) $PT_GROUP = "D";
		elseif ($PT_GROUP_N==1) $PT_GROUP = "M";
		
		$cmd = " select LEVEL_NO, LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO) like '$PT_GROUP%' order by LEVEL_SEQ_NO DESC ";
		$db_dpis2->send_cmd($cmd);
		$type_count = 0;
		while($data_dpis2 = $db_dpis2->get_array()){
			$type_count++;
			$LEVEL_NO = trim($data_dpis2[LEVEL_NO]);
			$LEVEL_NAME = trim($data_dpis2[LEVEL_NAME]);

			$arr_type[$PT_GROUP_N][$type_count][code] = $LEVEL_NO;
			$arr_type[$PT_GROUP_N][$type_count][name] = $LEVEL_NAME;
			for($i=1; $i<=11; $i++){ 
				$level = "$i";
				$level_no = str_pad(trim($i), 2, "0", STR_PAD_LEFT);
				$cmd = " select count(PER_ID) as COUNT_DATA from PER_COMDTL where COM_ID = $COM_ID and CMD_LEVEL='$level_no' and LEVEL_NO='$LEVEL_NO' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$count_per_id = $data[COUNT_DATA];
				
				$cmd = " select count(PER_ID) as COUNT_DATA from PER_COMDTL where COM_ID = $COM_ID and CMD_LEVEL='$level_no' and LEVEL_NO='$LEVEL_NO' and per_id < 900000000 ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$count_per = $data[COUNT_DATA];
				
				${"total_level".$i} += $count_per_id;
				${"tot_level".$i} += $count_per;
				$arr_group[$group_count]["level$i"] += $count_per_id;
				$arr_group[$group_count]["lvl$i"] += $count_per;
				$arr_type[$PT_GROUP_N][$type_count]["level$i"] = $count_per_id;				
				$arr_type[$PT_GROUP_N][$type_count]["lvl$i"] = $count_per;				
			} // end for
		} // end while
	} // end for

		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 2;

		for($i=1; $i<=count($arr_group); $i++){
			$data_count++;
			$PT_GROUP_N = $arr_group[$i][code];
			$PT_GROUP_NAME = $arr_group[$i][name];
			
			$row_total = $row_total1 = 0;
			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, "$PT_GROUP_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
			for($level=1; $level<=11; $level++){ 
				$show_score = ($arr_group[$i]["level$level"])?$arr_group[$i]["level$level"]:"0";
				$row_total += $arr_group[$i]["level$level"];
				$row_total1 += $arr_group[$i]["lvl$level"];
				$worksheet->write($xlsRow, $level, $show_score?number_format($show_score):"-", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			} // end for
			$worksheet->write($xlsRow, 12, $row_total?number_format($row_total):"-", set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 13, $row_total1?number_format($row_total1):"-", set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 14, $row_total-$row_total1?number_format($row_total-$row_total1):"-", set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
			
			for($j=1; $j<=count($arr_type[$PT_GROUP_N]); $j++){
				$data_count++;
				$LEVEL_NO = $arr_type[$PT_GROUP_N][$j][code];
				$LEVEL_NAME = $arr_type[$PT_GROUP_N][$j][name];

				$row_total = $row_total1 = 0;
				$xlsRow = $data_count;
				$worksheet->write($xlsRow, 0, "      $LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				for($level=1; $level<=11; $level++){ 
					$show_score = ($arr_type[$PT_GROUP_N][$j]["level$level"])?$arr_type[$PT_GROUP_N][$j]["level$level"]:"";
					$row_total += $arr_type[$PT_GROUP_N][$j]["level$level"];
					$row_total1 += $arr_type[$PT_GROUP_N][$j]["lvl$level"];
					$worksheet->write($xlsRow, $level, $show_score, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				} // end for
				$worksheet->write($xlsRow, 12, $row_total?number_format($row_total):"-", set_format("xlsFmtTableHeader", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 13, $row_total1?number_format($row_total1):"-", set_format("xlsFmtTableHeader", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 14, $row_total-$row_total1?number_format($row_total-$row_total1):"-", set_format("xlsFmtTableHeader", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "", "R", "TLRB", 0));
			} // end for
		} // end for

		$grand_total = $grand_total1 = 0;
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "รวม", set_format("xlsFmtTableHeader", "", "C", "TLRB", 0));
		for($i=1; $i<=11; $i++){ 
			$grand_total += ${"total_level".$i};
			$grand_total1 += ${"tot_level".$i};
			$worksheet->write($xlsRow, $i, ((${"total_level".$i})?${"total_level".$i}:"0"), set_format("xlsFmtTableHeader", "", "R", "TLRB", 0));
		} // end for
		$worksheet->write($xlsRow, 12, $grand_total, set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 13, $grand_total1, set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 14, $grand_total-$grand_total1, set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));

	$workbook->close();

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