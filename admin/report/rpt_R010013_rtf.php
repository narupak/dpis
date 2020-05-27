<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// ��˹�����ä���� set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_pt_code == 'O') $search_pt_name = "�����";
	elseif($search_pt_code == 'K') $search_pt_name = "�Ԫҡ��";
	elseif($search_pt_code == 'D') $search_pt_name = "�ӹ�¡��";
	elseif($search_pt_code == 'M') $search_pt_name = "������";

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (a.POS_ID >= 0 or a.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = 1)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
	//����ç���˹��дѺ�٧=>���˹觻����������� (�дѺ��, �дѺ�٧)	/�Ԫҡ�� (�дѺ����Ǫҭ,�ç�س�ز�) /�ӹ�¡�� (�дѺ�٧)
///	$arr_search_condition[] = "(a.LEVEL_NO in ('M1','M2','K4','K5','D2'))";	

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
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type) ){
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
	if(in_array("PER_ORG", $list_type) ){
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
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";			
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_COUNTRY", $list_type)){
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
		$list_type_text = "";
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
		if(trim($search_pt_name)){
			//���������˹�
			$list_type_text .= " ���˹觻�����$search_pt_name";
			$arr_search_condition[] = "(m.LEVEL_NO LIKE '%$search_pt_code%')";
		}
		//���˹����§ҹ
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= " ���˹����§ҹ$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= " ���˹����§ҹ$search_pn_name";
		}// end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

	include ("rpt_R010013_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R010013_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	///$report_title = "��§ҹ����ç���˹��дѺ�٧���ǹ�Ҫ����дѺ���";
	$report_title = "��§ҹ����ç���˹��дѺ�٧";
	if(trim($search_pt_name)){ $report_title .= " ���˹觻�����$search_pt_name"; }else{ $report_title .= " �ء���������˹�"; }
	$report_code = "H13";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	if ($search_pos_status==1) {
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
									a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, m.LEVEL_NAME, m.LEVEL_SHORTNAME, m.POSITION_TYPE, m.POSITION_LEVEL
					   from	 (	
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
					  group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), a.PER_SALARY,
									b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, m.LEVEL_NAME, m.LEVEL_SHORTNAME,m.POSITION_TYPE,m.POSITION_LEVEL,m.LEVEL_SEQ_NO
					  order by		$order_by, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO, a.PER_NAME, a.PER_SURNAME, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
									LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace("where", "and", $search_condition);
			$cmd = " select			b.PER_ID, i.PN_NAME, b.PER_NAME, b.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									SUBSTR(trim(b.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(b.PER_STARTDATE), 1, 10) as PER_STARTDATE, 
									b.PER_SALARY, a.ORG_ID, a.ORG_ID_1, c.ORG_NAME, $select_list, m.LEVEL_NAME, m.LEVEL_SHORTNAME, m.POSITION_TYPE, m.POSITION_LEVEL
						from		PER_POSITION a, PER_PERSONAL b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_MGT h, 
									PER_PRENAME i, PER_POSITIONHIS j, PER_DECORATEHIS k, PER_LEVEL m
						where		a.POS_ID=b.POS_ID(+) and a.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
									and a.PL_CODE=f.PL_CODE and a.PM_CODE=h.PM_CODE(+) and b.PN_CODE=i.PN_CODE(+)
									and b.PER_ID=j.PER_ID(+) and b.PER_ID=k.PER_ID(+) and a.LEVEL_NO=m.LEVEL_NO(+) 
									$search_condition
						group by		b.PER_ID, i.PN_NAME, b.PER_NAME, b.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									SUBSTR(trim(b.PER_BIRTHDATE), 1, 10), SUBSTR(trim(b.PER_STARTDATE), 1, 10), a.PER_SALARY,
									a.ORG_ID, a.ORG_ID_1, c.ORG_NAME, $group_by, m.LEVEL_NAME, m.LEVEL_SHORTNAME,m.POSITION_TYPE,m.POSITION_LEVEL,m.LEVEL_SEQ_NO
						order by		$order_by, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO, b.PER_NAME, a.PER_SURNAME, MIN(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)), a.PER_SALARY desc, 
									SUBSTR(trim(b.PER_STARTDATE), 1, 10), SUBSTR(trim(b.PER_BIRTHDATE), 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
									a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, m.LEVEL_NAME, m.LEVEL_SHORTNAME, m.POSITION_TYPE, m.POSITION_LEVEL
					   from	 (	
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
					 group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), a.PER_SALARY,
									b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, m.LEVEL_NAME, m.LEVEL_SHORTNAME,m.POSITION_TYPE,m.POSITION_LEVEL,m.LEVEL_SEQ_NO
					 order by		$order_by, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO, a.PER_NAME, a.PER_SURNAME, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
									LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
		} // end if
	} else {
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
									a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, m.LEVEL_NAME, m.LEVEL_SHORTNAME, m.POSITION_TYPE, m.POSITION_LEVEL
					   from	 (	
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
					  group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), a.PER_SALARY,
									b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, m.LEVEL_NAME, m.LEVEL_SHORTNAME,m.POSITION_TYPE,m.POSITION_LEVEL,m.LEVEL_SEQ_NO
					  order by		$order_by, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO, a.PER_NAME, a.PER_SURNAME, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
									LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace("where", "and", $search_condition);
			$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, 
									a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, m.LEVEL_NAME, m.LEVEL_SHORTNAME, m.POSITION_TYPE, m.POSITION_LEVEL
						from		PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_MGT h, 
									PER_PRENAME i, PER_POSITIONHIS j, PER_DECORATEHIS k, PER_LEVEL m
						where		a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
									and b.PL_CODE=f.PL_CODE and b.PM_CODE=h.PM_CODE(+) and a.PN_CODE=i.PN_CODE(+)
									and a.PER_ID=j.PER_ID(+) and a.PER_ID=k.PER_ID(+) and a.LEVEL_NO=m.LEVEL_NO(+) 
									$search_condition
						group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10), a.PER_SALARY,
									b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, m.LEVEL_NAME, m.LEVEL_SHORTNAME,m.POSITION_TYPE,m.POSITION_LEVEL,m.LEVEL_SEQ_NO
						order by		$order_by, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO, a.PER_NAME, a.PER_SURNAME, MIN(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)), a.PER_SALARY desc, 
									SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
									a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, m.LEVEL_NAME, m.LEVEL_SHORTNAME, m.POSITION_TYPE, m.POSITION_LEVEL
					   from	 (	
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
					 group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
									LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), a.PER_SALARY,
									b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, m.LEVEL_NAME, m.LEVEL_SHORTNAME,m.POSITION_TYPE,m.POSITION_LEVEL,m.LEVEL_SEQ_NO
					 order by		$order_by, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO, a.PER_NAME, a.PER_SURNAME, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
									LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
		} // end if
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
		$POSITION_TYPE =  trim($data[POSITION_TYPE]);
		if ($BKK_FLAG==1)
			$POSITION_LEVEL =  trim($data[LEVEL_SHORTNAME]);
		else
			$POSITION_LEVEL =  trim($data[POSITION_LEVEL]);
		
		$PL_NAME = $data[PL_NAME];
		$PM_NAME = $data[PM_NAME];
		$TMP_ORG_NAME = $data[ORG_NAME];

		$ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_ORG_NAME_1 = $data2[ORG_NAME];

		if ($PM_NAME=="�������Ҫ��èѧ��Ѵ" || $PM_NAME=="�ͧ�������Ҫ��èѧ��Ѵ" || $PM_NAME=="��Ѵ�ѧ��Ѵ") {
			$PM_NAME .= $TMP_ORG_NAME;
			$PM_NAME = str_replace("�ѧ��Ѵ�ѧ��Ѵ", "�ѧ��Ѵ", $PM_NAME); 
		} elseif ($PM_NAME=="��������") {
			$PM_NAME .= $TMP_ORG_NAME_1;
			$PM_NAME = str_replace("����ͷ��ӡ�û���ͧ�����", "�����", $PM_NAME); 
		}

		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$PER_AGE = floor(date_difference(date("Y-m-d"), $PER_BIRTHDATE, "year"));
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_RETIRE_YEAR = ($arr_temp[0] + 543) + (($arr_temp[1] >= 10)?61:60);
			$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
		}

		$PER_STARTDATE = trim($data[PER_STARTDATE]);
		$PER_WORKAGE = floor(date_difference(date("Y-m-d"), $PER_STARTDATE, "year"));
		$PER_STARTDATE = show_date_format($PER_STARTDATE,$DATE_DISPLAY);

		//���ѹ����͹�дѺ
		$cmd = " select POH_EFFECTIVEDATE
						from   PER_POSITIONHIS a, PER_MOVMENT b
						where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=3
						order by POH_EFFECTIVEDATE DESC ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
		if ($POH_EFFECTIVEDATE) {	
			$POH_EFFECTIVEDATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
		}
//		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE],$DATE_DISPLAY);
		
		//���ѹ��ç���˹觻Ѩ�غѹ
		if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
			$select_effectivedate="LEFT(trim(POH_EFFECTIVEDATE), 10)";
		}elseif($DPISDB=="oci8"){
			$select_effectivedate="SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10)";
		}
		$cmd = " select MAX($select_effectivedate) as EFFECTIVEDATE from PER_POSITIONHIS where (PER_ID=$PER_ID)";
		// echo "$cmd<br><br>";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$POS_CHANGE_DATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);
		$PER_SALARY = $data[PER_SALARY];

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = "$data_row." . str_repeat(" ", 3) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][pm_name] = $PM_NAME;
		$arr_content[$data_count][position_type] = $POSITION_TYPE;
		$arr_content[$data_count][levelname] = 		$POSITION_LEVEL;
		$arr_content[$data_count][leveldate] = 		$POH_EFFECTIVEDATE;		//�ѹ�������͹�дѺ
		$arr_content[$data_count][effectivedate] = $POS_CHANGE_DATE;		//�ѹ��ç���˹�
		$arr_content[$data_count][startdate] = 			$PER_STARTDATE;		//�ѹ��è�
		$arr_content[$data_count][retireyear] = $PER_RETIRE_YEAR;
				
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
			$NAME = $arr_content[$data_count][name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$POSITION_TYPE = $arr_content[$data_count][position_type];
			$LEVEL_NAME=$arr_content[$data_count][levelname];
			$LEVELDATE=$arr_content[$data_count][leveldate];
			$EFFECTIVEDATE=$arr_content[$data_count][effectivedate];
			$STARTDATE = $arr_content[$data_count][startdate];
			$RETIREYEAR = $arr_content[$data_count][retireyear];

			if($REPORT_ORDER == "MINISTRY"){
            	$arr_data = (array) null;
				$arr_data[] ="$NAME";
				$arr_data[] =  "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] ="";
            	$arr_data[] ="";
				$arr_data[] ="";
				$arr_data[] ="";

				$data_align = array("L", "L", "L", "C", "C", "C", "C", "C");

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
			}elseif($REPORT_ORDER == "DEPARTMENT"){
				if (trim($NAME)) {
					$arr_data = (array) null;
					$arr_data[] ="$NAME";
					$arr_data[] =  "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] ="";
					$arr_data[] ="";
					$arr_data[] ="";		    	
					$arr_data[] ="";
	
					$data_align = array("L", "L", "L", "C", "C", "C", "C", "C");

					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}
			}elseif($REPORT_ORDER == "CONTENT"){
				//new format************************************************************			
            	$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $PL_NAME;
				$arr_data[] = $PM_NAME;
				$arr_data[] = $LEVEL_NAME;
				$arr_data[] = $LEVELDATE;
            	$arr_data[] = $EFFECTIVEDATE;
		    	$arr_data[] = $STARTDATE;
            	$arr_data[] = $RETIREYEAR;

				$data_align = array("L", "L", "L", "C", "C", "C", "C", "C");

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if	
		} // end for
	}else{
		$result = $RTF->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);

?>