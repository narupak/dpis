<?
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	if($export_type=="report"){
		include ("../php_scripts/function_share.php");
	}else if($export_type=="graph"){
		include ("../../admin//php_scripts/function_share.php");	//���͹䢷���ͧ����ʴ���
	}	

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
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	 	if($search_edu) { $search_edu.= ' or '; }
		$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 

		if($EDU_TYPE[$i]==1)	$EDU_TYPE_LABEL .= "�زԷ�����è�";
		if($EDU_TYPE[$i]==2)	$EDU_TYPE_LABEL .= "/�ز�㹵��˹觻Ѩ�غѹ";
		if($EDU_TYPE[$i]==3)	$EDU_TYPE_LABEL .= "/�ز���� �";
		if($EDU_TYPE[$i]==4)	$EDU_TYPE_LABEL .= "/�ز��٧�ش";
		if($EDU_TYPE[$i]==5)	$EDU_TYPE_LABEL .= "/�ز�㹵��˹觻Ѩ�غѹ�������";
	} 
	
	if($search_per_type==1 || $search_per_type == 5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		 $line_title=" ��§ҹ";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title=" ���͵��˹�";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ���͵��˹�";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);
		$line_title=" ���͵��˹�";
	} // end if

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//�Ѵ��ҷ���ӡѹ�͡ �����������鹢����ū�ӡѹ 2 ��	������§���˹� index ���� 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);

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
				else if($select_org_structure==1)  $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure==1)  $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1)   $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID"; 

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= $line_code;
				$heading_name .= $line_search_name;
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO, f.LEVEL_NAME";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO, f.LEVEL_NAME";

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
			case "EDUCLEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.EL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.EL_CODE";

				$heading_name .= " $EL_TITLE";
				break;
			case "EDUCMAJOR" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.EM_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.EM_CODE";

				$heading_name .= " $EM_TITLE";
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

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		$arr_search_condition[] = "(c.OT_CODE='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ������Ҥ
		$list_type_text = "��ǹ��ҧ������Ҥ";
		$arr_search_condition[] = "(c.OT_CODE='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		$arr_search_condition[] = "(c.OT_CODE='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ҧ�����
		$list_type_text = "��ҧ�����";
		$arr_search_condition[] = "(c.OT_CODE='04')";
	}
	if(in_array("PER_ORG", $list_type)){
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
		// ��§ҹ
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= "$line_search_name";
		}
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
			if($select_org_structure==0) $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "�ٻẺ����͡��§ҹ : ". ($select_org_structure==1?"�ç���ҧ����ͺ���§ҹ - ":"�ç���ҧ��������� - ") ."$list_type_text";
	$search_budget_year5 = $search_budget_year + 4;
	$report_title = "$DEPARTMENT_NAME||�ӹǹ$PERSON_TYPE[$search_per_type]���ú���³�����Ҫ���㹪�ǧ 5 ��||�����է�����ҳ $search_budget_year �֧ �է�����ҳ $search_budget_year5";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0401";
	include ("rpt_R004001_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
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

		$fname= "rpt_R004001_rtf.rtf";

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
	
	function count_person($budget_year, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type, $search_edu, $select_org_structure;
//		global $ORG_NAME, $arr_age_duration;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		
//		if($budget_year) $year_condition = generate_year_condition($budget_year);
/***
		if($DPISDB=="odbc") $year_condition = "(LEFT(trim(a.PER_RETIREDATE), 10) = '".($budget_year)."-10-01')";
		elseif($DPISDB=="oci8") $year_condition = "(SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($budget_year)."-10-01')";
		elseif($DPISDB=="mysql") $year_condition = "(LEFT(trim(a.PER_RETIREDATE), 10) = '".($budget_year)."-10-01')";
***/
		if($DPISDB=="odbc") $year_condition = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($budget_year) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".($budget_year)."-10-01')";
		elseif($DPISDB=="oci8") $year_condition = "(SUBSTR(trim(a.PER_RETIREDATE), 1, 10) > '".(($budget_year) - 1)."-10-01' and SUBSTR(trim(a.PER_RETIREDATE), 1, 10) <= '".($budget_year)."-10-01')";
		elseif($DPISDB=="mysql") $year_condition = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($budget_year) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".($budget_year)."-10-01')";

		$search_condition = str_replace(" where ", " and ", $search_condition);
		if ($search_edu) $where = "and ($search_edu)";

		if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
							 from		(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_EDUCNAME e on (d.EN_CODE=e.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							  where		$year_condition
												$search_condition $where
							  group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_EDUCNAME e, PER_ORG g
							 where			$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
												and a.PER_ID=d.PER_ID(+) and d.EN_CODE=e.EN_CODE(+) and a.DEPARTMENT_ID=g.ORG_ID
												and $year_condition
												$search_condition $where
							  group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from		(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_EDUCNAME e on (d.EN_CODE=e.EN_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							  where		$year_condition
												$search_condition $where
							  group by	a.PER_ID ";
		}
		if($select_org_structure==1) {
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
	//echo "$cmd<hr>";
	//	$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_year_condition($budget_year){
		global $DPISDB;
		
//			�Դ������ب�ԧ (����ѧ���֧�ѹ�Դ�����Ѻ�繻�)
//			$birthdate_min = date_adjust(date("Y-m-d"), "y", -25);
//			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) > '$birthdate_min')";
//			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) > '$birthdate_min')";
//			elseif($DPISDB=="mysql")

//			�Դ੾�л��Դ �Ѻ�ջѨ�غѹ
			$birthyear_min = date("Y") - 25;
			if($DPISDB=="odbc") $year_condition = "(LEFT(trim(a.PER_BIRTHDATE), 4) > '$birthyear_min')";
			elseif($DPISDB=="oci8") $year_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) > '$birthyear_min')";
			elseif($DPISDB=="mysql") $year_condition = "(LEFT(trim(a.PER_BIRTHDATE), 4) > '$birthyear_min')";

//			echo "age <= 24 :: $age_condition<br>";
		
		return $year_condition;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $TP_CODE;
		global $line_code;
				
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
						$arr_addition_condition[] = "(trim(a.LEVEL_NO) = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
					}else{ 
						$arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
					} // end if
				break;
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = 0 or a.PER_GENDER is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE' or b.PV_CODE is null)";
				break;
				case "EDUCLEVEL" :
					if($EL_CODE) $arr_addition_condition[] = "(trim(e.EL_CODE) = '$EL_CODE')";
					else $arr_addition_condition[] = "(trim(e.EL_CODE) = '$EL_CODE' or e.EL_CODE is null)";
				break;
				case "EDUCMAJOR" :
					if($EM_CODE) $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE')";
					else $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE' or d.EM_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $search_edu;
		
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
				case "SEX" :
					$PER_GENDER = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
				break;
				case "EDUCLEVEL" :
					$EL_CODE = -1;
				break;
				case "EDUCMAJOR" :
					$EM_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if ($search_edu) $where = "and ($search_edu)";
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from		(	
											(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_EDUCNAME e on (d.EN_CODE=e.EN_CODE)
											) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition $where
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_EDUCNAME e, PER_LEVEL f, PER_ORG g
						 where			$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) $where and 
											d.EN_CODE=e.EN_CODE(+) and a.LEVEL_NO=f.LEVEL_NO(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(	
											(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_EDUCNAME e on (d.EN_CODE=e.EN_CODE)
											) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition $where
						 order by		$order_by ";
	}
	if($select_org_structure==1) {
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	//echo $cmd."<hr>";
	$data_count = 0;
	for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[����к�$MINISTRY_TITLE]";
							$MINISTRY_SHORT = "[����к�]";
						}else{
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							$MINISTRY_SHORT = $data2[ORG_SHORT];
							if($MINISTRY_NAME=="-")	$MINISTRY_NAME = $MINISTRY_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
						$arr_content[$data_count][short_name] = $MINISTRY_SHORT;
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[����к�$DEPARTMENT_TITLE]";
							$DEPARTMENT_SHORT = "[����к�]";
						}else{
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
							$DEPARTMENT_SHORT = $data2[ORG_SHORT];
							if($DEPARTMENT_NAME=="-")	$DEPARTMENT_NAME = $DEPARTMENT_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
						$arr_content[$data_count][short_name] = $DEPARTMENT_SHORT;
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[����к�$ORG_TITLE]";
							$ORG_SHORT = "[����к�]";
						}else{
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
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
							$ORG_NAME_1 = "[����к�$ORG_TITLE1]";
							$ORG_SHORT_1 = "[����к�]";
						}else{
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							$ORG_SHORT_1 = $data2[ORG_SHORT];
							if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][short_name] = $ORG_SHORT_1;
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
							$ORG_NAME_2 = "[����к�$ORG_TITLE2]";
							$ORG_SHORT_2 = "[����к�]";
						}else{
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							$ORG_SHORT_2 = $data2[ORG_SHORT];
							if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][short_name] = $ORG_SHORT_2;
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE == ""){
							$PL_NAME = "[����к���§ҹ]";
						}else{
							$field_line = $line_name; 
							if(trim($line_short_name)){	$field_line .= ",b.". trim($line_short_name);	}
							$cmd = " select $field_line from $line_table b where trim($line_code)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$line_name_1=str_replace("b.","",trim($line_name));
							if(trim($line_short_name)){ $field_get_line=trim($line_short_name); }else{	 $field_get_line=$line_name_1;	}
							$PL_NAME = trim($data2[$field_get_line])?$data2[$field_get_line]:$data2[$line_name_1];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LEVEL" :
					if($LEVEL_NO != trim($data[LEVEL_NO])){
						$LEVEL_NO = trim($data[LEVEL_NO]);
						$LEVEL_NAME = trim($data[LEVEL_NAME]);

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($LEVEL_NAME)?" ". $LEVEL_NAME:"[����к��дѺ���˹�]");
						$arr_content[$data_count][short_name] = "";
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "SEX" :
					if($PER_GENDER != trim($data[PER_GENDER])){
						$PER_GENDER = trim($data[PER_GENDER]);
						if(!$PER_GENDER) $GENDER_NAME = "[����к���]";
						elseif($PER_GENDER==1) $GENDER_NAME = "���";
						elseif($PER_GENDER==2) $GENDER_NAME = "˭ԧ";

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $GENDER_NAME;
						$arr_content[$data_count][short_name] = "";
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "PROVINCE" :
					if($PV_CODE != trim($data[PV_CODE])){
						$PV_CODE = trim($data[PV_CODE]);
						if($PV_CODE == ""){
							$PV_NAME = "[����кبѧ��Ѵ]";
						}else{
							$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PV_NAME = $data2[PV_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "PROVINCE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
						$arr_content[$data_count][short_name] = "";
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCLEVEL" :
					if($EL_CODE != trim($data[EL_CODE])){
						$EL_CODE = trim($data[EL_CODE]);
						if($EL_CODE == ""){
							$EL_NAME = "[����к��дѺ����֡��]";
						}else{
							$cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EL_NAME = $data2[EL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCLEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EL_NAME;
						$arr_content[$data_count][short_name] = "";
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCMAJOR" :
					if($EM_CODE != trim($data[EM_CODE])){
						$EM_CODE = trim($data[EM_CODE]);
						if($EM_CODE == ""){
							$EM_NAME = "[����к��Ң��Ԫ��͡]";
						}else{
							$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EM_NAME = $data2[EM_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCMAJOR";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EM_NAME;
						$arr_content[$data_count][short_name] = "";
						for($i=1; $i<=5; $i++) 
							$arr_content[$data_count][("count_".$i)] = count_person(($arr_budget_year[$i-1] - 543), $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4 + $GRAND_TOTAL_5;
	if($GRAND_TOTAL){
		for($i=1; $i<=5; $i++) ${"PERCENT_TOTAL_".$i} = (${"GRAND_TOTAL_".$i} / $GRAND_TOTAL) * 100;		
	} // end if

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	echo "GRAND_TOTAL = $GRAND_TOTAL";
	if($export_type=="report"){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo ">>cnt headtext=".count($heading_text).", cnt headwidth=".count($heading_width)."<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

		if($count_data){
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$COUNT_TOTAL = 0;
				for($i=1; $i<=5; $i++) ${"COUNT_".$i} = ${"PERCENT_".$i} = 0;
	
				$REPORT_ORDER = $arr_content[$data_count][type];
				$NAME = $arr_content[$data_count][name];
				for($i=1; $i<=5; $i++){
					${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
					$COUNT_TOTAL += ${"COUNT_".$i};
				} // end for
				
				if($COUNT_TOTAL){
					for($i=1; $i<=5; $i++) ${"PERCENT_".$i} = (${"COUNT_".$i} / $COUNT_TOTAL) * 100;
				} // end if
				if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;
	
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				for($i=1; $i<=5; $i++) {
					$arr_data[] = ${"COUNT_".$i};
					$arr_data[] = ${"PERCENT_".$i};
				}
				$arr_data[] = $COUNT_TOTAL;
				$arr_data[] = $PERCENT_TOTAL;
	
				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1) {
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
				} else {
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				}
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
		
			$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;
	
			$arr_data = (array) null;
			$arr_data[] = "���";
			for($i=1; $i<=5; $i++) {
				$arr_data[] = ${"GRAND_TOTAL_".$i};
				$arr_data[] = ${"PERCENT_TOTAL_".$i};
			}
			$arr_data[] = $GRAND_TOTAL;
			$arr_data[] = $PERCENT_TOTAL;

			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
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
	}else if($export_type=="graph"){//if($export_type=="report"){

		$arr_content_map = array("","count_1","percent_1","count_2","percent_2","count_3","percent_3","count_4","percent_4","count_5","percent_5","count_total",""); // ���� column � content ��� map ���ç�Ѻ head pdf
		$arr_series_caption = (array) null;
		$arr_series_caption[] = "$heading_name";
		for($i=0; $i<5; $i++) {
			$arr_series_caption[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($arr_budget_year[$i]):$arr_budget_year[$i]);
			$arr_series_caption[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($arr_budget_year[$i]):$arr_budget_year[$i]);
		}
		$arr_series_caption[] = "�ӹǹ���";
		$arr_series_caption[] = "������";

//		echo "<pre>"; print_r($arr_content); echo "</pre>";
		$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
		$arr_categories = array();
		$arr_series_caption_list = array(); 
		$f_first = true;
		for($i=0;$i<count($arr_content);$i++){
	//		echo "arr_content[$i][type](".$arr_content[$i][type].")==arr_rpt_order[".(count($arr_rpt_order)-1)."](".$arr_rpt_order[count($arr_rpt_order)-1].")<br>";
			if($arr_content[$i][type]==$arr_rpt_order[count($arr_rpt_order)-1]){
				$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
	//			$arr_categories[$i] = $arr_content[$i][short_name];
				$cntseq=0;
				for($j = 0; $j < count($arr_content_map); $j++) {
					if ($arr_column_sel[$arr_column_map[$j]]==1 && strpos($arr_content_map[$arr_column_map[$j]],"count")!==false) {
						$arr_series_caption_data[$cntseq][] = $arr_content[$i][$arr_content_map[$arr_column_map[$j]]];
						if ($f_first) $arr_series_caption_list[] = $arr_series_caption[$arr_column_map[$j]];
	//					if ($f_first) echo "caption (j:$j)=".$arr_series_caption[$arr_content_map[$arr_column_map[$j]]]."  contentname=".$arr_content_map[$arr_column_map[$j]]."  mapseq=".$arr_column_map[$j]."<br>";
						$cntseq++;
					}
				}
				$f_first=false;	// check ����Ѻ�ͺ�á��ҹ��
	//			$arr_series_caption_data[0][] = $arr_content[$i][count_1];
	//			$arr_series_caption_data[1][] = $arr_content[$i][count_2];
	//			$arr_series_caption_data[2][] = $arr_content[$i][count_3];
	//			echo "content ".$arr_categories[$i]."(".$arr_content[$i][count_1].",".$arr_content[$i][count_2].",".$arr_content[$i][count_3]."<br>";
			}//if($arr_content[$i][type]==$arr_rpt_order[0]){
		}//print_r($arr_categories);
		$series_caption_list = implode(";",$arr_series_caption_list);
//		echo "count (arr_series_caption_data)=".count($arr_series_caption_data)."<br>";
		for($j=0;$j<count($arr_series_caption_data);$j++){
			$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]);
		}
//		echo "<pre>"; print_r($arr_series_caption_data); echo "</pre>";
//		for($j=3;$j<count($arr_content_key);$j++){
//			$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]).";".${"GRAND_TOTAL_".($j-2)};
//		}
	
		$chart_title = $report_title;
		$chart_subtitle = $company_name;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
	//	$series_caption_list = implode(";", $arr_budget_year);
		$categories_list = implode(";", $arr_categories).";���";
		if(strtolower($graph_type)=="pie"){
			$series_list = $GRAND_TOTAL_1.";".$GRAND_TOTAL_2.";".$GRAND_TOTAL_3.";".$GRAND_TOTAL_4.";".$GRAND_TOTAL_5;
		}else{
			$series_list = implode("|", $arr_series_list);
		}
		//echo($series_list);
		switch( strtolower($graph_type) ){
			case "column" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Column/style/column.scs";
				break;
			case "bar" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Bar/style/bar.scs";
				break;
			case "line" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Line/style/line.scs";
				break;
			case "pie" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Pie/style/pie.scs";
				break;
		} //switch( strtolower($graph_type) ){
	}//}else if($export_type=="graph"){
?>