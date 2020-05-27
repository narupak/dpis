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
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=i.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "i.PL_NAME";
		 $type_code ="b.PT_CODE";
		 $select_type_code =",b.PT_CODE";
		 $mgt_code ="b.PM_CODE";
		 $select_mgt_code =",b.PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=i.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "i.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=i.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "i.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=i.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "i.TP_NAME";
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
	$search_per_type = 1;

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

	if($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			}
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			}
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			}
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			}
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			}
			if(trim($search_org_ass_id_2)){ 
				$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			}
		}
	}else{
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
	if(trim($search_org_id)){ 
		if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
		if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		$list_type_text = "$search_org_name";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการที่มีสิทธิได้รับเครื่องราชฯ $search_dc_name ปี พ.ศ.$search_year";
	$report_code = "R0507";
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

	$heading_width[0] = "85";
	$heading_width[1] = "85";
	$heading_width[2] = "20";
	$heading_width[3] = "20";
	$heading_width[4] = "20";
	$heading_width[5] = "20";
	$heading_width[6] = "20";
	$heading_width[7] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"วันที่เกษียณ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"วันเข้าสู่ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"วันเข้าสู่ระดับ",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[6] + $heading_width[7]) ,7,"ปัจจุบันได้รับเครื่องราชฯ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"หน่วยงาน/ชื่อ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"วันบรรจุ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"อายุราชการ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ก่อนปัจจุบัน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ปัจจุบัน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ชั้น",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"วันที่ได้รับ",'LTBR',1,'C',1);
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
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, b.ORG_ID, c.ORG_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL,
											LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, e.DC_CODE, g.DC_NAME,
											MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE $select_type_code  $select_mgt_code
						 from			(
												(
													(
														(
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
												) left join PER_POSITIONHIS h on (a.PER_ID=h.PER_ID and a.LEVEL_NO=h.LEVEL_NO)
											) left join $line_table i on ($line_join)
										) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
											$search_condition
						 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10), b.ORG_ID, c.ORG_NAME,
											$line_code, $line_name, a.LEVEL_NO, m.POSITION_LEVEL,
											LEFT(trim(a.PER_STARTDATE), 10), e.DC_CODE, g.DC_NAME,b.PT_CODE, b.PM_CODE
						 order by		e.DC_CODE, b.ORG_ID, a.LEVEL_NO, LEFT(trim(a.PER_STARTDATE), 10) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, b.ORG_ID, c.ORG_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL,
											SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, e.DC_CODE, g.DC_NAME,
											MIN(SUBSTR(trim(h.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE $select_type_code  $select_mgt_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, 
											PER_DECORDTL e, PER_DECOR f, PER_DECORATION g, PER_POSITIONHIS h, $line_table i, PER_LEVEL m
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
											and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
											and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=h.LEVEL_NO(+) and $line_join(+) and a.LEVEL_NO=m.LEVEL_NO(+)
											$search_condition
						 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), b.ORG_ID, c.ORG_NAME,
											$line_code, $line_name, a.LEVEL_NO, m.POSITION_LEVEL,
											SUBSTR(trim(a.PER_STARTDATE), 1, 10), e.DC_CODE, g.DC_NAME,b.PT_CODE, b.PM_CODE
						 order by		e.DC_CODE, b.ORG_ID, a.LEVEL_NO, SUBSTR(trim(a.PER_STARTDATE), 1, 10) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, b.ORG_ID, c.ORG_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL,
											LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, e.DC_CODE, g.DC_NAME,
											MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE $select_type_code  $select_mgt_code
						 from			(
												(
													(
														(
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
												) left join PER_POSITIONHIS h on (a.PER_ID=h.PER_ID and a.LEVEL_NO=h.LEVEL_NO)
											) left join $line_table i on ($line_join)
										) left join  PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
											$search_condition
						 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10), b.ORG_ID, c.ORG_NAME,
											$line_code, $line_name, a.LEVEL_NO, m.POSITION_LEVEL,
											LEFT(trim(a.PER_STARTDATE), 10), e.DC_CODE, g.DC_NAME,b.PT_CODE, b.PM_CODE
						 order by		e.DC_CODE, b.ORG_ID, a.LEVEL_NO, LEFT(trim(a.PER_STARTDATE), 10) ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	initialize_parameter(0);
	$DC_CODE = $ORG_ID = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;

		if($DC_CODE != trim($data[DC_CODE])){
			$DC_CODE = trim($data[DC_CODE]);
			$DC_NAME = trim($data[DC_NAME]);			

			$arr_content[$data_count][type] = "DECORATION";
			$arr_content[$data_count][name] = "$DC_NAME";
			$data_count++;
			
			$ORG_ID = -1;
		} // end if
		
		if($ORG_ID != $data[ORG_ID] && !$search_org_id){
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = trim($data[ORG_NAME]);	

			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][name] = "$ORG_NAME";
			$data_count++;
		} // end if

		$PER_ID = $data[PER_ID];
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = trim($data[PL_NAME]);
		$LEVEL_NO = $data[LEVEL_NO];
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$PER_RETIREDATE = "";
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_RETIREDATE = "1 ต.ค. ".($arr_temp[0] + 543 + 60 + ((substr($PER_BIRTHDATE, 5, 5) >= "10-01")?1:0));
			//$PER_BIRTHDATE = ($arr_temp[2] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
		} // end if
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE],$DATE_DISPLAY);
		
		if(trim($mgt_code)){
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where	PM_CODE=".$PM_CODE;
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);
		}
		if(trim($type_code)){
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
		}
				
		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID and b.DC_ORDER < 99
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, SUBSTR(trim(a.DEH_DATE), 1, 10) as DEH_DATE
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.DC_CODE=b.DC_CODE(+) and a.PER_ID=$PER_ID and b.DC_ORDER < 99
							 order by SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID and b.DC_ORDER < 99
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$LAST_DC_NAME = trim($data2[DC_SHORTNAME]);
		$LAST_DEH_DATE = show_date_format($data2[DEH_DATE],$DATE_DISPLAY);

		if($DPISDB=="odbc"){
			$cmd = " select 	MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE2
							 from 		PER_POSITIONHIS 
							 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='". str_pad(($LEVEL_NO - 1), 2, "0", STR_PAD_LEFT) ."' ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	MIN(SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE2
							 from 		PER_POSITIONHIS 
							 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='". str_pad(($LEVEL_NO - 1), 2, "0", STR_PAD_LEFT) ."' ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE2
							 from 		PER_POSITIONHIS 
							 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='". str_pad(($LEVEL_NO - 1), 2, "0", STR_PAD_LEFT) ."' ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$POH_EFFECTIVEDATE2 = show_date_format($data2[POH_EFFECTIVEDATE2],$DATE_DISPLAY);
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "$data_row.". $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][startdate] = "$PER_STARTDATE";
		$arr_content[$data_count][retiredate] = "$PER_RETIREDATE";
		$arr_content[$data_count][effectivedate] = "$POH_EFFECTIVEDATE";
		$arr_content[$data_count][last_effectivedate] = "$POH_EFFECTIVEDATE2";
		$arr_content[$data_count][last_dc_name] = "$LAST_DC_NAME";
		$arr_content[$data_count][last_deh_date] = "$LAST_DEH_DATE";
		if($PM_CODE){
			$arr_content[$data_count][position] = $PM_NAME;
			
			$data_count++;
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][name] = "";
			$arr_content[$data_count][startdate] = "";
			$arr_content[$data_count][retiredate] = "";
			$arr_content[$data_count][effectivedate] = "";
			$arr_content[$data_count][last_effectivedate] = "";
			$arr_content[$data_count][last_dc_name] = "";
			$arr_content[$data_count][last_deh_date] = "";
			$arr_content[$data_count][position] = "(". $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") .")";
		}else{
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		} // end if
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$STARTDATE = $arr_content[$data_count][startdate];
			$RETIREDATE = $arr_content[$data_count][retiredate];
			$EFFECTIVEDATE = $arr_content[$data_count][effectivedate];
			$LAST_EFFECTIVEDATE = $arr_content[$data_count][last_effectivedate];
			$LAST_DC_NAME = $arr_content[$data_count][last_dc_name];
			$LAST_DEH_DATE = $arr_content[$data_count][last_deh_date];
			
			if($REPORT_ORDER == "DECORATION"){
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

				$border = "LTBR";
				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$pdf->MultiCell(array_sum($heading_width), 7, "$NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + array_sum($heading_width);
				$pdf->y = $start_y;
			}else{
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				if($REPORT_ORDER == "ORG"){
					$border = "";
					$pdf->SetFont($fontb,'',14);
				} // end if
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[1], 7, "$POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[2], 7, "$STARTDATE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[3], 7, "$RETIREDATE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[4], 7, "$LAST_EFFECTIVEDATE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[5], 7, "$EFFECTIVEDATE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[6], 7, "$LAST_DC_NAME", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[7], 7, "$LAST_DEH_DATE", $border, 0, 'C', 0);
	
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=7; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
			} // end if

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