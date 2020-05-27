<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	 for ($i=0;$i<count($EDU_TYPE);$i++) {
		if($search_edu) { $search_edu.= ' or '; }
			$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; } 

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=i.PL_CODE";
		$i_name = "i.PL_NAME";
		$order_pl = "b.PL_CODE";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" สายงาน";
		$mgt_code ="b.PM_CODE";
		$select_mgt_code =",b.PM_CODE";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=i.PN_CODE";
		$i_name = "i.PN_NAME";
		$order_pl = "b.PN_CODE";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=i.EP_CODE";
		$i_name = "i.EP_NAME";
		$order_pl = "b.EP_CODE";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=i.TP_CODE";
		$i_name = "i.TP_NAME";
		$order_pl = "b.TP_CODE";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
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
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($order_pl)='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE_EDU) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
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
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	include ("rpt_R004012_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R004012_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานข้อมูล$PERSON_TYPE[$search_per_type] จำแนกตามวุฒิการศึกษา/วิชาเอก/สถานศึกษา";
	$report_code = "R0412";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, h.PN_NAME, a.PER_NAME, a.PER_SURNAME, $i_name as PL_NAME, a.LEVEL_NO, k.POSITION_LEVEL, 
											e.EN_NAME, e.EN_SHORTNAME, f.EM_NAME, g.INS_NAME, d.EDU_INSTITUTE  $select_type_code  $select_mgt_code
						 from			(
												(
													(
														(
															(
																(
																	(
																		(	
																			PER_PERSONAL a 
																			left join $position_table b on $position_join 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
																) left join PER_EDUCNAME e on (d.EN_CODE=e.EN_CODE)
															) left join PER_EDUCMAJOR f on (d.EM_CODE=f.EM_CODE)
														) left join PER_INSTITUTE g on (d.INS_CODE=g.INS_CODE)
													) left join PER_PRENAME h on (a.PN_CODE=h.PN_CODE)
												) left join $line_table i on $line_join
											) left join PER_LEVEL k on (a.LEVEL_NO=k.LEVEL_NO)
											$search_condition and ($search_edu)
						 order by		a.PER_NAME, $order_pl, a.LEVEL_NO, a.PER_ID, d.EDU_SEQ ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, h.PN_NAME, a.PER_NAME, a.PER_SURNAME, $i_name as PL_NAME, a.LEVEL_NO, k.POSITION_LEVEL, 
											e.EN_NAME, e.EN_SHORTNAME, f.EM_NAME, g.INS_NAME, d.EDU_INSTITUTE  $select_type_code  $select_mgt_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_EDUCNAME e, PER_EDUCMAJOR f, PER_INSTITUTE g, PER_PRENAME h, $line_table i, PER_LEVEL k
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PER_ID=d.PER_ID(+) and d.EN_CODE=e.EN_CODE(+) and d.EM_CODE=f.EM_CODE(+) and ($search_edu) and d.INS_CODE=g.INS_CODE(+) and a.LEVEL_NO=k.LEVEL_NO(+)
											and a.PN_CODE=h.PN_CODE(+) and $line_join(+) 
											$search_condition
						 order by		a.PER_NAME, $order_pl, a.LEVEL_NO, a.PER_ID, d.EDU_SEQ ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, h.PN_NAME, a.PER_NAME, a.PER_SURNAME, $i_name as PL_NAME, a.LEVEL_NO, k.POSITION_LEVEL, 
											e.EN_NAME, e.EN_SHORTNAME, f.EM_NAME, g.INS_NAME, d.EDU_INSTITUTE  $select_type_code  $select_mgt_code
						 from			(
												(
													(
														(
															(
																(
																	(
																		(	
																			PER_PERSONAL a 
																			left join $position_table b on $position_join 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
																) left join PER_EDUCNAME e on (d.EN_CODE=e.EN_CODE)
															) left join PER_EDUCMAJOR f on (d.EM_CODE=f.EM_CODE)
														) left join PER_INSTITUTE g on (d.INS_CODE=g.INS_CODE)
													) left join PER_PRENAME h on (a.PN_CODE=h.PN_CODE)
												) left join $line_table i on $line_join
											) left join PER_LEVEL k on (a.LEVEL_NO=k.LEVEL_NO)
											$search_condition and ($search_edu)
						 order by		a.PER_NAME, $order_pl, a.LEVEL_NO, a.PER_ID, d.EDU_SEQ ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo $cmd;
	$data_count = 0;
	$data_row = 0;
	$PER_ID = -1;
	while($data = $db_dpis->get_array()){
		if($PER_ID != $data[PER_ID]){
			$data_row++;

			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PL_NAME = trim($data[PL_NAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
			if(trim($mgt_code)){
				$PM_CODE = trim($data[PM_CODE]);
				$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PM_NAME = trim($data2[PM_NAME]);
			}
			$TMP_ORG_NAME = $data[ORG_NAME];

			$ORG_ID_1 = $data[ORG_ID_1];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME_1 = $data2[ORG_NAME];

			if ($PM_NAME=="ผู้ว่าราชการจังหวัด" || $PM_NAME=="รองผู้ว่าราชการจังหวัด" || $PM_NAME=="ปลัดจังหวัด") {
				$PM_NAME .= $TMP_ORG_NAME;
				$PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $PM_NAME); 
			} elseif ($PM_NAME=="นายอำเภอ") {
				$PM_NAME .= $TMP_ORG_NAME_1;
				$PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $PM_NAME); 
			}
			if(trim($type_code)){
				$PT_CODE = trim($data[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}

			$arr_content[$data_count][order] = "$data_row.";
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME ) . (trim($PM_NAME)?")":"");
		}else{
			$arr_content[$data_count][order] = "";
			$arr_content[$data_count][name] = "";
			$arr_content[$data_count][position] = "";
		} // end if

		$EN_NAME = trim($data[EN_SHORTNAME]);
		if (!$EN_NAME) $EN_NAME = trim($data[EN_NAME]);
		$EM_NAME = trim($data[EM_NAME]);
		$INS_NAME = trim($data[INS_NAME]);
		if (!$INS_NAME) $INS_NAME = trim($data[EDU_INSTITUTE]);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][educname] = $EN_NAME;
		$arr_content[$data_count][educmajor] = $EM_NAME;
		$arr_content[$data_count][institute] = $INS_NAME;

		$data_count++;
	} // end while
	
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
			$ORDER = $arr_content[$data_count][order];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][educname];
			$NAME_4 = $arr_content[$data_count][educmajor];
			$NAME_5 = $arr_content[$data_count][institute];

//			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME_1;
			$arr_data[] = $NAME_2;
			$arr_data[] = $NAME_3;
			$arr_data[] = $NAME_4;
			$arr_data[] = $NAME_5;
			
			$data_align = array( "C","L", "L","L", "L", "L");
			
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