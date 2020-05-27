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
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("POS_NO", "NAME", "LINE", "LEVEL", "ORG", "ORG_1", "ORG_2"); 
	$order_by = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POS_NO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "b.POS_NO_NAME, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO)), c.POEM_NO_NAME, IIf(IsNull(c.POEM_NO), 0, CLng(c.POEM_NO)), 
																						f.POEMS_NO_NAME, IIf(IsNull(f.POEMS_NO), 0, CLng(f.POEMS_NO)), i.POT_NO_NAME, IIf(IsNull(i.POT_NO), 0, CLng(i.POT_NO))";
				elseif($DPISDB=="oci8") $order_by .= "b.POS_NO_NAME, TO_NUMBER(b.POS_NO), c.POEM_NO_NAME, TO_NUMBER(c.POEM_NO), 
																							f.POEMS_NO_NAME, TO_NUMBER(f.POEMS_NO), i.POT_NO_NAME, TO_NUMBER(i.POT_NO)";
				elseif($DPISDB=="mysql") $order_by .= "b.POS_NO_NAME, b.POS_NO+0, c.POEM_NO_NAME, c.POEM_NO+0, f.POEMS_NO_NAME, f.POEMS_NO+0, i.POT_NO_NAME, i.POT_NO+0";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE, c.PN_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO desc";
				break;
			case "MINISTRY" :

				break;
			case "DEPARTMENT" :

				break; 
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, b.ORG_ID, e.ORG_SEQ_NO, e.ORG_CODE, c.ORG_ID, g.ORG_SEQ_NO, g.ORG_CODE, f.ORG_ID, j.ORG_SEQ_NO, j.ORG_CODE, i.ORG_ID";
				else if($select_org_structure==1) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID, e.ORG_SEQ_NO, e.ORG_CODE, c.ORG_ID, g.ORG_SEQ_NO, g.ORG_CODE, f.ORG_ID, j.ORG_SEQ_NO, j.ORG_CODE, i.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1, c.ORG_ID_1, f.ORG_ID_1, i.ORG_ID_1";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID, c.ORG_ID_1, f.ORG_ID_1, i.ORG_ID_1";				
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2, c.ORG_ID_2, f.ORG_ID_2, i.ORG_ID_2";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID, c.ORG_ID_2, f.ORG_ID_2, i.ORG_ID_2";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

	$search_condition = "";
	if(trim($search_salary_date)){
		$search_salary_date =  save_date($search_salary_date);
		$show_salary_date = show_date_format($search_salary_date, 3);
	} // end if
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (a.PER_STATUS = 1) and (b.POS_ID >= 0 or c.POEM_ID >= 0 or f.POEMS_ID >=0 or i.POT_ID >=0) )";
//	$arr_search_condition[] = "(a.PER_TYPE=1)";	
	
	$list_type_text = $ALL_REPORT_TITLE;
/*	if($select_org_structure==0){ $list_type_text .= " โครงสร้างตามกฏหมาย"; 	}
	elseif($select_org_structure==1){ $list_type_text .= " โครงสร้างตามมอบหมายงาน"; }  */		
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
		$arr_search_condition[] = "(d.OT_CODE='01' or e.OT_CODE='01')";
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
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(d.OT_CODE='02' or e.OT_CODE='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
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
		$arr_search_condition[] = "(d.OT_CODE='03' or e.OT_CODE='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(d.OT_CODE='04' or e.OT_CODE='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id or c.ORG_ID = $search_org_id or f.ORG_ID = $search_org_id or i.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			}
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1 or c.ORG_ID_1 = $search_org_id_1 or f.ORG_ID_1 = $search_org_id_1 or i.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			}
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2 or c.ORG_ID_2 = $search_org_id_2 or f.ORG_ID_2 = $search_org_id_2 or i.ORG_ID_2 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			}
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			}
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			}
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			}
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if(trim($search_pl_code) && trim($search_pn_code)  && trim($search_ep_code) ){ 
			$search_pl_code = trim($search_pl_code);
			$search_pn_code = trim($search_pn_code);
			$search_ep_code = trim($search_ep_code);
			$search_tp_code = trim($search_tp_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code') or (a.PER_TYPE=2 and trim(c.PN_CODE)='$search_pn_code') or (a.PER_TYPE=3 and trim(f.EP_CODE)='$search_ep_code')  or (a.PER_TYPE=4 and trim(i.TP_CODE)='$search_tp_code'))";
			$list_type_text .= "$search_pl_name, $search_pn_name,$search_ep_code,$search_tp_code";
		}elseif(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code') or (a.PER_TYPE=2 and trim(c.PN_CODE) like '%') or (a.PER_TYPE=3 and trim(f.EP_CODE) like '%') or (a.PER_TYPE=4 and trim(i.TP_CODE) like '%'))";
			$list_type_text .= "$search_pl_name";
		}elseif(trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%') or (a.PER_TYPE=2 and trim(c.PN_CODE)='$search_pn_code') or (a.PER_TYPE=3 and trim(f.EP_CODE) like '%') or (a.PER_TYPE=4 and trim(i.TP_CODE) like '%'))";
			$list_type_text .= "$search_pn_name";
		}elseif(trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%') or (a.PER_TYPE=2 and trim(c.PN_CODE) like '%') or (a.PER_TYPE=3 and trim(f.EP_CODE) ='$search_ep_code') or (a.PER_TYPE=4 and trim(i.TP_CODE) ='$search_tp_code'))";
			$list_type_text .= "$search_ep_name";
		}elseif(trim($search_tp_code)){
			$search_tp_code = trim($search_tp_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%') or (a.PER_TYPE=2 and trim(c.PN_CODE) like '%') or (a.PER_TYPE=3 and trim(f.EP_CODE) like '%') or (a.PER_TYPE=4 and trim(i.TP_CODE) ='$search_tp_code'))";
			$list_type_text .= "$search_tp_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE) = '$search_ct_code' or trim(e.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$search_pv_code' or trim(e.PV_CODE) = '$search_pv_code')";
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
			if($select_org_structure==0) $arr_search_condition[] = "(trim(d.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	include ("rpt_R004007_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R004007_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	if(in_array(1, $search_per_type) && in_array(2, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการและลูกจ้างประจำ ตำแหน่ง สังกัด";
	else if(in_array(1, $search_per_type) && in_array(3, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการและพนักงานราชการ ตำแหน่ง สังกัด";	
	else if(in_array(1, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการและลูกจ้างชั่วคราว ตำแหน่ง สังกัด";	
	
	else if(in_array(2, $search_per_type) && in_array(3, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างประจำและพนักงานราชการ ตำแหน่ง สังกัด";
	else if(in_array(2, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างประจำและลูกจ้างชั่วคราว ตำแหน่ง สังกัด";
	
	else if(in_array(3, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อพนักงานราชการและลูกจ้างชั่วคราว ตำแหน่ง สังกัด";

	else if(in_array(1, $search_per_type) && in_array(2, $search_per_type) && in_array(3, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการ , ลูกจ้างประจำ และพนักงานราชการ ตำแหน่ง สังกัด";	
	else if(in_array(1, $search_per_type) && in_array(2, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการ , ลูกจ้างประจำ และลูกจ้างชั่วคราว ตำแหน่ง สังกัด";	
	else if(in_array(1, $search_per_type) && in_array(3, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการ, พนักงานราชการ  และลูกจ้างชั่วคราว ตำแหน่ง สังกัด";	
	else if(in_array(2, $search_per_type) && in_array(3, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างประจำ,พนักงานราชการ  และลูกจ้างชั่วคราว ตำแหน่ง สังกัด";	
	
	else if(in_array(1, $search_per_type) && in_array(2, $search_per_type) && in_array(3, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการ , ลูกจ้างประจำ พนักงานราชการ และลูกจ้างชั่วคราว ตำแหน่ง สังกัด";
	
	elseif(in_array(1, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการ ตำแหน่ง สังกัด";
	elseif(in_array(2, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างประจำ ตำแหน่ง สังกัด";
	elseif(in_array(3, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อพนักงานราชการ ตำแหน่ง สังกัด";
	elseif(in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างชั่วคราว  ตำแหน่ง สังกัด";
	$report_code = "R0407";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);
	
	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, h.POSITION_TYPE, h.POSITION_LEVEL, 
										a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, b.PL_CODE, b.PM_CODE, b.PT_CODE, b.POS_NO_NAME, b.POS_NO, b.ORG_ID, b.ORG_ID_1, 
										b.ORG_ID_2, b.CL_NAME, d.ORG_NAME, a.PER_CARDNO,  a.PER_BIRTHDATE,  a.PER_RETIREDATE,	c.PN_CODE as EMP_PL_CODE, 
										c.POEM_NO_NAME as EMP_POS_NO_NAME, c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID,
										e.ORG_NAME as EMP_ORG_NAME, f.EP_CODE as EMPSER_PL_CODE, f.POEMS_NO_NAME as EMPSER_POS_NO_NAME, 
										f.POEMS_NO as EMPSER_POS_NO, f.ORG_ID as EMPSER_ORG_ID, g.ORG_NAME as EMPSER_ORG_NAME,
										i.TP_CODE as POT_PL_CODE, i.POT_NO_NAME as POT_POS_NO_NAME, i.POT_NO as POT_POS_NO, i.ORG_ID as POT_ORG_ID,
										j.ORG_NAME as POT_ORG_NAME										
						 from (
									(
										(
											( 	
												(
													(	
														(
															(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)
												) left join PER_ORG e on (c.ORG_ID=e.ORG_ID)
											) left join PER_POS_EMPSER f on (a.POEMS_ID=f.POEMS_ID)
										) left join PER_ORG g on (f.ORG_ID=g.ORG_ID)
									) left join PER_POS_TEMP i on (a.POT_ID=i.POT_ID)	
								) left join PER_ORG j on (i.ORG_ID=j.ORG_ID)
							) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
										$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, h.POSITION_TYPE, h.POSITION_LEVEL, 
										a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, b.PL_CODE, b.PM_CODE, b.PT_CODE, b.POS_NO_NAME, b.POS_NO, b.ORG_ID, b.ORG_ID_1, 
										b.ORG_ID_2, b.CL_NAME, d.ORG_NAME, a.PER_CARDNO,  a.PER_BIRTHDATE,  a.PER_RETIREDATE,	c.PN_CODE as EMP_PL_CODE, 
										c.POEM_NO_NAME as EMP_POS_NO_NAME, c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID,
										e.ORG_NAME as EMP_ORG_NAME, f.EP_CODE as EMPSER_PL_CODE, f.POEMS_NO_NAME as EMPSER_POS_NO_NAME, 
										f.POEMS_NO as EMPSER_POS_NO, f.ORG_ID as EMPSER_ORG_ID, g.ORG_NAME as EMPSER_ORG_NAME,
										i.TP_CODE as POT_PL_CODE, i.POT_NO_NAME as POT_POS_NO_NAME, i.POT_NO as POT_POS_NO, i.ORG_ID as POT_ORG_ID,
										j.ORG_NAME as POT_ORG_NAME										
						 from		PER_PERSONAL a, PER_POSITION b, PER_POS_EMP c, PER_ORG d, PER_ORG e, 
						 				PER_POS_EMPSER f, PER_ORG g, PER_LEVEL h, PER_POS_TEMP i, PER_ORG j
						 where	a.POS_ID=b.POS_ID(+) and b.ORG_ID=d.ORG_ID(+) and a.POEM_ID=c.POEM_ID(+) and c.ORG_ID=e.ORG_ID(+) and a.POEMS_ID=f.POEMS_ID(+) and f.ORG_ID=g.ORG_ID(+) and a.POT_ID=i.POT_ID(+) and i.ORG_ID=j.ORG_ID(+)
						 				and a.LEVEL_NO=h.LEVEL_NO(+) 
										$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, h.POSITION_TYPE, h.POSITION_LEVEL, 
										a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, b.PL_CODE, b.PM_CODE, b.PT_CODE, b.POS_NO_NAME, b.POS_NO, b.ORG_ID, b.ORG_ID_1, 
										b.ORG_ID_2, b.CL_NAME, d.ORG_NAME, a.PER_CARDNO,  a.PER_BIRTHDATE,  a.PER_RETIREDATE,	c.PN_CODE as EMP_PL_CODE, 
										c.POEM_NO_NAME as EMP_POS_NO_NAME, c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID,
										e.ORG_NAME as EMP_ORG_NAME, f.EP_CODE as EMPSER_PL_CODE, f.POEMS_NO_NAME as EMPSER_POS_NO_NAME, 
										f.POEMS_NO as EMPSER_POS_NO, f.ORG_ID as EMPSER_ORG_ID, g.ORG_NAME as EMPSER_ORG_NAME,
										i.TP_CODE as POT_PL_CODE, i.POT_NO_NAME as POT_POS_NO_NAME, i.POT_NO as POT_POS_NO, i.ORG_ID as POT_ORG_ID,
										j.ORG_NAME as POT_ORG_NAME										
						 from (
									(
										(
											( 	
												(
													(	
														(
															(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)
												) left join PER_ORG e on (c.ORG_ID=e.ORG_ID)
											) left join PER_POS_EMPSER f on (a.POEMS_ID=f.POEMS_ID)
										) left join PER_ORG g on (f.ORG_ID=g.ORG_ID)
									) left join PER_POS_TEMP i on (a.POT_ID=i.POT_ID)	
								) left join PER_ORG j on (i.ORG_ID=j.ORG_ID)
							) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
										$search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
		//	echo $cmd;
//$db_dpis->show_error();

//new format****************************************************************
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
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]);
			$CL_NAME = trim($data[CL_NAME]);
			$POSITION_TYPE = trim($data[POSITION_TYPE]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);

			$ORG_ID_1 = $data[ORG_ID_1];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data2[ORG_NAME];
			
			if($PER_TYPE==1){
				$POS_NO = $data[POS_NO_NAME].' '.$data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PM_CODE = trim($data[PM_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_NAME = $data[ORG_NAME];

				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE) = '$PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PM_NAME = trim($data2[PM_NAME]);
				if ($PM_NAME=="ผู้ว่าราชการจังหวัด" || $PM_NAME=="รองผู้ว่าราชการจังหวัด" || $PM_NAME=="ปลัดจังหวัด") {
					$PM_NAME .= $ORG_NAME;
					$PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $PM_NAME); 
				} elseif ($PM_NAME=="นายอำเภอ") {
					$PM_NAME .= $ORG_NAME_1;
					$PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $PM_NAME); 
				}

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_NO = $data[EMP_POS_NO_NAME].$data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_NAME = $data[EMP_ORG_NAME];

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
			}elseif($PER_TYPE==3){
				$POS_NO = $data[EMPSER_POS_NO_NAME].$data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_NAME = $data[EMPSER_ORG_NAME];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
		//	$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			} elseif($PER_TYPE==4){
				$POS_NO = $data[POT_POS_NO_NAME].$data[POT_POS_NO];
				$PL_CODE = trim($data[POT_PL_CODE]);
				$ORG_ID = $data[POT_ORG_ID];
				$ORG_NAME = $data[POT_ORG_NAME];
				
				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
		//	$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
			} 
			$PL_NAME = trim($PL_NAME);

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE) = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";

			$PER_SALARY = number_format($data[PER_SALARY]);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
			
			$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);
/*
			$POS_CHANGE_DATE = show_date_format($data[POS_CHANGE_DATE],$DATE_DISPLAY);
*/
			
			$cmd = " select 	a.DC_CODE, b.DC_SHORTNAME, b.DC_NAME
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.DC_CODE=b.DC_CODE and a.PER_ID=$PER_ID and b.DC_TYPE in (1,2)
							 order by b.DC_ORDER ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DC_CODE = trim($data2[DC_CODE]);
			$DC_NAME = "";
			if($DC_CODE) $DC_NAME = (trim($data2[DC_SHORTNAME])?$data2[DC_SHORTNAME]:$data2[DC_NAME]);
			$PER_CARDNO = card_no_format($PER_CARDNO, $CARD_NO_DISPLAY);

//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] = $data_count;
			$arr_data[] = $FULLNAME;
			$arr_data[] = $PER_CARDNO;
			$arr_data[] = $PER_BIRTHDATE;
			$arr_data[] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME ) . (trim($PM_NAME)?")":"");
			$arr_data[] = $POSITION_TYPE;
			$arr_data[] = $POSITION_LEVEL;
			$arr_data[] = $CL_NAME;
			$arr_data[] = $POS_NO;
			$arr_data[] = $ORG_NAME;
			$arr_data[] = $PER_SALARY;
			$arr_data[] = $PER_STARTDATE;
			$arr_data[] = $LEVEL_EFFECTIVEDATE;
			$arr_data[] = $DC_NAME;
			$arr_data[] = $PER_RETIREDATE;
			 
			$data_align = array("C", "L", "C", "C", "L", "L", "L", "C", "C", "L", "R", "C", "C", "C", "C");
			
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
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