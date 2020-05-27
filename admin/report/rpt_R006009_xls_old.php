<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_org_structure==0){
					if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
					else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
				}elseif($select_org_structure==1){
					if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
					else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
				}

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
				$select_list .=  "$line_code as PL_CODE";

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
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or b.POEM_ID >= 0))";
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
	if($list_type == "PER_ORG_TYPE_1"){
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
	}elseif($list_type == "PER_ORG_TYPE_2"){
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
	}elseif($list_type == "PER_ORG_TYPE_3"){
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
	}elseif($list_type == "PER_ORG_TYPE_4"){
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
	}elseif($list_type == "PER_ORG"){
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
				$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= "$line_search_name";
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
	$report_code = "R0609";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$worksheet->set_column(0, 0, 25);
	$worksheet->set_column(1, 1, 25);
	$worksheet->set_column(2, 2, 25);
	$worksheet->set_column(3, 3, 25);

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $LEVEL_NO, $POS_NO, $PER_NAME, $PER_SURNAME, $EP_CODE, $TP_CODE;
		global $line_code,$position_no_c;		
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
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
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $LEVEL_NO, $POS_NO, $PER_NAME, $PER_SURNAME, $EP_CODE,$TP_CODE;
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
						 from			(	
												(
													(	
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
						 $search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, b.ORG_ID, c.ORG_NAME,
											a.PER_STARTDATE, a.PER_SALARY, a.PER_MEMBER $select_type_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table e, PER_LEVEL g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and $line_join(+) and a.LEVEL_NO=g.LEVEL_NO(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, b.ORG_ID, c.ORG_NAME,
											a.PER_STARTDATE, a.PER_SALARY, a.PER_MEMBER $select_type_code
						 from			(	
												(
													(	
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
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
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);	
		}
		$PER_STARTDATE = substr($data[PER_STARTDATE], 0, 10);
		$WORKAGE = date_difference($search_date, $PER_STARTDATE, "full");
		$PER_STARTDATE = show_date_format($PER_STARTDATE,$DATE_DISPLAY);

		$PER_SALARY = $data[PER_SALARY];
		$PER_MEMBER = $data[PER_MEMBER];
		
		$arr_content[$data_count][type] = "CONTENT";
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
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

//		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$SURNAME = $arr_content[$data_count][surname];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL = $arr_content[$data_count][level];
			$ORG = $arr_content[$data_count][org];
			$STARTDATE = $arr_content[$data_count][startdate];
			$WORKAGE = $arr_content[$data_count][workage];
			$SALARY = $arr_content[$data_count][salary];
			$MEMBER = $arr_content[$data_count][member];
			
			if($data_count > 0){
				for($i=0; $i<5; $i++){
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				} // end for
			} // end if
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "ชื่อ $NAME", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 2, "สกุล $SURNAME", set_format("xlsFmtTableDetail", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "ตำแหน่ง $POSITION", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 2, "$LEVEL", set_format("xlsFmtTableDetail", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$ORG_TITLE $ORG", set_format("xlsFmtTableDetail", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "วันที่บรรจุ $STARTDATE  (รวม $WORKAGE)", set_format("xlsFmtTableDetail", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "เงินเดือนปัจจุบัน $SALARY บาท", set_format("xlsFmtTableDetail", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "ผลการคำนวณ", set_format("xlsFmtTableDetail", "B", "L", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "เงินบำเหน็จ $SALARY บาท", set_format("xlsFmtTableDetail", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "เงินบำนาญ $SALARY บาท", set_format("xlsFmtTableDetail", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "การเป็นสมาชิก กบข.", set_format("xlsFmtTableDetail", "B", "L", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
			$worksheet->insert_bitmap($xlsRow, 0, (($MEMBER)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp"), 35, 4, 1, 0.8);
			$worksheet->write_string($xlsRow, 1, "เป็นสมาชิก", set_format("xlsFmtTableDetail", "B", "L", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
			$worksheet->insert_bitmap($xlsRow, 2, ((!$MEMBER)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp"), 35, 4, 1, 0.8);
			$worksheet->write_string($xlsRow, 3, "ไม่เป็นสมาชิก", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>