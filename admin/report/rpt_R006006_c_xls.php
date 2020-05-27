<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$cmd = " select 	PV_CODE
					 from 		PER_ORG
					 where	ORG_ID=$DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PV_CODE = $data[PV_CODE];
	if($PV_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_PROVINCE = $data[PV_NAME];
	} // end if	
	
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
	$search_per_status = 1;
	
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
	$report_title = "บัญชีสรุปจำนวนข้าราชการ||สำหรับการเลื่อนขั้นเงินเดือนกรณีพิเศษ 1 ตุลาคม ". ($search_budget_year - 1) ."||$DEPARTMENT_NAME $MINISTRY_NAME||ประจำปีงบประมาณ $search_budget_year||หน่วยงานผู้เบิก $DEPARTMENT_NAME  จังหวัด$DEPARTMENT_PROVINCE";
	$report_code = "R0606";

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
		
		$worksheet->set_column(0, 0, 25);
		$worksheet->set_column(1, 1, 8);
		$worksheet->set_column(2, 2, 27);
		$worksheet->set_column(3, 3, 40);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "กลุ่มข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "จำนวนข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "1 ต.ค.", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ตัวอักษร", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	$cmd = " select PER_ID from PER_SALPROMOTE where SALQ_YEAR='$search_budget_year' and SALP_YN=1 and SALQ_TYPE=2 ";
	$count_data = $db_dpis->send_cmd($cmd);
	
	$data_count = 0;
	for($i=0; $i<8; $i++){
		if($i < 4){
			$search_level_min = 1;
			$search_level_max = 8;
		}else{
			$search_level_min = 9;
			$search_level_max = 11;
		} // end if

		if($i == 0 || ($i % 4) == 0){
			$arr_content[$data_count][type] = "HEADER";
			$arr_content[$data_count][name] = "กลุ่ม $search_level_min - $search_level_max";
			
			$data_count++;
		}elseif(($i % 4) == 1){
			if($DPISDB=="odbc"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="oci8"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
			} // end if

			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "1 ขั้น";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			
			if($i < 4) $arr_content[$data_count][remark] = $remark_1?"$remark_1":"(1-8)";
			else $arr_content[$data_count][remark] = $remark_2?"$remark_2":"(9-11)";
			
			$data_count++;
		}elseif(($i % 4) == 2){
			if($DPISDB=="odbc"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="oci8"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
			} // end if

			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "1.5 ขั้น";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			
			$data_count++;
		}elseif(($i % 4) == 3){
			if($DPISDB=="odbc"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="oci8"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
			} // end if

			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "ค่าตอบแทน 4 %";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			
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
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_PERSON = $arr_content[$data_count][count_person];
			$COUNT_SPEECH = $arr_content[$data_count][count_speech];
			$REMARK = $arr_content[$data_count][remark];
			
			if($REPORT_ORDER == "DETAIL"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$COUNT_PERSON", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$COUNT_SPEECH", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				if(($data_count % 4) == 1) $worksheet->write_string($xlsRow, 3, "$REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				else $worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
			} // end if
		} // end for				

		if($data_count > 0){ 
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "ได้ตรวจสอบถูกต้องแล้ว", set_format("xlsFmtTableDetail", "", "C", "", 0));

			for($i=0; $i<3; $i++){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			} // end for
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "( $confirm_name )", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "ตำแหน่ง  $confirm_position", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "วันที่                         ตุลาคม ". ($search_budget_year - 1), set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "กรมบัญชีกลางตรวจสอบแล้ว", set_format("xlsFmtTableDetail", "", "C", "", 0));

			for($i=0; $i<3; $i++){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			} // end for

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "( ............................................................ )", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "ตำแหน่ง ............................................................ ", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "วันที่ ............................................. ", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
		} // end if
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