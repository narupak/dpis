<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	  for ($i=0;$i<count($EDU_TYPE);$i++) {
	  if($search_edu) { $search_edu.= ' or '; }
	  $search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; } 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(trim($RPTORD_LIST)) $arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("EDUCLEVEL", "ORG");

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "g.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" : 
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break; 
			case "EDUCLEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "f.EL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "f.EL_CODE";

				$heading_name .= " $EL_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		 if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		else if($select_org_structure==1)  $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	}
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	}

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";	
	$arr_search_condition[] = "(trim(d.CT_CODE_EDU) <> '140')";
	if($search_el_code) $arr_search_condition[] = "(trim(f.EL_CODE)=trim('$search_el_code'))";

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
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงาน$PERSON_TYPE[$search_per_type]ที่สำเร็จการศึกษาในต่างประเทศ";
	$report_title .= trim($search_el_code)?"ระดับ$search_el_name":"";
	$report_title .= "||จำแนกตาม". trim($heading_name);
	$report_code = "R0208";

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
		
		$worksheet->set_column(0, 0, 50);
		$worksheet->set_column(1, 11, 6);
		$worksheet->set_column(12, 12, 8);
		$worksheet->set_column(13, 13, 10);
		$worksheet->set_column(14, 14, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "$heading_name", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 12, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "จำนวน ขรก.", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 14, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, "$i", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "ทั้งหมด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	function count_person($level_no, $PER_GENDER, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_search_condition, $search_el_code, $search_per_type, $search_edu, $select_org_structure;
		
//		echo "addition :: $addition_condition<br>";
		if(!$level_no){
			$search_condition = "";
			for($i=0; $i<count($arr_search_condition); $i++){
				if(strpos($arr_search_condition[$i], "trim(f.EL_CODE)")===false && strpos($arr_search_condition[$i], "trim(d.CT_CODE_EDU)")===false){
					if($search_condition) $search_condition .= " and ";
					$search_condition .= $arr_search_condition[$i];
				} // end if
			} // end for
			if(trim($search_condition)) $search_condition = " where ". $search_condition;
		} // end if

		if($level_no){ 
			$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
			if($DPISDB=="odbc") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="oci8") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="mysql") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
		} // end if

		if($PER_GENDER){ 
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$search_condition = " where a.PER_GENDER=$PER_GENDER " . $search_condition;
		} // end if
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) left join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
							 $search_condition and ($search_edu)
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		$position_join(+) and and b.ORG_ID=c.ORG_ID(+)
												and a.PER_ID=d.PER_ID(+) and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE(+)
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) left join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
							 $search_condition and ($search_edu)
							 group by	a.PER_ID ";
		} // end if
		if($select_org_structure==1){ 
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
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $EL_CODE, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID) $arr_addition_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
					else $arr_addition_condition[] = "(g.ORG_ID_REF = 0 or g.ORG_ID_REF is null)";				
					break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
					else $arr_addition_condition[] = "(a.DEPARTMENT_ID = 0 or a.DEPARTMENT_ID is null)";				
					break;
				case "EDUCLEVEL" :
					if($EL_CODE) $arr_addition_condition[] = "(trim(f.EL_CODE) = '$EL_CODE')";
					else $arr_addition_condition[] = "(trim(f.EL_CODE) = '' or f.EL_CODE is null)";
					break;
				case "ORG" :	
					if($ORG_ID){
					 	if($select_org_structure==0){ 
							$arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
							else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
							else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
						} 
					}
				break;
			} // end switch case
		} // end for
		
//		echo "<pre>"; print_r($arr_addition_condition); echo "</pre>";

		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $EL_CODE, $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					$MINISTRY_ID = -1;
					break;
				case "DEPARTMENT" : 
					$DEPARTMENT_ID = -1;
					break;
				case "EDUCLEVEL" :
					$EL_CODE = -1;
					break;
				case "ORG" :	
					$ORG_ID = -1;
					break;
			} // end switch case
		} // end for
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													(
														(	
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition and ($search_edu)
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f, PER_ORG g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE and a.DEPARTMENT_ID=g.ORG_ID
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													(
														(	
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition and ($search_edu)
						 order by		$order_by ";
	}
	if($select_org_structure==1){ 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	unset($LEVEL_TOTAL);
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){		
				case "EDUCLEVEL" :
					if($EL_CODE != trim($data[EL_CODE])){
						$EL_CODE = trim($data[EL_CODE]);
						if($EL_CODE == ""){
							$EL_NAME = "[ไม่ระบุ$EL_TITLE]";
						}else{
							$cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EL_NAME = $data2[EL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCLEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EL_NAME;
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุ$ORG_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1){ $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						$data_count++;
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . "ชาย";
						for($i=1; $i<=11; $i++){
							$arr_content[$data_count]["level_". $i] = count_person($i, 1, $search_condition, $addition_condition);
//							$LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_". $i];
						} // end for
						if($ORG_ID == ""){
							if($select_org_structure==0){ 
								$arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(b.ORG_ID = 0 or b.ORG_ID is null)");
							}else if($select_org_structure==1){ 
								$arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(a.ORG_ID = 0 or a.ORG_ID is null)");
							}
						}else{
							if($select_org_structure==0){ 
								$arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(b.ORG_ID=$ORG_ID)");
							}else if($select_org_structure==1){ 
								$arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(a.ORG_ID=$ORG_ID)");
							}
						}
//						$GRAND_ALL += $arr_content[$data_count][count_all];

						$data_count++;
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . "หญิง";
						for($i=1; $i<=11; $i++){
							$arr_content[$data_count]["level_". $i] = count_person($i, 2, $search_condition, $addition_condition);
//							$LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_". $i];
						} // end for
						if($ORG_ID == "") $arr_content[$data_count][count_all] = count_person(0, 2, $search_condition, "(b.ORG_ID = 0 or b.ORG_ID is null)");
						else $arr_content[$data_count][count_all] = count_person(0, 2, $search_condition, "(b.ORG_ID=$ORG_ID)");
//						$GRAND_ALL += $arr_content[$data_count][count_all];

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for		
	} // end while
	
	for($i=1; $i<=11; $i++) $LEVEL_TOTAL[$i] = count_person($i, 0, $search_condition, "");	
	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
	$GRAND_ALL = count_person(0, 0, $search_condition, "");
	
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

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			unset($COUNT_LEVEL);
			for($i=1; $i<=11; $i++) $COUNT_LEVEL[$i] = $arr_content[$data_count]["level_". $i];
			$COUNT_TOTAL = array_sum($COUNT_LEVEL);
			$COUNT_ALL = $arr_content[$data_count][count_all];
			
			$PERCENT_TOTAL = 0;
			if($COUNT_ALL) $PERCENT_TOTAL = ($COUNT_TOTAL / $COUNT_ALL) * 100;
			
			if($REPORT_ORDER=="SEX"){
				for($i=1; $i<=11; $i++) $COUNT_LEVEL[$i] = ($COUNT_LEVEL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_LEVEL[$i])):number_format($COUNT_LEVEL[$i])):"-");
				$COUNT_TOTAL = ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-");
				$COUNT_ALL = ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_ALL)):number_format($COUNT_ALL)):"-");				
				$PERCENT_TOTAL = ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-");

				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, $COUNT_LEVEL[$i], set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 12, $COUNT_TOTAL, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 13, $COUNT_ALL, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 14, $PERCENT_TOTAL, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			}else{
				for($i=1; $i<=11; $i++) $COUNT_LEVEL[$i] = ($COUNT_LEVEL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_LEVEL[$i])):number_format($COUNT_LEVEL[$i])):"");
				$COUNT_TOTAL = ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"");
				$COUNT_ALL = ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_ALL)):number_format($COUNT_ALL)):"");	
				$PERCENT_TOTAL = ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"");

				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, $COUNT_LEVEL[$i], set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 12, $COUNT_TOTAL, set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 13, $COUNT_ALL, set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 14, $PERCENT_TOTAL, set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			} // end if
		} // end for
				
		$PERCENT_TOTAL = 0;
		if($GRAND_ALL) $PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_ALL) * 100;

		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, ($LEVEL_TOTAL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($LEVEL_TOTAL[$i])):number_format($LEVEL_TOTAL[$i])):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 12, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 13, ($GRAND_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_ALL)):number_format($GRAND_ALL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 14, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
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