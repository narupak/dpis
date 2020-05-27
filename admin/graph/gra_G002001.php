<? 
	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	include ("../report/rpt_condition2.php");
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if($search_per_type==1) $report_title = "$DEPARTMENT_NAME||จำนวนข้าราชการจำแนกตามระดับตำแหน่ง";
	elseif($search_per_type==2) $report_title = "$DEPARTMENT_NAME||จำนวนลูกจ้างประจำจำแนกตามระดับตำแหน่ง";
	elseif($search_per_type==3) $report_title = "$DEPARTMENT_NAME||จำนวนพนักงานราชการจำแนกตามระดับตำแหน่ง";
	$report_code = "R0201";
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

	$heading_width[0] = "147";
	$heading_width[1] = "10";
	$heading_width[2] = "15";
	$heading_width[3] = "15";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,$heading_name,'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] * 11) ,7,"ระดับ",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"รวม",'LTBR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=1; $i<=11; $i++) $pdf->Cell($heading_width[1] ,7,"$i",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"คน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ร้อยละ",'LTBR',1,'C',1);
	} // function		

	function count_person($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type, $select_org_structure;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);

		if($search_per_type==1){
			// ข้าราชการ
			if($DPISDB=="odbc"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}
			}elseif($DPISDB=="oci8"){				
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d
									 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%'
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
								   
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c
									 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
								  
				} // end if
			}elseif($DPISDB=="mysql"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}
			} // end if
		}elseif($search_per_type==2){
			// ลูกจ้าง
			if($DPISDB=="odbc"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO)) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}
			}elseif($DPISDB=="oci8"){				
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d
									 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%'
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c
									 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				} // end if
			}elseif($DPISDB=="mysql"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO)) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}
			} // end if
		} // end if
		else if($search_per_type==3){
			// พนักงานราชการ
			if($DPISDB=="odbc"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMPER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO)) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}
			}elseif($DPISDB=="oci8"){				
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_EDUCATE d
									 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%'
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c
									 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				} // end if
			}elseif($DPISDB=="mysql"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO)) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}
			} // end if
		} // end if

		if($select_org_structure==1) {
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//$db_dpis2->show_error();
//echo $cmd;
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE,$EP_CODE, $EP_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
					else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
					else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
				break;
				case "ORG_2" :
					if($ORG_ID_2) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
					else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
				break;
				case "LINE" :
					if($search_per_type==1){
						if($PL_CODE) $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE')";
						else $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE' or b.PL_CODE is null)";
					}elseif($search_per_type==2){
						if($PN_CODE) $arr_addition_condition[] = "(trim(b.PN_CODE) = '$PN_CODE')";
						else $arr_addition_condition[] = "(trim(b.PN_CODE) = '$PN_CODE' or b.PN_CODE is null)";
					} // end if
					elseif($search_per_type==3){
						if($PN_CODE) $arr_addition_condition[] = "(trim(b.EP_CODE) = '$EP_CODE')";
						else $arr_addition_condition[] = "(trim(b.EP_CODE) = '$EP_CODE' or b.EP_CODE is null)";
					} // end if
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
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE, $EP_CODE;
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
	
	if($search_per_type==1){
		// ข้าราชการ
		if($DPISDB=="odbc"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
													$search_condition
								 order by		$order_by
							   ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by
							   ";
			} // end if
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%'
													$search_condition
								 order by		$order_by
							   ";
			}else{
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
													$search_condition
								 order by		$order_by
							   ";
			} // end if
		}elseif($DPISDB=="mysql"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
													$search_condition
								 order by		$order_by
							   ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by
							   ";
			} // end if
		} // end if
	}elseif($search_per_type==2){
		// ลูกจ้าง
		if($DPISDB=="odbc"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
													$search_condition
								 order by		$order_by
							   ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by
							   ";
			} // end if
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%'
													$search_condition
								 order by		$order_by
							   ";
			}else{
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
													$search_condition
								 order by		$order_by
							   ";
			} // end if
		}elseif($DPISDB=="mysql"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
													$search_condition
								 order by		$order_by
							   ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by
							   ";
			} // end if
		} // end if
	} // end if
	elseif($search_per_type==3){
		// พนักงานราชการ
		if($DPISDB=="odbc"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
													$search_condition
								 order by		$order_by
							   ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by
							   ";
			} // end if
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_EDUCATE d
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%'
													$search_condition
								 order by		$order_by
							   ";
			}else{
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
													$search_condition
								 order by		$order_by
							   ";
			} // end if
		}elseif($DPISDB=="mysql"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
													$search_condition
								 order by		$order_by
							   ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by
							   ";
			} // end if
		} // end if
	} // end if
	
	
	if($select_org_structure==1) {
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "$cmd<br>";
	//$db_dpis->show_error();
	
	$data_count = 0;
	unset($LEVEL_TOTAL);
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุสำนัก/กอง]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
							$ORG_NAME_1 = "[ไม่ระบุฝ่าย]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
							$ORG_NAME_2 = "[ไม่ระบุงาน]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LINE" :
					if($search_per_type==1){
						if($PL_CODE != trim($data[PL_CODE])){
							$PL_CODE = trim($data[PL_CODE]);
							if($PL_CODE == ""){
								$PL_NAME = "[ไม่ระบุสายงาน]";
							}else{
								$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
							for($i=1; $i<=11; $i++){ 
								$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
								if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
							} // end for
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					}elseif($search_per_type==2){
						if($PN_CODE != trim($data[PN_CODE])){
							$PN_CODE = trim($data[PN_CODE]);
							if($PN_CODE == ""){
								$PN_NAME = "[ไม่ระบุชื่อตำแหน่ง]";
							}else{
								$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PN_NAME = $data2[PN_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PN_NAME;
							for($i=1; $i<=11; $i++){ 
								$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
								if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
							} // end for
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					} // end if
					elseif($search_per_type==3){
						if($EP_CODE != trim($data[EP_CODE])){
							$EP_CODE = trim($data[EP_CODE]);
							if($EP_CODE == ""){
								$EP_NAME = "[ไม่ระบุชื่อตำแหน่ง]";
							}else{
								$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$EP_NAME = $data2[EP_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EP_NAME;
							for($i=1; $i<=11; $i++){ 
								$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
								if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
							} // end for
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					} // end if
				break;
		
				case "SEX" :
					if($PER_GENDER != trim($data[PER_GENDER])){
						$PER_GENDER = trim($data[PER_GENDER]) + 0;

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (($PER_GENDER==1)?"ชาย":(($PER_GENDER==2)?"หญิง":""));
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "PROVINCE" :
					if($PV_CODE != trim($data[PV_CODE])){
						$PV_CODE = trim($data[PV_CODE]);
						if($PV_CODE == ""){
							$PV_NAME = "[ไม่ระบุจังหวัด]";
						}else{
							$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PV_NAME = $data2[PV_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "PROVINCE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCLEVEL" :
					if($EN_CODE != trim($data[EN_CODE])){
						$EN_CODE = trim($data[EN_CODE]);
						if($EN_CODE == ""){
							$EN_NAME = "[ไม่ระบุระดับการศึกษา]";
						}else{
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
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCMAJOR" :
					if($EM_CODE != trim($data[EM_CODE])){
						$EM_CODE = trim($data[EM_CODE]);
						if($EM_CODE == ""){
							$EM_NAME = "[ไม่ระบุสาขาวิชาเอก]";
						}else{
							$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EM_NAME = $data2[EM_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCMAJOR";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EM_NAME;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
	if(array_search("EDUCLEVEL", $arr_rpt_order) !== false  && array_search("EDUCLEVEL", $arr_rpt_order) == 0)
		for($i=1; $i<=11; $i++) $LEVEL_TOTAL[$i] = count_person($i, $search_condition, "");
	if(array_search("EDUCMAJOR", $arr_rpt_order) !== false  && array_search("EDUCMAJOR", $arr_rpt_order) == 0)
		for($i=1; $i<=11; $i++) $LEVEL_TOTAL[$i] = count_person($i, $search_condition, "");

	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			unset($COUNT_LEVEL);
			for($i=1; $i<=11; $i++) $COUNT_LEVEL[$i] = $arr_content[$data_count]["level_".$i];
			$COUNT_TOTAL = array_sum($COUNT_LEVEL);
			
			if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$pdf->SetFont('angsab','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			for($i=1; $i<=11; $i++){ 
				$pdf->Cell($heading_width[1], 7, ($COUNT_LEVEL[$i]?number_format($COUNT_LEVEL[$i]):"-"), $border, 0, 'R', 0);
				$pdf->x = $start_x + $heading_width[0] + ($heading_width[1] * $i);
			} // end if
			$pdf->Cell($heading_width[2], 7, ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[3], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 0, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=13; $i++){
				if($i>=1 && $i<=11){
					$line_start_y = $start_y;		$line_start_x += $heading_width[1];
					$line_end_y = $max_y;		$line_end_x += $heading_width[1];
				}elseif($i > 11){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i - 10];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i - 10];
				}else{
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
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
				
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$border = "LTBR";
		$pdf->SetFont('angsab','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->MultiCell($heading_width[0], 7, "รวม", $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0];
		$pdf->y = $start_y;
		for($i=1; $i<=11; $i++) $pdf->Cell($heading_width[1], 7, ($LEVEL_TOTAL[$i]?number_format($LEVEL_TOTAL[$i]):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($GRAND_TOTAL?number_format($GRAND_TOTAL):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[3], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 1, 'R', 0);
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

?>