<?
	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	if (trim($EDU_TYPE))	{	$EDU_TYPE_TXT = "||"; }
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	$EDU_TYPE_TXT.= $EDU_TYPE[$i]."||"; } 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if(trim($RPTORD_LIST)=="SEX"){	//เลือกแยกตามเพศมาเพียงอันเดียวเท่านั้น
		$RPTORD_LIST = "ORG|".$RPTORD_LIST;
	}
	//echo $RPTORD_LIST;
	if(!trim($RPTORD_LIST)){ 	//กรณีไม่มีตัวเลือกแยกประเภทมาเลย
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	//print_r($arr_rpt_order);

	//แยกตามเงื่อนไขที่เลือก
	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_ID_REF";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.DEPARTMENT_ID";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

//				$heading_name .= " สายงาน";
				break;
			case "SEX" :											//แยกตามเพศ+ระดับการศึกษา
				$set_header="SEX";
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PER_GENDER";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.PER_GENDER";
				
				$heading_name .= " และเพศ";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID) $order_by = "g.ORG_ID_REF";
		elseif(!$DEPARTMENT_ID) $order_by = "a.DEPARTMENT_ID";
		else $order_by = "a.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID) $select_list = "g.ORG_ID_REF as MINISTRY_ID";
		elseif(!$DEPARTMENT_ID) $select_list = "a.DEPARTMENT_ID";
		else $select_list = "a.ORG_ID";
	} // end if

	$search_condition = "";
	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = "ทั้งส่วนราชการ";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
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
			if($select_org_structure==0) $arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='2')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='2')";
	//}elseif($list_type == "PER_LINE"){
	}elseif($list_type == "PER_TYPE"){	//จะต้องมีค่าส่งมาถึงจะสร้างรายงานได้
		$list_type_text = "";
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " $PROVINCE_NAME";
		} // end if
		
		if($search_pt_name){ $list_type_text .=" ตำแหน่งประเภท : $search_pt_name"; }
		if($search_per_type==1){
			$per_name = "ข้าราชการพลเรือน";
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= " / ตำแหน่งในสายงาน : $search_pl_name";
			}
		}elseif($search_per_type==2){
			$per_name = "ลูกจ้างประจำ";
			if(trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
				$list_type_text .= " / ตำแหน่งในสายงาน : $search_pn_name";
			}
		}elseif($search_per_type==3){
			$per_name = "พนักงานราชการ";
			if(trim($search_ep_code)){
				$search_ep_code = trim($search_ep_code);
				$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
				$list_type_text .= " / ตำแหน่งในสายงาน : $search_ep_name";
			}
		} // end if
	}elseif($list_type == "PER_COUNTRY"){
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
	}else{
		// ทั้งส่วนราชการ
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";

	$report_title = "จำนวน$per_name จำแนกตามระดับการศึกษา $heading_name";
	$report_title .= " ประจำปีงบประมาณ  พ.ศ. $search_year";

	$report_code = "R1008";
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

	if($set_header=="SEX"){
		$heading_width[0] = "77";	$heading_width[1] = "24";	$heading_width[2] = "17";	$heading_width[3] = "17";	$heading_width[4] = "17";	$heading_width[5] = "15";
		$heading_width[6] = "24";	$heading_width[7] = "17";	$heading_width[8] = "17";	$heading_width[9] = "17";	$heading_width[10] = "15";	$heading_width[11] = "15";
	}else{
		$heading_width[0] = "77";
		$heading_width[1] = "21";
		$heading_width[2] = "21";
	}

	function print_header(){
		global $pdf, $heading_width, $set_header;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		
		if($set_header=="SEX"){
			$pdf->Cell($heading_width[0] ,7,"ส่วนราชการ",'LTR',0,'C',1);
			$pdf->Cell(($heading_width[1]+$heading_width[2]+$heading_width[3]+$heading_width[4]+$heading_width[5]) ,7,"ชาย",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[6]+$heading_width[7]+$heading_width[8]+$heading_width[9]+$heading_width[10]) ,7,"หญิง",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[11] ,7,"จำนวน",'LTR',1,'C',1);

			//แถว 2 ---------------------
			$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);		
			$pdf->Cell(($heading_width[1]) ,7,"ต่ำกว่าปริญญาตรี",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[2]) ,7,"ปริญญาตรี",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[3]) ,7,"ปริญญาโท",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[4]) ,7,"ปริญญาเอก",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[5]) ,7,"รวม",'LTBR',0,'C',1);
			
			$pdf->Cell(($heading_width[6]) ,7,"ต่ำกว่าปริญญาตรี",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[7]) ,7,"ปริญญาตรี",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[8]) ,7,"ปริญญาโท",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[9]) ,7,"ปริญญาเอก",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[10]) ,7,"รวม",'LTBR',0,'C',1);
						
			$pdf->Cell($heading_width[11] ,7,"รวม",'LBR',1,'C',1);
		}else{
			$pdf->Cell($heading_width[0] ,7,"ส่วนราชการ",'LTR',0,'C',1);
			$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"ต่ำกว่าปริญญาตรี",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"ปริญญาตรี",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"ปริญญาโท",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"ปริญญาเอก",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"รวม",'LTBR',1,'C',1);
	
			$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
			for($i=0; $i<4; $i++){
				$pdf->Cell($heading_width[1] ,7,"จำนวน",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[2] ,7,"ร้อยละ",'LTBR',0,'C',1);
			} // end if
			$pdf->Cell($heading_width[1] ,7,"จำนวน",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"ร้อยละ",'LTBR',1,'C',1);
		}
	} // function		

	function count_person($education_level, $PER_GENDER, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $search_per_type;
		global $EDU_TYPE_TXT;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		if($education_level == 1) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('05', '10', '20', '30'))";
		elseif($education_level == 2) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('40'))";
		elseif($education_level == 3) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('60'))";
		elseif($education_level == 4) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('80'))";
		if($PER_GENDER){ 
			$search_condition .= (trim($search_condition)?" and ":" where ") . " (b.PER_GENDER=$PER_GENDER) ";
		}
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);

		//หาจำนวน
		if($search_per_type==1){
			// ข้าราชการ
			$pos_tb="PER_POSITION";
			$join_tb="a.POS_ID=b.POS_ID";
		}elseif($search_per_type==2){	
			// ลูกจ้าง
			$pos_tb="PER_POS_EMP";
			$join_tb="a.POEM_ID=b.POEM_ID";
		}elseif($search_per_type==3){ 
			//พนักงานราชการ
			$pos_tb="PER_POS_EMPSER";
			$join_tb="a.POEMS_ID=b.POEMS_ID";
		}

		if($DPISDB=="odbc"){
			$cmd = " select			count(b.PER_ID) as count_person
							 from		(	
							 					(
													(
														(
															(
																$pos_tb a 
																left join PER_PERSONAL b on ($join_tb) 
															) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (b.PER_ID=d.PER_ID and d.EDU_TYPE like '$EDU_TYPE_TXT%')
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							 $search_condition
							 group by	b.PER_ID
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(b.PER_ID) as count_person
							 from			$pos_tb a, PER_PERSONAL b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f,PER_ORG g
							 where		$join_tb(+) and a.ORG_ID=c.ORG_ID(+) and  a.DEPARTMENT_ID=g.ORG_ID(+)
												and b.PER_ID=d.PER_ID and d.EDU_TYPE like '%$EDU_TYPE_TXT%' and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 group by	b.PER_ID
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(b.PER_ID) as count_person
							 from		(	
							 					(
													(
														(
															(
																$pos_tb a 
																left join PER_PERSONAL b on ($join_tb) 
															) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (b.PER_ID=d.PER_ID and d.EDU_TYPE like '$EDU_TYPE_TXT%')
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							 $search_condition
							 group by	b.PER_ID
						   ";
		} // end if

		if($select_org_structure==1){
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);	
		}
		$count_person = $db_dpis2->send_cmd($cmd);
	//echo "<br>$cmd<br>";
	//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

	return $count_person;
	} // function

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE,$PER_GENDER;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID) $arr_addition_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
					else $arr_addition_condition[] = "(g.ORG_ID_REF = 0 or g.ORG_ID_REF is null)";
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
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER or a.PER_GENDER is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE,$PER_GENDER;
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
				case "SEX" :
					$PER_GENDER = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//หาชื่อส่วนราชการ 
	if($search_per_type==1){
		// ข้าราชการ
		$pos_tb="PER_POSITION";
		$join_tb="a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){	
		// ลูกจ้าง
		$pos_tb="PER_POS_EMP";
		$join_tb="a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		//พนักงานราชการ
		$pos_tb="PER_POS_EMPSER";
		$join_tb="a.POEMS_ID=b.POEMS_ID";
	}
	if($DPISDB=="odbc"){	
			$cmd = "select			distinct $select_list
							from		(
							 					(
													(
														(
															(	
																$pos_tb a 
																left join PER_PERSONAL b on ($join_tb) 
															) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) left join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition  and (d.EDU_TYPE like '%$EDU_TYPE_TXT%')
							 	order by		$order_by
						   		";
	}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			$pos_tb a, PER_PERSONAL b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f, PER_ORG g
							 where		$join_tb(+) and a.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
							 					and b.PER_ID=d.PER_ID and d.EDU_TYPE like '%$EDU_TYPE_TXT%' and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 order by		$order_by
						   ";
	}elseif($DPISDB=="mysql"){
			$cmd = "select			distinct $select_list
							from		(
							 					(
													(
														(
															(	
																$pos_tb a 
																left join PER_PERSONAL b on ($join_tb) 
															) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) left join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition  and (d.EDU_TYPE like '%$EDU_TYPE_TXT%')
							 	order by		$order_by
						   		";
	}

	if($select_org_structure==1){
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);	
	}
	$count_data = $db_dpis->send_cmd($cmd);
// echo "<br>$cmd<br>";
//	$db_dpis->show_error();

//	print_r($search_condition);
	$data_count = 0;
	$GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = $GRAND_TOTAL_4 = 0;
	$GRAND_TOTAL_5 = $GRAND_TOTAL_6 = $GRAND_TOTAL_7 = $GRAND_TOTAL_8 = 0;
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
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
	
						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
							$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
							$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
							$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
						}
					
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
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
							$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
							$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
							$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
						}
						
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
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
							$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
							$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
							$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
						}
						
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

						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
							$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
							$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
							$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				/******case "SEX" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุสำนัก/กอง]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . "$ORG_NAME";

						if($PER_GENDER != trim($data[PER_GENDER])){	
							$PER_GENDER = trim($data[PER_GENDER]) + 0;
							if($PER_GENDER){
								//ชาย
								$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
								//หญิง
								$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
								$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
								$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
								$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
		
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
								$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
							}
						}//end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;******/
			} // end switch case
		} // end for
	} // end while

	if($set_header=="SEX"){
		$GRAND_TOTAL_M = ($GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4);	//ชาย
		$GRAND_TOTAL_F = ($GRAND_TOTAL_5+ $GRAND_TOTAL_6 + $GRAND_TOTAL_7 + $GRAND_TOTAL_8);		//หญิง
	}else{
		$GRAND_TOTAL = ($GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4);
	}
//	$GRAND_TOTAL = count_person(0, $search_condition, "");
//	print_r($arr_content); echo "<BR>";
	$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
	$arr_categories = array();
	for($i=0;$i<count($arr_content);$i++){
		$arr_categories[$i] = $arr_content[$i][name];
		$arr_series_caption_data[0][] = $arr_content[$i][count_1];
		$arr_series_caption_data[1][] = $arr_content[$i][count_2];
		$arr_series_caption_data[2][] = $arr_content[$i][count_3];
		$arr_series_caption_data[3][] = $arr_content[$i][count_4];
		}
	//print_r($arr_series_caption_data);echo("<BR>");
	$arr_series_list[0] = implode(";", $arr_series_caption_data[0]);
	$arr_series_list[1] = implode(";", $arr_series_caption_data[1]);
	$arr_series_list[2] = implode(";", $arr_series_caption_data[2]);
	$arr_series_list[3] = implode(";", $arr_series_caption_data[3]);
	
	$chart_title = $report_title;
	$chart_subtitle = $company_name; 
	if(!$setWidth) $setWidth = "800";
	if(!$setHeight) $setHeight = "600";
	$selectedFormat = "SWF";
	$series_caption_list = "ต่ำกว่าปริญญาตรี;ปริญญาตรี;ปริญญาโท;ปริญญาเอก";
	$categories_list = implode(";", $arr_categories)."";
	if(strtolower($graph_type)=="pie"){
		$series_list = $GRAND_TOTAL_M.";".$GRAND_TOTAL_F.";".$GRAND_TOTAL_S;
		}else{
		$series_list = implode("|", $arr_series_list);
		}

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
		} // end switch case
?>