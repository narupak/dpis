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
	if($search_per_type==1) 
	elseif($search_per_type==2) 
	elseif($search_per_type==3) 
	$report_code = "R0613/$search_salq_type";

	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$report_title = "$DEPARTMENT_NAME||การจัดสรรโควตาเลื่อนขั้นเงินเดือนประจำปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type|| ".(($search_salq_type==1)?"1 เมษายน $search_budget_year":"1 ตุลาคม $search_budget_year")." จำแนกตาม ".(($select_org_structure==1)?"โครงสร้างตามมอบหมายงาน":"โครงสร้างตามกฎหมาย")." ระดับ 8 ลงมา";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$report_title = "$DEPARTMENT_NAME||การจัดสรรโควตาเลื่อนขั้นเงินเดือนประจำปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type|| ".(($search_salq_type==1)?"1 เมษายน $search_budget_year":"1 ตุลาคม $search_budget_year")." จำแนกตาม ".(($select_org_structure==1)?"โครงสร้างตามมอบหมายงาน":"โครงสร้างตามกฎหมาย")." ระดับ 8 ลงมา";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$report_title = "$DEPARTMENT_NAME||การจัดสรรโควตาเลื่อนขั้นเงินเดือนประจำปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type|| ".(($search_salq_type==1)?"1 เมษายน $search_budget_year":"1 ตุลาคม $search_budget_year")." จำแนกตาม ".(($select_org_structure==1)?"โครงสร้างตามมอบหมายงาน":"โครงสร้างตามกฎหมาย")." ระดับ 8 ลงมา";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		//$report_title = "$DEPARTMENT_NAME||การจัดสรรโควตาเลื่อนขั้นเงินเดือนประจำปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type|| ".(($search_salq_type==1)?"1 เมษายน $search_budget_year":"1 ตุลาคม $search_budget_year")." จำแนกตาม ".(($select_org_structure==1)?"โครงสร้างตามมอบหมายงาน":"โครงสร้างตามกฎหมาย")." ระดับ 8 ลงมา";
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
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_code = "R0613/$search_salq_type";
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

	$heading_width[0] = "10";
	$heading_width[1] = "97";
	$heading_width[2] = "10";
	$heading_width[3] = "20";
	$heading_width[4] = "20";
	$heading_width[5] = "20";
	$heading_width[6] = "20";
	$heading_width[7] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $SALQ_PERCENT;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"สังกัด",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] * 8) ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"รวมทั้งสิ้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"จำนวนคนที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"คงเหลือ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"โควตา",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"จัดสรร",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		for($i=1; $i<=8; $i++) $pdf->Cell($heading_width[2] ,7,"$i",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"(คน)",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ไม่ได้เลื่อน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"(คน)",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ร้อยละ $SALQ_PERCENT",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"(ร้อยละ)",'LBR',1,'C',1);
	} // function		

	function count_person($level_no, $salp_yn, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2,$position_table,$position_join;
		global $search_per_type, $search_salq_type, $search_budget_year, $select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($level_no){
			$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
			if($DPISDB=="odbc") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="oci8") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="mysql") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
		} // end if
		
		if($salp_yn !== "") $search_condition .= (trim($search_condition)?" and ":" where ") . "(d.SALQ_YEAR='$search_budget_year' and d.SALQ_TYPE=$search_salq_type and d.SALP_YN=$salp_yn)";
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														PER_PERSONAL a 
														left join $position_table b on ($position_join) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
							$search_condition
							group by		a.PER_ID ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SALPROMOTE d
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+)
												$search_condition
							 group by 	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														PER_PERSONAL a 
														left join $position_table b on ($position_join) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
							$search_condition
							group by		a.PER_ID ";
		} // end if
/******
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
								$search_condition
								group by		a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_SALPROMOTE d
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+)
													$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
								$search_condition
								group by		a.PER_ID ";
			} // end if
		}elseif($search_per_type==2){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
								$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_SALPROMOTE d
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+)
													$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
								$search_condition
								 group by 	a.PER_ID ";
			} // end if
		} elseif($search_per_type==3){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
								$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_SALPROMOTE d
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+)
													$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_SALPROMOTE d on (a.PER_ID=d.PER_ID)
								$search_condition
								 group by 	a.PER_ID ";
			} // end if
		} // end if  *********/
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
	
	$cmd = " select SALQ_PERCENT from PER_SALQUOTA where SALQ_YEAR='$search_budget_year' and SALQ_TYPE=$search_salq_type ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$SALQ_PERCENT = $data[SALQ_PERCENT] + 0;
	
	if($select_org_structure==0)
		$cmd = " select ORG_ID, SALQD_QTY1, SALQD_QTY2 from PER_SALQUOTADTL1 where SALQ_YEAR='$search_budget_year' and SALQ_TYPE=$search_salq_type ";
	elseif($select_org_structure==1)
		$cmd = " select ORG_ID, SALQD_QTY1, SALQD_QTY2 from PER_SALQUOTADTL2 where SALQ_YEAR='$search_budget_year' and SALQ_TYPE=$search_salq_type ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){ 
		$arr_quota[$data[ORG_ID]][QTY1] = $data[SALQD_QTY1];
		$arr_quota[$data[ORG_ID]][QTY2] = $data[SALQD_QTY2];
	} // end while

	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_PERSONAL a 
												left join $position_table b on ($position_join)
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_PERSONAL a 
												left join $position_table b on ($position_join)
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}
	/***
	if($search_per_type==1){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}
	}elseif($search_per_type==2){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}
	} elseif($search_per_type==3){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}
	} // end if ***/
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	unset($GRAND_LEVEL);
	$GRAND_ALL = $GRAND_UNPROMOTED = $GRAND_DIFF = $GRAND_QUOTA = $GRAND_ASSIGN = 0;
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
						for($i=1; $i<=8; $i++){
							$arr_content[$data_count]["count_".$i] = count_person($i, "", $search_condition, $addition_condition);
							$GRAND_LEVEL[$i] += $arr_content[$data_count]["count_".$i];
						} // end for
						$arr_content[$data_count][count_all] = count_person(0, "", $search_condition, $addition_condition);
						$arr_content[$data_count][count_unpromoted] = count_person(0, 0, $search_condition, $addition_condition);
						$arr_content[$data_count][count_diff] = $arr_content[$data_count][count_all] - $arr_content[$data_count][count_unpromoted];
						$arr_content[$data_count][count_quota] = $arr_quota[$ORG_ID][QTY1];
						$arr_content[$data_count][count_assign] = $arr_quota[$ORG_ID][QTY2];

						$GRAND_ALL += $arr_content[$data_count][count_all];
						$GRAND_UNPROMOTED += $arr_content[$data_count][count_unpromoted];
						$GRAND_DIFF += $arr_content[$data_count][count_diff];
						$GRAND_QUOTA += $arr_content[$data_count][count_quota];
						$GRAND_ASSIGN += $arr_content[$data_count][count_assign];

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for		
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			for($i=1; $i<=8; $i++) ${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
			$COUNT_ALL = $arr_content[$data_count][count_all];
			$COUNT_UNPROMOTED = $arr_content[$data_count][count_unpromoted];
			$COUNT_DIFF = $arr_content[$data_count][count_diff];
			$COUNT_QUOTA = $arr_content[$data_count][count_quota];
			$COUNT_ASSIGN = $arr_content[$data_count][count_assign];
			
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			for($i=1; $i<=8; $i++) $pdf->Cell($heading_width[2], 7, (${"COUNT_".$i}?number_format(${"COUNT_".$i}):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[3], 7, ($COUNT_ALL?number_format($COUNT_ALL):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[4], 7, ($COUNT_UNPROMOTED?number_format($COUNT_UNPROMOTED):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[5], 7, ($COUNT_DIFF?number_format($COUNT_DIFF):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[6], 7, ($COUNT_QUOTA?number_format($COUNT_QUOTA, 2):"-"), $border, 0, 'R', 0);
			if($search_salq_type==1)
				$pdf->Cell($heading_width[7], 7, ($COUNT_ASSIGN?number_format($COUNT_ASSIGN):"-"), $border, 0, 'R', 0);
			elseif($search_salq_type==2)
				$pdf->Cell($heading_width[7], 7, ($COUNT_ASSIGN?number_format($COUNT_ASSIGN, 2):"-"), $border, 0, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=14; $i++){
				if($i <= 1){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				}elseif($i <= 9){
					$line_start_y = $start_y;		$line_start_x += $heading_width[2];
					$line_end_y = $max_y;		$line_end_x += $heading_width[2];
				}else{
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i - 7];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i - 7];
				} // end if
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

		$border = "LTBR";
		$pdf->SetFont($fontb,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->Cell($heading_width[0], 7, "", $border, 0, 'L', 0);
		$pdf->Cell($heading_width[1], 7, "รวมทั้งสิ้น", $border, 0, 'L', 0);
		for($i=1; $i<=8; $i++) $pdf->Cell($heading_width[2], 7, ($GRAND_LEVEL[$i]?number_format($GRAND_LEVEL[$i]):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[3], 7, ($GRAND_ALL?number_format($GRAND_ALL):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[4], 7, ($GRAND_UNPROMOTED?number_format($GRAND_UNPROMOTED):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[5], 7, ($GRAND_DIFF?number_format($GRAND_DIFF):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[6], 7, ($GRAND_QUOTA?number_format($GRAND_QUOTA, 2):"-"), $border, 0, 'R', 0);
		if($search_salq_type==1)
			$pdf->Cell($heading_width[7], 7, ($GRAND_ASSIGN?number_format($GRAND_ASSIGN):"-"), $border, 1, 'R', 0);
		elseif($search_salq_type==2)
			$pdf->Cell($heading_width[7], 7, ($GRAND_ASSIGN?number_format($GRAND_ASSIGN, 2):"-"), $border, 1, 'R', 0);
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>