<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	 	if($search_edu) { $search_edu.= ' or '; }
		$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
	} 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";	
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";	
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";	
	} // end if

//	if(trim($RPTORD_LIST)) $arr_rpt_order = explode("|", $RPTORD_LIST);
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);
//	$arr_rpt_order = array("EDUCNAME", "EDUCCOUNTRY");

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	
	if($search_en_code) $arr_search_condition[] = "(trim(d.EN_CODE)=trim('$search_en_code'))";
	if($search_en_ct_code) $arr_search_condition[] = "(trim(d.CT_CODE_EDU)=trim('$search_en_ct_code'))";

	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");

	include ("rpt_R002007_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R002007_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]จำแนกตามวุฒิ/ประเทศ";
	$report_title .= (trim($search_en_code)?"||$search_en_name":"") . (trim($search_en_ct_code)?((trim($search_en_code)?" ":"||")."ประเทศ$search_en_ct_name"):"");
	//if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);	
	$report_code = "R0207";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	function count_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type, $search_edu;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
								 						(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on $position_join 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PER_ID=d.PER_ID  and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
								 						(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on $position_join 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID ";
			} // end if
		if($select_org_structure==1) {	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
		$count_person = $db_dpis2->send_cmd($cmd);
//	$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL,
											$line_code as PL_CODE, b.ORG_ID, c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2,
											d.EN_CODE, f.EN_NAME, d.EM_CODE, d.INS_CODE, e.INS_NAME, d.CT_CODE_EDU, d.EDU_INSTITUTE  $select_type_code
						 from			(
												(
													(
														(
															(	
															PER_PERSONAL a 
															left join $position_table b on $position_join 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
										) inner join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
											$search_condition  and ($search_edu)
						 order by		d.EN_CODE, d.CT_CODE_EDU, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, a.LEVEL_NO, $line_code ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL,
											$line_code as PL_CODE, b.ORG_ID, c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2,
											d.EN_CODE, f.EN_NAME, d.EM_CODE, d.INS_CODE, e.INS_NAME, d.CT_CODE_EDU, d.EDU_INSTITUTE  $select_type_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f, PER_LEVEL g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PER_ID=d.PER_ID  and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE and a.LEVEL_NO=g.LEVEL_NO
											$search_condition
						 order by		d.EN_CODE, d.CT_CODE_EDU, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, a.LEVEL_NO, $line_code ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL,
											$line_code as PL_CODE, b.ORG_ID, c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2,
											d.EN_CODE, f.EN_NAME, d.EM_CODE, d.INS_CODE, e.INS_NAME, d.CT_CODE_EDU, d.EDU_INSTITUTE $select_type_code
						 from			(
												(
													(
														(
															(	
															PER_PERSONAL a 
															left join $position_table b on $position_join 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
										) inner join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
											$search_condition  and ($search_edu)
						 order by		d.EN_CODE, d.CT_CODE_EDU, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, a.LEVEL_NO, $line_code ";
	}
	if($select_org_structure==1) {	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "<br>$cmd<br>";

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
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, true, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";
			
	if($count_data){

		$data_count = $data_row = $group_count = 0;
		$EN_CODE = -1;
		$CT_CODE = -1;
		while($data = $db_dpis->get_array()){
			$data_count++;
						
			if($EN_CODE != trim($data[EN_CODE]) || $CT_CODE != trim($data[CT_CODE])){
				$group_count++;
								
				$EN_CODE = trim($data[EN_CODE]);
				if(!trim($search_en_code)){
					if($EN_CODE) $EN_NAME = trim($data[EN_NAME]);
				} // end if
	
				$CT_CODE = trim($data[CT_CODE]);	
				if(!trim($search_en_ct_code)){
					if($CT_CODE){
						$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$CT_NAME = ($EN_CODE?" ":"") . "ประเทศ" . trim($data2[CT_NAME]);
					}
				} // end if

				if(!trim($search_en_code) || !trim($search_en_ct_code)){
					// on pdf is new page
					
					if(trim($search_en_code)) $count_person = count_person($search_condition, "(trim(d.CT_CODE_EDU)='$CT_CODE')");
					elseif(trim($search_en_ct_code)) $count_person = count_person($search_condition, "(trim(d.EN_CODE)='$EN_CODE')");
					else $count_person = count_person($search_condition, "(trim(d.EN_CODE)='$EN_CODE' and trim(d.CT_CODE_EDU)='$CT_CODE')");

//					$pdf->SetFont($fontb,'',14);
//					if($group_count > 1) $pdf->Cell(200,5,"",0,1,"L");
//					$pdf->Cell(200,7,((($NUMBER_DISPLAY==2)?convert2thaidigit($group_count):$group_count). ' ' . $EN_NAME . $CT_NAME),0,1,"L");
//					if($group_count > 1) { 
//						$result = $RTF->add_text_line("", 7, "", "C", "", "14", "b", 0, 0);
//						if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
//					}
					$RTF->paragraph();
					$result = $RTF->add_text_line(($group_count. ' ' . $EN_NAME), 7, "", "L", "", "14", "bUI", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
					$RTF->paragraph();
					
					$data_row = 0;
				} // end if
			} // end if		
			if($data_row == 0) 	$RTF->print_tab_header();

			$data_row++;

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = "$PN_NAME $PER_NAME $PER_SURNAME";
			
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);

			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);

			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select $line_name as PL_NAME from $line_table b where trim($line_code)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if($search_per_type == 1){
				$PL_NAME = trim($data2[PL_NAME]) . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"");
			}else{	// 2 || 3 || 4
				$PL_NAME = trim($data2[PN_NAME]) ;
			}
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];

			$EM_CODE = trim($data[EM_CODE]);
			$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EM_NAME = trim($data2[EM_NAME]);

			$INS_CODE = trim($data[INS_CODE]);
			$INS_NAME = $data[INS_NAME];
			if (!$INS_NAME) $INS_NAME = $data[EDU_INSTITUTE];

			$arr_data = (array) null;
			$arr_data[] = $data_row. ' ' .$FULLNAME;
			$arr_data[] = $PL_NAME;
			$arr_data[] = $ORG_NAME;
			$arr_data[] = $EM_NAME;
			$arr_data[] = $INS_NAME;

			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 

	$RTF->display($fname);	
?>