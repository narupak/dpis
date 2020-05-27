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
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "a.PL_CODE";
		$line_join = "a.PL_CODE=e.PL_CODE";
		$line_name = "e.PL_NAME";
		$position_no_name = "a.POS_NO_NAME";
		$position_no = "a.POS_NO";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);
		 $line_title = " สายงาน";
		 $type_code = "a.PT_CODE";
		 $select_type_code = ", a.PT_CODE";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "a.PN_CODE";
		$line_join = "a.PN_CODE=e.PN_CODE";
		$line_name = "e.PN_NAME";
		$position_no_name = "a.POEM_NO_NAME";
		$position_no = "a.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "a.EP_CODE";
		$line_join = "a.EP_CODE=e.EP_CODE";
		$line_name = "e.EP_NAME";
		$position_no_name = "a.POEMS_NO_NAME";
		$position_no = "a.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "a.TP_CODE";
		$line_join = "a.TP_CODE=e.TP_CODE";
		$line_name = "e.TP_NAME";
		$position_no_name = "a.POT_NO_NAME";
		$position_no = "a.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);
		$line_title = " ชื่อตำแหน่ง";
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

	$search_per_type = 1;
	$search_per_status[] = 1;

	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	
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
	$report_title = "บัญชีถือจ่ายเงินเดือน$PERSON_TYPE[$search_per_type]||$DEPARTMENT_NAME||ประจำปีงบประมาณ ". ($search_budget_year?(($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year):"0");
	$report_code = "R0615";

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
		
		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 7);
		$worksheet->set_column(4, 4, 7);
		$worksheet->set_column(5, 5, 7);
		$worksheet->set_column(6, 6, 25);
		$worksheet->set_column(7, 7, 7);
		$worksheet->set_column(8, 8, 7);
		$worksheet->set_column(9, 9, 8);
		$worksheet->set_column(10, 10, 7);
		$worksheet->set_column(11, 11, 7);
		$worksheet->set_column(12, 12, 7);
		$worksheet->set_column(13, 13, 7);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "เลขประจำตัว", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "สังกัด/ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ถือจ่ายปีที่แล้ว", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "ถือจ่ายปีนี้", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "ปรับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "จำนวนเงิน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 12, "ปรับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 9, "ลด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "เลื่อนขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 11, "ปรับวุฒิ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "ตั้งใหม่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
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
	
	if($DPISDB=="odbc"){
		$cmd = " select			$select_list, b.PER_ID, d.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_OFFNO, b.PER_CARDNO, b.PER_SALARY, $position_no_name as POS_NO_NAME, 
											IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, $line_code as PL_CODE, $line_name as PL_NAME, b.LEVEL_NO $select_type_code
						 from			(
												(
													(
														$position_table a 
														left join PER_PERSONAL b on ($position_join) 
													) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_PRENAME d on (b.PN_CODE=d.PN_CODE)
											) left join $line_table e on ($line_join)
											$search_condition
						 order by		$order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			$select_list, b.PER_ID, d.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_OFFNO, b.PER_CARDNO, b.PER_SALARY, $position_no_name as POS_NO_NAME, 
											TO_NUMBER($position_no) as POS_NO, $line_code as PL_CODE, $line_name as PL_NAME, b.LEVEL_NO $select_type_code
						 from			$position_table a, PER_PERSONAL b, PER_ORG c, PER_PRENAME d, $line_table e
						 where		$position_join(+) and a.ORG_ID=c.ORG_ID(+)
											and b.PN_CODE=d.PN_CODE(+) and $line_join(+)
											$search_condition
						 order by		$order_by, $position_no_name, TO_NUMBER($position_no) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			$select_list, b.PER_ID, d.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_OFFNO, b.PER_CARDNO, b.PER_SALARY, $position_no_name as POS_NO_NAME, 
											IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, $line_code as PL_CODE, $line_name as PL_NAME, b.LEVEL_NO $select_type_code
						 from			(
												(
													(
														$position_table a 
														left join PER_PERSONAL b on ($position_join) 
													) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_PRENAME d on (b.PN_CODE=d.PN_CODE)
											) left join $line_table e on ($line_join)
											$search_condition
						 order by		$order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)) ";
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

						$addition_condition = generate_condition($rpt_order_index);

						$data_row++;

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][order] = $data_row;
						$arr_content[$data_count][name] = $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if

					if($rpt_order_index == (count($arr_rpt_order) - 1)){
						$data_row++;
						
						$PER_ID = $data[PER_ID];
						$PN_NAME = trim($data[PN_NAME]);
						$PER_NAME = trim($data[PER_NAME]);
						$PER_SURNAME = trim($data[PER_SURNAME]);
						$PER_OFFNO = trim($data[PER_OFFNO]);
						if (!$PER_OFFNO) $PER_OFFNO = trim($data[PER_CARDNO]);
						$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);
						$LEVEL_NO = $data[LEVEL_NO];
						$PER_SALARY = $data[PER_SALARY];
						$PROMOTE_SALARY = $PER_SALARY - $PREV_PER_SALARY;
						if(trim($type_code)){
							$PT_CODE = trim($data[PT_CODE]);
							$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PT_NAME = trim($data3[PT_NAME]);	
						}
						
						$cmd=" select LAYER_NO from PER_LAYER where LAYER_TYPE=1 and LEVEL_NO='$LEVEL_NO' and LAYER_SALARY=$PER_SALARY ";
						$db_dpis3->send_cmd($cmd);
						$data_name=$db_dpis3->get_array();
						$LAYER_NO=$data_name[LAYER_NO];

						//หาอัตราเงินเดือนถือจ่ายปีที่แล้ว
						if($DPISDB=="odbc"){
							$cmd = "select PER_ID, LEFT(SAH_ENDDATE,10), LEVEL_NO, SAH_SALARY  
											from PER_SALARYHIS  
											where PER_ID=$PER_ID and LEFT(SAH_ENDDATE,10)<='".($search_budget_year-543-1)."-09-30'  
											order by LEFT(SAH_ENDDATE,10) DESC, LEVEL_NO DESC";
						}elseif($DPISDB=="oci8"){
							$cmd = "select PER_ID, SUBSTR(SAH_ENDDATE,1,10), LEVEL_NO, SAH_SALARY  
											from PER_SALARYHIS 
											where PER_ID=$PER_ID and SUBSTR(SAH_ENDDATE,1,10)<='".($search_budget_year-543-1)."-09-30'  
											order by SUBSTR(SAH_ENDDATE,1,10) DESC, LEVEL_NO DESC";
						}elseif($DPISDB=="mysql"){
							$cmd = "select PER_ID, LEFT(SAH_ENDDATE,10), LEVEL_NO, SAH_SALARY  
											from PER_SALARYHIS 
											where PER_ID=$PER_ID and LEFT(SAH_ENDDATE,10)<='".($search_budget_year-543-1)."-09-30'  
											order by LEFT(SAH_ENDDATE,10) DESC, LEVEL_NO DESC";
						}
						//echo "<br>$cmd<br>";
						$db_dpis3->send_cmd($cmd);
						$data_name=$db_dpis3->get_array();
						$PER_SALARY_OLD=$data_name[SAH_SALARY];	
						$LEVEL_NO_OLD=$data_name[LEVEL_NO];

						$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO_OLD'";
						$db_dpis3->send_cmd($cmd);
						$data_name=$db_dpis3->get_array();
						$LEVEL_NAME_OLD=$data_name[LEVEL_NAME];
						$POSITION_LEVEL_OLD = $data_name[POSITION_LEVEL];
						if (!$POSITION_LEVEL_OLD) $POSITION_LEVEL_OLD = $LEVEL_NAME_OLD;
					
						$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
						$db_dpis3->send_cmd($cmd);
						$data_name=$db_dpis3->get_array();
						$LEVEL_NAME=$data_name[LEVEL_NAME];
						$POSITION_LEVEL = $data_name[POSITION_LEVEL];
						if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
	
						$arr_content[$data_count][type] = "DETAIL";
						$arr_content[$data_count][name] = "$PN_NAME" . "$PER_NAME $PER_SURNAME";
						$arr_content[$data_count][per_offno] = $PER_OFFNO;
						$arr_content[$data_count][pos_no] = $POS_NO;
						$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
						$arr_content[$data_count][level_no_old] = $LEVEL_NAME_OLD;
						$arr_content[$data_count][per_salary_old] = $PER_SALARY_OLD;
						$arr_content[$data_count][level_no] = $LEVEL_NAME;
						$arr_content[$data_count][per_salary] = $PER_SALARY;
						$arr_content[$data_count][prev_level_no] = level_no_format($PREV_LEVEL_NO);
						$arr_content[$data_count][prev_per_salary] = $PREV_PER_SALARY;
						$arr_content[$data_count][promote_salary] = $PROMOTE_SALARY;
						$arr_content[$data_count][layer_no] = $LAYER_NO;

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for		
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
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$PER_OFFNO = $arr_content[$data_count][per_offno];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL_NO_OLD = $arr_content[$data_count][level_no_old];
			$PER_SALARY_OLD = $arr_content[$data_count][per_salary_old];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$PREV_LEVEL_NO = $arr_content[$data_count][prev_level_no];
			$PREV_PER_SALARY = $arr_content[$data_count][prev_per_salary];
			$PROMOTE_SALARY = $arr_content[$data_count][promote_salary];
//			$LAYER_NO = $arr_content[$data_count][layer_no];
			$LAYER_NO = "";
			
			if($REPORT_ORDER == "DETAIL"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_OFFNO):$PER_OFFNO), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION):$POSITION), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NO_OLD):$LEVEL_NO_OLD), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 5, ($PER_SALARY_OLD?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY_OLD)): number_format($PER_SALARY_OLD)):(($REPORT_ORDER=="DETAIL")?"-":"")), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 6, (($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NO):$LEVEL_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, ($LAYER_NO?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($LAYER_NO)): number_format($LAYER_NO)):(($REPORT_ORDER=="DETAIL")?"-":"")), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 8, ($PER_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)): number_format($PER_SALARY)):(($REPORT_ORDER=="DETAIL")?"-":"")), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 10, ($PROMOTE_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PROMOTE_SALARY)): number_format($PROMOTE_SALARY)):(($REPORT_ORDER=="DETAIL")?"-":"")), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
			}
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