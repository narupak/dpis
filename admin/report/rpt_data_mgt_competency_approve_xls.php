<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	ini_set("max_execution_time", $max_execution_time);

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$company_name = "";
	$report_title = "การขึ้นบัญชีผู้ผ่านการประเมินสมรรถนะหลักทางการบริหาร (เฉพาะผู้ผ่านหลักสูตร นบส.1)";
	$report_code = "rpt_data_mgt_competency_approve_xls";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	function print_header(){
		global $worksheet, $xlsRow;

		$worksheet->set_column(0, 0, 20);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 70);
		$worksheet->set_column(3, 3, 90);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "วันที่เข้ารับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง/สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "การประเมิน", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 1, "ขึ้นบัญชี", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
	} // function		

	$temp_start =  save_date($search_test_date_from);
	$temp_end =  save_date($search_test_date_to);
	$temp_start =  save_date($search_approve_date_from);
	$temp_end =  save_date($search_approve_date_to);
	$cmd = " select CA_ID, CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE,
							CA_APPROVE_DATE, PN_CODE, CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2,
							CA_SCORE_3, CA_SCORE_4, CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10,
							CA_SCORE_11, CA_SCORE_12, CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1,
							CA_ORG_NAME2, CA_LINE, LEVEL_NO, CA_MGT, CA_NEW_SCORE_1, CA_NEW_SCORE_2, CA_NEW_SCORE_3, CA_NEW_SCORE_4,
							CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, CA_NEW_SCORE_9, CA_NEW_SCORE_10,
							CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE 
							from PER_MGT_COMPETENCY_ASSESSMENT 
							where CA_APPROVE_DATE>='$temp_start' and CA_APPROVE_DATE<='$temp_end'
							order by CA_CODE ";
//							where CA_TEST_DATE>='$temp_start' and CA_TEST_DATE<='$temp_end'
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$CA_TEST_DATE = show_date_format(trim($data[CA_TEST_DATE]), $DATE_DISPLAY);
		$CA_APPROVE_DATE = show_date_format(trim($data[CA_APPROVE_DATE]), $DATE_DISPLAY);
//		$PER_ID = $data[PER_ID];
		$PER_ID = $data[CA_ID];
		$PN_CODE = trim($data[PN_CODE]);
		$CA_NAME = trim($data[CA_NAME]);
		$CA_SURNAME = trim($data[CA_SURNAME]);
		$CA_MINISTRY_NAME = trim($data[CA_MINISTRY_NAME]);
		$CA_DEPARTMENT_NAME = trim($data[CA_DEPARTMENT_NAME]);
		$CA_LINE = trim($data[CA_LINE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$CA_MGT = trim($data[CA_MGT]);

		$TMP_POSITION = "";
		if ($PER_ID) {
			$cmd = " select POS_ID, a.LEVEL_NO, b.LEVEL_NAME,b.POSITION_LEVEL, PN_CODE, PER_NAME, PER_SURNAME 
							from PER_PERSONAL a, PER_LEVEL b 
							where PER_ID=$PER_ID and a.LEVEL_NO=b.LEVEL_NO ";
			$db_dpis2->send_cmd($cmd);
			if ($data2 = $db_dpis2->get_array()) {
				$POS_ID = trim($data2[POS_ID]);
				$LEVEL_NO = trim($data2[LEVEL_NO]);
				$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
				$PN_CODE = trim($data2[PN_CODE]);
				$CA_NAME = trim($data2[PER_NAME]);
				$CA_SURNAME = trim($data2[PER_SURNAME]);
	
				if ($POS_ID) { 
					$cmd = " select b.PL_NAME, a.CL_NAME, c.ORG_NAME, a.PT_CODE from PER_POSITION a, PER_LINE b, PER_ORG c 
							where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
					$db_dpis2->send_cmd($cmd);
					//echo $cmd;
					$data2 = $db_dpis2->get_array();
	//				$TMP_POSITION = $data2[PL_NAME] . " " . $data2[CL_NAME];
					$TMP_POSITION = trim($data2[PL_NAME])?($data2[PL_NAME] ."".$LEVEL_NAME. ((trim($data2[PT_NAME]) != "ทั่วไป" && $LEVEL_NO >= 6)?$data2[PT_NAME]:"")):"".$LEVEL_NAME;
				}
				$ORG_NAME = $data2[ORG_NAME];
			} else { // ถ้าอ่านไม่ได้
				$POS_ID = "";
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$LEVEL_NAME = "";
				$ORG_NAME = trim($data[CA_ORG_NAME]);
				$TMP_POSITION = trim($data[CA_LINE]);
			}	// if ($data2 = $db_dpis2->get_array())
		}

		$PN_NAME = "";
		if ($PN_CODE) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][test_date] = $CA_TEST_DATE;
		$arr_content[$data_count][approve_date] = $CA_APPROVE_DATE;
		$arr_content[$data_count][ca_name] = "$PN_NAME$CA_NAME $CA_SURNAME";
		$arr_content[$data_count][position] = "$TMP_POSITION<br>$ORG_NAME";
				
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
			$CA_TEST_DATE = $arr_content[$data_count][test_date];
			$CA_APPROVE_DATE = $arr_content[$data_count][approve_date];
			$CA_NAME = $arr_content[$data_count][ca_name];
			$POSITION = $arr_content[$data_count][position];
		
//			$arr_data = (array) null;
//			$arr_data[] = $CA_TEST_DATE;
//			$arr_data[] = $CA_APPROVE_DATE;
//			$arr_data[] = $CA_NAME;
//			$arr_data[] = $POSITION;

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$CA_TEST_DATE.", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$CA_APPROVE_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$CA_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end for
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>