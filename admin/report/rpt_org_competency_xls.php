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
			$txt_dept = $txt_dept." and ORG_ID=$ORG_ID ";
		}
	}
	$cmd = "  select *	from PER_KPI_FORM 
					where KF_CYCLE=$KF_CYCLE and KF_START_DATE='$KF_START_DATE' $txt_dept ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while ($data = $db_dpis->get_array()) {
		if (!$KF_YEAR) {
			$KF_YEAR = substr($data[KF_END_DATE], 0, 4) + 543;
		}
		$KF_ID = $data[KF_ID];
		$cmd1 = " select * from PER_COMPETENCY_FORM where KF_ID=$KF_ID and CF_STATUS=1 ORDER BY CF_ID ";
		$db_dpis2->send_cmd($cmd1);
		while ($data2 = $db_dpis2->get_array()) {
			$CF_SCORE = $data2[CF_SCORE];
			$ARR_POINT = explode(",",$CF_SCORE);
			for($score_i = 0; $score_i < count($ARR_POINT); $score_i++) {
				$POINT_K = explode(":",$ARR_POINT[$score_i]);
				$CP_CODE = $POINT_K[0];
				$POINT = $POINT_K[1];
//				echo "$CP_CODE:$POINT<br>";
				$arr_content[$CP_CODE][$POINT]++;
			} //  end for $score_i
		} // end while read PER_COMPETENCY_FORM
	} // end while read PER_KPI_FORM
	
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
			$head_dept = $head_dept." $data2[ORG_NAME]";
		}
	}	
	$report_title = "ตารางแสดงจำนวนผู้ประเมินแยกตามสมรรถนะและคะแนน ของข้าราชการ $head_dept";
	$company_name = "ประจำรอบการประเมินครั้งที่ $KF_CYCLE พ.ศ. $KF_YEAR ";
	$report_code = "R_ORG_Competency";

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
		global $heading_name;
		
		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 70);
		$worksheet->set_column(2, 2, 12);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 12);
		$worksheet->set_column(5, 5, 12);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 12);

		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 1, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "L", "TLR", 1));
		$worksheet->write($xlsRow, 2, " ", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 3, " ", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 4, "คะแนนประเมิน", set_format("xlsFmtTableHeader", "B", "L", "TB", 1));
		$worksheet->write($xlsRow, 5, " ", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 6, " ", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 7, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "3", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "4", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "5", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	if(count($arr_content) > 0) {
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$xlsRow++;
		} //if($company_name){
		
		print_header();
	
		$ctot = (array) null;
		$gtotal=0;
		foreach($arr_content as $CP_CODE => $val) {
//			echo "CP_CODE=$CP_CODE<br>";
			$cmd1 = " select * from PER_COMPETENCE where CP_CODE=$CP_CODE ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$CP_NAME = $data2[CP_NAME];
			$xlsRow++;
			$seq_no++;
			$worksheet->write($xlsRow, 0, ($seq_no?number_format($seq_no):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$CP_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$cnt_col=1;
			$tot = 0;
			for($p=1; $p <= 5; $p++) {
				$cnt_col++;
				$val1 = (!$val[$p] ? "-" : $val[$p]);
				$worksheet->write_string($xlsRow, $cnt_col, "$val1", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$ctot[$p]=$ctot[$p]+(!$val[$p] ? 0 : $val[$p]);
				$tot=$tot+(!$val[$p] ? 0 : $val[$p]);
				$gtotal=$gtotal+(!$val[$p] ? 0 : $val[$p]);
			} // end for
			$worksheet->write($xlsRow, $cnt_col+1, $tot, set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
		} // end for
//		$ctot[1]=(!$ctot[1] ? "-" : $ctot[1]);
//		$ctot[2]=(!$ctot[2] ? "-" : $ctot[2]);
//		$ctot[3]=(!$ctot[3] ? "-" : $ctot[3]);
//		$ctot[4]=(!$ctot[4] ? "-" : $ctot[4]);
//		$ctot[5]=(!$ctot[5] ? "-" : $ctot[5]);
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "BL", 1));
		$worksheet->write($xlsRow, 1, "รวม", set_format("xlsFmtTableHeader", "B", "C", "B", 1));
		$worksheet->write($xlsRow, 2, "$ctot[1]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 3, "$ctot[2]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 4, "$ctot[3]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 5, "$ctot[4]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 6, "$ctot[5]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 7, "$gtotal", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
	} else { // else if ($count_data)
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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