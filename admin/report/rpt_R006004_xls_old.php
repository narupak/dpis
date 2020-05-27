<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "f.PL_NAME";
		$position_no= "b.POS_NO";
		$position_no_name= "b.POS_NO_NAME";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "f.PN_NAME";
		$position_no= "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "f.EP_NAME";
		$position_no= "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "f.TP_NAME";
		$position_no= "b.POT_NO";
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

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;

			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE1";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure == 0) $order_by .= "b.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "b.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "a.ORG_ID";
	}

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($DPISDB=="odbc"){
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-12-31')";
	}elseif($DPISDB=="oci8"){
		$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) < '". ($search_budget_year - 543) ."-12-31')";
	}elseif($DPISDB=="mysql"){
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-12-31')";
	} // end if

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
	$report_title = "บัญชีรายละเอียดการเลื่อนขั้นเงินเดือน $PERSON_TYPE[$search_per_type] ย้อนหลัง 5 ปี||กรม/ส่วนราชการที่เทียบเท่า $DEPARTMENT_NAME";
    $report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0604";

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
		global $heading_name,$NUMBER_DISPLAY;
		global $search_budget_year;
       $aaa =(($NUMBER_DISPLAY==2)?convert2thaidigit(1):1);
	   $bbb =(($NUMBER_DISPLAY==2)?convert2thaidigit(2):2);
		
		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 8);
		$worksheet->set_column(3, 3, 40);
		$worksheet->set_column(4, 4, 10);
		$worksheet->set_column(5, 14, 8);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ปีงบประมาณ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$line = 5;
		for($i=0; $i<5; $i++){
			$worksheet->write_string($xlsRow, $line++,(($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year - $i):$search_budget_year - $i), set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write_string($xlsRow, $line++,(($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year - $i):$search_budget_year - $i), set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		} // end for

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "ปัจจุบัน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$line = 5;
		for($i=0; $i<5; $i++){
			$worksheet->write($xlsRow, $line++, "ครั้งที่ $bbb", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, $line++, "ครั้งที่ $aaa", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		} // end for
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID, $ORG_ID_1;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_1" :	
					if($select_org_structure==0){
						if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID_1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :	
					$ORG_ID_1 = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
//	if ($search_condition) $search_condition = $search_condition." and a.PER_ID < 1000"; else $search_condition = "a.PER_ID < 1000";
	if($DPISDB=="odbc"){
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP $select_type_code
						 from			(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on ($line_join)
											$search_condition
						 order by		$order_by, IIf(IsNull($position_no), 0, CLng($position_no)), LEFT(trim(e.SAH_EFFECTIVEDATE), 10) desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, TO_NUMBER($position_no) as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP $select_type_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_SALARYHIS e, $line_table f
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID and $line_join(+)
											$search_condition
						 order by		$order_by, TO_NUMBER($position_no), SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) desc ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP $select_type_code
						 from			(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on ($line_join)
											$search_condition
						 order by		$order_by, $position_no, e.SAH_EFFECTIVEDATE desc ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if
           if($ORG_NAME=="" || $ORG_NAME=="NULL"  || $ORG_NAME =="-")	$ORG_NAME="[ไม่ระบุ $ORG_TITLE]";
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][name] = $ORG_NAME;
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;

				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						if($ORG_ID_1 != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if
                      if($ORG_NAME_1=="" || $ORG_NAME_1=="NULL"   || $ORG_NAME_1 =="-")	$ORG_NAME_1="[ไม่ระบุ$ORG_TITLE1]";
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG_1";
//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][name] = $ORG_NAME_1;
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
			} // end switch case

			if($rpt_order_index == (count($arr_rpt_order) - 1)){
				if($PER_ID != $data[PER_ID]){
					$data_row++;
							
					$PER_ID = $data[PER_ID];
					$PN_NAME = trim($data[PN_NAME]);
					$PER_NAME = trim($data[PER_NAME]);
					$PER_SURNAME = trim($data[PER_SURNAME]);
					//$POS_NO = $data[POS_NO];
					$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
					$PL_CODE = trim($data[PL_CODE]);
					$PL_NAME = trim($data[PL_NAME]);
					$LEVEL_NO = $data[LEVEL_NO];
					if(trim($type_code)){
						$PT_CODE = trim($data[PT_CODE]);
						$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$PT_NAME = trim($data3[PT_NAME]);
					}
					$PER_SALARY = $data[PER_SALARY];
					
					$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
					$db_dpis3->send_cmd($cmd);
//					$db_dpis->show_error();
					$data3 = $db_dpis3->get_array();
					$LEVEL_NAME=$data3[LEVEL_NAME];
					$POSITION_LEVEL = $data3[POSITION_LEVEL];
					if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
					$arr_content[$data_count][type] = "DETAIL";
					$arr_content[$data_count][order] = $data_row;
//					$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
					$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
					$arr_content[$data_count][pos_no] = $POS_NO;
					$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
					$arr_content[$data_count][salary] = $PER_SALARY;
					for($i=1; $i<=10; $i++){ 
						$arr_content[$data_count]["count_".$i] = 0;
						$arr_content[($data_count + 1)]["count_".$i] = 0;
					} // end for
	
					$data_count++;
						
					$arr_content[$data_count][type] = "DETAIL2";
					$data_count++;
				} // end if

				$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
				$arr_temp = explode("-", $SAH_EFFECTIVEDATE);
				$SAH_YEAR = $arr_temp[0] + 543;
				$SAH_MONTH = $arr_temp[1] + 0;
				$SAH_DATE = $arr_temp[2] + 0;
							
				$MOV_CODE = trim($data[MOV_CODE]);
				$PR_LEVEL = 0;
				if ($BKK_FLAG==1) {
					if ($MOV_CODE == '030' || $MOV_CODE == '052') {
						$PR_LEVEL = 0.5;
				    } elseif ($MOV_CODE == '013' || $MOV_CODE == '032') {
						$PR_LEVEL = 1.0;
					} elseif ($MOV_CODE == '031' || $MOV_CODE == '033') {
						$PR_LEVEL = 1.5;
					} elseif ($MOV_CODE == '014' || $MOV_CODE == '034') {
						$PR_LEVEL = 2.0;
					} elseif ($MOV_CODE == '14' || $MOV_CODE == '25' || $MOV_CODE == '52' || $MOV_CODE == '53' || $MOV_CODE == '044' || $MOV_CODE == '050' || 
						$MOV_CODE == '059' || $MOV_CODE == '20020' || $MOV_CODE == '20021' || $MOV_CODE == '20027') {
						if ($SM_CODE == '2') {
							$PR_LEVEL = 0.5;
						} elseif ($SM_CODE == '3') {
							$PR_LEVEL = 1.0;
						} elseif ($SM_CODE == '4') {
							$PR_LEVEL = 1.5;
						} elseif ($SM_CODE == '5') {
							$PR_LEVEL = 2.0;
						}
					} else {
						$PR_LEVEL = 0;
					}
				} else { 
					if ($MOV_CODE == '21310' || $MOV_CODE == '21351') {
						$PR_LEVEL = 0.5;
					} elseif ($MOV_CODE == '21320' || $MOV_CODE == '21352') {
						$PR_LEVEL = 1.0;
					} elseif ($MOV_CODE == '21330' || $MOV_CODE == '21353') {
						$PR_LEVEL = 1.5;
					} elseif ($MOV_CODE == '21340' || $MOV_CODE == '21354') {
						$PR_LEVEL = 2.0;
					} else {
						$PR_LEVEL = 0;
					}
				} // end if
				$SAH_SALARY = $data[SAH_SALARY];
				$SAH_PERCENT_UP = $data[SAH_PERCENT_UP];
				if ($SAH_PERCENT_UP) $PR_LEVEL = $SAH_PERCENT_UP;
				
				if($SAH_MONTH == 10){
					$arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] += $PR_LEVEL;
					if($SAH_SALARY > $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)]) $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] = $SAH_SALARY;
				}elseif($SAH_MONTH == 4){
					$arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] += $PR_LEVEL;
					if($SAH_SALARY > $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)]) $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] = $SAH_SALARY;
				}
			} // end if
		} // end for
		}
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
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$PER_SALARY = $arr_content[$data_count][salary];
			for($i=1; $i<=10; $i++) ${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
			
			if($REPORT_ORDER == "DETAIL" || $REPORT_ORDER == "DETAIL2"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2,(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION):$POSITION), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				if($REPORT_ORDER == "DETAIL"){
					$worksheet->write_string($xlsRow, 4,(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)):number_format($PER_SALARY)), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					for($i=1; $i<=10; $i++) 
					$worksheet->write_string($xlsRow, ($i + 4), (${"COUNT_".$i}?(($NUMBER_DISPLAY==2)?convert2thaidigit(${"COUNT_".$i}):${"COUNT_".$i}):"-"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				}elseif($REPORT_ORDER == "DETAIL2"){
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					for($i=1; $i<=10; $i++)
					 $worksheet->write_string($xlsRow, ($i + 4), (${"COUNT_".$i}?(($NUMBER_DISPLAY==2)?convert2thaidigit("(".number_format(${"COUNT_".$i}).")"):("(".number_format(${"COUNT_".$i})).")"):""), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				} // end if
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2,(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION):$POSITION), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				for($i=1; $i<=10; $i++) $worksheet->write_string($xlsRow, ($i + 4), "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			} // end if
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
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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