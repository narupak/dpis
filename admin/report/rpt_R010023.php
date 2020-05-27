<?
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
//	$arr_search_condition[] = "(a.PER_STATUS = 1)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$where = "";
	$arr_level_no_condi = (array) null;
	foreach ($LEVEL_NO as $level_no)
	{
		if ($level_no) $arr_level_no_condi[] = "'$level_no'";
//		echo "level_no=$level_no<br>";
	}
	if (count($arr_level_no_condi) > 0) $where = "and (trim(a.LEVEL_NO) in (".implode(",",$arr_level_no_condi)."))";
	if ($search_level_no) $arr_search_condition[] = "(trim(a.LEVEL_NO) = '$search_level_no')";

//	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  $f_all = true; else $f_all = false;
//	$list_type_text = $ALL_REPORT_TITLE;
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
				$select_list .= "d.ORG_ID_REF as MINISTRY_ID";	

				if($group_by) $group_by .= ", ";
				$group_by .= "d.ORG_ID_REF "; 
				
				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			//	select :: a.DEPARTMENT_ID as DEPARTMENT_ID,d.ORG_ID_REF as MINISTRY_ID
			//	order :: d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.DEPARTMENT_ID ";

				if($group_by) $group_by .= ", ";
				$group_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID "; 
				
				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";

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
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type)){
		$list_type_text = "";
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= " $PL_TITLE $search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= " $POS_EMP_TITLE $search_pn_name";
		}elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(b.EP_CODE)='$search_ep_code')";
			$list_type_text .= " $POS_EMPSER_TITLE $search_ep_name";
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
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "บัญชีรายชื่อ$PERSON_TYPE[$search_per_type]แสดงระยะเวลาการดำรงตำแหน่ง $MINISTRY_NAME $DEPARTMENT_NAME";
//	$report_title .= " ประจำปีงบประมาณ  พ.ศ. $search_year";
	$report_code = "H23";
	include ("rpt_R010023_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
		
	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_R010023_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='L';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
	} else {
		$unit="mm";
		$paper_size="A3";
		$lang_code="TH";
		$orientation='L';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	if($DPISDB=="odbc"){ 
		$select_effectivedate="LEFT(trim(f.POH_EFFECTIVEDATE), 10)";
	}elseif($DPISDB=="oci8"){
		$select_effectivedate="SUBSTR(trim(f.POH_EFFECTIVEDATE), 1, 10)";
	}elseif($DPISDB=="mysql"){
		$select_effectivedate="LEFT(trim(f.POH_EFFECTIVEDATE), 10)";
	} 

	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, POS_NO_NAME, POS_NO, 
											b.ORG_ID, c.ORG_NAME, $select_list,
											b.PL_CODE, b.PM_CODE, f.PM_NAME, g.PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY, 
											LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, 
											LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_CARDNO
						 from			(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join PER_POSITION b on (a.POS_ID=b.POS_ID)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
															) left join PER_PRENAME e on (a.PN_CODE=e.PN_CODE)
														) left join PER_MGT f on (b.PM_CODE=f.PM_CODE)
													) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
												) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
											) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											$search_condition
						 order by		$order_by, i.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, f.PM_SEQ_NO, 
									a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_STARTDATE), 10) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, POS_NO_NAME, POS_NO, 
											b.ORG_ID, c.ORG_NAME, $select_list,
											b.PL_CODE, b.PM_CODE, f.PM_NAME, g.PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY, 
											SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE, 
											SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, a.PER_CARDNO
						 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_PRENAME e, PER_MGT f, PER_LINE g, PER_TYPE h, PER_LEVEL i
						where		a.POS_ID=b.POS_ID(+) 
											and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=d.ORG_ID(+) and a.PN_CODE=e.PN_CODE(+)
											and b.PM_CODE=f.PM_CODE(+) and b.PL_CODE=g.PL_CODE(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+)
											$search_condition
						 order by		$order_by, i.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, f.PM_SEQ_NO, 
									NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY'), SUBSTR(trim(a.PER_STARTDATE), 1, 10) ";		   
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, e.PN_NAME, a.PER_NAME, a.PER_SURNAME, POS_NO_NAME, POS_NO, 
											b.ORG_ID, c.ORG_NAME, $select_list,
											b.PL_CODE, b.PM_CODE, f.PM_NAME, g.PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, b.PT_CODE, h.PT_NAME, a.PER_SALARY, 
											LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,
											LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, a.PER_CARDNO
						 from			(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join PER_POSITION b on (a.POS_ID=b.POS_ID)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
															) left join PER_PRENAME e on (a.PN_CODE=e.PN_CODE)
														) left join PER_MGT f on (b.PM_CODE=f.PM_CODE)
													) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
												) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
											) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											$search_condition
						 order by		$order_by, i.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, f.PM_SEQ_NO, 
									a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_STARTDATE), 10) ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	 //echo "<br>$cmd<br>";
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$MINISTRY_ID = -1;
	$DEPARTMENT_ID = -1;
	$cnt_rpt_order = count($arr_rpt_order);
	while($data = $db_dpis->get_array()){		
		if($MINISTRY_ID != trim($data[MINISTRY_ID])){
			$MINISTRY_ID = trim($data[MINISTRY_ID]);
			if($MINISTRY_ID != ""){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$MINISTRY_NAME = $data2[ORG_NAME];
			} // end if
			
			if($f_all){	
				$arr_content[$data_count][type] = "MINISTRY";
				$arr_content[$data_count][name] =  $MINISTRY_NAME;
	
				$data_row = 0;
				$data_count++;
			}		
		} // end if
		
		if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
			$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
			if($DEPARTMENT_ID != ""){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$DEPARTMENT_NAME = $data2[ORG_NAME];
			} // end if
			
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
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$POS_NO_NAME = trim($data[POS_NO_NAME]);
		$POS_NO = trim($data[POS_NO]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		//หาชื่อระดับตำแหน่ง และตำแหน่งประเภท
		$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' order by LEVEL_SEQ_NO";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = $data2[LEVEL_NAME];
		$POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = trim($data[PL_NAME]);
		$PM_CODE = trim($data[PM_CODE]);
		$PM_NAME = trim($data[PM_NAME]);

		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = $data[PT_NAME];
		$ORG_NAME = $data[ORG_NAME];
		$PER_SALARY = $data[PER_SALARY];

		//หาวุฒิการศึกษาสูงสุด
		if($DPISDB=="odbc"){
			$cmd = " 	select		MAX(a.EDU_SEQ), MAX(a.EDU_ENDYEAR), MAX(f.EL_NAME) as EL_NAME
							from		((((PER_EDUCATE a
							left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
							) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
							) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
							) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
							)left join PER_EDUCLEVEL f on (b.EL_CODE=f.EL_CODE)
				 			where		a.PER_ID=$PER_ID and (a.EDU_TYPE like '%4%' or a.EDU_TYPE like '%2%')
				 			order by	MAX(a.EDU_SEQ) desc, MAX(a.EDU_ENDYEAR) desc ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		MAX(a.EDU_SEQ), MAX(a.EDU_ENDYEAR), MAX(f.EL_NAME) as EL_NAME
					from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e,PER_EDUCLEVEL f
					where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and
							a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and 
							b.EL_CODE=f.EL_CODE(+) and a.INS_CODE=d.INS_CODE(+) and 
							d.CT_CODE=e.CT_CODE(+)
					order by		MAX(a.EDU_SEQ) desc, MAX(a.EDU_ENDYEAR) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " 	select		MAX(a.EDU_SEQ), MAX(a.EDU_ENDYEAR), MAX(f.EL_NAME) as EL_NAME
							from		((((PER_EDUCATE a
							left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
							) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
							) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
							) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
							)left join PER_EDUCLEVEL f on (b.EL_CODE=f.EL_CODE)
				 			where		a.PER_ID=$PER_ID and (a.EDU_TYPE like '%4%' or a.EDU_TYPE like '%2%')
				 			order by	MAX(a.EDU_SEQ) desc, MAX(a.EDU_ENDYEAR) desc ";
		} // end if
		$db_dpis2->send_cmd($cmd);
		//echo "<br>$cmd<br>";
		//$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$EL_NAME = trim($data2[EL_NAME]);
		//--------------------
		
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		if(trim($PER_BIRTHDATE)){
		    $FULL_AGE = date_difference(date("Y-m-d"), $PER_BIRTHDATE, "full");
			$PER_AGE = floor(date_difference(date("Y-m-d"), $PER_BIRTHDATE, "year"));
			$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,1);
		}
		
		//หาวันเกษียณอายุ
		$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],1);
		$PER_STARTDATE = trim($data[PER_STARTDATE]);
		if(trim($PER_STARTDATE)){
			$PER_WORKAGE = floor(date_difference(date("Y-m-d"), $PER_STARTDATE, "year"));
			$PER_STARTDATE = show_date_format($PER_STARTDATE,1);
		} // end if

		//หาวันเลื่อนระดับ
		if ($MFA_FLAG==1 && $PM_CODE) 
			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$LEVEL_NO' and PM_CODE='$PM_CODE' and  
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		else
			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$LEVEL_NO' and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		// echo "$cmd<br><br>";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$LEVEL_DATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
		$tmp_date = explode("-", $data2[POH_EFFECTIVEDATE]);
		$TEMP_ENDDATE = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
		$POH_ENDDATE = date("Y-m-d", $TEMP_ENDDATE);

		//หาวันดำรงตำแหน่งปัจจุบัน
		$cmd = " select POH_EFFECTIVEDATE, POH_ENDDATE, POH_PL_NAME, POH_PM_NAME, POH_LEVEL_NO, POH_ORG1, POH_ORG2, POH_ORG3
						from   PER_POSITIONHIS a, PER_MOVMENT b
						where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and POH_PM_NAME is NOT NULL and
									(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11) $where
						order by POH_EFFECTIVEDATE ";
		 //echo "$cmd<br><br>";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$START_DATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
		$END_DATE = show_date_format($POH_ENDDATE,1);
	    $DIFF = date_difference($POH_ENDDATE, $data2[POH_EFFECTIVEDATE], "full");
		$POH_PL_NAME = trim($data2[POH_PL_NAME]);
		$POH_PM_NAME = trim($data2[POH_PM_NAME]);
		$POH_LEVEL_NO = trim($data2[POH_LEVEL_NO]);
		$POH_MINISTRY = trim($data2[POH_ORG1]);
		$POH_DEPARTMENT = trim($data2[POH_ORG2]);
		$POH_ORG = trim($data2[POH_ORG3]);
		$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$POH_LEVEL_NO' order by LEVEL_SEQ_NO";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_LEVEL_NAME = $data2[LEVEL_NAME];
		$POH_LEVEL = $data2[POSITION_LEVEL];
		if (!$POH_LEVEL) $POH_LEVEL = $LEVEL_NAME;

		if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4
			
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][sequence] = $data_row;
		if ($have_pic && $img_file){
				if ($FLAG_RTF)
				$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
				else
				$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
		}
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][cardno] = $PER_CARDNO;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][pm_name] = $PM_NAME;
		$arr_content[$data_count][ministry] = $MINISTRY_NAME;
		$arr_content[$data_count][department] = $DEPARTMENT_NAME;
		$arr_content[$data_count][org] = $ORG_NAME;
		$arr_content[$data_count][levelname] = 					$POSITION_LEVEL;
		$arr_content[$data_count][educate] = 						$EL_NAME;								//วุฒิการศึกษาสูงสุด
		$arr_content[$data_count][start_date] = 	$START_DATE;		//วันดำรงตำแหน่งปัจจุบัน
		$arr_content[$data_count][end_date] = 			$END_DATE;		//วันที่สิ้นสุด
		$arr_content[$data_count][diff] = 						$DIFF;	//อายุ    
		$arr_content[$data_count][level_date] = 			$LEVEL_DATE;		//วันที่เลื่อนระดับปัจจุบัน
		$arr_content[$data_count][salary] = 							$PER_SALARY; //เงินเดือน
		$arr_content[$data_count][startdate] = 						$PER_STARTDATE;	//วันบรรจุ
		$arr_content[$data_count][retiredate] = 					$PER_RETIREDATE;	//วันเกษียณอายุ    
		$arr_content[$data_count][birthdate] = 						$PER_BIRTHDATE;	//วันเกิด    
		$arr_content[$data_count][full_age] = 						$FULL_AGE;	//อายุ    
		$arr_content[$data_count][poh_pl_name] = $POH_PL_NAME;
		$arr_content[$data_count][poh_pm_name] = $POH_PM_NAME;
		$arr_content[$data_count][poh_level] = $POH_LEVEL;
		$arr_content[$data_count][poh_ministry] = $POH_MINISTRY;
		$arr_content[$data_count][poh_department] = $POH_DEPARTMENT;
		$arr_content[$data_count][poh_org] = $POH_ORG;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
			$RTF->paragraph(); 
				
		//	echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$COUNT=$arr_content[$data_count][sequence];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME = $arr_content[$data_count][name];
			$CARDNO = $arr_content[$data_count][cardno];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$MINISTRY = $arr_content[$data_count][ministry];
			$DEPARTMENT = $arr_content[$data_count][department];
			$ORG = $arr_content[$data_count][org];
			$POSITION_LEVEL = $arr_content[$data_count][levelname];
			$EDUCATE = $arr_content[$data_count][educate];
			$SALARY = $arr_content[$data_count][salary];

			$START_DATE = $arr_content[$data_count][start_date];
			$END_DATE=$arr_content[$data_count][end_date];
			$DIFF=$arr_content[$data_count][diff];
			$LEVELDATE=$arr_content[$data_count][level_date];
			$STARTDATE = $arr_content[$data_count][startdate];
			$RETIREDATE = $arr_content[$data_count][retiredate];
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			$FULL_AGE = $arr_content[$data_count][full_age];
			$POH_PL_NAME = $arr_content[$data_count][poh_pl_name];
			$POH_PM_NAME = $arr_content[$data_count][poh_pm_name];
			$POH_LEVEL = $arr_content[$data_count][poh_level];
			$POH_MINISTRY = $arr_content[$data_count][poh_ministry];
			$POH_DEPARTMENT = $arr_content[$data_count][poh_department];
			$POH_ORG = $arr_content[$data_count][poh_org];

			if($REPORT_ORDER == "MINISTRY"){
            	$arr_data = (array) null;
				$arr_data[] = "";
				if ($have_pic && $img_file) $arr_data[] = ""; 
				$arr_data[] = "<**1**>$NAME";
				$arr_data[] = "<**1**>$NAME";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
            	$arr_data[] = "";
		    	$arr_data[] = "";
            	$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
            	$arr_data[] = "";
		    	$arr_data[] = "";
            	$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
            	$arr_data[] = "";
		    	$arr_data[] = "";
            	$arr_data[] = "";
				$arr_data[] = "";
            	$arr_data[] = "";
				$arr_data[] = "";
				$data_align = array("L", "L", "C", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C");
				
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
			}elseif($REPORT_ORDER == "DEPARTMENT"){
				if (trim($NAME)) {
					$arr_data = (array) null;
					$arr_data[] = "";
					if ($have_pic && $img_file) $arr_data[] = ""; 
					$arr_data[] = "<**1**>$NAME";
					$arr_data[] = "<**1**>$NAME";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$data_align = array("L", "L", "C", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C");
					
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}
			}elseif($REPORT_ORDER == "CONTENT"){
            	$arr_data = (array) null;
				$arr_data[] = $COUNT;
				if ($have_pic && $img_file) $arr_data[] = $IMAGE;
				$arr_data[] = $NAME;
				$arr_data[] = $CARDNO;
				$arr_data[] = $LEVELDATE;
				$arr_data[] = $PM_NAME;
				$arr_data[] = $PL_NAME;
				$arr_data[] = $POSITION_LEVEL;
				$arr_data[] = $MINISTRY;
				$arr_data[] = $DEPARTMENT;
				$arr_data[] = $ORG;
		    	$arr_data[] = $BIRTHDATE;
            	$arr_data[] = $FULL_AGE;
				$arr_data[] = $EDUCATE;
				$arr_data[] = $START_DATE;
				$arr_data[] = $END_DATE;
            	$arr_data[] = $DIFF;
				$arr_data[] = $POH_PM_NAME;
				$arr_data[] = $POH_PL_NAME;
				$arr_data[] = $POH_LEVEL;
				$arr_data[] = $POH_MINISTRY;
				$arr_data[] = $POH_DEPARTMENT;
				$arr_data[] = $POH_ORG;
				
				if($have_pic)
					$data_align = array("C","C", "L", "C", "L", "C", "C", "C", "C", "C", "C", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C");
				else
					$data_align = array("C", "L", "C", "L", "C", "C", "C", "C", "C", "C", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C");
				
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if	
		} // end for
		if (!$FLAG_RTF)
			$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "b", "000000", "");	
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
//			$RTF->close_section(); 

		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 
	
		$pdf->close();
		$pdf->Output();	
	}
?>