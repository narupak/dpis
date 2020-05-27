<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "e.PL_NAME";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$type_code = "b.PT_CODE";
		$select_type_code = ", b.PT_CODE";
		$mgt_code = "b.PM_CODE";
		$select_mgt_code = ", b.PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "e.PN_NAME";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "e.EP_NAME";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=e.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "e.TP_NAME";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
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
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(g.SAH_KF_YEAR='$search_budget_year' and g.MOV_CODE in ('51', '21370', '21375'))";

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

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "บัญชีรายชื่อข้าราชการที่ไม่ได้เลื่อนขั้นเงินเดือน ปีงบประมาณ $search_budget_year";
	$report_code = "R0608";
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
    $search_budget_year="";

	$heading_width[0] = "12";
	$heading_width[1] = "65";
	$heading_width[2] = "58";
	$heading_width[3] = "15";
	$heading_width[4] = "20";
	$heading_width[5] = "30";
	
	//new format**************************************************
    $heading_text[0] = "ลำดับ|ที่";
	$heading_text[1] = "ส่วนราชการ/ชื่อ - สกุล|";
	$heading_text[2] = "ตำแหน่ง|";
	$heading_text[3] = "เลขที่|";
	$heading_text[4] = "เงินเดือน|";
	$heading_text[5] = "หมายเหตุ|";

	$heading_align = array('C','C','C','C','C','C');

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
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.SAH_REMARK $select_type_code $select_mgt_code
						 from			(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
											$search_condition
						 group by	$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)), 
											a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, g.SAH_REMARK $select_type_code $select_mgt_code
						 order by		g.SAH_REMARK, $order_by, a.PER_NAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, TO_NUMBER($position_no) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.SAH_REMARK $select_type_code $select_mgt_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_SALARYHIS g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and a.PER_ID=g.PER_ID
											$search_condition
						 group by	$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name, TO_NUMBER($position_no), 
											a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, g.SAH_REMARK $select_type_code $select_mgt_code
						 order by		g.SAH_REMARK, $order_by, a.PER_NAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.SAH_REMARK $select_type_code $select_mgt_code
						 from			(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
											$search_condition
						 group by	$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)), 
											a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, g.SAH_REMARK $select_type_code $select_mgt_code
						 order by		g.SAH_REMARK, $order_by, a.PER_NAME ";
	}       
/********
	if($search_per_type==1){
		if($DPISDB=="odbc"){
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
												a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, b.PT_CODE, f.PT_NAME, g.SAH_REMARK, b.PM_CODE, h.PM_NAME
							 from			(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) left join $line_table e on ($line_join)
														) left join PER_TYPE f on (b.PT_CODE=f.PT_CODE)
													) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
												$search_condition
							 group by	$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)), 
												a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, b.PT_CODE, f.PT_NAME, g.SAH_REMARK, b.PM_CODE, h.PM_NAME
							 order by		g.SAH_REMARK, $order_by, a.PER_NAME ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, TO_NUMBER($position_no) as POS_NO, 
												a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, b.PT_CODE, f.PT_NAME, g.SAH_REMARK, b.PM_CODE, h.PM_NAME
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_TYPE f, 
							 					PER_SALARYHIS g, PER_MGT h
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and $line_join(+) and b.PT_CODE=f.PT_CODE(+) and a.PER_ID=g.PER_ID and b.PM_CODE=h.PM_CODE(+)
												$search_condition
							 group by	$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, TO_NUMBER($position_no), 
												a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, b.PT_CODE, f.PT_NAME, g.SAH_REMARK, b.PM_CODE, h.PM_NAME
							 order by		g.SAH_REMARK, $order_by, a.PER_NAME ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
												a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, b.PT_CODE, f.PT_NAME, g.SAH_REMARK, b.PM_CODE, h.PM_NAME
							 from			(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) left join $line_table e on ($line_join)
														) left join PER_TYPE f on (b.PT_CODE=f.PT_CODE)
													) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
												$search_condition
							 group by	$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)), 
												a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, b.PT_CODE, f.PT_NAME, g.SAH_REMARK, b.PM_CODE, h.PM_NAME
							 order by		g.SAH_REMARK, $order_by, a.PER_NAME ";
		}
	}else{	// 2 || 3 || 4
		if($DPISDB=="odbc"){
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
												a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.SAH_REMARK
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
												$search_condition
							 group by	$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)), 
												a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, g.SAH_REMARK
							 order by		g.SAH_REMARK, $order_by, a.PER_NAME ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, TO_NUMBER($position_no) as POS_NO, 
												a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.SAH_REMARK
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_SALARYHIS g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and a.PER_ID=g.PER_ID
												$search_condition
							 group by	$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, TO_NUMBER($position_no), 
												a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, g.SAH_REMARK
							 order by		g.SAH_REMARK, $order_by, a.PER_NAME ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
												a.PER_SALARY, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.SAH_REMARK
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) inner join PER_SALARYHIS g on (a.PER_ID=g.PER_ID)
												$search_condition
							 group by	$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)), 
												a.PER_SALARY, $line_code, $line_name, a.LEVEL_NO, g.SAH_REMARK
							 order by		g.SAH_REMARK, $order_by, a.PER_NAME ";
		}
	} //end if
*******/
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd<br>";
	$data_count = $data_row = $REASON_COUNT = 0;
	$SAH_REMARK = -1;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		if($SAH_REMARK != trim($data[SAH_REMARK])){
			$SAH_REMARK = trim($data[SAH_REMARK]);
			$REASON_NAME = "";
			switch($SAH_REMARK){
				case 1 :
					$REASON_NAME = "ลาติดตามคู่สมรส";
					break;
				case 2 :
					$REASON_NAME = "เงินเดือนเต็มขั้น";
					break;
				case 3 :
					$REASON_NAME = "ลาฝึกอบรม/ดูงาน";
					break;
				case 4 :
					$REASON_NAME = "บรรจุใหม่";
					break;
			} // end switch case
			if (!$REASON_NAME) $REASON_NAME = $SAH_REMARK;
//			echo "REASON :: $SAH_REMARK :: $REASON_NAME<br>";
			
			$REASON_COUNT++;
			$arr_content[$data_count][type] = "REASON";
			$arr_content[$data_count][name] = $REASON_COUNT .".". $REASON_NAME;
			
			$data_count++;
			initialize_parameter(0);
		} // end if
		
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
					} // end if

					if($rpt_order_index == (count($arr_rpt_order) - 1)){
						$data_row++;
						
						$PER_ID = $data[PER_ID];
						$PN_NAME = trim($data[PN_NAME]);
						$PER_NAME = trim($data[PER_NAME]);
						$PER_SURNAME = trim($data[PER_SURNAME]);
						$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);
						$LEVEL_NO = $data[LEVEL_NO];
						$PER_SALARY = $data[PER_SALARY];
						if(trim( $type_code)){
							$PT_CODE = trim($data[PT_CODE]);
							$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PT_NAME = trim($data3[PT_NAME]);	
						}
						if(trim($mgt_code)){
							$PM_CODE = trim($data[PM_CODE]);
							$cmd = " select PM_NAME from PER_MGT where	PM_CODE=".$PM_CODE;
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PM_NAME = trim($data3[PM_NAME]);
						}
						$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
						$db_dpis3->send_cmd($cmd);
//						$db_dpis->show_error();
						$data3 = $db_dpis3->get_array();
						$LEVEL_NAME=$data3[LEVEL_NAME];
						$POSITION_LEVEL = $data3[POSITION_LEVEL];
						if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
	
						$arr_content[$data_count][type] = "DETAIL";
						$arr_content[$data_count][order] = $data_row;
						$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
						$arr_content[$data_count][pos_no] = $POS_NO;
						$arr_content[$data_count][position] = ($PM_CODE?"$PM_NAME ( ":"") . $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") . ($PM_CODE?" )":"");
						$arr_content[$data_count][salary] = $PER_SALARY;

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
//new format****************************************************************
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$pdf->AutoPageBreak = false; 
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$PER_SALARY = $arr_content[$data_count][salary];
			
			if($REPORT_ORDER == "REASON"){
				$arr_data = (array) null;
				$arr_data[] = $NAME;
			}else{			
				$arr_data = (array) null;
				$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER);
				$arr_data[] = $NAME;
				$arr_data[] = $POSITION;
				$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);       
				$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY);       
				
				$data_align = array("L", "L", "L", "C", "C");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "cordia", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		/*		$border = "";
				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$pdf->Cell(200, 7, "$NAME", $border, 1, 'L', 0);
	//			print_header();
			}else{			
			/*
				$border = "";
				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0){
					$pdf->SetFont($fontb,'',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				}else{
					$pdf->SetFont($font,'',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				} // end if
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[3], 7, "$POS_NO", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[4], 7, ($PER_SALARY?number_format($PER_SALARY):""), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[5], 7, "", $border, 0, 'L', 0);
	
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=5; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];

					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y - 10) < 15){ 
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
			//			print_header();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1) || $arr_content[($data_count + 1)][type] == "REASON") $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			*/		} // end if
	
		} // end for				
		$pdf->add_data_tab("", 7, "RHBL", $data_align, "cordia", "12", "", "000000", "");		// เส้นปิดบรรทัด				
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>