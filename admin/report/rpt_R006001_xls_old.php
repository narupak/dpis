<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "g.PL_NAME";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "g.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "g.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "g.TP_NAME";
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
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type] อีก $search_layer_no ขั้น จะถึงขั้นสูงสุดของเงินเดือน";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0601";

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
		
		$worksheet->set_column(0, 0, 45);
		$worksheet->set_column(1, 1, 45);
		$worksheet->set_column(2, 2, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "หน่วยงาน/ชื่อ - นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID;
				
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
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	if($search_per_type==1 || $search_per_type==3 || $search_per_type==5){	
		if($DPISDB=="odbc"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY,
											d.LAYER_NO, MAX(e.LAYER_NO) as MAX_LAYER_NO
							  from		(
												(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) inner join PER_LAYER d on (a.LEVEL_NO=d.LEVEL_NO and a.PER_SALARY=d.LAYER_SALARY)
														) inner join PER_LAYER e on (a.LEVEL_NO=e.LEVEL_NO)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on ($line_join)
											) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
							 $search_condition
							 group by $select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code, $line_name, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.LAYER_NO
							 having	IIf(IsNull(MAX(e.LAYER_NO)), 0, MAX(e.LAYER_NO)) - IIf(IsNull(d.LAYER_NO), 0, d.LAYER_NO) = $search_layer_no
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY,
											d.LAYER_NO, MAX(e.LAYER_NO) as MAX_LAYER_NO
							  from		PER_PERSONAL a, $position_table b, PER_LAYER d, PER_LAYER e, PER_PRENAME f, $line_table g, PER_TYPE h
							  where	$position_join(+) and a.LEVEL_NO=d.LEVEL_NO and a.PER_SALARY=d.LAYER_SALARY
											and a.LEVEL_NO=e.LEVEL_NO and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PT_CODE=h.PT_CODE(+)
											$search_condition
							 group by $select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code, $line_name, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.LAYER_NO
							 having	(NVL(MAX(e.LAYER_NO),0) - NVL(d.LAYER_NO, 0)) = $search_layer_no
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY,
											d.LAYER_NO, MAX(e.LAYER_NO) as MAX_LAYER_NO
							  from		(
												(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) inner join PER_LAYER d on (a.LEVEL_NO=d.LEVEL_NO and a.PER_SALARY=d.LAYER_SALARY)
														) inner join PER_LAYER e on (a.LEVEL_NO=e.LEVEL_NO)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on ($line_join)
											) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
							 $search_condition
							 group by $select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code, $line_name, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.LAYER_NO
							 having	IIf(IsNull(MAX(e.LAYER_NO)), 0, MAX(e.LAYER_NO)) - IIf(IsNull(d.LAYER_NO), 0, d.LAYER_NO) = $search_layer_no
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		} // end if
	}else{	// 2 || 4
		if($DPISDB=="odbc"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY,
											d.LAYERE_NO, MAX(e.LAYERE_NO) as MAX_LAYER_NO
							  from		(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) inner join PER_LAYEREMP d on (trim(b.PG_CODE_SALARY)=trim(d.PG_CODE) and a.PER_SALARY=d.LAYERE_SALARY)
													) inner join PER_LAYEREMP e on (trim(b.PG_CODE_SALARY)=trim(e.PG_CODE))
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
							 $search_condition
							 group by $select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code, $line_name, a.PER_SALARY, d.LAYERE_NO
							 having	IIf(IsNull(MAX(e.LAYERE_NO)), 0, MAX(e.LAYERE_NO)) - IIf(IsNull(d.LAYERE_NO), 0, d.LAYERE_NO) = $search_layer_no
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY,
											d.LAYERE_NO, MAX(e.LAYERE_NO) as MAX_LAYER_NO
							  from		PER_PERSONAL a, $position_table b, PER_LAYEREMP d, PER_LAYEREMP e, PER_PRENAME f, $line_table g
							  where	$position_join(+) and trim(b.PG_CODE_SALARY)=trim(d.PG_CODE) and a.PER_SALARY=d.LAYERE_SALARY
											and trim(b.PG_CODE_SALARY)=trim(e.PG_CODE) and a.PN_CODE=f.PN_CODE(+) and $line_join(+)
											$search_condition
							 group by $select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code, $line_name, a.PER_SALARY, d.LAYERE_NO
							 having	(NVL(MAX(e.LAYERE_NO),0) - NVL(d.LAYERE_NO, 0)) = $search_layer_no
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY,
											d.LAYERE_NO, MAX(e.LAYERE_NO) as MAX_LAYER_NO
							  from		(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) inner join PER_LAYEREMP d on (trim(b.PG_CODE_SALARY)=trim(d.PG_CODE) and a.PER_SALARY=d.LAYERE_SALARY)
													) inner join PER_LAYEREMP e on (trim(b.PG_CODE_SALARY)=trim(e.PG_CODE))
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
							 $search_condition
							 group by $select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											$line_code, $line_name, a.PER_SALARY, d.LAYERE_NO
							 having	IIf(IsNull(MAX(e.LAYERE_NO)), 0, MAX(e.LAYERE_NO)) - IIf(IsNull(d.LAYERE_NO), 0, d.LAYERE_NO) = $search_layer_no
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		} // end if
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
		
		$data_row++;
		$PER_ID = $data[PER_ID];
		$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PL_NAME = trim($data[PL_NAME]);
		$PER_SALARY = $data[PER_SALARY];

		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5))."$data_row. $PER_NAME";
		$arr_content[$data_count][position] = (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"ระดับ ".level_no_format($LEVEL_NO));
		$arr_content[$data_count][per_salary] = $PER_SALARY;

		$data_count++;
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
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$PER_SALARY = $arr_content[$data_count][per_salary];
						
			if($REPORT_ORDER == "ORG"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION):$POSITION), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, ($PER_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)):number_format($PER_SALARY)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			} // end if
		} // end for				
	}else{
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