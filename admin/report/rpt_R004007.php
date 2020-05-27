<?php
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
//	echo "have_pic=$have_pic<br>";

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time",0);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("POS_NO", "NAME", "LINE", "LEVEL", "ORG", "ORG_1", "ORG_2"); 
	$order_by = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POS_NO" :
				if($order_by) $order_by .= ", ";
				if ($BKK_FLAG==1) {
					if($DPISDB=="odbc") $order_by .= "k.ORG_SEQ_NO, k.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, e.ORG_CODE, 
																							g.ORG_SEQ_NO, g.ORG_CODE, j.ORG_SEQ_NO, j.ORG_CODE, b.POS_NO_NAME, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO)), 
																							c.POEM_NO_NAME, IIf(IsNull(c.POEM_NO), 0, CLng(c.POEM_NO)), f.POEMS_NO_NAME, IIf(IsNull(f.POEMS_NO), 0, CLng(f.POEMS_NO)), 
																							i.POT_NO_NAME, IIf(IsNull(i.POT_NO), 0, CLng(i.POT_NO))";
					elseif($DPISDB=="oci8") $order_by .= "k.ORG_SEQ_NO, k.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, e.ORG_CODE, g.ORG_SEQ_NO, g.ORG_CODE, 
																								j.ORG_SEQ_NO, j.ORG_CODE, b.POS_NO_NAME, TO_NUMBER(replace(b.POS_NO,'-','')), c.POEM_NO_NAME, TO_NUMBER(replace(c.POEM_NO,'-','')), 
																								f.POEMS_NO_NAME, TO_NUMBER(replace(f.POEMS_NO,'-','')), i.POT_NO_NAME, TO_NUMBER(replace(i.POT_NO,'-',''))";
					elseif($DPISDB=="mysql") $order_by .= "k.ORG_SEQ_NO, k.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, e.ORG_CODE, g.ORG_SEQ_NO, g.ORG_CODE, 
																									j.ORG_SEQ_NO, j.ORG_CODE, b.POS_NO_NAME, b.POS_NO+0, c.POEM_NO_NAME, c.POEM_NO+0, 
																									f.POEMS_NO_NAME, f.POEMS_NO+0, i.POT_NO_NAME, i.POT_NO+0";
				} else {
					if($DPISDB=="odbc") $order_by .= "k.ORG_SEQ_NO, k.ORG_CODE, b.POS_NO_NAME, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO)), 
																							c.POEM_NO_NAME, IIf(IsNull(c.POEM_NO), 0, CLng(c.POEM_NO)), 
																							f.POEMS_NO_NAME, IIf(IsNull(f.POEMS_NO), 0, CLng(f.POEMS_NO)), 
																							i.POT_NO_NAME, IIf(IsNull(i.POT_NO), 0, CLng(i.POT_NO))";
					elseif($DPISDB=="oci8") $order_by .= "k.ORG_SEQ_NO, k.ORG_CODE, b.POS_NO_NAME, TO_NUMBER(replace(b.POS_NO,'-','')), c.POEM_NO_NAME, TO_NUMBER(replace(c.POEM_NO,'-','')), 
																								f.POEMS_NO_NAME, TO_NUMBER(replace(f.POEMS_NO,'-','')), i.POT_NO_NAME, TO_NUMBER(replace(i.POT_NO,'-',''))";
					elseif($DPISDB=="mysql") $order_by .= "k.ORG_SEQ_NO, k.ORG_CODE, b.POS_NO_NAME, b.POS_NO+0, c.POEM_NO_NAME, c.POEM_NO+0, 
																									f.POEMS_NO_NAME, f.POEMS_NO+0, i.POT_NO_NAME, i.POT_NO+0";
				}
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
                                /*
                                    if($select_org_structure==0) $order_by .= "k.ORG_SEQ_NO, k.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, b.ORG_ID, e.ORG_SEQ_NO, e.ORG_CODE, c.ORG_ID, 
                                            g.ORG_SEQ_NO, g.ORG_CODE, f.ORG_ID, j.ORG_SEQ_NO, j.ORG_CODE, i.ORG_ID, b.POS_NO_NAME, TO_NUMBER(replace(b.POS_NO,'-','')), c.POEM_NO_NAME, 
                                            TO_NUMBER(replace(c.POEM_NO,'-','')), f.POEMS_NO_NAME, TO_NUMBER(replace(f.POEMS_NO,'-','')), i.POT_NO_NAME, TO_NUMBER(replace(i.POT_NO,'-',''))";
                                    else if($select_org_structure==1) $order_by .= "k.ORG_SEQ_NO, k.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID, e.ORG_SEQ_NO, e.ORG_CODE, c.ORG_ID, 
                                            g.ORG_SEQ_NO, g.ORG_CODE, f.ORG_ID, j.ORG_SEQ_NO, j.ORG_CODE, i.ORG_ID, b.POS_NO_NAME, TO_NUMBER(replace(b.POS_NO,'-','')), c.POEM_NO_NAME, 
                                            TO_NUMBER(replace(c.POEM_NO,'-','')), f.POEMS_NO_NAME, TO_NUMBER(replace(f.POEMS_NO,'-','')), i.POT_NO_NAME, TO_NUMBER(replace(i.POT_NO,'-',''))";
                                */
                                   if($select_org_structure==0)     $order_by .= "k.ORG_ACTIVE DESC,k.ORG_SEQ_NO, k.ORG_CODE, 
                                        d.ORG_ACTIVE DESC,d.ORG_SEQ_NO, d.ORG_CODE,
                                        d1.ORG_ACTIVE DESC ,d1.ORG_SEQ_NO, d1.ORG_CODE,
                                        d2.ORG_ACTIVE DESC ,d2.ORG_SEQ_NO, d2.ORG_CODE, 
                                        b.ORG_ID, e.ORG_SEQ_NO, e.ORG_CODE, c.ORG_ID, 
                                        g.ORG_SEQ_NO, g.ORG_CODE, f.ORG_ID, j.ORG_SEQ_NO, j.ORG_CODE, i.ORG_ID, b.POS_NO_NAME, to_number(replace(b.POS_NO,'-','')),c.POEM_NO_NAME, 
                                        to_number(replace(c.POEM_NO,'-','')), f.POEMS_NO_NAME, to_number(replace(f.POEMS_NO,'-','')), i.POT_NO_NAME, to_number(replace(i.POT_NO,'-',''))";

                                    else if($select_org_structure==1) $order_by .= "k.ORG_ACTIVE DESC,k.ORG_SEQ_NO, k.ORG_CODE, 
                                        d.ORG_ACTIVE DESC,d.ORG_SEQ_NO, d.ORG_CODE,
                                        d1.ORG_ACTIVE DESC ,d1.ORG_SEQ_NO, d1.ORG_CODE,
                                        d2.ORG_ACTIVE DESC ,d2.ORG_SEQ_NO, d2.ORG_CODE, 
                                        a.ORG_ID, e.ORG_SEQ_NO, e.ORG_CODE, c.ORG_ID, 
                                        g.ORG_SEQ_NO, g.ORG_CODE, f.ORG_ID, j.ORG_SEQ_NO, j.ORG_CODE, i.ORG_ID, b.POS_NO_NAME, to_number(replace(b.POS_NO,'-','')), c.POEM_NO_NAME, 
                                        to_number(replace(c.POEM_NO,'-','')), f.POEMS_NO_NAME, to_number(replace(f.POEMS_NO,'-','')), i.POT_NO_NAME, to_number(replace(i.POT_NO,'-',''))";
                                
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
        /*เพิ่มเงื่อนไข ค้นหาด้วยสถานะพ้นสภาพนะจ๊ะ...*/
        if(!empty($search_per_status[0])){
            $valStatus = 2;
        }else{
            $valStatus = 1;
        }
        /**/
        
	$arr_search_condition[] = "(a.PER_TYPE in (".$search_per_type.") and (a.PER_STATUS = $valStatus) and (b.POS_ID >= 0 or c.POEM_ID >= 0 or f.POEMS_ID >=0 or i.POT_ID >=0) )";
//	$arr_search_condition[] = "(a.PER_TYPE=1)";	
	
	$list_type_text = $ALL_REPORT_TITLE;
/*	if($select_org_structure==0){ $list_type_text .= " โครงสร้างตามกฏหมาย"; 	}
	elseif($select_org_structure==1){ $list_type_text .= " โครงสร้างตามมอบหมายงาน"; }  */		
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(d.OT_CODE='01' or e.OT_CODE='01' or g.OT_CODE='01' or  j.OT_CODE='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
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
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	/*if(in_array(1, $search_per_type) && in_array(2, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการและลูกจ้างประจำ ตำแหน่ง สังกัด";
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
	elseif(in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างชั่วคราว  ตำแหน่ง สังกัด";*/
        if($search_per_type==1) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการ ตำแหน่ง สังกัด";
	elseif($search_per_type==2) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างประจำ ตำแหน่ง สังกัด";
	elseif($search_per_type==3) $report_title = "$DEPARTMENT_NAME||รายชื่อพนักงานราชการ ตำแหน่ง สังกัด";
	elseif($search_per_type==4) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างชั่วคราว  ตำแหน่ง สังกัด";
	$report_code = "R0407";
	include ("rpt_R004007_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R004007_rtf.rtf";

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
		if ($ITA_FLAG==1) 
			$paper_size="A4";
		else
			$paper_size="A3";
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
        $con_ita_flag="";
        if($ITA_FLAG==1){
            
            if(empty($search_ita_flag_date)){
                $search_ita_flag_date=date('Y-m-d');
            }else{
                $arr_ita_flag_date= explode('/', $search_ita_flag_date);
                $search_ita_flag_date=($arr_ita_flag_date[2]-543).'-'.$arr_ita_flag_date[1].'-'.$arr_ita_flag_date[0];
                
            }
            /*  เดิม http://dpis.ocsc.go.th/Service/node/2530
                $con_ita_flag=" AND trunc(months_between(to_date('".$search_ita_flag_date."','YYYY-MM-DD'),
                to_date(substr(PER_STARTDATE,9,2)||substr(PER_STARTDATE,6,2)||substr(PER_STARTDATE,1,4),'DDMMYYYY'))/12) >=1 ";
            */
            
            //ปรับแก้ http://dpis.ocsc.go.th/Service/node/2530
            $con_ita_flag=" AND trunc(months_between(to_date('".$search_ita_flag_date."','YYYY-MM-DD'),
            to_date(substr(PER_OCCUPYDATE,9,2)||substr(PER_OCCUPYDATE,6,2)||substr(PER_OCCUPYDATE,1,4),'DDMMYYYY'))/12) >=1 ";
        }
	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, a.POS_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, h.POSITION_TYPE, h.POSITION_LEVEL, 
										a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, b.PL_CODE, b.PM_CODE, b.PT_CODE, b.POS_NO_NAME, b.POS_NO, b.ORG_ID, b.ORG_ID_1, 
										b.ORG_ID_2, b.CL_NAME, d.ORG_NAME, a.PER_CARDNO,  a.PER_BIRTHDATE,  a.PER_RETIREDATE,	c.PN_CODE as EMP_PL_CODE, 
										c.POEM_NO_NAME as EMP_POS_NO_NAME, c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID, c.ORG_ID_1 as EMP_ORG_ID_1, 
										c.ORG_ID_2 as EMP_ORG_ID_2, e.ORG_NAME as EMP_ORG_NAME, f.EP_CODE as EMPSER_PL_CODE, f.POEMS_NO_NAME as EMPSER_POS_NO_NAME, 
										f.POEMS_NO as EMPSER_POS_NO, f.ORG_ID as EMPSER_ORG_ID, f.ORG_ID_1 as EMPSER_ORG_ID_1, f.ORG_ID_2 as EMPSER_ORG_ID_2, 
										g.ORG_NAME as EMPSER_ORG_NAME,	i.TP_CODE as POT_PL_CODE, i.POT_NO_NAME as POT_POS_NO_NAME, i.POT_NO as POT_POS_NO, 
										i.ORG_ID as POT_ORG_ID, i.ORG_ID_1 as POT_ORG_ID_1, i.ORG_ID_2 as POT_ORG_ID_2, j.ORG_NAME as POT_ORG_NAME, d.PV_CODE, 
										a.ORG_ID as ORG_ID_ASS, PER_ADD1, PER_ADD2, PER_HOME_TEL, PER_OFFICE_TEL, PER_MOBILE, PER_EMAIL										
						 from	(
										(
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
									) left join PER_ORG k on (a.DEPARTMENT_ID=k.ORG_ID)
										$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
                /* เพิ่มเติ่มสำหรับเงื่อนไข เกษียณอายุ*/
                $joinTB='';
                $onFld='';
                $sqlBegin ='';
                $sqlEnd ='';
                $condition ='';
                if(!empty($search_mov_code[0])){
                    $joinTB=',PER_POSITIONHIS l,PER_MOVMENT m';
                    $onFld='and a.PER_ID=l.PER_ID(+) and l.MOV_CODE=m.MOV_CODE(+)';
                    $sqlBegin ='select distinct * from (';
                    $sqlEnd =')';
                    $condition =' and trim(m.MOV_SUB_TYPE) =92 ';
                }
                /**/
                
		$cmd = " select		a.PER_ID, a.POS_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME,l.LEVEL_NAME as LEVEL_POTION, h.POSITION_TYPE, h.POSITION_LEVEL, 
										a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, b.PL_CODE, b.PM_CODE, b.PT_CODE, b.POS_NO_NAME, b.POS_NO, b.ORG_ID, b.ORG_ID_1, 
										b.ORG_ID_2, b.CL_NAME, d.ORG_NAME, a.PER_CARDNO,  a.PER_BIRTHDATE,  a.PER_RETIREDATE, c.PN_CODE as EMP_PL_CODE, 
										c.POEM_NO_NAME as EMP_POS_NO_NAME, c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID, c.ORG_ID_1 as EMP_ORG_ID_1, 
										c.ORG_ID_2 as EMP_ORG_ID_2, e.ORG_NAME as EMP_ORG_NAME, f.EP_CODE as EMPSER_PL_CODE, f.POEMS_NO_NAME as EMPSER_POS_NO_NAME, 
										f.POEMS_NO as EMPSER_POS_NO, f.ORG_ID as EMPSER_ORG_ID, f.ORG_ID_1 as EMPSER_ORG_ID_1, f.ORG_ID_2 as EMPSER_ORG_ID_2, 
										g.ORG_NAME as EMPSER_ORG_NAME, i.TP_CODE as POT_PL_CODE, i.POT_NO_NAME as POT_POS_NO_NAME, i.POT_NO as POT_POS_NO, 
										i.ORG_ID as POT_ORG_ID, i.ORG_ID_1 as POT_ORG_ID_1, i.ORG_ID_2 as POT_ORG_ID_2, j.ORG_NAME as POT_ORG_NAME, d.PV_CODE, 
										a.ORG_ID as ORG_ID_ASS, PER_ADD1, PER_ADD2, PER_HOME_TEL, PER_OFFICE_TEL, PER_MOBILE, PER_EMAIL, PER_CERT_OCC										
						 from PER_PERSONAL a, 
                                                    PER_POSITION b, 
                                                    PER_POS_EMP c, 
                                                    PER_ORG d, 
                                                    PER_ORG d1 /*modify*/,
                                                    PER_ORG d2 /*modify*/,
                                                    PER_ORG e, 
                                                    PER_POS_EMPSER f, 
                                                    PER_ORG g, 
                                                    PER_LEVEL h, 
                                                    PER_POS_TEMP i, 
                                                    PER_ORG j, 
                                                    PER_ORG k,
                                                    PER_LEVEL l,
                                                    PER_EMPSER_POS_NAME m
                                                    
                                                                                $joinTB 
						 where	a.POS_ID=b.POS_ID(+) 
                                                    and b.ORG_ID=d.ORG_ID(+) 
                                                    and b.ORG_ID_1=d1.ORG_ID(+) /*modify*/
                                                    and b.ORG_ID_2=d2.ORG_ID(+) /*modify*/ 
                                                    and a.POEM_ID=c.POEM_ID(+) 
                                                    and c.ORG_ID=e.ORG_ID(+) 
                                                    and a.POEMS_ID=f.POEMS_ID(+) 
                                                    and f.ORG_ID=g.ORG_ID(+) 
                                                    and a.POT_ID=i.POT_ID(+) 
                                                    and i.ORG_ID=j.ORG_ID(+)	
                                                    and a.DEPARTMENT_ID=k.ORG_ID(+) 
                                                    and a.LEVEL_NO=h.LEVEL_NO(+) 
                                                    and m.LEVEL_NO=l.LEVEL_NO(+)
                                                    and f.EP_CODE=m.EP_CODE(+)
                                                                                $onFld 
										$search_condition $condition $con_ita_flag
						 order by		$order_by ";
                $cmd = $sqlBegin.$cmd.$sqlEnd;
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, a.POS_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, h.POSITION_TYPE, h.POSITION_LEVEL, 
										a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, b.PL_CODE, b.PM_CODE, b.PT_CODE, b.POS_NO_NAME, b.POS_NO, b.ORG_ID, b.ORG_ID_1, 
										b.ORG_ID_2, b.CL_NAME, d.ORG_NAME, a.PER_CARDNO,  a.PER_BIRTHDATE,  a.PER_RETIREDATE,	c.PN_CODE as EMP_PL_CODE, 
										c.POEM_NO_NAME as EMP_POS_NO_NAME, c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID, c.ORG_ID_1 as EMP_ORG_ID_1, 
										c.ORG_ID_2 as EMP_ORG_ID_2, e.ORG_NAME as EMP_ORG_NAME, f.EP_CODE as EMPSER_PL_CODE, f.POEMS_NO_NAME as EMPSER_POS_NO_NAME, 
										f.POEMS_NO as EMPSER_POS_NO, f.ORG_ID as EMPSER_ORG_ID, f.ORG_ID_1 as EMPSER_ORG_ID_1, f.ORG_ID_2 as EMPSER_ORG_ID_2, 
										g.ORG_NAME as EMPSER_ORG_NAME, 	i.TP_CODE as POT_PL_CODE, i.POT_NO_NAME as POT_POS_NO_NAME, i.POT_NO as POT_POS_NO, 
										i.ORG_ID as POT_ORG_ID, i.ORG_ID_1 as POT_ORG_ID_1, i.ORG_ID_2 as POT_ORG_ID_2, j.ORG_NAME as POT_ORG_NAME, d.PV_CODE, 
										a.ORG_ID as ORG_ID_ASS, PER_ADD1, PER_ADD2, PER_HOME_TEL, PER_OFFICE_TEL, PER_MOBILE, PER_EMAIL										
						 from	(
										(
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
									) left join PER_ORG k on (a.DEPARTMENT_ID=k.ORG_ID)
										$search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("f.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("i.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
   //echo "<pre>".$cmd;
//$db_dpis->show_error();

	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
	if ($FLAG_RTF) {
		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
			
	//	echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	} else {
		$pdf->AutoPageBreak = false; 
	//	echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
	}
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$POS_ID = $data[POS_ID];
			$PER_TYPE = $data[PER_TYPE];
			$LEVEL_NO = trim($data[LEVEL_NO]);
                        $POH_LEVEL_NO = trim($data[POH_LEVEL_NO]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]);
			$CL_NAME = trim($data[CL_NAME]);
			$POSITION_TYPE = trim($data[POSITION_TYPE]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
			$LEVEL_POTION = trim($data[LEVEL_POTION]);
			/* http://dpis.ocsc.go.th/Service/node/2161 */
			/*แก้เรื่อง พนักงานราชการไม่ควรมีระดับตำแหน่งต่อท้ายตำแหน่ง */
			if($PER_TYPE==3){
				//$POSITION_TYPE = $POSITION_LEVEL;
				$POSITION_TYPE = $LEVEL_POTION;
				$POSITION_LEVEL = "";
			}
                        
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
			$PER_HOME_TEL = trim($data[PER_HOME_TEL]);
			$PER_OFFICE_TEL = trim($data[PER_OFFICE_TEL]);
			$PER_MOBILE = trim($data[PER_MOBILE]);
			$PER_EMAIL = trim($data[PER_EMAIL]);
			$PER_TEL = "";
			if ($PER_OFFICE_TEL) $PER_TEL = $PER_OFFICE_TEL;
			if ($PER_HOME_TEL) $PER_TEL .= "\n" . $PER_HOME_TEL;
			if ($PER_MOBILE) $PER_TEL .= "\n" . $PER_MOBILE;

			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_1 = $data2[ORG_NAME];
			}
			
			$TMP_PER_BIRTHDATE = substr($data[PER_BIRTHDATE], 0, 10);
		    $TMP_AGE = date_difference(date("Y-m-d"), $TMP_PER_BIRTHDATE, "full");
			
			$SALARY_MIDPOINT = "";
			$cmd = " select LAYER_TYPE, a.POS_NO, a.PL_CODE from PER_POSITION a, PER_LINE b where POS_ID = $POS_ID and a.PL_CODE = b.PL_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LAYER_TYPE = $data2[LAYER_TYPE] + 0;

			$cmd = " select 	LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
							LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2, LAYER_SALARY_FULL
							from	PER_LAYER 
							where LAYER_TYPE=0 and LEVEL_NO='$LEVEL_NO' and LAYER_NO=0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
                        /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/
                        //if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $data[PER_SALARY] <= $LAYER_SALARY_MAX) {
			if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $data[PER_SALARY] < $data[LAYER_SALARY_FULL]) {
				$LAYER_SALARY_MIDPOINT = $data2[LAYER_EXTRA_MIDPOINT] + 0;
				$LAYER_SALARY_MIDPOINT1 = $data2[LAYER_EXTRA_MIDPOINT1] + 0;
				$LAYER_SALARY_MIDPOINT2 = $data2[LAYER_EXTRA_MIDPOINT2] + 0;
			} else {
				$LAYER_SALARY_MIDPOINT = $data2[LAYER_SALARY_MIDPOINT] + 0;
				$LAYER_SALARY_MIDPOINT1 = $data2[LAYER_SALARY_MIDPOINT1] + 0;
				$LAYER_SALARY_MIDPOINT2 = $data2[LAYER_SALARY_MIDPOINT2] + 0;
			}
			if ($data[PER_SALARY] >= $LAYER_SALARY_MIDPOINT) $SALARY_MIDPOINT = number_format($LAYER_SALARY_MIDPOINT2);
			else $SALARY_MIDPOINT = number_format($LAYER_SALARY_MIDPOINT1);
			
			$ORG_ID_ASS = $data[ORG_ID_ASS];
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG_ASS where ORG_ID=$ORG_ID_ASS ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_ASS = $data2[ORG_NAME];
			
			if($PER_TYPE==1){
				$POS_NO = $data[POS_NO_NAME].' '.$data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PM_CODE = trim($data[PM_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];
				$ORG_NAME = $data[ORG_NAME];
				$PV_CODE = $data[PV_CODE];

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
				if ($PL_NAME==$PM_NAME) $PM_NAME = "";
				if ($PM_NAME=="ผู้ว่าราชการจังหวัด" || $PM_NAME=="รองผู้ว่าราชการจังหวัด" || $PM_NAME=="ปลัดจังหวัด" || $PM_NAME=="หัวหน้าสำนักงานจังหวัด") {
					$PM_NAME .= $ORG_NAME;
					$PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $PM_NAME); 
					$PM_NAME = str_replace("สำนักงานจังหวัดสำนักงานจังหวัด", "สำนักงานจังหวัด", $PM_NAME); 
				} elseif ($PM_NAME=="นายอำเภอ") {
					$PM_NAME .= $ORG_NAME_1;
					$PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $PM_NAME); 
				}

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_NO = $data[EMP_POS_NO_NAME].$data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];
				$ORG_NAME = $data[EMP_ORG_NAME];

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
                                $PM_NAME = "";
				$cmd = " select POSITION_LEVEL from PER_POS_LEVEL_SALARY a, PER_LEVEL b 
								where a.LEVEL_NO = b.LEVEL_NO and trim(PN_CODE) = '$PL_CODE' order by a.LEVEL_NO ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CL_NAME = trim($data2[POSITION_LEVEL]);

				$cmd = " select POSITION_LEVEL from PER_POS_LEVEL_SALARY a, PER_LEVEL b 
								where a.LEVEL_NO = b.LEVEL_NO and trim(PN_CODE) = '$PL_CODE' order by a.LEVEL_NO desc ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				if ($CL_NAME) $CL_NAME .= " - ".trim($data2[POSITION_LEVEL]);
			}elseif($PER_TYPE==3){
				$POS_NO = $data[EMPSER_POS_NO_NAME].$data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];
				$ORG_NAME = $data[EMPSER_ORG_NAME];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
                                $PM_NAME = "";
			} elseif($PER_TYPE==4){
				$POS_NO = $data[POT_POS_NO_NAME].$data[POT_POS_NO];
				$PL_CODE = trim($data[POT_PL_CODE]);
				$ORG_ID = $data[POT_ORG_ID];
				$ORG_ID_1 = $data[POT_ORG_ID_1];
				$ORG_ID_2 = $data[POT_ORG_ID_2];
				$ORG_NAME = $data[POT_ORG_NAME];
				
				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
                                $PM_NAME = "";
			} 
			$PL_NAME = trim($PL_NAME);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = '$ORG_ID_1' ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = '$ORG_ID_2' ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);

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
			
			$TMP_PER_STARTDATE = substr($data[PER_STARTDATE], 0, 10);
                        $TMP_OFFICER_TIME = date_difference(date("Y-m-d"), $TMP_PER_STARTDATE, "full");
                        
                    
			//$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='$POH_LEVEL_NO' ";/*เดิม*/
                        
                        
                        /*Release 5.2.1.16
                         * http://dpis.ocsc.go.th/Service/node/1700
                         */
                        $cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE 
                            from PER_POSITIONHIS where PER_ID=$PER_ID and trim(POH_LEVEL_NO)='$LEVEL_NO'
							 and trim(mov_code) not IN(SELECT trim(mov_code) FROM per_movment WHERE mov_code IN('110','11010','11020')) "; /* http://dpis.ocsc.go.th/Service/node/1865ไม่หยิบประเภทความเคลื่ีอนยไวที่่เป้น รักษาราชการแทน*/
                           /* and trim(mov_code) not IN(SELECT trim(mov_code) FROM per_movment WHERE mov_sub_type IN(7)) ";  http://dpis.ocsc.go.th/Service/node/1700 ไม่หยิบประเภทความเคลื่ีอนยไวที่่เป้น รักษาราชการแทน*/
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);
           // echo $cmd;
/*
			$POS_CHANGE_DATE = show_date_format($data[POS_CHANGE_DATE], $DATE_DISPLAY);
*/
			
			if(trim($search_salary_date)){
				$cmd = " select SAH_SALARY, SAH_POS_NO, SAH_PAY_NO from PER_SALARYHIS 
								  where PER_ID=$PER_ID and SAH_EFFECTIVEDATE <= '$search_salary_date' 
								 order by SAH_SALARY desc, SAH_EFFECTIVEDATE desc ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_SALARY = $data2[SAH_SALARY];
//				$POS_NO = $data2[SAH_POS_NO];
				$PAY_NO = $data2[SAH_PAY_NO];

				$cmd = " select ORG_NAME, PV_CODE from PER_POSITION a, PER_ORG b where a.ORG_ID = b.ORG_ID and POS_NO = '$PAY_NO' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_ORG_NAME = trim($data2[ORG_NAME]);
				$PAY_PV_CODE = trim($data2[PV_CODE]);
			}
                        $order_by_deco = "ASC";
                        if($show_first_decorate){
                            $order_by_deco = "DESC";
                        }
			/*เพิ่ม ปีที่ได้รับเครื่องราช ,A.DEH_DATE*/
                        $cmd = " select 	a.DC_CODE, b.DC_SHORTNAME, b.DC_NAME,a.DEH_DATE
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.DC_CODE=b.DC_CODE and a.PER_ID=$PER_ID and b.DC_TYPE in (1,2)
							 order by b.DC_ORDER $order_by_deco";
                        //die('<pre>'.$cmd);
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DC_CODE = trim($data2[DC_CODE]);
                        
                        /**/
                        $DEH_DATE="";
                        if($have_year==1){
                            $DEH_DATEArr= explode("-", trim($data2[DEH_DATE]) );
                            $DEH_DATE="(".($DEH_DATEArr[0]+543).")";
                        }
                        /**/
                        
			$DC_NAME = "";
			if($DC_CODE) $DC_NAME = (trim($data2[DC_SHORTNAME])?$data2[DC_SHORTNAME]:$data2[DC_NAME]).$DEH_DATE;
			$PER_CARDNO = card_no_format($PER_CARDNO, $CARD_NO_DISPLAY);
                        
			//====================================================== การศึกษา =====================================================
			$EL_NAME1 = $EN_NAME1 = $EM_NAME1 =  $INS_NAME1 = $EL_NAME2 = $EN_NAME2 = $EM_NAME2 =  $INS_NAME2 = 
			$EL_NAME4 = $EN_NAME4 = $EM_NAME4 =  $INS_NAME4 = "";
			if($DPISDB=="odbc") {
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'	"	;
				$db_dpis2->send_cmd($cmd);
		//	$db_dpis2->show_error();
		//		echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME2 = trim($data4[EN_NAME]);
				$INS_NAME2 = trim($data4[INS_NAME]);
				$EL_NAME2 = trim($data4[EL_NAME]);
				$EM_NAME2 = trim($data4[EM_NAME]);
				if (!$INS_NAME2) $INS_NAME2 = trim($data4[EDU_INSTITUTE]);
				
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'	";
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME1 = trim($data4[EN_NAME]);
				$INS_NAME1 = trim($data4[INS_NAME]);
				$EL_NAME1 = trim($data4[EL_NAME]);		
				$EM_NAME1 = trim($data4[EM_NAME]);
				if (!$INS_NAME1) $INS_NAME1 = trim($data4[EDU_INSTITUTE]);
				
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%'	";
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME4 = trim($data4[EN_NAME]);
				$INS_NAME4 = trim($data4[INS_NAME]);
				$EL_NAME4 = trim($data4[EL_NAME]);		
				$EM_NAME4 = trim($data4[EM_NAME]);
				if (!$INS_NAME4) $INS_NAME4 = trim($data4[EDU_INSTITUTE]);
			}elseif($DPISDB=="oci8"){
				$cmd = "select   b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
										from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR e
										where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%2%' and a.en_code=b.en_code(+) and 
											a.ins_code=d.ins_code(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=e.EM_CODE(+)";
										
				$db_dpis2->send_cmd($cmd);
		//		$db_dpis2->show_error();
		//		echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME2 = trim($data4[EN_NAME]);
				$INS_NAME2 = trim($data4[INS_NAME]);
				$EL_NAME2 = trim($data4[EL_NAME]);
				$EM_NAME2 = trim($data4[EM_NAME]);
				if (!$INS_NAME2) $INS_NAME2 = trim($data4[EDU_INSTITUTE]);
				
				$cmd = "select   b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
										from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR e
										where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%1%' and a.en_code=b.en_code(+) and 
											a.ins_code=d.ins_code(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=e.EM_CODE(+)";
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME1 = trim($data4[EN_NAME]);
				$INS_NAME1 = trim($data4[INS_NAME]);
				$EL_NAME1 = trim($data4[EL_NAME]);		
				$EM_NAME1 = trim($data4[EM_NAME]);
				if (!$INS_NAME1) $INS_NAME1 = trim($data4[EDU_INSTITUTE]);
			
				$cmd = "select   b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
										from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR e
										where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%4%' and a.en_code=b.en_code(+) and 
											a.ins_code=d.ins_code(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=e.EM_CODE(+)";
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME4 = trim($data4[EN_NAME]);
				$INS_NAME4 = trim($data4[INS_NAME]);
				$EL_NAME4 = trim($data4[EL_NAME]);		
				$EM_NAME4 = trim($data4[EM_NAME]);
				if (!$INS_NAME4) $INS_NAME4 = trim($data4[EDU_INSTITUTE]);
			}elseif($DPISDB=="mysql"){
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'	"	;
										
				$db_dpis2->send_cmd($cmd);
		//		$db_dpis2->show_error();
		//		echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME2 = trim($data4[EN_NAME]);
				$INS_NAME2 = trim($data4[INS_NAME]);
				$EL_NAME2 = trim($data4[EL_NAME]);
				$EM_NAME2 = trim($data4[EM_NAME]);
				if (!$INS_NAME2) $INS_NAME2 = trim($data4[EDU_INSTITUTE]);
				
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%2%'	"	;
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME1 = trim($data4[EN_NAME]);
				$INS_NAME1 = trim($data4[INS_NAME]);
				$EL_NAME1 = trim($data4[EL_NAME]);		
				$EM_NAME1 = trim($data4[EM_NAME]);
				if (!$INS_NAME1) $INS_NAME1 = trim($data4[EDU_INSTITUTE]);
				
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%4%'	"	;
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME4 = trim($data4[EN_NAME]);
				$INS_NAME4 = trim($data4[INS_NAME]);
				$EL_NAME4 = trim($data4[EL_NAME]);		
				$EM_NAME4 = trim($data4[EM_NAME]);		 
				if (!$INS_NAME4) $INS_NAME4 = trim($data4[EDU_INSTITUTE]);
			}

			if ($ITA_FLAG==1) {
				$cmd = " select a.*, b.AP_NAME, c.PV_NAME 
								from PER_ADDRESS a left join PER_AMPHUR b on (a.AP_CODE=b.AP_CODE) left join PER_PROVINCE c on (a.PV_CODE=c.PV_CODE) 
								where PER_ID = $PER_ID and ADR_TYPE=1 ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();

				$PER_ADD = $PER_ADD1 = $DT_CODE_ADR = $AP_CODE_ADR = $PV_CODE_ADR = "";
				$DT_CODE_ADR = trim($data2[DT_CODE]);
				$cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$DT_NAME_ADR = trim($data3[DT_NAME]);
				if (!$DT_NAME_ADR) $DT_NAME_ADR = $data2[ADR_DISTRICT];
				
				$AP_CODE_ADR = trim($data2[AP_CODE]);
				$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$AP_NAME_ADR = trim($data3[AP_NAME]);
				
				$PV_CODE_ADR = trim($data2[PV_CODE]);
				$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PV_NAME_ADR = trim($data3[PV_NAME]);
					
				if($data2[ADR_VILLAGE]) $PER_ADD1 .= "หมู่บ้าน".$data2[ADR_VILLAGE]." ";
				if($data2[ADR_BUILDING]) $PER_ADD1 .= "อาคาร".$data2[ADR_BUILDING]." ";
				if($data2[ADR_NO]) $PER_ADD1 .= "เลขที่ ".$data2[ADR_NO]." ";
				if($data2[ADR_MOO]) $PER_ADD1 .= "ม.".$data2[ADR_MOO]." ";
				if($data2[ADR_SOI]) $PER_ADD1 .= "ซ.".str_replace("ซ.","",str_replace("ซอย","",$data2[ADR_SOI]))." ";
				if($data2[ADR_ROAD]) $PER_ADD1 .= "ถ.".str_replace("ถ.","",str_replace("ถนน","",$data2[ADR_ROAD]))." ";
				if($DT_NAME_ADR) {
					if ($PV_CODE_ADR=="1000") {
						$PER_ADD1 .= "แขวง".$DT_NAME_ADR." ";
					} else {
						$PER_ADD1 .= "ต.".$DT_NAME_ADR." ";
					}
				}
				if($AP_NAME_ADR) {
					if ($PV_CODE_ADR=="1000") {
						$PER_ADD1 .= "เขต".$AP_NAME_ADR." ";
					} else {
						$PER_ADD1 .= "อ.".$AP_NAME_ADR." ";
					}
				}
				if($PV_NAME_ADR) {
					if ($PV_CODE_ADR=="1000") {
						$PER_ADD1 .= $PV_NAME_ADR." ";
					} else {
						$PER_ADD1 .= "จ.".$PV_NAME_ADR." ";
					}
				}
				if($data2[ADR_POSTCODE]) $PER_ADD1 .= $data2[ADR_POSTCODE]." ";
				if (!$PER_ADD1) $PER_ADD1 = trim($data[PER_ADD1]);

				$cmd = " select a.*, b.AP_NAME, c.PV_NAME 
								from PER_ADDRESS a left join PER_AMPHUR b on (a.AP_CODE=b.AP_CODE) left join PER_PROVINCE c on (a.PV_CODE=c.PV_CODE) 
								where PER_ID = $PER_ID and ADR_TYPE=2 ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();

				$PER_ADD2 = $DT_CODE_ADR = $AP_CODE_ADR = $PV_CODE_ADR = "";
				$DT_CODE_ADR = trim($data2[DT_CODE]);
				$cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$DT_NAME_ADR = trim($data3[DT_NAME]);
				if (!$DT_NAME_ADR) $DT_NAME_ADR = $data2[ADR_DISTRICT];
				
				$AP_CODE_ADR = trim($data2[AP_CODE]);
				$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$AP_NAME_ADR = trim($data3[AP_NAME]);
				
				$PV_CODE_ADR = trim($data2[PV_CODE]);
				$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PV_NAME_ADR = trim($data3[PV_NAME]);
					
				if($data2[ADR_VILLAGE]) $PER_ADD2 .= "หมู่บ้าน".$data2[ADR_VILLAGE]." ";
				if($data2[ADR_BUILDING]) $PER_ADD2 .= "อาคาร".$data2[ADR_BUILDING]." ";
				if($data2[ADR_NO]) $PER_ADD2 .= "เลขที่ ".$data2[ADR_NO]." ";
				if($data2[ADR_MOO]) $PER_ADD2 .= "ม.".$data2[ADR_MOO]." ";
				if($data2[ADR_SOI]) $PER_ADD2 .= "ซ.".str_replace("ซ.","",str_replace("ซอย","",$data2[ADR_SOI]))." ";
				if($data2[ADR_ROAD]) $PER_ADD2 .= "ถ.".str_replace("ถ.","",str_replace("ถนน","",$data2[ADR_ROAD]))." ";
				if($DT_NAME_ADR) {
					if ($PV_CODE_ADR=="1000") {
						$PER_ADD2 .= "แขวง".$DT_NAME_ADR." ";
					} else {
						$PER_ADD2 .= "ต.".$DT_NAME_ADR." ";
					}
				}
				if($AP_NAME_ADR) {
					if ($PV_CODE_ADR=="1000") {
						$PER_ADD2 .= "เขต".$AP_NAME_ADR." ";
					} else {
						$PER_ADD2 .= "อ.".$AP_NAME_ADR." ";
					}
				}
				if($PV_NAME_ADR) {
					if ($PV_CODE_ADR=="1000") {
						$PER_ADD2 .= $PV_NAME_ADR." ";
					} else {
						$PER_ADD2 .= "จ.".$PV_NAME_ADR." ";
					}
				}
				if($data2[ADR_POSTCODE]) $PER_ADD2 .= $data2[ADR_POSTCODE]." ";
				if (!$PER_ADD2) $PER_ADD2 = trim($data[PER_ADD2]);
				if ($PER_ADD1) $PER_ADD = $PER_ADD1;
				if ($PER_ADD2) $PER_ADD .= "\n" . $PER_ADD2;
			}
			
			$img_file = "";
			if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4

			$arr_data = (array) null;
			$arr_data[] = $data_count;
			if ($have_pic && $img_file) {
				if ($FLAG_RTF)
					$arr_data[] = "<*img*".$img_file.",15*img*>";	// ,ตัวเลขหลัง comma คือ image ratio
				else
					$arr_data[] = "<*img*".$img_file.",4*img*>";		// , ตัวเลขหลัง comma คือ จำนวนบรรทัดที่จะกำหนดให้ในแต่ละบรรทัด
			}
			$arr_data[] = $FULLNAME;
			if ($ITA_FLAG==1) {
				$arr_data[] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME ) . (trim($PM_NAME)?")":"");
				$arr_data[] = $PER_ADD;
				$arr_data[] = $PER_TEL;
				$arr_data[] = $PER_EMAIL;
				 
			} else {
				$arr_data[] = $PER_CARDNO;
				$arr_data[] = $PER_BIRTHDATE;
				$arr_data[] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME ) . (trim($PM_NAME)?")":"");/*เดิม*/
				$arr_data[] = $POSITION_TYPE;
                                if($search_per_type!=3){
                                    $arr_data[] = $POSITION_LEVEL;
                                    $arr_data[] = $CL_NAME;
                                }    
				$arr_data[] = $POS_NO;
				$arr_data[] = $ORG_NAME;
				$arr_data[] = $PER_SALARY;
				$arr_data[] = $PER_STARTDATE;
				$arr_data[] = $LEVEL_EFFECTIVEDATE;
				$arr_data[] = $DC_NAME;
				$arr_data[] = $PER_RETIREDATE;
				 
			}
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else {
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
//				echo "out...pdf-x::".$pdf->x." , pdf-y::".$pdf->y."<br>";
			}
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
		if (!$FLAG_RTF)
			$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "14", "", "000000", "");		// เส้นปิดบรรทัด			
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
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