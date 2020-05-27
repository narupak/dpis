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
	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
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
	$search_dc_type = 2;

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
//	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(f.DE_YEAR) = '$search_year')";
	if(trim($search_dc_type==1)){ 
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "ชั้นสายสะพาย";
	}elseif(trim($search_dc_type==2)){
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "ชั้นต่ำกว่าสายสะพาย";
	} // end if

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
	$company_name = "แบบ ขร 2/" . substr($search_year, -2);
	$report_title = "บัญชีแสดงจำนวนชั้นตราเครื่องราชอิสริยาภรณ์||ซึ่งขอพระราชทานให้แก่ผู้ขอพระราชทานเครื่องราชอิสริยาภรณ์||$MINISTRY_NAME||$search_dc_name ประจำปี พ.ศ.$search_year";
	$report_code = "R0503";
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
	$heading_width[1] = "42";
	$heading_width[2] = "10";
	$heading_width[3] = "10";
	$heading_width[4] = "15";
	$heading_width[5] = "40";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));

		$pdf->Cell($heading_width[0] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"กรม/ส่วนราชการ",'LTR',0,'C',1);
		$pdf->Cell((($heading_width[2] + $heading_width[3]) * 8) ,7,"เครื่องราชอิสริยาภรณ์",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3] + $heading_width[4]) ,7,"รวม",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ที่เทียบเท่า",'LR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"ท.ช.",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"ท.ม.",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"ต.ช.",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"ต.ม.",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"จ.ช.",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"จ.ม.",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"บ.ช.",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"บ.ม.",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3] + $heading_width[4]) ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"หมายเหตุ",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		for($i=1; $i<=9; $i++){
			$pdf->Cell($heading_width[2] ,7,"บุรุษ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[3] ,7,"สตรี",'LTBR',0,'C',1);
		} // end for
		$pdf->Cell($heading_width[4] ,7,"รวม",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',1,'C',1);
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
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER 
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, 
							 					PER_DECORDTL e, PER_DECOR f, PER_DECORATION g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}elseif($DPISDB=="mysql"){
					$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";		
			}	
/********	
	if($search_per_type==1){
		if($DPISDB=="odbc"){
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER 
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME d, 
							 					PER_DECORDTL e, PER_DECOR f, PER_DECORATION g
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}elseif($DPISDB=="mysql"){
					$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";		
			}
	}elseif($search_per_type==2){
		if($DPISDB=="odbc"){
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PRENAME d, 
							 					PER_DECORDTL e, PER_DECOR f, PER_DECORATION g
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}
	} elseif($search_per_type==3){
		if($DPISDB=="odbc"){
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PRENAME d, 
							 					PER_DECORDTL e, PER_DECOR f, PER_DECORATION g
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(e.DC_CODE) as COUNT_DC, e.DC_CODE, a.PER_GENDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	e.DC_CODE, a.PER_GENDER ";
		}
	} // end if
**********/
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = $data_row = 0;
//	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$data_row++;

		$COUNT_DC = $data[COUNT_DC] + 0;
		$DC_CODE = trim($data[DC_CODE]);
		$PER_GENDER = trim($data[PER_GENDER]);

		if(in_array($DC_CODE, array("15", "16", "23", "24", "28", "29", "33", "34"))){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][name] = "$DEPARTMENT_NAME";
			$arr_content[$data_count]["$DC_CODE:$PER_GENDER"] += $COUNT_DC;
			$arr_content[$data_count]["TOTAL:$PER_GENDER"] += $COUNT_DC;
			$arr_content[$data_count]["TOTAL"] += $COUNT_DC;
			
			$arr_total["$DC_CODE:$PER_GENDER"] += $COUNT_DC;
			$arr_total["TOTAL:$PER_GENDER"] += $COUNT_DC;
			$arr_total["TOTAL"] += $COUNT_DC;
		} // end if
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			$NAME = $arr_content[$data_count][name];
			$DC_15_1 = $arr_content[$data_count]["15:1"];
			$DC_15_2 = $arr_content[$data_count]["15:2"];
			$DC_16_1 = $arr_content[$data_count]["16:1"];
			$DC_16_2 = $arr_content[$data_count]["16:2"];
			$DC_23_1 = $arr_content[$data_count]["23:1"];
			$DC_23_2 = $arr_content[$data_count]["23:2"];
			$DC_24_1 = $arr_content[$data_count]["24:1"];
			$DC_24_2 = $arr_content[$data_count]["24:2"];
			$DC_28_1 = $arr_content[$data_count]["28:1"];
			$DC_28_2 = $arr_content[$data_count]["28:2"];
			$DC_29_1 = $arr_content[$data_count]["29:1"];
			$DC_29_2 = $arr_content[$data_count]["29:2"];
			$DC_33_1 = $arr_content[$data_count]["33:1"];
			$DC_33_2 = $arr_content[$data_count]["33:2"];
			$DC_34_1 = $arr_content[$data_count]["34:1"];
			$DC_34_2 = $arr_content[$data_count]["34:2"];
			$TOTAL_1 = $arr_content[$data_count]["TOTAL:1"];
			$TOTAL_2 = $arr_content[$data_count]["TOTAL:2"];
			$TOTAL = $arr_content[$data_count][TOTAL];
			
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, ($data_count + 1), $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[2], 7, ($DC_15_1?number_format($DC_15_1):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, ($DC_15_2?number_format($DC_15_2):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, ($DC_16_1?number_format($DC_16_1):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, ($DC_16_2?number_format($DC_16_2):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, ($DC_23_1?number_format($DC_23_1):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, ($DC_23_2?number_format($DC_23_2):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, ($DC_24_1?number_format($DC_24_1):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, ($DC_24_2?number_format($DC_24_2):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, ($DC_28_1?number_format($DC_28_1):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, ($DC_28_2?number_format($DC_28_2):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, ($DC_29_1?number_format($DC_29_1):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, ($DC_29_2?number_format($DC_29_2):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, ($DC_33_1?number_format($DC_33_1):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, ($DC_33_2?number_format($DC_33_2):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, ($DC_34_1?number_format($DC_34_1):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, ($DC_34_2?number_format($DC_34_2):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, ($TOTAL_1?number_format($TOTAL_1):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, ($TOTAL_2?number_format($TOTAL_2):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[4], 7, ($TOTAL?number_format($TOTAL):"-"), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[5], 7, "", $border, 0, 'C', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=21; $i++){
				if($i <= 1){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				}elseif($i == 20){
					$line_start_y = $start_y;		$line_start_x += $heading_width[4];
					$line_end_y = $max_y;		$line_end_x += $heading_width[4];
				}elseif($i == 21){
					$line_start_y = $start_y;		$line_start_x += $heading_width[5];
					$line_end_y = $max_y;		$line_end_x += $heading_width[5];
				}elseif(($i % 2) == 0){
					$line_start_y = $start_y;		$line_start_x += $heading_width[2];
					$line_end_y = $max_y;		$line_end_x += $heading_width[2];
				}elseif(($i % 2) == 1){
					$line_start_y = $start_y;		$line_start_x += $heading_width[3];
					$line_end_y = $max_y;		$line_end_x += $heading_width[3];
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

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
		$pdf->MultiCell($heading_width[1], 7, "รวม", $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width[2], 7, ($arr_total["15:1"]?number_format($arr_total["15:1"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[3], 7, ($arr_total["15:2"]?number_format($arr_total["15:2"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[2], 7, ($arr_total["16:1"]?number_format($arr_total["16:1"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[3], 7, ($arr_total["16:2"]?number_format($arr_total["16:2"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[2], 7, ($arr_total["23:1"]?number_format($arr_total["23:1"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[3], 7, ($arr_total["23:2"]?number_format($arr_total["23:2"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[2], 7, ($arr_total["24:1"]?number_format($arr_total["24:1"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[3], 7, ($arr_total["24:2"]?number_format($arr_total["24:2"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[2], 7, ($arr_total["28:1"]?number_format($arr_total["28:1"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[3], 7, ($arr_total["28:2"]?number_format($arr_total["28:2"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[2], 7, ($arr_total["29:1"]?number_format($arr_total["29:1"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[3], 7, ($arr_total["29:2"]?number_format($arr_total["29:2"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[2], 7, ($arr_total["33:1"]?number_format($arr_total["33:1"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[3], 7, ($arr_total["33:2"]?number_format($arr_total["33:2"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[2], 7, ($arr_total["34:1"]?number_format($arr_total["34:1"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[3], 7, ($arr_total["34:2"]?number_format($arr_total["34:2"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[2], 7, ($arr_total["TOTAL:1"]?number_format($arr_total["TOTAL:1"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[3], 7, ($arr_total["TOTAL:2"]?number_format($arr_total["TOTAL:2"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[4], 7, ($arr_total["TOTAL"]?number_format($arr_total["TOTAL"]):"-"), $border, 0, 'C', 0);
		$pdf->Cell($heading_width[5], 7, "", $border, 0, 'C', 0);
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>