<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", "1800");
	
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
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	//print_r($RPTORD_LIST);

	$select_list = "";		$order_by = "";	$heading_name = "";
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
				$select_list .= "a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.DEPARTMENT_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

				$heading_name .= " สายงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID) $order_by = "c.ORG_ID_REF";
		elseif(!$DEPARTMENT_ID) $order_by = "a.DEPARTMENT_ID";
		else $order_by = "a.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID) $select_list = "c.ORG_ID_REF as MINISTRY_ID";
		elseif(!$DEPARTMENT_ID) $select_list = "a.DEPARTMENT_ID";
		else $select_list = "a.ORG_ID";
	} // end if

	$search_condition = "";
	/*$arr_search_condition[] = "(b.PER_TYPE = 1)";
	$arr_search_condition[] = "(b.PER_STATUS = 1)";*/
	//$ARR_LEVEL = array("K3"=>"ชำนาญการพิเศษ","K4"=>"เชี่ยวชาญ","K5"=>"ทรงคุณวุฒิ","D1"=>"อำนวยการระดับต้น","D2"=>"อำนวยการระดับสูง","M1"=>"บริหารระดับต้น","M2"=>"บริหารระดับสูง");
	$ARR_LEVEL = array("K3"=>"ชพ.","K4"=>"ชช.","K5"=>"ทว.","D1"=>"อต.","D2"=>"อส.","M1"=>"บต.","M2"=>"บส."); //"09"
	//print_r($ARR_LEVEL);

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='1')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='1'";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ส่วนภูมิภาค
		//$list_type_text = "ส่วนภูมิภาค";
		//if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='2')";
		//elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='2')";
	}elseif($list_type == "PER_LINE"){//จะต้องมีค่าส่งมาถึงจะสร้างรายงานได้
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		if($search_pt_name){ $list_type_text .=" ตำแหน่งประเภท$search_pt_name / "; }
		if($search_per_type==1){
			$per_name = "ข้าราชการ";
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_pl_name";
			}
		}/**elseif($search_per_type==2){
			$per_name = "ลูกจ้างประจำ";
			if(trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_pn_name";
			}
		}elseif($search_per_type==3){
			$per_name = "พนักงานราชการ";
			if(trim($search_ep_code)){
				$search_ep_code = trim($search_ep_code);
				$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_ep_name";
			}
		} // end if**/
	}
	/**elseif($list_type == "PER_COUNTRY"){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(a.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}**/
	else{
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "กรอบการสั่งสมประสบการณ์ของส่วนราชการ จำแนกตามตำแหน่งประเภท";
	$report_code = "R1202";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	$count_position_type=count($ARR_POSITION_TYPE[$search_pt_name]);
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_width, $heading_name;
		global $ARR_LEVEL;
		
		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 40);
		$worksheet->set_column(2, 2, 50);
		for($i=0;$i < count($ARR_LEVEL);$i++){
			 $worksheet->set_column($i+3, $i+3, 15);
		}
		//$worksheet->set_column(count($ARR_LEVEL)+3, count($ARR_LEVEL)+3, 20);


		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "$MINISTRY_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		for($i=0;$i < count($ARR_LEVEL);$i++){
			if($i==0){
				$worksheet->write($xlsRow, $i+3, "จำนวนกรอบการสั่งสมประสบการณ์ตามตำแหน่งประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			}else{
				$worksheet->write($xlsRow, $i+3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			}
		}
		//$worksheet->write($xlsRow, count($ARR_LEVEL)+3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		//แถว 2 ---------------------
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$i=0;	$k=0;
		foreach ($ARR_LEVEL as $key => $value) {
			$i++;
			$k=($i+2);
			$worksheet->write($xlsRow, $k, "$value", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		}
		//$worksheet->write($xlsRow, ($k+1), "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	//หาจำนวนกรอบการสั่งสมประสบการณ์
	function count_eaf($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type;
		global $select_org_structure;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);

		if($DPISDB=="odbc"){
			$cmd = " select			count(a.EAF_ID) as count_eaf
							from				(
														EAF_MASTER a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													)	left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
								 where			(a.LEVEL_NO='$level_no')  
												$search_condition
							 group by		a.EAF_ID
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = "select count(a.EAF_ID) as count_eaf
								from EAF_MASTER a ,PER_ORG b, PER_ORG c
								where a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and (a.LEVEL_NO='$level_no')  
								$search_condition
								group by a.EAF_ID
								";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.EAF_ID) as count_eaf
							from				(
														EAF_MASTER a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													)	left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
								 where			(a.LEVEL_NO='$level_no')  
												$search_condition
							 group by		a.EAF_ID
						   ";
		}

		if($select_org_structure==1){
			 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			 $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		}
		$count_eaf = $db_dpis2->send_cmd($cmd);
		//echo "<br>X2:::$count_eaf  + $cmd<br>";
		//$db_dpis2->show_error();
		if($count_eaf==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_eaf] == 0) $count_eaf = 0;
		} // end if
return $count_eaf;
} // function

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(c.ORG_ID_REF = '$MINISTRY_ID')";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = '$DEPARTMENT_ID')";
				break;
				case "ORG" :	
					if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = '$ORG_ID')";
					else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
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
					$PL_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	//แสดงรายชื่อหน่วยราชการ
	//หาชื่อส่วนราชการ 
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
							 from			(
														EAF_MASTER a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) 	left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by
						   ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select distinct $select_list
								from EAF_MASTER a ,PER_ORG b, PER_ORG c
								where a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) 
								$search_condition
								order by		$order_by
								";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
							 from			(
														EAF_MASTER a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) 	left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by
						   ";
	}
	if($select_org_structure==1){
		 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		 $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
// echo "<br>$cmd<br>";
	$data_count = 0;
	$data_row = 0;
	$GRAND_TOTAL = 0;
	foreach ($ARR_LEVEL as $key => $value) {
			$LEVEL_NO = $key;
			$LEVEL_GRAND_TOTAL[$LEVEL_NO] = 0;
	} // end for
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$data_row++;
						$arr_content[$data_count][sequence] = $data_row;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 7)) . $MINISTRY_NAME;

						foreach ($ARR_LEVEL as $key => $value) {
							$LEVEL_NO = $key;
							$arr_content[$data_count]["count_".$LEVEL_NO] = count_eaf($LEVEL_NO, $search_condition, $addition_condition);
							$arr_content[$data_count][total] += $arr_content[$data_count]["count_".$LEVEL_NO];
							
							if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
						} //end for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
						
					} // end if
				break;

				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$data_row++;
						$arr_content[$data_count][sequence] = $data_row;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 7)) . $DEPARTMENT_NAME;
						
						foreach ($ARR_LEVEL as $key => $value) {
							$LEVEL_NO = $key;
							$arr_content[$data_count]["count_".$LEVEL_NO] = count_eaf($LEVEL_NO, $search_condition, $addition_condition);
							$arr_content[$data_count][total] += $arr_content[$data_count]["count_".$LEVEL_NO];
							
							if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
						} //end for
						
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
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if
						//$data_row = 0;
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$data_row++;
						$arr_content[$data_count][sequence] = $data_row;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 7)) . $ORG_NAME;

						foreach ($ARR_LEVEL as $key => $value) {
							$LEVEL_NO = $key;
							$arr_content[$data_count]["count_".$LEVEL_NO] = count_eaf($LEVEL_NO, $search_condition, $addition_condition);
							$arr_content[$data_count][total] += $arr_content[$data_count]["count_".$LEVEL_NO];
							
							if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
						} //end for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;
				
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE == ""){
							$PL_NAME = "[ไม่ระบุสายงาน]";
						}else{
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "LINE";
						$data_row++;
						$arr_content[$data_count][sequence] = $data_row;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 7)) . $PL_NAME;

						foreach ($ARR_LEVEL as $key => $value) {
							$LEVEL_NO = $key;
							$arr_content[$data_count]["count_".$LEVEL_NO] = count_eaf($LEVEL_NO, $search_condition, $addition_condition);
							$arr_content[$data_count][total] += $arr_content[$data_count]["count_".$LEVEL_NO];
							
							if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
						} //end for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

			} // end switch case
		} // end for
	} // end while
	$GRAND_TOTAL = array_sum($LEVEL_GRAND_TOTAL);
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			//$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0;$j < count($ARR_LEVEL)+2;$j++)	$worksheet->write($xlsRow, $j+1, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
			//$worksheet->write($xlsRow, (count($ARR_LEVEL)+3), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if
	
		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0;$j < count($ARR_LEVEL)+2;$j++)	$worksheet->write($xlsRow, $j+1, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
			//$worksheet->write($xlsRow, (count($ARR_LEVEL)+3), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if
				
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$COUNT = $arr_content[$data_count][sequence];
			$NAME = $arr_content[$data_count][name];
			foreach ($ARR_LEVEL as $key => $value) {
				$LEVEL_NO = $key;
				${"COUNT_".$LEVEL_NO} = $arr_content[$data_count]["count_".$LEVEL_NO];
			} //end for
			$COUNT_TOTAL = $arr_content[$data_count][total];
			
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$i=0; $k=0;
				$worksheet->write_string($xlsRow, 0, "$COUNT", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				if($REPORT_ORDER == "MINISTRY"){
					$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				}elseif($REPORT_ORDER == "DEPARTMENT"){
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 2, "$NAME", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				}

				foreach ($ARR_LEVEL as $key => $value) {
					$i++;
					$k=($i+2);
					$LEVEL_NO = $key;
					$worksheet->write($xlsRow, $k, (${"COUNT_".$LEVEL_NO}?number_format(${"COUNT_".$LEVEL_NO}):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				} // loop for
				//$worksheet->write($xlsRow, ($k+1), ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}else{
				$xlsRow++;
				$i=0; $k=0;
				$worksheet->write_string($xlsRow, 0, "$COUNT", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				if($REPORT_ORDER == "MINISTRY"){
					$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				}elseif($REPORT_ORDER == "DEPARTMENT"){
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 2, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				}

				foreach ($ARR_LEVEL as $key => $value) {
					$i++;
					$k=($i+2);
					$LEVEL_NO = $key;
					$worksheet->write($xlsRow, $k, (${"COUNT_".$LEVEL_NO}?number_format(${"COUNT_".$LEVEL_NO}):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				} // loop for
				//$worksheet->write($xlsRow, ($k+1), ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			} // end if
		} // end for
		
		//รวมทั้งหมด
		$xlsRow++;
		$i=0; $k=0;
		$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		$worksheet->write_string($xlsRow, 1, "-", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
		$worksheet->write_string($xlsRow, 2, "-", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
		
		foreach ($ARR_LEVEL as $key => $value) {
			$i++;
			$k=($i+2);
			$LEVEL_NO = $key;
			$worksheet->write($xlsRow, $k, ($LEVEL_GRAND_TOTAL[$LEVEL_NO]?number_format($LEVEL_GRAND_TOTAL[$LEVEL_NO]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		} // loop for
		//$worksheet->write($xlsRow, ($k+1), ($GRAND_TOTAL?number_format($GRAND_TOTAL):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($j=1;$j <=count($ARR_LEVEL)+3;$j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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