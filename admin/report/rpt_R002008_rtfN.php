<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
		if($search_edu) { $search_edu.= ' or '; }
		$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; } 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_field = "b.TP_CODE";
	} // end if

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	
	
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);
//	$arr_rpt_order = array("EDUCLEVEL", "ORG");

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
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "EDUCLEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "f.EL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "f.EL_CODE";

				$heading_name .= " $EL_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		 if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		else if($select_org_structure==1)  $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
	}

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";	
	$arr_search_condition[] = "(trim(d.CT_CODE_EDU) <> '140')";
	if($search_el_code) $arr_search_condition[] = "(trim(f.EL_CODE)=trim('$search_el_code'))";

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
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	include ("rpt_R002008_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R002008_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงาน$PERSON_TYPE[$search_per_type]ที่สำเร็จการศึกษาในต่างประเทศ";
	$report_title .= trim($search_el_code)?"ระดับ$search_el_name":"";
	$report_title .= "||จำแนกตาม". trim($heading_name);
	$report_code = "R0208";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	function count_person($level_no, $PER_GENDER, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $arr_search_condition, $search_el_code, $search_per_type, $search_edu, $select_org_structure;
		
//		echo "addition :: $addition_condition<br>";
		if(!$level_no){
			$search_condition = "";
			for($i=0; $i<count($arr_search_condition); $i++){
				if(strpos($arr_search_condition[$i], "trim(f.EL_CODE)")===false && strpos($arr_search_condition[$i], "trim(d.CT_CODE_EDU)")===false){
					if($search_condition) $search_condition .= " and ";
					$search_condition .= $arr_search_condition[$i];
				} // end if
			} // end for
			if(trim($search_condition)) $search_condition = " where ". $search_condition;
		} // end if

		if($level_no){ 
			$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
			if($DPISDB=="odbc") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="oci8") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="mysql") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
		} // end if

		if($PER_GENDER){ 
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$search_condition = " where a.PER_GENDER=$PER_GENDER " . $search_condition;
		} // end if
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from	(		
											(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) left join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							 $search_condition and ($search_edu)
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f, PER_ORG g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=g.ORG_ID
												and a.PER_ID=d.PER_ID(+) and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE(+)
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on $position_join
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) left join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							 $search_condition and ($search_edu)
							 group by	a.PER_ID ";
		} // end if
		if($select_org_structure==1){ 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
		}
		$count_person = $db_dpis2->send_cmd($cmd);
	//$db_dpis2->show_error();
	 //echo $cmd."<hr>";
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type;
		global $EL_CODE, $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
					break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
					break;
				case "ORG" :	
					if($select_org_structure==0){ 
						if($ORG_ID && $ORG_ID!=-1)	$arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";					
					} else if($select_org_structure==1) { 
						if($ORG_ID && $ORG_ID!=-1)	$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";					
					}	
					break;
				case "EDUCLEVEL" :
					if($EL_CODE) $arr_addition_condition[] = "(trim(f.EL_CODE) = '$EL_CODE')";
					else $arr_addition_condition[] = "(trim(f.EL_CODE) = '' or f.EL_CODE is null)";
					break;
			} // end switch case
		} // end for
		
//		echo "<pre>"; print_r($arr_addition_condition); echo "</pre>";

		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $EL_CODE, $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID;
		
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
				case "EDUCLEVEL" :
					$EL_CODE = -1;
					break;
			} // end switch case
		} // end for
	} // function

	//แสดงรายชื่อหน่วยงาน
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													(
														(	
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition  and ($search_edu)
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){	
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f, PER_ORG g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
											and a.PER_ID=d.PER_ID  and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
											$search_condition and ($search_edu) 
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													(
														(	
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition  and ($search_edu)
						 order by		$order_by ";
	}
	if($select_org_structure==1){ 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo $cmd."<br>";
	$data_count = 0;
	unset($LEVEL_TOTAL);
	initialize_parameter(0);
	$rpt_order_start_index = 0;	// order ที  0 = COUNTRY ยังไม่ได้คำนวณ เริ่ม order ที่ 1 (MINISTRY) ก่อน
	while($data = $db_dpis->get_array()){
	
	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){		
				case "MINISTRY" :
						if($MINISTRY_ID != trim($data[MINISTRY_ID])){
							$MINISTRY_ID =  trim($data[MINISTRY_ID]);
							if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$MINISTRY_NAME = $data2[ORG_NAME];
								$MINISTRY_SHORT = $data2[ORG_SHORT];
							
							//if ($f_all) {
//								$addition_condition = generate_condition($rpt_order_index);
								if ($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1) {
										$arr_content[$data_count][type] = "COUNTRY";
										$rpt_order_start_index = 0;
										$addition_condition = "";
								} else {
										$arr_content[$data_count][type] = "MINISTRY";
										$rpt_order_start_index = 1;
										$addition_condition = generate_condition(1);
								}
					
//								$arr_content[$data_count][type] = "MINISTRY";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $rpt_order_start_index) * 5)) .$MINISTRY_NAME;
								// ช + ญ
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no,1, $search_condition, $addition_condition) + count_person($tmp_level_no,2, $search_condition, $addition_condition);
								} // end for
								$arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(g.ORG_ID_REF=$MINISTRY_ID)") + count_person(0, 2, $search_condition, "(g.ORG_ID_REF=$MINISTRY_ID)");
	
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter(($rpt_order_index + 1) - $first_order);
								$data_count++;
							//} // end if ($f_all)
						} //end if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1)
					} // end if
				break;
			case "DEPARTMENT" : 
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
						if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){		
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$DEPARTMENT_NAME = $data2[ORG_NAME];
								$DEPARTMENT_SHORT = $data2[ORG_SHORT];
								
								$addition_condition = generate_condition($rpt_order_index);
	
								$arr_content[$data_count][type] = "DEPARTMENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $rpt_order_start_index) * 5)) .$DEPARTMENT_NAME;
								// ช + ญ
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no,1, $search_condition, $addition_condition) + count_person($tmp_level_no,2, $search_condition, $addition_condition);
								} // end for
								if($DEPARTMENT_ID == "") $arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(a.DEPARTMENT_ID = 0 or a.DEPARTMENT_ID is null)") + count_person(0, 2, $search_condition, "(a.DEPARTMENT_ID = 0 or a.DEPARTMENT_ID is null)");
								else $arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(a.DEPARTMENT_ID=$DEPARTMENT_ID)") + count_person(0, 2, $search_condition, "(a.DEPARTMENT_ID=$DEPARTMENT_ID)");
						
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter(($rpt_order_index + 1) - $first_order);
							$data_count++;
							} // end if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1)
						} // end if
				break;
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1){ $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;

							if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
							
								$arr_content[$data_count][type] = "ORG";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $rpt_order_start_index) * 5)) . $ORG_NAME;
								$last_d_cnt = $data_count;
								$data_count++;
							
								$arr_content[$data_count][type] = "SEX";
								$arr_content[$data_count][name] = str_repeat(" ", ((($rpt_order_index + 1) - $rpt_order_start_index) * 5)) . "ชาย";
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no,1, $search_condition, $addition_condition);
									$arr_content[$last_d_cnt]["level_".$tmp_level_no] = $arr_content[$data_count]["level_".$tmp_level_no];
									//if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
								} // end for
								if($ORG_ID == "") $arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(b.ORG_ID = 0 or b.ORG_ID is null)");
								else $arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(b.ORG_ID=$ORG_ID)");
								$arr_content[$last_d_cnt][count_all] = $arr_content[$data_count][count_all];
								//$GRAND_ALL += $arr_content[$data_count][count_all];
								$data_count++;
								
								$arr_content[$data_count][type] = "SEX";
								$arr_content[$data_count][name] = str_repeat(" ", ((($rpt_order_index + 1) - $rpt_order_start_index) * 5)) . "หญิง";
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, 2,$search_condition, $addition_condition);
									$arr_content[$last_d_cnt]["level_".$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
									//if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
								} // end for
								
								if($ORG_ID == "") $arr_content[$data_count][count_all] = count_person(0, 2, $search_condition, "(b.ORG_ID = 0 or b.ORG_ID is null)");
								else $arr_content[$data_count][count_all] = count_person(0, 2, $search_condition, "(b.ORG_ID=$ORG_ID)");
								$arr_content[$last_d_cnt][count_all] += $arr_content[$data_count][count_all];
								//$GRAND_ALL += $arr_content[$data_count][count_all];
							
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter(($rpt_order_index + 1) - $first_order);			
								$data_count++;
							} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
						} // end if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1)
					} // end if
				break;
				case "EDUCLEVEL" :
					if($EL_CODE != trim($data[EL_CODE])){
						$EL_CODE = trim($data[EL_CODE]);
						if($EL_CODE != ""){
							$cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EL_NAME = $data2[EL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCLEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $rpt_order_start_index) * 5)) . $EL_NAME;
						// ช + ญ
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no,1, $search_condition, $addition_condition) + count_person($tmp_level_no,2, $search_condition, $addition_condition);
						} // end for
						if($EL_CODE == "") $arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(d.EL_CODE = 0 or d.EL_CODE is null)") + count_person(0, 2, $search_condition, "(d.EL_CODE = 0 or d.EL_CODE is null)");
						else $arr_content[$data_count][count_all] = count_person(0, 1, $search_condition, "(d.EL_CODE='$EL_CODE')") + count_person(0, 2, $search_condition, "(d.EL_CODE='$EL_CODE')");
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter(($rpt_order_index + 1) - $first_order);
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for		
		}
	} // end while
	
	$sum_grand_total = 0;
	$arr_tmp_data = (array) null;
	$arr_tmp_data[] = "";	// แทน name
	for($i=0; $i<count($ARR_LEVEL_NO); $i++) {
		$tmp_level_no = $ARR_LEVEL_NO[$i]; 
		$LEVEL_TOTAL[$tmp_level_no] = count_person($tmp_level_no, 0, $search_condition, "");
		$arr_tmp_data[] = $LEVEL_TOTAL[$tmp_level_no];
	} // end for
	$arr_tmp_data[] = "";	// แทน count_total
	$arr_tmp_data[] = "";	// แทน count_all
	$arr_tmp_data[] = "";	// แทน percent_total
	for($i=0; $i<count($arr_tmp_data); $i++){ 
		if ($arr_column_sel[$arr_column_map[$i]]==1) 
			if ($arr_tmp_data[$arr_column_map[$i]]) $sum_grand_total += $arr_tmp_data[$arr_column_map[$i]];
	} // end for
//	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
	$GRAND_ALL = count_person(0, 0, $search_condition, "");
	
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
			unset($COUNT_LEVEL);
			$sum_count_total = 0;
			$arr_tmp_data = (array) null;
			$arr_tmp_data[] = "";	// แทน name
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				$COUNT_LEVEL[$tmp_level_no] = $arr_content[$data_count]["level_". $tmp_level_no];
				$arr_tmp_data[] = $COUNT_LEVEL[$tmp_level_no];
			} // end for
			$arr_tmp_data[] = "";	// แทน count_total
			$arr_tmp_data[] = "";	// แทน count_all
			$arr_tmp_data[] = "";	// แทน percent_total
			for($i=0; $i<count($arr_tmp_data); $i++){ 
				if ($arr_column_sel[$arr_column_map[$i]]==1) 
					if ($arr_tmp_data[$arr_column_map[$i]]) $sum_count_total += $arr_tmp_data[$arr_column_map[$i]];
			} // end for
//			$COUNT_TOTAL = array_sum($COUNT_LEVEL);
			$COUNT_ALL = $arr_content[$data_count][count_all];
			
			$PERCENT_TOTAL = 0;
//			if($COUNT_ALL) $PERCENT_TOTAL = ($COUNT_TOTAL / $COUNT_ALL) * 100;
			if($COUNT_ALL) $PERCENT_TOTAL = ($sum_count_total / $COUNT_ALL) * 100;
//			echo "$NAME-->$PERCENT_TOTAL = ($sum_count_total / $COUNT_ALL) * 100<br>";

/*
			if($REPORT_ORDER=="SEX"){
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$COUNT_LEVEL[$tmp_level_no] = ($COUNT_LEVEL[$tmp_level_no]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_LEVEL[$tmp_level_no])):number_format($COUNT_LEVEL[$tmp_level_no])):"-");
				} // end for
				$COUNT_TOTAL = ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-");
				$COUNT_ALL = ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_ALL)):number_format($COUNT_ALL)):"-");				
				$PERCENT_TOTAL = ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-");

				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$COUNT_LEVEL[$tmp_level_no] = ($COUNT_LEVEL[$tmp_level_no]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_LEVEL[$tmp_level_no])):number_format($COUNT_LEVEL[$tmp_level_no])):"");
				} // end for
				$COUNT_TOTAL = ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"");
				$COUNT_ALL = ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_ALL)):number_format($COUNT_ALL)):"");	
				$PERCENT_TOTAL = ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"");

				$border = "";
				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if
*/
			$arr_data = (array) null;
			$arr_data[] = $NAME;
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$arr_data[] = $COUNT_LEVEL[$tmp_level_no];
			} // end for
			$arr_data[] = $sum_count_total;
			$arr_data[] = $COUNT_ALL;
			$arr_data[] = $PERCENT_TOTAL;
			
			if($REPORT_ORDER=="SEX"){
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}else{
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if
		} // end for
				
		$PERCENT_TOTAL = 0;
		if($GRAND_ALL) $PERCENT_TOTAL = ($sum_grand_total / $GRAND_ALL) * 100;

		$arr_data = (array) null;
		$arr_data[] = "รวม";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				$arr_data[] = $LEVEL_TOTAL[$tmp_level_no];
		} // end for
		$arr_data[] = $sum_grand_total;
		$arr_data[] = $GRAND_ALL;
		$arr_data[] = $PERCENT_TOTAL;

		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
	
?>