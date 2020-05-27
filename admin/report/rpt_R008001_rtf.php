<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "e.POS_ID=g.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "g.PL_CODE=i.PL_CODE";
		$line_name = "i.PL_NAME";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "e.POEM_ID=g.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "g.PN_CODE=i.PN_CODE";
		$line_name = "i.PN_NAME";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "e.POEMS_ID=g.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "g.EP_CODE=i.EP_CODE";
		$line_name = "i.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "e.POT_ID=g.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "g.TP_CODE=i.TP_CODE";
		$line_name = "i.TP_NAME";
	} // end if
	
	$search_per_status[] = 1;

	$search_condition = "";
//	$arr_search_condition[] = "(e.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(e.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(e.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if(trim($search_date_min)){
		$arr_temp = explode("/", $search_date_min);
		$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_min = ($arr_temp[0] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.INV_DATE), 10) >= '$search_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.INV_DATE), 1, 10) >= '$search_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.INV_DATE), 10) >= '$search_date_min')";
	} // end if
	if(trim($search_date_max)){
		$arr_temp = explode("/", $search_date_max);
		$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_max = ($arr_temp[0] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.INV_DATE), 10) <= '$search_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.INV_DATE), 1, 10) <= '$search_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.INV_DATE), 10) <= '$search_date_max')";
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(e.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(e.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	include ("rpt_R008001_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R008001_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]ที่อยู่ระหว่างการสอบสวนทางวินัย". (($show_date_min || $show_date_max)?"||ข้อมูล":"") . (($show_date_min)?"ระหว่างวันที่ $show_date_min ":"") . (($show_date_max)?"ถึงวันที่ $show_date_max":"");
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0801";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	if($DPISDB=="odbc"){
		$cmd = " select			e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, $line_name, e.LEVEL_NO,  h.ORG_NAME,
											d.CR_NAME, c.CRD_NAME , e.DEPARTMENT_ID
						 from			(	
												(
													(
														(
															(
																(
																	(
																		
																			PER_INVEST2 a 
																			inner join PER_INVEST2DTL b on (a.INV_ID=b.INV_ID)
																		) inner join PER_CRIME_DTL c on (b.CRD_CODE=c.CRD_CODE)
																	) inner join PER_CRIME d on (c.CR_CODE=d.CR_CODE)
																) inner join PER_PERSONAL e on (b.PER_ID=e.PER_ID)
															) left join PER_PRENAME f on (e.PN_CODE=f.PN_CODE)
														) left join $position_table g on ($position_join)
													) left join PER_ORG h on (g.ORG_ID=h.ORG_ID)
												) left join $line_table i on ($line_join)
										
						 $search_condition
						 order by		LEFT(trim(a.INV_DATE), 10) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, $line_name, e.LEVEL_NO,h.ORG_NAME,
											d.CR_NAME, c.CRD_NAME, e.DEPARTMENT_ID
						 from			PER_INVEST2 a, PER_INVEST2DTL b, PER_CRIME_DTL c, PER_CRIME d, PER_PERSONAL e, PER_PRENAME f, 
											$position_table g, PER_ORG h, $line_table i
						 where		a.INV_ID=b.INV_ID and b.CRD_CODE=c.CRD_CODE and c.CR_CODE=d.CR_CODE 
											and b.PER_ID=e.PER_ID and e.PN_CODE=f.PN_CODE(+)
											and $position_join(+) and g.ORG_ID=h.ORG_ID and $line_join 
											$search_condition
						 order by		SUBSTR(trim(a.INV_DATE), 1, 10) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, $line_name, e.LEVEL_NO,  h.ORG_NAME,
											d.CR_NAME, c.CRD_NAME, e.DEPARTMENT_ID
						 from			(	
												(
													(
														(
															(
																(
																	(
																		
																			PER_INVEST2 a 
																			inner join PER_INVEST2DTL b on (a.INV_ID=b.INV_ID)
																		) inner join PER_CRIME_DTL c on (b.CRD_CODE=c.CRD_CODE)
																	) inner join PER_CRIME d on (c.CR_CODE=d.CR_CODE)
																) inner join PER_PERSONAL e on (b.PER_ID=e.PER_ID)
															) left join PER_PRENAME f on (e.PN_CODE=f.PN_CODE)
														) left join $position_table g on ($position_join)
													) left join PER_ORG h on (g.ORG_ID=h.ORG_ID)
												) left join $line_table i on ($line_join)
											
						 $search_condition
						 order by		LEFT(trim(a.INV_DATE), 10)  ";
	}
	
	if($select_org_structure==1) { 
		$cmd = str_replace("g.ORG_ID", "e.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PL_NAME = $data[PL_NAME];
		$LEVEL_NO = $data[LEVEL_NO];
		$PT_CODE = trim($data[PT_CODE]);
		$ORG_NAME = $data[ORG_NAME];
		$CR_NAME = $data[CR_NAME];
		$CRD_NAME = $data[CRD_NAME];

		$PER_DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$cmd = "select ORG_NAME from PER_ORG where ORG_ID=$PER_DEPARTMENT_ID";
		$db_dpis3->send_cmd($cmd);
//		$db_dpis->show_error();
		$data3 = $db_dpis3->get_array();
		$PER_DEPARTMENT_NAME = $data3[ORG_NAME];

		if($DEPARTMENT_ID){
			$ORG_PER_NAME = $ORG_NAME;
		}else{
			$ORG_PER_NAME = $PER_DEPARTMENT_NAME." / ".$ORG_NAME;
		}

		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
		$db_dpis3->send_cmd($cmd);
//		$db_dpis->show_error();
		$data3 = $db_dpis3->get_array();
		$LEVEL_NAME=$data3[LEVEL_NAME];
		$POSITION_LEVEL = $data3[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = "$data_row.";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		$arr_content[$data_count][org_name] = $ORG_PER_NAME;
		$arr_content[$data_count][cr_name] = $CR_NAME;
		$arr_content[$data_count][crd_name] = $CRD_NAME;

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	$RTF->open_section(1); 
//	$RTF->set_font($font, 20);
//	$RTF->color("0");	// 0=BLACK
		
	$RTF->add_header("", 0, false);	// header default
	$RTF->add_footer("", 0, false);		// footer default
	$RTF->paragraph();
		
	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "$head_text1<br>";
	$tab_align = "center";
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][org_name];
			$NAME_4 = $arr_content[$data_count][cr_name];
			$NAME_5 = $arr_content[$data_count][crd_name];
			
			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME_1;
			$arr_data[] = $NAME_2;
			$arr_data[] = $NAME_3;
			$arr_data[] = $NAME_4;
			$arr_data[] = $NAME_5;

			$data_align = array("C", "L", "L", "L", "L", "L");

			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
	
?>