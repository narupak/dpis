<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	
	include ("rpt_R010008_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	 if($search_edu) { $search_edu.= ' or '; }
	$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; } 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_pt_code == 'O') $search_pt_name = "ทั่วไป";
	elseif($search_pt_code == 'K') $search_pt_name = "วิชาการ";
	elseif($search_pt_code == 'D') $search_pt_name = "อำนวยการ";
	elseif($search_pt_code == 'M') $search_pt_name = "บริหาร";

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  $f_all = true; else $f_all = false;

	if(trim($RPTORD_LIST)=="SEX"){	//เลือกแยกตามเพศมาเพียงอันเดียวเท่านั้น
		$RPTORD_LIST = "ORG|".$RPTORD_LIST;
	}
	//echo $RPTORD_LIST;
	if(!trim($RPTORD_LIST)){ 	//กรณีไม่มีตัวเลือกแยกประเภทมาเลย
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
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	//print_r($arr_rpt_order);

	//แยกตามเงื่อนไขที่เลือก
	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_ID_REF";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

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
			case "SEX" :											//แยกตามเพศ+ระดับการศึกษา
				$set_header="SEX";
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PER_GENDER";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.PER_GENDER";
				
				$heading_name .= " และเพศ";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "g.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "g.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";
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
			$arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
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
			$arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
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
			$arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
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
				$list_type_text .= " / ตำแหน่งในสายงาน : $search_pl_name";
			}
		}elseif($search_per_type==2){
			if(trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
				$list_type_text .= " / ตำแหน่งในสายงาน : $search_pn_name";
			}
		}elseif($search_per_type==3){
			if(trim($search_ep_code)){
				$search_ep_code = trim($search_ep_code);
				$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
				$list_type_text .= " / ตำแหน่งในสายงาน : $search_ep_name";
			}
		} // end if
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE_EDU) = '$search_ct_code')";
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
			$arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R010008_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";

	$report_title = "จำนวน$PERSON_TYPE[$search_per_type] จำแนกตามระดับการศึกษา $heading_name";
	$report_title .= " ประจำปีงบประมาณ  พ.ศ. $search_year";
    if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);

	$report_code = "H08";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	function count_person($education_level, $PER_GENDER, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $search_per_type, $select_org_structure, $search_edu;
		global $EDU_TYPE_TXT;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		if($education_level == 1) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('1'))";
		elseif($education_level == 2) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('2'))";
		elseif($education_level == 3) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('3'))";
		elseif($education_level == 4) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('4'))";
		if($PER_GENDER){ 
			$search_condition .= (trim($search_condition)?" and ":" where ") . " (b.PER_GENDER=$PER_GENDER) ";
		}
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
							 from		(	
							 					(
													(
														(
															$pos_tb a 
															left join PER_PERSONAL b on ($join_tb) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							 $search_condition  and ($search_edu)
							 group by	b.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(b.PER_ID) as count_person
							 from			$pos_tb a, PER_PERSONAL b, PER_ORG c, PER_EDUCATE d, PER_EDUCLEVEL f, PER_ORG g
							 where		$join_tb(+) and a.ORG_ID=c.ORG_ID(+) and trim(d.EL_CODE)=trim(f.EL_CODE(+)) and  a.DEPARTMENT_ID=g.ORG_ID(+)
												and b.PER_ID=d.PER_ID  and ($search_edu) 
												$search_condition
							 group by	b.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(b.PER_ID) as count_person
							 from		(	
							 					(
													(
														(
															$pos_tb a 
															left join PER_PERSONAL b on ($join_tb) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							 $search_condition  and ($search_edu)
							 group by	b.PER_ID ";
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
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE,$PER_GENDER;
				
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
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER or a.PER_GENDER is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE,$PER_GENDER;
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
				case "SEX" :
					$PER_GENDER = -1;
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
		//พนักงานราชการ
		$pos_tb="PER_POS_EMPSER";
		$join_tb="a.POEMS_ID=b.POEMS_ID";
	}
	if($DPISDB=="odbc"){	
			$cmd = "select			distinct $select_list
							from		(
							 					(
													(
														(
															$pos_tb a 
															left join PER_PERSONAL b on ($join_tb) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition  and ($search_edu)
							 	order by		$order_by ";
	}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			$pos_tb a, PER_PERSONAL b, PER_ORG c, PER_EDUCATE d, PER_EDUCLEVEL f, PER_ORG g
							 where		$join_tb(+) and a.ORG_ID=c.ORG_ID(+) and trim(d.EL_CODE)=trim(f.EL_CODE(+)) and a.DEPARTMENT_ID=g.ORG_ID(+)
							 					and b.PER_ID=d.PER_ID and ($search_edu) 
												$search_condition
							 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
			$cmd = "select			distinct $select_list
							from		(
							 					(
													(
														(
															$pos_tb a 
															left join PER_PERSONAL b on ($join_tb) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition  and ($search_edu)
							 	order by		$order_by ";
	}

	if($select_org_structure==1){
		 $cmd = str_replace("a.ORG_ID", "b.ORG_ID", $cmd);	
		 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>$cmd ($count_data)<br>";
//	$db_dpis->show_error();

//	print_r($search_condition);
	$data_count = 0;
	$GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = $GRAND_TOTAL_4 = 0;
	$GRAND_TOTAL_5 = $GRAND_TOTAL_6 = $GRAND_TOTAL_7 = $GRAND_TOTAL_8 = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
//		echo "arr_rpt_order=".implode(",",$arr_rpt_order)."<br>";
		$small_REPORT_ORDER = $arr_rpt_order[count($arr_rpt_order)-1];	// ระดับต่ำสุด ไม่รวม SEX
		if ($small_REPORT_ORDER=="SEX") $small_REPORT_ORDER = $arr_rpt_order[count($arr_rpt_order)-2]; // ไม่เอา SEX
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
//					echo "MINISTRY_ID($MINISTRY_ID) != data_MINISTRY_ID(".trim($data[MINISTRY_ID]).")   [".($MINISTRY_ID != trim($data[MINISTRY_ID]))."]<br>";
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){
//						echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++>>> in MINISTRY<br>";
						$MINISTRY_ID = trim($data[MINISTRY_ID]);
						if($MINISTRY_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							$MINISTRY_SHORT = $data2[ORG_SHORT];
						} // end if

if($f_all){
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
						$arr_content[$data_count][short_name] = $MINISTRY_SHORT;
	
						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							if ($small_REPORT_ORDER=="MINISTRY") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							}
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							if ($small_REPORT_ORDER=="MINISTRY") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
								$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
							}
						}
					
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
}					
					} // end if
				break;

				case "DEPARTMENT" :
//					echo "DEPARTMENT_ID($DEPARTMENT_ID) != data_DEPARTMENT_ID(".trim($data[DEPARTMENT_ID]).")   [".($DEPARTMENT_ID != trim($data[DEPARTMENT_ID]))."]<br>";
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
//						echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++>>> in DEPARTMENT<br>";
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
						if($DEPARTMENT_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
							$DEPARTMENT_ORG_SHORT = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
						$arr_content[$data_count][short_name] = $DEPARTMENT_ORG_SHORT;

						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							if ($small_REPORT_ORDER=="DEPARTMENT") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							}
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							if ($small_REPORT_ORDER=="DEPARTMENT") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
								$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
							}
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "ORG" :
//					echo "ORG_ID($ORG_ID) != data_ORG_ID(".trim($data[ORG_ID]).")   [".($ORG_ID != trim($data[ORG_ID]))."]<br>";
					if($ORG_ID != trim($data[ORG_ID])){
//						echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++>>> in ORG<br>";
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][short_name] = $ORG_SHORT;

						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							if ($small_REPORT_ORDER=="ORG") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							}
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							if ($small_REPORT_ORDER=="ORG") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
								$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
							}
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				
				case "LINE" :
//					echo "PL_CODE($PL_CODE) != data_PL_CODE(".trim($data[PL_CODE]).")  [".($PL_CODE != trim($data[PL_CODE]))."]<br>";
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

						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							if ($small_REPORT_ORDER=="LINE") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							}
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							if ($small_REPORT_ORDER=="LINE") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
								$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
							}
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while

	if($set_header=="SEX"){
		$GRAND_TOTAL_M = ($GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4);	//ชาย
		$GRAND_TOTAL_F = ($GRAND_TOTAL_5+ $GRAND_TOTAL_6 + $GRAND_TOTAL_7 + $GRAND_TOTAL_8);		//หญิง
	}else{
		$GRAND_TOTAL = ($GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4);
	}
//	$GRAND_TOTAL = count_person(0, $search_condition, "");
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
			$NAME = $arr_content[$data_count][name];
//			echo "$data_count..REPORT_ORDER=".$REPORT_ORDER.", NAME=$NAME<br>";
			if($set_header=="SEX"){
				//ชาย
				$COUNT_1 = $arr_content[$data_count][count_1];
				$COUNT_2 = $arr_content[$data_count][count_2];
				$COUNT_3 = $arr_content[$data_count][count_3];
				$COUNT_4 = $arr_content[$data_count][count_4];
				$COUNT_TOTAL_M = ($COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4);
				//หญิง
				$COUNT_5 = $arr_content[$data_count][count_5];
				$COUNT_6 = $arr_content[$data_count][count_6];
				$COUNT_7 = $arr_content[$data_count][count_7];
				$COUNT_8 = $arr_content[$data_count][count_8];
				$COUNT_TOTAL_F = ($COUNT_5 + $COUNT_6 + $COUNT_7 + $COUNT_8);

				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $COUNT_3;
				$arr_data[] = $COUNT_4;
				$arr_data[] = $COUNT_TOTAL_M;
				$arr_data[] = $COUNT_5;
				$arr_data[] = $COUNT_6;
				$arr_data[] = $COUNT_7;
				$arr_data[] = $COUNT_8;
				$arr_data[] = $COUNT_TOTAL_F;
				$arr_data[] = ($COUNT_TOTAL_M+$COUNT_TOTAL_F);

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}else{
				$COUNT_1 = $arr_content[$data_count][count_1];
				$COUNT_2 = $arr_content[$data_count][count_2];
				$COUNT_3 = $arr_content[$data_count][count_3];
				$COUNT_4 = $arr_content[$data_count][count_4];
				$COUNT_TOTAL = ($COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4);
				
				$PERCENT_1 = $PERCENT_2 = $PERCENT_3 = $PERCENT_4 = $PERCENT_TOTAL = 0;
				if($COUNT_TOTAL){ 
					$PERCENT_1 = ($COUNT_1 / $COUNT_TOTAL) * 100;
					$PERCENT_2 = ($COUNT_2 / $COUNT_TOTAL) * 100;
					$PERCENT_3 = ($COUNT_3 / $COUNT_TOTAL) * 100;
					$PERCENT_4 = ($COUNT_4 / $COUNT_TOTAL) * 100;
				} // end if
				
				if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $PERCENT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $PERCENT_2;
				$arr_data[] = $COUNT_3;
				$arr_data[] = $PERCENT_3;
				$arr_data[] = $COUNT_4;
				$arr_data[] = $PERCENT_4;
				$arr_data[] = $COUNT_TOTAL;
				$arr_data[] = $PERCENT_TOTAL;

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}	//end else
		} // end for

		//รวมทั้งหมด		
		$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = $PERCENT_TOTAL_4 = 0;
		if($GRAND_TOTAL){ 
			$PERCENT_TOTAL_1 = ($GRAND_TOTAL_1 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_4 = ($GRAND_TOTAL_4 / $GRAND_TOTAL) * 100;
		} // end if
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		if($set_header=="SEX"){	
			$arr_data = (array) null;
			$arr_data[] = "รวม";
			$arr_data[] = $GRAND_TOTAL_1;
			$arr_data[] = $GRAND_TOTAL_2;
			$arr_data[] = $GRAND_TOTAL_3;
			$arr_data[] = $GRAND_TOTAL_4;
			$arr_data[] = $GRAND_TOTAL_M;
			$arr_data[] = $GRAND_TOTAL_5;
			$arr_data[] = $GRAND_TOTAL_6;
			$arr_data[] = $GRAND_TOTAL_7;
			$arr_data[] = $GRAND_TOTAL_8;
			$arr_data[] = $GRAND_TOTAL_F;
			$arr_data[] = ($GRAND_TOTAL_M+$GRAND_TOTAL_F);

			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{
			$arr_data = (array) null;
			$arr_data[] = "รวม";
			$arr_data[] = $GRAND_TOTAL_1;
			$arr_data[] = $PERCENT_TOTAL_1;
			$arr_data[] = $GRAND_TOTAL_2;
			$arr_data[] = $PERCENT_TOTAL_2;
			$arr_data[] = $GRAND_TOTAL_3;
			$arr_data[] = $PERCENT_TOTAL_3;
			$arr_data[] = $GRAND_TOTAL_4;
			$arr_data[] = $PERCENT_TOTAL_4;
			$arr_data[] = $GRAND_TOTAL;
			$arr_data[] = $PERCENT_TOTAL;

			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
?>