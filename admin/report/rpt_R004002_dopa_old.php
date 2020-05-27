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
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "e.PL_NAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "e.PN_NAME";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "e.EP_NAME";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
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
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.LEVEL_NO, b.LEVEL_SEQ_NO";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.LEVEL_SEQ_NO desc";

				break;
		} // end switch case
	} // end for

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_RETIREDATE), 1, 10) > '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(a.PER_RETIREDATE), 1, 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".($search_budget_year - 543)."-10-01')";

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
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
		}
		if($select_org_structure==0){
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if
		}else{ //==1
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if
		}
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
/***
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		}elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(b.EP_CODE)='$search_ep_code')";
			$list_type_text .= "$search_ep_name";
		} // end if  ***/
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
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "";
	$report_title = "";
	$report_code = "R0402";
	$show_budget_year = convert2thaidigit($search_budget_year);

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3, $position_table, $position_join,$select_org_structure;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $search_budget_year;
		global $days_per_year, $days_per_month, $seconds_per_day,$CARD_NO_DISPLAY,$DATE_DISPLAY;
		global $search_per_type, $position_table, $position_join, $line_table, $line_name, $line_join;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($search_per_type==1){ //****code ต่าง : select list และ join PER_MGT f เพิ่มมาด้วย
			if($DPISDB=="odbc"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, f.PM_NAME, g.POSITION_LEVEL,
													a.PER_CARDNO,	LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2
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
								 order by a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, f.PM_NAME, g.POSITION_LEVEL, 
													a.PER_CARDNO, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_MGT f, PER_LEVEL g
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and b.PM_CODE=f.PM_CODE(+) and a.LEVEL_NO=g.LEVEL_NO(+)
													$search_condition
								 order by a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, f.PM_NAME, g.POSITION_LEVEL,
													a.PER_CARDNO,	LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2
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
								 order by a.PER_NAME, a.PER_SURNAME ";
			} // end if
		}else{	//==2 | 3 | 4
			if($DPISDB=="odbc"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, 
													a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_CARDNO,
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE
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
								 order by a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO,g.LEVEL_NAME, 
													a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, a.PER_CARDNO,
													SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_LEVEL g
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=g.LEVEL_NO(+)
													$search_condition
								 order by a.PER_NAME, a.PER_SURNAME ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, 
													a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_CARDNO,
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE
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
								 order by a.PER_NAME, a.PER_SURNAME ";
			}
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
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
			$ORG_NAME = trim($data[ORG_NAME]);
			$CARD_NO=trim($data[PER_CARDNO]);
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_2 = $data[ORG_ID_2];
			$ORG_NAME_1 = $ORG_NAME_2 = "";
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
			if ($ORG_NAME=="-") $ORG_NAME = "";
			if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";
			if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";
			$ORG_NAME = trim($ORG_NAME_1. " " . $ORG_NAME);
			if ($ORG_NAME=="ไม่สังกัดสำนัก-กอง") $ORG_NAME= "กรมการปกครอง";
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][person] = convert2thaidigit($person_count);	
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			if ($PM_NAME)
				$arr_content[$data_count][position] = $PM_NAME . " (" . $PL_NAME . $POSITION_LEVEL . ")";
			else
				$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
			$arr_content[$data_count][birthdate] = convert2thaidigit($PER_BIRTHDATE);	
			$arr_content[$data_count][card_no] = convert2thaidigit(card_no_format($CARD_NO,$CARD_NO_DISPLAY));
			$arr_content[$data_count][org] = $ORG_NAME;

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
	
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_PERSONAL a 
											) left join PER_LEVEL b on (a.LEVEL_NO=b.LEVEL_NO)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, PER_LEVEL b
						 where		a.LEVEL_NO=b.LEVEL_NO(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												PER_PERSONAL a 
											) left join PER_LEVEL b on (a.LEVEL_NO=b.LEVEL_NO)
											$search_condition
						 order by		$order_by ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($LEVEL_NO != $data[LEVEL_NO]){
						$LEVEL_NO = $data[LEVEL_NO];
							$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$LEVEL_NAME = $data2[LEVEL_NAME];

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $LEVEL_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
		
			} // end switch case
		} // end for
		
		if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition);
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
			
			$border = "";
			if($REPORT_ORDER == "ORG"){
				$label = $NAME_1;
				$RTF->new_line();
				$RTF->set_font($font, 18);
				$RTF->add_text($RTF->bold(1) . $label . $RTF->bold(0), "left");
				$RTF->paragraph();
				// เริ่มตาราง
				$RTF->ln();			
			}else{
				$RTF->set_table_font($font, 16);
				$RTF->open_line();			
				$RTF->cell($NAME_0 . ".", "5", "right", "0");
				$RTF->cell($NAME_1, "30", "left", "0");
				$RTF->cell($NAME_3, "65", "left", "0");
				$RTF->close_line();

				$RTF->open_line();			
				$RTF->cell("", "5", "right", "0");
				$RTF->cell($NAME_2, "30", "left", "0");
				$RTF->cell($NAME_4, "65", "left", "0");
				$RTF->close_line();
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