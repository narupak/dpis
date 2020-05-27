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
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID";

				$heading_name .= " สำนัก/กอง";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_1";

				$heading_name .= " ฝ่าย";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_2";

				$heading_name .= " งาน";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				if($search_per_type==1) $select_list .= "b.PL_CODE";
				elseif($search_per_type==2) $select_list .= "b.PN_CODE";
				elseif($search_per_type==3) $select_list .= "b.EP_CODE";

				if($order_by) $order_by .= ", ";
				if($search_per_type==1) $order_by .= "b.PL_CODE";
				elseif($search_per_type==2) $order_by .= "b.PN_CODE";
				elseif($search_per_type==3) $order_by .= "b.EP_CODE";

				if($search_per_type==1) $heading_name .= " สายงาน";
				elseif($search_per_type==2) $heading_name .= " ชื่อตำแหน่ง";
				elseif($search_per_type==3) $heading_name .= " ชื่อตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "b.ORG_ID";
	if(!trim($select_list)) $select_list = "b.ORG_ID";

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
//	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(e.MOV_CODE) in ('123', '12310'))";		//ค้นหารายชื่อข้าราชการที่มีประเภทการเคลื่อนไหวคือ ตาย

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";

	$list_type_text = "ทั้งส่วนราชการ";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG"){
		// สำนัก/กอง , ฝ่าย , งาน
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(e.ORG_ID_3 = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(e.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(e.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		} // end if
		elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(e.EP_CODE)='$search_ep_code')";
			$list_type_text .= "$search_ep_name";
		} // end if
	}elseif($list_type == "PER_COUNTRY"){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}else{
		// ทั้งส่วนราชการ
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
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if($search_per_type==1) $type_name="ข้าราชการ";
	elseif($search_per_type==2) $type_name="ลูกจ้างประจำ";
	elseif($search_per_type==3) $type_name="พนักงานราชการ";
	
	$report_title = "แบบสำรวจ$type_name ที่เสียชีวิตในปีงบประมาณ $search_budget_year || ส่วนราชการ $DEPARTMENT_NAME";
	$report_code = "R20007";
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

	$heading_width[0] = "10";
	$heading_width[1] = "15";
	$heading_width[2] = "40";
	$heading_width[3] = "48";
	$heading_width[4] = "25";
	$heading_width[5] = "25";
	$heading_width[6] = "35";
	$heading_width[7] = "25";
	$heading_width[8] = "25";
	$heading_width[9] = "32";

	function print_header(){
		global $pdf, $heading_width, $heading_name;

		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"คำนำ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ชื่อ-สกุล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ชื่อตำแหน่งในสายงาน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"จังหวัดที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"วัน/เดือน/ปี",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"วัน/เดือน/ปี",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"วัน/เดือน/ปี",'LTR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"สาเหตุ",'LTR',1,'C',1);
		
		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"หน้านาม",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ที่ปฏิบัติงาน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"เกิด",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ที่บรรจุ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ที่เสียชีวิต",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"ที่เสียชีวิต",'LBR',1,'C',1);
	} // function

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3,$RPT_N;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $month_abbr,$select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($search_per_type==1){
			// ข้าราชการ
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.PT_CODE, e.MOV_CODE
								 from			(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
														) left join PER_LINE f on (e.PL_CODE=f.PL_CODE)
													) left join PER_TYPE g on (e.PT_CODE=g.PT_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, e.PT_CODE, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, 
													SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,
													MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, e.PT_CODE, e.MOV_CODE
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_LINE f, PER_TYPE g
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and a.PER_ID=e.PER_ID(+) and e.PL_CODE=f.PL_CODE(+) and e.PT_CODE=g.PT_CODE(+)
													$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, e.PT_CODE, 
								 					SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10), e.MOV_CODE
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.PT_CODE, e.MOV_CODE
								 from			(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
														) left join PER_LINE f on (e.PL_CODE=f.PL_CODE)
													) left join PER_TYPE g on (e.PT_CODE=g.PT_CODE)
								$search_condition
								group by	 	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, e.PT_CODE, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			} // end if
		}elseif($search_per_type==2){
			// ลูกจ้าง
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME as PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_POS_NAME f on (e.PN_CODE=f.PN_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME, e.LEVEL_NO, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME as PL_NAME, e.LEVEL_NO, 
													SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,
													MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_POS_NAME f
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and a.PER_ID=e.PER_ID(+) and e.PN_CODE=f.PN_CODE(+)
													$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME, e.LEVEL_NO, 
								 					SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10), e.MOV_CODE 
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME as PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_POS_NAME f on (e.PN_CODE=f.PN_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME, e.LEVEL_NO, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			} // end if
		} // end if
		elseif($search_per_type==3){
			// พนักงานราชการ
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME as PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_EMPSER_POS_NAME f on (e.EP_CODE=f.EP_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME, e.LEVEL_NO, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME as PL_NAME, e.LEVEL_NO, 
													SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,
													MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_EMPSER_POS_NAME f
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and a.PER_ID=e.PER_ID(+) and e.EP_CODE=f.EP_CODE(+)
													$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME, e.LEVEL_NO, 
								 					SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10), e.MOV_CODE 
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME as PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_EMPSER_POS_NAME f on (e.EP_CODE=f.EP_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME, e.LEVEL_NO, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			} // end if
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
//		echo "<br>$cmd<br>";
//		$db_dpis2->show_error();
		while($data = $db_dpis2->get_array()){
			$person_count++;
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PL_NAME = trim($data[PL_NAME]);
			
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' order by LEVEL_SEQ_NO";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$arr_temp = explode(" ", trim($data2[LEVEL_NAME]));
			//หาชื่อตำแหน่งประเภท
			if($search_per_type==1){
				$POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
			}elseif($search_per_type==2){
				$POSITION_TYPE = $arr_temp[0];
			}elseif($search_per_type==3){
				$POSITION_TYPE = str_replace("กลุ่มงาน", "", $arr_temp[0]);
			}
			//หาชื่อระดับตำแหน่ง 
			$LEVEL_NAME =  trim(str_replace("ระดับ", "",$arr_temp[1]));
			
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", $PER_BIRTHDATE);
				$PER_BIRTHDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			}
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			if($PER_STARTDATE){
				$arr_temp = explode("-", $PER_STARTDATE);
				$PER_STARTDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			}
			$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
			if($POH_EFFECTIVEDATE){
				$arr_temp = explode("-", $POH_EFFECTIVEDATE);
				$POH_EFFECTIVEDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if			

			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data2 = $db_dpis3->get_array();
			$PT_NAME = $data2[PT_NAME];
			
			$MOV_CODE = trim($data[MOV_CODE]);
			$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data2 = $db_dpis3->get_array();
			$MOV_NAME = $data2[MOV_NAME];

			/***
			//หาวุฒิการศึกษาสูงสุด
			$cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%||4||%'
							 order by a.EDU_SEQ desc,a.EN_CODE
						  ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
//			echo "<br>$cmd<br>";
			$data2 = $db_dpis3->get_array();
			$EDU_MAX = $data2[EN_NAME];
			***/
			
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = $person_count .". ";
			$arr_content[$data_count][prename] = $PN_NAME;
			$arr_content[$data_count][name] = 	    $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = trim($PL_NAME)?($PL_NAME ." ". $POSITION_TYPE . (($PT_CODE != "11" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"ระดับ ".$LEVEL_NAME;
			$arr_content[$data_count][levelname] = $LEVEL_NAME;
			$arr_content[$data_count][province_work] = $PROVINCE_WORK;	
			///$arr_content[$data_count][edu_max] = $EDU_MAX;
			$arr_content[$data_count][birthdate]	= $PER_BIRTHDATE;
			$arr_content[$data_count][startdate]	= $PER_STARTDATE;			//วันบรรจุ
			$arr_content[$data_count][deaddate] = $PER_DEADDATE;			//วันที่เสียชีวิต
			$arr_content[$data_count][reason] = $MOV_NAME;						//สาเหตุที่เสียชีวิต
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
					else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($select_org_structure==0){
						if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}
				break;
				case "ORG_2" :
					if($select_org_structure==0){
						if($ORG_ID_2) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
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
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
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
			} // end switch case
		} // end for
	} // function
	
	//แสดงรายชื่อหน่วยงาน
	if($search_per_type==1){
		// ข้าราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_POSITIONHIS e
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}
	}elseif($search_per_type==2){
		// ลูกจ้าง
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_POSITIONHIS e
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}
	} // end if
	elseif($search_per_type==3){
		// พนักงานราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_POSITIONHIS e
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}
	} // end if
	if($select_org_structure==1){	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "<br>$cmd<br>";
	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
//					echo "ORG => $ORG_ID :: $data[ORG_ID]<br>";
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
//							$ORG_NAME = "[ไม่ระบุสำนัก/กอง]";
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 9)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
//							$ORG_NAME_1 = "[ไม่ระบุฝ่าย]";
						}else{
							if($select_org_structure==0) $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 9)) . $ORG_NAME_1;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
//							$ORG_NAME_2 = "[ไม่ระบุงาน]";
						}else{
							if($select_org_structure==0) $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 9)) . $ORG_NAME_2;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);									
						list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;		
			} // end switch case
		} // end for
		
		if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition);
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$PRE_NAME = $arr_content[$data_count][prename];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL_NAME = $arr_content[$data_count][levelname];
			$PROVINCE_WORK =  $arr_content[$data_count][province_work];	
			///$EDU_MAX = $arr_content[$data_count][edu_max];	
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			$STARTDATE = $arr_content[$data_count][startdate];
			$DEADDATE = $arr_content[$data_count][deaddate];	
			$MOV_NAME = $arr_content[$data_count][reason];
			
			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$pdf->SetFont('angsab','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'L', 0);
			$pdf->Cell($heading_width[1], 7, "$PRE_NAME", $border, 0, 'L', 0);
			$pdf->MultiCell($heading_width[2], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, "$POSITION", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[4], 7, "$LEVEL_NAME", $border, 0, 'L', 0);
			$pdf->Cell($heading_width[5], 7, "$PROVINCE_WORK", $border, 0, 'L', 0);
			$pdf->Cell($heading_width[6], 7, "$BIRTHDATE", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[7], 7, "$STARTDATE", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[8], 7, "$DEADDATE", $border, 0, 'L', 0);
			$pdf->MultiCell($heading_width[9], 7, "$MOV_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9];
			$pdf->y = $start_y;
			
			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=9; $i++){
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
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>