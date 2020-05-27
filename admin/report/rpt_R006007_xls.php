<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$search_per_type = 1;
	$search_per_status[] = 1;
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "บัญชีสรุปการใช้เงินเลื่อนขั้นเงินเดือนข้าราชการ||สำหรับการเลื่อนขั้นเงินเดือน วันที่ 1 เมษายน และวันที่ 1 ตุลาคม ". ($search_budget_year - 1) ."||$DEPARTMENT_NAME $MINISTRY_NAME||ประจำปีงบประมาณ พ.ศ.$search_budget_year";
	$report_code = "R0607";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($search_level_min, $search_level_max){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $search_budget_year;
		
		$worksheet->set_column(0, 0, 50);
		$worksheet->set_column(1, 1, 12);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 12);
		$worksheet->set_column(5, 5, 40);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "C", "", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "C", "", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "", 0));
		$worksheet->write($xlsRow, 5, "กลุ่มข้าราชการระดับ $search_level_min - $search_level_max", set_format("xlsFmtTableDetail", "B", "C", "", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "วงเงินเลื่อนขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "รวมเงินเลื่อน 0.5 ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 3, "ค่าตอบแทน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 4, "รวมเงินเลื่อนขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ส่วนราชการ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "ร้อยละ 6", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "1 ขั้น 1.5 ขั้น", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "2 % และ 4 %", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "ทั้งสิ้น", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "(เม.ย./ต.ค.)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "(ต.ค.)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	$cmd = " select PER_ID from PER_SALPROMOTE where SALQ_YEAR='$search_budget_year' and SALP_YN=1 ";
	$count_data = $db_dpis->send_cmd($cmd);
	
	$data_count = 0;
	for($i=0; $i<4; $i++){
		if($i == 0 || ($i % 2) == 0){
			$arr_content[$data_count][type] = "HEADER";
			$arr_content[$data_count][name] = "ราชการบริหารส่วนกลาง";
			
			if($i == 0) $arr_content[$data_count][remark_2] = $remark_1?"$remark_1":"หมายเหตุ 1-8";
			else $arr_content[$data_count][remark_2] = $remark_2?"$remark_2":"หมายเหตุ 9-11";
			
			$data_count++;
		}else{
			if($i==1){
				$search_level_min = 1;
				$search_level_max = 8;
			}elseif($i==3){
				$search_level_min = 9;
				$search_level_max = 11;
			} // end if
					
			if($DPISDB=="odbc"){ 

				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-09-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and (b.SALP_LEVEL=0.5 or b.SALP_LEVEL=1 or b.SALP_LEVEL=1.5) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary_1 = $data[PROMOTED_SALARY] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and (b.SALP_PERCENT=2 or b.SALP_PERCENT=4) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary_2 = $data[PROMOTED_SALARY] + 0;
				
				$total_promoted_salary = $promoted_salary_1 + $promoted_salary_2;
	
			}elseif($DPISDB=="oci8"){

				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and SUBSTR(trim(PER_STARTDATE), 1, 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and (b.SALP_LEVEL=0.5 or b.SALP_LEVEL=1 or b.SALP_LEVEL=1.5) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_salary_1 = $data[PROMOTED_SALARY] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and (b.SALP_PERCENT=2 and b.SALP_PERCENT=4) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_salary_2 = $data[PROMOTED_SALARY] + 0;
				
				$total_promoted_salary = $promoted_salary_1 + $promoted_salary_2;

			}elseif($DPISDB=="mysql"){

				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-09-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and (b.SALP_LEVEL=0.5 or b.SALP_LEVEL=1 or b.SALP_LEVEL=1.5) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary_1 = $data[PROMOTED_SALARY] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and (b.SALP_PERCENT=2 or b.SALP_PERCENT=4) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary_2 = $data[PROMOTED_SALARY] + 0;
				
				$total_promoted_salary = $promoted_salary_1 + $promoted_salary_2;
	
			} // end if


			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "$DEPARTMENT_NAME";
			$arr_content[$data_count][percent_salary] = number_format($percent_salary, 2);
			$arr_content[$data_count][promoted_salary_1] = number_format($promoted_salary_1, 2);
			$arr_content[$data_count][promoted_salary_2] = number_format($promoted_salary_2, 2);
			$arr_content[$data_count][total_promoted_salary] = number_format($total_promoted_salary, 2);
			$arr_content[$data_count][remark_1] = "ได้ตรวจสอบถูกต้องแล้ว";
			
			$data_count++;
		} // end if
	} // end for	
	
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

//		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			if(($data_count % 2) == 0){
				if($data_count > 0){ 
					for($i=0; $i<3; $i++){
						$xlsRow++;
						$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
						$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
					} // end if

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "( $confirm_name )", set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "ตำแหน่ง  $confirm_position", set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "วันที่                         ตุลาคม ". ($search_budget_year - 1), set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "กรมบัญชีกลางตรวจสอบแล้ว", set_format("xlsFmtTableDetail", "", "C", "", 0));

					for($i=0; $i<3; $i++){
						$xlsRow++;
						$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
						$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
					} // end if

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$REMARK_2", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "( ............................................................ )", set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "ตำแหน่ง ............................................................ ", set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "วันที่ ............................................. ", set_format("xlsFmtTableDetail", "", "C", "", 0));

					for($i=0; $i<3; $i++){
						$xlsRow++;
						$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
						$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
						$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
					} // end if
				} // end if

				if($data_count == 0) print_header(1, 8);
				elseif($data_count == 2) print_header(9, 11);
			} // end if

			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$PERCENT_SALARY = $arr_content[$data_count][percent_salary];
			$PROMOTED_SALARY_1 = $arr_content[$data_count][promoted_salary_1];
			$PROMOTED_SALARY_2 = $arr_content[$data_count][promoted_salary_2];
			$TOTAL_PROMOTED_SALARY = $arr_content[$data_count][total_promoted_salary];
			$REMARK_1 = $arr_content[$data_count][remark_1];
			if($REPORT_ORDER == "HEADER") $REMARK_2 = $arr_content[$data_count][remark_2];

			if($REPORT_ORDER == "DETAIL"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PERCENT_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$PROMOTED_SALARY_1", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$PROMOTED_SALARY_2", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$TOTAL_PROMOTED_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$REMARK_1", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
			} // end if
		} // end for				

		if($data_count > 0){ 
			for($i=0; $i<3; $i++){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			} // end if

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "( $confirm_name )", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "ตำแหน่ง  $confirm_position", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "วันที่                         ตุลาคม ". ($search_budget_year - 1), set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "กรมบัญชีกลางตรวจสอบแล้ว", set_format("xlsFmtTableDetail", "", "C", "", 0));

			for($i=0; $i<3; $i++){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			} // end if

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$REMARK_2", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "( ............................................................ )", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "ตำแหน่ง ............................................................ ", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "วันที่ ............................................. ", set_format("xlsFmtTableDetail", "", "C", "", 0));

			for($i=0; $i<3; $i++){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			} // end if
		} // end if
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
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>