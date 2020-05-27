<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if ($PER_TYPE==1) {
		$search_from = ", PER_POSITION c";
		$arr_search_condition[] = "b.POS_ID=c.POS_ID";
	} elseif ($PER_TYPE==2) { 
		$search_from = ", PER_POS_EMP c";
		$arr_search_condition[] = "b.POEM_ID=c.POEM_ID";
	} elseif ($PER_TYPE==3) { 
		$search_from = ", PER_POS_EMPSER c";
		$arr_search_condition[] = "b.POEMS_ID=c.POEMS_ID"; 
	} elseif ($PER_TYPE==4) {
		$search_from = ", PER_POS_TEMP c";
		$arr_search_condition[] = "b.POT_ID=c.POT_ID"; 
	}
	
	if ($ABS_STARTDATE) {
		$temp_start =  save_date($ABS_STARTDATE);
		$arr_search_condition[] = "(ABS_STARTDATE >= '$temp_start')";
	} // end if
	
	if ($ABS_ENDDATE) {
		$temp_end =  save_date($ABS_ENDDATE);
		$arr_search_condition[] = "(ABS_ENDDATE <= '$temp_end')";
	} // end if

	if ($ORG_ID){ 
		$arr_search_condition[] = "(c.ORG_ID = $ORG_ID)";	
	}elseif($DEPARTMENT_ID){
		$cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and ORG_ID_REF=$DEPARTMENT_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	}elseif($MINISTRY_ID){
		$cmd = " select 	b.ORG_ID
						 from   	PER_ORG a, PER_ORG b
						 where  	a.OL_CODE='02' and b.OL_CODE='03' and a.ORG_ID_REF=$MINISTRY_ID and b.ORG_ID_REF=a.ORG_ID
						 order by a.ORG_ID, b.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	}elseif($PV_CODE){
		$cmd = " select 	ORG_ID
						 from   	PER_ORG
						 where  	OL_CODE='03' and PV_CODE='$PV_CODE'
						 order by ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	}
		
	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานแสดงข้อมูล$PERSON_TYPE[$search_per_type]ที่ส่งใบลาไม่ถูกต้อง/ยังไม่ได้ส่งใบลา";
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

		$worksheet->set_column(0, 0, 35);
		$worksheet->set_column(1, 1, 35);
		$worksheet->set_column(2, 2, 12);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 12);
		$worksheet->set_column(5, 5, 30);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ตั้งแต่วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "ถึงวันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "จำนวนวัน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "การส่งใบลา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, AB_CODE, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER, 
						  					b.POS_ID, b.POEM_ID, b.POEMS_ID, c.ORG_ID 
						 from 			PER_ABSENT a, PER_PERSONAL b
						  					$search_from
						 where 		b.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and ABS_LETTER in (1, 3) 
						  					$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, AB_CODE, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER, 
						  					b.POS_ID, b.POEM_ID, b.POEMS_ID, c.ORG_ID 
						 from 			PER_ABSENT a, PER_PERSONAL b  
						  					$search_from
						 where 		b.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and ABS_LETTER in (1, 3) 
						  					$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, AB_CODE, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER, 
						  					b.POS_ID, b.POEM_ID, b.POEMS_ID, c.ORG_ID 
						 from 			PER_ABSENT a, PER_PERSONAL b
						  					$search_from
						 where 		b.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and ABS_LETTER in (1, 3) 
						  					$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";
	} // end if
		if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
			//$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$total_data = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_PER_ID = $data[PER_ID];
		$TMP_PN_CODE = $data[PN_CODE];
		$TMP_PER_NAME = $data[PER_NAME];
		$TMP_PER_SURNAME = $data[PER_SURNAME];
		$TMP_ABS_STARTDATE = show_date_format($data[ABS_STARTDATE],$DATE_DISPLAY);
		$TMP_ABS_ENDDATE = show_date_format($data[ABS_ENDDATE],$DATE_DISPLAY);
		$TMP_ABS_DAY = trim($data[ABS_DAY]);
		$TMP_ABS_LETTER = trim($data[ABS_LETTER]);
		if ($TMP_ABS_LETTER == 1) 				$ABS_LETTER_STR = "ยังไม่ได้ส่ง";
		elseif ($TMP_ABS_LETTER == 3) 		$ABS_LETTER_STR = "ไม่ถูกต้อง";		

		$TMP_PN_CODE = trim($data[PN_CODE]);
		if($TMP_PN_CODE){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = $data2[PN_NAME];
		} // end if
		
		$TMP_AB_CODE = trim($data[AB_CODE]);
		if($TMP_AB_CODE){
			$cmd = " select AB_NAME from PER_ABSENTTYPE where AB_CODE='$TMP_AB_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_AB_NAME = $data2[AB_NAME];
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][per_name] =  (($NUMBER_DISPLAY==2)?convert2thaidigit($data_row):$data_row).". $TMP_PN_NAME$TMP_PER_NAME $TMP_PER_SURNAME";
		$arr_content[$data_count][ab_name] = $TMP_AB_NAME;
		$arr_content[$data_count][abs_startdate] = $TMP_ABS_STARTDATE;
		$arr_content[$data_count][abs_enddate] = $TMP_ABS_ENDDATE;
		$arr_content[$data_count][abs_day] = $TMP_ABS_DAY;
		$arr_content[$data_count][abs_letter] = $ABS_LETTER_STR;
		
		$data_count++;
		$total_data++;
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
			$PER_NAME = $arr_content[$data_count][per_name];
			$AB_NAME = $arr_content[$data_count][ab_name];
			$ABS_STARTDATE = $arr_content[$data_count][abs_startdate];
			$ABS_ENDDATE = $arr_content[$data_count][abs_enddate];
			$ABS_DAY = number_format($arr_content[$data_count][abs_day]);
			$ABS_LETTER = $arr_content[$data_count][abs_letter];
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$AB_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2,(($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_STARTDATE):$ABS_STARTDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_ENDDATE):$ABS_ENDDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4,(($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_DAY):$ABS_DAY), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$ABS_LETTER", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end for				

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "TLB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "TB", 0));
		$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "R", "TB", 0));
		$worksheet->write($xlsRow, 5, "จำนวนรายการทั้งหมด ". (($NUMBER_DISPLAY==2)?convert2thaidigit($total_data):$total_data)." รายการ", set_format("xlsFmtTableDetail", "B", "R", "TRB", 0));
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
	header("Content-Type: application/x-msexcel; name=\"รายงานแสดงข้อมูลที่ส่งใบลาไม่ถูกต้อง/ยังไม่ได้ส่งใบลา.xls\"");
	header("Content-Disposition: inline; filename=\"รายงานแสดงข้อมูลที่ส่งใบลาไม่ถูกต้อง/ยังไม่ได้ส่งใบลา.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>