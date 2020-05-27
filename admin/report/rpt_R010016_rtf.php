<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_pt_code == 'O') $search_pt_name = "ทั่วไป";
	elseif($search_pt_code == 'K') $search_pt_name = "วิชาการ";
	elseif($search_pt_code == 'D') $search_pt_name = "อำนวยการ";
	elseif($search_pt_code == 'M') $search_pt_name = "บริหาร";

	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type)  && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type)  && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type)  && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "LEVEL"); 

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
				$select_list .= "h.ORG_SEQ_NO, h.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "h.ORG_SEQ_NO, h.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				elseif($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE";

				$heading_name .= " $PL_TITLE";
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO, g.LEVEL_NAME, g.LEVEL_SEQ_NO";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "g.LEVEL_SEQ_NO";

				$heading_name .= " $LEVEL_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		elseif($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	}
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		elseif($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	}

	$search_condition = "";	
	if(trim($search_end_date)){
//		$search_end_date =  save_date($search_end_date);
//		$show_end_date = show_date_format($search_end_date, 3);
		$arr_temp = explode("/", $search_end_date);
		
		$show_end_date = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];

		$search_end_date = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". ($arr_temp[0] + 0);
		
		$search_start_effective = ($arr_temp[2]-$search_pos_year-543) ."-01-01";
		$search_end_effective =  ($arr_temp[2]-543) ."-". $arr_temp[1] ."-". ($arr_temp[0] + 0);
	} // end if
	if(trim($search_pos_year)){ 
		if($DPISDB=="odbc") $search_effective = "and (LEFT(trim(h.POH_EFFECTIVEDATE), 10) >= '$search_start_effective' and LEFT(trim(h.POH_EFFECTIVEDATE), 10) <= '$search_end_effective')";
		elseif($DPISDB=="oci8") $search_effective = "and (SUBSTR(trim(h.POH_EFFECTIVEDATE), 1, 10) >= '$search_start_effective' and SUBSTR(trim(h.POH_EFFECTIVEDATE), 1, 10) <= '$search_end_effective')";
		elseif($DPISDB=="mysql") $search_effective = "and (LEFT(trim(h.POH_EFFECTIVEDATE), 10) >= '$search_start_effective' and LEFT(trim(h.POH_EFFECTIVEDATE), 10) <= '$search_end_effective')";
	} // end if
	$arr_search_condition[] = "(a.PER_TYPE = 1 and a.PER_STATUS =  1)";

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
	if(in_array("PER_ORG_TYPE_2", $list_type) ){
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
	if(in_array("PER_ORG", $list_type) ){
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure==1)  $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type) ){
		// สายงาน
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		} // end if
		elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(b.EP_CODE)='$search_ep_code')";
			$list_type_text .= "$search_ep_name";
		} // end if
	}
	if(in_array("PER_COUNTRY", $list_type) ){
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
		
		//ค้นหาจาก
		if(trim($search_pt_name)){
			//ประเภทตำแหน่ง
			$list_type_text .= " ตำแหน่งประเภท$search_pt_name";
			$arr_search_condition[] = "(g.LEVEL_NO LIKE '%$search_pt_code%')";
		}
		if(trim($search_pl_code)){	
			// สายงาน
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= " ตำแหน่งในสายงาน$search_pl_name";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	include ("rpt_R010016_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R010016_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการ";
	if(trim($search_pt_name)){	$report_title .= "ตำแหน่งประเภท$search_pt_name"; }
	$report_title .= " ที่ดำรงตำแหน่งครบ ". (trim($search_pos_year)?"$search_pos_year ปี":"") ." นับถึงวันที่ ($show_end_date)";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "H16";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis4;
		global $arr_rpt_order, $rpt_order_index, $arr_content, $data_count, $person_count, $month_abbr;
		global $search_effective,$search_pos_year, $select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		// รายชื่อตำแหน่งที่ดำรงตำแหน่งครบจำนวนปีที่เลือกมา			
		if($DPISDB=="odbc"){
			$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, LEFT(trim(b.POS_CHANGE_DATE), 10) as POS_CHANGE_DATE,
												b.PL_CODE, b.PM_CODE, b.PT_CODE, b.ORG_ID_1, c.ORG_NAME, d.PN_NAME, e.PL_NAME, f.PT_NAME, g.LEVEL_NAME, g.POSITION_LEVEL
							 from		(
												(
													(
														(
															(
																(
																PER_PERSONAL a
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID)											
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_LINE e on (b.PL_CODE=e.PL_CODE)
												) left join PER_TYPE f on (b.PT_CODE=f.PT_CODE)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
										) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
							 $search_condition
							 order by		IIf(IsNull(b.POS_NO), 0, CInt(b.POS_NO)), b.PL_CODE, g.LEVEL_SEQ_NO desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 	
												SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE, SUBSTR(trim(b.POS_CHANGE_DATE), 1, 10) as POS_CHANGE_DATE, 
												b.PL_CODE, b.PM_CODE, b.PT_CODE, b.ORG_ID_1, c.ORG_NAME, d.PN_NAME, e.PL_NAME, f.PT_NAME, g.LEVEL_NAME, g.POSITION_LEVEL
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME d, PER_LINE e, PER_TYPE f, PER_LEVEL g, PER_ORG h
							 where		a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) and b.PL_CODE=e.PL_CODE(+) and 
												b.PT_CODE=f.PT_CODE(+) and a.LEVEL_NO=g.LEVEL_NO(+) and b.DEPARTMENT_ID=h.ORG_ID(+)
												$search_condition
							 order by		TO_NUMBER(b.POS_NO), b.PL_CODE, g.LEVEL_SEQ_NO desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, LEFT(trim(b.POS_CHANGE_DATE), 10) as POS_CHANGE_DATE,
												b.PL_CODE, b.PM_CODE, b.PT_CODE, b.ORG_ID_1, c.ORG_NAME, d.PN_NAME, e.PL_NAME, f.PT_NAME, g.LEVEL_NAME, g.POSITION_LEVEL
							 from		(
												(
													(
														(
															(
																(
																PER_PERSONAL a
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID)											
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_LINE e on (b.PL_CODE=e.PL_CODE)
												) left join PER_TYPE f on (b.PT_CODE=f.PT_CODE)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
										) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
							 $search_condition
							 order by		b.POS_NO, b.PL_CODE, g.LEVEL_SEQ_NO desc ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo "<br>$cmd<br>";
		while($data = $db_dpis2->get_array()){
				$PER_ID = trim($data[PER_ID]);
				$PN_NAME = trim($data[PN_NAME]);
				$PER_NAME = trim($data[PER_NAME]);
				$PER_SURNAME = trim($data[PER_SURNAME]);
				$POS_NO = $data[POS_NO];
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$LEVEL_NAME = trim($data[LEVEL_NAME]);
				$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
				$PL_NAME =trim($data[PL_NAME]);			
				$PM_CODE = trim($data[PM_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$PT_NAME =trim($data[PT_NAME]);			
				$ORG_NAME = $data[ORG_NAME];

				$ORG_ID_1 = $data[ORG_ID_1];
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_1 = $data2[ORG_NAME];

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

				$PL_NAME = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME ) . (trim($PM_NAME)?")":"");

				$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
				
				//หาวันเกษียณอายุ
				$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
				
				//วันที่ดำรงตำแหน่ง
				$PER_CHANGE_DATE = show_date_format($data[POS_CHANGE_DATE],$DATE_DISPLAY);

				//หาวันดำรงตน.ปัจจุบัน และ วันครบกำหนด
				$cmd = " select MAX(h.POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS h where (h.PER_ID=$PER_ID) and LEVEL_NO='$LEVEL_NO' $search_effective";
				//echo "$cmd<br><br>";
				$count3=$db_dpis4->send_cmd($cmd);
				$data2= $db_dpis4->get_array();
				
				$POS_EFFECTIVEDATE = trim($data2[EFFECTIVEDATE]);
				if(trim($POS_EFFECTIVEDATE)){	
					$arr_temp = explode("-", $POS_EFFECTIVEDATE);
					//วันครบกำหนดจำนวนปีที่เลือก
					$POS_UNTIL = ($arr_temp[0]+$search_pos_year)."-".$arr_temp[1]."-".$arr_temp[2];
					$POS_UNTIL = show_date_format($POS_UNTIL,$DATE_DISPLAY);

					$POS_CHANGE_DATE = show_date_format($POS_EFFECTIVEDATE,$DATE_DISPLAY);
				} //end if
			
				if ($count3) {
					$person_count++;
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][order] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $person_count;
					$arr_content[$data_count][name] = $ORG_ID . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
					$arr_content[$data_count][pl_name] = $PL_NAME;
					$arr_content[$data_count][pos_change_date] = $POS_CHANGE_DATE;
					$arr_content[$data_count][pos_duration] = $POS_UNTIL;
					$arr_content[$data_count][retiredate] = $PER_RETIREDATE;
		
					$data_count++;
				}
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $LEVEL_NAME;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" : 
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(h.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1)		$arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
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
					}else if($select_org_structure==1){
						if($ORG_ID_2 && $ORG_ID_2!=-1) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE' or b.PL_CODE is null)";
				break;
				case "LEVEL" :
					if($LEVEL_NO) $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO')";
					else $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO' or a.LEVEL_NO is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global  $MINISTRY_ID, $DEPARTMENT_ID,$ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $LEVEL_NAME;
		
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
				break;
				case "LEVEL" :
					$LEVEL_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function

	//-----------------------------
	//หา per_id ที่ตรงตามเงื่อนไขการดำรงตำแหน่งที่ครบตามปีที่เลือก เพื่อค้นหาต่อไป		
		if($DPISDB=="odbc"){
			$cmd = "select distinct h.PER_ID as PER_ID from PER_PERSONAL a,PER_POSITIONHIS h where (a.PER_ID=h.PER_ID) $search_effective $search_condition order by h.PER_ID";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = "select distinct h.PER_ID as PER_ID from PER_PERSONAL a,PER_POSITIONHIS h where (a.PER_ID=h.PER_ID) $search_effective $search_condition order by h.PER_ID";
		}elseif($DPISDB=="mysql"){
			$cmd = "select distinct h.PER_ID as PER_ID from PER_PERSONAL a,PER_POSITIONHIS h where (a.PER_ID=h.PER_ID) $search_effective $search_condition order by h.PER_ID";
		} // end if
		$db_dpis2->send_cmd($cmd);
		//echo "<br>$cmd<br>";
		while($data2 = $db_dpis2->get_array()) $arr_per_id[] = trim($data2[PER_ID]);
		$addition_condition_perid = "";
		$arr_search_perid[] = "(a.PER_ID in (". implode($arr_per_id, ",") ."))";
		if(count($arr_search_perid)) $addition_condition_perid = implode(" and ", $arr_search_perid);
		if(trim($addition_condition_perid)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition_perid;
	//--------------------------
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from		(
						 					(
												PER_PERSONAL a
												inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
						 					) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
						 				) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
						 $search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){			//====================
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_LEVEL g , PER_ORG h
							 where		a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID(+) and a.LEVEL_NO=g.LEVEL_NO(+) and a.DEPARTMENT_ID=h.ORG_ID(+)
												$search_condition
							 order by	$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(	
						 					(
												PER_PERSONAL a
												inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
						 					) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
						 				) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
						 $search_condition
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1) { 
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
			switch($REPORT_ORDER){
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

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY_NAME";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
					} // end if
			break;	
			
			case "DEPARTMENT" : 
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
						if($DEPARTMENT_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
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
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						if($ORG_ID_1 != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != trim($data[ORG_ID_2])){
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						if($ORG_ID_2 != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
					} // end if
				break;
		
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = $data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
					} // end if
				break;

				case "LEVEL" :
					if($LEVEL_NO != trim($data[LEVEL_NO])){
						$LEVEL_NO = trim($data[LEVEL_NO]);
						$LEVEL_NAME = trim($data[LEVEL_NAME]);

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($LEVEL_NAME)?"". $LEVEL_NAME :"[ไม่ระบุระดับตำแหน่ง]");
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
					} // end if
				break;		
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	$RTF->open_section(1); 
//	$RTF->set_font($font, 20);
//	$RTF->color("0");	// 0=BLACK
		
	$RTF->add_header("", 0, false);	// header default
	$RTF->add_footer("", 0, false);		// footer default
	
	$RTF->paragraph(); 
		
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
			$ORDER = $arr_content[$data_count][order] ;
			$NAME = $arr_content[$data_count][name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$POS_CHANGE_DATE = $arr_content[$data_count][pos_change_date];
			$POS_UNTIL = $arr_content[$data_count][pos_duration];
			$RETIREDATE = $arr_content[$data_count][retiredate];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME;
			$arr_data[] = $PL_NAME;
			$arr_data[] = $POS_CHANGE_DATE;       
			$arr_data[] = $POS_UNTIL;
			$arr_data[] = $RETIREDATE;
			
			$data_align = array("L", "L", "L", "C", "C", "C");
			
			if($REPORT_ORDER != "CONTENT")
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
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