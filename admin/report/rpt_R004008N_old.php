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

	$cmd = " select LEVEL_NO, LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL 
					where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO,LEVEL_NO ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){ 
		$ARR_LEVEL_NO[] = $data[LEVEL_NO];		
		$ARR_LEVEL_SHORTNAME[] = $data[LEVEL_SHORTNAME];
	}
	
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

	if($export_type=="report"){
		include ("../report/rpt_condition3.php");
		}else if($export_type=="graph"){
		include ("../../admin/report/rpt_condition3.php");	//เงื่อนไขที่ต้องการแสดงผล
		}	
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ลาไปศึกษา ฝึกอบรม สัมมนา ดูงาน".($search_en_ct_code?" ณ ประเทศ$search_en_ct_name":"")."||ตั้งแต่วันที่ $show_date_min ถึงวันที่ $show_date_max";
	$report_code = "R0408";
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

	$heading_width[0] = "100";
	$heading_width[1] = "12";
	$heading_width[2] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $ARR_LEVEL_NO,$ARR_LEVEL_SHORTNAME;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ประเทศ",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] * count($ARR_LEVEL_NO)) ,7,"ระดับตำแหน่ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"รวม",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
			$pdf->Cell($heading_width[1] ,7,"$tmp_level_shortname",'LTBR',0,'C',1);
		} // end for
		$pdf->Cell($heading_width[2] ,7,"",'LBR',1,'C',1);
	} // function		

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
if($export_type=="report"){
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_TOTAL = 0;
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				${"COUNT_".$tmp_level_no} = $arr_content[$data_count]["count_".$tmp_level_no];
				$COUNT_TOTAL += ${"COUNT_".$tmp_level_no};
			} // end for
			
			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			if($REPORT_ORDER=="COUNTRY"){
				$border = "";
				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$pdf->MultiCell($heading_width[0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$pdf->Cell($heading_width[1], 7, "", $border, 0, 'R', 0);
				} // end for
				$pdf->Cell($heading_width[2], 7, "", $border, 0, 'R', 0);
			}elseif($REPORT_ORDER=="CONTENT"){
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$pdf->MultiCell($heading_width[0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$pdf->Cell($heading_width[1], 7, (${"COUNT_".$tmp_level_no}?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(${"COUNT_".$tmp_level_no})):number_format(${"COUNT_".$tmp_level_no})):"-"), $border, 0, 'R', 0);
				} // end for
				$pdf->Cell($heading_width[2], 7, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), $border, 0, 'R', 0);
			} // end if

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=(count($ARR_LEVEL_NO)+1); $i++){
				if($i>=1 && $i<=count($ARR_LEVEL_NO)){
					$line_start_y = $start_y;		$line_start_x += $heading_width[1];
					$line_end_y = $max_y;		$line_end_x += $heading_width[1];
				}elseif($i > count($ARR_LEVEL_NO)){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i - (count($ARR_LEVEL_NO)-1)];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i - (count($ARR_LEVEL_NO)-1)];
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
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
	}else if($export_type=="graph"){//if($export_type=="report"){
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
	$arr_categories = array();
	for($i=0;$i<count($arr_content);$i++){
		if($arr_content[$i][type]==$arr_rpt_order[0]){
			$arr_categories[$i] = $arr_content[$i][name];
			for($j=2;$j<count($arr_content_key);$j++){
				$arr_series_caption_data[$j][] = $arr_content[$i][$arr_content_key[$j]];
				}//for($j=2;$j<count($arr_content_key);$j++){
			}//if($arr_content[$i][type]==$arr_rpt_order[0]){
		}//for($i=0;$i<count($arr_content);$i++){
//	echo "<pre>"; print_r($arr_series_caption_data); echo "</pre>";
	for($j=2;$j<count($arr_content_key);$j++){
		$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]).";".${"GRAND_TOTAL_".($j-1)};
		}
	
	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	if(!$setWidth) $setWidth = "800";
	if(!$setHeight) $setHeight = "600";
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