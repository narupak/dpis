<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$arr_content = (array) null;

	if ($DEPARTMENT_ID == "total") {
		$txt_dept = "";
	} else {
		$txt_dept = "and DEPARTMENT_ID=$DEPARTMENT_ID";
		if ($ORG_ID <> $DEPARTMENT_ID) {
			$txt_dept = $txt_dept." and ORG_ID=$ORG_ID";
		}
	}
	$cmd = "  select PD_GUIDE_ID, KF_END_DATE, count(PD_GUIDE_ID) as CNT from PER_DEVELOPE_PLAN a, PER_KPI_FORM b
					where PD_PLAN_KF_ID = KF_ID and KF_CYCLE=$KF_CYCLE and KF_START_DATE='$KF_START_DATE' $txt_dept 
					group by PD_GUIDE_ID, KF_END_DATE
					order by PD_GUIDE_ID, KF_END_DATE ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while ($data = $db_dpis->get_array()) {
		if (!$KF_YEAR) {
			$KF_YEAR = substr($data[KF_END_DATE], 0, 4) + 543;
		}
		$GUIDE_ID = $data[PD_GUIDE_ID];
		// อ่านข้อมูลแนวทาง
		$cmd1 = " select 	* from PER_DEVELOPE_GUIDE where PD_GUIDE_ID=$GUIDE_ID ";
		$db_dpis2->send_cmd($cmd1);
		$data2 = $db_dpis2->get_array();
		$CP_CODE = trim($data2[PD_GUIDE_COMPETENCE]);
		$DESCRIPTION1 = trim($data2[PD_GUIDE_DESCRIPTION1]);
		// อ่านข้อมูลสมรรถนะ
		$cmd1 = " select * from PER_COMPETENCE where CP_CODE=$CP_CODE ";
		$db_dpis2->send_cmd($cmd1);
		$data2 = $db_dpis2->get_array();
		$CP_NAME = $data2[CP_NAME];

		$arr_content[$GUIDE_ID][cnt] = $data[CNT];
		$arr_content[$GUIDE_ID][cp_code] = $CP_CODE;
		$arr_content[$GUIDE_ID][desc] = "$CP_NAME:::$DESCRIPTION1";
	} // end while read PER_KPI_FORM
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$report_code = "R_ORG_Develop_Plan";

	if (count($arr_content)) {
		if ($DEPARTMENT_ID == "total") {
			$head_dept = "";
		} else {
			$cmd1 = " SELECT * FROM PER_ORG WHERE ORG_ID=$DEPARTMENT_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd1);
			//	$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$head_dept = "$data2[ORG_NAME]";
			if ($ORG_ID <> $DEPARTMENT_ID) {
				$cmd1 = " SELECT * FROM PER_ORG WHERE ORG_ID=$ORG_ID ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd1);
			//		$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$head_dept = $head_dept."  $data2[ORG_NAME]";
			}
		}	

		$report_title = "ตารางแสดงจำนวนแผนการพัฒนารวมของข้าราชการ $head_dept";
		$company_name = "ประจำรอบการประเมินครั้งที่ $KF_CYCLE พ.ศ. $KF_YEAR ";
		
		function print_header(){
			global $worksheet, $xlsRow;
			global $heading_name;
		
			$worksheet->set_column(0, 0, 6);
			$worksheet->set_column(1, 1, 130);
			$worksheet->set_column(2, 2, 12);

			$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
			$worksheet->write($xlsRow, 1, "สมรรถนะ:::แผนการพัฒนา", set_format("xlsFmtTableHeader", "B", "L", "TLR", 1));
			$worksheet->write($xlsRow, 2, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		} // function
	
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$xlsRow++;
		} //if($company_name){
		
		print_header();
		
		foreach($arr_content as $I_GUIDE_ID => $val) {
			$xlsRow++;
			$seq_no++;
			$I_CNT = $arr_content[$I_GUIDE_ID][cnt];
			$I_DESC = $arr_content[$I_GUIDE_ID][desc];
			$worksheet->write($xlsRow, 0, ($seq_no?number_format($seq_no):"-"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$I_DESC", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$I_CNT", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
		} // end foreach
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "BL", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "B", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "BR", 1));
	} else { // else if ($count_data)
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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