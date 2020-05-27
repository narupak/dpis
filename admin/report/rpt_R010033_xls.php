<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("MINISTRY", "DEPARTMENT", "ORG", "LINE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF";

				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.PM_SEQ_NO, a.PM_CODE, f.PL_SEQ_NO, a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.PM_SEQ_NO, a.PM_CODE, f.PL_SEQ_NO, a.PL_CODE";

				$heading_name .= " ตำแหน่งในการบริหารงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){
			$order_by = "c.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){
			$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){
			$select_list = "c.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){
			$select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if
	
	$search_condition = "";
	$arr_search_condition[] = "(POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_MGT", $list_type)){
		// ตำแหน่งในการบริหารงาน
		$list_type_text = "";
		$search_PM_CODE = trim($search_PM_CODE);
		$arr_search_condition[] = "(trim(a.PM_CODE)='$search_PM_CODE')";
		$list_type_text .= "$search_PM_NAME";
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "$list_type_text";

	$report_title = "$DEPARTMENT_NAME||จำนวนข้าราชการ".$list_type_text." ประจำปีงบประมาณ พ.ศ. ".(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year)):($search_budget_year))."||จำแนกตามตำแหน่งในการบริหารงาน และสังกัด";
	$report_code = "R1033";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$heading_width[0] = "8";
	$heading_width[1] = "40"; 	// ให้กว้าง 152 หักไป 48 เหลือ 104
	$heading_width[2] = "10";	// 

	function set_cell_width($heading_width, $org){
		global $worksheet;

		$worksheet->set_column(0, 0, $heading_width[0]);
		$worksheet->set_column(1, 1, $heading_width[1]);
		$idx = 1;
		foreach($org as $ORG_ID => $val) {
			$idx++;
			$worksheet->set_column($idx, $idx, $heading_width[2]);
		}
		$idx++;
		$worksheet->set_column($idx, $idx, $heading_width[2]);
	} // function

	function print_header($org_seq, $org){
		global $worksheet, $xlsRow, $heading_name;
		global $ARR_LEVEL_GROUP, $ARR_LEVEL_GROUP_NAME, $ARR_LEVEL, $ARR_LEVEL_NAME, $TOTAL_LEVEL;
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "", "", "TLR", "0"));
		$worksheet->write($xlsRow, 1, "ตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "", "", "TLR", "0"));

		$idx = 1;
		foreach($org_seq as $key => $val) {
//			foreach($org[$val] as $ORG_NO => $val1) {
				$idx++;
				$worksheet->write($xlsRow, $idx, $org[$val][name], set_format("xlsFmtTableHeader", "", "L", "TLB", "1"));
//			echo "$ORG_ID-$val<br>";
//			}
		}
		$idx++;
		$worksheet->write($xlsRow, $idx, "รวม", set_format("xlsFmtTableHeader", "", "", "TBLR", "1"));
	} // function

	function count_person($budget_year, $pos_org_id, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;	
		$year_condition="";
//		if($budget_year) $year_condition = generate_year_condition($budget_year);
/*		if($DPISDB=="odbc") $year_condition = " and (LEFT(trim(d.PER_RETIREDATE), 10) >= '".($budget_year - 1)."-10-01' and LEFT(trim(d.PER_RETIREDATE), 10) < '".($budget_year)."-10-01')";
		elseif($DPISDB=="oci8") $year_condition = " and (SUBSTR(trim(d.PER_RETIREDATE), 1, 10) >= '".($budget_year - 1)."-10-01' and SUBSTR(trim(d.PER_RETIREDATE), 1, 10) < '".($budget_year)."-10-01')";
		elseif($DPISDB=="mysql") $year_condition = " and (SUBSTRING(trim(d.PER_RETIREDATE), 1, 10) >= '".($budget_year - 1)."-10-01' and SUBSTRING(trim(d.PER_RETIREDATE), 1, 10) < '".($budget_year)."-10-01')";  */

		$pos_org_condition = "a.ORG_ID='$pos_org_id'";

		$search_condition = str_replace(" where ", " and ", $search_condition);

		if($DPISDB=="odbc"){
			$cmd = " select			count(d.PER_ID) as count_person
							from				(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
							 where			$pos_org_condition $year_condition
													$search_condition 
							 group by		d.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(d.PER_ID) as count_person
							 from				PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d
							 where			a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_ID=d.POS_ID(+) and d.PER_TYPE=1 and d.PER_STATUS=1
								 					and $pos_org_condition $year_condition
													$search_condition 
							 group by		d.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(d.PER_ID) as count_person
							from				(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
							 where			$pos_org_condition $year_condition 
													$search_condition 
							 group by		d.PER_ID ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("a.ORG_ID", "d.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_year_condition($budget_year){
		global $DPISDB;
		
//			คิดตามอายุจริง (ถ้ายังไม่ถึงวันเกิดจะไม่นับเป็นปี)
//			$birthdate_min = date_adjust(date("Y-m-d"), "y", -25);
//			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(d.PER_BIRTHDATE), 10) > '$birthdate_min')";
//			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(d.PER_BIRTHDATE), 1, 10) > '$birthdate_min')";

//			คิดเฉพาะปีเกิด กับปีปัจจุบัน
			$birthyear_min = date("Y") - 25;
			if($DPISDB=="odbc") $year_condition = "(LEFT(trim(d.PER_BIRTHDATE), 4) > '$birthyear_min')";
			elseif($DPISDB=="oci8") $year_condition = "(SUBSTR(trim(d.PER_BIRTHDATE), 1, 4) > '$birthyear_min')";
			elseif($DPISDB=="mysql") $year_condition = "(SUBSTRING(trim(d.PER_BIRTHDATE), 1, 4) > '$birthyear_min')";

//			echo "age <= 24 :: $age_condition<br>";
		
		return $year_condition;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PM_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1)  $arr_addition_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1)  $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($ORG_ID && $ORG_ID!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = $ORG_ID)";
					}else{ 
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = 0 or d.ORG_ID is null)";
					}
				break;
				case "LINE" :
					if($PM_CODE) $arr_addition_condition[] = "(trim(a.PM_CODE) = '$PM_CODE')";
					else $arr_addition_condition[] = "(trim(a.PM_CODE) = '$PM_CODE' or a.PM_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PM_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					$MINISTRY_ID = -1;
				break;
				case "DEPARTMENT" :	
					$DEPARTMENT_ID = -1;
				break;
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "LINE" :
					$PM_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	function clear_display_order($req_index){
		global $arr_rpt_order, $display_order_number;
		
		$current_index = $req_index + 1;
		for($i=$current_index; $i<count($arr_rpt_order); $i++){
			$display_order_number[$current_index] = 0;
		} // loop for
	} // function

	function display_order($req_index){
		global $display_order_number;
		
		$return_display_order = "";
		$current_index = $req_index;
		while($current_index >= 0){
			if($current_index == $req_index){
				$return_display_order = $display_order_number[$current_index];
			}else{
				$return_display_order = $display_order_number[$current_index].".".$return_display_order;
			} // if else
			$current_index--;
		} // loop while
		
		return $return_display_order;
	} // function

	$budget_year = ($search_budget_year - 543);
	$year_condition = "";
/*	if($DPISDB=="odbc") $year_condition = "and (LEFT(trim(d.PER_RETIREDATE), 10) >= '".($budget_year - 1)."-10-01' and LEFT(trim(d.PER_RETIREDATE), 10) < '".($budget_year)."-10-01')";
	elseif($DPISDB=="oci8") $year_condition = "and (SUBSTR(trim(d.PER_RETIREDATE), 1, 10) >= '".($budget_year - 1)."-10-01' and SUBSTR(trim(d.PER_RETIREDATE), 1, 10) < '".($budget_year)."-10-01')";
	elseif($DPISDB=="mysql") $year_condition = "and (SUBSTRING(trim(d.PER_RETIREDATE), 1, 10) >= '".($budget_year - 1)."-10-01' and SUBSTRING(trim(d.PER_RETIREDATE), 1, 10) < '".($budget_year)."-10-01')"; */

	if($DPISDB=="odbc"){
		$cmd = " select			distinct b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $select_list, count(d.PER_ID)  as COUNT_PERSON
						 from		(
						 					(
												(
													(
														PER_POSITION a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) left join PER_MGT e on (a.PM_CODE=e.PM_CODE)
												) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
										) left join PER_LINE f on (a.PL_CODE=f.PL_CODE)
						$search_condition $year_condition and (POS_STATUS=1) 
						 group by		b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $order_by
						order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $select_list, count(d.PER_ID)  as COUNT_PERSON
						 from			PER_POSITION a, PER_ORG b, PER_MGT e, PER_ORG c, PER_PERSONAL d, PER_LINE f
						 where			a.ORG_ID=b.ORG_ID(+) and a.PM_CODE=e.PM_CODE(+) and a.PL_CODE=f.PL_CODE(+) and a.DEPARTMENT_ID=c.ORG_ID(+) 
						 					and a.POS_ID=d.POS_ID(+) and d.PER_TYPE=1 and d.PER_STATUS=1
											$search_condition $year_condition and (POS_STATUS=1)  
						 group by		b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $order_by
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $select_list, count(d.PER_ID) as COUNT_PERSON
						 from		(
						 					(
												(
													(
														PER_POSITION a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) left join PER_MGT e on (a.PM_CODE=e.PM_CODE)
												) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
										) left join PER_LINE f on (a.PL_CODE=f.PL_CODE)
						 $search_condition $year_condition and (POS_STATUS=1)  
						 group by		b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $order_by
						 order by 		$order_by ";
	} // end if
 	if($select_org_structure==1) { 
		$cmd = str_replace("a.ORG_ID", "d.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();

	$arr_content = (array) null;
	$arr_grand_total = (array) null;
	$arr_org = (array) null;
	$arr_org_seq = (array) null;
	$arr_count = (array) null;

	$data_count = -1;
	initialize_parameter(0);
	unset($display_order_number);
		
	$DISPLAY_ORDER_PLCODE = 0;
	$count_person = 0;
	while($data = $db_dpis->get_array()) {
		$s_org = $data[ORG_ID];
		$s_org_seq = $data[ORG_SEQ_NO];
		if (!$s_org_seq) $s_org_seq = $s_org;
		$s_pm = trim($data[PM_CODE]);
		$s_pl = trim($data[PL_CODE]);
		if (!$s_pm) $s_pm = trim($data[PL_CODE]);
		
		// รวมพวก seq เท่ากันให้เป็น 1 แถว
		$b_pm_seq = $data[PM_SEQ_NO];
		if (!$b_pm_seq) $b_pm_seq = 900000+$data[PL_SEQ_NO];
		if (!$b_pm_seq) $b_pm_seq = 9000000+$s_pm;
		if ($s_pm_seq != $b_pm_seq) {
			$s_pm_seq = $b_pm_seq;
			$data_count ++;
		}
		// จบ การรวมพวก seq เท่ากันให้เป็น 1 แถว
		
		if (!in_array($s_org_seq, $arr_org_seq)) {
			$arr_org_seq[] = $s_org_seq;  // เพื่อจัดลำดับ สำหรับ ORG_SEQ_NO
		}
		if (!$arr_org[$s_org_seq]) {
			$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$s_org ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$s_org_name = ($data2[ORG_SHORT]?$data2[ORG_SHORT]:$data2[ORG_NAME]);
			if($s_org_name){
				$arr_org[$s_org_seq][code] = $s_org;
				$arr_org[$s_org_seq][name] = $s_org_name;
			}
		}
		//-------------------------------------------------------------------------
		$count_person= $data[COUNT_PERSON];
		if(!$count_person)	$count_person=0;
		$arr_count_person[$s_pm][$s_org] = $count_person; 
		//$arr_count_total_person[$s_pm] += $arr_count_person[$s_pm][$s_org]; 
		//------------------------------------------------------------------------
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$MINISTRY_ID = trim($data[MINISTRY_ID]);
						if($MINISTRY_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][code] = "M".$DEPARTMENT_ID;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
						
						$arr_content[$data_count][$s_org][$s_pm] = $arr_count_person[$s_pm][$s_org];	 
						$arr_content[$data_count][total] += $arr_content[$data_count][$s_org][$s_pm];

						if($rpt_order_index == 0){ 
							//$arr_grand_total[$s_org] += $arr_count[$data_count][$s_org][$arr_content[$data_count][code]];
							$arr_grand_total[$s_org] += $arr_count_person[$s_pm][$s_org];
						}

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

				case "DEPARTMENT" :
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
						if($DEPARTMENT_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][code] = "D".$DEPARTMENT_ID;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
						$arr_content[$data_count][$s_org][$s_pm] = $arr_count_person[$s_pm][$s_org];	
						$arr_content[$data_count][total] += $arr_content[$data_count][$s_org][$s_pm];

						if($rpt_order_index == 0){ 
							//$arr_grand_total[$s_org] += $arr_count[$data_count][$s_org][$arr_content[$data_count][code]];
							$arr_grand_total[$s_org] += $arr_count_person[$s_pm][$s_org];
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						} // end if


						if($ORG_NAME){
								$addition_condition = generate_condition($rpt_order_index);
		
								$arr_content[$data_count][type] = "ORG";
								$arr_content[$data_count][order] = display_order($rpt_order_index);
								$arr_content[$data_count][code] = "O".$ORG_ID;
								$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
								$arr_content[$data_count][$s_org][$s_pm] = $arr_count_person[$s_pm][$s_org];	
								$arr_content[$data_count][total]  += $arr_content[$data_count][$s_org][$s_pm];
		
								if($rpt_order_index == 0){ 
									//$arr_grand_total[$s_org] += $arr_count[$data_count][$s_org][$arr_content[$data_count][code]];
									$arr_grand_total[$s_org] += $arr_count_person[$s_pm][$s_org];
								}
		
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
						} //end if($ORG_NAME)
					} // end if
				break;
				
				case "LINE" :
if($PM_CODE != trim($data[PM_CODE])){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
							$cmd = " select PM_NAME, PM_SHORTNAME from PER_MGT where trim(PM_CODE)='$s_pm' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PM_NAME = trim($data2[PM_SHORTNAME])?$data2[PM_SHORTNAME]:$data2[PM_NAME];
							if (!$PM_NAME) {
								$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$s_pl' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PM_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
							}	
//						} // end if
							
							if($PM_NAME){
										$addition_condition = generate_condition($rpt_order_index);
							
										$arr_content[$data_count][type] = "LINE";
										$arr_content[$data_count][order] = display_order($rpt_order_index);
										$arr_content[$data_count][code] = $s_pm;
										$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PM_NAME;
										
										$arr_content[$data_count][$s_org][$s_pm] = $arr_count_person[$s_pm][$s_org];	
										$arr_content[$data_count][total]  += $arr_content[$data_count][$s_org][$s_pm];
				
										if($rpt_order_index == 0){ 
											//$arr_grand_total[$s_org] += $arr_count[$data_count][$s_org][$s_pm];
											$arr_grand_total[$s_org] += $arr_count_person[$s_pm][$s_org];
										}
										
										if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
										$data_count++;
							} // end if($PM_NAME)
} // end if($PM_CODE != trim($data[PM_CODE]))
				break;		
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//echo "<pre>"; print_r($arr_count_person); echo "</pre>";

	if ($count_data) {
		set_cell_width($heading_width, $arr_org);

		sort($arr_org_seq);
		
		$xlsRow = 0;
		
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "L", "", "1"));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "", "", "1"));
			$idx = 1;
			foreach($arr_org_seq as $key => $val) {
//				foreach($arr_org[$val] as $ORG_NO => $val1) {
					$idx++;
					$worksheet->write($xlsRow, $idx, "", set_format("xlsFmtTitle", "B", "", "", "1"));
//				}
			}
			$idx++;
			$worksheet->write($xlsRow, $idx, "", set_format("xlsFmtTitle", "B", "", "", "1"));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtTitle", "B", "L", "", "0"));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "", "", "0"));
			$idx = 1;
			foreach($arr_org_seq as $key => $val) {
//				foreach($arr_org[$val] as $ORG_NO => $val1) {
					$idx++;
					$worksheet->write($xlsRow, $idx, "", set_format("xlsFmtTitle", "B", "", "", "0"));
//				}
			}
			$idx++;
			$worksheet->write($xlsRow, $idx, "", set_format("xlsFmtTitle", "B", "", "", "0"));
		}
		
		print_header($arr_org_seq, $arr_org);

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
//			$DISPLAY_ORDER = $arr_content[$data_count][order];
			$DISPLAY_ORDER = $data_count+1;
			$NAME = $arr_content[$data_count][name];
			$CODE = $arr_content[$data_count][code];

if(!in_array($CODE,$arr_exist_plcode)){
			$DISPLAY_ORDER_PLCODE++;
			$arr_exist_plcode[] = $CODE;
//			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($DISPLAY_ORDER_PLCODE ):$DISPLAY_ORDER_PLCODE), set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));

				$idx = 1;
//				foreach($arr_org as $ORG_ID => $val) {
				foreach($arr_org_seq as $key => $val) {
//					foreach($arr_org[$val] as $ORG_NO => $val1) {
						$idx++;
//						$worksheet->write($xlsRow, $idx, ($arr_content[$data_count][$ORG_ID]?number_format($arr_content[$data_count][$ORG_ID]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
//						echo "[$data_count][$ORG_SEQ][$ORG_NO]=".$arr_count[$data_count][$ORG_SEQ][$ORG_NO]."<br>";

//						$worksheet->write($xlsRow, $idx, ($arr_count[$data_count][$arr_org[$val][code]][$CODE]?number_format($arr_count[$data_count][$arr_org[$val][code]][$CODE]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));

						$worksheet->write_string($xlsRow, $idx, ($arr_count_person[$CODE][$arr_org[$val][code]]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($arr_count_person[$CODE][$arr_org[$val][code]])):number_format($arr_count_person[$CODE][$arr_org[$val][code]])):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				}
				$idx++;
				//$worksheet->write($xlsRow, $idx, ($arr_content[$data_count][total]?number_format($arr_content[$data_count][total]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, $idx, (array_sum($arr_count_person[$CODE])?(($NUMBER_DISPLAY==2)?convert2thaidigit(array_sum($arr_count_person[$CODE])):number_format(array_sum($arr_count_person[$CODE]))):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
//			}
} //end if(!in_array($CODE,$arr_exist_plcode))			
		} // end for loop
		
		$xlsRow++;
		$index = 1;
		$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		$worksheet->write_string($xlsRow, 1, "รวมทั้งหมด", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$GRAND_TOTAL = 0;
		$idx = 1;
		foreach($arr_org_seq as $key => $val) {
//			foreach($arr_org[$val] as $ORG_NO => $val1) {
				$idx++;
				$worksheet->write_string($xlsRow, $idx, ($arr_grand_total[$arr_org[$val][code]]?(($NUMBER_DISPLAY==2)?convert2thaidigit($arr_grand_total[$arr_org[$val][code]]):number_format($arr_grand_total[$arr_org[$val][code]])):"-"), set_format("xlsFmtTableDetail", "B","R", "TLRB", 0)); 
				$GRAND_TOTAL += $arr_grand_total[$arr_org[$val][code]];
//			}
		}
		$idx++;
		$worksheet->write_string($xlsRow, $idx, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit($GRAND_TOTAL):number_format($GRAND_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "", "", "1"));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "", "", "0"));
		$idx = 1;
		foreach($arr_org_seq as $key => $val) {
			foreach($arr_org[$val] as $ORG_NO => $val1) {
				$idx++;
				$worksheet->write($xlsRow, $idx, "", set_format("xlsFmtTitle", "", "", "", "0"));
			}
		}
		$idx++;
		$worksheet->write($xlsRow, $idx, "", set_format("xlsFmtTitle", "", "", "", "0"));
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