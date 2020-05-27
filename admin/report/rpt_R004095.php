<?php
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
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))";
				elseif($DPISDB=="oci8") $order_by .= "to_number(replace(b.POS_NO,'-',''))";
				elseif($DPISDB=="mysql") $order_by .= "b.POS_NO+0";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="oci8") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="mysql") $order_by .= "a.LEVEL_NO desc";
				break;
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID_1";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID_2";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type) and (a.PER_STATUS = 1) and (b.POS_ID >= 0)";
//	$arr_search_condition[] = "(a.PER_TYPE=1)";	
	
	$list_type_text = $ALL_REPORT_TITLE;
	if($select_org_structure==0){ $list_type_text .= " โครงสร้างตามกฏหมาย"; 	}
	elseif($select_org_structure==1){ $list_type_text .= " โครงสร้างตามมอบหมายงาน"; }  		
	
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
		$arr_search_condition[] = "(d.OT_CODE='01')";
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
		$arr_search_condition[] = "(d.OT_CODE='02')";
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
		$arr_search_condition[] = "(d.OT_CODE='03')";
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
		$arr_search_condition[] = "(d.OT_CODE='04')";
	}
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
				$arr_search_condition[] = "(a.ORG_ID_1 =  $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				$arr_search_condition[] = "(a.ORG_ID_2 =  $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if(trim($search_pl_code) && trim($search_pn_code)  && trim($search_ep_code) ){ 
			$search_pl_code = trim($search_pl_code);
			$search_pn_code = trim($search_pn_code);
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code'))";
			$list_type_text .= "$search_pl_name, $search_pn_name,$search_ep_code";
		}elseif(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code'))";
			$list_type_text .= "$search_pl_name";
		}elseif(trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%'))";
			$list_type_text .= "$search_pn_name";
		}elseif(trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%'))";
			$list_type_text .= "$search_ep_name";
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
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type] (ตามมอบหมายงาน)";
	$report_code = "R0495";
	include ("rpt_R004095_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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
		$orientation='L';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('angsa','',8);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	if($DPISDB=="odbc"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, 
										d.ORG_SEQ_NO, d.ORG_CODE, d.ORG_NAME, a.ORG_ID AS ORG_ID_ASSIGN 
						 from	(
										(
											(
												( 	
													(
														(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
											) left join PER_PROVINCE i on (d.PV_CODE=i.PV_CODE)											
										) left join PER_ORG_TYPE j on (d.OT_CODE=j.OT_CODE)
									) left join PER_LEVEL p on (k.LEVEL_NO_ASSIGN=p.LEVEL_NO)
								$search_condition
						group by a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, i.PV_NAME, j.OT_NAME,c.ORG_NAME, 
										d.ORG_SEQ_NO, d.ORG_CODE, d.ORG_NAME, a.ORG_ID
						order by		$order_by";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, 
										d.ORG_SEQ_NO, d.ORG_CODE, d.ORG_NAME, a.ORG_ID AS ORG_ID_ASSIGN, 
                                                                                a.ORG_ID_1 AS ORG_ID_ASSIGN_1 , a.ORG_ID_2 AS ORG_ID_ASSIGN_2
										from PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_LEVEL h, PER_PROVINCE i, PER_ORG_TYPE j, PER_LEVEL p 
										where a.POS_ID=b.POS_ID(+) and b.ORG_ID=d.ORG_ID(+) and a.LEVEL_NO=h.LEVEL_NO(+) and d.PV_CODE=i.PV_CODE(+)
										and d.OT_CODE=j.OT_CODE(+) and a.department_id=c.org_id(+)
										$search_condition
						group by a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, i.PV_NAME, j.OT_NAME,c.ORG_NAME, 
										d.ORG_SEQ_NO, d.ORG_CODE, d.ORG_NAME, a.ORG_ID ,a.ORG_ID_1,a.ORG_ID_2
						order by		$order_by";
	}
	/*if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$cmd = str_replace("PER_ORG_ASS_TYPE", "PER_ORG_TYPE", $cmd);
	}*/
	$count_data = $db_dpis->send_cmd($cmd);
//	echo $cmd."<br><br>";
//	$db_dpis->show_error();
	if($count_data){
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
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$ORG_NAME_2="";
			$ORG_NAME_1="";
			$ORG_NAME_0="";
			$ORG_NAME="";
			$ORG_ID_REF="";

			$ORG_NAME_ASS_2="";
			$ORG_NAME_ASS_1="";
			$ORG_NAME_ASS="";
			$ORG_NAME_ASSIGN="";
			$ORG_ID_REF_ASSIGN="";

			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];

			$POS_NO = $data[POS_NO];
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE) = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$NAME = ($PN_NAME)."$PER_NAME";
			$SURNAME = trim($data[PER_SURNAME]);

			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);

			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);

			$PL_NAME = trim($PL_NAME) . " " . $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"");

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$level=trim($data[LEVEL_NAME]);
			$LEVEL_NAME1 = substr($level,strpos($level,"ประเภท")+6);
			$LEVEL_NAME1 = substr($LEVEL_NAME1,0,strlen($LEVEL_NAME1)-strlen(substr($LEVEL_NAME1,strpos($LEVEL_NAME1,"ระดับ")-1)));
			$LEVEL_NAME2 = substr($level,strpos($level,"ระดับ")+5);

			$PER_SALARY = $data[PER_SALARY];
		
			//หน่วยงานตามกฎหมาย
			$ORG_ID_REF = $data[ORG_ID_2];
			if ($ORG_ID_REF=="") $ORG_ID_REF = $data[ORG_ID_1];
			if ($ORG_ID_REF=="") $ORG_ID_REF = $data[ORG_ID];

			//วนลูปจนกว่าจะได้ชื่อกรม
			$DEPARTMENT_NAME="";
			while($DEPARTMENT_NAME == ""){
				$cmd = " SELECT ORG_NAME,ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID='$ORG_ID_REF'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				
				if (trim($data2[OL_CODE]) == "05"){
					$ORG_NAME_2 = $data2[ORG_NAME];
					$ORG_ID_REF = $data2[ORG_ID_REF];
				}
				elseif (trim($data2[OL_CODE]) == "04") {
					$ORG_NAME_1 = $data2[ORG_NAME];
					$ORG_ID_REF = $data2[ORG_ID_REF];
				}
				elseif (trim($data2[OL_CODE]) == "03") {
					$ORG_NAME_0 = $data2[ORG_NAME];
					$ORG_ID_REF = $data2[ORG_ID_REF];
				}
				elseif (trim($data2[OL_CODE]) == "02") $DEPARTMENT_NAME = $data2[ORG_NAME];
			}
			
			$ORG_NAME = $ORG_NAME_2;
			if ($ORG_NAME != "") $ORG_NAME.= "/ ".$ORG_NAME_1;
			else $ORG_NAME= $ORG_NAME_1;
			if ($ORG_NAME != "") $ORG_NAME.= "/ ".$ORG_NAME_0;
			else $ORG_NAME= $ORG_NAME_0;
			$PV_NAME=trim($data[PV_NAME]);
			$OT_NAME=trim($data[OT_NAME]);
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			
			//หน่วยงานตามมอบหมายงาน
			//$ORG_ID_REF_ASSIGN = $data[ORG_ID_ASSIGN];
                        $ORG_ID_REF_ASSIGN = $data[ORG_ID_ASSIGN_2];
			if ($ORG_ID_REF_ASSIGN=="") $ORG_ID_REF_ASSIGN = $data[ORG_ID_ASSIGN_1];
			if ($ORG_ID_REF_ASSIGN=="") $ORG_ID_REF_ASSIGN = $data[ORG_ID_ASSIGN];
                        
			//$ORG_ID_ASSIGN = $data[ORG_ID_ASSIGN];
			//ถ้าไม่มีหน่วยงานตามมอบหมายงานให้แสดงค่าว่าง
			if ($ORG_ID_REF_ASSIGN==""){
				$ORG_NAME_ASSIGN="";
				$PV_NAME_ASSIGN="";
				$OT_NAME_ASSIGN="";
				$DEPARTMENT_NAME_ASSIGN="";
				$DEPARTMENT_DIFF="";
			}else{
				//วนลูปจนกว่าจะได้ชื่อกรม
				$DEPARTMENT_NAME_ASSIGN="";
				while($DEPARTMENT_NAME_ASSIGN == ""){
					$cmd = " SELECT ORG_NAME,ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID='$ORG_ID_REF_ASSIGN'";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();

					if (trim($data2[OL_CODE]) == "05"){
						$ORG_NAME_ASS_2 = $data2[ORG_NAME];
						$ORG_ID_REF_ASSIGN = $data2[ORG_ID_REF];
					}
					elseif (trim($data2[OL_CODE]) == "04") {
						$ORG_NAME_ASS_1 = $data2[ORG_NAME];
						$ORG_ID_REF_ASSIGN = $data2[ORG_ID_REF];
					}
					elseif (trim($data2[OL_CODE]) == "03") {
						$ORG_NAME_ASS = $data2[ORG_NAME];
						$ORG_ID_REF_ASSIGN = $data2[ORG_ID_REF];
					}
					elseif (trim($data2[OL_CODE]) == "02") $DEPARTMENT_NAME_ASSIGN = $data2[ORG_NAME];
				} // while($DEPARTMENT_NAME_ASSIGN == "")
				$ORG_NAME_ASSIGN = $ORG_NAME_ASS_2;
				if ($ORG_NAME_ASSIGN != "") $ORG_NAME_ASSIGN.= "/ ".$ORG_NAME_ASS_1;
				else $ORG_NAME_ASSIGN= $ORG_NAME_ASS_1;
				if ($ORG_NAME_ASSIGN != "") $ORG_NAME_ASSIGN.= "/ ".$ORG_NAME_ASS;
				else $ORG_NAME_ASSIGN= $ORG_NAME_ASS;

				$cmd = " SELECT PV_NAME FROM PER_PROVINCE a, PER_ORG_ASS b WHERE a.PV_CODE=b.PV_CODE and b.ORG_ID='$ORG_ID_ASSIGN'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PV_NAME_ASSIGN=trim($data[PV_NAME]);

				$cmd = " SELECT OT_NAME FROM PER_ORG_TYPE a, PER_ORG_ASS b WHERE a.OT_CODE=b.OT_CODE and b.ORG_ID='$ORG_ID_ASSIGN'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$OT_NAME_ASSIGN=trim($data[OT_NAME]);

				if ($DEPARTMENT_NAME == $DEPARTMENT_NAME_ASSIGN) $DEPARTMENT_DIFF="กรมเดียวกัน";
				else  $DEPARTMENT_DIFF="ต่างกรม";
			} // end if ($ORG_ID_ASSIGN=="")
			
			if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4
			
			$arr_data = (array) null;
			$arr_data[] = $data_count;
			if ($have_pic && $img_file) {
				if ($FLAG_RTF)
					$arr_data[] = "<*img*".$img_file.",15*img*>";	// ,ตัวเลขหลัง comma คือ image ratio
				else
					$arr_data[] = "<*img*".$img_file.",4*img*>";		// , ตัวเลขหลัง comma คือ จำนวนบรรทัดที่จะกำหนดให้ในแต่ละบรรทัด
			}	
			$arr_data[] = $POS_NO;	
			$arr_data[] = $NAME;					
			$arr_data[] = $SURNAME;			
			$arr_data[] = $PL_NAME;			
			$arr_data[] = $LEVEL_NAME1;
			$arr_data[] = $LEVEL_NAME2;
			$arr_data[] = $PER_SALARY;
			$arr_data[] = $ORG_NAME;
			$arr_data[] = $PV_NAME;
			$arr_data[] = $OT_NAME;
			$arr_data[] = $DEPARTMENT_NAME;
			$arr_data[] = $ORG_NAME_ASSIGN;
			$arr_data[] = $PV_NAME_ASSIGN;
			$arr_data[] = $OT_NAME_ASSIGN;
			$arr_data[] = $DEPARTMENT_NAME_ASSIGN;
			$arr_data[] = $DEPARTMENT_DIFF;

			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
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