<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php"); 

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);		
		$line_title=" สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);		
		$line_title=" ชื่อตำแหน่ง";
	} // end if	
	
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
				if($select_org_structure==0) $select_list .= "b.ORG_ID";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= $line_code;

				$heading_name .= $line_title;
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
	 	if($select_org_structure==0) $order_by = "b.ORG_ID";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	 }
	if(!trim($select_list)){
	 	if($select_org_structure==0) $select_list = "b.ORG_ID";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	 }

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_OCCUPYDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_OCCUPYDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_OCCUPYDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_OCCUPYDATE), 10) < '".($search_budget_year - 543)."-10-01')";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(c.OT_CODE='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(c.OT_CODE='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(c.OT_CODE='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
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
		$arr_search_condition[] = "(c.OT_CODE='04')";
	}
	if(in_array("PER_ORG", $list_type)){
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
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= $line_search_name;
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
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
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
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
	$company_name = "";
	$report_title = "จ.18||ปีงบประมาณ $search_budget_year ณ  1 ต.ค. ".($search_budget_year -1)."||";
	$report_title .= $MINISTRY_NAME?$MINISTRY_NAME:"".$DEPARTMENT_NAME?$DEPARTMENT_NAME:"";
	$report_code = "R20010";
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
	
	$heading_width[0] = "7";
	$heading_width[1] = "27";      
	$heading_width[2] = "38";
	$heading_width[3] = "17";
	$heading_width[4] = "40";
	$heading_width[5] = "45";
	$heading_width[6] = "10";
	$heading_width[7] = "10";
	$heading_width[8] = "10";
	
	function print_header($HISTORY_NAME){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		switch($HISTORY_NAME){
			case "PERSONAL" :
				$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[1] ,7,"เลขประจำตัว",'LTR',0,'C',1);
				$pdf->Cell($heading_width[2] ,7,"ชื่อ-สกุล",'LTR',0,'C',1);
				$pdf->Cell($heading_width[3] ,7,"เลขเงิน",'LTR',0,'C',1);
				$pdf->Cell($heading_width[4] ,7,"ชื่อตำแหน่ง",'LTR',0,'C',1);
				$pdf->Cell($heading_width[5] ,7,"สังกัด",'LTR',0,'C',1);
				$pdf->Cell($heading_width[6] +  $heading_width[7] +$heading_width[8],7,"ถือจ่ายปีนี้",'TLTR',1,'C',1);
				
				$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[4] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[6] ,7,"ระดับ",'TLBR',0,'C',1);
				$pdf->Cell($heading_width[7] ,7,"ขั้น",'TLBR',0,'C',1);
				$pdf->Cell($heading_width[8] ,7,"อัตรา",'TLBR',1,'C',1);
				break;	
			case "SALARY" :
				$pdf->Cell($heading_width[0]+$heading_width[1]+$heading_width[2]+$heading_width[3]+$heading_width[4] ,7,"เงินพิเศษ",'TLBR',0,'C',1);	
				$pdf->Cell($heading_width[5]+$heading_width[6]+$heading_width[7]+$heading_width[8] ,7,"การเลื่อนขั้น",'TLBR',1,'C',1);

				$pdf->SetFont($font,'',10);
				$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
				$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
				$pdf->Cell($heading_width[0] +$heading_width[1]+$heading_width[2],7,"เงินส่วนบุคคล",'TLBR',0,'C',1);
				$pdf->Cell($heading_width[3]+$heading_width[4] ,7,"เงินประจำตำแหน่ง",'TLBR',0,'C',1);
				$pdf->Cell($heading_width[5] ,7,"วันที่",'TLBR',0,'C',1);
				$pdf->Cell($heading_width[6] ,7,"รูปแบบ",'TLBR',0,'C',1);
				$pdf->Cell($heading_width[7] ,7,"จำนวนเงิน",'TLBR',0,'C',1);
				$pdf->Cell($heading_width[8] ,7,"ผลต่าง",'TLBR',1,'C',1);
				break;	
			case "DEPART" :
				$pdf->SetFillColor(hexdec("6A"),hexdec("D2"),hexdec("99"));
				$pdf->Cell($heading_width[0]+$heading_width[1]+$heading_width[2]+$heading_width[3]+$heading_width[4]+$heading_width[5]+$heading_width[6]+$heading_width[7]+$heading_width[8] ,7,"ฝ่ายทะเบียนและบัตร",'TLBR',1,'C',1);
				break;	
			case "SUMLEVEL" :
				$pdf->SetFont($font,'',10);
				$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
				$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
				$pdf->Cell($heading_width[0]+$heading_width[1]+$heading_width[2]+$heading_width[3]+$heading_width[4] ,7,"",'LTR',0,'C',1);
				$pdf->Cell($heading_width[5] ,7,"ผลรวม",'LTR',0,'C',1);
				$pdf->Cell($heading_width[6] ,7,"จำนวนขั้น",'LTR',0,'C',1);
				$pdf->Cell($heading_width[7] ,7,"เงินล่าสุด",'LTR',0,'C',1);
				$pdf->Cell($heading_width[8] ,7,"จำนวนผลต่าง",'LTR',1,'C',1);
				break;	
		} // end switch case
	} // function		

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3,$RPT_N;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count,$arr_mov_code,$DATE_DISPLAY;
		global $select_org_structure,$position_table,$position_join;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												LEFT(trim(a.PER_OCCUPYDATE), 10) as PER_OCCUPYDATE
							 from						(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
							$search_condition ";
		}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as 
												PER_BIRTHDATE, SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) as PER_OCCUPYDATE													
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.PN_CODE=d.PN_CODE(+)
												$search_condition ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												LEFT(trim(a.PER_OCCUPYDATE), 10) as PER_OCCUPYDATE
							 from						(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
							$search_condition ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo "<br>$cmd<br>";
		while($data = $db_dpis2->get_array()){
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$PER_OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE],$DATE_DISPLAY);

			//หาวุฒิการศึกษาบรรจุ	
			$cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'
							 order by a.EDU_SEQ desc,a.EN_CODE ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$EDU_USE = $data2[EN_NAME];
			
			//หาวุฒิการศึกษาสูงสุด
			$cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%'
							 order by a.EDU_SEQ desc,a.EN_CODE ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$EDU_MAX = $data2[EN_NAME];

			//หาส่วนราชการเดิม
			//ระบุวันก่อนหน้าที่ได้รับโอน หาจากวันสิ้นสุดการดำรงตำแหน่ง
			if(count($arr_mov_code)) $mov_code = " and trim(MOV_CODE) in (". implode(" , ", $arr_mov_code) . ")";
			if($DPISDB=="odbc"){
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_OCCUPYDATE' ";
				$cmd = " select 		POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
														PL_CODE, PT_CODE, PN_CODE, EP_CODE
								from				PER_POSITIONHIS 
								where			PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by	 	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			}elseif($DPISDB=="oci8"){	
				$BEFORE_MOVDATE = " SUBSTR(POH_EFFECTIVEDATE,1,10)	= '$PER_OCCUPYDATE' ";
				$cmd = "select 		POH_ID, SUBSTR(POH_EFFECTIVEDATE,1,10),SUBSTR(POH_ENDDATE,1,10), POH_ORG_TRANSFER, 
												LEVEL_NO, PL_CODE, PT_CODE, PN_CODE, EP_CODE
								from 		PER_POSITIONHIS
								where 	PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by 	SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc ";				 					 
			}elseif($DPISDB=="mysql"){
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_OCCUPYDATE' ";
				$cmd = " select 		POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
														PL_CODE, PT_CODE, PN_CODE, EP_CODE
								from				PER_POSITIONHIS 
								where			PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by	 	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			} // end if
			$count_poh = $db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
//			echo "<br>$cmd<br>";
			$data2 = $db_dpis3->get_array();
			$OLD_ORG = $data2[POH_ORG_TRANSFER];		
			$LEVEL_NO = $data2[LEVEL_NO];		
			$PL_CODE = $data2[PL_CODE];		
			$PT_CODE = trim($data[PT_CODE]);
			$PN_CODE = $data2[PN_CODE];		
			$EP_CODE = $data2[EP_CODE];		

			$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$POSITION_TYPE = trim($data2[POSITION_TYPE]);
			$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
	
			if($search_per_type==1) {
				$cmd = "select $line_name as PL_NAME from $line_table b where $line_code='$PL_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[PL_NAME]);
	
				$cmd = "select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PT_NAME = trim($data[PT_NAME]);

				if ($RPT_N)
					$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME $POSITION_TYPE" : "") . (trim($PM_NAME) ?")":"");
				else
					$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
			} else{ // 2 || 3 || 4
				$cmd = "select $line_name as PL_NAME from $line_table b where $line_code='$PN_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[PN_NAME]);
			} //end if			

			if ($count_poh) {
				$data_count++;
				$person_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][order] = $person_count .". ";
				$arr_content[$data_count][prename] = $PN_NAME;
				$arr_content[$data_count][name] = $PER_NAME ." ". $PER_SURNAME;
				$arr_content[$data_count][position] = $PL_NAME;
				$arr_content[$data_count][levelname] = $LEVEL_NAME; 
				$arr_content[$data_count][educate_use] = $EDU_USE;
				$arr_content[$data_count][educate_max] =  $EDU_MAX;
				$arr_content[$data_count][birthdate]	= $PER_BIRTHDATE;
				$arr_content[$data_count][posdate] = $PER_OCCUPYDATE;
				$arr_content[$data_count][old_org] = $OLD_ORG;	 //ส่วนราชการเดิมก่อนโอน
				$arr_content[$data_count][reason] = ""; // $MOV_NAME;
			}

		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
				
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
		global $arr_rpt_order, $arr_mov_code;
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
	if(count($arr_mov_code)) $mov_code = " and trim(e.MOV_CODE) in (". implode(" , ", $arr_mov_code) . ")";
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and LEFT(a.PER_OCCUPYDATE,10) = LEFT(e.POH_EFFECTIVEDATE,10))
											$search_condition $mov_code
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											and a.PER_ID=e.PER_ID(+) and SUBSTR(a.PER_OCCUPYDATE,1,10) = SUBSTR(e.POH_EFFECTIVEDATE,1,10)
											$search_condition $mov_code
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and LEFT(a.PER_OCCUPYDATE,10) = LEFT(e.POH_EFFECTIVEDATE,10))
											$search_condition $mov_code
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "";
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 8)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);	
						list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
		} // end for
		
		if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition);
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){

		$pdf->AutoPageBreak = false;
		
		print_header("PERSONAL");			//---1
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$PRE_NAME = $arr_content[$data_count][prename];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL_NAME = $arr_content[$data_count][levelname];
			$EDU_USE = $arr_content[$data_count][educate_use];
			$EDU_MAX = $arr_content[$data_count][educate_max];
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			$POSTDATE = $arr_content[$data_count][posdate]; 
			$OLD_ORG = $arr_content[$data_count][old_org];
			$MOV_NAME = $arr_content[$data_count][reason];
			
				//---ข้อมูลบุคคล
				print_header("DEPART");		//---2
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
				$pdf->Cell($heading_width[0] ,7,"$ORDER",$border,0,"C");

				$pdf->MultiCell($heading_width[1], 7, "-", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;

				$pdf->MultiCell($heading_width[2], 7, "$PRE_NAME.$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				
				$pdf->MultiCell($heading_width[3], 7, "-", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				
				$pdf->MultiCell($heading_width[4], 7, "-", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
				$pdf->y = $start_y;
				
				$pdf->MultiCell($heading_width[5], 7, "$OLD_ORG", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				
				$pdf->MultiCell($heading_width[6], 7, "-", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
				$pdf->y = $start_y;
				
				$pdf->MultiCell($heading_width[7], 7, "-", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]+ $heading_width[6]+ $heading_width[7];
				$pdf->y = $start_y;

				$pdf->MultiCell($heading_width[8], 7, "-", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]+ $heading_width[6]+ $heading_width[7]+ $heading_width[8];
				$pdf->y = $start_y;

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

				for($i=0; $i<=8; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y - 10) < 10){ 
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					$pdf->AddPage();
					//___print_header(PERSONAL);	
					$max_y = $pdf->y;
				}
				$pdf->x = $start_x;			$pdf->y = $max_y;
			//===============================================================
			//---เงินพิเศษ + การเลื่อนขั้น  <ดึงค่ามาใส่>
			print_header("SALARY");		//---3
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
		
			$pdf->Cell($heading_width[0] ,7,"",$border,0,"C");

			$pdf->MultiCell($heading_width[1], 7, "1", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;

			$pdf->MultiCell($heading_width[2], 7, "", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			
			$pdf->MultiCell($heading_width[3], 7, "", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			
			$pdf->MultiCell($heading_width[4], 7, "2", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
			$pdf->y = $start_y;
			
			$pdf->MultiCell($heading_width[5], 7, "3", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
			$pdf->y = $start_y;
			
			$pdf->MultiCell($heading_width[6], 7, "4", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
			$pdf->y = $start_y;
			
			$pdf->MultiCell($heading_width[7], 7, "5", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]+ $heading_width[6]+ $heading_width[7];
			$pdf->y = $start_y;

			$pdf->MultiCell($heading_width[8], 7, "6", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]+ $heading_width[6]+ $heading_width[7]+ $heading_width[8];
			$pdf->y = $start_y;

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

			for($i=0; $i<=8; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 10){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				$pdf->AddPage();
				//___print_header(SALARY);	
				$max_y = $pdf->y;
			}
			$pdf->x = $start_x;			$pdf->y = $max_y;
			//===============================================================
			print_header("SUMLEVEL");	//---4
			//___if($data_count < $count_data) $pdf->AddPage();
		} // end for
	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>