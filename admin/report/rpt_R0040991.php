<?
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1 || $search_per_type == 5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "g.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);		
		$line_title=" สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "g.PN_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "g.EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "g.TP_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);		
		$line_title=" ชื่อตำแหน่ง";
	} // end if	

	$arr_history_name = explode("|", $HISTORY_LIST);
//	$arr_history_name = array("POSITIONHIS", "SALARYHIS", "EXTRA_INCOMEHIS", "EDUCATE", "TRAINING", "ABILITY", "HEIR", "ABSENTHIS", "PUNISHMENT", "TIMEHIS", "REWARDHIS", "MARRHIS", "NAMEHIS", "DECORATEHIS", "SERVICEHIS", "SPECIALSKILLHIS"); 
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS=1)";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$DEPARTMENT_NAME = $data[ORG_NAME];
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$MINISTRY_NAME = $data[ORG_NAME];

		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";

	} // end if

	if($select_org_structure==0) {
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			$arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}else if($select_org_structure==1) {
		if(trim($search_org_ass_id)){ 
			$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
			$list_type_text .= "$search_org_ass_name";
		} // end if
		if(trim($search_org_ass_id_1)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
			$list_type_text .= " - $search_org_ass_name_1";
		} // end if
		if(trim($search_org_ass_id_2)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
			$list_type_text .= " - $search_org_ass_name_2";
		} // end if
	}
	
	if (in_array("TRAIN", $list_type)){ //ค้นหารายหลักสูตร
		$arr_search_condition[] = "(trim(d.TR_CODE)=trim('$search_tr_code'))";
	}
	if(in_array("SELECT", $list_type)){//ค้นหารายบุคคล
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}
	if(in_array("ALL", $list_type)){//ค้นหารายปีงบประมาณ
		if($DPISDB=="odbc") $arr_search_condition[] = "LEFT(trim(d.TRN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and (LEFT(trim(d.TRN_ENDDATE), 10) < '".($search_budget_year - 543)."-10-01' or d.TRN_ENDDATE is null)";
		
		elseif($DPISDB=="oci8") $arr_search_condition[] = "SUBSTR(trim(d.TRN_STARTDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and (SUBSTR(trim(d.TRN_ENDDATE), 1, 10) < '".($search_budget_year - 543)."-10-01' or d.TRN_ENDDATE is null)";
		
		elseif($DPISDB=="mysql") $arr_search_condition[] = "LEFT(trim(d.TRN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and (LEFT(trim(d.TRN_ENDDATE), 10) < '".($search_budget_year - 543)."-10-01' or d.TRN_ENDDATE is null)";
	}
	if(in_array("ALL2", $list_type)){//ค้นหารายปีงบประมาณ
		if($DPISDB=="odbc") $arr_search_condition[] = "LEFT(trim(d.TRN_STARTDATE), 10) >= '".(($search_budget_year2 - 543) - 1)."-10-01' and (LEFT(trim(d.TRN_ENDDATE), 10) < '".($search_budget_year2 - 543)."-10-01' or d.TRN_ENDDATE is null)";
		
		elseif($DPISDB=="oci8") $arr_search_condition[] = "SUBSTR(trim(d.TRN_STARTDATE), 1, 10) >= '".(($search_budget_year2 - 543) - 1)."-10-01' and (SUBSTR(trim(d.TRN_ENDDATE), 1, 10) < '".($search_budget_year2 - 543)."-10-01' or d.TRN_ENDDATE is null)";
		
		elseif($DPISDB=="mysql") $arr_search_condition[] = "LEFT(trim(d.TRN_STARTDATE), 10) >= '".(($search_budget_year2 - 543) - 1)."-10-01' and (LEFT(trim(d.TRN_ENDDATE), 10) < '".($search_budget_year2 - 543)."-10-01' or d.TRN_ENDDATE is null)";
	}

	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	$DateOfTraining="";
	if ($date_of_training != "") $DateOfTraining=" เกณฑ์วันอบรม " . $date_of_training . " วัน";

	$company_name = "";
	if(in_array("TRAIN", $list_type)) $report_title = "$DEPARTMENT_NAME||รายงานการเข้าร่วมการพัฒนารายหลักสูตร";
	if(in_array("SELECT", $list_type)) $report_title = "$DEPARTMENT_NAME||รายงานประวัติการพัฒนารายบุคคล";
	if(in_array("ALL", $list_type)) $report_title = "$DEPARTMENT_NAME||รายงานการพัฒนาข้าราชการ ประจำปีงบประมาณ $search_budget_year";
	if(in_array("ALL2", $list_type)) $report_title = "$DEPARTMENT_NAME||รายงานการพัฒนาข้าราชการสรุปจำนวนวันอบรม ประจำปีงบประมาณ $search_budget_year2" . $DateOfTraining;
	$report_code = "R040991";
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_R004095_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='L';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		if (in_array("ALL", $list_type)) $orientation='L';
		else $orientation='P';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('angsa','',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}
	
	function print_header1(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		
		$heading_width[0] = "17";
		$heading_width[1] = "80";
		$heading_width[2] = "20";
		$heading_width[3] = "40";
		$heading_width[4] = "40";
		
		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - สกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ค่าใช้จ่าย",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"วันเข้ารับการอบรม",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"วันสิ้นสุดการอบรม",'LTBR',1,'C',1);
	} // function	

	function print_header2(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		
		$heading_width[0] = "40";
		$heading_width[1] = "160";
		
		$pdf->Cell($heading_width[0] ,7,"วันที่อบรม",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เรื่องที่อบรม/ รุ่นที่/ สถานที่",'LTBR',1,'C',1);
	} // function

	function print_header3(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	
		$heading_width[0] = "10";
		$heading_width[1] = "35";
		$heading_width[2] = "35";
		$heading_width[3] = "35";
		$heading_width[4] = "55";
		$heading_width[5] = "35";
		$heading_width[6] = "15";
		$heading_width[7] = "50";
		$heading_width[8] = "15";

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - สกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"สังกัด",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เรื่อง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"วันที่อบรม",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ระยะเวลา",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"สถานที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ค่าใช้จ่าย",'LTBR',1,'C',1);
	} // function

	function print_header4(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	
		$heading_width[0] = "15";
		$heading_width[1] = "50";
		$heading_width[2] = "70";
		$heading_width[3] = "35";
		$heading_width[4] = "15";
		$heading_width[5] = "15";

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - สกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"สังกัด",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ระยะเวลา",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ผ่านเกณฑ์",'LTBR',1,'C',1);

	} // function

	if (in_array("TRAIN", $list_type)){ //ค้นหารายหลักสูตร
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			(
														(
															(
																(
																	(
																		(	
																			(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join) 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_TYPE h, PER_LEVEL i
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
													and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
													and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			(
														(
															(
																(
																	(
																		(	
																			(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join) 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}
		}else{	// 2 || 3 || 4
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			(
														(
															(
																(
																	(	
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
															) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_LEVEL i
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
													and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=i.LEVEL_NO(+)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			(
														(
															(
																(
																	(	
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
															) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}
		} //end if
	}//จบการค้นหารายหลักสูตร
	if(in_array("SELECT", $list_type)){//ค้นหารายบุคคล
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(
													(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME f, PER_LINE g, PER_TYPE h, PER_LEVEL i,PER_POSITIONHIS j
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PN_CODE=f.PN_CODE(+) and b.PL_CODE=g.PL_CODE(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(
													(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}
		}elseif($search_per_type==2){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(	
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join PER_POS_NAME g on (b.PN_CODE=g.PN_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) , LEFT(trim(a.PER_STARTDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PRENAME f, PER_POS_NAME g, PER_LEVEL i,PER_POSITIONHIS j
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PN_CODE=f.PN_CODE(+) and b.PN_CODE=g.PN_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(	
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join PER_POS_NAME g on (b.PN_CODE=g.PN_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) , LEFT(trim(a.PER_STARTDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}
		} elseif($search_per_type==3){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME as PL_NAME, a.LEVEL_NO, a.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join PER_EMPSER_POS_NAME g on (b.EP_CODE=g.EP_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME, a.LEVEL_NO, a.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10)  , LEFT(trim(a.PER_STARTDATE), 1, 10)  
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PRENAME f, PER_EMPSER_POS_NAME g, PER_LEVEL i,PER_POSITIONHIS j
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PN_CODE=f.PN_CODE(+) and b.EP_CODE=g.EP_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME , SUBSTR(trim(a.PER_STARTDATE), 1, 10)  , SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) 
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME as PL_NAME, a.LEVEL_NO, a.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join PER_EMPSER_POS_NAME g on (b.EP_CODE=g.EP_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME, a.LEVEL_NO, a.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10)  , LEFT(trim(a.PER_STARTDATE), 1, 10)  
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}
		} // end if
	}//จบการค้นหารายบุคคล
	if(in_array("ALL", $list_type) || in_array("ALL2", $list_type)){//ค้นหารายปีงบประมาณ
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			(
														(
															(
																(
																	(
																		(	
																			(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join) 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_TYPE h, PER_LEVEL i
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
													and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			(
														(
															(
																(
																	(
																		(	
																			(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join) 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}
		}else{	// 2 || 3 || 4
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			(
														(
															(
																(
																	(	
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
															) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_LEVEL i
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
													and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=i.LEVEL_NO(+)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			(
														(
															(
																(
																	(	
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
															) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}
		} //end if
	}//จบการค้นหารายปีงบประมาณ

	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$TRN_PRICE_SUM = 0;
	//echo $cmd . "<br>";
	if (in_array("TRAIN", $list_type)){ //ค้นหารายหลักสูตร
		while($data = $db_dpis->get_array()){
			$data_row++;
			if ($data_row==1){
				$TRN_ORG = $data[TRN_ORG];
				if ($data[TRN_TYPE]=="1") $TRN_TYPE1 = "ฝึกอบรม";
				elseif ($data[TRN_TYPE]=="2") $TRN_TYPE1 = "ดูงาน";
				else $TRN_TYPE1 = "สัมมนา";
				$TRN_TYPE2 = "";
				$TR_NAME = $data[TR_NAME];
				$TRN_DAY = $data[TRN_DAY] . " วัน";
				$TRN_PLACE = $data[TRN_PLACE];
			}
	
			$PER_ID = $data[PER_ID];
			$PN_NAME = $data[PN_NAME];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$PL_NAME = $data[PL_NAME];
			$LEVEL_NO = $data[LEVEL_NO];
			$POSITION_LEVEL = $data[POSITION_LEVEL];
			$PT_CODE = trim($data[PT_CODE]);
			$ORG_SHORT = "";//$data[ORG_SHORT];
			$ORG_NAME = $data[ORG_NAME];
			$TRN_NO = $data[TRN_NO];
			$TRN_STARTDATE = trim($data[TRN_STARTDATE]);
			$TRN_STARTDATE = show_date_format($TRN_STARTDATE,$DATE_DISPLAY);

			$TRN_ENDDATE = trim($data[TRN_ENDDATE]);
			$TRN_ENDDATE = show_date_format($TRN_ENDDATE,$DATE_DISPLAY);
	
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "$data_row.";
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
			$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
			$arr_content[$data_count][trn_startdate] = $TRN_STARTDATE;
			if (($TRN_ENDDATE != "0  543") && ($TRN_STARTDATE != $TRN_ENDDATE) && ($TRN_ENDDATE != "")) $arr_content[$data_count][trn_enddate] = $TRN_ENDDATE;
			else $arr_content[$data_count][trn_enddate] = "-";
			$arr_content[$data_count][trn_day] = $TRN_DAY . " วัน";
			$arr_content[$data_count][trn_place] = $TRN_PLACE;
			$arr_content[$data_count][trn_price] = number_format(0,2,'.',' ');
			$TRN_PRICE_SUM+=$arr_content[$data_count][trn_price];

			$data_count++;
		} // end while

		if($count_data){
			if ($FLAG_RTF) {
				$RTF->add_header("", 0, false);	// header default
				$RTF->add_footer("", 0, false);		// footer default
					
			//	echo "$head_text1<br>";
				$tab_align = "center";
				$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
			} else {
				$pdf->Cell(100 ,7,"หน่วยงานผู้จัด : ".$TRN_ORG,0,1,"L");
				$pdf->Cell(100 ,7,"ประเภทการสมัคร : -",0,1,"L");
				$pdf->Cell(100 ,7,"ลักษณะการพัฒนา : ".$TRN_TYPE1,0,0,"L");
				$pdf->Cell(70 ,7,"ประเภทหลักสูตร :  ".($TRN_TYPE2?$TRN_TYPE2:"-"),0,1,"L");
				$pdf->Cell(100 ,7,"ชื่อหลักสูตร : ".$TR_NAME,0,1,"L");
				$pdf->Cell(100 ,7,"สถานที่ : " . $TRN_PLACE,0,1,"L");
				$pdf->Cell(100 ,7,"รวมระยะเวลา : " . $TRN_DAY,0,1,"L");
				$pdf->Cell(100 ,7,"หมายเหตุ : ",0,1,"L");
				$pdf->AutoPageBreak = false;

			print_header1();
			
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$ORDER = $arr_content[$data_count][order];
				$NAME_1 = $arr_content[$data_count][name];
				$NAME_2 = $arr_content[$data_count][trn_price];
				$NAME_3 = $arr_content[$data_count][trn_startdate];
				$NAME_4 = $arr_content[$data_count][trn_enddate];
				
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

				$border = "";
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[1], 7, "$NAME_1", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$NAME_2", $border, "R");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$NAME_3", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "$NAME_4", $border, 1, 'C');

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=4; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y - 10) < 15){ 
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
						print_header1();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			} // end for
			
			$border = "LTBR";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$pdf->Cell(($heading_width[0] + $heading_width[1]), 7, "รวม", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7,number_format($TRN_PRICE_SUM,2,'.',' '), $border, 0, 'R', 0);
			$pdf->Cell(($heading_width[3] + $heading_width[4]), 7, "", $border, 1, 'C', 0);
		}else{
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		} // end if
	}//จบรายงานการค้นหารายหลักสูตร

	if(in_array("SELECT", $list_type)){//ค้นหารายบุคคล
		$data_count2 = 0;
		while($data = $db_dpis->get_array()){
			$data_row++;
			$arr_content[$data_count][PER_ID] = $data[PER_ID];
			$arr_content[$data_count][PN_NAME] = $data[PN_NAME];
			$arr_content[$data_count][PER_NAME] = $data[PER_NAME];
			$arr_content[$data_count][PER_SURNAME] = $data[PER_SURNAME];
			$arr_content[$data_count][PL_NAME] = $data[PL_NAME];
			$arr_content[$data_count][LEVEL_NO] = $data[LEVEL_NO];
			$arr_content[$data_count][POSITION_LEVEL] = $data[POSITION_LEVEL];
			$arr_content[$data_count][PT_CODE] = trim($data[PT_CODE]);
			$arr_content[$data_count][ORG_NAME] = $data[ORG_NAME];

			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$arr_content[$data_count][PER_BIRTHDATE] = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);

			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$arr_content[$data_count][PER_STARTDATE] = show_date_format($PER_STARTDATE,$DATE_DISPLAY);

			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$arr_content[$data_count][POH_EFFECTIVEDATE] = show_date_format($POH_EFFECTIVEDATE,$DATE_DISPLAY);

			$arr_content[$data_count][AGE] = date_difference(date("Y-m-d"), $data[PER_BIRTHDATE], "ymd");
			$arr_content[$data_count][WORK_DURATION] = date_difference(date("Y-m-d"), $data[PER_STARTDATE], "ymd");

			if($search_per_type==1){
				if($DPISDB=="odbc"){
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from		(
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
																		) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																	) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
																) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
															) left join $line_table g on ($line_join)
														) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
													) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
												) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10), LEFT(trim(d.TRN_ENDDATE), 1, 10), d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
				}elseif($DPISDB=="oci8"){
					$search_condition = str_replace(" where ", " and ", $search_condition);
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_TYPE h, PER_LEVEL i,PER_POSITIONHIS j
									 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
														and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
														and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10),d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from		(
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
																		) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																	) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
																) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
															) left join $line_table g on ($line_join)
														) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
													) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
												) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10), LEFT(trim(d.TRN_ENDDATE), 1, 10), d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
				}
			}else{	// 2 || 3 || 4
				if($DPISDB=="odbc"){
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from		(	
														(
															(
																(
																	(
																		(	
																			(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join) 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10), LEFT(trim(d.TRN_ENDDATE), 1, 10), d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) , LEFT(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID,LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
				}elseif($DPISDB=="oci8"){
					$search_condition = str_replace(" where ", " and ", $search_condition);
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_LEVEL i,PER_POSITIONHIS j
									 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
														and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
														and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10),d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID,SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									from		(	
														(
															(
																(
																	(
																		(	
																			(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join) 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10), LEFT(trim(d.TRN_ENDDATE), 1, 10), d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) , LEFT(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID,LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
				}
			} //end if
			$count_data2 = $db_dpis2->send_cmd($cmd);
			//echo $cmd . "<br><br>";
			while($data2 = $db_dpis2->get_array()){
				$arr_content2[$data_count2][PER_ID] = $data[PER_ID];

				$TRN_ORG = $data2[TRN_ORG];
				if ($data2[TRN_TYPE]=="1") $TRN_TYPE = "ฝึกอบรม";
				elseif ($data2[TRN_TYPE]=="2") $TRN_TYPE = "ดูงาน";
				else $TRN_TYPE = "สัมมนา";
				
				$TR_NAME = $data2[TR_NAME];
				$TRN_PLACE = $data2[TRN_PLACE];
				$TRN_DAY = $data2[TRN_DAY];
				
				$TRN_NO = $data2[TRN_NO];
				$TRN_STARTDATE = trim($data2[TRN_STARTDATE]);
				$TRN_STARTDATE = show_date_format($TRN_STARTDATE,$DATE_DISPLAY);

				$TRN_ENDDATE = trim($data2[TRN_ENDDATE]);
				$TRN_ENDDATE = show_date_format($TRN_ENDDATE,$DATE_DISPLAY);
				
				$arr_content2[$data_count2][trn_traindate] = $TRN_STARTDATE;
				if (($TRN_ENDDATE != "0  543") && ($TRN_STARTDATE != $TRN_ENDDATE) && ($TRN_ENDDATE != "")) $arr_content2[$data_count2][trn_traindate] .= " - " . $TRN_ENDDATE;
				$arr_content2[$data_count2][type] = "CONTENT";
				$arr_content2[$data_count2][trn_training] = $TRN_TYPE . $TR_NAME;
				if ($TRN_NO != "") $arr_content2[$data_count2][trn_training] .= " รุ่นที่ " . $TRN_NO;
				if ($TRN_ORG != "") $arr_content2[$data_count2][trn_training] .= " / จัดโดย" . $TRN_ORG;
				if ($TRN_PLACE != "") $arr_content2[$data_count2][trn_training] .= " / ณ " . $TRN_PLACE;
				if ($TRN_DAY != "") $arr_content2[$data_count2][trn_training] .= " / จำนวน " . $TRN_DAY . " วัน";
				$data_count2++;
			}
			//echo count($arr_content2)."<br><br>";
			$data_count++;
		} // end while
		if($count_data){
			$data2_check=0;
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				if ($data_count==0){
					$pdf->Cell(100 ,7,"ชื่อ : ".$arr_content[$data_count][PN_NAME] . $arr_content[$data_count][PER_NAME] ." ". $arr_content[$data_count][PER_SURNAME],0,1,"L");
					$pdf->Cell(100 ,7,"ตำแหน่ง : ".$arr_content[$data_count][PL_NAME] . $arr_content[$data_count][POSITION_LEVEL],0,1,"L");
					$pdf->Cell(100 ,7,"สังกัด : ".$arr_content[$data_count][ORG_NAME],0,1,"L");
					$pdf->Cell(70 ,7,"วัน เดือน ปีเกิด :  ".$arr_content[$data_count][PER_BIRTHDATE],0,0,"L");
					$pdf->Cell(100 ,7,"อายุ : ".$arr_content[$data_count][AGE],0,1,"L");
					$pdf->Cell(70 ,7,"เริ่มรับราชการวันที่ : " . $arr_content[$data_count][PER_STARTDATE],0,0,"L");
					$pdf->Cell(100 ,7,"อายุราชการ : " . $arr_content[$data_count][WORK_DURATION],0,1,"L");
					$pdf->Cell(100 ,7,"วันดำรงตำแหน่ง : " . $arr_content[$data_count][POH_EFFECTIVEDATE],0,1,"L");
					$pdf->AutoPageBreak = false;
					print_header2();

				}else{
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					$pdf->AddPage();

					$PER_ID_PREV=$arr_content[$data_count][PER_ID];
					$pdf->Cell(100 ,7,"ชื่อ : ".$arr_content[$data_count][PN_NAME] . $arr_content[$data_count][PER_NAME] ." ". $arr_content[$data_count][PER_SURNAME],0,1,"L");
					$pdf->Cell(100 ,7,"ตำแหน่ง : ".$arr_content[$data_count][PL_NAME] . $arr_content[$data_count][POSITION_LEVEL],0,1,"L");
					$pdf->Cell(100 ,7,"สังกัด : ".$arr_content[$data_count][ORG_NAME],0,1,"L");
					$pdf->Cell(70 ,7,"วัน เดือน ปีเกิด :  ".$arr_content[$data_count][PER_BIRTHDATE],0,0,"L");
					$pdf->Cell(100 ,7,"อายุ : ".$arr_content[$data_count][AGE],0,1,"L");
					$pdf->Cell(70 ,7,"เริ่มรับราชการวันที่ : " . $arr_content[$data_count][PER_STARTDATE],0,0,"L");
					$pdf->Cell(100 ,7,"อายุราชการ : " . $arr_content[$data_count][WORK_DURATION],0,1,"L");
					$pdf->Cell(100 ,7,"วันดำรงตำแหน่ง : " . $arr_content[$data_count][POH_EFFECTIVEDATE],0,1,"L");
					print_header2();
					$max_y = $pdf->y;
					$pdf->x = $start_x;			$pdf->y = $max_y;
				}
				
				$REPORT_ORDER = $arr_content[$data_count][type];
				//echo "count(arr_content2) = " . count($arr_content2) . "<br>";
				//echo "data2_check = " . $data2_check . "<br>";
				$data2_check_PER_ID=0;
				if ((count($arr_content2)>0) && (count($arr_content2)>= $data2_check)) {
					while ($arr_content[$data_count][PER_ID]==$arr_content2[$data2_check][PER_ID]){
						//echo "in while <br><br><br><br>";
						$data2_check_PER_ID++;

						$NAME_1 = $arr_content2[$data2_check][trn_traindate];
						$NAME_2 = $arr_content2[$data2_check][trn_training];
						
						$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

						$border = "";
						$pdf->SetFont('angsa','',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

						$pdf->Cell($heading_width[0], 7, "$NAME_1", $border, 0, 'L');
						$pdf->MultiCell($heading_width[1], 7, "$NAME_2", $border, 1, 'L');
						if($pdf->y > $max_y) $max_y = $pdf->y;
						$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
						$pdf->y = $start_y;

						//================= Draw Border Line ====================
						$line_start_y = $start_y;		$line_start_x = $start_x;
						$line_end_y = $max_y;		$line_end_x = $start_x;
						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
						
						for($i=0; $i<=1; $i++){
							$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
							$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
							$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
						} // end for
						$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
						//====================================================

						if(($pdf->h - $max_y - 10) < 15){ 
							//$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
							if($data_count < (count($arr_content) - 1)){
								$pdf->AddPage();
								print_header2();
								$max_y = $pdf->y;
							} // end if
						}else{
							if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
						} // end if
						$pdf->x = $start_x;			$pdf->y = $max_y;
						$data2_check++;
					}// end while $arr_content[$data_count][PER_ID]==$arr_content2[$data2_check][PER_ID]
				}// $data_row2 check
				if ($data2_check_PER_ID==0){
					//echo "not while <br><br><br><br>";
					$NAME_1 = "-";
					$NAME_2 = "-";
											
					$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

					$border = "";
					$pdf->SetFont('angsa','',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

					$pdf->Cell($heading_width[0], 7, "$NAME_1", $border, 0, 'L');
					$pdf->MultiCell($heading_width[1], 7, "$NAME_2", $border, 1, 'L');
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
					$pdf->y = $start_y;

					//================= Draw Border Line ====================
					$line_start_y = $start_y;		$line_start_x = $start_x;
					$line_end_y = $max_y;		$line_end_x = $start_x;
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
					for($i=0; $i<=1; $i++){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					} // end for
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					//====================================================

					if(($pdf->h - $max_y - 10) < 15){ 
						//$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
						if($data_count < (count($arr_content) - 1)){
							$pdf->AddPage();
							print_header2();
							$max_y = $pdf->y;
						} // end if
					}else{
						if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
					} // end if
					$pdf->x = $start_x;			$pdf->y = $max_y;
				}
			} // end for
			
			$border = "LTBR";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		}else{
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		} // end if
	}//จบรายงานการค้นหารายบุคคล

	if(in_array("ALL", $list_type)){//ค้นหารายปีงบประมาณ
		$TRN_PRICE_SUM=0;
		while($data = $db_dpis->get_array()){
			$data_row++;

			$PER_ID = $data[PER_ID];
			$PN_NAME = $data[PN_NAME];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$PL_NAME = $data[PL_NAME];
			$LEVEL_NO = $data[LEVEL_NO];
			$POSITION_LEVEL = $data[POSITION_LEVEL];
			$PT_CODE = trim($data[PT_CODE]);
			$ORG_SHORT = $data[ORG_SHORT];
			$ORG_NAME = $data[ORG_NAME];
			$TRN_NO = $data[TRN_NO];
			$TRN_STARTDATE = trim($data[TRN_STARTDATE]);
			$TRN_STARTDATE = show_date_format($TRN_STARTDATE,$DATE_DISPLAY);

			$TRN_ENDDATE = trim($data[TRN_ENDDATE]);
			$TRN_ENDDATE = show_date_format($TRN_ENDDATE,$DATE_DISPLAY);
			$TRN_DAY = $data[TRN_DAY];
			$TR_NAME = $data[TR_NAME];
			$TRN_PLACE = $data[TRN_PLACE];

			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "$data_row.";
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
			$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
			$arr_content[$data_count][tr_name] = $TR_NAME;
			$arr_content[$data_count][trn_startdate] = $TRN_STARTDATE;
			if (($TRN_ENDDATE != "0  543") && ($TRN_STARTDATE != $TRN_ENDDATE) && ($TRN_ENDDATE != "")) $arr_content[$data_count][trn_startdate] .= " - " . $TRN_ENDDATE;
			$arr_content[$data_count][trn_day] = $TRN_DAY . " วัน";
			$arr_content[$data_count][trn_place] = $TRN_PLACE;
			$arr_content[$data_count][trn_price] = number_format(0,2,'.',' ');
			$TRN_PRICE_SUM+=$arr_content[$data_count][trn_price];

			$data_count++;
		} // end while
		
		if($count_data){
			$pdf->AutoPageBreak = false;
			print_header3();
			
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$ORDER = $arr_content[$data_count][order];
				$NAME_1 = $arr_content[$data_count][name];
				$NAME_2 = $arr_content[$data_count][position];
				$NAME_3 = $arr_content[$data_count][org_name];
				$NAME_4 = $arr_content[$data_count][tr_name];
				$NAME_5 = $arr_content[$data_count][trn_startdate];
				$NAME_6 = $arr_content[$data_count][trn_day];
				$NAME_7 = $arr_content[$data_count][trn_place];
				$NAME_8 = $arr_content[$data_count][trn_price];
				
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

				$border = "";
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[1], 7, "$NAME_1", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$NAME_2", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$NAME_3", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[4], 7, "$NAME_4", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[5], 7, "$NAME_5", $border, 0, 'L', 0);
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[6], 7, "$NAME_6", $border, 0, 'R', 0);
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[7], 7, "$NAME_7", $border, 'L');
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[8], 7, "$NAME_8", $border, 1, 'R', 0);

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
						print_header3();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			} // end for
			
			$border = "LTBR";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6]), 7, "รวม", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[7], 7, number_format($data_row) ."  รายการ  ", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[8], 7, (number_format($TRN_PRICE_SUM,2,'.',' ')), $border, 0, 'R', 0);
		}else{
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
			//$pdf->Cell(287,10,$search_condition,0,1,'C');
		} // end if
	}//จบรายงานการค้นหารายปีงบประมาณ
	
	if(in_array("ALL2", $list_type)){//ค้นหารายปีงบประมาณ สรุปจำนวนวันอบรม
		$TRN_PRICE_SUM=0;
		while($data = $db_dpis->get_array()){
			if ($PER_ID_PREV != $data[PER_ID]){
				if ($PER_ID_PREV!=""){
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][order] = "$data_row.";
					$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
					$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
					$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
					$arr_content[$data_count][trn_day] = $TRN_DAY;
					$data_count++;
				}
				$data_row++;
				
				$PER_ID_PREV = $data[PER_ID];
				$PN_NAME = $data[PN_NAME];
				$PER_NAME = $data[PER_NAME];
				$PER_SURNAME = $data[PER_SURNAME];
				$PL_NAME = $data[PL_NAME];
				$LEVEL_NO = $data[LEVEL_NO];
				$POSITION_LEVEL = $data[POSITION_LEVEL];
				$ORG_SHORT = $data[ORG_SHORT];
				$ORG_NAME = $data[ORG_NAME];
				$PT_CODE = trim($data[PT_CODE]);
				$TRN_DAY = $data[TRN_DAY];
			}else{
				$TRN_DAY += $data[TRN_DAY];
			}
		} // end while
		if ($PER_ID_PREV!=""){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "$data_row.";
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
			$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
			$arr_content[$data_count][trn_day] = $TRN_DAY;
			$data_count++;
		}
		
		if($count_data){
			$pdf->AutoPageBreak = false;
			print_header4();
			
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$ORDER = $arr_content[$data_count][order];
				$NAME_1 = $arr_content[$data_count][name];
				$NAME_2 = $arr_content[$data_count][position];
				$NAME_3 = $arr_content[$data_count][org_name];
				$NAME_4 = $arr_content[$data_count][trn_day]." วัน";
				if ($arr_content[$data_count][trn_day]>= $date_of_training) $NAME_5 = "ผ่าน";
				else $NAME_5 = "-";
				
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

				$border = "";
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[1], 7, "$NAME_1", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$NAME_2", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$NAME_3", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "$NAME_4", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[5], 7, "$NAME_5", $border, 1, 'C', 0);

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=5; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y - 10) < 15){ 
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
						print_header4();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			} // end for
			
			$border = "LTBR";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]), 7, "รวม " . number_format($data_row) ."  รายการ  ", $border, 1, 'L', 0);
		}else{
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		} // end if
	}//จบรายงานการค้นหารายปีงบประมาณ สรุปจำนวนวันอบรม

	$pdf->close();
	$pdf->Output();		
	
?>