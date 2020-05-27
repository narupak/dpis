<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");


	ini_set("max_execution_time", $max_execution_time);
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	 	if($search_edu) { $search_edu.= ' or '; }
		$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
	} 
	
//	$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";		//กำหนดค่าเริ่มต้น ต้องแสดงประเทศด้วยทุกครั้ง เริ่ม index ที่ตำแหน่งแรกคือ 0
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
	} else $f_all = false;	

	if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
	if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
	if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);
	//print_r($arr_rpt_order);	exit;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		 $line_title=" สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";		
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";		
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if
	
	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "COUNTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.CT_CODE_EDU as CT_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.CT_CODE_EDU";					

				$heading_name .= " $CT_TITLE";
				break;			
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
				$order_by .= "h.ORG_SEQ_NO, h.ORG_CODE, a.DEPARTMENT_ID ";

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
				
				$select_list .= "$line_code as PL_CODE";
				
				if($order_by) $order_by .= ", ";
				$order_by .= $line_code;
				
				$heading_name .=$line_title;
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.LEVEL_SEQ_NO, a.LEVEL_NO";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "g.LEVEL_SEQ_NO desc, a.LEVEL_NO";

				$heading_name .= " $LEVEL_TITLE";
				break;
			case "SEX" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PER_GENDER";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_GENDER";

				$heading_name .= " $SEX_TITLE";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.PV_CODE";

				$heading_name .= " $PV_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) {
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2"; 	}
		else if($select_org_structure==1){	$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2"; }
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2"; } 	
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(f.EL_TYPE) in ('1', '2', '3', '4'))";
	
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			}			
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
				$list_type_text .= $line_search_name;
		}
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
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	include ("rpt_R002002_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R002002_rtf.rtf";

   //RTF $papersize = "A4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="A4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]จำแนกตามระดับการศึกษา";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);	
	$report_code = "R0202";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	function count_person($education_level, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type, $search_edu,$select_org_structure;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($education_level == 1) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('1'))";
		elseif($education_level == 2) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('2'))";
		elseif($education_level == 3) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('3'))";
		elseif($education_level == 4) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('4'))";
		
		//ตัดเงื่อนไขที่กำหนดไว้ข้างบนออก เพราะมันจะคำนวณค่ามาผิดเอาทั้งหมดมา ไม่เป็นไปตามเงื่อนไขของแต่ละระดับการศึกษา ยกเว้นที่ sum ทั้งหมด
		if($education_level != 0){	$search_condition = str_replace("and (trim(f.EL_TYPE) in ('1', '2', '3', '4'))","", $search_condition);	}

		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from		(	
							 					(
							 						(
														(
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join  
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
							 $search_condition  and ($search_edu)
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCLEVEL f, PER_ORG h
							 where		$position_join and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID(+) and d.INS_CODE=e.INS_CODE(+) and trim(d.EL_CODE)=trim(f.EL_CODE(+)) and a.DEPARTMENT_ID=h.ORG_ID(+)
												$search_condition and ($search_edu) 
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from		(	
							 					(
							 						(
														(
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join  
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID )
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
							 $search_condition  and ($search_edu)
							 group by	a.PER_ID ";
		} // end if
		if($select_org_structure==1){
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
		//echo "$cmd<hr>"; exit;
		//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function count_person

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $LEVEL_NAME , $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $EP_CODE,$CT_CODE,$TP_CODE;
		global $line_code;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "COUNTRY" :
					if($CT_CODE && $CT_CODE !=-1){ $arr_addition_condition[] = "(trim(d.CT_CODE_EDU) = '$CT_CODE')";	}
					else{ $arr_addition_condition[] = "(trim(d.CT_CODE_EDU) = '$CT_CODE' or d.CT_CODE_EDU is null)";	}
				break;
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(h.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else if($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_1" :
					if($select_org_structure==0){
						if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}else if($select_org_structure==1){
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
					if($PL_CODE) $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
				break;
				case "LEVEL" :
					if($LEVEL_NO){ 
						if($DPISDB=="odbc") $arr_addition_condition[] = "(a.LEVEL_NO = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
						elseif($DPISDB=="oci8") $arr_addition_condition[] = "(a.LEVEL_NO = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
						elseif($DPISDB=="mysql") $arr_addition_condition[] = "(a.LEVEL_NO = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
					}else{ 
						if($DPISDB=="odbc") $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
						elseif($DPISDB=="oci8") $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
						elseif($DPISDB=="mysql") $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
					} // end if
				break;
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = 0 or a.PER_GENDER is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE' or a.PV_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function  generate_condition
	
	function initialize_parameter($current_index){
		global $arr_rpt_order, $position_table, $position_join;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE,$PN_CODE, $LEVEL_NO, $LEVEL_NAME , $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $EP_CODE,$CT_CODE,$TP_CODE;
		
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "COUNTRY" :
					$CT_CODE=-1;
				break;
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
					$PN_CODE = -1;
					$EP_CODE = -1;
					$TP_CODE = -1;
				break;
				case "LEVEL" :
					$LEVEL_NO = -1;
				break;
				case "SEX" :
					$PER_GENDER = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function Initialize_parameter

	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from		(	
						 					(
												(
													(
														(	
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join  
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
										) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
											$search_condition  and ($search_edu)
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCLEVEL f, PER_LEVEL g, PER_ORG h
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
						 					and a.PER_ID=d.PER_ID(+) and d.INS_CODE=e.INS_CODE(+) and trim(d.EL_CODE)=trim(f.EL_CODE(+)) and a.LEVEL_NO=g.LEVEL_NO(+) and a.DEPARTMENT_ID=h.ORG_ID(+) 
											$search_condition and ($search_edu)
						 order by $order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(	
						 					(
												(
													(
														(	
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join  
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
										) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
											$search_condition  and ($search_edu)
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";	exit;
	$data_count = 0;
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} = 0;
	while($data = $db_dpis->get_array()){
		
		//echo  "<hr><pre>".trim($data[CT_CODE_EDU])."+".trim($data[DEPARTMENT_ID])."+".trim($data[ORG_ID])."/".trim($data[ORG_ID_1])."/".trim($data[ORG_ID_2])."</pre>";
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "COUNTRY" :				//----ต้องเข้า case นี้เสมอ----//
					if($CT_CODE != trim($data[CT_CODE])){
						$CT_CODE = trim($data[CT_CODE]);
						if($CT_CODE != "" && $CT_CODE!=0 && $CT_CODE!=-1){
							$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$CT_NAME = $data2[CT_NAME];
							$CT_SHORT = $CT_NAME;
						}else{
							$CT_NAME = "[ไม่ระบุประเทศ]";
						}

//						echo "CT_CODE=$CT_CODE, CT_NAME=$CT_NAME<br>";
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "COUNTRY";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $CT_NAME;
						$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index) * 5)) . $CT_SHORT;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);
					
						if($rpt_order_index==0){	////----ต้องเข้า case นี้เสมอ----//		นับรวมจำนวนแค่ที่เดียวพอ
							for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
					
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

				case "MINISTRY" :
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){
						$MINISTRY_ID = trim($data[MINISTRY_ID]);
						if($MINISTRY_ID != "" && $MINISTRY_ID != 0 && $MINISTRY_ID != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							$MINISTRY_SHORT = $data2[ORG_SHORT];
						}else{
							$MINISTRY_NAME = "[ไม่ระบุกระทรวง]";
						}

//						echo "MINISTRY_ID=$MINISTRY_ID, MINISTRY_NAME=$MINISTRY_NAME<br>";
						$addition_condition = generate_condition($rpt_order_index);
	
						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $MINISTRY_NAME;
						$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index) * 5)) . $MINISTRY_SHORT;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);
	
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
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
						}else{
							$DEPARTMENT_NAME = "[ไม่ระบุกรม]";
						}

						$addition_condition = generate_condition($rpt_order_index);
	
						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $DEPARTMENT_NAME;
						$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index) * 5)) . $DEPARTMENT_SHORT;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);
	
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
				} // end if
				break;

				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;

							 if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
									$addition_condition = generate_condition($rpt_order_index);
			
									$arr_content[$data_count][type] = "ORG";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $ORG_NAME;
									$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index) * 5)) . $ORG_SHORT;
									$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
									$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);
			
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
								$data_count++;
								} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
							} // end if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1)
						} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						$ORG_NAME_1 = $ORG_SHORT_1 = "ไม่ระบุ";
						if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							$ORG_SHORT_1 = $data2[ORG_SHORT];
							if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
						} //end if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1)

							if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-")){
								$addition_condition = generate_condition($rpt_order_index);
					
								$arr_content[$data_count][type] = "ORG_1";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $ORG_NAME_1;
								$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index) * 5)) . $ORG_SHORT_1;
								$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
								$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
								$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
								$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);
		
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
								} // end 	if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-"))
						} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != trim($data[ORG_ID_2])){
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						$ORG_NAME_2 = $ORG_SHORT_2 = "ไม่ระบุ";
						if($ORG_ID_2!= "" && $ORG_ID_2 != 0 && $ORG_ID_2 != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							$ORG_SHORT_2 = $data2[ORG_SHORT];
							if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
						} // end if($ORG_ID_2!= "" && $ORG_ID_2 != 0 && $ORG_ID_2 != -1)

							 if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-")){
								$addition_condition = generate_condition($rpt_order_index);
					
								$arr_content[$data_count][type] = "ORG_2";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $ORG_NAME_2;
								$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index) * 5)) . $ORG_SHORT_2;
								$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
								$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
								$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
								$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);
		
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
								} // end 	if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-"))
						} // end if
				break;
		
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							if($search_per_type==1){
								$cmd = " select $line_name as PL_NAME, $line_short_name from $line_table b where trim($line_code)='$PL_CODE' ";
							}else{
								$cmd = " select $line_name as PL_NAME from $line_table b where trim($line_code)='$PL_CODE' ";
							}
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_NAME]);
							if($search_per_type==1){
								$PL_NAME = trim($data2[$line_short_name])?$data2[$line_short_name]:$PL_NAME;
							}

							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $PL_NAME;
							$arr_content[$data_count][short_name] = "";
							$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
							$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);
	
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					} // end if
				break;
		
				case "LEVEL" :
					if($LEVEL_NO != trim($data[LEVEL_NO])){
						$LEVEL_NO = trim($data[LEVEL_NO]);
						$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSITION_TYPE = trim($data2[POSITION_TYPE]);
						$POSITION_LEVEL = trim($data2[POSITION_LEVEL]);
						if ($POSITION_LEVEL=="ต้น" || $POSITION_LEVEL=="สูง") $POSITION_LEVEL = $POSITION_TYPE.$POSITION_LEVEL;

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . (trim($POSITION_LEVEL)?"".$POSITION_LEVEL:"[ไม่ระบุระดับตำแหน่ง]");
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "SEX" :
					if($PER_GENDER != trim($data[PER_GENDER])){
						$PER_GENDER = trim($data[PER_GENDER]);
						if(!$PER_GENDER) $GENDER_NAME = "[ไม่ระบุเพศ]";
						elseif($PER_GENDER==1) $GENDER_NAME = "ชาย";
						elseif($PER_GENDER==2) $GENDER_NAME = "หญิง";

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $GENDER_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "PROVINCE" :
					if($PV_CODE != trim($data[PV_CODE])){
						$PV_CODE = trim($data[PV_CODE]);
						if($PV_CODE != ""&& $PV_CODE!=0){
							$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PV_NAME = $data2[PV_NAME];

							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "PROVINCE";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index) * 5)) . $PV_NAME;
							$arr_content[$data_count][short_name] = "";
							$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
							$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);
	
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					} // end if
				break;
			} // end switch case
		} // end for
		} // end if
	} // end while

	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4;

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
			
		if($count_data){
		
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$NAME = $arr_content[$data_count][name];
				$COUNT_1 = $arr_content[$data_count][count_1];
				$COUNT_2 = $arr_content[$data_count][count_2];
				$COUNT_3 = $arr_content[$data_count][count_3];
				$COUNT_4 = $arr_content[$data_count][count_4];
				$COUNT_TOTAL = $COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4;
				
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
			} // end for

			$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = $PERCENT_TOTAL_4 = 0;
			if($GRAND_TOTAL){ 
				$PERCENT_TOTAL_1 = ($GRAND_TOTAL_1 / $GRAND_TOTAL) * 100;
				$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2 / $GRAND_TOTAL) * 100;
				$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3 / $GRAND_TOTAL) * 100;
				$PERCENT_TOTAL_4 = ($GRAND_TOTAL_4 / $GRAND_TOTAL) * 100;
			} // end if
			$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;
	
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

			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
		} // end if
		$RTF->close_tab(); 
//		$RTF->close_section(); 
	
		$RTF->display($fname);
?>