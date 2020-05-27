<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE"); 
/*
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

				if($order_by) $order_by .= ", ";
				if($search_per_type==1) $order_by .= "b.PL_CODE";
				elseif($search_per_type==2) $order_by .= "b.PN_CODE";

				if($search_per_type==1) $heading_name .= " สายงาน";
				elseif($search_per_type==2) $heading_name .= " ชื่อตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "b.ORG_ID";
	if(!trim($select_list)) $select_list = "b.ORG_ID";
*/
	$search_level_no = trim($search_level_no);
	$search_condition = $search_condition1 = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
//	$arr_search_condition[] = "(trim(e.MOV_CODE) in ('10110', '10120', '10130', '10410', '10420', '10430', '10440', '10450', '10460', '10510', '10520') and rtrim(e.LEVEL_NO) = '$search_level_no')";
//	$arr_search_condition[] = "(rtrim(e.LEVEL_NO) = '$search_level_no')";
	$arr_search_condition[] = "(rtrim(a.LEVEL_NO) = '$search_level_no')";
	$arr_search_condition[] = "(i.DC_TYPE in (1,2))";

	$list_type_text = "ทั้งส่วนราชการ";

//	include ("../report/rpt_condition3.php");
	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		// สำนัก/กอง , ฝ่าย , งาน
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		}elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(b.EP_CODE)='$search_ep_code')";
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
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);	
	
	$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$search_level_no' ";
	$db_dpis2->send_cmd($cmd2);
	//$db_dpis2->show_error();
	$data=$db_dpis2->get_array();
	$level_name=$data[LEVEL_NAME];

	if($DPISDB=="odbc"){  
			///$search_condition .= " and (rtrim(e.LEVEL_NO) <>'$search_level_no') "; 
			$search_condition1 .= $search_condition;
			$search_condition1 .= " and rtrim(e.LEVEL_NO)='$search_level_no'"; 		//สำหรับที่ไม่มีประวัติการดำรงตำแหน่งก่อนหน้า มาถึงก็เป็นระดับนี้เลย (1 record)
	if ($search_level_no=="O1")
		$search_condition .= " and e.LEVEL_NO in('04','03','02','01') ";
	elseif ($search_level_no=="O2")
		$search_condition .= " and e.LEVEL_NO in('06','05','04','O1') ";
	elseif ($search_level_no=="O3")
		$search_condition .= " and e.LEVEL_NO in('08','07','06','O2') ";
	elseif ($search_level_no=="O4")
		$search_condition .= " and e.LEVEL_NO in('09','08','O3') ";
	elseif ($search_level_no=="K1")
		$search_condition .= " and e.LEVEL_NO in('05','04','03') ";
	elseif ($search_level_no=="K2")
		$search_condition .= " and e.LEVEL_NO in('07','06','05') ";
	elseif ($search_level_no=="K3")
		$search_condition .= " and e.LEVEL_NO in('08','07','K2') ";
	elseif ($search_level_no=="K4")
		$search_condition .= " and e.LEVEL_NO in('09','08','K3') ";
	elseif ($search_level_no=="K5")
		$search_condition .= " and e.LEVEL_NO in('10','09','K4') ";
	elseif ($search_level_no=="D1")
		$search_condition .= " and e.LEVEL_NO in('08','07') ";
	elseif ($search_level_no=="D2")
		$search_condition .= " and e.LEVEL_NO in('09','08','D1') ";
	elseif ($search_level_no=="M1")
		$search_condition .= " and e.LEVEL_NO in('09','08') ";
	elseif ($search_level_no=="M2")
		$search_condition .= " and e.LEVEL_NO in('11','10','09','M1') ";	
	} else {
			///$search_condition .= " and (rtrim(e.LEVEL_NO) !='$search_level_no') "; 
			$search_condition1 .= $search_condition;
			$search_condition1 .= " and rtrim(e.LEVEL_NO)='$search_level_no'"; 		//สำหรับที่ไม่มีประวัติการดำรงตำแหน่งก่อนหน้า มาถึงก็เป็นระดับนี้เลย (1 record)
	if ($search_level_no=="O1")
		$search_condition .= " and e.LEVEL_NO in('04','03','02','01') ";
	elseif ($search_level_no=="O2")
		$search_condition .= " and e.LEVEL_NO in('06','05','04','O1') ";
	elseif ($search_level_no=="O3")
		$search_condition .= " and e.LEVEL_NO in('08','07','06','O2') ";
	elseif ($search_level_no=="O4")
		$search_condition .= " and e.LEVEL_NO in('09','08','O3') ";
	elseif ($search_level_no=="K1")
		$search_condition .= " and e.LEVEL_NO in('05','04','03') ";
	elseif ($search_level_no=="K2")
		$search_condition .= " and e.LEVEL_NO in('07','06','05','K1') ";
	elseif ($search_level_no=="K3")
		$search_condition .= " and e.LEVEL_NO in('08','07','K2') ";
	elseif ($search_level_no=="K4")
		$search_condition .= " and e.LEVEL_NO in('09','08','K3') ";
	elseif ($search_level_no=="K5")
		$search_condition .= " and e.LEVEL_NO in('10','09','K4') ";
	elseif ($search_level_no=="D1")
		$search_condition .= " and e.LEVEL_NO in('08','07') ";
	elseif ($search_level_no=="D2")
		$search_condition .= " and e.LEVEL_NO in('09','08','D1') ";
	elseif ($search_level_no=="M1")
		$search_condition .= " and e.LEVEL_NO in('09','08') ";
	elseif ($search_level_no=="M2")
		$search_condition .= " and e.LEVEL_NO in('11','10','09','M1') ";
	}
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if($search_per_type==1) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการที่ดำรงตำแหน่งในระดับ ". level_no_format($level_name) ." เรียงตามลำดับอาวุโส";
	elseif($search_per_type==2) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างที่ดำรงตำแหน่งในระดับ ". level_no_format($level_name) ." เรียงตามลำดับอาวุโส";
	elseif($search_per_type==3) $report_title = "$DEPARTMENT_NAME||รายชื่อพนักงานราชการที่ดำรงตำแหน่งในระดับ ". level_no_format($level_name) ." เรียงตามลำดับอาวุโส";
	$report_code = "R0403";
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

	$heading_width[0] = "30";
	$heading_width[1] = "65";
	$heading_width[2] = "15";
	$heading_width[3] = "25";
	$heading_width[4] = "25";
	$heading_width[5] = "20";
	$heading_width[6] = "12";
	$heading_width[7] = "15";
	$heading_width[8] = "20";
	$heading_width[9] = "20";
	$heading_width[10] = "20";
	$heading_width[11] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ชื่อ - สกุล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง/ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"เลขที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"วุฒิ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"สาขา",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"วันเข้าสู่ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"เงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"เครื่องราชฯ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"วันบรรจุ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"วันเดือนปีเกิด",'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"วันเกษียณ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"สำนัก/กอง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"วิชาเอก",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"(อายุราชการ)",'LBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"(อายุตัว)",'LBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"",'LBR',1,'C',1);
	} // function		

	if($search_per_type==1){
		// ข้าราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, 
												c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER				
					   from			(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_LINE f on (b.PL_CODE=f.PL_CODE)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition and (e.LEVEL_NO < a.LEVEL_NO)
					    group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by  e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) 
						 ";
		}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
			$cmd = " select 	distinct e.PER_ID, a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 		
											a.PER_RETIREDATE,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER
								from PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_LINE f, PER_TYPE g, PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j 
								where a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID(+) and b.PL_CODE=f.PL_CODE(+) 
											and b.PT_CODE=g.PT_CODE(+) and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+) 
								$search_condition 											
								 group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
 											 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
								 order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),a.PER_SALARY desc,MAX(i.DC_ORDER) desc,
								 			SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) 
								"; 
								//**การเปลี่ยน group by/order by มีผลต่อการแสดงผลที่จะไม่ดึงวันที่เริ่มต้นเข้าสู่ระดับ ของระดับตำแหน่งก่อนหน้าจะเข้าสู่ระดับปัจจุบัน
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, 
												c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER	
				 	   from			(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and e.LEVEL_NO < a.LEVEL_NO)
												) left join PER_LINE f on (b.PL_CODE=f.PL_CODE)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition
				 	group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
					order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10)  ";
		}
	}elseif($search_per_type==2){
		// ลูกจ้าง
		if($DPISDB=="odbc"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER					 
					 	  from			(
										(
											(
												(
													(
														(
															(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_POS_NAME f on (b.PN_CODE=f.PN_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								 and e.LEVEL_NO in$arr_level_no[$search_level_no]
								$search_condition
						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								 LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) 				  
						";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 							
											c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER 
					   from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_POS_NAME f,
					   				PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j
				 	   where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
									and a.PER_ID=e.PER_ID(+) and b.PN_CODE=f.PN_CODE(+)
									and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+)  and e.LEVEL_NO in$arr_level_no[$search_level_no]
									$search_condition
 						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME, a.LEVEL_NO,
 									 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
						 order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
						 			SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";	
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER			
					   from			(
										(
											(
												(
													(
														(
															(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_POS_NAME f on (b.PN_CODE=f.PN_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								 and e.LEVEL_NO in $arr_level_no[$search_level_no]
								$search_condition
						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								 LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";
		}
	} // end if
	elseif($search_per_type==3){
		// พนักงานราชการ
		if($DPISDB=="odbc"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER	
					   from			(
										(
											(
												(
													(
														(
															(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_EMPSER_POS_NAME f on (b.EP_CODE=f.EP_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								and e.LEVEL_NO in $arr_level_no[$search_level_no]
								$search_condition
						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10) 							
						";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 							
											c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER 			 
						   from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_EMPSER_POS_NAME f,
					   				PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j
				 	   where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
									and a.PER_ID=e.PER_ID(+) and b.EP_CODE=f.EP_CODE(+)
									and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+)  and e.LEVEL_NO in$arr_level_no[$search_level_no]
									$search_condition
 						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME, a.LEVEL_NO,
 									 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
						 order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
						 			 SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";		
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER	
				 	   from			(
										(
											(
												(
													(
														(
															(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_EMPSER_POS_NAME f on (b.EP_CODE=f.EP_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j (a.LEVEL_NO=j.LEVEL_NO)
								 and e.LEVEL_NO in$arr_level_no[$search_level_no]
								$search_condition
						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";
		}
	} // end if
	//สร้าง query ใหม่ สำหรับข้อมูล record เดียว
	$cmd1=$cmd;
	$cmd1 = str_replace($search_condition,$search_condition1,$cmd1); 
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("a.ORG_ID_1", "b.ORG_ID_1", $cmd);
		$cmd = str_replace("a.ORG_ID_2", "b.ORG_ID_2", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}

	$data_count = 0;
	$person_count = 0;
	//ประมวลผลข้าราชการที่ดำรงตำแหน่ง ตามiระดับตำแหน่งที่เลือกมา
	$count_data1 = $db_dpis->send_cmd($cmd1);
	while($data = $db_dpis->get_array()){
		$ARRALL_PERID_LEVELNO[] = $data[PER_ID];
	}	//end while
//==========================================
	
	$count_data = $db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){		
		if($PER_ID == $data[PER_ID]) continue;
		//$person_count++;		
		
		$PER_ID = $data[PER_ID];
		if(in_array($PER_ID,$ARRALL_PERID_LEVELNO)){
			$ARRALL_HAVE_PERID_LEVELNO[] = $PER_ID;
		}
		$PN_NAME[$PER_ID] = trim($data[PN_NAME]);
		$PER_NAME[$PER_ID] = trim($data[PER_NAME]);
		$PER_SURNAME[$PER_ID] = trim($data[PER_SURNAME]);
		$POS_NO[$PER_ID] = trim($data[POS_NO]);
		$PL_NAME[$PER_ID] = trim($data[PL_NAME]);
		$LEVEL_NO[$PER_ID] = trim($data[LEVEL_NO]);
		$LEVEL_NOHIS = trim($data[LEVEL_NOHIS]);
		$ARRSORTLEVEL[$PER_ID]['per_sortlevel'] = $LEVEL_NOHIS;		//--------
		$LEVEL_NAME[$PER_ID] = trim($data[LEVEL_NAME]);
		$PT_CODE[$PER_ID] = trim($data[PT_CODE]);
		$PT_NAME[$PER_ID] = trim($data[PT_NAME]);
		$ORG_NAME[$PER_ID] = trim($data[ORG_NAME]);
		$ORG_ID_1=trim($data[ORG_ID_1]);
		$PER_RETIREDATE=trim($data[PER_RETIREDATE]);
		$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
		if(trim($POH_EFFECTIVEDATE)){
			$ARRSORTLEVEL[$PER_ID]['poh_effectivedate'] = $POH_EFFECTIVEDATE;		//--------
			//เก็บวันเริ่มต้นเข้าสู่ระดับ ที่เป็นระดับตำแหน่งก่อนเลื่อนระดับล่าสุด เพื่อใช้จัดเรียงข้อมูลระดับตำแหน่ง/วันที่ (e.LEVEL_NO และ e.POH_EFFECTIVEDATE)
			$PER_EFFECTIVEDATE[$LEVEL_NOHIS][$PER_ID] =$POH_EFFECTIVEDATE;
			$arr_temp = explode("-", $POH_EFFECTIVEDATE);
			$POH_EFFECTIVEDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);	
		} // end if	
		$PER_SALARY[$PER_ID] = trim($data[PER_SALARY]);
		$ARRSORTLEVEL[$PER_ID]['per_salary'] = $PER_SALARY[$PER_ID];	//--------
		$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
		if(trim($PER_BIRTHDATE)){
			$ARRSORTLEVEL[$PER_ID]['per_birthdate'] = $PER_BIRTHDATE;	//--------
			$PER_AGE[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "year"));		//floor
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_BIRTHDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
		if(trim($PER_STARTDATE)){
			$ARRSORTLEVEL[$PER_ID]['per_startdate'] = $PER_STARTDATE;	//--------
			$PER_WORK[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_STARTDATE), "year"));		//floor
			$arr_temp = explode("-", $PER_STARTDATE);
			$PER_STARTDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		if(trim($PER_RETIREDATE)){
			$arr_temp = explode("-", $PER_RETIREDATE);
			$PER_RETIREDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	

		if($DPISDB=="odbc"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		
					   (
					   PER_EDUCATE a 
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
									) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
					   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c
					   where	a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		
					   (
					   PER_EDUCATE a
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
								)	left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
					   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		} // end if
		$db_dpis2->send_cmd($cmd);
	//$db_dpis2->show_error();
		$PER_EDUCATE[$PER_ID] = "";
		while($data2 = $db_dpis2->get_array()){
			if($PER_EDUCATE[$PER_ID]) $PER_EDUCATE[$PER_ID] .= ", ";
			$PER_EDUCATE[$PER_ID] .= trim($data2[EN_SHORTNAME]);
			$EM_NAME[$PER_ID] = trim($data2[EM_NAME]);
		} // end while

		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a
								left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
					   order by 	LEFT(trim(a.DEH_DATE), 10) desc
					";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a, PER_DECORATION b
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3) and a.DC_CODE=b.DC_CODE(+)
					   order by 	SUBSTR(trim(a.DEH_DATE), 1, 10) desc
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a
								left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
					   order by 	LEFT(trim(a.DEH_DATE), 10) desc
					";
		} // end if
		$db_dpis2->send_cmd($cmd);
//	$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$PER_DECORATE[$PER_ID] = trim($data2[DC_SHORTNAME]);
		$ARRSORTLEVEL[$PER_ID]['per_decorate'] = $PER_DECORATE[$PER_ID];	//--------
		
		$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1";
		$db_dpis3->send_cmd($cmd2);
		$data_org=$db_dpis3->get_array();
		$ORG_ID_1_NAME[$PER_ID]=$data_org[ORG_NAME];
	} // end while
	
	//============================================================================
	//เปรียบเทียบ Array เพื่อคัดหา PER_ID ที่มีเพียง record เดียว เข้ามาก็เป็นตำแหน่งนี้เลย ไม่มีประวัติการดำรงตน.ก่อนหน้า
	$result = array_diff($ARRALL_PERID_LEVELNO, $ARRALL_HAVE_PERID_LEVELNO);
	$strresult=implode("','",$result);
	$cmd2 = $cmd1; 
	$cmd2 = str_replace($search_condition1,$search_condition1." and e.PER_ID in('$strresult') ",$cmd2); 
	$count_data2 = $db_dpis->send_cmd($cmd2);
	while($data = $db_dpis->get_array()){		
		$PER_ID = $data[PER_ID];
		$PN_NAME[$PER_ID] = trim($data[PN_NAME]);
		$PER_NAME[$PER_ID] = trim($data[PER_NAME]);
		$PER_SURNAME[$PER_ID] = trim($data[PER_SURNAME]);
		$POS_NO[$PER_ID] = trim($data[POS_NO]);
		$PL_NAME[$PER_ID] = trim($data[PL_NAME]);
		$LEVEL_NO[$PER_ID] = trim($data[LEVEL_NO]);
		$LEVEL_NOHIS = trim($data[LEVEL_NOHIS]);
		$ARRSORTLEVEL[$PER_ID]['per_sortlevel'] = $LEVEL_NOHIS;		//--------
		$LEVEL_NAME[$PER_ID] = trim($data[LEVEL_NAME]);
		$PT_CODE[$PER_ID] = trim($data[PT_CODE]);
		$PT_NAME[$PER_ID] = trim($data[PT_NAME]);
		$ORG_NAME[$PER_ID] = trim($data[ORG_NAME]);
		$ORG_ID_1=trim($data[ORG_ID_1]);
		$PER_RETIREDATE=trim($data[PER_RETIREDATE]);
		$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
		if(trim($POH_EFFECTIVEDATE)){
			$ARRSORTLEVEL[$PER_ID]['poh_effectivedate'] = $POH_EFFECTIVEDATE;		//--------
			//เก็บวันเริ่มต้นเข้าสู่ระดับ ที่เป็นระดับตำแหน่งก่อนเลื่อนระดับล่าสุด เพื่อใช้จัดเรียงข้อมูลระดับตำแหน่ง/วันที่ (e.LEVEL_NO และ e.POH_EFFECTIVEDATE)
			$PER_EFFECTIVEDATE[$LEVEL_NOHIS][$PER_ID] =$POH_EFFECTIVEDATE;
			$arr_temp = explode("-", $POH_EFFECTIVEDATE);
			$POH_EFFECTIVEDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);	
		} // end if	
		$PER_SALARY[$PER_ID] = trim($data[PER_SALARY]);
		$ARRSORTLEVEL[$PER_ID]['per_salary'] = $PER_SALARY[$PER_ID];	//--------
		$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
		if(trim($PER_BIRTHDATE)){
			$ARRSORTLEVEL[$PER_ID]['per_birthdate'] = $PER_BIRTHDATE;	//--------
			$PER_AGE[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "year"));		//floor
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_BIRTHDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
		if(trim($PER_STARTDATE)){
			$ARRSORTLEVEL[$PER_ID]['per_startdate'] = $PER_STARTDATE;	//--------
			$PER_WORK[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_STARTDATE), "year"));		//floor
			$arr_temp = explode("-", $PER_STARTDATE);
			$PER_STARTDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		if(trim($PER_RETIREDATE)){
			$arr_temp = explode("-", $PER_RETIREDATE);
			$PER_RETIREDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		
		if($DPISDB=="odbc"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		
					   (
					   PER_EDUCATE a 
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
									) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
					   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c
					   where	a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		
					   (
					   PER_EDUCATE a
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
								)	left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
					   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		} // end if
		$db_dpis2->send_cmd($cmd);
	//$db_dpis2->show_error();
		$PER_EDUCATE[$PER_ID] = "";
		while($data2 = $db_dpis2->get_array()){
			if($PER_EDUCATE[$PER_ID]) $PER_EDUCATE[$PER_ID] .= ", ";
			$PER_EDUCATE[$PER_ID] .= trim($data2[EN_SHORTNAME]);
			$EM_NAME[$PER_ID] = trim($data2[EM_NAME]);
		} // end while

		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a
								left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
					   order by 	LEFT(trim(a.DEH_DATE), 10) desc
					";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a, PER_DECORATION b
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3) and a.DC_CODE=b.DC_CODE(+)
					   order by 	SUBSTR(trim(a.DEH_DATE), 1, 10) desc
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a
								left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
					   order by 	LEFT(trim(a.DEH_DATE), 10) desc
					";
		} // end if
		$db_dpis2->send_cmd($cmd);
//	$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$PER_DECORATE[$PER_ID] = trim($data2[DC_SHORTNAME]);
		$ARRSORTLEVEL[$PER_ID]['per_decorate'] = $PER_DECORATE[$PER_ID];	//--------
		
		$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1";
		$db_dpis3->send_cmd($cmd2);
		$data_org=$db_dpis3->get_array();
		$ORG_ID_1_NAME[$PER_ID]=$data_org[ORG_NAME];
	} // end while
	//รวม num_results ของ 2 query 
	$count_data = ($count_data+$count_data2);			//+count($result);	
	
/*******************************
	//การเรียงลำดับโดย...
	1. วันเข้าสู่ระดับ <
	2. ถ้า 1. เท่ากันหาเงินเดือน >
	3. ถ้า 2. เท่ากันหาวันบรรจุ <
	4. ถ้า 3. เท่ากันหาาวันเกิด <
	5. ถ้า 4. เท่ากันหาเครื่องราช >
********************************/	
$SORTBY=array('poh_effectivedate'=>'asc','per_salary'=>'desc','per_startdate'=>'asc','per_birthdate'=>'asc','per_decorate'=>'desc');

//___print("<pre> --------------------------- <br>");
//___print_r($ARRSORTLEVEL);
/*print($count_data." : <br>");	
print_r($ARRALL_PERID_LEVELNO);	
print("<br>=======================<br>");	
print_r($ARRALL_HAVE_PERID_LEVELNO);		
print("<br>=======================<br>");	
print_r($result);*/
//___print("</pre>");

/*foreach($ARRSORTLEVEL as $key=>$value){
	//เปรียบเทียบค่าเมื่อมีค่าเท่ากันก็ sort ตัวถัดไป per_sortlevel
	echo "$key => $value<br>";
} //end foreach */

	
	//แสดงข้อมูลตาม Array L ซึ่งเรียงจาก index แรกก่อนจนถึงอันสุดท้ายจะแสดง record ท้ายสุด
	$L["O1"]=array('04','03','02','01','O1');
	$L["O2"]=array('06','05','04','O2','O1');
	$L["O3"]=array('08','07','06','O3','O2');
	$L["O4"]=array('09','08','O4','O3');
	$L["K1"]=array('05','04','03','K1');		
	$L["K2"]=array('07','06','05','K2','K1');
	$L["K3"]=array('08','07','K3','K2');
	$L["K4"]=array('09','08','K4','K3');
	$L["K5"]=array('10','09','K5','K4');
	$L["D1"]=array('08','07','D1');
	$L["D2"]=array('09','08','D2','D1');
	$L["M1"]=array('09','08','M1');
	$L["M2"]=array('11','10','09','M2','M1');
	
for($i=0; $i < count($L[$search_level_no]); $i++){	
	$index=$L[$search_level_no][$i];		//$index=level no
	asort($PER_EFFECTIVEDATE[$index]);		//เรียงลำดับตาม ระดับตน. และวันที่เข้าสู่ระดับ
	if(is_array($PER_EFFECTIVEDATE[$index]) && !empty($PER_EFFECTIVEDATE[$index])){
		foreach($PER_EFFECTIVEDATE[$index] as $key=>$value){	//key=perid, value=min_effectivedate
			$person_count++;		
			$PER_ID=$key;
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $person_count .". ". $PN_NAME[$PER_ID].$PER_NAME[$PER_ID]." ". $PER_SURNAME[$PER_ID];
			$arr_content[$data_count][position] = $PL_NAME[$PER_ID]. " ". level_no_format($LEVEL_NAME[$PER_ID]) . (($PT_CODE[$PER_ID] != "11" && $LEVEL_NO[$PER_ID] >= 6)?"$PT_NAME[$PER_ID]":"")."\n$ORG_ID_1_NAME[$PER_ID]  $ORG_NAME[$PER_ID]";
			$arr_content[$data_count][posno] = $POS_NO[$PER_ID];
			$arr_content[$data_count][educate] = $PER_EDUCATE[$PER_ID];
			$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE2[$PER_ID];	//$value
			//หาระดับตำแหน่งนั้น--------------------
			$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$index' ";
			$db_dpis3->send_cmd($cmd2);
			$levelname=$db_dpis3->get_array();
			//---------------------------------------------
			$arr_content[$data_count][per_sortlevel]=$levelname[LEVEL_NAME];				//___$index?level_no_format($index):"";
			$arr_content[$data_count][per_salary] = $PER_SALARY[$PER_ID]?number_format($PER_SALARY[$PER_ID]):"";
			$arr_content[$data_count][decorate] = $PER_DECORATE[$PER_ID];
			$arr_content[$data_count][per_startdate] = $PER_STARTDATE2[$PER_ID]."\n (".$PER_WORK[$PER_ID]." ปี)";
			$arr_content[$data_count][per_birthdate] = $PER_BIRTHDATE2[$PER_ID]."\n (".$PER_AGE[$PER_ID]." ปี)";	
			$arr_content[$data_count][per_retiredate]=$PER_RETIREDATE2[$PER_ID];
			$arr_content[$data_count][em_name]=$EM_NAME[$PER_ID];
			$data_count++;
		}
	}
} //end for	

	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][posno];
			$NAME_4 = $arr_content[$data_count][educate];
			$NAME_5 = $arr_content[$data_count][poh_effectivedate];
			$NAME_11 = $arr_content[$data_count][per_sortlevel];
			$NAME_6 = $arr_content[$data_count][per_salary];
			$NAME_7 = $arr_content[$data_count][decorate];
			$NAME_8 = $arr_content[$data_count][per_startdate];
			$NAME_8_1 = $arr_content[$data_count][per_work];
			$NAME_9 = $arr_content[$data_count][per_birthdate];
			$NAME_9_1 = $arr_content[$data_count][per_age];
			$NAME_10=$arr_content[$data_count][per_retiredate];
			$NAME_12=$arr_content[$data_count][em_name];
			
			$border = "";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, "$NAME_1", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[1], 7, "$NAME_2", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[2], 7, "$NAME_3", $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[3], 7, "$NAME_4", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[4], 7, "$NAME_12", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[5], 7, "$NAME_5", $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[6], 7, "$NAME_11", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[7], 7, "$NAME_6", $border, 0, 'R', 0);
			$pdf->MultiCell($heading_width[8], 7, "$NAME_7", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
			$pdf->y = $start_y;		
			$pdf->MultiCell($heading_width[9], 7, "$NAME_8", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9];
			$pdf->y = $start_y;		
			$pdf->MultiCell($heading_width[10], 7, "$NAME_9", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10];
			$pdf->y = $start_y;		
			$pdf->Cell($heading_width[11], 7, "", $border, 0, 'C', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=11; $i++){
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