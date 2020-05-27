<?
	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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
	$ARR_LEVEL_NAME["M2"][1] = "สูง";
	$ARR_LEVEL_NAME["M1"][1] = "ต้น";
	$ARR_LEVEL_NAME["D2"][1] = "สูง";
	$ARR_LEVEL_NAME["D1"][1] = "ต้น";
	$ARR_LEVEL_NAME["K5"][1] = "ทรงคุณวุฒิ";
	$ARR_LEVEL_NAME["K4"][1] = "เชียวชาญ";
	$ARR_LEVEL_NAME["K3"][1] = "การพิเศษ";
	$ARR_LEVEL_NAME["K2"][1] = "ชำนาญการ";
	$ARR_LEVEL_NAME["K1"][1] = "ปฏิบัติการ";
	$ARR_LEVEL_NAME["O4"][1] = "พิเศษ";
	$ARR_LEVEL_NAME["O3"][1] = "อาวุโส";
	$ARR_LEVEL_NAME["O2"][1] = "ชำนาญงาน";
	$ARR_LEVEL_NAME["O1"][1] = "ปฏิบัติงาน";
	
	if(!trim($RPTORD_LIST)){ 
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
	$arr_search_condition[] = "(d.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(d.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = "ทั้งส่วนราชการ";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='1')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(b.OT_CODE), 1, 1)='1'";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='2')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(b.OT_CODE), 1, 1)='2')";
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		} // end if
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
		// ทั้งส่วนราชการ
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "$list_type_text";
	if($search_per_type==1) 
		$report_title = "$DEPARTMENT_NAME||จำนวนข้าราชการพลเรือนที่จะเกษียณอายุ ประจำปีงบประมาณ พ.ศ. $search_budget_year";
	elseif($search_per_type==2) 
		$report_title = "$DEPARTMENT_NAME||จำนวนลูกจ้างที่จะเกษียณอายุ ประจำปีงบประมาณ พ.ศ. $search_budget_year";
	$report_code = "R1012";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('angsa','',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "65";
	$heading_width[1] = "16";
	$heading_width[2] = "15";
	
	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $ARR_LEVEL_GROUP, $ARR_LEVEL_GROUP_NAME, $ARR_LEVEL, $ARR_LEVEL_NAME;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"",'LTR',0,'C',1);
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			$pdf->Cell(($heading_width[1] * count($ARR_LEVEL[$LEVEL_GROUP])) ,7,$ARR_LEVEL_GROUP_NAME[$LEVEL_GROUP],'LTBR',0,'C',1);
		} // loop for
		$pdf->Cell($heading_width[2] ,7,"",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,$heading_name,'LR',0,'C',1);
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				$pdf->Cell($heading_width[1] ,7,$ARR_LEVEL_NAME[$LEVEL_NO][0],'LTR',0,'C',1);
			} // loop for
		} // loop for
		$pdf->Cell($heading_width[2] ,7,"รวม",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				$pdf->Cell($heading_width[1] ,7,$ARR_LEVEL_NAME[$LEVEL_NO][1],'LBR',0,'C',1);
			} // loop for
		} // loop for
		$pdf->Cell($heading_width[2] ,7,"",'LBR',1,'C',1);
	} // function		

	function count_person($budget_year, $level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $search_per_type;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		
//		if($budget_year) $year_condition = generate_year_condition($budget_year);
		if($DPISDB=="odbc") $year_condition = "(LEFT(trim(d.PER_RETIREDATE), 10) >= '".($budget_year - 1)."-10-01' and LEFT(trim(d.PER_RETIREDATE), 10) <= '".($budget_year)."-10-01')";
		elseif($DPISDB=="oci8") $year_condition = "(SUBSTR(trim(d.PER_RETIREDATE), 1, 10) >= '".($budget_year - 1)."-10-01' and SUBSTR(trim(d.PER_RETIREDATE), 1, 10) <= '".($budget_year)."-10-01')";

		$search_condition = str_replace(" where ", " and ", $search_condition);

		if($search_per_type==1){
			// ข้าราชการ
			if($DPISDB=="odbc"){
				$cmd = " select			count(d.PER_ID) as count_person
								from				(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
								 where			d.LEVEL_NO='$level_no' and $year_condition
													$search_condition
								 group by		d.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(d.PER_ID) as count_person
								 from			PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d
								 where			a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_ID=d.POS_ID(+)
								 					and d.LEVEL_NO='$level_no' and $year_condition
													$search_condition
								 group by		d.PER_ID
							   ";
			}
		}elseif($search_per_type==2){
			// ลูกจ้าง
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a
																left join PER_POS_EMP b on (a.POS_ID=b.POS_ID)
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
													) left join PER_EDUCNAME e on (d.EN_CODE=e.EN_CODE)
								  where		$year_condition
													$search_condition
								  group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d, PER_EDUCNAME e
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%' and d.EN_CODE=e.EN_CODE(+)
													and $year_condition
													$search_condition
								  group by	a.PER_ID
							   ";
			}
		} // end if

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

//			echo "age <= 24 :: $age_condition<br>";
		
		return $year_condition;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
				
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
					if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
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

	if($search_per_type==1){
		// ข้าราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_POSITION a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d
							 where			a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_ID=d.POS_ID(+)
												$search_condition
							 order by		$order_by
						   ";
		}
	}elseif($search_per_type==2){
		// ลูกจ้าง
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_POS_EMP a
													left joinPER_PERSONAL b on (a.POS_ID=b.POS_ID)
												) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_POS_EMP a,PER_PERSONAL  b, PER_ORG c, PER_EDUCATE d, PER_EDUCNAME e
							 where		a.POS_ID=b.POS_ID(+) and a.ORG_ID=c.ORG_ID(+) 
												and b.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%' and d.EN_CODE=e.EN_CODE(+)
												$search_condition
							 order by		$order_by
						   ";
		}
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
		$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
		for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
			$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
			$LEVEL_GRAND_TOTAL[$LEVEL_NO] = 0;
		} // loop for
	} // loop for
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[ไม่ระบุกระทรวง]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person(($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
								
								if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[ไม่ระบุกรม]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person(($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
								
								if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุสำนัก/กอง]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person(($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
								
								if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
							} // loop for
						} // loop for

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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person(($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
								
								if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
							} // loop for
						} // loop for
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
			} // end switch case
		} // end for
	} // end while
	
	print_r($arr_content); echo "<BR>";
	$GRAND_TOTAL = array_sum($LEVEL_GRAND_TOTAL);

?>