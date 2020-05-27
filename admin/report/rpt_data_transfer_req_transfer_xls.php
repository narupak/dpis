<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_per_type = (trim($search_per_type))? $search_per_type : 1;
	if($DEPARTMENT_ID) $arr_search_condition[] = "(DEPARTMENT_ID = $DEPARTMENT_ID)";	
	if($ORG_ID) $arr_search_condition[] = "(ORG_ID_1=$ORG_ID or ORG_ID_2=$ORG_ID or ORG_ID_3=$ORG_ID)";
  	
	if(trim($search_name)) 		$arr_search_condition[] = "(TR_NAME like '%$search_name%')";
  	if(trim($search_cardno)) 	$arr_search_condition[] = "(TR_CARDNO like '$search_cardno%')";
	if(trim($PER_TYPE_SEARCH)) 	$arr_search_condition[] = "(TR_PER_TYPE = $PER_TYPE_SEARCH)";
  	if(trim($EN_CODE)) 			$arr_search_condition[] = "(EN_CODE='$EN_CODE')";
  	if(trim($EM_CODE)) 			$arr_search_condition[] = "(EM_CODE='$EM_CODE')";	
  	if(trim($INS_CODE)) 			$arr_search_condition[] = "(INS_CODE='$INS_CODE')";		
  	if(trim($TR_POSITION))		$arr_search_condition[] = "(TR_POSITION like '%$TR_POSITION%')";
  	if(trim($LEVEL_START_N) || trim($LEVEL_END_N)) 			
		$arr_search_condition[] = "(TR_LEVEL >= '$LEVEL_START_N' and TR_LEVEL <= '$LEVEL_END_N')";	
	if(trim($PL_PN_CODE) && trim($search_per_type) == 1)
		$arr_search_condition[] = "(PL_CODE_1='$PL_PN_CODE' or PL_CODE_2='$PL_PN_CODE' or PL_CODE_3='$PL_PN_CODE')";
	elseif(trim($PL_PN_CODE) && trim($search_per_type) == 2)
		$arr_search_condition[] = "(PN_CODE_1='$PL_PN_CODE' or PN_CODE_2='$PL_PN_CODE' or PN_CODE_3='$PL_PN_CODE')";	
  	if(trim($LEVEL_START_F) || trim($LEVEL_END_F)) 
		$arr_search_condition[] = "((LEVEL_NO_1 >= '$LEVEL_START_F' and LEVEL_NO_1 <= '$LEVEL_END_F') or (LEVEL_NO_2 >= '$LEVEL_START_F' and LEVEL_NO_2 <= '$LEVEL_END_F') or (LEVEL_NO_3 >= '$LEVEL_START_F' and LEVEL_NO_3 <= '$LEVEL_END_F'))";
	if(trim($TR_DATE_START) || trim($TR_DATE_END)) {
		$temp_start =  save_date($TR_DATE_START);
		$temp_end =  save_date($TR_DATE_END);
		$arr_search_condition[] = "(TR_DATE >= '$temp_start' or TR_DATE <= '$temp_end')";
	}
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	$company_name = "";
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
		$worksheet->set_column(1, 1, 35);
		$worksheet->set_column(2, 2, 35);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 35);
		$worksheet->set_column(5, 5, 15);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "อัตราเงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		
		
	$cmd = " select 		TR_ID, TR_NAME, TR_POSITION, TR_LEVEL, TR_ORG3, TR_ORG2, TR_SALARY
					 from 			PER_TRANSFER_REQ
					 where 		TR_TYPE = 1
										$search_condition	
					 order by 	TR_DATE";
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$temp_TR_ID = trim($data[TR_ID]);
		$TR_NAME = $data[TR_NAME];
		$TR_POSITION = $data[TR_POSITION];
		
		$LEVEL_NO = $data[TR_LEVEL];
		
		$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		
		$data2 = $db_dpis2->get_array();
		$TR_LEVEL = $data2[LEVEL_NAME];
		
		$TR_ORG3 = $data[TR_ORG3];
		$TR_SALARY = number_format($data[TR_SALARY], 2, '.', ',');
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][tr_name] = $TR_NAME;
		$arr_content[$data_count][tr_position] = $TR_POSITION;
		$arr_content[$data_count][tr_level] = level_no_format($TR_LEVEL);
		$arr_content[$data_count][tr_org3] = $TR_ORG3;
		$arr_content[$data_count][tr_salary] = $TR_SALARY;
				
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
			$TR_NAME = $arr_content[$data_count][tr_name];
			$TR_POSITION = $arr_content[$data_count][tr_position];
			$TR_LEVEL = $arr_content[$data_count][tr_level];
			$TR_ORG3 = $arr_content[$data_count][tr_org3];
			$TR_SALARY = $arr_content[$data_count][tr_salary];
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0,(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$TR_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$TR_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$TR_LEVEL", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$TR_ORG3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, (($NUMBER_DISPLAY==2)?convert2thaidigit($TR_SALARY):$TR_SALARY), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
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
	header("Content-Type: application/x-msexcel; name=\"สอบถามข้อมูลข้าราชการ/ลูกจ้างสมควรสับเปลี่ยน.xls\"");
	header("Content-Disposition: inline; filename=\"สอบถามข้อมูลข้าราชการ/ลูกจ้างสมควรสับเปลี่ยน.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>