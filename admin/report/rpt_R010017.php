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
	
	$IMG_PATH = "../../attachment/pic_personal/";	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	include ("rpt_R010017_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($HISTORY_LIST)){ 
		$HISTORY_LIST="ORG|SPECIALSKILLHIS"; //default
	} // end if
	//$arr_rpt_order = explode("|", $RPTORD_LIST);
	$arr_history_name = explode("|", $HISTORY_LIST);
	
	//echo "<pre>".print_r($arr_history_name)."</pre>";

	//print_r($arr_rpt_order);
	//แยกตามเงื่อนไขที่เลือก
	$select_list = "";		$order_by = "";		$heading_name = "";
	//for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		//$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		//switch($REPORT_ORDER){
	for($history_index=0; $history_index<count($arr_history_name); $history_index++){
		$HISTORY_NAME = $arr_history_name[$history_index];
		switch($HISTORY_NAME){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";
				
				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "d.ORG_SEQ_NO, d.ORG_CODE, c.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";

//				if($order_by) $order_by .= ", ";
//				if($select_org_structure == 0) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, c.ORG_ID";
//				else if($select_org_structure == 1) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";

//				$heading_name .= " $ORG_TITLE";
				break;
			case "EDUCATE" :
				$heading_name .= " ประวัติการศึกษา ";
				break;
			case "SPECIALSKILLHIS" :
				$heading_name .= " ความเชี่ยวชาญ";
				break;
		} // end switch case
	} // end for
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "e.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";
		}else{
			if($select_org_structure == 0) $select_list = "d.ORG_SEQ_NO, d.ORG_CODE, c.ORG_ID";
			else if($select_org_structure == 1) $select_list = "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";
		}
	} // end if
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "e.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";
		}else{
			if($select_org_structure == 0) $order_by = "d.ORG_SEQ_NO, d.ORG_CODE, c.ORG_ID";
			else if($select_org_structure == 1)  $order_by = "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";
		}
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE=$search_per_type)";

	$list_type_text = $ALL_REPORT_TITLE;
	
  	if($MINISTRY_ID){
		/****
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
		***/
		$arr_search_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID) ";
		$list_type_text .= " $MINISTRY_NAME";
	}
	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " $DEPARTMENT_NAME";
	}
	if($PROVINCE_CODE){
		/***
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
		***/
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if

//		if(!trim($select_org_structure)){
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){
			 if($select_org_structure == 0) $arr_search_condition[] = "(c.ORG_ID = $search_org_id or d.ORG_ID = $search_org_id)";	
			 else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID = $search_org_id or a.ORG_ID = $search_org_id)";
			 $list_type_text .= " $search_org_name";
		}
		if(trim($search_org_id_1)){
			 if($select_org_structure == 0) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1 or d.ORG_ID_1 = $search_org_id_1)";	
			else  if($select_org_structure == 1) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1 or a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " $search_org_name_1";
		}
		if(trim($search_org_id_2)){
			 if($select_org_structure == 0) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2 or d.ORG_ID_2 = $search_org_id_2)";
			else  if($select_org_structure == 1) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2 or a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " $search_org_name_2";
		}
//	}

	if(in_array("SELECT", $list_type) ){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}
	if(in_array("CONDITION", $list_type) ){ //ระบุเงื่อนไข
		$search_level_minname = $search_level_maxname ="";
//		if(trim($search_pos_no)) $arr_search_condition[] = "(c.POS_NO like '$search_pos_no%' or d.POEM_NO like '$search_pos_no%')";
		if(trim($search_pos_no)) $arr_search_condition[] = "(trim(c.POS_NO)='$search_pos_no' or trim(d.POEM_NO)='$search_pos_no')";
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
		
			$cmd = " select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$search_min_level' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$F_POSITION_TYPE = $data[POSITION_TYPE];
			$F_LEVEL_NAME =  $data[POSITION_LEVEL];
			if($F_POSITION_TYPE && $F_LEVEL_NAME){ $search_level_minname="$F_POSITION_TYPE $F_LEVEL_NAME"; }
		}
		if(trim($search_max_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			
			$cmd = " select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$search_max_level' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$T_POSITION_TYPE = $data[POSITION_TYPE];
			$T_LEVEL_NAME =  $data[POSITION_LEVEL];
			if($T_POSITION_TYPE && $T_LEVEL_NAME){ 
				if($search_level_minname){	$search_level_maxname=" ถึง"; }
				$search_level_maxname.="$T_POSITION_TYPE $T_LEVEL_NAME"; 
			}
		}
		if(trim($search_level_minname) || trim($search_level_maxname)){
			$search_level_name = $search_level_minname.$search_level_maxname;
		}
		if(trim($search_pl_code)){
			$arr_search_condition[] = "(c.PL_CODE ='$search_pl_code')"; 	
			$search_pl_name = $search_pl_name; 
		}
	} // end if
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";

	//เลือกเฉพาะสายงาน นักบริหาร และนักปกครอง <default>
	if(!trim($search_pl_code)){	
		if ($BKK_FLAG==1)
			$arr_search_condition[] = "(c.PL_CODE in ('10109','11001','31004'))";
		else
			$arr_search_condition[] = "(c.PL_CODE in ('010108','010307','510108','510307'))";
		$search_pl_name = "นักบริหาร และนักปกครอง"; 
	}
	//เลือกเฉพาะบริหารต้น และบริหารสูง <default>
	if(!trim($search_level_name)){
		$arr_search_condition[] = "(trim(a.LEVEL_NO) in ('M1','M2'))";
		$search_level_name = "บริหารต้น และบริหารสูง"; 
	}
	//-----------------------------------------------------------------------------------------------	
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "ข้อมูลสายงาน$search_pl_name ตำแหน่งประเภท$search_level_name แสดงวุฒิการศึกษาและความเชี่ยวชาญพิเศษ";
	$report_code = "H17";

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

		$fname= "rpt_R010017_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

		$RTF->set_default_font($font, 14);
//		echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$orientation='P';

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
	}

	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width[EDUCATE]);
		for($h = 0; $h < count($heading_width[EDUCATE]); $h++) {
			$heading_width[EDUCATE][$h] = $heading_width[EDUCATE][$h] / $sum_w * 100;
		}		
		$sum_w = array_sum($heading_width[SPECIALSKILLHIS]);
		for($h = 0; $h < count($heading_width[SPECIALSKILLHIS]); $h++) {
			$heading_width[SPECIALSKILLHIS][$h] = $heading_width[SPECIALSKILLHIS][$h] / $sum_w * 100;
		}		
	}
/*
	if ($FLAG_RTF) {
		function print_header(){
			global $RTF, $heading_width;

//			$RTF->new_page();	
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."ส่วนราชการ / ชื่อ-สกุล".$RTF->bold(0), $heading_width[0], "center", "25", "TBLR");	
				$RTF->cell($RTF->bold(1)."ตำแหน่ง".$RTF->bold(0), $heading_width[1], "center", "25", "TBLR");	
				$RTF->cell($RTF->bold(1)."ระดับวุฒิ การศึกษา".$RTF->bold(0), $heading_width[2], "center", "25", "TBLR");	
				$RTF->cell($RTF->bold(1)."ชื่อวุฒิ การศึกษา".$RTF->bold(0), $heading_width[3], "center", "25", "TBLR");	
				$RTF->cell($RTF->bold(1)."สาขาวิชาเอก".$RTF->bold(0), $heading_width[4], "center", "25", "TBLR");	
				$RTF->cell($RTF->bold(1)."สถาบัน การศึกษา".$RTF->bold(0), $heading_width[5], "center", "25", "TBLR");	
				$RTF->cell($RTF->bold(1)."ประเทศ".$RTF->bold(0), $heading_width[6], "center", "25", "TBLR");	
				$RTF->cell($RTF->bold(1)."วันเกษียณ (1 ต.ค.)".$RTF->bold(0), $heading_width[7], "center", "25", "TBLR");	
				$RTF->close_line();
		} // function
	}
*/
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(c.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(c.ORG_ID = 0 or c.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
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
		$select_list.=",c.POS_ID,c.POS_NO,c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2";
		$pos_tb="PER_POSITION";
		$join_tb="a.POS_ID=c.POS_ID";
	}elseif($search_per_type==2){	
		$select_list.=",c.POEM_ID,c.POEM_NO as EMP_POS_NO, c.PN_CODE as EMP_PL_CODE,c.ORG_ID as EMP_ORG_ID, c.ORG_ID_1 as EMP_ORG_ID_1, c.ORG_ID_2 as EMP_ORG_ID_2";
		$pos_tb="PER_POS_EMP";
		$join_tb="a.POEM_ID=c.POEM_ID";
	}elseif($search_per_type==3){ 
		$select_list.=",c.POEMS_ID,c.POEMS_NO as EMPSER_POS_NO, c.EP_CODE as EMPSER_PL_CODE,c.ORG_ID as EMPSER_ORG_ID, c.ORG_ID_1 as EMPSER_ORG_ID_1, c.ORG_ID_2 as EMPSER_ORG_ID_2";
		$pos_tb="PER_POS_EMPSER";
		$join_tb="a.POEMS_ID=c.POEMS_ID";
	}

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,  LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_TYPE, a.PER_CARDNO,
											$select_list
						 from			PER_PRENAME b inner join 
										(((( 	
										PER_PERSONAL a 
										left join $pos_tb c on ($join_tb) 
										) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
										) left join PER_MGT f on (c.PM_CODE=f.PM_CODE)
										) on (trim(a.PN_CODE)=trim(b.PN_CODE))
										$search_condition
						 order by	$order_by, f.PM_SEQ_NO,a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.LEVEL_NO, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
											SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,  
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_TYPE, a.PER_CARDNO,
											$select_list
						 from			PER_PERSONAL a, PER_PRENAME b, $pos_tb c,PER_ORG d,PER_ORG e, PER_MGT f
						 where		trim(a.PN_CODE)=trim(b.PN_CODE) and $join_tb(+)
											and c.ORG_ID=d.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and c.PM_CODE=f.PM_CODE(+)
											$search_condition
						 order by	$order_by, f.PM_SEQ_NO,NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY') ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,  LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_TYPE, a.PER_CARDNO,
											$select_list
						 from			PER_PRENAME b inner join 
										(((( 	
										PER_PERSONAL a 
										left join $pos_tb c on ($join_tb) 
										) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
										) left join PER_MGT f on (c.PM_CODE=f.PM_CODE)
										) on (trim(a.PN_CODE)=trim(b.PN_CODE))
										$search_condition
						 order by	$order_by, f.PM_SEQ_NO,a.PER_NAME, a.PER_SURNAME ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>cmd ::: $cmd ($count_data)<br>"; //no rows
	//$db_dpis->show_error();
//################################################

$first_order = 1;	// order ที  0 = COUNTRY ยังไม่ได้คำนวณ เริ่ม order ที่ 1 (MINISTRY) ก่อน
if($count_data){	
	$data_count = 0;
	initialize_parameter(0);

	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
	if ($FLAG_RTF) {
		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
		$RTF->paragraph();
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	} else {
		$pdf->AutoPageBreak = false;
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
	}
	if (!$result) echo "****** error ****** on open table for $table<br>";

	while($data = $db_dpis->get_array()){
		$data_count++;
		$PER_ID = $data[PER_ID];
		$PER_TYPE = $data[PER_TYPE];
	
		if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];

				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_ID = $data[POEM_ID];
				$POS_NO = $data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO = $data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			} 
			$ORG_NAME = "";
			if($ORG_ID){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_1 = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_2 = trim($data2[ORG_NAME]);
			}
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			//$LEVEL_NAME = trim(str_replace("ประเภท", "", $data2[LEVEL_NAME]));
			$LEVEL_NAME = trim($data2[LEVEL_NAME]);
			$POSITION_LEVEL = $data2[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
			
			if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4
			$img_file = file_exists($img_file) ? $img_file : "";
			
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";
			$PER_CARDNO = $data[PER_CARDNO];	
			
			//$img_file = "";
			//if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";			
/*
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
			if($PER_RETIREDATE){
				$arr_temp = explode("-", substr($PER_RETIREDATE, 0, 10));
				$PER_RETIREDATE = ($arr_temp[2] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if
*/
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
			} // end if

			//หาวันเกษียณอายุ
			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);

		for($history_index=0; $history_index<count($arr_history_name); $history_index++){
				$HISTORY_NAME = $arr_history_name[$history_index];
				switch($HISTORY_NAME){
					case "MINISTRY" :
							if($MINISTRY_ID != trim($data[MINISTRY_ID])){ 
								$MINISTRY_ID = trim($data[MINISTRY_ID]);
								if($MINISTRY_ID != ""){
									$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
									if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
									$db_dpis2->send_cmd($cmd);
		//							$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$MINISTRY_NAME = $data2[ORG_NAME];
								} // end if

							if ($FLAG_RTF) {
								$RTF->add_text_line($MINISTRY_NAME, 7, "", "L", "", "14", "b", 0, 0);
							} else {
								$pdf->add_text_line($MINISTRY_NAME, 7, "", "L", "", "14", "b", 0, 0, 10);
							}
						} // end if
					break;
					
					case "DEPARTMENT" :	
							if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){ //กรม
								$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
								if($DEPARTMENT_ID != ""){
									$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
									if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
									$db_dpis2->send_cmd($cmd);
		//							$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$DEPARTMENT_NAME = $data2[ORG_NAME];
								} // end if

							if ($FLAG_RTF) {
								$RTF->add_text_line($DEPARTMENT_NAME, 7, "", "L", "", "14", "b", 0, 0);
							} else {
								$pdf->add_text_line($DEPARTMENT_NAME, 7, "", "L", "", "14", "b", 0, 0, 10);
							}
//							echo "DEPARTMENT_NAME=$DEPARTMENT_NAME<br>";
						} // end if
				break;
	
				case "ORG" :
						if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != ""){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
							} // end if
	
							if ($FLAG_RTF) {
								$RTF->add_text_line($ORG_NAME, 7, "", "L", "", "14", "b", 0, 0);
							} else {
								$pdf->add_text_line($ORG_NAME, 7, "", "L", "", "14", "b", 0, 0, 10);
							}
//							echo "ORG_NAME=$ORG_NAME<br>";
						} // end if
				break;
			
				case "EDUCATE" :
							$border = "";
							$arr_data = (array) null;
							$arr_data[] = $data_count." ".$FULLNAME;	//	$NAME;
							$arr_data[] = (trim($PL_NAME)?($PL_NAME ." (". $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME).")";
/*	
								if($have_pic && $img_file){
									$fix_width = 50; 
									$RTF->open_line();
									$image_x = ($imgx + (($image_w+2) * $j) + 1);
									list($width, $height) = getimagesize($img_file); 
									$new_h = $height / $width * $fix_width;
									$new_ratio = floor(100 / $height * $fix_width); 
//									$fsnum = substr($RTF->_font_size(),3);
//									$w_chr = floor($fix_width / $fsnum);
									$w_chr = 7;
									$RTF->open_line();
									$RTF->set_font($font,14);
									$RTF->color("1");	// 0=DARKGRAY
//									$image_x = ($pdf->x + 170);		$image_y = ($pdf->y - 33);		$image_w = 25;			$image_h = 35;
//									$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
									$RTF->cellImage(($img_file ? $img_file : ""), "$new_ratio", "$w_chr", "left", "8", "");
									$RTF->close_line();
								} // end if
*/
							$EN_NAME = "";	$EM_NAME = "";	$INS_NAME = "";	$CT_NAME = "";
//							$where = " and a.EDU_TYPE like '%2%'";
//							if ($BKK_FLAG==1) $where = " and a.EL_CODE in ('001', '002', '003', '005') ";
//							else $where = " and a.EL_CODE in ('40', '50', '60', '70', '80', '90') ";
							$where = " and f.EL_TYPE > '1' ";
							if($DPISDB=="odbc"){
								$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, f.EL_NAME, a.EDU_INSTITUTE, a.CT_CODE_EDU
									 	from			((((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
												)left join PER_EDUCLEVEL f on (trim(a.EL_CODE)=trim(f.EL_CODE))
										 where		a.PER_ID=$PER_ID $where
										 order by		iif(isnull(a.EDU_SEQ),0,a.EDU_SEQ) desc, a.EDU_ENDYEAR desc ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, f.EL_NAME, a.EDU_INSTITUTE, a.CT_CODE_EDU
										from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e,PER_EDUCLEVEL f
										where		a.PER_ID=$PER_ID and
													a.EN_CODE=b.EN_CODE(+) and 
													a.EM_CODE=c.EM_CODE(+) and 
													trim(a.EL_CODE)=trim(f.EL_CODE(+)) and
													a.INS_CODE=d.INS_CODE(+) and 
													d.CT_CODE=e.CT_CODE(+) $where
										order by		to_number(nvl(a.EDU_SEQ,0)) desc, a.EDU_ENDYEAR desc ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, f.EL_NAME, a.EDU_INSTITUTE, a.CT_CODE_EDU
									 	from			((((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
												)left join PER_EDUCLEVEL f on (trim(a.EL_CODE)=trim(f.EL_CODE))
										 where		a.PER_ID=$PER_ID $where
										 order by		a.EDU_SEQ+0 desc, a.EDU_ENDYEAR desc ";			
							} // end if
							
							$count_educatehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_educatehis){
								$educatehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$educatehis_count++;
									$EN_NAME = trim($data2[EN_NAME]);
									$EM_NAME = trim($data2[EM_NAME]);
									$EL_NAME = trim($data2[EL_NAME]);
									$INS_NAME = trim($data2[INS_NAME]);
									$CT_NAME = trim($data2[CT_NAME]);
									if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
									if (!$CT_NAME) {
										$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
										$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
										$db_dpis1->send_cmd($cmd);
			//							$db_dpis1->show_error();
										$data1 = $db_dpis1->get_array();
										$CT_NAME = $data1[CT_NAME];
									}

									if ($educatehis_count==2) {	// ล้างค่าชื่อ ตำแหน่งให้ไม่ต้องแสดงในบรรทัดที่ 2 และต่อ ๆ ไป
										$arr_data = (array) null;
										$arr_data[] = "";		// สังกัด/ชื่อ
										$arr_data[] = "";		// ตำแหน่ง
									}
									$arr_data[] = $EL_NAME;	// ระดับวุฒิ|การศึกษา
									$arr_data[] = $EN_NAME;	// ชื่อวุฒิ|การศึกษา
									$arr_data[] = $EM_NAME;	// สาขาวิชาเอก|
									$arr_data[] = $INS_NAME;	// สถาบัน|การศึกษา
									$arr_data[] = $CT_NAME;	// ประเทศ
									$arr_data[] = $PER_RETIREDATE;	// ปีเกษียณ|(1 ต.ค.)
							
									$data_align = array("L", "L", "C", "C", "C", "C", "C", "C");
									$style = array("B", "B", "", "", "", "", "", "");
									$a_border = array("", "", "B", "B", "B", "B", "B", "B");
		
									if ($FLAG_RTF)
										$result = $RTF->add_data_tab($arr_data, 7, $a_border, $data_align, "", "14", $style, $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
									else
										$result = $pdf->add_data_tab($arr_data, 7, $a_border, $data_align, "", "14", $style, "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

								} // end while
							}else{
								if ($FLAG_RTF) {
									$RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
								} else {
									$pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0, 10);
								}
							} // end if
					break;
						
					case "SPECIALSKILLHIS" :  //ความเชี่ยวชาญพิเศษ
							$SS_NAME = "";
							$SPS_EMPHASIZE = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
												 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
												 where		a.PER_ID=$PER_ID and
																	a.SS_CODE=b.SS_CODE
												 order by		a.SPS_ID ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
												 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
												 where		a.PER_ID=$PER_ID and
																	a.SS_CODE=b.SS_CODE
												 order by		a.SPS_ID ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
												 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
												 where		a.PER_ID=$PER_ID and
																	a.SS_CODE=b.SS_CODE
												 order by		a.SPS_ID ";		
							} // end if
							$count_specialskillhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_specialskillhis){
								if ($FLAG_RTF)
									$RTF->add_text_line("ความเชี่ยวชาญพิเศษ", 7, "", "L", "", "14", "b", 0, 0);
								else
									$pdf->add_text_line("ความเชี่ยวชาญพิเศษ", 7, "", "L", "", "14", "b", 0, 0, 10);
								$specialskillhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$specialskillhis_count++;
									$SS_NAME = trim($data2[SS_NAME]);
									$SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
									$specialskillhis = "     ".$SS_NAME." ".$SPS_EMPHASIZE;
									
									$border = "";
									if ($FLAG_RTF)
										$RTF->add_text_line($specialskillhis, 7, "", "L", "", "14", "", 0, 0);
									else
										$pdf->add_text_line($specialskillhis, 7, "", "L", "", "14", "", 0, 0, 10);
								} // end while
							}else{
								if ($FLAG_RTF) {
	//								$RTF->new_page();
									$RTF->open_line();
									$RTF->set_font($font, 16);
									$RTF->color("0");	// 0='BLACK'
									$RTF->cell($RTF->bold(1)."********** ไม่มีข้อมูล **********".$RTF->bold(0), 100, "center", "25", "");	
									$RTF->close_line();
								} else {
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
								}
							} // end if
						break;
				} // end switch
			} // end for
			//if($data_count < $count_data) $pdf->AddPage();
		} // end while
		if ($FLAG_RTF) {
			$RTF->close_tab(); 
//			$RTF->close_section(); 
		} else {
			$pdf->close_tab(""); 
		}

	}else{
		if ($FLAG_RTF) {
//			$RTF->new_page();
			$RTF->open_line();
			$RTF->set_font($font, 16);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell($RTF->bold(1)."********** ไม่มีข้อมูล **********".$RTF->bold(0), 100, "center", "25", "");	
			$RTF->close_line();
		} else {
			$pdf->SetFont($font,'b',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		}
	} // end if

	if ($FLAG_RTF) {
		$RTF->display($fname);
	} else {
		$pdf->close();
		$pdf->Output();
	}
	ini_set("max_execution_time", 30);
?>