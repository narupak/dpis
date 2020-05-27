<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("rpt_R010006_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}


	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (a.POS_ID >= 0 or a.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = 1)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
//	$arr_search_condition[] = "(g.PT_GROUP = '3')";
//	$arr_search_condition[] = "(a.LEVEL_NO in ('M1', 'M2','D2'))";

//	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  $f_all = true; else $f_all = false;
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	
	
	$list_type_text = $ALL_REPORT_TITLE;
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);

	$select_list = "";
	$group_by = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
		case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME";	

				if($group_by) $group_by .= ", ";
				$group_by .= "d.ORG_ID_REF, e.ORG_NAME "; 
				
				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
	//	select :: 	a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME
	//	group :: 	a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME
	//	order ::	d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME ";

				if($group_by) $group_by .= ", ";
				$group_by .= "a.DEPARTMENT_ID, d.ORG_NAME "; 
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break; 
		}
	}

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
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type)){
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
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	
		//ค้นหาจาก
		if(trim($search_pt_name)){
			//ประเภทตำแหน่ง
			$list_type_text .= " ตำแหน่งประเภท$search_pt_name";
			$arr_search_condition[] = "(j.LEVEL_NO LIKE '%$search_pt_code%')";
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
///	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R010005_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "รายงานประวัติวุฒิการศึกษาข้าราชการ";
	if($search_pt_name){
		$report_title .= " ตำแหน่งประเภท$search_pt_name";
	}
	if($search_pl_name){
		$report_title .= " ตำแหน่งในสายงาน$search_pl_name";
	}
	$report_code = "H06";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	if($DPISDB=="odbc"){	
		$cmd = " select		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
										b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
										LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,
										a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, j.LEVEL_NAME, j.POSITION_LEVEL
					   from		
										(
											(
												(
													(
														(
															(
																(
																	PER_PERSONAL a
																	inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
														) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
													) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
											) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
										) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
					  $search_condition
					   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
											LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10),
											a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, j.LEVEL_NAME, j.POSITION_LEVEL, j.LEVEL_SEQ_NO
						order by	$order_by, h.PM_SEQ_NO, j.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
									a.PER_NAME, a.PER_SURNAME, a.PER_SALARY desc, LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
									b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
									SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE,
									a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, j.LEVEL_NAME, j.POSITION_LEVEL
					   from		PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_MGT h, 
									PER_PRENAME i, PER_LEVEL j
					   where	a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
									and b.PL_CODE=f.PL_CODE  and b.PM_CODE=h.PM_CODE(+) and a.PN_CODE=i.PN_CODE(+)	and  a.LEVEL_NO=j.LEVEL_NO(+) 
									$search_condition
					   group by	a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
									b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 
									SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_RETIREDATE), 1, 10),
									a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, j.LEVEL_NAME, j.POSITION_LEVEL, j.LEVEL_SEQ_NO
					   order by	$order_by, h.PM_SEQ_NO, j.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
									a.PER_NAME, a.PER_SURNAME, a.PER_SALARY desc, 
									SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
										b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
										LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,
										a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, j.LEVEL_NAME, j.POSITION_LEVEL
					   from		
										(
											(
												(
													(
														(
															(
																(
																	PER_PERSONAL a
																	inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
														) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
													) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
											) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
										) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
					  $search_condition
					   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
											b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
											LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10),
											a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, j.LEVEL_NAME, j.POSITION_LEVEL, j.LEVEL_SEQ_NO
						order by	$order_by h.PM_SEQ_NO, j.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
									a.PER_NAME, a.PER_SURNAME, a.PER_SALARY desc, LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
	} // end if 
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$MINISTRY_ID = -1;
	$DEPARTMENT_ID = -1;
	$cnt_rpt_order = count($arr_rpt_order);
	while($data = $db_dpis->get_array()){		
		if($MINISTRY_ID != $data[MINISTRY_ID]){
			$MINISTRY_ID = $data[MINISTRY_ID];
			$MINISTRY_NAME = $data[MINISTRY_NAME];
			
			if($f_all){
				$arr_content[$data_count][type] = "MINISTRY";
				$arr_content[$data_count][name] = $MINISTRY_NAME;
	
				$data_row = 0;
				$data_count++;
			}
		} // end if
		
		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			
			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][name] = str_repeat(" ", (($cnt_rpt_order - 1) * 5)) . $DEPARTMENT_NAME;

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
				
		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = $data[PL_NAME];
		$PM_CODE = trim($data[PM_CODE]);
		$PM_NAME = $data[PM_NAME];
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

		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = $data[PT_NAME];
		
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
		$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
		if($PER_RETIREDATE){
			$arr_temp = explode("-", $PER_RETIREDATE);
			$PER_RETIRE_YEAR = ($arr_temp[0] + 543);
		}

		$PER_SALARY = $data[PER_SALARY];
		
		$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);

		$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POSITION_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);
		
		//หาประวัติวุฒิการศึกษา
		$EN_NAME = "";	$EM_NAME = "";	$INS_NAME = "";	$CT_NAME = "";
		if($DPISDB=="odbc"){
			$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME,f.EL_NAME
					from			((((PER_EDUCATE a
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
								) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
								) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
							)left join PER_EDUCLEVEL f on (b.EL_CODE=f.EL_CODE)
					 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'
					 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
		}elseif($DPISDB=="oci8"){
			$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME,f.EL_NAME
					from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e,PER_EDUCLEVEL f
					where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and
								a.EN_CODE=b.EN_CODE(+) and 
								a.EM_CODE=c.EM_CODE(+) and 
								b.EL_CODE=f.EL_CODE(+) and
								a.INS_CODE=d.INS_CODE(+) and 
								d.CT_CODE=e.CT_CODE(+)
					order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
		}elseif($DPISDB=="mysql"){
			$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME,f.EL_NAME
					from			(((PER_EDUCATE a
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
								) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
								) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
					 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'
					 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";			
		} // end if
		$count_educatehis = $db_dpis2->send_cmd($cmd);
		if($count_educatehis){
			$educatehis_count = 0;
			while($data2 = $db_dpis2->get_array()){
				$educatehis_count++;
				if ($EN_NAME[$PER_ID])
					$EN_NAME[$PER_ID] .= "*Enter*".trim($data2[EN_NAME]);
				else
					$EN_NAME[$PER_ID] .= trim($data2[EN_NAME]);
				if ($EM_NAME[$PER_ID])
					$EM_NAME[$PER_ID] .= "*Enter*".trim($data2[EM_NAME]);
				else
					$EM_NAME[$PER_ID] .= trim($data2[EM_NAME]);
				if ($EL_NAME[$PER_ID])
					$EL_NAME[$PER_ID] .= "*Enter*".trim($data2[EL_NAME]);
				else
					$EL_NAME[$PER_ID] .= trim($data2[EL_NAME]);
				if ($INS_NAME[$PER_ID])
					$INS_NAME[$PER_ID] .= "*Enter*".trim($data2[INS_NAME]);
				else
					$INS_NAME[$PER_ID] .= trim($data2[INS_NAME]);
				if ($CT_NAME[$PER_ID])
					$CT_NAME[$PER_ID] .= "*Enter*".trim($data2[CT_NAME]);
				else
					$CT_NAME[$PER_ID] .= trim($data2[CT_NAME]);
			} //end while
		} else {
			if($DPISDB=="odbc"){
				$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME,f.EL_NAME
						from			((((PER_EDUCATE a
									left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
									) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
									) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
									) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
								)left join PER_EDUCLEVEL f on (b.EL_CODE=f.EL_CODE)
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME,f.EL_NAME
						from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e,PER_EDUCLEVEL f
						where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' and
									a.EN_CODE=b.EN_CODE(+) and 
									a.EM_CODE=c.EM_CODE(+) and 
									b.EL_CODE=f.EL_CODE(+) and
									a.INS_CODE=d.INS_CODE(+) and 
									d.CT_CODE=e.CT_CODE(+)
						order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME,f.EL_NAME
						from			(((PER_EDUCATE a
									left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
									) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
									) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
									) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";			
			} // end if
			$count_educatehis = $db_dpis2->send_cmd($cmd);
			if($count_educatehis){
				$educatehis_count = 0;
				while($data2 = $db_dpis2->get_array()){
					$educatehis_count++;
					if ($EN_NAME[$PER_ID])
						$EN_NAME[$PER_ID] .= "*Enter*".trim($data2[EN_NAME]);
					else
						$EN_NAME[$PER_ID] .= trim($data2[EN_NAME]);
					if ($EM_NAME[$PER_ID])
						$EM_NAME[$PER_ID] .= "*Enter*".trim($data2[EM_NAME]);
					else
						$EM_NAME[$PER_ID] .= trim($data2[EM_NAME]);
					if ($EL_NAME[$PER_ID])
						$EL_NAME[$PER_ID] .= "*Enter*".trim($data2[EL_NAME]);
					else
						$EL_NAME[$PER_ID] .= trim($data2[EL_NAME]);
					if ($INS_NAME[$PER_ID])
						$INS_NAME[$PER_ID] .= "*Enter*".trim($data2[INS_NAME]);
					else
						$INS_NAME[$PER_ID] .= trim($data2[INS_NAME]);
					if ($CT_NAME[$PER_ID])
						$CT_NAME[$PER_ID] .= "*Enter*".trim($data2[CT_NAME]);
					else
						$CT_NAME[$PER_ID] .= trim($data2[CT_NAME]);
				} //end while
			} //end if
		} //end if

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = "$data_row." .str_repeat(" ", 3) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][pl_name] = $PL_NAME . $POSITION_LEVEL;
		$arr_content[$data_count][pm_name] = $PM_NAME;
		$arr_content[$data_count][el_name]   = $EL_NAME[$PER_ID];
		$arr_content[$data_count][en_name]  = $EN_NAME[$PER_ID];
		$arr_content[$data_count][em_name] = $EM_NAME[$PER_ID];
		$arr_content[$data_count][retireyear] = $PER_RETIRE_YEAR;
	
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

//new format****************************************************************
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
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$EL_NAME = $arr_content[$data_count][el_name];
			$EN_NAME = $arr_content[$data_count][en_name];
			$EM_NAME = $arr_content[$data_count][em_name];
			$RETIREYEAR = $arr_content[$data_count][retireyear];

			if($REPORT_ORDER == "MINISTRY"){
				$ORG_NAME = $arr_content[$data_count][name];
            	$arr_data = (array) null;
				$arr_data[] ="$NAME";
				$arr_data[] =  "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] ="";
				$arr_data[] ="";
            	$arr_data[] ="";
		    	
				$data_align = array("L", "L", "L", "L", "C", "C", "C");

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
			}elseif($REPORT_ORDER == "DEPARTMENT"){
				if (trim($NAME)) {
					$ORG_NAME = $arr_content[$data_count][name];
					$arr_data = (array) null;
					$arr_data[] ="$NAME";
					$arr_data[] =  "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] ="";
					$arr_data[] ="";
					$arr_data[] ="";
					
					$data_align = array("L", "L", "L", "L", "C", "C", "C");

					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}
			}elseif($REPORT_ORDER == "CONTENT"){
            	$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] =  $PL_NAME;
				$arr_data[] =  $PM_NAME;
				$arr_data[] = $EL_NAME;
				$arr_data[] = $EN_NAME;
				$arr_data[] = $EM_NAME;       
            	$arr_data[] = $RETIREYEAR;
		    	
				$data_align = array("L", "L", "L", "L", "L", "L", "C");

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