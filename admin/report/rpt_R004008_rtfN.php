<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($search_en_ct_code) $arr_search_condition[] = "(trim(d.CT_CODE)='$search_en_ct_code')";

	$arr_temp = explode("/", $search_date_min);
	$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	$show_date_min = show_date_format($search_date_min, $DATE_DISPLAY);
	$show_date_min = (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date_min):$show_date_min);
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(d.TRN_STARTDATE), 10) >= '$search_date_min')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(d.TRN_STARTDATE), 1, 10) >= '$search_date_min')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(d.TRN_STARTDATE), 10) >= '$search_date_min')";

	$arr_temp = explode("/", $search_date_max);
	$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	$show_date_max = show_date_format($search_date_max, $DATE_DISPLAY);
	$show_date_max = (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date_max):$show_date_max);
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(d.TRN_ENDDATE), 10) <= '$search_date_max')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(d.TRN_ENDDATE), 1, 10) <= '$search_date_max')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(d.TRN_ENDDATE), 10) <= '$search_date_max')";

	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
	include ("rpt_R004008_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R004008_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ลาไปศึกษา ฝึกอบรม สัมมนา ดูงาน".($search_en_ct_code?" ณ ประเทศ$search_en_ct_name":"")."||ตั้งแต่วันที่ $show_date_min ถึงวันที่ $show_date_max";
	$report_code = "R0408";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);
	
	function count_scholar($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type,$select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
							 where		d.SC_TYPE=1 and trim(a.LEVEL_NO) = '$level_no'
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
												and a.PER_ID=d.PER_ID and d.INS_CODE=e.INS_CODE(+) and d.SC_TYPE=1 and trim(a.LEVEL_NO) = '$level_no'
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
							 where		d.SC_TYPE=1 and trim(a.LEVEL_NO) = '$level_no'
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
							 group by	a.PER_ID ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function count_train($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type,$select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
							 where		trim(a.LEVEL_NO) = '$level_no'
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
												and a.PER_ID=d.PER_ID and trim(a.LEVEL_NO) = '$level_no'
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
							 where		trim(a.LEVEL_NO) = '$level_no'
												$search_condition
							 group by	a.PER_ID ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			distinct d.CT_CODE
						 from			(
												(	
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
											$search_condition
						 order by		d.CT_CODE ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct d.CT_CODE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PER_ID=d.PER_ID
											$search_condition
						 order by		d.CT_CODE ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct d.CT_CODE
						 from			(
												(	
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
											$search_condition
						 order by		d.CT_CODE ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$CT_CODE = trim($data[CT_CODE]);
		$arr_country[] = $CT_CODE;
	} // end while

//	echo "<pre>"; print_r($arr_country); echo "</pre>";

	$search_condition = str_replace(" where ", " and ", $search_condition);
	if($DPISDB=="odbc"){
		$cmd = " select			distinct e.CT_CODE
						 from			(
												(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
											) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
						where			d.SC_TYPE=1
											". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
						 order by		e.CT_CODE ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			distinct e.CT_CODE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											and a.PER_ID=d.PER_ID and d.SC_TYPE=1 and d.INS_CODE=e.INS_CODE(+)
											". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
						 order by		e.CT_CODE ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct e.CT_CODE
						 from			(
												(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
											) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
						where			d.SC_TYPE=1
											". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
						 order by		e.CT_CODE ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$CT_CODE = trim($data[CT_CODE]);
		$arr_country[] = $CT_CODE;
	} // end while

//	echo "<pre>"; print_r($arr_country); echo "</pre>";

	array_unique($arr_country);
	$count_data = count($arr_country);
//	echo "<pre>"; print_r($arr_country); echo "</pre>";

	$data_count = 0;
	foreach($arr_country as $CT_CODE){
//		$CT_CODE = trim($data[CT_CODE]);
		if($CT_CODE == ""){
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
		$arr_content[$data_count][name] = "ประเทศ$CT_NAME";
//		$arr_content[$data_count][count_1] = count_person($search_condition, $addition_condition);
//		$GRAND_TOTAL += $arr_content[$data_count][count_1];

		$search_condition = str_replace("d.CT_CODE", "e.CT_CODE", $search_condition);
		$search_condition = str_replace("d.TRN_STARTDATE", "d.SC_STARTDATE", $search_condition);
		$search_condition = str_replace("d.TRN_ENDDATE", "d.SC_ENDDATE", $search_condition);

		$data_count++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "- ศึกษา";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content[$data_count]["count_".$tmp_level_no] = count_scholar(str_pad($tmp_level_no, 2, "0", STR_PAD_LEFT), $search_condition, $addition_condition);
		} // end for
		$addition_condition = str_replace("e.CT_CODE", "d.CT_CODE", $addition_condition);
		$search_condition = str_replace("e.CT_CODE", "d.CT_CODE", $search_condition);
		$search_condition = str_replace("d.SC_STARTDATE", "d.TRN_STARTDATE", $search_condition);
		$search_condition = str_replace("d.SC_ENDDATE", "d.TRN_ENDDATE", $search_condition);

		$data_count++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "- ฝึกอบรม / สัมมนา";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content[$data_count]["count_".$tmp_level_no] = count_train(str_pad($tmp_level_no, 2, "0", STR_PAD_LEFT), $search_condition, ($addition_condition . " and (d.TRN_TYPE in (1, 3))"));
		} // end for

		$data_count++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "- ดูงาน";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content[$data_count]["count_".$tmp_level_no] = count_train(str_pad($tmp_level_no, 2, "0", STR_PAD_LEFT), $search_condition, ($addition_condition . " and (d.TRN_TYPE in (2))"));
		} // end for

		$data_count++;
	} // end foreach
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	$RTF->open_section(1); 
//	$RTF->set_font($font, 20);
//	$RTF->color("0");	// 0=BLACK
		
	$RTF->add_header("", 0, false);	// header default
	$RTF->add_footer("", 0, false);		// footer default
		
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
			$NAME = $arr_content[$data_count][name];
			$COUNT_TOTAL = 0;
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				${"COUNT_".$tmp_level_no} = $arr_content[$data_count]["count_".$tmp_level_no];
				$COUNT_TOTAL += ${"COUNT_".$tmp_level_no};
			} // end for

			$arr_data = (array) null;
			if($REPORT_ORDER=="COUNTRY"){
				$arr_data[] = $NAME;
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$arr_data[] = "";
				} // end for
				$arr_data[] = "";
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			}elseif($REPORT_ORDER=="CONTENT"){
				$arr_data[] = $NAME;
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$arr_data[] = ${"COUNT_".$tmp_level_no};
				} // end for
				$arr_data[] =  $COUNT_TOTAL;
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			} // end if
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