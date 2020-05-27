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
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 
	$search_per_type = 1;
	$search_dc_type = 1;

	$search_condition = "";
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

	$company_name = "แบบ ขร 1/" . substr($search_year, -2);
	$report_title = "บัญชีแสดงจำนวนชั้นตราเครื่องราชอิสริยาภรณ์||ซึ่งขอพระราชทานให้แก่ข้าราชการ||$MINISTRY_NAME||$search_dc_name ประจำปี พ.ศ.$search_year";
	$report_code = "R0502";
	include ("rpt_R005002_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_R005002_rtf.rtf";

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
		$orientation='L';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global  $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
				break;
				case "DEPARTMENT" :
				break;
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
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
		
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

/**********	
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
*********/
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$data_row++;

		$COUNT_DC = $data[COUNT_DC] + 0;
		$DC_CODE = trim($data[DC_CODE]);
		$PER_GENDER = trim($data[PER_GENDER]);

		if(in_array($DC_CODE, array("08", "09", "10", "11"))){
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
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			$NAME = $arr_content[$data_count][name];
			$DC_08_1 = $arr_content[$data_count]["08:1"];
			$DC_08_2 = $arr_content[$data_count]["08:2"];
			$DC_09_1 = $arr_content[$data_count]["09:1"];
			$DC_09_2 = $arr_content[$data_count]["09:2"];
			$DC_10_1 = $arr_content[$data_count]["10:1"];
			$DC_10_2 = $arr_content[$data_count]["10:2"];
			$DC_11_1 = $arr_content[$data_count]["11:1"];
			$DC_11_2 = $arr_content[$data_count]["11:2"];
			$TOTAL_1 = $arr_content[$data_count]["TOTAL:1"];
			$TOTAL_2 = $arr_content[$data_count]["TOTAL:2"];
			$TOTAL = $arr_content[$data_count][TOTAL];

			$arr_data = (array) null;
			$arr_data[] = ($data_count + 1);
			$arr_data[] = $NAME;
			$arr_data[] = $DC_08_1;
			$arr_data[] = $DC_08_2;
			$arr_data[] = $DC_09_1;
			$arr_data[] = $DC_09_2;
			$arr_data[] = $DC_10_1;
			$arr_data[] = $DC_10_2;
			$arr_data[] = $DC_11_1;
			$arr_data[] = $DC_11_2;
			$arr_data[] = $TOTAL_1;
			$arr_data[] = $TOTAL_2;
			$arr_data[] = $TOTAL;
			$arr_data[] = "";
	
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for				

		$arr_data = (array) null;
		$arr_data[] = "";
		$arr_data[] = "รวม";
		$arr_data[] = $arr_total["08:1"];
		$arr_data[] = $arr_total["08:2"];
		$arr_data[] = $arr_total["09:1"];
		$arr_data[] = $arr_total["09:2"];
		$arr_data[] = $arr_total["10:1"];
		$arr_data[] = $arr_total["10:2"];
		$arr_data[] = $arr_total["11:1"];
		$arr_data[] = $arr_total["11:2"];
		$arr_data[] = $arr_total["TOTAL:1"];
		$arr_data[] = $arr_total["TOTAL:2"];
		$arr_data[] = $arr_total["TOTAL"];
		$arr_data[] = "";

		if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else
			$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
//			$RTF->close_section(); 

		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 

		$pdf->close();
		$pdf->Output();	
	}
?>