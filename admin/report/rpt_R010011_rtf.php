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
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$line_name = "g.PL_NAME";
		$line_code= "b.PL_CODE";
		$position_no= "b.POS_NO";
		$mgt_code ="b.PM_CODE";
		$select_mgt_code =",b.PM_CODE";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$line_name = "g.PN_NAME";
		$line_code= "b.PN_CODE";
		$position_no= "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$line_name = "g.EP_NAME";
		$line_code= "b.EP_CODE";
		$position_no= "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$line_name = "g.TP_NAME";
		$line_code= "b.TP_CODE";
		$position_no= "b.POT_NO";
	} // end if	
	
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
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	
	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "i.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "i.ORG_ID_REF";

				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "i.ORG_SEQ_NO, i.ORG_CODE, b.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "i.ORG_SEQ_NO, i.ORG_CODE, b.DEPARTMENT_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1)  $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; 

				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE";

				$heading_name .= " สายงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "i.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "i.ORG_SEQ_NO, i.ORG_CODE, b.DEPARTMENT_ID";
		}else{
			 if($select_org_structure == 0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
			 else if($select_org_structure == 1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "i.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "i.ORG_SEQ_NO, i.ORG_CODE, b.DEPARTMENT_ID";
		}else{
			if($select_org_structure == 0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
			else if($select_org_structure == 1) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
		}
	} // end if
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = 1)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
	//เลือกหลักสูตร นบส. หรือเทียบเท่า
	if ($BKK_FLAG==1)
		$arr_search_condition[] = "(trim(d.TR_CODE) in ('2049','3473','4487','5315','5316','5556','5557') or TR_NAME like '%นักบริหารระดับสูง%' or TR_NAME like '%นบส%' or 
															TRN_COURSE_NAME like '%นักบริหารระดับสูง%' or TRN_COURSE_NAME like '%นบส%')";
	else
		$arr_search_condition[] = "(trim(d.TR_CODE) in ('1-140-0001','1000000001','1000000002','1000000003','1000000005','1000000006') or TR_NAME like '%นักบริหารระดับสูง%' or TR_NAME like '%นบส%' or 
															TRN_COURSE_NAME like '%นักบริหารระดับสูง%' or TRN_COURSE_NAME like '%นบส%')";

	$list_type_text = $ALL_REPORT_TITLE;
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(i.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
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
			$arr_search_condition[] = "(i.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_COUNTRY", $list_type) ){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		$list_type_text = "";
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(i.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if

		//ค้นหาจาก
		if(trim($search_pt_name)){
			//ประเภทตำแหน่ง
			$list_type_text .= " ตำแหน่งประเภท$search_pt_name";
			$arr_search_condition[] = "(k.LEVEL_NO LIKE '%$search_pt_code%')";
		}
		//ตำแหน่งในสายงาน
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= " ตำแหน่งในสายงาน$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= " ตำแหน่งในสายงาน$search_pn_name";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	include ("rpt_R010011_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R010011_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "รายชื่อข้าราชการที่ผ่านการอบรมหลักสูตร นบส. หรือเทียบเท่า";
	$report_code = "H11";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	if($DPISDB=="odbc"){
	$cmd = " select		a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME,a.LEVEL_NO,$line_code, $line_name as PL_NAME,
										b.ORG_ID_1, c.ORG_SHORT, c.ORG_NAME,d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 10) as TRN_STARTDATE, 
										LEFT(trim(d.TRN_ENDDATE), 10) as TRN_ENDDATE,e.TR_NAME,k.LEVEL_NAME,k.POSITION_LEVEL,
										$select_list  $select_type_code  $select_mgt_code
					 from	(
									(
										(
											(
												(
													(
														(	
															(	
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
													) left join PER_TRAIN e on (trim(d.TR_CODE)=trim(e.TR_CODE))
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
										) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
									) left join PER_ORG i on (b.DEPARTMENT_ID=i.ORG_ID) 
								) left join PER_LEVEL k on (a.LEVEL_NO=k.LEVEL_NO)
					$search_condition
					order by	$order_by, k.LEVEL_SEQ_NO desc, h.PM_SEQ_NO, LEFT(trim(d.TRN_STARTDATE), 10), $line_code ";
	}elseif($DPISDB=="oci8"){
	$search_condition = str_replace(" where ", " and ", $search_condition);
	$cmd = " select		a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME,a.LEVEL_NO,$line_code, $line_name as PL_NAME,
										b.ORG_ID_1, c.ORG_SHORT, c.ORG_NAME,d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE,
										SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,e.TR_NAME,k.LEVEL_NAME,k.POSITION_LEVEL,
										$select_list  $select_type_code  $select_mgt_code
					 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_MGT h, PER_ORG i, PER_LEVEL k
					 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)  and b.DEPARTMENT_ID=i.ORG_ID(+)
										and a.PER_ID=d.PER_ID and trim(d.TR_CODE)=trim(e.TR_CODE(+))
										and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PM_CODE=h.PM_CODE(+)
										and  a.LEVEL_NO=k.LEVEL_NO(+) 
										$search_condition
					 order by	$order_by, k.LEVEL_SEQ_NO desc, h.PM_SEQ_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), $line_code ";
	}elseif($DPISDB=="mysql"){
	$cmd = " select		a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME,a.LEVEL_NO,$line_code, $line_name as PL_NAME,
										b.ORG_ID_1, c.ORG_SHORT, c.ORG_NAME,d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 10) as TRN_STARTDATE, 
										LEFT(trim(d.TRN_ENDDATE), 10) as TRN_ENDDATE,e.TR_NAME,k.LEVEL_NAME,k.POSITION_LEVEL,
										$select_list  $select_type_code   $select_mgt_code
					 from	(
									(
										(
											(
												(
													(
														(	
															(	
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
													) left join PER_TRAIN e on (trim(d.TR_CODE)=trim(e.TR_CODE))
												) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
											) left join $line_table g on ($line_join)
										) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
									) left join PER_ORG i on (b.DEPARTMENT_ID=i.ORG_ID) 
								) left join PER_LEVEL k on (a.LEVEL_NO=k.LEVEL_NO)
					$search_condition
					order by	$order_by, k.LEVEL_SEQ_NO desc, h.PM_SEQ_NO, LEFT(trim(d.TRN_STARTDATE), 10), $line_code ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>$cmd $count_data<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$MINISTRY_ID = -1;
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($MINISTRY_ID != trim($data[MINISTRY_ID])){
			$MINISTRY_ID = trim($data[MINISTRY_ID]);
			//$MINISTRY_NAME = $data[MINISTRY_NAME];
			if($MINISTRY_ID != ""){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID order by ORG_SEQ_NO ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$MINISTRY_NAME = $data2[ORG_NAME];
			} // end if
			
			$arr_content[$data_count][type] = "MINISTRY";
			$arr_content[$data_count][name] = $MINISTRY_NAME;

			$data_row = 0;
			$data_count++;
		} // end if

		if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
			$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
			//$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			if($DEPARTMENT_ID != ""){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID order by ORG_SEQ_NO ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$DEPARTMENT_NAME = $data2[ORG_NAME];
			} // end if
			
			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][name] = $DEPARTMENT_NAME;

			$data_row = 0;
			$data_count++;
		} // end if

		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$LEVEL_NAME =  trim($data[LEVEL_NAME]);
		$POSITION_LEVEL =  trim($data[POSITION_LEVEL]);
		$PL_NAME = $data[PL_NAME];
		$PER_TRAINING = trim($data[TR_NAME]);
		$PER_TRAINING = str_replace("หลักสูตร", "", $PER_TRAINING);
		if(trim($mgt_code)){
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);
		}
		$TMP_ORG_NAME = $data[ORG_NAME];

		$ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_ORG_NAME_1 = $data2[ORG_NAME];

		if ($PM_NAME=="ผู้ว่าราชการจังหวัด" || $PM_NAME=="รองผู้ว่าราชการจังหวัด" || $PM_NAME=="ปลัดจังหวัด") {
			$PM_NAME .= $TMP_ORG_NAME;
			$PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $PM_NAME); 
		} elseif ($PM_NAME=="นายอำเภอ") {
			$PM_NAME .= $TMP_ORG_NAME_1;
			$PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $PM_NAME); 
		}
		if(trim($type_code)){
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
		}
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][sequence] = "$data_row." ;
		$arr_content[$data_count][name] = str_repeat(" ", 2) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME ) . (trim($PM_NAME)?")":"");
		$arr_content[$data_count][training] = $PER_TRAINING;
				
		$data_count++;
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
			$COUNT = $arr_content[$data_count][sequence] ;
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$TRAINING = $arr_content[$data_count][training];

			if($REPORT_ORDER == "MINISTRY"){
            	$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] =  "$NAME";
				$arr_data[] = "";
				$arr_data[] = "";
		    	
				$data_align = array("L", "L", "L", "C");

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
			}elseif($REPORT_ORDER == "DEPARTMENT"){
				$arr_data = (array) null;
				$arr_data[] ="";
				$arr_data[] = "$NAME";
				$arr_data[] = "";
				$arr_data[] = "";
		    	
				$data_align = array("L", "L", "L", "C");

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}elseif($REPORT_ORDER == "CONTENT"){
				//new format************************************************************			
            	$arr_data = (array) null;
				$arr_data[] = $COUNT;
				$arr_data[] = $NAME;
				$arr_data[] = $POSITION;
				$arr_data[] = $TRAINING;
					    	
				$data_align = array("C", "L", "L", "L");
				
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if	
		} // end for
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);

?>