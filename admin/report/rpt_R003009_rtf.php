<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_code = "b.PL_CODE";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_code = "b.PN_CODE";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_code = "b.EP_CODE";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_code = "b.TP_CODE";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
	} // end if

	if(!trim($RPTORD_LIST)) $RPTORD_LIST = "ORG";
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
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2"; 

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
				else if($select_org_structure==1)  $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		else if($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	}
	if(!trim($select_list)){
	 	if($select_org_structure==0)$select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		else if($select_org_structure==1)$select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	}

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

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
		$arr_search_condition[] = "(c.OT_CODE='01')";
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
		$arr_search_condition[] = "(c.OT_CODE='02')";
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
		$arr_search_condition[] = "(c.OT_CODE='03')";
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
		$arr_search_condition[] = "(c.OT_CODE='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			}	
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			}
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
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
			} // end if
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			}
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "($line_code=' $line_search_code')";
			$list_type_text .= $line_search_name;
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
	
	$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.LEVEL_NO) = '$search_level_no_min')";
	$search_condition .= (trim($search_condition)?" and ":" where ") . "(h.MOV_SUB_TYPE = 3 and trim(f.LEVEL_NO) = '$search_level_no_max')";
	
	$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$search_level_no_min'";
	$db_dpis->send_cmd($cmd);
	$a = $db_dpis->get_array();
	$search_level_name_min= $a[LEVEL_NAME];
	
	$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$search_level_no_max'";
	$db_dpis->send_cmd($cmd);
	$a = $db_dpis->get_array();
	$search_level_name_max= $a[LEVEL_NAME];

	include ("rpt_R003009_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R003009_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='P';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||ระยะเวลาเฉลี่ยในการเลื่อนจาก ". $search_level_name_min ." เป็น ". $search_level_name_max;
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0309";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	function count_year($search_per_gender, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $arr_rpt_order, $search_per_type, $search_level_no_min, $search_level_no_max,$select_org_structure;
		global $min_year, $max_year;
		
		$search_level_no_min = trim($search_level_no_min);
		$search_level_no_max = trim($search_level_no_max);
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if(trim($search_per_gender)) $search_condition .= (trim($search_condition)?" and ":" where ") . "(a.PER_GENDER=$search_per_gender)";
		$count_condition = "";

		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as MIN_POH_EFFECTIVEDATE,
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)) as MAX_POH_EFFECTIVEDATE
							 from	(
											(	
							 					(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_POSITIONHIS f on (a.PER_ID=f.PER_ID)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
										) left join PER_MOVMENT h on (e.MOV_CODE=h.MOV_CODE)
							$search_condition $count_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as MIN_POH_EFFECTIVEDATE,
												MIN(SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)) as MAX_POH_EFFECTIVEDATE
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e, PER_POSITIONHIS f, PER_ORG g, PER_MOVMENT h
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.PER_ID=e.PER_ID(+) and a.PER_ID=f.PER_ID(+) and a.DEPARTMENT_ID=g.ORG_ID(+) and e.MOV_CODE=h.MOV_CODE(+)
												$search_condition $count_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as MIN_POH_EFFECTIVEDATE,
												MIN(LEFT(trim(f.POH_EFFECTIVEDATE), 10)) as MAX_POH_EFFECTIVEDATE
							 from	(
											(	
							 					(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_POSITIONHIS f on (a.PER_ID=f.PER_ID)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
										) left join PER_MOVMENT h on (e.MOV_CODE=h.MOV_CODE)
							$search_condition $count_condition
							 group by	a.PER_ID ";
		} // end if

		$total_year = 0;
		$min_year = $max_year = "";
		if($select_org_structure==1){
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		while($data = $db_dpis2->get_array()){
				$PER_ID = $data[PER_ID];
				$MIN_POH_EFFECTIVEDATE = substr(trim($data[MIN_POH_EFFECTIVEDATE]), 0, 10);
				$MAX_POH_EFFECTIVEDATE = substr(trim($data[MAX_POH_EFFECTIVEDATE]), 0, 10);

				if(trim($MIN_POH_EFFECTIVEDATE) && trim($MAX_POH_EFFECTIVEDATE)){
					$count_year = date_difference($MIN_POH_EFFECTIVEDATE, $MAX_POH_EFFECTIVEDATE, "y");
					$total_year += $count_year;
	
					if($min_year=="") $min_year = $count_year;
					elseif($min_year > $count_year) $min_year = $count_year;
	
					if($max_year=="") $max_year = $count_year;
					elseif($max_year < $count_year) $max_year = $count_year;
				} // end if
		} // end while

		if($count_person){
			$avg_year = ($total_year / $count_person);
			$count_year = $avg_year;
		}else{
			$count_year = 0;
		} // end if

		return $count_year;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" : 
					if($MINISTRY_ID && $MINISTRY_ID!=-1)		$arr_addition_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1)		$arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else if($select_org_structure==1){  
						if($ORG_ID && $ORG_ID!=-1)  $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
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
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
		
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
			} // end switch case
		} // end for
	} // function
	
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from	(
										(
											(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_POSITIONHIS f on (a.PER_ID=f.PER_ID)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
									) left join PER_MOVMENT h on (e.MOV_CODE=h.MOV_CODE)
										$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e, PER_POSITIONHIS f, PER_ORG g, PER_MOVMENT h
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
											and a.PER_ID=e.PER_ID(+) and a.PER_ID=f.PER_ID(+) and e.MOV_CODE=h.MOV_CODE(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from	(
										(
											(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_POSITIONHIS f on (a.PER_ID=f.PER_ID)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
									) left join PER_MOVMENT h on (e.MOV_CODE=h.MOV_CODE)
										$search_condition
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1){
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;
	
	$data_count = 0;
	$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
			case "MINISTRY" : 
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1){	
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							
							//if ($f_all) {	
							if(($MINISTRY_NAME !="" && $MINISTRY_NAME !="-") || ($BKK_FLAG==1 && $MINISTRY_NAME !="" && $MINISTRY_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
							
								$arr_content[$data_count][type] = "MINISTRY";
								$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
								$arr_content[$data_count][count_1] = count_year(1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = count_year(2, $search_condition, $addition_condition);
								$arr_content[$data_count][count_3] = count_year(0, $search_condition, $addition_condition);
								$arr_content[$data_count][count_4] = $min_year;
								$arr_content[$data_count][count_5] = $max_year;
							
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
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);	
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						
						if(($DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-") || ($BKK_FLAG==1 && $DEPARTMENT_NAME !="" && $DEPARTMENT_NAME !="-")){
							$addition_condition = generate_condition($rpt_order_index);
						
							$arr_content[$data_count][type] = "DEPARTMENT";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
							$arr_content[$data_count][count_1] = count_year(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_year(2, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_year(0, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = $min_year;
							$arr_content[$data_count][count_5] = $max_year;
						
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
							$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						
							  if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
		
								$arr_content[$data_count][type] = "ORG";
								$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
								$arr_content[$data_count][count_1] = count_year(1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = count_year(2, $search_condition, $addition_condition);
								$arr_content[$data_count][count_3] = count_year(0, $search_condition, $addition_condition);
								$arr_content[$data_count][count_4] = $min_year;
								$arr_content[$data_count][count_5] = $max_year;
		
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
						} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
					} // end if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1)
				} // end if
				break;		
			} // end switch case
		} // end for
		}
	} // end while
	
	$GRAND_TOTAL_1 = count_year(1, $search_condition, "");
	$GRAND_TOTAL_2 = count_year(2, $search_condition, "");
	$GRAND_TOTAL_3 = count_year(0, $search_condition, "");
	$GRAND_TOTAL_4 = $min_year;
	$GRAND_TOTAL_5 = $max_year;

//	echo "<pre>"; print_r($arr_content); echo "</pre>";

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
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			$COUNT_2 = $arr_content[$data_count][count_2];
			$COUNT_3 = $arr_content[$data_count][count_3];
			$COUNT_4 = $arr_content[$data_count][count_4];
			$COUNT_5 = $arr_content[$data_count][count_5];

	//new format************************************************************			
            	$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $COUNT_3;
				$arr_data[] = $COUNT_4;
            	$arr_data[] = $COUNT_5;

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for

	//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] = "รวม";
			$arr_data[] = $GRAND_TOTAL_1;
			$arr_data[] = $GRAND_TOTAL_2;
			$arr_data[] = $GRAND_TOTAL_3;
			$arr_data[] = $GRAND_TOTAL_4;
			$arr_data[] = $GRAND_TOTAL_5;
			
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
?>