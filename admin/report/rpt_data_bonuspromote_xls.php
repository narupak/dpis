<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานการจัดทำเงินรางวัลประจำปี";
	$report_code = "";
	
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
		$worksheet->set_column(1, 1, 40);
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 15);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง/ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "เงินรางวัลประจำปี (%)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "จำนวนเงิน (บาท)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		
		
	$TMP_ORG_ID = (trim($ORG_ID))? $ORG_ID : $ORG_ID_ASS; 
	if ($BONUS_TYPE == 1) {					
		$table = ", PER_POSITION d ";
		$where = " and d.ORG_ID=$TMP_ORG_ID and b.POS_ID=d.POS_ID ";
	} elseif ($BONUS_TYPE == 2) {		
		$table = ", PER_POS_EMP d ";
		$where = " and d.ORG_ID=$TMP_ORG_ID and b.POEM_ID=d.POEM_ID ";
	} elseif ($BONUS_TYPE == 3) {		
		$table = ", PER_POS_EMPSER d ";	
		$where = " and d.ORG_ID=$TMP_ORG_ID and b.POEMS_ID=d.POEM_ID ";
	} elseif ($BONUS_TYPE == 4) {			
		$table = ", PER_POS_TEMP d ";	
		$where = " and d.ORG_ID=$TMP_ORG_ID and b.POT_ID=d.POT_ID ";
	}

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, BONUS_PERCENT, BONUS_QTY, PER_NAME, PER_SURNAME, PN_NAME, PER_SALARY, b.LEVEL_NO, e.POSITION_LEVEL, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
						 from			PER_BONUSPROMOTE a, PER_PERSONAL b, PER_PRENAME c, PER_LEVEL e  $table 
						 where		BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and 
											a.DEPARTMENT_ID=$DEPARTMENT_ID and 								
											a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE  and b.LEVEL_NO=e.LEVEL_NO
											$where 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		a.PER_ID, BONUS_PERCENT, BONUS_QTY, PER_NAME, PER_SURNAME, PN_NAME, PER_SALARY, b.LEVEL_NO, e.POSITION_LEVEL, 
											b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
						 from 			PER_BONUSPROMOTE a, PER_PERSONAL b, PER_PRENAME c, PER_LEVEL e  $table 
						 where		BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and 
											a.DEPARTMENT_ID=$DEPARTMENT_ID and 
										  	a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE  and b.LEVEL_NO=e.LEVEL_NO
										  	$where 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, BONUS_PERCENT, BONUS_QTY, PER_NAME, PER_SURNAME, PN_NAME, PER_SALARY, b.LEVEL_NO, e.POSITION_LEVEL, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
						 from			PER_BONUSPROMOTE a, PER_PERSONAL b, PER_PRENAME c, PER_LEVEL e  $table 
						 where		BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and 
											a.DEPARTMENT_ID=$DEPARTMENT_ID and 								
											a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE  and b.LEVEL_NO=e.LEVEL_NO
											$where 
						 order by 	PER_NAME, PER_SURNAME ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_PER_ID = $data[PER_ID];
		$TMP_BONUS_PERCENT = $data[BONUS_PERCENT];
		$TMP_BONUS_QTY = number_format($data[BONUS_QTY], 2, '.', ',');
		$TMP_PER_SALARY = number_format($data[PER_SALARY], 2, '.', ',') ;
		$tmp_salary = $data[PER_SALARY];
		$TMP_PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];
		$TMP_LEVEL_NO = trim($data[LEVEL_NO]);
		$TMP_LEVEL_NAME = trim($data[POSITION_LEVEL]);
		
		$TMP_POS_ID = $data[POS_ID];
		$TMP_POEM_ID = $data[POEMS_ID];
		$TMP_POEMS_ID = $data[POEM_ID];	
		$TMP_POT_ID = $data[POT_ID];	
		if($TMP_POS_ID){
			$cmd = " select PL_NAME, a.PT_CODE from PER_POSITION a, PER_LINE b where POS_ID=$TMP_POS_ID and a.PL_CODE=b.PL_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PL_NAME = trim($data2[PL_NAME]);
			$TMP_PT_CODE = trim($data2[PT_CODE]);
			$TMP_PT_NAME = trim($data2[PT_NAME]);
			$POSITION = trim($TMP_PL_NAME)?($TMP_PL_NAME . $TMP_LEVEL_NAME . (($TMP_PT_NAME != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?"$TMP_PT_NAME":"")):" $TMP_LEVEL_NAME";
		} // end if
		if($TMP_POEM_ID){
			$cmd = " select PN_NAME from PER_POS_EMP a, PER_POS_NAME b where a.POEM_ID=$TMP_POEM_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION = $data2[PN_NAME];
		} // end if
		if($TMP_POEMS_ID){
			$cmd = " select EP_NAME from PER_POS_EMPSER a, PER_EMPSER_POS_NAME where a.POEMS_ID=$TMP_POEMS_ID and a.POEMS_ID=b.POEMS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION = $data2[EP_NAME];
		} // end if
		if($TMP_POT_ID){
			$cmd = " select TP_NAME from PER_POS_TEMP a, PER_TEMP_POS_NAME where a.POT_ID=$TMP_POT_ID and a.POT_ID=b.POT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION = $data2[TP_NAME];
		} // end if
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][per_name] = $TMP_PER_NAME;
		$arr_content[$data_count][per_position] = $POSITION;
		$arr_content[$data_count][per_salary] = $TMP_PER_SALARY;
		$arr_content[$data_count][bonus_percent] = $TMP_BONUS_PERCENT;
		$arr_content[$data_count][bonus_qty] = $TMP_BONUS_QTY;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
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

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 1, "ปีงบประมาณ $BONUS_YEAR", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 2, "กระทรวง $MINISTRY_NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 1, "กรม $DEPARTMENT_NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 2, "สำนัก/กอง $ORG_NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 1, "เงินที่จัดสรร $BONUSQ_QTY", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 2, "รวมยอด $SUM_BONUSQ_QTY", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 1, "คงเหลือ $REST_BONUSQ_QTY", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 2, "% จัดทำเงินรางวัลประจำปี $BONUS_PERCENT_ALL", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

	if($count_data){
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_POSITION = $arr_content[$data_count][per_position];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$BONUS_PERCENT = $arr_content[$data_count][bonus_percent];
			$BONUS_QTY = $arr_content[$data_count][bonus_qty];
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$PER_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$BONUS_PERCENT", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$BONUS_QTY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
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

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"รายงานการจัดทำเงินรางวัลประจำปี.xls\"");
	header("Content-Disposition: inline; filename=\"รายงานการจัดทำเงินรางวัลประจำปี.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>