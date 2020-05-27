<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", 0);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	
	

	$company_name = "";
	$report_title = "ประวัติการลา : " . $PER_NAME;
	$report_code = "P0110";
	
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
		global $worksheet, $xlsRow;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 20);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 40);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ประเภทการลา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ตั้งแต่วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "ถึงวันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "จำนวนวัน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		
		
	if($DPISDB=="oci8"){
		$cmd = " select ABS_ID, pah.AB_CODE, pat.AB_NAME, ABS_STARTDATE, ABS_STARTPERIOD,
                                ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, AUDIT_FLAG, ABS_REMARK   
                        from PER_ABSENTHIS pah, PER_ABSENTTYPE pat 
                        where PER_ID=$PER_ID and pah.AB_CODE=pat.AB_CODE 
                        ORDER BY ABS_STARTDATE desc ";	
	}// end if
	
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo $cmd;
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		//$TMP_PER_ID = $data[PER_ID];
                $db_AB_NAME = $data[AB_NAME];
                $db_ABS_STARTDATE = show_date_format($data[ABS_STARTDATE], $DATE_DISPLAY);
                $db_ABS_ENDDATE = show_date_format($data[ABS_ENDDATE], $DATE_DISPLAY);
                
                $db_ABS_STARTPERIOD = $data[ABS_STARTPERIOD];
                $db_ABS_ENDPERIOD = $data[ABS_ENDPERIOD];
                
                $STR_ABS_STARTPERIOD ="";
                if($db_ABS_STARTPERIOD=="1"){
                    $STR_ABS_STARTPERIOD =" (ครึ่งวันเช้า)";
                }elseif($db_ABS_STARTPERIOD=="2"){
                    $STR_ABS_STARTPERIOD =" (ครึ่งวันบ่าย)";
                }
                if($data[ABS_DAY]!="0.5"){
                    $STR_ABS_STARTPERIOD ="";
                }
                $db_ABS_DAY = trim(round($data[ABS_DAY],2)).$STR_ABS_STARTPERIOD;
                $db_ABS_REMARK = $data[ABS_REMARK];
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][ab_name] = $db_AB_NAME;
		$arr_content[$data_count][abs_startdate] = $db_ABS_STARTDATE;
		$arr_content[$data_count][abs_enddate] = $db_ABS_ENDDATE;
		$arr_content[$data_count][abs_day] = $db_ABS_DAY;
		$arr_content[$data_count][abs_remark] = $db_ABS_REMARK;
				
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
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$AB_NAME = $arr_content[$data_count][ab_name];
			$ABS_STARTDATE = $arr_content[$data_count][abs_startdate];
			$ABS_ENDDATE = $arr_content[$data_count][abs_enddate];
			$ABS_DAY = $arr_content[$data_count][abs_day];
			$ABS_REMARK = $arr_content[$data_count][abs_remark];
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0,(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$AB_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$ABS_STARTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$ABS_ENDDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$ABS_DAY", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5,  "$ABS_REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 0);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"P0110.xls\"");
	header("Content-Disposition: inline; filename=\"P0110.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>