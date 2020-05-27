<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "g.PL_NAME";
		$position_no_name = "b.POS_NO_NAME";
		$position_no= "b.POS_NO";
		$type_code = "b.PT_CODE";
		$select_type_code = ", b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "g.PN_NAME";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "g.EP_NAME";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "g.TP_NAME";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
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
				if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
	}

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(d.SAH_KF_YEAR = '$search_budget_year' and h.MOV_SUB_TYPE in ('49'))";
	$arr_search_condition[] = "(d.SAH_KF_CYCLE = $search_salq_type)";

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
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	include ("rpt_R006003_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R006003_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type] ที่ไม่มีสิทธิ์ได้เลื่อนขั้นในปีงบประมาณ $search_budget_year ครั้งที่ $search_salq_type";
	$report_code = "R0603";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
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
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
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
		$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO, a.LEVEL_NO, 
										$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, h.MOV_NAME, d.SAH_REMARK $select_type_code
						  from	(	
										(
											(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
										) left join $line_table g on ($line_join)
									) left join PER_MOVMENT h on (d.MOV_CODE=h.MOV_CODE)
						 $search_condition
						 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO, a.LEVEL_NO, 
										$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, h.MOV_NAME, d.SAH_REMARK $select_type_code
						  from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_SALARYHIS d, PER_ORG e, PER_PRENAME f, $line_table g, PER_MOVMENT h
						  where	$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID and a.DEPARTMENT_ID=e.ORG_ID(+) and 
										a.PN_CODE=f.PN_CODE(+) and $line_join(+) and d.MOV_CODE=h.MOV_CODE(+)
										$search_condition
						 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO, a.LEVEL_NO, 
										$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, h.MOV_NAME, d.SAH_REMARK $select_type_code
						  from	(	
										(
											(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
										) left join $line_table g on ($line_join)
									) left join PER_MOVMENT h on (d.MOV_CODE=h.MOV_CODE)
						 $search_condition
						 order by $order_by, a.LEVEL_NO desc, a.PER_SALARY desc ";
	} // end if
/****
	if($search_per_type==1){	
		if($DPISDB=="odbc"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		(
												(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on ($line_join)
											) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
							 $search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_SALARYHIS d, PER_PRENAME f, $line_table g, PER_TYPE h
							  where	$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
							  				and a.PER_ID=d.PER_ID and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PT_CODE=h.PT_CODE(+)
											$search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		(
												(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on ($line_join)
											) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
							 $search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		} // end if
	}else{ // 2 ||  3 || 4
		if($DPISDB=="odbc"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
							 $search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_SALARYHIS d, PER_PRENAME f, $line_table g
							  where	$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
							  				and a.PER_ID=d.PER_ID and a.PN_CODE=f.PN_CODE(+) and $line_join(+)
											$search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	$select_list, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, a.LEVEL_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.PER_SALARY, d.SAH_REMARK, d.SAH_REMARK
							  from		(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SALARYHIS d on (a.PER_ID=d.PER_ID)
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
							 $search_condition
							 order by $select_list, a.LEVEL_NO desc, a.PER_SALARY desc ";
		} // end if
	} ******/
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){
						$MINISTRY_ID = trim($data[MINISTRY_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = $MINISTRY_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
						if($DEPARTMENT_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = $DEPARTMENT_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
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

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
		
		$data_row++;
		$PER_ID = $data[PER_ID];
		$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
		$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PL_NAME = trim($data[PL_NAME]);
		if(trim($type_code)){
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$PT_NAME = trim($data3[PT_NAME]);	
		}
		$PER_SALARY = $data[PER_SALARY];
		$SAH_REMARK = trim($data[SAH_REMARK]);
		
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
		$db_dpis3->send_cmd($cmd);
//		$db_dpis->show_error();
		$data3 = $db_dpis3->get_array();
		$LEVEL_NAME=$data3[LEVEL_NAME];
		$POSITION_LEVEL = $data3[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
		switch($SAH_REMARK){
			case 1 :
				$SAH_REMARK = "ลาติดตามคู่สมรส";
				break;
			case 2 :
				$SAH_REMARK = "เงินเดือนเต็มขั้น";
				break;
			case 3 :
				$SAH_REMARK = "ลาฝึกอบรม/ดูงาน";
				break;
			case 4 :
				$SAH_REMARK = "บรรจุใหม่";
				break;
			default :
				$SAH_REMARK = "";
				break;
		} // end switch case
		$MOV_NAME = trim($data[MOV_NAME]);
		$SAH_REMARK = trim($data[SAH_REMARK]);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PER_NAME;
		$arr_content[$data_count][position] = (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME);
		$arr_content[$data_count][posno] = $POS_NO;
		$arr_content[$data_count][per_salary] = $PER_SALARY;
		$arr_content[$data_count][mov_name] = $MOV_NAME;
		$arr_content[$data_count][remark] = $SAH_REMARK;

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
//		$RTF->open_section(1); 
//		$RTF->set_font($font, 20);
//		$RTF->color("0");	// 0=BLACK
		
		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
		
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
//		echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		if (!$result) echo "****** error ****** on open table for $table<br>";

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$POS_NO = $arr_content[$data_count][posno];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$MOV_NAME = $arr_content[$data_count][mov_name];
			$SAH_REMARK = $arr_content[$data_count][remark];
			$SAH_REMARK = eregi_replace("<br>", " ", $SAH_REMARK);

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME;
			$arr_data[] = $POSITION;
			$arr_data[] = $POS_NO;
			$arr_data[] = $PER_SALARY;
			$arr_data[] = $MOV_NAME;
			$arr_data[] = $SAH_REMARK;
	
			if($REPORT_ORDER == "ORG")
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