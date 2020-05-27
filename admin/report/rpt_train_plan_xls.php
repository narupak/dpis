<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$company_name = "";
	$report_title = "แผนฝึกอบรมประจำปี $year";
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
		global $POS_NO_TITLE, $FULLNAME_TITLE;

		$worksheet->set_column(0, 0, 50);
		$worksheet->set_column(1, 1, 50);
		$worksheet->set_column(2, 2, 50);
		$worksheet->set_column(3, 3, 50);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 30);
		$worksheet->set_column(6, 6, 20);
		$worksheet->set_column(5, 7, 20);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ชื่อโครงการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "หน่วยที่จัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "หน่วยงานที่รับผิดชอบ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ผู้อนุมัติ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "หมวดโครงการ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "วันที่อนุมัติ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "เลขที่หนังสือ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "สถานที่อบรม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

	} // function		

	$cmd = " select PROJ_NAME, PROJ_ID_REF, TPJ_BUDGET_YEAR, DEPARTMENT_ID, TPJ_MANAGE_ORG, TPJ_RESPONSE_ORG, TPJ_APP_PER_ID, PG_ID, TPJ_APP_DATE, TPJ_APP_DOC_NO, TPJ_INOUT_TRAIN, PLAN_ID from PER_TRAIN_PROJECT where TPJ_BUDGET_YEAR='$year' order by TPJ_BUDGET_YEAR, PLAN_ID, PROJ_NAME ";
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PROJ_NAME = $data[PROJ_NAME];
		$PROJ_ID_REF = $data[PROJ_ID_REF];
		$TPJ_BUDGET_YEAR = $data[TPJ_BUDGET_YEAR];
//		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$TPJ_MANAGE_ORG = $data[TPJ_MANAGE_ORG];
		$TPJ_RESPONSE_ORG = $data[TPJ_MANAGE_ORG];

		$TPJ_APP_PER_ID = $data[TPJ_APP_PER_ID];
		$cmd2 = " select PER_NAME, PER_SURNAME, PN_NAME
					   from	PER_PERSONAL a, PER_PRENAME b
					  where a.PN_CODE=b.PN_CODE and PER_ID=$TPJ_APP_PER_ID ";
		$db_dpis2->send_cmd($cmd2);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();	
		$TPJ_APP_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];

		$PG_ID = $data[PG_ID];
		$cmd2 = " select PG_ID, PG_NAME from PER_PROJECT_GROUP where PG_ID=$PG_ID and PG_ACTIVE=1 ";
		$db_dpis2->send_cmd($cmd2);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();	
		$PG_NAME = $data2[PG_NAME];

		$TPJ_APP_DATE = $data[TPJ_APP_DATE];
		$TPJ_APP_DOC_NO = $data[TPJ_APP_DOC_NO];
		$TPJ_INOUT_TRAIN = $data[TPJ_INOUT_TRAIN];

		$PLAN_ID = $data[PLAN_ID];
		$cmd2 = " select * from PER_TRAIN_PLAN where PLAN_ID=$PLAN_ID ";
		$db_dpis2->send_cmd($cmd2);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();	
		$PLAN_NAME = $data2[PLAN_NAME];

		$arr_content[$data_count][pj_name]=$PROJ_NAME;
		$arr_content[$data_count][pj_id_ref] = $PROJ_ID_REF;
		$arr_content[$data_count][bg_year] = $TPJ_BUDGET_YEAR;
		$arr_content[$data_count][dept_id] = $DEPARTMENT_ID;
		$arr_content[$data_count][manage_org] = $TPJ_MANAGE_ORG;
		$arr_content[$data_count][respon_org] = $TPJ_RESPONSE_ORG;
		$arr_content[$data_count][per_id] = $TPJ_APP_PER_ID;
		$arr_content[$data_count][per_name] = $TPJ_APP_PER_NAME;
		$arr_content[$data_count][pg_id] = $PG_ID;
		$arr_content[$data_count][pg_name] = $PG_NAME;
		$arr_content[$data_count][app_date] = $TPJ_APP_DATE;
		$arr_content[$data_count][doc_no] = $TPJ_APP_DOC_NO;
		$arr_content[$data_count][io_train] = $TPJ_INOUT_TRAIN;
		$arr_content[$data_count][plan_name] = $PLAN_NAME;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		$PLAN_NAME = "";
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$PROJ_NAME = $arr_content[$data_count][pj_name];
			$PROJ_ID_REF = $arr_content[$data_count][pj_id_ref];
//			$TPJ_BUDGET_YEAR = $arr_content[$data_count][bg_year];
//			$DEPARTMENT_ID = $arr_content[$data_count][dept_id];
			$TPJ_MANAGE_ORG = $arr_content[$data_count][manage_org];
			$TPJ_RESPONSE_ORG = $arr_content[$data_count][respon_org];
//			$TPJ_APP_PER_ID = $arr_content[$data_count][per_id];
			$TPJ_APP_PER_NAME = $arr_content[$data_count][per_name];
//			$PG_ID = $arr_content[$data_count][pg_id];
			$PG_NAME = $arr_content[$data_count][pg_name];
			$TPJ_APP_DATE = $arr_content[$data_count][app_date];
			$TPJ_APP_DOC_NO = $arr_content[$data_count][doc_no];
			$TPJ_INOUT_TRAIN = ($arr_content[$data_count][io_train]=="1"?"อบรมภายใน":"อบรมภายนอก");
			
			if ($PLAN_NAME != $arr_content[$data_count][plan_name]) {								
				$PLAN_NAME = $arr_content[$data_count][plan_name];
				$xlsRow++;
				$worksheet->write($xlsRow, 0, $PLAN_NAME, set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$PROJ_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$TPJ_MANAGE_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$TPJ_RESPONSE_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$TPJ_APP_PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$PG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$TPJ_APP_DATE", set_format("xlsFmtTitle", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$TPJ_APP_DOC_NO", set_format("xlsFmtTitle", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$TPJ_INOUT_TRAIN", set_format("xlsFmtTitle", "B", "C", "TLRB", 0));
		} // end for				
	}else{
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

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"แผนฝึกอบรมประจำปี $year.xls\"");
	header("Content-Disposition: inline; filename=\"แผนฝึกอบรมประจำปี $year.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>