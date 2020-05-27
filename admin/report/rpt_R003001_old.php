<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		 $line_join = "b.PL_CODE=f.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		 $line_seq="f.PL_SEQ_NO";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";	
		$line_seq="b.POEM_SEQ_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";	
		$line_seq="b.POEMS_SEQ_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";	
		$line_seq="b.POT_SEQ_NO";
	} // end if

	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "SEX", "PROVINCE", "EDUCLEVEL", "EDUCMAJOR"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID"; 

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID"; 

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID"; 

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";
					
				if($order_by) $order_by .= ", ";
				$order_by .= "$line_code";
				
				$heading_name .=  $line_title;
				break;
			case "SEX" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PER_GENDER";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_GENDER";

				$heading_name .= " $SEX_TITLE";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.PV_CODE";

				$heading_name .= " $PV_TITLE";
				break;
			case "EDUCLEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.EN_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.EN_CODE";

				$heading_name .= " $EN_TITLE";
				break;
			case "EDUCMAJOR" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.EM_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.EM_CODE";

				$heading_name .= " $EM_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0) $order_by = "b.ORG_ID";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list = "b.ORG_ID";
		else if($select_org_structure==1)  $select_list = "a.ORG_ID";
	}

	$search_condition = "";
	$budget_year = $search_budget_year - 543; 
	$budget_year_from = $budget_year - 1; 
	$budget_year_from = $budget_year_from.'-10-01'; 
	$budget_year_to = $budget_year.'-09-30';
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";			//search_per_status  เงื่อนไขที่ถูกส่งมา

	$list_type_text = $ALL_REPORT_TITLE;
	if($export_type=="report"){
		include ("../report/rpt_condition3.php");
	}else if($export_type=="graph"){
		include ("../../admin/report/rpt_condition3.php");	//เงื่อนไขที่ต้องการแสดงผล
	}
	
	$search_condition .= (trim($search_condition)?" and ":" where ") . "POH_EFFECTIVEDATE >= '$budget_year_from' and POH_EFFECTIVEDATE <= '$budget_year_to'";	//PER_OCCUPYDATE จาก PER_PERSONAL ค่ามันเท่ากันหมด เวลา select มาค่าเท่ากัน

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ที่บรรจุในปีงบประมาณ $search_budget_year";	
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0301";
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "120";
	$heading_width[1] = "25";
	$heading_width[2] = "15";
	$heading_width[3] = "25";
	$heading_width[4] = "15";
	
	//new format**************************************************
    $heading_text[0] = "$heading_name";
	$heading_text[1] = "บรรจุใหม่";
	$heading_text[2] = "รับโอน";
	$heading_text[3] = "บรรจุกลับ";
	$heading_text[4] = "รวม";
		
	$heading_align = array('C','C','C','C','C');


	function count_person($movement_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $arr_rpt_order, $search_per_type, $search_budget_year,$select_org_structure, $BKK_FLAG;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		switch($movement_type){
			case 1 :
				if ($BKK_FLAG==1)
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('1', '23', '41', '58', '054', '102', '103', '104', '20001'))";
				else
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('101', '10110', '10120', '10130'))";
			break;
			case 2 :
				if ($BKK_FLAG==1)
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('32', '33', '4', '105', '106'))";
				else
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('105', '10510', '10520'))";
			break;
			case 3 :
				if ($BKK_FLAG==1)
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('18', '2', '107', '108', '109', '110'))";
				else
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('10140'))";
			break;
		} // end switch case

		if($DPISDB=="odbc"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
			}else{
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
			} // end if
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_POSITIONHIS e
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
													and a.PER_ID=e.PER_ID(+)
													$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
			}else{
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
													and a.PER_ID=e.PER_ID(+)
													$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
			} // end if
		}elseif($DPISDB=="mysql"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
			}else{
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
			} // end if
		} // end if
		if($select_org_structure==1){ 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo $count_person." :: ".$cmd."<hr>";
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE, $EP_CODE, $TP_CODE;
		global $line_code;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else if($select_org_structure==1){
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
				case "ORG_2" :
					if($select_org_structure==0){
						if($ORG_ID_2) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID_2) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "LINE" :
						if($PL_CODE) $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
						else $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
				break;
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER or a.PER_GENDER is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(c.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(c.PV_CODE) = '$PV_CODE' or c.PV_CODE is null)";
				break;
				case "EDUCLEVEL" :
					if($EN_CODE) $arr_addition_condition[] = "(trim(d.EN_CODE) = '$EN_CODE')";
					else $arr_addition_condition[] = "(trim(d.EN_CODE) = '$EN_CODE' or d.EN_CODE is null)";
				break;
				case "EDUCMAJOR" :
					if($EM_CODE) $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE')";
					else $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE' or d.EM_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE, $EP_CODE, $TP_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :
					$ORG_ID_1 = -1;
				break;
				case "ORG_2" :
					$ORG_ID_2 = -1;
				break;
				case "LINE" :
					$PL_CODE = -1;
				break;
				case "SEX" :
					$PER_GENDER = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
					$PN_CODE = -1;
				break;
				case "EDUCLEVEL" :
					$EN_CODE = -1;
				break;
				case "EDUCMAJOR" :
					$EM_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	if($DPISDB=="odbc"){
		if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
			$cmd = " select			distinct $select_list
							 from			(
													(
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $condwhere
							 order by		$order_by ";
		}else{
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $condwhere
							 order by		$order_by ";
		} // end if
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
			$cmd = " select			distinct $select_list, a.PER_ID
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_POSITIONHIS e
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
												and a.PER_ID=e.PER_ID(+)
												$search_condition $condwhere
							 order by		$order_by ";
		}else{
			$cmd = " select			distinct $select_list, a.PER_ID
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.PER_ID=e.PER_ID(+)
												$search_condition $condwhere
							 order by		$order_by ";
		} // end if
	}elseif($DPISDB=="mysql"){
		if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
			$cmd = " select			distinct $select_list
							 from			(
													(
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $condwhere
							 order by		$order_by ";
		}else{
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $condwhere
							 order by		$order_by ";
		} // end if
	}
	if($select_org_structure==1){ 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
						} // end if
                      if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][short_name] = $ORG_SHORT;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						if($ORG_ID_1 != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							$ORG_SHORT_1 = $data2[ORG_SHORT];
						} // end if
                    if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][short_name] = $ORG_SHORT_1;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != trim($data[ORG_ID_2])){
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						if($ORG_ID_2 != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							$ORG_SHORT_2 = $data2[ORG_SHORT];
						} // end if
                  if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][short_name] = $ORG_SHORT_2;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;
		
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							if($search_per_type==1){
								$cmd = " select $line_name as PL_NAME, $line_short_name from $line_table b where trim($line_code)='$PL_CODE' ";
							}else{
								$cmd = " select $line_name as PL_NAME from $line_table b where trim($line_code)='$PL_CODE' ";
							}
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_NAME]);
							if($search_per_type==1){
								$PL_NAME = trim($data2[$line_short_name])?$data2[$line_short_name]:$PL_NAME;
							}
						} // end if
            if(($PL_NAME !="" && $PL_NAME !="-") || ($BKK_FLAG==1 && $PL_NAME !="" && $PL_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;
		
				case "SEX" :
					if($PER_GENDER != trim($data[PER_GENDER])){
						$PER_GENDER = trim($data[PER_GENDER]) + 0;

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (($PER_GENDER==1)?"ชาย":(($PER_GENDER==2)?"หญิง":""));
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "PROVINCE" :
					if($PV_CODE != trim($data[PV_CODE])){
						$PV_CODE = trim($data[PV_CODE]);
						if($PV_CODE != ""){
							$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PV_NAME = $data2[PV_NAME];
						} // end if
                   if(($PV_NAME !="" && $PV_NAME !="-") || ($BKK_FLAG==1 && $PV_NAME !="" && $PV_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "PROVINCE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;

				case "EDUCLEVEL" :
					if($EN_CODE != trim($data[EN_CODE])){
						$EN_CODE = trim($data[EN_CODE]);
						if($EN_CODE != ""){
							$cmd = " select EN_SHORTNAME, EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
//							$EN_NAME = trim($data2[EN_SHORTNAME])?$data2[EN_SHORTNAME]:$data2[EN_NAME];
							$EN_NAME = $data2[EN_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCLEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EN_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCMAJOR" :
					if($EM_CODE != trim($data[EM_CODE])){
						$EM_CODE = trim($data[EM_CODE]);
						if($EM_CODE != ""){
							$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EM_NAME = $data2[EM_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCMAJOR";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EM_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
		}//if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1))
	} // end while
	
	if(array_search("EDUCLEVEL", $arr_rpt_order) !== false  && array_search("EDUCLEVEL", $arr_rpt_order) == 0){
		$GRAND_TOTAL_1 = count_person(1, $search_condition, "");
		$GRAND_TOTAL_2 = count_person(2, $search_condition, "");
		$GRAND_TOTAL_3 = count_person(3, $search_condition, "");
	} // end if
	if(array_search("EDUCMAJOR", $arr_rpt_order) !== false  && array_search("EDUCMAJOR", $arr_rpt_order) == 0){
		$GRAND_TOTAL_1 = count_person(1, $search_condition, "");
		$GRAND_TOTAL_2 = count_person(2, $search_condition, "");
		$GRAND_TOTAL_3 = count_person(3, $search_condition, "");
	} // end if

	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3;
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

//new format****************************************************************
if($export_type=="report"){
	    if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$pdf->AutoPageBreak = false; 
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			$COUNT_2 = $arr_content[$data_count][count_2];
			$COUNT_3 = $arr_content[$data_count][count_3];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_2 + $COUNT_3;

		//new format************************************************************			
            	$arr_data = (array) null;
				$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME);
				$arr_data[] =($COUNT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_1)):number_format($COUNT_1)):"-");
				$arr_data[] =($COUNT_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_2)):number_format($COUNT_2)):"-");
				$arr_data[] =($COUNT_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_3)):number_format($COUNT_3)):"-");
				$arr_data[] = ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-");
		
				$data_align = array("L", "R", "R", "R", "R");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "cordia", "12", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
				$pdf->add_data_tab("", 0, "RHBL", $data_align, "cordia", "12", "", "000000", "");		// เส้นปิดบรรทัด			

	//new format************************************************************			
            	$arr_data = (array) null;
	        	$arr_data[] =รวม;
				$arr_data[] =($GRAND_TOTAL_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_1)):number_format($GRAND_TOTAL_1)):"-");
				$arr_data[] =($GRAND_TOTAL_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_2)):number_format($GRAND_TOTAL_2)):"-");
				$arr_data[] =($GRAND_TOTAL_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_3)):number_format($GRAND_TOTAL_3)):"-");
				$arr_data[] =($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-");
				
				$data_align = array("L", "R", "R", "R", "R");				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "cordia", "12", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$pdf->add_data_tab("", 0, "RHBL", $data_align, "cordia", "12", "b", "000000", "");		// เส้นปิดบรรทัด				
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
	}else if($export_type=="graph"){//if($export_type=="report"){
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
	$arr_categories = array();
	for($i=0;$i<count($arr_content);$i++){
		if($arr_content[$i][type]==$arr_rpt_order[0]){
			$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
			for($j=2;$j<count($arr_content_key);$j++){
				$arr_series_caption_data[$j][] = $arr_content[$i][$arr_content_key[$j]];
				}//for($j=2;$j<count($arr_content_key);$j++){
			}//if($arr_content[$i][type]==$arr_rpt_order[0]){
		}//for($i=0;$i<count($arr_content);$i++){
	for($j=3;$j<count($arr_content_key);$j++){
		$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]).";".${"GRAND_TOTAL_".($j-2)};
		}
//	echo "<pre>"; print_r($arr_series_list); echo "</pre>";
	
	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
	if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
	$selectedFormat = "SWF";
	$series_caption_list = "บรรจุใหม่;รับโอน;บรรจุกลับ";
	$categories_list = implode(";", $arr_categories).";รวม";
	if(strtolower($graph_type)=="pie"){
		$series_list = $GRAND_TOTAL_1.";".$GRAND_TOTAL_2.";".$GRAND_TOTAL_3;
		}else{
		$series_list = implode("|", $arr_series_list);
		}
//	echo($series_list);
	switch( strtolower($graph_type) ){
		case "column" :
			$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Column/style/column.scs";
			break;
		case "bar" :
			$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Bar/style/bar.scs";
			break;
		case "line" :
			$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Line/style/line.scs";
			break;
		case "pie" :
			$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Pie/style/pie.scs";
			break;
		} //switch( strtolower($graph_type) ){
	}//}else if($export_type=="graph"){
?>