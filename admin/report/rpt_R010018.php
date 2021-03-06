<?
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// ��˹�����ä���� set_of_colors
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
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
	//����ç���˹��дѺ�٧=>���˹觻������ӹ�¡�� (�дѺ�٧)	/�Ԫҡ�� (�дѺ����Ǫҭ����) /����� (�дѺ�ѡ�о����)
///	$arr_search_condition[] = "(a.LEVEL_NO in ('O4','K4','K5','D2','M1','M2'))";	

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
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//�Ѵ��ҷ���ӡѹ�͡ �����������鹢����ū�ӡѹ 2 ��	������§���˹� index ���� 0 1 2 ...
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
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME ";

				if($group_by) $group_by .= ", ";
				$group_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, d.ORG_NAME "; 
				
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
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
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
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
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
			else if($select_org_structure == 1) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure == 1) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type)){
		// ��§ҹ
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
		} // end if
	}
	if(in_array("PER_COUNTRY", $list_type) ){
		// ����� , �ѧ��Ѵ
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
	if(in_array("ALL", $list_type) || !isset($list_type)){	//�óշ����� ������������͡ check box list_type ���
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
		
		//���Ҩҡ
		$arr_level_no_condi = (array) null;
		foreach ($LEVEL_NO as $search_level_no)
		{
			if ($search_level_no) $arr_level_no_condi[] = "'$search_level_no'";
	//		echo "search_level_no=$search_level_no<br>";
		}
		if (count($arr_level_no_condi) > 0) $arr_search_condition[] = "(trim(a.LEVEL_NO) in (".implode(",",$arr_level_no_condi)."))";

		// ��§ҹ
		if($search_per_type==1 && trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
				$list_type_text .= " $PL_TITLE $search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
				$list_type_text .= " $POS_EMP_TITLE $search_pn_name";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
///	$report_title = "��§ҹ����ç���˹觻����������� �ӹ�¡���дѺ�٧ �Ԫҡ���дѺ����Ǫҭ������з�����дѺ�ѡ�о����";
	if ($BKK_FLAG==1)
		$report_title = "��§ҹ����Ҫ��á�ا෾��ҹ�����ѭ�дѺ�٧";
	else
		$report_title = "��§ҹ����Ҫ��þ����͹���ѭ�дѺ�٧";
	if(trim($search_pt_name)){	 $report_title .="���˹觻�����$search_pt_name";	}
	$report_code = "H18";
	include ("rpt_R010018_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
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

		$fname= "rpt_R010018_rtf.rtf";

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
		$paper_size="A4";
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
		$cmd = " select	a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
								LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, POS_NO_NAME, POS_NO, $select_list,
								MIN(LEFT(trim( j.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
				   from		
				   				(
									(
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
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
									) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
				   where	$search_condition
				   group by	a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,	
									LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10), a.PER_SALARY, 
									b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, POS_NO_NAME, POS_NO, 
									$group_by, m.LEVEL_SEQ_NO
				   order by	$order_by, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO, 
									a.PER_NAME, a.PER_SURNAME, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), 
									a.PER_SALARY desc, LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select	 a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,
									SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE,	a.PER_SALARY,b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, POS_NO_NAME, POS_NO, $select_list,
									MIN(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
				   from		PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_MGT h, 
				   				PER_PRENAME i, PER_POSITIONHIS j, PER_DECORATEHIS k, PER_LEVEL m
				   where	a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
								and b.PL_CODE=f.PL_CODE and b.PM_CODE=h.PM_CODE(+) and a.PN_CODE=i.PN_CODE(+)
								and a.PER_ID=j.PER_ID(+) and a.PER_ID=k.PER_ID(+) and a.LEVEL_NO=m.LEVEL_NO 
								$search_condition
				   group by	a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_RETIREDATE), 1, 10), a.PER_SALARY,
									b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, POS_NO_NAME, POS_NO, $group_by, m.LEVEL_SEQ_NO
				   order by	$order_by, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO, 
									NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY'), MIN(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)), 
									a.PER_SALARY desc, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select	a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
								LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE, a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, POS_NO_NAME, POS_NO, $select_list,
								MIN(LEFT(trim( j.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
				   from		
				   				(
									(
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
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
									) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
				   where		$search_condition
				   group by	a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10), a.PER_SALARY,
									b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, POS_NO_NAME, POS_NO, $group_by, m.LEVEL_SEQ_NO
				   order by	$order_by, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO, 
									a.PER_NAME, a.PER_SURNAME, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), 
									a.PER_SALARY desc, LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
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
		$POS_NO_NAME = trim($data[POS_NO_NAME]);
		$POS_NO = trim($data[POS_NO]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		//�Ҫ����дѺ���˹� ��е��˹觻�����
		$cmd="select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		$PL_NAME = trim($data[PL_NAME]);
		$PM_NAME = trim($data[PM_NAME]);
		$PT_CODE =trim($data[PT_CODE]);			
		$TMP_ORG_NAME = $data[ORG_NAME];

		$ORG_ID_1 = $data[ORG_ID_1];
		$TMP_ORG_NAME_1 = "";
		if($ORG_ID_1){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME_1 = $data2[ORG_NAME];
		}
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);

		$PL_NAME = pl_name_format($PL_NAME, $PM_NAME, $PT_NAME, $LEVEL_NO, 2, $TMP_ORG_NAME, $TMP_ORG_NAME_1);

		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$PER_AGE = floor(date_difference(date("Y-m-d"), $PER_BIRTHDATE, "year"));
		if($PER_BIRTHDATE){
			$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
		}

		//���ѹ����͹�дѺ
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
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);

		//���ѹ��ç���˹觻Ѩ�غѹ
		if ($BKK_FLAG==1) 
			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and POH_POS_NO_NAME='$POS_NO_NAME' and POH_POS_NO='$POS_NO' and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		else
			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and POH_POS_NO='$POS_NO' and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$PER_STARTDATE = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);

		//���ѹ���³����
		$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
		if($PER_RETIREDATE){
			$arr_temp = explode("-", $PER_RETIREDATE);
			$PER_RETIREDATE = ($arr_temp[0] + 543);
		}

		$PER_SALARY = $data[PER_SALARY];

		if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4
		
		$arr_content[$data_count][type] = "CONTENT";
		if ($have_pic && $img_file){
			if ($FLAG_RTF)
			$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
			else
			$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
		}
		$arr_content[$data_count][name] = "$data_row." . str_repeat(" ", 1) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = $PL_NAME;
		$arr_content[$data_count][position_type] = 	$POSITION_TYPE;
		$arr_content[$data_count][levelname] = 			$LEVEL_NAME;
		$arr_content[$data_count][leveldate] = 			$POH_EFFECTIVEDATE;		//�ѹ�������͹�дѺ
		$arr_content[$data_count][effectivedate] = 		$PER_STARTDATE;		//�ѹ��ç���˹觻Ѩ�غѹ
		$arr_content[$data_count][retiredate] = 			$PER_RETIREDATE;	//�ѹ���³����    
		
		//����ͧ�Ҫ��������ó� ���/�ѹ������Ѻ  �٧�ش�ѹ����
		/*$cmd="select k.DEH_DATE as DC_DATE,l.DC_NAME as DC_NAME,l.DC_SHORTNAME as DC_SHORT_NAME,l.DC_ORDER
						from PER_DECORATEHIS k, PER_DECORATION l 
						where  (k.PER_ID=$PER_ID) and (k.DC_CODE=l.DC_CODE)
						order by k.DEH_DATE desc,l.DC_ORDER ";*/
		if($DPISDB=="odbc" || $DPISDB=="mysql"){
			$select="MAX(LEFT(trim(k.DEH_DATE), 10)) as DC_DATE,MIN(l.DC_ORDER)";
			$groupby="k.PER_ID,l.DC_NAME,l.DC_SHORTNAME";
			$orderby="(LEFT(trim(k.DEH_DATE), 10)) desc,MIN(l.DC_ORDER)";
		}elseif($DPISDB=="oci8"){
			$select="MAX(SUBSTR(trim(k.DEH_DATE), 1, 10)) as DC_DATE,MIN(l.DC_ORDER)";
			$groupby="k.PER_ID,l.DC_NAME,l.DC_SHORTNAME";
			$orderby="MAX(SUBSTR(trim(k.DEH_DATE), 1, 10)) desc, MIN(l.DC_ORDER)";
		} // end if
		
		$cmd="select $select,l.DC_NAME as DC_NAME,l.DC_SHORTNAME as DC_SHORT_NAME
						from PER_DECORATEHIS k, PER_DECORATION l 
						where  (k.PER_ID=$PER_ID) and (k.DC_CODE=l.DC_CODE) and l.DC_TYPE in (1,2)
						group by $groupby
						order by $orderby";
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		//echo "<br>$cmd<br>";
		$data2 = $db_dpis2->get_array();
		$DC_TYPE = trim($data2[DC_SHORT_NAME]);
		$DC_DATE = show_date_format($data2[DC_DATE],$DATE_DISPLAY);

		$arr_content[$data_count][dc_type] = $DC_TYPE;  //�������ͧ�Ҫ�
		$arr_content[$data_count][dc_date] = $DC_DATE; 	//�ѹ������Ѻ
		$arr_content[$data_count][remark] = $REMARK;		//�����˵�

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
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$POSITION_TYPE = $arr_content[$data_count][position_type];
			$LEVEL_NAME = $arr_content[$data_count][levelname];
			$LEVELDATE=$arr_content[$data_count][leveldate];
			$EFFECTIVEDATE=$arr_content[$data_count][effectivedate];
			$STARTDATE = $arr_content[$data_count][startdate];
			$RETIREDATE = $arr_content[$data_count][retiredate];
			//����ͧ�Ҫ� ���/�ѹ������Ѻ
			$DC_TYPE= $arr_content[$data_count][dc_type];
			$DC_DATE= $arr_content[$data_count][dc_date];
			$REMARK = $arr_content[$data_count][remark];

			if($REPORT_ORDER == "MINISTRY"){
            	$arr_data = (array) null;
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
				$data_align = array("L", "L", "L", "C", "C", "C", "C", "C");
				
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
			}elseif($REPORT_ORDER == "DEPARTMENT"){
				if (trim($NAME)) {
					$arr_data = (array) null;
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
					$data_align = array("L", "L", "L", "C", "C", "C", "C", "C", "C", "C");
					
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}
			}elseif($REPORT_ORDER == "CONTENT"){
            	$arr_data = (array) null;
				if ($have_pic && $img_file) $arr_data[] = $IMAGE;
				$arr_data[] = $NAME;
				$arr_data[] = $POSITION;
				$arr_data[] = $POSITION_TYPE;
				$arr_data[] = $LEVEL_NAME;
				$arr_data[] = $LEVELDATE;
            	$arr_data[] = $EFFECTIVEDATE;
		    	$arr_data[] = $RETIREDATE;
            	$arr_data[] = $DC_TYPE;
				$arr_data[] = $DC_DATE;
            	$arr_data[] = $REMARK;

				if($have_pic)
				$data_align = array("C","L", "L", "C", "C", "C", "C", "C", "C", "C", "L");
				else
				$data_align = array("L", "L", "C", "C", "C", "C", "C", "C", "C", "L");
				
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
			$result = $RTF->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ����բ����� **********", 7, "", "L", "", "14", "b", 0, 0);
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