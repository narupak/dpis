<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	 if($search_edu) { $search_edu.= ' or '; }
$search_edu.= "d.EDU_TYPE like '%||$EDU_TYPE[$i]||%' "; } 

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(f.EL_CODE) in ('05', '10', '20', '30', '40', '60', '80'))";

	$list_type_text = "ทั้งส่วนราชการ";
	if($export_type=="report"){
		include ("../report/rpt_condition3.php");
		}else if($export_type=="graph"){
		include ("../../admin/report/rpt_condition3.php");	//เงื่อนไขที่ต้องการแสดงผล
		}	
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if($search_per_type==1) $report_title = "$DEPARTMENT_NAME||จำนวนข้าราชการจำแนกตามระดับการศึกษา";
	elseif($search_per_type==2) $report_title = "$DEPARTMENT_NAME||จำนวนลูกจ้างประจำจำแนกตามระดับการศึกษา";
	elseif($search_per_type==3) $report_title = "$DEPARTMENT_NAME||จำนวนพนักงานราชการจำแนกตามระดับการศึกษา";
	$report_code = "R0202";
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

	$heading_width[0] = "87";
	$heading_width[1] = "20";
	$heading_width[2] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ประเทศที่สำเร็จการศึกษา",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"ต่ำกว่าป.ตรี",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"ป.ตรี",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"ป.โท",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"ป.เอก",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"รวม",'LTBR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=0; $i<4; $i++){
			$pdf->Cell($heading_width[1] ,7,"จำนวน",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"ร้อยละ",'LTBR',0,'C',1);
		} // end if
		$pdf->Cell($heading_width[1] ,7,"จำนวน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ร้อยละ",'LTBR',1,'C',1);
	} // function		

	function count_person($education_level, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $search_per_type, $search_edu;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($education_level == 1) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('05', '10', '20', '30'))";
		elseif($education_level == 2) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('40'))";
		elseif($education_level == 3) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('60'))";
		elseif($education_level == 4) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('80'))";
		
		
		
		
		if($search_per_type==1){
			// ข้าราชการ
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
								 						(
															(
																(	
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
								 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
								 						(
															(
																(	
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID )
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			} // end if
		}elseif($search_per_type==2){
			// ลูกจ้าง
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) 
								 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
													$search_condition
								 group by	a.PER_ID
							   ";
							   
							 
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			} // end if
		} // end if
		elseif($search_per_type==3){
			// พนักงานราชการ
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
								 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			} // end if
		} // end if
		if($select_org_structure==1) $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);   //##############	
		$count_person = $db_dpis2->send_cmd($cmd);
		//echo "$cmd<hr>";
	//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	if($search_per_type==1){
		// ข้าราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE)) as CT_CODE
							 from			(
													(
														(
															(	
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												$search_condition  and ($search_edu)
							 order by		IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE))
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct NVL(e.CT_CODE, 0) as CT_CODE
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 order by		NVL(e.CT_CODE, 0)
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct e.CT_CODE as CT_CODE
							 from			(
													(
														(
															(	
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												$search_condition  and ($search_edu)
							 order by		e.CT_CODE
						   ";
		}
	}elseif($search_per_type==2){
		// ลูกจ้าง
		if($DPISDB=="odbc"){
			$cmd = " select			distinct IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE)) as CT_CODE
							 from			(
													(
														(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_EDUCATE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												$search_condition  and ($search_edu)
							 order by		IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE))
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct NVL(e.CT_CODE, 0) as CT_CODE
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 order by		NVL(e.CT_CODE, 0)
						   ";
						   /*
						   $cmd = " select			distinct NVL(e.CT_CODE, 0) as CT_CODE
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%' and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 order by		NVL(e.CT_CODE, 0)
						   
						   */
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct e.CT_CODE as CT_CODE
							 from			(
													(
														(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_EDUCATE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												$search_condition  and ($search_edu)
							 order by		e.CT_CODE
						   ";
		}
	} // end if
	elseif($search_per_type==3){
		// พนักงานราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE)) as CT_CODE
							 from			(
													(
														(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_EDUCATE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												$search_condition  and ($search_edu)
							 order by		IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE))
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct NVL(e.CT_CODE, 0) as CT_CODE
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 order by		NVL(e.CT_CODE, 0)
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct e.CT_CODE as CT_CODE
							 from			(
													(
														(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID )
													) left join PER_EDUCATE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												$search_condition  and ($search_edu)
							 order by		e.CT_CODE
						   ";
		}
	} // end if
	if($select_org_structure==1) $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);   //##############	
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = $GRAND_TOTAL_4 = 0;
	while($data = $db_dpis->get_array()){
		$CT_CODE = trim($data[CT_CODE]);
		if($CT_CODE == 0 || $CT_CODE == ""){
			$CT_NAME = "[ไม่ระบุประเทศ]";

			$addition_condition = "(trim(e.CT_CODE) = '$CT_CODE' or e.CT_CODE is null)";
		}else{
			$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$CT_NAME = $data2[CT_NAME];

			$addition_condition = "(trim(e.CT_CODE) = '$CT_CODE')";
		} // end if

		$arr_content[$data_count][type] = "COUNTRY";
//		$arr_content[$data_count][name] = "ประเทศ$CT_NAME";
		$arr_content[$data_count][name] = "$CT_NAME";
		$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
		$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
		$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
		$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);

		$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
		$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
		$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
		$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];

		$data_count++;
	} // end while
	
	
	
//	$GRAND_TOTAL_1 = count_person(1, $search_condition, "");
//	$GRAND_TOTAL_2 = count_person(2, $search_condition, "");
//	$GRAND_TOTAL_3 = count_person(3, $search_condition, "");
//	$GRAND_TOTAL_4 = count_person(4, $search_condition, "");
	
	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4;
//	$GRAND_TOTAL = count_person(0, $search_condition, "");
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
if($export_type=="report"){		
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			$COUNT_2 = $arr_content[$data_count][count_2];
			$COUNT_3 = $arr_content[$data_count][count_3];
			$COUNT_4 = $arr_content[$data_count][count_4];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4;
			
			$PERCENT_1 = $PERCENT_2 = $PERCENT_3 = $PERCENT_4 = $PERCENT_TOTAL = 0;
			if($COUNT_TOTAL){ 
				$PERCENT_1 = ($COUNT_1 / $COUNT_TOTAL) * 100;
				$PERCENT_2 = ($COUNT_2 / $COUNT_TOTAL) * 100;
				$PERCENT_3 = ($COUNT_3 / $COUNT_TOTAL) * 100;
				$PERCENT_4 = ($COUNT_4 / $COUNT_TOTAL) * 100;
			} // end if
			if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

			$border = "";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[1], 7, ($COUNT_1?number_format($COUNT_1):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_1?number_format($PERCENT_1, 2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_2?number_format($COUNT_2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_2?number_format($PERCENT_2, 2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_3?number_format($COUNT_3):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_3?number_format($PERCENT_3, 2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_4?number_format($COUNT_4):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_4?number_format($PERCENT_4, 2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 0, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=10; $i++){
				if($i==0){
					$line_start_y = $start_y;		$line_start_x += $heading_width[0];
					$line_end_y = $max_y;		$line_end_x += $heading_width[0];
				}elseif(($i % 2) == 1){
					$line_start_y = $start_y;		$line_start_x += $heading_width[1];
					$line_end_y = $max_y;		$line_end_x += $heading_width[1];
				}elseif(($i % 2) == 0){
					$line_start_y = $start_y;		$line_start_x += $heading_width[2];
					$line_end_y = $max_y;		$line_end_x += $heading_width[2];
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
				
		$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = $PERCENT_TOTAL_4 = 0;
		if($GRAND_TOTAL){ 
			$PERCENT_TOTAL_1 = ($GRAND_TOTAL_1 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_4 = ($GRAND_TOTAL_4 / $GRAND_TOTAL) * 100;
		} // end if
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$border = "LTBR";
		$pdf->SetFont('angsab','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->MultiCell($heading_width[0], 7, "รวม", $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_1?number_format($GRAND_TOTAL_1):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_1?number_format($PERCENT_TOTAL_1, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_2?number_format($GRAND_TOTAL_2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_2?number_format($PERCENT_TOTAL_2, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_3?number_format($GRAND_TOTAL_3):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_3?number_format($PERCENT_TOTAL_3, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_4?number_format($GRAND_TOTAL_4):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_4?number_format($PERCENT_TOTAL_4, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL?number_format($GRAND_TOTAL):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 1, 'R', 0);
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
	}else if($export_type=="graph"){//if($export_type=="report"){
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	echo "<pre>"; print_r($arr_rpt_order); echo "</pre>";
	$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
	$arr_categories = array();
	for($i=0;$i<count($arr_content);$i++){
//		if($arr_content[$i][type]==$arr_rpt_order[0]){
		$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
			for($j=2;$j<count($arr_content_key);$j++){
				$arr_series_caption_data[$j][] = $arr_content[$i][$arr_content_key[$j]];
				}//for($j=2;$j<count($arr_content_key);$j++){
//			}//if($arr_content[$i][type]==$arr_rpt_order[0]){
		}//for($i=0;$i<count($arr_content);$i++){
//	echo "<pre>"; print_r($arr_series_caption_data); echo "</pre>";
	for($j=2;$j<count($arr_content_key);$j++){
		$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]).";".${"GRAND_TOTAL_".($j-1)};
		}
	
	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
	if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
	$selectedFormat = "SWF";
	$series_caption_list = "ต่ำกว่าป.ตรี;ป.ตรี;ป.โท;ป.เอก";
	$categories_list = implode(";", $arr_categories).";รวม";
	if(strtolower($graph_type)=="pie"){
		$series_list = $GRAND_TOTAL_1.";".$GRAND_TOTAL_2.";".$GRAND_TOTAL_3.";".$GRAND_TOTAL_4;
		}else{
		$series_list = implode("|", $arr_series_list);
		}
	//echo($series_list);
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