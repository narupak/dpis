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
		$line_join = "b.PL_CODE=f.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "f.PL_NAME";
		$position_no= "b.POS_NO";
		$position_no_name= "b.POS_NO_NAME";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_code = "b.PN_CODE";
                $position_no_name= "b.POEM_NO_NAME";
		$line_name = "f.PN_NAME";
		$position_no= "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_code = "b.EP_CODE";
                $position_no_name= "b.POEMS_NO_NAME";
		$line_name = "f.EP_NAME";
		$position_no= "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_code = "b.TP_CODE";
                $position_no_name= "b.POT_NO_NAME";
		$line_name = "f.TP_NAME";
		$position_no= "b.POT_NO";
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
//	$arr_rpt_order = array("ORG"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "g.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" : 
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break; 
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;

			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1";
		else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1";
		else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1";
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($DPISDB=="odbc"){
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-12-31')";
	}elseif($DPISDB=="oci8"){
		$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) < '". ($search_budget_year - 543) ."-12-31')";
	}elseif($DPISDB=="mysql"){
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-12-31')";
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;

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
        if($select_org_structure==0){
           if($search_org_id)  $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
        }elseif($select_org_structure==1){
            if($search_org_ass_id)  $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
        }
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

//	$company_name = "";
	$report_title = "บัญชีรายละเอียดการเลื่อนขั้นเงินเดือน $PERSON_TYPE[$search_per_type] ย้อนหลัง 5 ปี||กรม/ส่วนราชการที่เทียบเท่า $DEPARTMENT_NAME";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0604";
	include ("rpt_R006004_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R006004_rtf.rtf";

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
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(g.ORG_ID_REF= $MINISTRY_ID)";
					break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
					break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1)  $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1)  $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
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
			} // end switch case
		} // end for
        
    
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1;
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
			} // end switch case
		} // end for
	} // function
	
//	if ($search_condition) $search_condition = $search_condition." and a.PER_ID < 1000"; else $search_condition = "a.PER_ID < 1000";
	if($DPISDB=="odbc"){
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											$position_no_name as POS_NO_NAME, $line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP, e.SM_CODE  $select_type_code
						 from		(
											(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on ($line_join)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
						 order by		$order_by, IIf(IsNull($position_no), 0, CLng($position_no)), LEFT(trim(e.SAH_EFFECTIVEDATE), 10) desc, LEFT(trim(e.SAH_DOCDATE), 10) desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, to_number(replace($position_no,'-','')) as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP, e.SM_CODE $select_type_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_SALARYHIS e, $line_table f, PER_ORG g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID and $line_join(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
											$search_condition
						 order by		$order_by, to_number(replace($position_no,'-','')), SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) desc, SUBSTR(trim(e.SAH_DOCDATE), 1, 10) desc ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP, e.SM_CODE $select_type_code
						 from		(
											(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on ($line_join)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
						 order by		$order_by, $position_no, e.SAH_EFFECTIVEDATE desc, e.SAH_DOCDATE desc ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "<pre>".$cmd."<br>";
	$data_count = $data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
				$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
				switch($REPORT_ORDER){
				case "MINISTRY" :
						if($MINISTRY_ID != trim($data[MINISTRY_ID])){
							$MINISTRY_ID = trim($data[MINISTRY_ID]);
							if($MINISTRY_ID != "" && $MINISTRY_ID != 0 && $MINISTRY_ID != -1){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$MINISTRY_NAME = $data2[ORG_NAME];
							} // end if

							if($MINISTRY_NAME) {
								$addition_condition = generate_condition($rpt_order_index);

								$arr_content[$data_count][type] = "MINISTRY";
		//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
								$arr_content[$data_count][name] = $MINISTRY_NAME;
								
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

								$data_count++;
							}
						} // end if
					break;
				case "DEPARTMENT" : 
						if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
							$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
							if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$DEPARTMENT_NAME = $data2[ORG_NAME];
							} // end if

							if($DEPARTMENT_NAME) {
								$addition_condition = generate_condition($rpt_order_index);

								$arr_content[$data_count][type] = "DEPARTMENT";
								$arr_content[$data_count][name] = $DEPARTMENT_NAME;
								
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

								$data_count++;
							}
						} // end if
					break;
					case "ORG" :
						if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
							} // end if
	//                       if($ORG_NAME=="" || $ORG_NAME=="NULL")	$ORG_NAME="[ไม่ระบุ $ORG_TITLE]";
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
							if($ORG_NAME) {
								$addition_condition = generate_condition($rpt_order_index);

								$arr_content[$data_count][type] = "ORG";
		//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
								$arr_content[$data_count][name] = $ORG_NAME;
								
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

								$data_count++;
							}
						} // end if
					break;

					case "ORG_1" :
						if($ORG_ID_1 != trim($data[ORG_ID_1])){
							$ORG_ID_1 = trim($data[ORG_ID_1]);
							$ORG_NAME_1 = $ORG_SHORT_1 = "";
							if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_1 = $data2[ORG_NAME];
							} // end if
	//	              if($ORG_NAME_1=="" || $ORG_NAME_1=="NULL")	$ORG_NAME_1="[ไม่ระบุ$ORG_TITLE1]";
							if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
							if($ORG_NAME_1) {
								$addition_condition = generate_condition($rpt_order_index);

								$arr_content[$data_count][type] = "ORG_1";
		//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
								$arr_content[$data_count][name] = $ORG_NAME_1;
								
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

								$data_count++;
							}
						} // end if
					break;
				} // end switch case

				if($rpt_order_index == (count($arr_rpt_order) - 1)){
					if($PER_ID != $data[PER_ID]){
						$data_row++;
								
						$PER_ID = $data[PER_ID];
						$PN_NAME = trim($data[PN_NAME]);
						$PER_NAME = trim($data[PER_NAME]);
						$PER_SURNAME = trim($data[PER_SURNAME]);
						//$POS_NO = $data[POS_NO];
						$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);
						$LEVEL_NO = $data[LEVEL_NO];
						if(trim($type_code)){
							$PT_CODE = trim($data[PT_CODE]);
							$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PT_NAME = trim($data3[PT_NAME]);
						}
						$PER_SALARY = $data[PER_SALARY];
						
						$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
						$db_dpis3->send_cmd($cmd);
	//					$db_dpis->show_error();
						$data3 = $db_dpis3->get_array();
						$LEVEL_NAME=$data3[LEVEL_NAME];
						$POSITION_LEVEL = $data3[POSITION_LEVEL];
                        
						if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
			
						if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4
			
						$arr_content[$data_count][type] = "DETAIL";
						$arr_content[$data_count][order] = $data_row;
						if ($have_pic && $img_file){
							if ($FLAG_RTF)
							$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
							else
							$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
						}
	//					$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
						$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
						$arr_content[$data_count][pos_no] = $POS_NO;
						$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
						$arr_content[$data_count][salary] = $PER_SALARY;
						for($i=1; $i<=10; $i++){ 
							$arr_content[$data_count]["count_".$i] = 0;
							$arr_content[($data_count + 1)]["count_".$i] = 0;
						} // end for
						$data_count++;
                                                
                                                $arr_content[$data_count][type] = "DETAIL2";
                                                $data_count++;
                                                
                                                if($search_per_type!=2){
                                                    $arr_content[$data_count][type] = "DETAIL3";
                                                    $data_count++;
                                                }
                                                 
					} // end if

					$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
					$arr_temp = explode("-", $SAH_EFFECTIVEDATE);
					$SAH_YEAR = $arr_temp[0] + 543;
					$SAH_MONTH = $arr_temp[1] + 0;
					$SAH_DATE = $arr_temp[2] + 0;
								
					$SM_CODE = trim($data[SM_CODE]);
					$MOV_CODE = trim($data[MOV_CODE]);
					$cmd = " select MOV_SUB_TYPE,MOV_NAME from PER_MOVMENT where	MOV_CODE='$MOV_CODE' ";
					$db_dpis3->send_cmd($cmd);
					$data3 = $db_dpis3->get_array();
					$MOV_SUB_TYPE = trim($data3[MOV_SUB_TYPE]);
                                        $MOV_NAME = trim($data3[MOV_NAME]);
					$PR_LEVEL = 0;
                                       
					if (($MOV_SUB_TYPE == '45') || ($BKK_FLAG==1 && $SM_CODE == '2') || ($BKK_FLAG!=1 && $SM_CODE == '1')) {
						$PR_LEVEL = 0.5;
					} elseif (($MOV_SUB_TYPE == '46') || ($BKK_FLAG==1 && $SM_CODE == '3') || ($BKK_FLAG!=1 && $SM_CODE == '2')) {
						$PR_LEVEL = 1.0;
					} elseif (($MOV_SUB_TYPE == '47') || ($BKK_FLAG==1 && $SM_CODE == '4') || ($BKK_FLAG!=1 && $SM_CODE == '3')) {
						$PR_LEVEL = 1.5;
					} elseif (($MOV_SUB_TYPE == '48') || ($BKK_FLAG==1 && $SM_CODE == '5') || ($BKK_FLAG!=1 && $SM_CODE == '4')) {
						$PR_LEVEL = 2.0;
					} else {
						$PR_LEVEL = 0;
					}
                                        
                                        if(!$PR_LEVEL){
                                            if(strpos($MOV_NAME, "0.5 ขั้น") == TRUE){ $PR_LEVEL = 0.5;}
                                            if(strpos($MOV_NAME, "1 ขั้น") == TRUE){ $PR_LEVEL = 1.0;}
                                            if(strpos($MOV_NAME, "1.5 ขั้น") == TRUE){ $PR_LEVEL = 1.5;}
                                            if(strpos($MOV_NAME, "2 ขั้น") == TRUE){ $PR_LEVEL = 2.0;}
                                        }
                                        
                                        
					$SAH_SALARY = $data[SAH_SALARY];
					$SAH_PERCENT_UP = $data[SAH_PERCENT_UP];
					if ($SAH_PERCENT_UP) $PR_LEVEL = $SAH_PERCENT_UP;
					
					if($SAH_MONTH == 10){
                                            if ($SAH_PERCENT_UP || $PR_LEVEL) { //if ($SAH_PERCENT_UP) {
                                                if($search_per_type!=2){
                                                    if (!$arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)]){
                                                        $arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] = $PR_LEVEL;
                                                        $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] =$MOV_NAME;
                                                    }	
                                                }else{
                                                    if (!$arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)])
                                                        $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] = $PR_LEVEL;
                                                }    
                                            } else {
                                                if($search_per_type!=2){
                                                    $arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] += $PR_LEVEL;
                                                    $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] +=$MOV_NAME; 
                                                }else{
                                                    $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] += $PR_LEVEL;
                                                }    
                                            }
                                            if($SAH_SALARY > $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)]) $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] = $SAH_SALARY;
					
					}elseif($SAH_MONTH == 4){
                                            if ($SAH_PERCENT_UP || $PR_LEVEL) { //if ($SAH_PERCENT_UP) {
                                                if($search_per_type!=2){
                                                    if (!$arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)]){
                                                        $arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] = $PR_LEVEL;
                                                        $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] =$MOV_NAME;  
                                                    }
                                                }else{
                                                    if (!$arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)])
                                                        $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] = $PR_LEVEL;
                                                }    
                                            } else {
                                                if($search_per_type!=2){
                                                    $arr_content[($data_count - 3)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] += $PR_LEVEL;
                                                    $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] +=$MOV_NAME;
                                                }else{
                                                    $arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] += $PR_LEVEL;
                                                }    
                                            }
                                            if($SAH_SALARY > $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)]) $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] = $SAH_SALARY;
					
					}
				} // end if
			} // end for
		}
	} // end while
	
	//echo "<pre>"; print_r($arr_content); echo "</pre>";

	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
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
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$PER_SALARY = $arr_content[$data_count][salary];
			for($i=1; $i<=10; $i++) ${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			if ($have_pic && $img_file) $arr_data[] = $IMAGE;
			$arr_data[] = $NAME;
			$arr_data[] = $POS_NO;
			$arr_data[] = $POSITION;
			if($REPORT_ORDER == "DETAIL"){
				$arr_data[] = $PER_SALARY;
				for($i=1; $i<=10; $i++) $arr_data[] = ${"COUNT_".$i};
			}elseif($REPORT_ORDER == "DETAIL2"){
				$arr_data[] = "";
				for($i=1; $i<=10; $i++) $arr_data[] = ${"COUNT_".$i};
			}else if($REPORT_ORDER == "DETAIL3"){
                            if($search_per_type!=2){
				$arr_data[] = "";
				for($i=1; $i<=10; $i++) $arr_data[] = ${"COUNT_".$i};
                            }    
			}else{
				$arr_data[] = "";
				for($i=1; $i<=10; $i++) $arr_data[] = "";
			} // end if
	
			if($REPORT_ORDER == "DETAIL" || $REPORT_ORDER == "DETAIL2" || $REPORT_ORDER == "DETAIL3") {
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			} else {
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			}
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for				
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