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
		$line_join = "b.PL_CODE=g.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "g.PL_NAME";
		$position_no_name = "b.POS_NO_NAME";
		$position_no= "b.POS_NO";
		$type_code = "b.PT_CODE";
		$select_type_code = ", b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "g.PN_NAME";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "g.EP_NAME";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "g.TP_NAME";
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

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(d.SAH_KF_YEAR = '$search_budget_year' and d.MOV_CODE in ('51', '21370', '21375'))";
	$arr_search_condition[] = "(d.SAH_KF_CYCLE = $search_salq_type)";

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
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type] ที่ไม่มีสิทธิ์ได้เลื่อนขั้นในปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type";
	$report_code = "R0603";
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
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "15";
	$heading_width[1] = "70";
	$heading_width[2] = "72";
	$heading_width[3] = "20";
	$heading_width[4] = "20";
	$heading_width[5] = "40";
	$heading_width[6] = "50";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"หน่วยงาน/ชื่อ - นามสกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"เลขที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เงินเดือน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"เหตุที่ไม่มีสิทธิ์",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"หมายเหตุ",'LTBR',1,'C',1);
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
		$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO, a.LEVEL_NO, 
										$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK $select_type_code
						  from		(
											(
												(
													(
														PER_PERSONAL a
														left join $position_table b on ($position_join)
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
											) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
										) left join $line_table g on ($line_join)
						 $search_condition
						 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO, a.LEVEL_NO, 
										$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK $select_type_code
						  from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_SALARYHIS d, PER_PRENAME f, $line_table g
						  where	$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
										and a.PER_ID=d.PER_ID and a.PN_CODE=f.PN_CODE(+) and $line_join(+)
										$search_condition
						 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO, a.LEVEL_NO, 
										$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK $select_type_code
						  from		(
											(
												(
													(
														PER_PERSONAL a
														left join $position_table b on ($position_join)
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
											) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
										) left join $line_table g on ($line_join)
						 $search_condition
						 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
	} // end if
/****
	if($search_per_type==1){	
		if($DPISDB=="odbc"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		(
												(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on ($line_join)
											) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
							 $search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_SALARYHIS d, PER_PRENAME f, $line_table g, PER_TYPE h
							  where	$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
							  				and a.PER_ID=d.PER_ID and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PT_CODE=h.PT_CODE(+)
											$search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		(
												(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on ($line_join)
											) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
							 $search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		} // end if
	}else{ // 2 ||  3 || 4
		if($DPISDB=="odbc"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
							 $search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_SALARYHIS d, PER_PRENAME f, $line_table g
							  where	$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
							  				and a.PER_ID=d.PER_ID and a.PN_CODE=f.PN_CODE(+) and $line_join(+)
											$search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
							 $search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		} // end if
	} ******/
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
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

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
		
		$data_row++;
		$PER_ID = $data[PER_ID];
		$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
		$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PL_NAME = trim($data[PL_NAME]);
		if(trim($type_code)){
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$PT_NAME = trim($data3[PT_NAME]);	
		}
		$PER_SALARY = $data[PER_SALARY];
		$SAH_REMARK = trim($data[SAH_REMARK]);
		
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
		$db_dpis3->send_cmd($cmd);
//		$db_dpis->show_error();
		$data3 = $db_dpis3->get_array();
		$LEVEL_NAME=$data3[LEVEL_NAME];
		$POSITION_LEVEL = $data3[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
		switch($SAH_REMARK){
			case 1 :
				$SAH_REMARK = "ลาติดตามคู่สมรส";
				break;
			case 2 :
				$SAH_REMARK = "เงินเดือนเต็มขั้น";
				break;
			case 3 :
				$SAH_REMARK = "ลาฝึกอบรม/ดูงาน";
				break;
			case 4 :
				$SAH_REMARK = "บรรจุใหม่";
				break;
			default :
				$SAH_REMARK = "";
				break;
		} // end switch case
		$SAH_REMARK = trim($data[SAH_REMARK]);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PER_NAME;
		$arr_content[$data_count][position] = (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME);
		$arr_content[$data_count][posno] = $POS_NO;
		$arr_content[$data_count][per_salary] = $PER_SALARY;
		$arr_content[$data_count][SAH_REMARK] = $SAH_REMARK;
		$arr_content[$data_count][SAH_REMARK] = $SAH_REMARK;

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$POS_NO = $arr_content[$data_count][posno];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$SAH_REMARK = $arr_content[$data_count][SAH_REMARK];
			$SAH_REMARK = $arr_content[$data_count][SAH_REMARK];
			
			$SAH_REMARK = eregi_replace("<br>", " ", $SAH_REMARK);
			
			$border = "";
			if($REPORT_ORDER == "ORG"){
				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $ORDER, $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, "$POSITION", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[3], 7, $POS_NO, $border, 0, 'C', 0);
			$pdf->Cell($heading_width[4], 7, ($PER_SALARY?number_format($PER_SALARY):""), $border, 0, 'R', 0);
			$pdf->MultiCell($heading_width[5], 7, "$SAH_REMARK", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[6], 7, "$SAH_REMARK", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
			$pdf->y = $start_y;

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=6; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for				
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
	
?>