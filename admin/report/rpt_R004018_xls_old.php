<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$ARR_LEVEL_GROUP = array("M", "D", "K", "O");
	$ARR_LEVEL_GROUP_NAME["M"] = "บริหาร";
	$ARR_LEVEL_GROUP_NAME["D"] = "อำนวยการ";
	$ARR_LEVEL_GROUP_NAME["K"] = "วิชาการ";
	$ARR_LEVEL_GROUP_NAME["O"] = "ทั่วไป";

	$ARR_LEVEL["M"] = array("M2", "M1");
	$ARR_LEVEL["D"] = array("D2", "D1");
	$ARR_LEVEL["K"] = array("K5", "K4", "K3", "K2", "K1");
	$ARR_LEVEL["O"] = array("O4", "O3", "O2", "O1");
	$ARR_LEVEL_NAME["M2"][0] = "";
	$ARR_LEVEL_NAME["M1"][0] = "";
	$ARR_LEVEL_NAME["D2"][0] = "";
	$ARR_LEVEL_NAME["D1"][0] = "";
	$ARR_LEVEL_NAME["K5"][0] = "";
	$ARR_LEVEL_NAME["K4"][0] = "";
	$ARR_LEVEL_NAME["K3"][0] = "ชำนาญ";
	$ARR_LEVEL_NAME["K2"][0] = "";
	$ARR_LEVEL_NAME["K1"][0] = "";
	$ARR_LEVEL_NAME["O4"][0] = "ทักษะ";
	$ARR_LEVEL_NAME["O3"][0] = "";
	$ARR_LEVEL_NAME["O2"][0] = "";
	$ARR_LEVEL_NAME["O1"][0] = "";
	$ARR_LEVEL_NAME["M2"][1] = "ระดับสูง";
	$ARR_LEVEL_NAME["M1"][1] = "ระดับต้น";
	$ARR_LEVEL_NAME["D2"][1] = "ระดับสูง";
	$ARR_LEVEL_NAME["D1"][1] = "ระดับต้น";
	$ARR_LEVEL_NAME["K5"][1] = "ทรงคุณวุฒิ";
	$ARR_LEVEL_NAME["K4"][1] = "เชียวชาญ";
	$ARR_LEVEL_NAME["K3"][1] = "การพิเศษ";
	$ARR_LEVEL_NAME["K2"][1] = "ชำนาญการ";
	$ARR_LEVEL_NAME["K1"][1] = "ปฏิบัติการ";
	$ARR_LEVEL_NAME["O4"][1] = "พิเศษ";
	$ARR_LEVEL_NAME["O3"][1] = "อาวุโส";
	$ARR_LEVEL_NAME["O2"][1] = "ชำนาญงาน";
	$ARR_LEVEL_NAME["O1"][1] = "ปฏิบัติงาน";

	$TOTAL_LEVEL = 0;
	foreach($ARR_LEVEL_GROUP as $LEVEL_GROUP) $TOTAL_LEVEL += count($ARR_LEVEL[$LEVEL_GROUP]);

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
				if($select_org_structure==0) $select_list .= "a.DEPARTMENT_ID";
				else if($select_org_structure==1) $select_list .= "d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.DEPARTMENT_ID";
				else if($select_org_structure==1) $order_by .= "d.ORG_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "a.ORG_ID";
				else if($select_org_structure==1) $select_list .= "d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.ORG_ID";
				else if($select_org_structure==1) $order_by .= "d.ORG_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				if($DPISDB=="oci8") $select_list .= "e.PM_SEQ_NO, f.PL_SEQ_NO, a.PM_CODE, a.PL_CODE ";
				else $select_list .= "e.PM_SEQ_NO, a.PM_CODE";

				if($order_by) $order_by .= ", ";
				if($DPISDB=="oci8") $order_by .= "e.PM_SEQ_NO, f.PL_SEQ_NO, a.PM_CODE, a.PL_CODE ";
				else $order_by .= "e.PM_SEQ_NO, a.PM_CODE";

				$heading_name .= " ตำแหน่งในการบริหารงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "c.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ $order_by = "a.DEPARTMENT_ID";
		}else{
			if($select_org_structure==0) $order_by = "a.ORG_ID";
			else if($select_org_structure==1) $order_by = "d.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "c.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ $select_list = "a.DEPARTMENT_ID";
		}else{
			if($select_org_structure==0) $select_list = "a.ORG_ID";
			else if($select_org_structure==1) $select_list = "d.ORG_ID";
		}
	} // end if

	$search_condition = "";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
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
	}elseif($list_type == "PER_ORG_TYPE_3"){
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
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		$search_pl_code = trim($search_pl_code);
		$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
		$list_type_text .= "$search_pl_name";
	}elseif($list_type == "PER_COUNTRY"){
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
	}else{
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
	$report_title = "$DEPARTMENT_NAME||จำนวนข้าราชกราที่บรรจุใหม่ บรรจุกลับ รับโอน และการสูญเสียในกรณีต่าง ๆ||ประจำปีงบประมาณ พ.ศ. $search_budget_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0418";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function set_cell_width(){
		global $worksheet, $TOTAL_LEVEL;

		$worksheet->set_column(0, 0, 5);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, (($TOTAL_LEVEL * 2)+3), 5);
	} // function
	
	function print_header(){
		global $worksheet, $xlsRow, $heading_name;
		global $ARR_LEVEL_GROUP, $ARR_LEVEL_GROUP_NAME, $ARR_LEVEL, $ARR_LEVEL_NAME, $TOTAL_LEVEL;
		
		$xlsRow++;
		$index = 1;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "", "", "TLR", "0"));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "", "", "TLR", "0"));
		for($i=0; $i<($TOTAL_LEVEL * 2); $i++){
			$index++;
			if($i==0){
				$worksheet->write($xlsRow, $index, "ตำแหน่งประเภท", set_format("xlsFmtTableHeader", "", "", "TLBR", "1"));
			}else{
				$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "TLBR", "1"));
			} // end if else
		} // loop for
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 2), "", set_format("xlsFmtTableHeader", "", "", "TL", "0"));
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 3), "", set_format("xlsFmtTableHeader", "", "", "TR", "0"));

		$xlsRow++;
		$index = 1;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "", "", "LR", "0"));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "", "", "LR", "0"));
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				$index++;
				if($j==0){
					$worksheet->write($xlsRow, $index, $ARR_LEVEL_GROUP_NAME[$LEVEL_GROUP], set_format("xlsFmtTableHeader", "", "", "TLBR", "1"));
					$index++;
					$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "TLBR", "1"));
				}else{
					$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "TLBR", "1"));
					$index++;
					$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "TLBR", "1"));
				} // end if else
			} // loop for
		} // loop for		
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 2), "รวม", set_format("xlsFmtTableHeader", "", "", "LR", "1"));
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 3), "", set_format("xlsFmtTableHeader", "", "", "LR", "1"));

		$xlsRow++;
		$index = 1;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "", "", "LR", "0"));
		$worksheet->write($xlsRow, 1, "ส่วนราชการ", set_format("xlsFmtTableHeader", "", "", "LR", "0"));
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				if( trim($ARR_LEVEL_NAME[$LEVEL_NO][0]) ){
					$index++;
					$worksheet->write($xlsRow, $index, $ARR_LEVEL_NAME[$LEVEL_NO][0], set_format("xlsFmtTableHeader", "", "", "TLR", "1"));
					$index++;
					$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "TLR", "1"));
				}else{
					$index++;
					$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "TL", "0"));
					$index++;
					$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "TR", "0"));
				} // if else
			} // loop for
		} // loop for		
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 2), "", set_format("xlsFmtTableHeader", "", "", "L", "0"));
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 3), "", set_format("xlsFmtTableHeader", "", "", "R", "0"));

		$xlsRow++;
		$index = 1;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "", "", "LR", "0"));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "", "", "LR", "0"));
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				if( trim($ARR_LEVEL_NAME[$LEVEL_NO][1]) ){
					$index++;
					$worksheet->write($xlsRow, $index, $ARR_LEVEL_NAME[$LEVEL_NO][1], set_format("xlsFmtTableHeader", "", "", "LBR", "1"));
					$index++;
					$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "LBR", "1"));
				}else{
					$index++;
					$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "LB", "0"));
					$index++;
					$worksheet->write($xlsRow, $index, "", set_format("xlsFmtTableHeader", "", "", "BR", "0"));
				} // if else
			} // loop for
		} // loop for		
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 2), "", set_format("xlsFmtTableHeader", "", "", "LB", "0"));
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 3), "", set_format("xlsFmtTableHeader", "", "", "BR", "0"));

		$xlsRow++;
		$index = 1;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "", "", "LBR", "0"));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "", "", "LBR", "0"));
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				$index++;
				$worksheet->write($xlsRow, $index, "ชาย", set_format("xlsFmtTableHeader", "", "", "TLBR", "0"));
				$index++;
				$worksheet->write($xlsRow, $index, "หญิง", set_format("xlsFmtTableHeader", "", "", "TLBR", "0"));
			} // loop for
		} // loop for
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 2), "ชาย", set_format("xlsFmtTableHeader", "", "", "TLBR", "0"));
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 3), "หญิง", set_format("xlsFmtTableHeader", "", "", "TLBR", "0"));
	} // function		

	function count_person($gender, $budget_year, $level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $select_org_structure;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		
		$gender_condition = "d.PER_GENDER='$gender'";

		$search_condition = str_replace(" where ", " and ", $search_condition);

		if($DPISDB=="odbc"){
			$cmd = " select			count(d.PER_ID) as count_person
							from				
											(
												(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
											) left join PER_LINE f on (a.PL_CODE=f.PL_CODE)
							 where			d.LEVEL_NO='$level_no' and $gender_condition
													$search_condition and (POS_STATUS=1) 
							 group by		d.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(d.PER_ID) as count_person
								 from			PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d, PER_LINE f
								 where		a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.PL_CODE=f.PL_CODE(+) and a.POS_ID=d.POS_ID(+) and d.PER_TYPE=1 and d.PER_STATUS=1 and (POS_STATUS=1) 
								 					and d.LEVEL_NO='$level_no' and $gender_condition
													$search_condition
								 group by	d.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(d.PER_ID) as count_person
							from			(
												(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
											) left join PER_LINE f on (a.PL_CODE=f.PL_CODE)
							 where			d.LEVEL_NO='$level_no' and $gender_condition
													$search_condition and (POS_STATUS=1) 
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
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PM_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID) $arr_addition_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
					else $arr_addition_condition[] = "(c.ORG_ID_REF = 0 or c.ORG_ID_REF is null)";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
					else $arr_addition_condition[] = "(a.DEPARTMENT_ID = 0 or a.DEPARTMENT_ID is null)";
				break;
				case "ORG" :	
					if($ORG_ID){
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = $ORG_ID)";
					}else{ 
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = 0 or d.ORG_ID is null)";
					}
				break;
				case "LINE" :
					$arr_addition_condition[] = "(trim(a.PM_CODE) = '$PM_CODE' or (trim(a.PL_CODE) = '$PM_CODE' and a.PM_CODE is NULL))";
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

	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from		(
						 				(
												(
													PER_POSITION a
													left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
									) left join PER_LINE d on (a.PL_CODE=f.PL_CODE)
						$search_condition  and (POS_STATUS=1) 
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_POSITION a, PER_ORG b, PER_MGT e, PER_ORG c, PER_PERSONAL d, PER_LINE f
						 where			a.ORG_ID=b.ORG_ID(+) and a.PM_CODE=e.PM_CODE(+) and  a.PL_CODE=f.PL_CODE(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_ID=d.POS_ID(+) and (POS_STATUS=1) 
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(
						 				(
												(
													PER_POSITION a
													left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
									) left join PER_LINE d on (a.PL_CODE=f.PL_CODE)
						 $search_condition  and (POS_STATUS=1) 
						 order by		$order_by ";
	} // end if
 	if($select_org_structure==1) { 
		$cmd = str_replace("a.ORG_ID", "d.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
		$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
		for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
			$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
			$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] = 0;
			$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] = 0;
		} // loop for
	} // loop for
	initialize_parameter(0);
	unset($display_order_number);
	while($data = $db_dpis->get_array()){
		$s_pm = trim($data[PM_CODE]);
		$s_pl = trim($data[PL_CODE]);
		if (!$s_pm) $s_pm = trim($data[PL_CODE]);
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
						}else{
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								
								if($rpt_order_index == 0){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
						}else{
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								
								if($rpt_order_index == 0){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุ$ORG_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								
								if($rpt_order_index == 0){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				
				case "LINE" :
					if($PM_CODE != trim($data[PM_CODE])){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$PM_CODE = $s_pm;
						if($PM_CODE == ""){
							$PM_NAME = "[ไม่ระบุตำแหน่งในการบริหารงาน]";
						}else{
							$cmd = " select PM_NAME, PM_SHORTNAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PM_NAME = trim($data2[PM_SHORTNAME])?$data2[PM_SHORTNAME]:$data2[PM_NAME];
							if (!$PM_NAME) {
								$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PM_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PM_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
								if (!$PM_NAME) {
									$PM_NAME = "[ไม่ระบุตำแหน่งในการบริหารงาน]";
								}
							} // end if
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PM_NAME;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_m_".$LEVEL_NO)] = count_person("1", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total_m] += $arr_content[$data_count][("count_m_".$LEVEL_NO)];
								$arr_content[$data_count][("count_f_".$LEVEL_NO)] = count_person("2", ($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total_f] += $arr_content[$data_count][("count_f_".$LEVEL_NO)];
								
								if($rpt_order_index == 0){ 
									$LEVEL_GRAND_TOTAL["M"][$LEVEL_NO] += $arr_content[$data_count]["count_m_".$LEVEL_NO];
									$LEVEL_GRAND_TOTAL["F"][$LEVEL_NO] += $arr_content[$data_count]["count_f_".$LEVEL_NO];
								} // end if
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$GRAND_TOTAL_M = array_sum($LEVEL_GRAND_TOTAL["M"]);
	$GRAND_TOTAL_F = array_sum($LEVEL_GRAND_TOTAL["F"]);
	$GRAND_TOTAL = $GRAND_TOTAL_M + $GRAND_TOTAL_F;
	
	set_cell_width();
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "", "", "1"));
			for($j=1; $j<=(($TOTAL_LEVEL * 2) + 3); $j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "", "", "1"));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtTitle", "B", "L", "", "0"));
			for($j=1; $j<=(($TOTAL_LEVEL * 2) + 3); $j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtSubTitle", "B", "L", "", "0"));
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$DISPLAY_ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
				$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
				for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
					$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
					${"COUNT_M_".$LEVEL_NO} = $arr_content[$data_count][("count_m_".$LEVEL_NO)];
					${"COUNT_F_".$LEVEL_NO} = $arr_content[$data_count][("count_f_".$LEVEL_NO)];
				} // loop for
			} // loop for
			$TOTAL_M = $arr_content[$data_count][total_m];
			$TOTAL_F = $arr_content[$data_count][total_f];

			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$index = 1;
				$worksheet->write_string($xlsRow, 0, "$DISPLAY_ORDER", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
					$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
					for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
						$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
						$index++;
						$worksheet->write($xlsRow, $index, (${"COUNT_M_".$LEVEL_NO}?number_format(${"COUNT_M_".$LEVEL_NO}):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
						$index++;
						$worksheet->write($xlsRow, $index, (${"COUNT_F_".$LEVEL_NO}?number_format(${"COUNT_F_".$LEVEL_NO}):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					} // loop for
				} // loop for
				$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 2), ($TOTAL_M?number_format($TOTAL_M):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 3), ($TOTAL_F?number_format($TOTAL_F):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}else{
				$xlsRow++;
				$index = 1;
				$worksheet->write_string($xlsRow, 0, "$DISPLAY_ORDER", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
					$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
					for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
						$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
						$index++;
						$worksheet->write($xlsRow, $index, (${"COUNT_M_".$LEVEL_NO}?number_format(${"COUNT_M_".$LEVEL_NO}):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
						$index++;
						$worksheet->write($xlsRow, $index, (${"COUNT_F_".$LEVEL_NO}?number_format(${"COUNT_F_".$LEVEL_NO}):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					} // loop for
				} // loop for
				$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 2), ($TOTAL_M?number_format($TOTAL_M):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 3), ($TOTAL_F?number_format($TOTAL_F):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			} // end if
		} // end for
		
		$xlsRow++;
		$index = 1;
		$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		$worksheet->write_string($xlsRow, 1, "รวมทั้งหมด", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				$index++;
				$worksheet->write($xlsRow, $index, ($LEVEL_GRAND_TOTAL["M"][$LEVEL_NO]?number_format($LEVEL_GRAND_TOTAL["M"][$LEVEL_NO]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$index++;
				$worksheet->write($xlsRow, $index, ($LEVEL_GRAND_TOTAL["F"][$LEVEL_NO]?number_format($LEVEL_GRAND_TOTAL["F"][$LEVEL_NO]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			} // loop for
		} // loop for
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 2), ($GRAND_TOTAL?number_format($GRAND_TOTAL):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 1));
		$worksheet->write($xlsRow, (($TOTAL_LEVEL * 2) + 3), "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 1));

	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "", "", "1"));
		for($j=1; $j<=(($TOTAL_LEVEL * 2) + 3); $j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "", "", "1"));
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