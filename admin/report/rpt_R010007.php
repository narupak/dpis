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

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	
//--------------------------------------------------------------------------------------------------------
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

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
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);

	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID_REF";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

//				$heading_name .= " สายงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "d.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "d.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";
		}else{
			if($select_org_structure==0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		}
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_TYPE", $list_type)){	//จะต้องมีค่าส่งมาถึงจะสร้างรายงานได้
		$list_type_text = "";
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " $PROVINCE_NAME";
		} // end if

		if($search_pt_name){ $list_type_text .=" ตำแหน่งประเภท : $search_pt_name"; }
		if($search_per_type==1){
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= " / $PL_TITLE : $search_pl_name";
			}
		}elseif($search_per_type==2){
			if(trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
				$list_type_text .= " / $POS_EMP_TITLE : $search_pn_name";
			}
		}elseif($search_per_type==3){
			if(trim($search_ep_code)){
				$search_ep_code = trim($search_ep_code);
				$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
				$list_type_text .= " / $POS_EMPSER_TITLE : $search_ep_name";
			}
		} // end if
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(a.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "จำนวน$PERSON_TYPE[$search_per_type] แยกตามเพศ $heading_name";
	$report_title .= " ประจำปีงบประมาณ  พ.ศ. $search_year";            
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "H07";
	include ("rpt_R010007_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R010007_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		if ($sum_w > 200) {
			$orientation='L';
	//		$paper_w = 287;	// ขนาดความกว้างของกระดาษ A4 แนว Landscape
	//		$ratio = 287 / $sum_w;
		} else {
			$orientation='P';
	//		$paper_w = 200;
	//		$ratio = 200 / $sum_w;
		}
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
		
	//	echo  "sum_total_w=$sum_total_w<br>";
		if ($sum_total_w > 200) {
			$orientation='L';
			$paper_w = 287;	// ขนาดความกว้างของกระดาษ A4 แนว Landscape
			$ratio = 287 / $sum_total_w;
		} else {
			$orientation='P';
			$paper_w = 200;
			$ratio = 200 / $sum_total_w;
		}

		for($i=0; $i<count($heading_width); $i++) {
			$heading_width[$i] = (string)((int)$heading_width[$i]*$ratio);
		}
	//	echo "head w:".(implode(",",$heading_width))." (ratio:$ratio) ($sum_total_w)<br>";

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	function count_person($level_no,$gender, $search_condition, $addition_condition){ 
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type, $select_org_structure;
	
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
		
		if($gender=="ชาย"){				$search_condition .= (trim($search_condition)?" and ":" where ") . " (b.PER_GENDER=1) ";		}
		elseif($gender=="หญิง"){		$search_condition .= (trim($search_condition)?" and ":" where ") . " (b.PER_GENDER=2) ";		}
		elseif($gender=="รวม"){		$search_condition .= (trim($search_condition)?" and ":" where ") . " (b.PER_GENDER=1 or b.PER_GENDER=2)";		}

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);

		//หาจำนวน
		if($search_per_type==1){
			$pos_tb="PER_POSITION";
			$join_tb="a.POS_ID=b.POS_ID";
		}elseif($search_per_type==2){	
			$pos_tb="PER_POS_EMP";
			$join_tb="a.POEM_ID=b.POEM_ID";
		}elseif($search_per_type==3){ 
			$pos_tb="PER_POS_EMPSER";
			$join_tb="a.POEMS_ID=b.POEMS_ID";
		}

		if($DPISDB=="odbc"){
			$cmd = " select			count(b.PER_ID) as count_person
							from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													)	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
								 where		(b.LEVEL_NO='$level_no') 
													$search_condition
							 group by		b.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		count(b.PER_ID) as count_person
							from			$pos_tb a, PER_PERSONAL b,PER_ORG c, PER_ORG d
							where		$join_tb(+) and	a.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=d.ORG_ID(+) 
							and 			(b.LEVEL_NO='$level_no') 
												$search_condition
							group by		b.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(b.PER_ID) as count_person
							from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													)	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
								 where		(b.LEVEL_NO='$level_no') 
													$search_condition
							 group by		b.PER_ID ";
		} // end if

		if($select_org_structure==1){ 
			$cmd = str_replace("a.ORG_ID", "b.ORG_ID", $cmd);
			 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
		//echo "<br>$cmd<br>";
		//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if
		return $count_person;
	} // function

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
		
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
				case "LINE" :
					$PL_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//หาชื่อส่วนราชการ 
	if($search_per_type==1){
		$pos_tb="PER_POSITION";
		$join_tb="a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){	
		$pos_tb="PER_POS_EMP";
		$join_tb="a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		$pos_tb="PER_POS_EMPSER";
		$join_tb="a.POEMS_ID=b.POEMS_ID";
	}
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
							 from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													) 	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
												$search_condition
							 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
							 from			$pos_tb a,PER_PERSONAL  b, PER_ORG c, PER_ORG  d
							 where		$join_tb(+) and a.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=d.ORG_ID(+)
												$search_condition
							 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
							 from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													) 	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
												$search_condition
							 order by		$order_by ";
	}
	if($select_org_structure==1){
		 $cmd = str_replace("a.ORG_ID", "b.ORG_ID", $cmd);	
		 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
// echo "<br>$cmd<br>";
//	$db_dpis->show_error();

	$data_count = 0;
	for($k=0;$k<count($ARR_pt_name); $k++){
		$pt_name = $ARR_pt_name[$k];
		$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
		for($i=0;$i<$count_position_type; $i++){
			$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
			$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
			for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
				$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
				
				if($tmp_gender=="ชาย" || $tmp_gender=="ช"){$label_gender="M"; }
				elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	$label_gender="F"; }
				elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){$label_gender="S"; }
				
				$LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] = 0;
				$LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] = 0;
				$LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] = 0;
			}//end for
		}//end for
	} // end for $k
	initialize_parameter(0);
	$first_order = 1;	// order ที  0 = COUNTRY ยังไม่ได้คำนวณ เริ่ม order ที่ 1 (MINISTRY) ก่อน
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
				//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							$MINISTRY_SHORT = $data2[ORG_SHORT];
				
					//if($f_all){
//					echo ""DEPARTMENT_ID=$DEPARTMENT_ID (".(!$DEPARTMENT_ID)."), DEPARTMENT_NAME=$DEPARTMENT_NAME (".($DEPARTMENT_NAME == "").")<br>";
					if((($MINISTRY_NAME !="" && $MINISTRY_NAME !="-") || ($BKK_FLAG==1 && $MINISTRY_NAME !="" && $MINISTRY_NAME !="-")) && $DEPARTMENT_NAME==""){
						$addition_condition = generate_condition($rpt_order_index);
				
						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
						$arr_content[$data_count][short_name] = $MINISTRY_SHORT;
				
						for($k=0;$k<count($ARR_pt_name); $k++){
							$pt_name = $ARR_pt_name[$k];
							$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
							for($i=0;$i<$count_position_type; $i++){
								$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
								$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
								for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
									$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
									//--set gender label	
									if($tmp_gender=="ชาย" || $tmp_gender=="ช"){$label_gender="M"; }
									elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	$label_gender="F";	}
									elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){$label_gender="S";	}
									//---------------------------------------------------------------------------
				
									$arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender] = count_person($tmp_level_no,$tmp_gender,$search_condition, $addition_condition);
									//รวมจำนวนทั้งหมดแยก			
									if($tmp_gender=="ชาย" || $tmp_gender=="ช"){	
										$arr_content[$data_count][total_m] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];	//รวมชาย
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดชาย
									}elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	
										$arr_content[$data_count][total_f] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมหญิง
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดหญิง
									 }elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){		
										$arr_content[$data_count][total_s] = ($arr_content[$data_count][total_m]+$arr_content[$data_count][total_f]);		//รวมทั้งหมด ช+ญ
									}
				
									//รวมจำนวนทั้งหมดจากคอลัมน์  แถวแรกจนแถวสุดท้าย
									if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
								} // end for
							} // end for
						} // end for $k
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
							} // end if(($MINISTRY_NAME !="" && $MINISTRY_NAME !="-") || ($BKK_FLAG==1 && $MINISTRY_NAME !="" && $MINISTRY_NAME !="-"))
						//} // end if ($f_all)
					} // end if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1)
				} // end if
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
							$DEPARTMENT_ORG_SHORT = $data2[ORG_SHORT];

							if(($DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-") || ($BKK_FLAG==1 && $DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
							
								$arr_content[$data_count][type] = "DEPARTMENT";
								$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)).$DEPARTMENT_NAME;
								$arr_content[$data_count][short_name] = $DEPARTMENT_ORG_SHORT;
							
								for($k=0;$k<count($ARR_pt_name); $k++){
									$pt_name = $ARR_pt_name[$k];
									$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
									for($i=0;$i<$count_position_type; $i++){
										$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
										$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
										for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
											$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
											//--set gender label	
											if($tmp_gender=="ชาย" || $tmp_gender=="ช"){$label_gender="M"; }
											elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	$label_gender="F";	}
											elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){$label_gender="S";	}
											//---------------------------------------------------------------------------
							
											$arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender] = count_person($tmp_level_no,$tmp_gender,$search_condition, $addition_condition);
											//รวมจำนวนทั้งหมดแยก			
											if($tmp_gender=="ชาย" || $tmp_gender=="ช"){	
												$arr_content[$data_count][total_m] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];	//รวมชาย
												if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดชาย
											}elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	
												$arr_content[$data_count][total_f] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมหญิง
												if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดหญิง
											 }elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){		
												$arr_content[$data_count][total_s] = ($arr_content[$data_count][total_m]+$arr_content[$data_count][total_f]);		//รวมทั้งหมด ช+ญ
											}
							
											//รวมจำนวนทั้งหมดจากคอลัมน์  แถวแรกจนแถวสุดท้าย
											if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
										} // end for
									} // end for
								} // end for $k
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
									} // end if(($DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-") || ($BKK_FLAG==1 && $DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-"))
								} // end if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1)
							} // end if
				break;
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;

					if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][short_name] = $ORG_SHORT;

						for($k=0;$k<count($ARR_pt_name); $k++){
							$pt_name = $ARR_pt_name[$k];
							$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
							for($i=0;$i<$count_position_type; $i++){
								$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
								$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
								for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
									$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
									//--set gender label	
									if($tmp_gender=="ชาย" || $tmp_gender=="ช"){$label_gender="M"; }
									elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	$label_gender="F";	}
									elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){$label_gender="S";	}
									//---------------------------------------------------------------------------
	
									$arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender] = count_person($tmp_level_no,$tmp_gender,$search_condition, $addition_condition);
									//รวมจำนวนทั้งหมดแยก			
									if($tmp_gender=="ชาย" || $tmp_gender=="ช"){	
										$arr_content[$data_count][total_m] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];	//รวมชาย
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดชาย
									}elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	
										$arr_content[$data_count][total_f] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมหญิง
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดหญิง
									 }elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){		
										$arr_content[$data_count][total_s] = ($arr_content[$data_count][total_m]+$arr_content[$data_count][total_f]);		//รวมทั้งหมด ช+ญ
									}
	
									//รวมจำนวนทั้งหมดจากคอลัมน์  แถวแรกจนแถวสุดท้าย
									if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
								} // end for
							} // end for
						} // end for $k
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
						} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
					} // end if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1)
				} // end if
				break;
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";

						for($k=0;$k<count($ARR_pt_name); $k++){
							$pt_name = $ARR_pt_name[$k];
							$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
							for($i=0;$i<$count_position_type; $i++){
								$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
								$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
								for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
									$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
									//--set gender label	
									if($tmp_gender=="ชาย" || $tmp_gender=="ช"){				$label_gender="M"; }
									elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	$label_gender="F";	}
									elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){	 	$label_gender="S";	}
									//---------------------------------------------------------------------------
	
									$arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender] = count_person($tmp_level_no,$tmp_gender,$search_condition, $addition_condition);
									//รวมจำนวนทั้งหมดแยก			
									if($tmp_gender=="ชาย" || $tmp_gender=="ช"){	
										$arr_content[$data_count][total_m] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];	//รวมชาย
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_M[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดชาย
									}elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	
										$arr_content[$data_count][total_f] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมหญิง
										if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL_F[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];		//รวมทั้งหมดหญิง
									 }elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){		
										$arr_content[$data_count][total_s] = ($arr_content[$data_count][total_m]+$arr_content[$data_count][total_f]);		//รวมทั้งหมด ช+ญ
									}
	
									//รวมจำนวนทั้งหมดจากคอลัมน์  แถวแรกจนแถวสุดท้าย
									if($rpt_order_index==$first_order) $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender] += $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
								} // end for
							} // end for
						} // end for $k
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	$GRAND_TOTAL = array_sum($LEVEL_GRAND_TOTAL);
	$GRAND_TOTAL_M = array_sum($LEVEL_GRAND_TOTAL_M);
	$GRAND_TOTAL_F = array_sum($LEVEL_GRAND_TOTAL_F);
	$GRAND_TOTAL_S = ($GRAND_TOTAL_M+$GRAND_TOTAL_F);

if($export_type=="report"){	
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
			$RTF->paragraph(); 
				
		//	echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

		//รายละเอียดเนื้อหาทั้งหมด
//		echo "1..".count($arr_content)."<br>";
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];						//ส่วนราชการ
	
			for($k=0;$k<count($ARR_pt_name); $k++){
				$pt_name = $ARR_pt_name[$k];
				$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
				for($i=0;$i<$count_position_type; $i++){
					$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
					$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
					for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
						$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
						//--set gender label
						if($tmp_gender=="ชาย" || $tmp_gender=="ช"){$label_gender="M"; }
						elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	$label_gender="F";  }
						elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){$label_gender="S";  }
						//-----------------------------------------------------------------------------
						${"COUNT_".$tmp_level_no."_".$label_gender} = $arr_content[$data_count]["count_".$tmp_level_no."_".$label_gender];
					} //end for
				} //end for
			} // end for $k
//			$COUNT_TOTAL = $arr_content[$data_count][total];
			$COUNT_TOTAL_M = $arr_content[$data_count][total_m];
			$COUNT_TOTAL_F = $arr_content[$data_count][total_f];
			$COUNT_TOTAL_S= $arr_content[$data_count][total_s];

			$arr_data = (array) null;
			$arr_data[] ="$NAME";
			for($k=0;$k<count($ARR_pt_name); $k++){
				$pt_name = $ARR_pt_name[$k];
				$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
				for($i=0;$i<$count_position_type; $i++){
					$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
					$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
					for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
						$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
						//--set gender label
						if($tmp_gender=="ชาย" || $tmp_gender=="ช"){	$label_gender="M"; }
						elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	$label_gender="F"; }
						elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){		$label_gender="S"; }
						//-----------------------------------------------------------------------------
						$arr_data[] = ${"COUNT_".$tmp_level_no."_".$label_gender};
					} //end for
				} //end for
			}	// end for $k
			$arr_data[] = $COUNT_TOTAL_M;
			$arr_data[] = $COUNT_TOTAL_F;
			$arr_data[] = $COUNT_TOTAL_S;
			
			if($REPORT_ORDER=="DEPARTMENT" || (array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1))
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			else
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		//$PERCENT_TOTAL = ($GRAND_TOTAL_S / $GRAND_TOTAL_S) * 100;

		$arr_data = (array) null;
		$arr_data[] ="รวม";
		for($k=0;$k<count($ARR_pt_name); $k++){
			$pt_name = $ARR_pt_name[$k];
			$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
			for($i=0;$i<$count_position_type; $i++){
				$tmp_position_type = $ARR_POSITION_TYPE[$pt_name][$i];
				$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
				for($j=0;$j<count($ARR_GENDER[$pt_name][$i]);$j++) {
					$tmp_gender = $ARR_GENDER[$pt_name][$i][$j];	
					//--set gender label
					if($tmp_gender=="ชาย" || $tmp_gender=="ช"){	$label_gender="M"; }
					elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	$label_gender="F"; }
					elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){		$label_gender="S"; }
					//-----------------------------------------------------------------------------
					$arr_data[] = $LEVEL_GRAND_TOTAL[$tmp_level_no."_".$label_gender];
				} //end for
			} //end for
		}	// end for $k
		$arr_data[] = $GRAND_TOTAL_M;
		$arr_data[] = $GRAND_TOTAL_F;
		$arr_data[] = $GRAND_TOTAL_S;
		
		if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");		//TRHBL
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
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
}else if($export_type=="graph"){

	$arr_content_map = (array) null;
	$arr_series_caption = (array) null;
	
	$arr_content_map[] = "name";
	$arr_series_caption[] = "";
	for($k=0;$k<count($ARR_pt_name);$k++) {
		$count_position_type=count($ARR_POSITION_TYPE[$ARR_pt_name[$k]]);
		for($i=0;$i<$count_position_type;$i++) {
			$tmp_position_type=$ARR_POSITION_TYPE[$ARR_pt_name[$k]][$i];
			$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
			$count=count($ARR_GENDER[$ARR_pt_name[$k]][$i]);
			for($j=0;$j<$count;$j++) {
				$tmp_gender = $ARR_GENDER[$ARR_pt_name[$k]][$i][$j];
				if($tmp_gender=="ชาย" || $tmp_gender=="ช"){$label_gender="M"; }
				elseif($tmp_gender=="หญิง" || $tmp_gender=="ญ"){	$label_gender="F";	}
				elseif($tmp_gender=="รวม" || $tmp_gender=="ร"){$label_gender="S";	}
				$arr_content_map[] = "count_".$tmp_level_no."_".$label_gender;
				$arr_series_caption[] = $ARR_pt_name[$k]."-".$tmp_position_type."-".$label_gender;
			}
		}
	}
	$arr_content_map[] = "total_m";
	$arr_content_map[] = "total_f";
	$arr_content_map[] = "total_s";
	$arr_series_caption[] = "รวม-ชาย";
	$arr_series_caption[] = "รวม-หญิง";
	$arr_series_caption[] = "รวมทั้งหมด";

	$arr_series_caption[] = "";
	$arr_content_key = array_keys($arr_content[0]); //print_r($arr_content_key);
	$arr_categories = array();
	$arr_series_caption_list = array(); 
	$f_first = true;
//	echo "count (arr_content):".count($arr_content)."<br>";
	for($i=0;$i<count($arr_content);$i++){
//		echo "arr_content ($i):".$arr_content[$i][name]."<br>";
		$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
		$cntseq=0;
		for($j=0; $j<count($arr_content_map); $j++){ 
//			echo "level $j:";
			if ($arr_column_sel[$arr_column_map[$j]]==1 && strpos($arr_content_map[$arr_column_map[$j]],"count_")!==false) {
				$arr_series_caption_data[$cntseq][] = $arr_content[$i][$arr_content_map[$arr_column_map[$j]]];
//				echo "-->map:".$arr_content_map[$arr_column_map[$j]]." data=".$arr_content[$i][$arr_content_map[$arr_column_map[$j]]]."";
				if ($f_first) $arr_series_caption_list[] = $arr_series_caption[$arr_column_map[$j]];
				$cntseq++;
			}
//			echo "<br>";
		} // end for $j
		$f_first=false;	// check สำหรับรอบแรกเท่านั้น
	} // end for $i
	$series_caption_list = implode(";",$arr_series_caption_list);
//	echo "count (arr_series_caption_data)=".count($arr_series_caption_data)."<br>";
	for($j=0;$j<count($arr_series_caption_data);$j++){
		$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]);
	}

	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
	if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
	$selectedFormat = "SWF";
//	$series_caption_list = "ชาย;หญิง"; 
//	$categories_list = implode(";", $arr_categories).";รวมทั้งหมด";
	$categories_list = implode(";", $arr_categories)."";
//	echo "categories_list=$categories_list<br>";
	if(strtolower($graph_type)=="pie"){
		$series_list = $GRAND_TOTAL_M.";".$GRAND_TOTAL_F;	//???
	}else{
		$series_list = implode("|", $arr_series_list);
	}

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
		} //end switch
 } //end if
?>