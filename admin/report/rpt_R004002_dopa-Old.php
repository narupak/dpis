<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$fname= "rpt_R004002_RTF.rtf";
	if (!$font) $font = "AngsanaUPC";

	$IMG_PATH = "../images/";
	
	$RTF = new RTF();
//	$RTF = new RTF("a4", 1150, 720, 600, 200);
	$RTF->set_default_font($font, 16);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
									
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type==5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$position_no = "b.POS_NO";
		$position_no_name = "b.POS_NO_NAME";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "e.PL_NAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$position_no = "b.POEM_NO";
		$position_no_name = "b.POEM_NO_NAME";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "e.PN_NAME";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$position_no = "b.POEMS_NO";
		$position_no_name = "b.POEMS_NO_NAME";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "e.EP_NAME";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$position_no = "b.POT_NO";
		$position_no_name = "b.POT_NO_NAME";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=e.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "e.TP_NAME";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.DEPARTMENT_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.DEPARTMENT_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, g.LEVEL_SEQ_NO, b.LEVEL_NO";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, g.LEVEL_SEQ_NO desc";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, g.LEVEL_SEQ_NO desc";

				$heading_name .= " $ORG_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){
			$order_by = "d.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){
			if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.DEPARTMENT_ID";
			else if($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";	// ตามกฏหมาย
			else if($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";	// ตามมอบหมายงาน
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){
			$select_list = "d.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){
			if($select_org_structure==0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.DEPARTMENT_ID";
			else if($select_org_structure==1) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
			else if($select_org_structure==1) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
		}
	} // end if
//	echo "select_list=$select_list (select_org_structure=$select_org_structure)<br>";

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_RETIREDATE), 1, 10) > '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(a.PER_RETIREDATE), 1, 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".($search_budget_year - 543)."-10-01')";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
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

		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='02')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
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
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
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
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='04')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='04')";
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
			$list_type_text .= " $line_search_name";
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
//			$arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "";
	$report_title = "";
	$report_code = "R0402";
	$show_budget_year = convert2thaidigit($search_budget_year);

        
	function list_person($search_condition, $addition_condition, $org_id, $level_no){
		global $DPISDB, $db_dpis2, $db_dpis3, $position_table, $position_join,$select_org_structure;
		global $arr_rpt_order, $rpt_order_index, $arr_content, $data_count, $person_count, $search_budget_year;
		global $days_per_year, $days_per_month, $seconds_per_day,$CARD_NO_DISPLAY,$DATE_DISPLAY;
		global $search_per_type, $position_table, $position_join, $position_no, $position_no_name, $line_table, $line_name, $line_join;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if ($org_id) $search_condition .= " and b.ORG_ID = $org_id ";
		//if ($level_no) $search_condition .= " and a.LEVEL_NO = '$level_no' ";
//		echo "org_id=$org_id, search_condition=$search_condition<br>";

		if($search_per_type==1){ //****code ต่าง : select list และ join PER_MGT f เพิ่มมาด้วย
			if($DPISDB=="odbc"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, f.PM_NAME, g.POSITION_LEVEL,
													a.PER_CARDNO,	LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, c.ORG_NAME, b.DEPARTMENT_ID, b.ORG_ID_1, b.ORG_ID_2, 
													$position_no as POS_NO, $position_no_name as POS_NO_NAME
								 from			(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join $position_table b on $position_join
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join $line_table e on ($line_join)
													) left join PER_MGT f on (b.PM_CODE=f.PM_CODE)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, f.PM_NAME, g.POSITION_LEVEL, 
													a.PER_CARDNO, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, c.ORG_NAME, b.DEPARTMENT_ID, b.ORG_ID_1, b.ORG_ID_2, 
													$position_no as POS_NO, $position_no_name as POS_NO_NAME
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_MGT f, PER_LEVEL g
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and b.PM_CODE=f.PM_CODE(+) and a.LEVEL_NO=g.LEVEL_NO(+)
													$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, f.PM_NAME, g.POSITION_LEVEL,
													a.PER_CARDNO,	LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, c.ORG_NAME, b.DEPARTMENT_ID, b.ORG_ID_1, b.ORG_ID_2, 
													$position_no as POS_NO, $position_no_name as POS_NO_NAME
								 from			(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join $position_table b on $position_join
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join $line_table e on ($line_join)
													) left join PER_MGT f on (b.PM_CODE=f.PM_CODE)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			} // end if
		}else{	//==2 | 3 | 4
			if($DPISDB=="odbc"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, 
													a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_CARDNO,
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, 
													c.ORG_NAME, b.DEPARTMENT_ID, b.ORG_ID_1, b.ORG_ID_2, $position_no as POS_NO, $position_no_name as POS_NO_NAME
								 from			(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO,g.LEVEL_NAME, 
                                            a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, a.PER_CARDNO,
                                            SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE, 
                                            c.ORG_NAME, b.DEPARTMENT_ID, b.ORG_ID_1, b.ORG_ID_2, $position_no as POS_NO, $position_no_name as POS_NO_NAME
                                         from PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_LEVEL g
                                         where $position_join(+) and b.ORG_ID=c.ORG_ID(+)
                                             and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=g.LEVEL_NO(+)
                                                $search_condition
                                         order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, 
													a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_CARDNO,
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, 
													c.ORG_NAME, b.DEPARTMENT_ID, b.ORG_ID_1, b.ORG_ID_2, $position_no as POS_NO, $position_no_name as POS_NO_NAME
								 from			(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join $line_table e on ($line_join)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								$search_condition
								 order by b.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME ";
			}
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("b.DEPARTMENT_ID", "a.DEPARTMENT_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
		//echo "list_person>>>$cmd<br><br><br>";
                //die('xxx');
//		$db_dpis2->show_error();
                        while($data = $db_dpis2->get_array()){
                            $data_count++;
                            $person_count++;
                            $PER_ID = $data[PER_ID];
                            $PN_NAME = trim($data[PN_NAME]);
                            $PER_NAME = trim($data[PER_NAME]);
                            $PER_SURNAME = trim($data[PER_SURNAME]);
                            $PL_NAME = trim($data[PL_NAME]);
                            $PM_NAME = trim($data[PM_NAME]);
                            $POSITION_LEVEL = trim($data[POSITION_LEVEL]);
                            $POS_NO_NAME = trim($data[POS_NO_NAME]);
                            if (substr($POS_NO_NAME,0,4)=="กปด.")
                                    $POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
                            else
                                    $POS_NO = $POS_NO_NAME.trim($data[POS_NO]);
                            $ORG_NAME = trim($data[ORG_NAME]);
                            $CARD_NO=trim($data[PER_CARDNO]);
                            $PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
                            $TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
                            $ORG_ID_1 = $data[ORG_ID_1];
                            $ORG_ID_2 = $data[ORG_ID_2];
                            $TMP_DEPARTMENT_NAME = $ORG_NAME_1 = $ORG_NAME_2 = "";
                            if ($TMP_DEPARTMENT_ID) {
                                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
                                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                                    $db_dpis3->send_cmd($cmd);
                                    $data3 = $db_dpis3->get_array();
                                    $TMP_DEPARTMENT_NAME = $data3[ORG_NAME];
                            }
                            if ($ORG_ID_2) {
                                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
                                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                                    $db_dpis3->send_cmd($cmd);
                                    $data3 = $db_dpis3->get_array();
                                    $ORG_NAME_2 = $data3[ORG_NAME];
                            }
                            if ($ORG_ID_1) {
                                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
                                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                                    $db_dpis3->send_cmd($cmd);
                                    $data3 = $db_dpis3->get_array();
                                    $ORG_NAME_1 = $data3[ORG_NAME];
                            }

                            if ($PM_NAME=="นายอำเภอ") $ORG_NAME_1 = str_replace("ที่ทำการปกครอง","",$ORG_NAME_1);
    //			$ORG_NAME = trim($ORG_NAME_2. " " . $ORG_NAME_1. " " . $ORG_NAME);
    //			echo ">>ORG_NAME_2=".$ORG_NAME_2. " , ORG_NAME_1=" . $ORG_NAME_1. " , ORG_NAME=" . $ORG_NAME."<br>";
                            if ($ORG_NAME=="-") $ORG_NAME = "";
                            if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";
                            if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";
                            $ORG_NAME = trim($ORG_NAME_1. " " . $ORG_NAME);
    //			$ORG_NAME = trim($ORG_NAME_2. " " . $ORG_NAME_1. " " . $ORG_NAME);
                            if ($ORG_NAME=="ไม่สังกัดสำนัก-กอง") $ORG_NAME= "กรมการปกครอง";
                            $arr_content[$data_count][type] = "CONTENT";
                            $arr_content[$data_count][person] = convert2thaidigit($person_count);	
                            $arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
                            $arr_content[$data_count][pl_name] = $PL_NAME;
                            $arr_content[$data_count][pm_name] = $PM_NAME;
                            if ($PM_NAME) 
                                    $arr_content[$data_count][position] = $PM_NAME . " (" . $PL_NAME . $POSITION_LEVEL . ")";
                            else 
                                    $arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
                            $arr_content[$data_count][birthdate] = convert2thaidigit($PER_BIRTHDATE);	
                            $arr_content[$data_count][card_no] = convert2thaidigit(card_no_format($CARD_NO,$CARD_NO_DISPLAY));
                            $arr_content[$data_count][org] = $ORG_NAME;
                            $arr_content[$data_count][dept] = $TMP_DEPARTMENT_NAME;
                            $arr_content[$data_count][pos_no] = $POS_NO;

    //			$data_count++;
                        } // end while
		
	} // function

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type;
		global $LEVEL_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($LEVEL_NO) $arr_addition_condition[] = "(a.LEVEL_NO = '$LEVEL_NO')";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($search_per_type==1){ //****code ต่าง : select list และ join PER_MGT f เพิ่มมาด้วย
		if($DPISDB=="odbc"){
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from		(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (c.DEPARTMENT_ID=d.ORG_ID)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							order by $order_by, g.LEVEL_SEQ_NO desc  ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG d, PER_LEVEL g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and c.DEPARTMENT_ID=d.ORG_ID(+)
												and a.LEVEL_NO=g.LEVEL_NO(+)
												$search_condition
							 order by $order_by, g.LEVEL_SEQ_NO desc  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from		(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (c.DEPARTMENT_ID=d.ORG_ID)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							order by $order_by, g.LEVEL_SEQ_NO desc  ";
		} // end if
	}else{	//==2 | 3 | 4
		if($DPISDB=="odbc"){
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from	(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_ORG d on (c.DEPARTMENT_ID=d.ORG_ID)
										) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							order by $order_by, g.LEVEL_SEQ_NO desc  ";
		}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select		distinct $select_list, b.LEVEL_NO, g.LEVEL_SEQ_NO
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG d, PER_LEVEL g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and c.DEPARTMENT_ID=d.ORG_ID(+)
												and a.LEVEL_NO=g.LEVEL_NO(+)
												$search_condition
							order by  $order_by, g.LEVEL_SEQ_NO desc  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		distinct $select_list, g.LEVEL_NO, g.LEVEL_SEQ_NO
							 from		(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (c.DEPARTMENT_ID=d.ORG_ID)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition
							order by $order_by, g.LEVEL_SEQ_NO desc  ";
		}
	} // end if
	if($select_org_structure==1) { 	// 1 คือ มอบหมายงาน  0 คือ กฎหมาย
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("b.DEPARTMENT_ID", "a.DEPARTMENT_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
/*		
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list, b.LEVEL_NO, g.LEVEL_SEQ_NO
						 from			(
												PER_PERSONAL a 
											) left join PER_LEVEL b on (a.LEVEL_NO=b.LEVEL_NO)
											$search_condition
						 order by		$order_by, g.LEVEL_SEQ_NO desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list, b.LEVEL_NO, g.LEVEL_SEQ_NO
						 from			PER_PERSONAL a, PER_LEVEL b
						 where		a.LEVEL_NO=b.LEVEL_NO(+)
											$search_condition
						 order by		$order_by, g.LEVEL_SEQ_NO desc ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list, b.LEVEL_NO, g.LEVEL_SEQ_NO
						 from			(
												PER_PERSONAL a 
											) left join PER_LEVEL b on (a.LEVEL_NO=b.LEVEL_NO)
											$search_condition
						 order by		$order_by, g.LEVEL_SEQ_NO desc ";
	} // end if
*/
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "main.....$cmd ($count_data)<br>";
      //  die('sdsdsdf');
//	$db_dpis->show_error();
	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);
	$DEPARTMENT_ID = "";
	$MINISTRY_NAME = "";
	$ORG_NAME = "";
	$DEPARTMENT_NAME = "";
	$ORG_MIX = "";
	$ORG_IDX_ID = 0;
	$arr_rpt_order[] = "LEVEL";
       
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
//							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
							$MINISTRY_NAME = "";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if
					}
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
//							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
							$DEPARTMENT_NAME = "";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if
					} // end if
                                        
				break;
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						$ORG_IDX_ID = $ORG_ID;
						if($ORG_ID == ""){
//							$ORG_NAME = "[ไม่ระบุ$ORG_TITLE]";
							$ORG_NAME = "";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if
					}
					if ($ORG_MIX != trim($MINISTRY_NAME." ".$DEPARTMENT_NAME." ".$ORG_NAME))  {
						$ORG_MIX = trim($MINISTRY_NAME." ".$DEPARTMENT_NAME." ".$ORG_NAME);
						if (!$ORG_MIX) $ORG_MIX = "ไม่ระบุ สังกัด";
						
						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-2) * 5)) . $ORG_MIX;
						$data_count++;
					} // end if
					//echo "DEPARTMENT_ID=$DEPARTMENT_ID , ORG_IDX_ID=$ORG_IDX_ID , ORG_MIX=$ORG_MIX <br><br><br>";
				break;
				case "LEVEL" :
					if($LEVEL_NO != $data[LEVEL_NO]){
						$LEVEL_NO = $data[LEVEL_NO];
						
						$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$LEVEL_NAME = $data2[LEVEL_NAME];
						if (!$LEVEL_NAME) $LEVEL_NAME = "ไม่ระบุ ระดับ";

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-2) * 5)) . $LEVEL_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						list_person($search_condition, $addition_condition, $ORG_IDX_ID, $LEVEL_NO);
                                                //echo "$search_condition , $addition_condition, $ORG_IDX_ID, $LEVEL_NO <br><br>";
						$data_count++;
					} // end if
				break;
		
			} // end switch case
		} // end for
                
		if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition, $ORG_IDX_ID, $LEVEL_NO);

	} // end while

//	echo "<pre>"; print_r($arr_content); echo "</pre>";

	if($count_data){
//		echo "aaaa";
		$show_person = convert2thaidigit($person_count);
		$img_file = $IMG_PATH.krut.".jpg";
		$img_dim = 100;
		
		$RTF->add_image("$img_file", $img_dim, "center");
		
		$RTF->paragraph();
		$RTF->set_font($font, 16);
		if ($BKK_FLAG==1 && !$DEPARTMENT_NAME) 
			$RTF->add_text("ประกาศ" . $MINISTRY_NAME, "center");
		else
			$RTF->add_text("ประกาศ" . $DEPARTMENT_NAME, "center");
		$RTF->paragraph();
		$RTF->add_text("เรื่อง  ข้าราชการพ้นจากราชการเพราะอายุครบหกสิบปีบริบูรณ์", "center");
		$RTF->paragraph();
		$RTF->add_text("(ครบเกษียณอายุ) ในสิ้นปีงบประมาณ พ.ศ. " . $show_budget_year, "center");
		$RTF->paragraph();
		$RTF->add_text("                    ด้วยในสิ้นปีงบประมาณ พ.ศ. " . $show_budget_year . " " . $DEPARTMENT_NAME . "มีข้าราชการที่มีอายุครบหกสิบปีบริบูรณ์และ", "left");
		$RTF->paragraph();
		$RTF->add_text("จะต้องพ้นจากราชการ (ครบเกษียณอายุ)  ตั้งแต่วันที่ ๑ ตุลาคม " . $show_budget_year . " เป็นต้นไป  ตามความในมาตรา ๑๙ แห่งพระราชบัญญัติ", "left");
		$RTF->paragraph();
		$RTF->add_text("บำเหน็จบำนาญข้าราชการ พ.ศ. ๒๔๙๔ และที่แก้ไขเพิ่มเติม และพระราชบัญญัติกองทุนบำเหน็จบำนาญข้าราชการ พ.ศ. ๒๕๓๙", "left");
		$RTF->paragraph();
		$RTF->add_text("จำนวน " . $show_person . " ราย  ดังนี้", "left");
		$RTF->paragraph();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME_0 = $arr_content[$data_count][person];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][card_no];
			$NAME_3 = $arr_content[$data_count][position];
			$NAME_4 = $arr_content[$data_count][org];
			$NAME_5 = $arr_content[$data_count][remark];
			$POS_NO = $arr_content[$data_count][pos_no];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PM_NAME = $arr_content[$data_count][pm_name];
			
			$border = "";
			if($REPORT_ORDER == "ORG"){
				$label = $NAME_1;
//				echo "ORG-->".$NAME_1."<br>";
				$RTF->new_line();
				$RTF->set_font($font, 18);
				$RTF->add_text($RTF->bold(1) . $label . $RTF->bold(0), "left");
			} else if($REPORT_ORDER == "LEVEL"){
				$label = $NAME_1;
//				echo "ORG-->".$NAME_1."<br>";
				$RTF->new_line();
				$RTF->set_font($font, 18);
				$RTF->add_text($RTF->bold(1) . $label . $RTF->bold(0), "left");
				$RTF->paragraph();
				// เริ่มตาราง
				$RTF->ln();			
			}else{
				$RTF->set_table_font($font, 16);
//				echo "   not ORG-->".$NAME_1."<br>";
				$RTF->open_line();			
				$RTF->cell($NAME_0 . ".", "7", "right", "0");
				$RTF->cell($NAME_1, "30", "left", "0");
				if ($BKK_FLAG==1)
					$RTF->cell($PL_NAME, "65", "left", "0");
				else
					$RTF->cell($NAME_3, "65", "left", "0");
				$RTF->close_line();

				$RTF->open_line();			
				$RTF->cell("", "7", "right", "0");
				if ($BKK_FLAG==1) {
					$RTF->cell("", "30", "left", "0");
					$RTF->cell("(ตำแหน่งเลขที่ ".$POS_NO." )", "65", "left", "0");
				}else{
					$RTF->cell($NAME_2, "30", "left", "0");
					$RTF->cell($NAME_4, "65", "left", "0");
				}
				$RTF->close_line();

				if ($BKK_FLAG==1) {
					$RTF->open_line();			
					$RTF->cell("", "7", "right", "0");
					if ($PM_NAME) $NAME_4 = $PM_NAME . " " . $NAME_4;
					$RTF->cell("", "30", "left", "0");
					$RTF->cell($NAME_4, "65", "left", "0");
					$RTF->close_line();
				}
/*			
				$label = $NAME_0 . ". " . $NAME_1 . "  " . $NAME_3;
				$RTF->new_line();
				$RTF->add_text($label, "left");
				$label = $NAME_5 . "  " . $NAME_2 . "  " . $NAME_4;
				$RTF->new_line();
				$RTF->add_text($label, "left");
				$RTF->new_line(); */
			} // end if
		} // end for				
	}else{
		$RTF->new_line();
		$RTF->add_text("********** ไม่มีข้อมูล **********", "center");
		$RTF->paragraph();
	} // end if

	$RTF->display($fname);

	ini_set("max_execution_time", 30);

?>