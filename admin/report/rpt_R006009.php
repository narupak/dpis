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
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "e.PL_NAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$position_no= "b.POS_NO";
		$position_no_c= "c.POS_NO";
		$position_title=" สายงาน";
		 $type_code ="b.PT_CODE";
		 $select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "e.PN_NAME";
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$position_no= "b.POEM_NO";
		$position_no_c= "c.POEM_NO";
		$position_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "e.EP_NAME";
		 $line_search_code=trim($search_ep_code);
		 $line_search_name=trim($search_ep_name);
		$position_no= "b.POEMS_NO";
		$position_no_c= "c.POEMS_NO";
		$position_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=e.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "e.TP_NAME";
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);
		$position_no= "b.POT_NO";
		$position_no_c= "c.POT_NO";	
		$position_title=" ชื่อตำแหน่ง";
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "LEVEL", "POSNO", "NAME"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "h.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "h.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
			break;
			case "DEPARTMENT" : 
				if($select_list) $select_list .= ", ";
				$select_list .= "a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
			break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID";
				if($select_org_structure==1) $select_list .= "a.ORG_ID"; 

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				if($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";
				if($select_org_structure==1) $select_list .= "a.ORG_ID"; 

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				if($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";
				if($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				if($select_org_structure==1) $order_by .= "a.ORG_ID"; 

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= $line_code;

				$heading_name .= $position_title;
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO";

				$heading_name .= " $LEVEL_TITLE";
				break;
			case "POSNO" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$position_no as POS_NO";

				if($order_by) $order_by .= ", ";
				$order_by .= $position_no;

				$heading_name .= " $POS_NO_TITLE";
				break;
			case "NAME" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PER_NAME, a.PER_SURNAME";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";

				$heading_name .= " $FULLNAME_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure == 0) $order_by .= "b.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "b.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "a.ORG_ID";
	}

	$search_per_status[] = 1;

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if(trim($search_date)){
		$arr_temp = explode("/", $search_date);
		$show_date = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
		$search_date = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	} // end if

	if($list_person_type == "SELECT"){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}elseif($list_person_type == "CONDITION"){
		if(trim($search_pos_no)){ 
			$arr_search_condition[] = "($position_no_c like '$search_pos_no%')";
		} 
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)) $arr_search_condition[] = "(a.LEVEL_NO >= '$search_min_level')";
		if(trim($search_max_level)) $arr_search_condition[] = "(a.LEVEL_NO <= '$search_max_level')";
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;

//	include ("../report/rpt_condition3.php");
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
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
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= "$line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
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
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานการคำนวณบำเหน็จ/บำนาญ||ณ วันที่ $show_date";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0609";
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

		$fname= "rpt_R006009_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
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
		$orientation='P';

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
		global $MINISTRY_ID, $DEPARTMENT_ID,$ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $LEVEL_NO, $POS_NO, $PER_NAME, $PER_SURNAME, $EP_CODE, $TP_CODE;
		global $line_code,$position_no_c;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(h.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_1" :	
					if($select_org_structure==0){
						if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
					}
				break;
				case "ORG_2" :	
					if($select_org_structure==0){
						if($ORG_ID_2 && $ORG_ID_2!=-1) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID_2 && $ORG_ID_2!=-1) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
				break;
				case "LEVEL" :
					if($LEVEL_NO) $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO')";
					else $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO' or a.LEVEL_NO is null)";
				break;
				case "POSNO" :
					if($POS_NO){ 
						$arr_search_condition[] = "($position_no_c = '$POS_NO')";
					}else{ 
						$arr_addition_condition[] = "($position_no_c= $POS_NO or $position_no_c is null)";
					} // end if
				break;
				case "NAME" :
					if($PER_NAME) $arr_addition_condition[] = "(trim(a.PER_NAME) = '$PER_NAME')";
					else $arr_addition_condition[] = "(trim(a.PER_NAME) = '$PER_NAME' or a.PER_NAME is null)";
					if($PER_SURNAME) $arr_addition_condition[] = "(trim(a.PER_SURNAME) = '$PER_SURNAME')";
					else $arr_addition_condition[] = "(trim(a.PER_SURNAME) = '$PER_SURNAME' or a.PER_SURNAME is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $LEVEL_NO, $POS_NO, $PER_NAME, $PER_SURNAME, $EP_CODE,$TP_CODE;
		
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
				case "ORG_1" :	
					$ORG_ID_1 = -1;
				break;
				case "ORG_2" :	
					$ORG_ID_2 = -1;
				break;
				case "LINE" :
					$PL_CODE = -1;
					$PN_CODE=-1;
					$EP_CODE=-1;
					$TP_CODE=-1;
				break;
				case "LEVEL" :
					$LEVEL_NO = -1;
				break;
				case "POSNO" :
					$POS_NO = -1;
				break;
				case "NAME" :
					$PER_NAME = -1;
					$PER_SURNAME = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, b.ORG_ID, c.ORG_NAME,
											a.PER_STARTDATE, a.PER_SALARY, a.PER_MEMBER $select_type_code
						 from		(	
						 					(	
												(
													(	
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
										) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
						 $search_condition
						 order by		$order_by ";
						 
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, b.ORG_ID, c.ORG_NAME,
											a.PER_STARTDATE, a.PER_SALARY, a.PER_MEMBER $select_type_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_LEVEL g, PER_ORG h
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and $line_join(+) and a.LEVEL_NO=g.LEVEL_NO(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, b.ORG_ID, c.ORG_NAME,
											a.PER_STARTDATE, a.PER_SALARY, a.PER_MEMBER $select_type_code
						 from		(	
						 					(	
												(
													(	
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
										) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
						 $search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PL_CODE = $data[PL_CODE];
		$PL_NAME = $data[PL_NAME];
		$LEVEL_NO = $data[LEVEL_NO];
		$LEVEL_NAME = $data[LEVEL_NAME];
		$ORG_ID = $data[ORG_ID];
		$ORG_NAME = $data[ORG_NAME];
		if(trim($type_code)){
			$PT_CODE = $data[PL_CODE];
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);	
		}
		$PER_STARTDATE = substr($data[PER_STARTDATE], 0, 10);
		$WORKAGE = date_difference($search_date, $PER_STARTDATE, "full");
		$PER_STARTDATE = show_date_format($PER_STARTDATE,$DATE_DISPLAY);

		$PER_SALARY = $data[PER_SALARY];
		$PER_MEMBER = $data[PER_MEMBER];
		
		if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4
		
		$arr_content[$data_count][type] = "CONTENT";
		if ($have_pic && $img_file){
			if ($FLAG_RTF) {
//				$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
				$arr_content[$data_count][image] = "$img_file";
			} else {
//				$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
				$arr_content[$data_count][image] = "$img_file";
			}
		}
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME;
		$arr_content[$data_count][surname] = $PER_SURNAME;
		$arr_content[$data_count][position] = $PL_NAME ." ". (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		$arr_content[$data_count][level] = $LEVEL_NAME;
		$arr_content[$data_count][org] = $ORG_NAME;
		$arr_content[$data_count][startdate] = $PER_STARTDATE;
		$arr_content[$data_count][workage] = $WORKAGE;
		$arr_content[$data_count][salary] = number_format($PER_SALARY);
		$arr_content[$data_count][member] = $PER_MEMBER;

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

	if($count_data){
		if ($FLAG_RTF) {
			$RTF->set_font($font, 14);
			$RTF->color("0");	// 0=BLACK
		} else {
			$pdf->AutoPageBreak = false;
//		print_header();
		}
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME = $arr_content[$data_count][name];
			$SURNAME = $arr_content[$data_count][surname];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL = $arr_content[$data_count][level];
			$ORG = $arr_content[$data_count][org];
			$STARTDATE = $arr_content[$data_count][startdate];
			$STARTDATE = (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE):$STARTDATE);
			$WORKAGE = $arr_content[$data_count][workage];
			$WORKAGE = (($NUMBER_DISPLAY==2)?convert2thaidigit($WORKAGE):$WORKAGE);
			$SALARY = $arr_content[$data_count][salary];
			$SALARY = (($NUMBER_DISPLAY==2)?convert2thaidigit($SALARY):$SALARY);
			$MEMBER = $arr_content[$data_count][member];
			
			if($data_count > 0) 
				if ($FLAG_RTF) {
					$RTF->new_page();
				} else {
					$pdf->AddPage();
				}
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
		
			$border = "";
			if ($FLAG_RTF) {
				$RTF->set_table_font($font, 14);
				if($have_pic){
					if (file_exists($IMAGE)) {
						$fiximage_w = 120;  // 50px
						list($width, $height, $type, $attr) = getimagesize($IMAGE);
						$new_h = $height / $width * $fiximage_w;
						$new_ratio = floor(100 / $height * $fiximage_w); 
						$RTF->open_line();
						$RTF->cellImage(($IMAGE ? $IMAGE : ""), "$new_ratio", "95", "Right", "0", "");
						$RTF->close_line();
					}
				}

				$RTF->open_line();			
				$RTF->cell("ชื่อ $NAME", "50", "left", "0");
				$RTF->cell("สกุล $SURNAME", "150", "left", "0");
				$RTF->close_line();

				$RTF->open_line();			
				$RTF->cell("ตำแหน่ง $POSITION", "50", "left", "0");
				$RTF->cell("$LEVEL", "50", "left", "0");
				$RTF->close_line();

				$RTF->open_line();			
				$RTF->cell("$ORG_TITLE $ORG", "200", "left", "0");
				$RTF->close_line();

				$RTF->open_line();	
				$RTF->cell("วันที่บรรจุ $STARTDATE  (รวม $WORKAGE)", "200", "left", "0");
				$RTF->close_line();

				$RTF->open_line();	
				$RTF->cell("เงินเดือนปัจจุบัน $SALARY บาท", "200", "left", "0");
				$RTF->close_line();

				$RTF->set_font($font, 14);
				$RTF->open_line();	
				$RTF->cell($RTF->bold(1)."ผลการคำนวณ".$RTF->bold(0), "200", "left", "0");
				$RTF->close_line();

				$RTF->open_line();	
				$RTF->cell("เงินบำเหน็จ $SALARY บาท", "200", "left", "0");
				$RTF->close_line();
				$RTF->open_line();	
				$RTF->cell("เงินบำนาญ $SALARY บาท", "200", "left", "0");
				$RTF->close_line();

				$RTF->open_line();	
				$RTF->cell("การเป็นสมาชิก กบข.", "200", "left", "0");
				$RTF->close_line();

				$RTF->open_line();	
				$RTF->cell("", "15", "left", "0");
				$RTF->cellImage(($MEMBER?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg"), 4, 4, "center", 0);
				$RTF->cell("เป็นสมาชิก", "40", "left", "0");
				$RTF->cell("", "20", "center", "0");
				$RTF->cellImage((!$MEMBER?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg"), 4, 4, "center", 0);
				$RTF->cell("ไม่เป็นสมาชิก", "130", "center", "0");
				$RTF->close_line();
			} else {
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				if($have_pic){
//					echo "IMAGE=".$IMAGE."<br>";
					if (file_exists($IMAGE)) {
						$fiximage_w = 20;  // 20px
						list($width, $height, $type, $attr) = getimagesize($IMAGE);
//						$pdf->Cell(50, 7, "$IMAGE", $border, 0, 'L', 0);
						$image_x = ($pdf->x + 170);	 $image_y = ($pdf->y - 20);	 $image_w = 25;	 $image_h = 35;
						$pdf->Image($IMAGE, $image_x, $image_y, $image_w, $image_h);
					}
				}
				
				$pdf->Cell(50, 7, "ชื่อ $NAME", $border, 0, 'L', 0);
				$pdf->Cell(150, 7, "สกุล $SURNAME", $border, 1, 'L', 0);

				$pdf->Cell(100, 7, "ตำแหน่ง $POSITION", $border, 0, 'L', 0);
				$pdf->Cell(100, 7, "$LEVEL", $border, 1, 'L', 0);

				$pdf->Cell(200, 7, "$ORG_TITLE $ORG", $border, 1, 'L', 0);

				$pdf->Cell(200, 7, "วันที่บรรจุ $STARTDATE  (รวม $WORKAGE)", $border, 1, 'L', 0);

				$pdf->Cell(200, 7, "เงินเดือนปัจจุบัน $SALARY บาท", $border, 1, 'L', 0);

				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell(200, 7, "ผลการคำนวณ", $border, 1, 'L', 0);

				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell(200, 7, "เงินบำเหน็จ $SALARY บาท", $border, 1, 'L', 0);
				$pdf->Cell(200, 7, "เงินบำนาญ $SALARY บาท", $border, 1, 'L', 0);

				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell(200, 7, "การเป็นสมาชิก กบข.", $border, 1, 'L', 0);

				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell(15, 7, "", $border, 0, 'C', 0);
	//			if($MEMBER){ 
					$start_x = $pdf->x;			$start_y = $pdf->y;
					$pdf->Image((($MEMBER)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg"),($pdf->x - 15 + (15 / 2) - 1.5), ($pdf->y + 1.5), 4, 4,"jpg");
					$pdf_x = $start->x;			$pdf_y = $start->y;
	//			} 
				$pdf->Cell(40, 7, "เป็นสมาชิก", $border, 0, 'L', 0);
				$pdf->Cell(15, 7, "", $border, 0, 'C', 0);
	//			if(!$MEMBER){ 
					$start_x = $pdf->x;			$start_y = $pdf->y;
					$pdf->Image(((!$MEMBER)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg"),($pdf->x - 15 + (15 / 2) - 1.5), ($pdf->y + 1.5), 4, 4,"jpg");
					$pdf_x = $start->x;			$pdf_y = $start->y;
	//			} 			
				$pdf->Cell(130, 7, "ไม่เป็นสมาชิก", $border, 0, 'L', 0);
			}
		} // end for		
	}else{
		if ($FLAG_RTF) {
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
		} else {
			$pdf->SetFont($fontb,'',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		}
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
	//	$RTF->close_section(); 
		
		$RTF->display($fname);
	} else {
	    $pdf->close_tab(""); 
		$pdf->close();
		$pdf->Output();		
	}
?>