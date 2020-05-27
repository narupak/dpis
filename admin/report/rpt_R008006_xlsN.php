<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("../report/rpt_function.php");
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if

	$cmd = "select LEVEL_NO, LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO,LEVEL_NO";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){ 
		$ARR_LEVEL_NO[] = $data[LEVEL_NO];		
		$ARR_LEVEL_SHORTNAME[] = $data[LEVEL_SHORTNAME];
	}

	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
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

	$search_per_status[] = 1;

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(d.PUN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(d.PUN_STARTDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(d.PUN_STARTDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(d.PUN_STARTDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(d.PUN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(d.PUN_STARTDATE), 10) < '".($search_budget_year - 543)."-10-01')";

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
	$report_title = "$DEPARTMENT_NAME||สถิติฐานความผิดทางวินัย จำแนกตามระดับตำแหน่ง||ในปีงบประมาณ $search_budget_year";
	$report_code = "R0806";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$heading_width[0] = "147";
	$heading_width[1] = "10";
	$heading_width[2] = "15";
	$heading_width[3] = "15";

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $ARR_LEVEL_NO,$ARR_LEVEL_SHORTNAME;
		
		$worksheet->set_column(0, 0, 70);
		$worksheet->set_column(1, count($ARR_LEVEL_NO), 5);
		$worksheet->set_column(count($ARR_LEVEL_NO)+1, count($ARR_LEVEL_NO)+1, 8);
		$worksheet->set_column(count($ARR_LEVEL_NO)+2, count($ARR_LEVEL_NO)+2, 8);

		$worksheet->write($xlsRow, 0, "$heading_name", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		for($i=2; $i<=count($ARR_LEVEL_NO); $i++){
			$worksheet->write($xlsRow, $i, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		} // loop for
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+1, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
			$worksheet->write($xlsRow, $i+1, "$tmp_level_shortname", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		} // loop for
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+1, "คน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+2, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function count_person($cr_code, $level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type, $select_org_structure, $position_table, $position_join;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);

		if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																inner join $position_table b on ($position_join) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e
								 where		$position_join and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) and d.CRD_CODE=e.CRD_CODE(+)
													and trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																inner join $position_table b on ($position_join) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
								 where		trim(a.LEVEL_NO) = '$level_no' and trim(e.CR_CODE)='$cr_code'
													$search_condition
								 group by	a.PER_ID ";
			} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
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
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																inner join $position_table b on ($position_join) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PUNISHMENT d, PER_CRIME_DTL e, PER_CRIME f
							 where		$position_join and b.ORG_ID=c.ORG_ID and a.PER_ID=d.PER_ID(+) 
							 					and d.CRD_CODE=e.CRD_CODE(+) and e.CR_CODE=f.CR_CODE(+)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list, e.CR_CODE, f.CR_NAME
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																inner join $position_table b on ($position_join) 
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PUNISHMENT d on (a.PER_ID=d.PER_ID)
													) left join PER_CRIME_DTL e on (d.CRD_CODE=e.CRD_CODE)
												) left join PER_CRIME f on (e.CR_CODE=f.CR_CODE)
												$search_condition
							 order by		$order_by, e.CR_CODE, f.CR_NAME ";
		} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	unset($LEVEL_TOTAL);
	$GRAND_TOTAL = 0;
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

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
						
						if($rpt_order_index == (count($arr_rpt_order) - 1)){
							if($CR_CODE != trim($data[CR_CODE])){
								$CR_CODE = trim($data[CR_CODE]);
								$CR_NAME = trim($data[CR_NAME]);
								
								$arr_content[$data_count][type] = "CONTENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $CR_NAME;
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person("$CR_CODE",$tmp_level_no, $search_condition, $addition_condition);
									if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
								} // end for
								$data_count++;
							} // end if
						} // end if
					} // end if
				break;		
			} // end switch case
		} // end for
	} // end while
	
	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=1; $j<=count($ARR_LEVEL_NO)+2; $j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($i=1; $i<=4; $i++)$worksheet->write($xlsRow, $i, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=5; $j<=count($ARR_LEVEL_NO)+2; $j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			unset($COUNT_LEVEL);
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				$COUNT_LEVEL[$tmp_level_no] = $arr_content[$data_count]["level_".$tmp_level_no];
			}
			$COUNT_TOTAL = array_sum($COUNT_LEVEL);
			
			if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$worksheet->write($xlsRow, $i+1, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				}
				$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+1, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+2, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}else{
				if($REPORT_ORDER == "ORG"){
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
						$tmp_level_no = $ARR_LEVEL_NO[$i];
						$worksheet->write($xlsRow, $i+1, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					}	
					$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+1, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+2, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				}elseif($REPORT_ORDER == "CONTENT"){
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
						$tmp_level_no = $ARR_LEVEL_NO[$i];
						$worksheet->write($xlsRow, $i+1, ($COUNT_LEVEL[$tmp_level_no]?number_format($COUNT_LEVEL[$tmp_level_no]):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					}
					$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+1, ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+2, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				} // end if
			} // end if
		} // end for
				
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$worksheet->write($xlsRow, $i+1, ($LEVEL_TOTAL[$tmp_level_no]?number_format($LEVEL_TOTAL[$tmp_level_no]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		}
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+1, ($GRAND_TOTAL?number_format($GRAND_TOTAL):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, count($ARR_LEVEL_NO)+2, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($i=1; $i<count($ARR_LEVEL_NO)+2; $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
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