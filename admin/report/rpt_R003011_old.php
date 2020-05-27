<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		 $line_search_code=trim($search_pl_code);
		 $line_search_name=trim($search_pl_name);
		 $line_title=" สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";	
		 $line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";	
		 $line_search_code=trim($search_ep_code);
		 $line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";	
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if

	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_ORG3 as ORG_ID_3";
				else if($select_org_structure==1)  $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_ORG3";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_UNDER_ORG1";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_UNDER_ORG1";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_UNDER_ORG2";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_UNDER_ORG2";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";
					
				if($order_by) $order_by .= ", ";
				$order_by .= "$line_code";
				
				$heading_name .=  $line_title;
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0) $order_by = "e.POH_ORG3";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure==0)  $select_list = "e.POH_ORG3";
		else if($select_org_structure==1)   $select_list = "a.ORG_ID";
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
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
		$arr_search_condition[] = "(c.OT_CODE='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
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
		$arr_search_condition[] = "(c.OT_CODE='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
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
		$arr_search_condition[] = "(c.OT_CODE='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
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
		$arr_search_condition[] = "(c.OT_CODE='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(e.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} 
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
			}
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} 
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			}
		}
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
	}elseif($list_type == "PER_COUNTRY"){
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
	}else{
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
			if($select_org_structure==0) $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	if ($search_mov_code[0]==1) { 
		if ($BKK_FLAG==1)
			$arr_mov_code[] = " '35', '208', '13', '27', '28', '29', '59', '20101', '20102', '20103', '20104', '20105', '20106', '20107', '20108', '20109', '20110', '20112', '203', '204', '205', '209' ";
		else
			$arr_mov_code[] = " '118', '11810', '11820', '11830', '120', '12010', '12020', '12030', '121', '12110', '122', '12210' ";
		$mov_name .= "ลาออก ";
	} 
	if ($search_mov_code[0]==2 || $search_mov_code[1]==2) {
		if ($BKK_FLAG==1)
			$arr_mov_code[] = " '5', '202' ";
		else
			$arr_mov_code[] = " '106','10610','10620' ";
		$mov_name .= "ให้โอน ";
	} 
	if ($search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3) {
		if ($BKK_FLAG==1)
			$arr_mov_code[] = " '17','201' ";
		else
			$arr_mov_code[] = " '119','11910' ";
		$mov_name .= "เกษียณอายุ ";
	}
	if ($search_mov_code[0]==4 || $search_mov_code[1]==4 || $search_mov_code[2]==4 || $search_mov_code[3]==4) {
		if ($BKK_FLAG==1)
			$arr_mov_code[] = " '30', '206' ";
		else
			$arr_mov_code[] = " '123','12310' ";
		$mov_name .= "เสียชีวิต ";
	}

	if ($mov_name=="เกษียณอายุ ") {
		if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE)";
		elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE)";
		elseif($DPISDB=="mysql") $search_condition .= "and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE)";
	} elseif (strpos($mov_name,"เกษียณอายุ") !== false) {
		if($DPISDB=="odbc") $search_condition .= " and ((LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE) or (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01'))";
		elseif($DPISDB=="oci8") $search_condition .= " and ((SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE) or (SUBSTR(trim(a.PER_POSDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and SUBSTR(trim(a.PER_POSDATE), 1, 10) < '".($search_budget_year - 543)."-10-01'))";
		elseif($DPISDB=="mysql") $search_condition .= " and ((LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE) or (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01'))";
	} else {
		if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_POSDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and SUBSTR(trim(a.PER_POSDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	}
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";

	$show_budget_year = (($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year);
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type] ที่$mov_name ในปีงบประมาณ $show_budget_year";
	$report_code = "R0311";
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

	if ($search_mov_code[0]==1 || $search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3 || $search_mov_code[0]==4 || 
		$search_mov_code[1]==4 || $search_mov_code[2]==4 || $search_mov_code[3]==4) {
		$heading_width[0] = "10";
		$heading_width[1] = "15";
		$heading_width[2] = "38";
		$heading_width[3] = "30";
		$heading_width[4] = "18";
		$heading_width[5] = "35";
		$heading_width[6] = "35";
		$heading_width[7] = "25";
		$heading_width[8] = "25";
		$heading_width[9] = "25";
		$heading_width[10] = "30";
	
	//new format***************************************************
		$heading_text[0] = "ลำดับ|ที่";
		$heading_text[1] = "คำนำ|หน้านาม";
		$heading_text[2] = "ชื่อ-สกุล|";
		$heading_text[3] = "ชื่อตำแหน่ง|ในสายงาน";
		$heading_text[4] = "ระดับ|";
		$heading_text[5] = "วุฒิการศึกษา|";
		$heading_text[6] = "วุฒิการศึกษา|สูงสุด";
		$heading_text[7] = "วัน/เดือน/ปี|เกิด ";
		$heading_text[8] = "วัน/เดือน/ปี|ที่บรรจุ ";
		$heading_text[9] = "วัน/เดือน/ปี|ที่ลาออก";
		$heading_text[10] = "เหตุผลใน|การลาออก";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C');				
		
	} else {
		$heading_width[0] = "10";
		$heading_width[1] = "15";
		$heading_width[2] = "40";
		$heading_width[3] = "30";
		$heading_width[4] = "25";
		$heading_width[5] = "35";
		$heading_width[6] = "30";
		$heading_width[7] = "20";
		$heading_width[8] = "20";
		$heading_width[9] = "20";
		$heading_width[10] = "45";
		
	//new format ***************************************************************
		$heading_text[0] = "ลำดับ|ที่";
		$heading_text[1] = "คำนำ|หน้านาม";
		$heading_text[2] = "ชื่อ-สกุล|";
		$heading_text[3] = "ชื่อตำแหน่ง|ในสายงาน";
		$heading_text[4] = "ระดับ";
		$heading_text[5] = "วุฒิการศึกษา|ที่รับโอน";
		$heading_text[6] = "วุฒิการศึกษา|สูงสุด";
		$heading_text[7] = "วัน/เดือน/ปี|เกิด ";
		$heading_text[8] = "วัน/เดือน/ปี|ที่รับโอน ";
		$heading_text[9] = "ส่วนราชการ|เดิมก่อนโอน";
		$heading_text[10] = "ลักษณะการโอน|(ปกติ/สอบ)";
			
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C');				
	}
			
	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3,$RPT_N,$select_org_structure;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $arr_mov_code, $position_table, $position_join, $line_code, $DATE_DISPLAY;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($DPISDB=="odbc"){
			$cmd = " select		distinct a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as 
											PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, $line_code as PL_CODE, a.LEVEL_NO, a.MOV_CODE,
											LEFT(trim(a.PER_POSDATE), 10) as PER_POSDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE
							 from	(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
										) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
											$search_condition ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select		distinct a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as 
											PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, $line_code as PL_CODE, a.LEVEL_NO, a.MOV_CODE, 
											SUBSTR(trim(a.PER_POSDATE), 1, 10) as PER_POSDATE,
											SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE
							 from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=e.PER_ID(+) and a.PN_CODE=d.PN_CODE(+)
											$search_condition ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		distinct a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as 
											PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, $line_code as PL_CODE, a.LEVEL_NO, a.MOV_CODE,
											LEFT(trim(a.PER_POSDATE), 10) as PER_POSDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE
							 from	(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
										) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
											$search_condition ";
		} // end if
		if($select_org_structure==1){
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis2->show_error();
		while($data = $db_dpis2->get_array()){
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);

			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], $DATE_DISPLAY);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE], $DATE_DISPLAY);
			$PER_POSDATE = substr(trim($data[PER_POSDATE]), 0, 10);
			$PER_RETIREDATE = substr(trim($data[PER_RETIREDATE]), 0, 10);
			if (!$PER_POSDATE) $PER_POSDATE = $PER_RETIREDATE;

			//หาวุฒิการศึกษาบรรจุ	
			$cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'
							 order by a.EDU_SEQ desc,a.EN_CODE ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$EDU_USE = $data2[EN_NAME];
			
			//หาวุฒิการศึกษาสูงสุด
			$cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%'
							 order by a.EDU_SEQ desc,a.EN_CODE ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$EDU_MAX = $data2[EN_NAME];

			//หาส่วนราชการเดิม
			//ระบุวันก่อนหน้าที่ได้รับโอน หาจากวันสิ้นสุดการดำรงตำแหน่ง
			if(count($arr_mov_code)) $mov_code = " and trim(MOV_CODE) in (". implode(" , ", $arr_mov_code) . ")";
			if($DPISDB=="odbc"){
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_POSDATE' ";
				$cmd = " select 	POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
												PL_CODE, PT_CODE, PN_CODE, EP_CODE, MOV_CODE
								from		PER_POSITIONHIS 
								where		PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			}elseif($DPISDB=="oci8"){	
				$BEFORE_MOVDATE = " SUBSTR(POH_EFFECTIVEDATE,1,10)	= '$PER_POSDATE' ";
				$cmd = "select 		POH_ID, SUBSTR(POH_EFFECTIVEDATE,1,10),SUBSTR(POH_ENDDATE,1,10), POH_ORG_TRANSFER, 
												LEVEL_NO, PL_CODE, PT_CODE, PN_CODE, EP_CODE, MOV_CODE
								from 		PER_POSITIONHIS
								where 	PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by 	SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc ";				 					 
			}elseif($DPISDB=="mysql"){
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_POSDATE' ";
				$cmd = " select 	POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
												PL_CODE, PT_CODE, PN_CODE, EP_CODE, MOV_CODE
								from		PER_POSITIONHIS 
								where		PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			} // end if
			$count_poh = $db_dpis3->send_cmd($cmd);
//				$db_dpis3->show_error();
//				echo "<br>$cmd<br>";
			$data2 = $db_dpis3->get_array();
			$OLD_ORG = trim($data2[POH_ORG_TRANSFER]);	
			$LEVEL_NO = trim($data2[LEVEL_NO]);		
			$PL_CODE = trim($data2[PL_CODE]);		
			$PT_CODE = trim($data2[PT_CODE]);
			$PN_CODE = trim($data2[PN_CODE]);		
			$EP_CODE = trim($data2[EP_CODE]);		
			$MOV_CODE = trim($data2[MOV_CODE]);

			if (!$LEVEL_NO) $LEVEL_NO = trim($data[LEVEL_NO]);		
			if (!$PL_CODE) $PL_CODE = trim($data[PL_CODE]);		
			if (!$MOV_CODE) $MOV_CODE = trim($data[MOV_CODE]);		

			$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$POSITION_TYPE = trim($data2[POSITION_TYPE]);
			$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
			
			if($search_per_type==1 || $search_per_type==5) {
				$cmd = "select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[PL_NAME]);
	
				$cmd = "select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PT_NAME = trim($data[PT_NAME]);

				if ($RPT_N)
					$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$POSITION_LEVEL" : "") . (trim($PM_NAME) ?")":"");
				else
					$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");

			} elseif($search_per_type==2) {
				$cmd = "select PN_NAME from PER_POS_NAME where PN_CODE='$PN_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[PN_NAME]);
			} elseif($search_per_type==3) {
				$cmd = "select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[EP_NAME]);
			} elseif($search_per_type==4) {
				$cmd = "select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[TP_NAME]);
			}

			$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data2 = $db_dpis3->get_array();
			$MOV_NAME = $data2[MOV_NAME];
			
			if ($count_poh || ($PER_POSDATE == $PER_RETIREDATE)) {
				$PER_POSDATE = show_date_format($PER_POSDATE, $DATE_DISPLAY);

				$data_count++;
				$person_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][order] = $person_count .". ";
				$arr_content[$data_count][prename] = $PN_NAME;
				$arr_content[$data_count][name] = $PER_NAME ." ". $PER_SURNAME;
				$arr_content[$data_count][position] = $PL_NAME;
				$arr_content[$data_count][levelname] = $LEVEL_NAME; 
				$arr_content[$data_count][educate_use] = $EDU_USE;
				$arr_content[$data_count][educate_max] =  $EDU_MAX;
				$arr_content[$data_count][birthdate]	= $PER_BIRTHDATE;
				$arr_content[$data_count][startdate]	= $PER_STARTDATE;
				$arr_content[$data_count][posdate] =  $PER_POSDATE;
				$arr_content[$data_count][old_org] = $OLD_ORG;	 //ส่วนราชการเดิมก่อนโอน
				$arr_content[$data_count][reason] = $MOV_NAME;
			}
			
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $ORG_NAME, $ORG_NAME_1, $ORG_NAME_2;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($ORG_NAME){
						if($select_org_structure==0){
							$arr_addition_condition[] = "(e.POH_ORG3 = '$ORG_NAME')";
						}else  if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						} 
					} 
				break;
				case "ORG_1" :
					if($ORG_NAME_1){
						if($select_org_structure==0){
							$arr_addition_condition[] = "(e.POH_UNDER_ORG1 = '$ORG_NAME_1')";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_1)";
						}
					}
				break;
				case "ORG_2" :
					if($ORG_NAME_2){
						if($select_org_structure==0){
							$arr_addition_condition[] = "(e.POH_UNDER_ORG2 = '$ORG_NAME_2')";
						}else  if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_2)";
						}
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order, $arr_mov_code;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :
					$ORG_ID_1 = -1;
				break;
				case "ORG_2" :
					$ORG_ID_2 = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//แสดงรายชื่อหน่วยงาน
	if(count($arr_mov_code)) $mov_code = " and trim(e.MOV_CODE) in (". implode(" , ", $arr_mov_code) . ")";
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e
						 where			$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=e.PER_ID(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											$search_condition
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1){
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "<br>$cmd<br>";
	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			if($ORG_ID != $data[ORG_ID]){
				$ORG_ID = $data[ORG_ID];
				if($ORG_ID == ""){
					$ORG_NAME = "";
				}else{
					if($select_org_structure==0) {
						$ORG_NAME = $ORG_ID;
					} elseif($select_org_structure==1) {
						$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
						$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
						$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_NAME = $data2[ORG_NAME];
					}
				} // end if

				$addition_condition = generate_condition($rpt_order_index);

				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

				if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
				list_person($search_condition, $addition_condition);
				$data_count++;
			} // end if
		} // end for
		
		if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition);
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$pdf->AutoPageBreak = false; 
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$PRE_NAME = $arr_content[$data_count][prename];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL_NAME = $arr_content[$data_count][levelname];
			$EDU_USE = $arr_content[$data_count][educate_use];
			$EDU_MAX = $arr_content[$data_count][educate_max];
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			$STARTDATE = $arr_content[$data_count][startdate];
			$POSDATE = $arr_content[$data_count][posdate]; 
			$OLD_ORG = $arr_content[$data_count][old_org];
			$MOV_NAME = $arr_content[$data_count][reason];
			$REPORT_ORDER = $arr_content[$data_count][type];

			$arr_data = (array) null;
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER);
			$arr_data[] = $PRE_NAME;
			$arr_data[] = $NAME;
			$arr_data[] = $POSITION;
			$arr_data[] = $LEVEL_NAME;
			$arr_data[] = $EDU_USE;
			$arr_data[] =$EDU_MAX;
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE):$BIRTHDATE);
			if ($search_mov_code[0]==1 || $search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3 || 
				$search_mov_code[0]==4 || $search_mov_code[1]==4 || $search_mov_code[2]==4 || $search_mov_code[3]==4) {			
				$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE):$STARTDATE);
				$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($POSDATE):$POSDATE);
			} else {
			   $arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($POSDATE):$POSDATE);
				$arr_data[] =$OLD_ORG;
			}		
			$arr_data[] =$MOV_NAME;

			$data_align = array("C", "L", "L", "L", "L", "L","L", "C", "C","C", "L");
			
			$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "cordia", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		$pdf->add_data_tab("", 7, "RHBL", $data_align, "cordia", "12", "", "000000", "");		// เส้นปิดบรรทัด				
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>