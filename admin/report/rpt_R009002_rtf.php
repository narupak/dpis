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
		$position_join = "b.POS_ID=c.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "c.PL_CODE=e.PL_CODE";
		$line_code = "c.PL_CODE";
		$line_name = "e.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);		
		$line_title=" สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "c.PN_CODE=e.PN_CODE";
		$line_code = "c.PN_CODE";
		$line_name = "e.PN_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "c.EP_CODE=e.EP_CODE";
		$line_code = "c.EP_CODE";
		$line_name = "e.EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "c.TP_CODE=e.TP_CODE";
		$line_code = "c.TP_CODE";
		$line_name = "e.TP_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);		
		$line_title=" ชื่อตำแหน่ง";
	} // end if	

	$search_condition = "";
//	$arr_search_condition[] = "(b.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(b.PER_TYPE = 1)";
	$arr_search_condition[] = "(b.PER_STATUS = 1)";
  	if(trim($search_budget_year)){ 
		if($DPISDB=="odbc"){ 
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_budget_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_budget_year - 543)."-10-01')";
		}elseif($DPISDB=="oci8"){
			$arr_search_condition[] = "(SUBSTR(a.KF_START_DATE, 1, 10) >= '". ($search_budget_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(SUBSTR(a.KF_END_DATE, 1, 10) < '". ($search_budget_year - 543)."-10-01')";
		}elseif($DPISDB=="mysql"){
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_budget_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_budget_year - 543)."-10-01')";
		} // end if
	} // end if
	$arr_search_condition[] = "(a.KF_CYCLE in (". implode(",", $search_kf_cycle) ."))";
	if($search_diff_type != 0){
		switch($search_diff_type){
			case 1 :
				if($DPISDB=="odbc"){
					if($plus_diff) $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) = $plus_diff)";
					else $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) >= 0)";
				}elseif($DPISDB=="oci8"){
					if($plus_diff) $arr_search_condition[] = "((nvl(g.KC_EVALUATE, 0) - nvl(h.PC_TARGET_LEVEL, 0)) = $plus_diff)";
					else $arr_search_condition[] = "((nvl(g.KC_EVALUATE, 0) - nvl(h.PC_TARGET_LEVEL, 0)) >= 0)";
				}elseif($DPISDB=="mysql"){
					if($plus_diff) $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) = $plus_diff)";
					else $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) >= 0)";
				} // end if
			break;
			case -1 :
				if($DPISDB=="odbc"){
					if($minus_diff) $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) = ". ($minus_diff * -1) .")";
					else $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) < 0)";
				}elseif($DPISDB=="oci8"){
					if($minus_diff) $arr_search_condition[] = "((nvl(g.KC_EVALUATE, 0) - nvl(h.PC_TARGET_LEVEL, 0)) = ". ($minus_diff * -1) .")";
					else $arr_search_condition[] = "((nvl(g.KC_EVALUATE, 0) - nvl(h.PC_TARGET_LEVEL, 0)) < 0)";
				}elseif($DPISDB=="mysql"){
					if($minus_diff) $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) = ". ($minus_diff * -1) .")";
					else $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) < 0)";
				} // end if
			break;
		} // end switch case
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;
	
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
			else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= $line_search_name;
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$search_pv_code')";
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
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	include ("rpt_R009002_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R009002_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

//	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานแผนพัฒนาผลการปฏิบัติงานรายบุคคล ปีงบประมาณ $search_budget_year";
	if(in_array(1, $search_kf_cycle)) $report_title .= " ครั้งที่ 1";
	if(in_array(2, $search_kf_cycle)) $report_title .= (in_array(1, $search_kf_cycle)?" และ":"")." ครั้งที่ 2";
	if($search_diff_type == -1) $report_title .= "||(เฉพาะระดับของสมรรถนะต่ำกว่าที่กำหนด)";
	elseif($search_diff_type == 1) $report_title .= "||(เฉพาะระดับของสมรรถนะสูงกว่าที่กำหนด)";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0902";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.PT_CODE, c.ORG_ID, d.ORG_NAME, $line_name,
											g.CP_CODE, i.CP_NAME, g.KC_EVALUATE, g.PC_TARGET_LEVEL
						 from			(
												(
													(
														(
															(
																(
																	(
																		PER_KPI_FORM a
																		inner join PER_KPI_COMPETENCE g on (a.KF_ID=g.KF_ID)
																	) inner join PER_COMPETENCE i on (a.DEPARTMENT_ID=i.DEPARTMENT_ID and g.CP_CODE=i.CP_CODE)
																) inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
															) inner join PER_POSITION_COMPETENCE h on (b.POS_ID=h.POS_ID) and (g.CP_CODE=h.CP_CODE) and h.PER_TYPE=1
														) inner join $position_table c on ($position_join)
													) inner join PER_ORG d on (c.ORG_ID=d.ORG_ID)
												) inner join $line_table e on ($line_join)
											) left join PER_PRENAME f on (b.PN_CODE=f.PN_CODE)						 
						 $search_condition
						 order by		c.ORG_ID, a.PER_ID, (g.KC_EVALUATE - g.PC_TARGET_LEVEL), g.CP_CODE ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.PT_CODE, c.ORG_ID, d.ORG_NAME, $line_name,
											g.CP_CODE, i.CP_NAME, g.KC_EVALUATE, g.PC_TARGET_LEVEL
						 from			PER_KPI_FORM a, PER_PERSONAL b, $position_table c, PER_ORG d, $line_table e, PER_PRENAME f, 
						 					PER_KPI_COMPETENCE g, PER_POSITION_COMPETENCE h, PER_COMPETENCE i
						 where		a.PER_ID=b.PER_ID and $position_join and c.ORG_ID=d.ORG_ID and $line_join  
						 					and b.PN_CODE=f.PN_CODE(+) and a.KF_ID=g.KF_ID and b.POS_ID=h.POS_ID and g.CP_CODE=h.CP_CODE 
											and a.DEPARTMENT_ID=i.DEPARTMENT_ID and g.CP_CODE=i.CP_CODE and h.PER_TYPE=1 
											$search_condition
						 order by		c.ORG_ID, a.PER_ID, (nvl(g.KC_EVALUATE, 0) - nvl(g.PC_TARGET_LEVEL, 0)), g.CP_CODE ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.PT_CODE, c.ORG_ID, d.ORG_NAME, $line_name,
											g.CP_CODE, i.CP_NAME, g.KC_EVALUATE, g.PC_TARGET_LEVEL
						 from			(
												(
													(
														(
															(
																(
																	(
																		PER_KPI_FORM a
																		inner join PER_KPI_COMPETENCE g on (a.KF_ID=g.KF_ID)
																	) inner join PER_COMPETENCE i on (a.DEPARTMENT_ID=i.DEPARTMENT_ID and g.CP_CODE=i.CP_CODE)
																) inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
															) inner join PER_POSITION_COMPETENCE h on (b.POS_ID=h.POS_ID) and (g.CP_CODE=h.CP_CODE) and h.PER_TYPE=1
														) inner join $position_table c on ($position_join)
													) inner join PER_ORG d on (c.ORG_ID=d.ORG_ID)
												) inner join $line_table e on ($line_join)
											) left join PER_PRENAME f on (b.PN_CODE=f.PN_CODE)						 
						 $search_condition
						 order by		c.ORG_ID, a.PER_ID, (g.KC_EVALUATE - g.PC_TARGET_LEVEL), g.CP_CODE ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$ORG_ID = -1;
	$PER_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($ORG_ID != $data[ORG_ID]){
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];
			if($ORG_NAME=="" || $ORG_NAME=="NULL"  || $ORG_NAME =="-")	$ORG_NAME="[ไม่ระบุ $ORG_TITLE]";
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][name] = $ORG_NAME;

			$data_row = 0;
			$data_count++;
		} // end if
		
		$DATA_ORD = "";
		$PN_NAME = "";
		$PER_NAME = "";
		$PER_SURNAME = "";
		$PL_NAME = "";
		$PT_NAME = "";
		$LEVEL_NO = "";
		
		if($PER_ID != $data[PER_ID]){ 
			$data_row++;

			$PER_ID = $data[PER_ID];
			$PN_NAME = $data[PN_NAME];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$LEVEL_NO = $data[LEVEL_NO];
			$PL_NAME = $data[PL_NAME];
			
			$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis->show_error();
			$data3 = $db_dpis3->get_array();
			$LEVEL_NAME=$data3[LEVEL_NAME];
			$POSITION_LEVEL = $data3[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];
	
			$DATA_ORD = "$data_row.";
		} // end if

		$CP_CODE = $data[CP_CODE];
		$CP_NAME = $data[CP_NAME];
		$KC_EVALUATE = $data[KC_EVALUATE];
		$PC_TARGET_LEVEL[$CP_CODE] = $data[PC_TARGET_LEVEL];
//		$PC_DIFF = $KC_EVALUATE - $PC_TARGET_LEVEL;
		$PC_DIFF = ($PC_TARGET_LEVEL[$CP_CODE] - $KC_EVALUATE);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $DATA_ORD;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][perline] = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"";
		$arr_content[$data_count][cp_name] = $CP_NAME;
		$arr_content[$data_count][kc_evaluate] = $KC_EVALUATE;
		$arr_content[$data_count][pc_target_level] = $PC_TARGET_LEVEL[$CP_CODE];
		$arr_content[$data_count][pc_diff] = $PC_DIFF;
		
		$data_count++;
	} // end while
	
//echo "<pre>"; print_r($arr_content); echo "</pre>";
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
			$NAME = $arr_content[$data_count][name];
			$PL_NAME = $arr_content[$data_count][perline];
			$CP_NAME = $arr_content[$data_count][cp_name];
			$KC_EVALUATE = $arr_content[$data_count][kc_evaluate];
			$PC_TARGET_LEVEL = $arr_content[$data_count][pc_target_level];
			$PC_DIFF = $arr_content[$data_count][pc_diff];

			if($REPORT_ORDER == "ORG"){
				$arr_data = (array) null;
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$data_align = array("L","L","L","L","L","L");
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $NAME;
				$arr_data[] = $PL_NAME;
				$arr_data[] = $CP_NAME;
				$arr_data[] = $PC_TARGET_LEVEL;
				$arr_data[] = $KC_EVALUATE;
				$arr_data[] = $PC_DIFF;
				$data_align = array("C","L","L","L","R","R","R");
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}
		} // end for

	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
	
?>