<?
//	header ('Content-type: text/html; charset=windows-874');
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
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";		
		$line_short_name = "PL_SHORTNAME";
		$line_seq="f.PL_SEQ_NO";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" ��§ҹ";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";	
		$line_seq="f.PN_SEQ_NO";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ���͵��˹�";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";	
		$line_seq="f.EP_SEQ_NO";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ���͵��˹�";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";		
		$line_seq="f.TP_SEQ_NO";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
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
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "LEVEL", "SEX", "PROVINCE"); 

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
				elseif($select_org_structure==1)  $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				elseif($select_org_structure==1)  $order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";
				elseif($select_org_structure==1)  $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				elseif($select_org_structure==1)  $order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_seq, $line_code as PL_CODE";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "$line_seq, $line_code";
				
				$heading_name .=$line_title;
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.LEVEL_SEQ_NO, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "g.LEVEL_SEQ_NO desc";

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
	if(!trim($order_by)){
		if($select_org_structure==0)  $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		elseif($select_org_structure==1)  $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	}
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2";
		elseif($select_org_structure==1)  $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2";
	}

	$search_condition = "";
	if(trim($search_effective_date))
		$search_effective_date =  save_date($search_effective_date);
	else
		$search_effective_date = date("Y-m-d");
	$show_effective_date = show_date_format($search_effective_date, 3);
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
//		echo "1..MINISTRY_ID=$MINISTRY_ID, DEPARTMENT_ID=$DEPARTMENT_ID<br>";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
//		echo "2..MINISTRY_ID=$MINISTRY_ID, DEPARTMENT_ID=$DEPARTMENT_ID<br>";
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ������Ҥ
		$list_type_text = "��ǹ��ҧ������Ҥ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
//		echo "3..MINISTRY_ID=$MINISTRY_ID, DEPARTMENT_ID=$DEPARTMENT_ID<br>";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
//		echo "4..MINISTRY_ID=$MINISTRY_ID, DEPARTMENT_ID=$DEPARTMENT_ID<br>";
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ҧ�����
		$list_type_text = "��ҧ�����";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
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
//		echo "all..MINISTRY_ID=$MINISTRY_ID, DEPARTMENT_ID=$DEPARTMENT_ID<br>";
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID==1){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

//			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
//			$list_type_text .= " - $MINISTRY_NAME";
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
	
	$company_name = "�ٻẺ����͡��§ҹ : ". ($select_org_structure==1?"�ç���ҧ����ͺ���§ҹ - ":"�ç���ҧ��������� - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||�ç���ҧ����$PERSON_TYPE[$search_per_type] � �ѹ��� $show_effective_date";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0204";
	include ("rpt_R002004_format_v2.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
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

		$fname= "rpt_R002004_rtf.rtf";

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
		$pdf->SetFont($font,'',14);
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
		$heading_column = 4;
		$arr_age_duration[1][name] = "21 - 30";
		$arr_age_duration[2][name] = "31 - 40";	
		$arr_age_duration[3][name] = "41 - 50";	
		$arr_age_duration[4][name] = "51 - 60";	
	} else {
		
		/*$heading_column = 8;
                $arr_age_duration[1][name] = "<=24";
		$arr_age_duration[2][name] = "25 - 29";	
		$arr_age_duration[3][name] = "30 - 34";	
		$arr_age_duration[4][name] = "35 - 39";	
		$arr_age_duration[5][name] = "40 - 44";	
		$arr_age_duration[6][name] = "45 - 49";	
		$arr_age_duration[7][name] = "50 - 54";	
		$arr_age_duration[8][name] = ">=55";*/
                
                /*��Ѻ�����������Ш��...*/
                $heading_column = 10;
                $arr_age_duration[1][name] = "<21";
		$arr_age_duration[2][name] = "21-25";
		$arr_age_duration[3][name] = "26-30";
		$arr_age_duration[4][name] = "31-35";
		$arr_age_duration[5][name] = "36-40";
		$arr_age_duration[6][name] = "41-45";
		$arr_age_duration[7][name] = "46-50";
                $arr_age_duration[8][name] = "51-55";
                $arr_age_duration[9][name] = "56-60";
		$arr_age_duration[10][name] = ">60";
                /**/
	}

	function count_person($age_index, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join, $search_effective_date;
		global $search_per_type, $select_org_structure;
//		global $ORG_NAME, $arr_age_duration;
		global $MINISTRY_ID, $DEPARTMENT_ID;
		global $arr_column_sel,	$arr_column_map, $arr_age_duration;

//		echo "count_person 1-->search_condition=$search_condition<br>";
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		
		if($age_index) $age_condition = generate_age_condition($age_index);
//		else $age_condition = "a.PER_BIRTHDATE is not null and trim(a.PER_BIRTHDATE) <> ''";
		else $age_condition = "a.PER_BIRTHDATE is not null";
		$search_condition = str_replace(" where ", " and ", $search_condition);

//		echo "count_person 2-->search_condition=$search_condition<br>";
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														PER_PERSONAL a
														left join $position_table b on ($position_join)
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
							  where		$age_condition
												$search_condition
							  group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG e
							 where			$position_join and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and $age_condition
												$search_condition
							  group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														PER_PERSONAL a
														left join $position_table b on ($position_join)
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
							  where		$age_condition
												$search_condition
							  group by	a.PER_ID ";
		} // end if
		//echo $age_index.'__<pre>'.$cmd.'<br>';
			if($select_org_structure==1) { 
				$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
				$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			}
		if($age_index){
			$count_person = $db_dpis2->send_cmd($cmd);
//			echo (!$db_dpis2->ERROR)?$db_dpis2->show_error():"$cmd<br>";
//			$db_dpis2->show_error(); echo "<br><hr>";
//			echo "1..$cmd ($count_person)<br>";
			if($count_person==1){
				$data = $db_dpis2->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if($data[count_person] == 0) $count_person = 0;
//				echo "2..$cmd ($count_person)<br>";
			} // end if

			return $count_person;
		}else{
			$cmd = str_replace("count(a.PER_ID) as count_person", "a.PER_ID, a.PER_BIRTHDATE", $cmd) . ", a.PER_BIRTHDATE order by a.PER_BIRTHDATE, a.PER_ID";
			$db_dpis2->send_cmd($cmd);
//			echo (!$db_dpis2->ERROR)?$db_dpis2->show_error():"$cmd<br>";
//			$db_dpis2->show_error();
//			echo "2..$cmd<br>";
//			echo "1************************************* sum_age=$sum_age<br>";
			$sum_age = 0;
			while($data = $db_dpis2->get_array()){
				$PER_ID = $data[PER_ID];
				$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);

//				�Դ������ب�ԧ (����ѧ���֧�ѹ�Դ�����Ѻ�繻�)
//				$PER_AGE = floor(date_difference($search_effective_date, $PER_BIRTHDATE, "y"));

//				�Դ੾�л��Դ �Ѻ�ջѨ�غѹ
				list($BIRTHYEAR, $BIRTHMONTH, $BIRTHDATE) = split("-", $PER_BIRTHDATE, 3);
				$PER_AGE = substr($search_effective_date,0,4) - $BIRTHYEAR;
				
//				$arr_age[$ORG_NAME][$arr_age_duration[$age_index][name]][$PER_ID.":".$PER_BIRTHDATE] = $PER_AGE;
//				$arr_age[$ORG_NAME][$PER_ID.":".$PER_BIRTHDATE] = $PER_AGE;
				for($i=1; $i<=count($arr_age_duration); $i++) {
					if ($arr_age_duration[$i][name]) {
						list($min_age, $max_age) = split("-", $arr_age_duration[$i][name], 2);
						if (trim($min_age) && trim($max_age)) {
							$min_age = (trim($min_age) + 0) * 1;
							$max_age = (trim($max_age) + 1) * 1;
//							echo "1-->";
						} else {
//							echo "2-->";
							if (trim($min_age)) {
								if (substr(trim($min_age),0,2) == "<") { //if (substr(trim($min_age),0,2) == "<=") {
									$max_age = (int)substr(trim($min_age),2) + 1;
									$min_age = 0;
//									echo ".1-->";
								} else {
									$min_age = (int)substr(trim($min_age),2);
									$max_age = 150;
//									echo ".2-->";
								}
							}
						}
//						echo "$min_age < $PER_AGE < $max_age  [".$arr_age_duration[$i][name]."] $sum_age<br>";
						if ($PER_AGE >= $min_age && $PER_AGE < $max_age) {	// check ��ǧ���� �������㹪�ǧ���ط��١��ͧ ���� check ��Ҫ�ǧ��������͡��������
							if ($arr_column_sel[$arr_column_map[$i]]==1) { // �кǡ੾�� �����ŷ�����͡�ʴ�
								$sum_age += $PER_AGE;
//								echo "i=$i, map=".$arr_column_map[$i].", age=$PER_AGE, sum=$sum_age<br>";
								break;
							}
						}
					} // end if ($arr_age_duration[$i][name])
				} // end loop for $i
			} // end while	
			
//			echo "<pre>"; print_r($arr_age); echo "</pre>";
//			echo "2************************************* sum_age=$sum_age<br>";
			return $sum_age;
		} // end if		
	} // function
	
	function generate_age_condition($age_index){
		global $DPISDB, $arr_age_duration, $search_effective_date;
		
		if($arr_age_duration[$age_index][name]=="<21"){ //if($arr_age_duration[$age_index][name]=="<=24"){
//			�Դ������ب�ԧ (����ѧ���֧�ѹ�Դ�����Ѻ�繻�)
			$birthdate_min = date_adjust($search_effective_date, "y", -21); //$birthdate_min = date_adjust($search_effective_date, "y", -25);
			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) > '$birthdate_min')";
			elseif($DPISDB=="oci8") $age_condition =  "trunc(months_between(sysdate, TO_DATE(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 'yyyy/mm/dd'))/12) < trunc(months_between(sysdate, TO_DATE('$birthdate_min', 'yyyy/mm/dd'))/12)"; //��� "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) > '$birthdate_min')";
			elseif($DPISDB=="mysql") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) > '$birthdate_min')";

//			�Դ੾�л��Դ �Ѻ�ջѨ�غѹ
//			$birthyear_min = substr($search_effective_date,0,4) - 25;
//			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 4) > '$birthyear_min')";
//			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) > '$birthyear_min')";

//			echo "age <= 24 :: $age_condition<br>";
		}elseif($arr_age_duration[$age_index][name]==">60"){ //}elseif($arr_age_duration[$age_index][name]==">=55"){
//			�Դ������ب�ԧ (����ѧ���֧�ѹ�Դ�����Ѻ�繻�)
			$birthdate_max = date_adjust($search_effective_date, "y", -60); //$birthdate_max = date_adjust($search_effective_date, "y", -55);
			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_max')";
			elseif($DPISDB=="oci8") $age_condition = "trunc(months_between(sysdate, TO_DATE(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 'yyyy/mm/dd'))/12) > trunc(months_between(sysdate, TO_DATE('$birthdate_max', 'yyyy/mm/dd'))/12)"; //��� "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) <= '$birthdate_max')";
			elseif($DPISDB=="mysql") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_max')";

//			�Դ੾�л��Դ �Ѻ�ջѨ�غѹ
//			$birthyear_max = substr($search_effective_date,0,4) - 55;
//			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 4) <= '$birthyear_max')";
//			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) <= '$birthyear_max')";

//			echo "age >= 55 :: $age_condition<br>";
		}else{
			list($min_age, $max_age) = split("-", $arr_age_duration[$age_index][name], 2);
			
                        $min_age = (trim($min_age) + 0) * -1;
			$max_age = (trim($max_age) + 0) * -1;
                        
//                      ���  
//                      $min_age = (trim($min_age) + 0) * -1;
//			$max_age = (trim($max_age) + 1) * -1;
//			�Դ������ب�ԧ (����ѧ���֧�ѹ�Դ�����Ѻ�繻�)
                        
			$birthdate_max = date_adjust($search_effective_date, "y", $min_age);
			$birthdate_min = date_adjust($search_effective_date, "y", $max_age);
			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_max' and LEFT(trim(a.PER_BIRTHDATE), 10) > '$birthdate_min')";
                        elseif($DPISDB=="oci8") $age_condition = "trunc(months_between(sysdate, TO_DATE(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 'yyyy/mm/dd'))/12) between trunc(months_between(sysdate, TO_DATE('$birthdate_max', 'yyyy/mm/dd'))/12) and trunc(months_between(sysdate, TO_DATE('$birthdate_min', 'yyyy/mm/dd'))/12)"; //��� "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) <= '$birthdate_max' and SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) > '$birthdate_min')";
			elseif($DPISDB=="mysql") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_max' and LEFT(trim(a.PER_BIRTHDATE), 10) > '$birthdate_min')";

//			�Դ੾�л��Դ �Ѻ�ջѨ�غѹ
//			$birthyear_max = substr($search_effective_date,0,4) + $min_age;
//			$birthyear_min = substr($search_effective_date,0,4) + $max_age;
//			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 4) <= '$birthyear_max' and LEFT(trim(a.PER_BIRTHDATE), 4) > '$birthyear_min')";
//			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) <= '$birthyear_max' and SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) > '$birthyear_min')";

//			echo "age $min_age - $max_age :: $age_condition<br>";
		} // end if
		
		return $age_condition;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $LEVEL_NAME, $POSITION_LEVEL, $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $EP_CODE;
		global $line_code;	

		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) { $arr_addition_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)"; //echo "(e.ORG_ID_REF = MINISTRY_ID($MINISTRY_ID))"; 
					}
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else if($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1)  $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_1" :
					if($select_org_structure==0){
						if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}else  if($select_org_structure==1){
						if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
					}
				break;
				case "ORG_2" :
					if($select_org_structure==0){
						if($ORG_ID_2 && $ORG_ID_2!=-1) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
					}else  if($select_org_structure==1){
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
	} // function
	
	function initialize_parameter($current_index){   
		global $arr_rpt_order, $position_table, $position_join;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $LEVEL_NAME, $POSITION_LEVEL, $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $EP_CODE;
		
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
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
												) left join $line_table f on ($line_join)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG e, $line_table f, PER_LEVEL g
						 where			$position_join and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and $line_join and a.LEVEL_NO=g.LEVEL_NO(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													(
														(
															PER_PERSONAL a
															left join $position_table b on ($position_join)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
												) left join $line_table f on ($line_join)
											) left join  PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
											$search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) {
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
//$db_dpis->show_error();		exit;
//	echo "arr_rpt_order(".implode(",",$arr_rpt_order).")<br>";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "cmd=".$cmd."($count_data)<BR>";
        //die('<pre>'.$cmd);
	$data_count = 0;
	initialize_parameter(0);
	$first_order = 1;	// order ��  0 = COUNTRY �ѧ�����ӹǳ ����� order ��� 1 (MINISTRY) ��͹
	for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} = 0;
	while($data = $db_dpis->get_array()){
		//$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
				$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
//				echo "rpt_order_index=$rpt_order_index-->$REPORT_ORDER<br>";
				switch($REPORT_ORDER){
					case "MINISTRY" :
						if ($CTRL_TYPE < 3) {
							if($MINISTRY_ID != trim($data[MINISTRY_ID])){
								$MINISTRY_ID = trim($data[MINISTRY_ID]);
								if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1){
									$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$MINISTRY_NAME = $data2[ORG_NAME];
									$MINISTRY_SHORT = $data2[ORG_SHORT];
						
									if ($f_all) {
											if ($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1) {
												$arr_content[$data_count][type] = "COUNTRY";
												//$rpt_order_start_index = 0;
												//$addition_condition = "";
											} else {
												$arr_content[$data_count][type] = "MINISTRY";
												//$rpt_order_start_index = 1;
												//$addition_condition = generate_condition(1);
											}

		//									echo "rpt_order_index=$rpt_order_index<br>";
											$addition_condition = generate_condition($rpt_order_index);
									
											$GRAND_TOTAL1 = count_person(1, $search_condition, "$addition_condition");
											$GRAND_TOTAL2 = count_person(2, $search_condition, "$addition_condition");
											$GRAND_TOTAL3 = count_person(3, $search_condition, "$addition_condition");
											$GRAND_TOTAL4 = count_person(4, $search_condition, "$addition_condition");
											$GRAND_TOTAL5 = count_person(5, $search_condition, "$addition_condition");
											$GRAND_TOTAL6 = count_person(6, $search_condition, "$addition_condition");
											$GRAND_TOTAL7 = count_person(7, $search_condition, "$addition_condition");
											$GRAND_TOTAL8 = count_person(8, $search_condition, "$addition_condition");
                                                                                        /**/
                                                                                        $GRAND_TOTAL9 = count_person(9, $search_condition, "$addition_condition");
											$GRAND_TOTAL10 = count_person(10, $search_condition, "$addition_condition");
                                                                                        $CNT_TOTAL = ($GRAND_TOTAL1+$GRAND_TOTAL2+$GRAND_TOTAL3+$GRAND_TOTAL4+$GRAND_TOTAL5+
                                                                                                     $GRAND_TOTAL6+$GRAND_TOTAL7+$GRAND_TOTAL8+$GRAND_TOTAL9+$GRAND_TOTAL10)+0;
                                                                                        /**/
											$GRAND_TOTAL = count_person(0, $search_condition, "$addition_condition");
									
											$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)).$MINISTRY_NAME;
											$arr_content[$data_count][parent] = 0;	// COUNTRY
											$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)).$MINISTRY_SHORT;
											$arr_content[$data_count][count_1] = $GRAND_TOTAL1;
											$arr_content[$data_count][count_2] = $GRAND_TOTAL2;
											$arr_content[$data_count][count_3] = $GRAND_TOTAL3;
											$arr_content[$data_count][count_4] = $GRAND_TOTAL4;
											$arr_content[$data_count][count_5] = $GRAND_TOTAL5;
											$arr_content[$data_count][count_6] = $GRAND_TOTAL6;
											$arr_content[$data_count][count_7] = $GRAND_TOTAL7;
											$arr_content[$data_count][count_8] = $GRAND_TOTAL8;
                                                                                        /**/
                                                                                        $arr_content[$data_count][count_9] = $GRAND_TOTAL9;
											$arr_content[$data_count][count_10] = $GRAND_TOTAL10;
                                                                                        $arr_content[$data_count][cnt_man] = $CNT_TOTAL;
                                                                                        /**/
											$arr_content[$data_count][sum_age] = $GRAND_TOTAL;
											
		//									for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} = 0;
											if($rpt_order_index==$first_order){
		//										for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];		
												for($i=1; $i<=$heading_column; $i++) {
													${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];	
		//											echo "_".$i."=".${"GRAND_TOTAL_".$i};	
												}
		//										echo "<br>";
											} // end if
									
											if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
											$data_count++;
										} // end if ($f_all)
									} //end if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1)
								} // end if
							} // end if
						break;
						
					case "DEPARTMENT" :
						if ($CTRL_TYPE < 5) {
							if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
								$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
								if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){
									$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$DEPARTMENT_NAME = $data2[ORG_NAME];
									$DEPARTMENT_SHORT = $data2[ORG_SHORT];
								
									$addition_condition = generate_condition($rpt_order_index);
								
									$GRAND_TOTAL1 = count_person(1, $search_condition, $addition_condition);
									$GRAND_TOTAL2 = count_person(2, $search_condition, $addition_condition);
									$GRAND_TOTAL3 = count_person(3, $search_condition, $addition_condition);
									$GRAND_TOTAL4 = count_person(4, $search_condition, $addition_condition);
									$GRAND_TOTAL5 = count_person(5, $search_condition, $addition_condition);
									$GRAND_TOTAL6 = count_person(6, $search_condition, $addition_condition);
									$GRAND_TOTAL7 = count_person(7, $search_condition, $addition_condition);
									$GRAND_TOTAL8 = count_person(8, $search_condition, $addition_condition);
                                                                        /**/
                                                                        $GRAND_TOTAL9 = count_person(9, $search_condition, $addition_condition);
									$GRAND_TOTAL10 = count_person(10, $search_condition, $addition_condition);
                                                                        $CNT_TOTAL = ($GRAND_TOTAL1+$GRAND_TOTAL2+$GRAND_TOTAL3+$GRAND_TOTAL4+$GRAND_TOTAL5+
                                                                                     $GRAND_TOTAL6+$GRAND_TOTAL7+$GRAND_TOTAL8+$GRAND_TOTAL9+$GRAND_TOTAL10)+0;
                                                                        /**/
									$GRAND_TOTAL = count_person(0, $search_condition, $addition_condition);

									$arr_content[$data_count][type] = "DEPARTMENT";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $DEPARTMENT_NAME;
									$arr_content[$data_count][parent] = "";
									$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $DEPARTMENT_SHORT;
									$arr_content[$data_count][id] = $DEPARTMENT_ID;
									$arr_content[$data_count][count_1] = $GRAND_TOTAL1;
									$arr_content[$data_count][count_2] = $GRAND_TOTAL2;
									$arr_content[$data_count][count_3] = $GRAND_TOTAL3;
									$arr_content[$data_count][count_4] = $GRAND_TOTAL4;
									$arr_content[$data_count][count_5] = $GRAND_TOTAL5;
									$arr_content[$data_count][count_6] = $GRAND_TOTAL6;
									$arr_content[$data_count][count_7] = $GRAND_TOTAL7;
									$arr_content[$data_count][count_8] = $GRAND_TOTAL8;
                                                                        /**/
                                                                        $arr_content[$data_count][count_9] = $GRAND_TOTAL9;
									$arr_content[$data_count][count_10] = $GRAND_TOTAL10;
                                                                        $arr_content[$data_count][cnt_man] = $CNT_TOTAL;
                                                                        /**/
									$arr_content[$data_count][sum_age] = $GRAND_TOTAL;
					
									if($rpt_order_index==$first_order){
										for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
									} // end if

								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
								} // end if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1)
							} // end if
						} // end if
						break;
						
					case "ORG" :
						if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1){
								$cmd = " select a.ORG_NAME,a.ORG_SHORT,b.ORG_NAME as ORG_NAME_PARENT from PER_ORG a join PER_ORG b on (a.ORG_ID_REF=b.ORG_ID) where a.ORG_ID=$ORG_ID order by b.ORG_NAME ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
								$ORG_SHORT = $data2[ORG_SHORT];
								$ORG_NAME_PARENT = $data2[ORG_NAME_PARENT];
								if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
			   
							   if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
									$addition_condition = generate_condition($rpt_order_index);
			
									$arr_content[$data_count][type] = "ORG";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_NAME;
									$arr_content[$data_count][parent] = $ORG_NAME_PARENT;
									$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_SHORT;
                                                                        
									$arr_content[$data_count][count_1] = $cnt1 = count_person(1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = $cnt2 = count_person(2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = $cnt3 = count_person(3, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = $cnt4 = count_person(4, $search_condition, $addition_condition);
									$arr_content[$data_count][count_5] = $cnt5 = count_person(5, $search_condition, $addition_condition);
									$arr_content[$data_count][count_6] = $cnt6 = count_person(6, $search_condition, $addition_condition);
									$arr_content[$data_count][count_7] = $cnt7 = count_person(7, $search_condition, $addition_condition);
									$arr_content[$data_count][count_8] = $cnt8 = count_person(8, $search_condition, $addition_condition);
                                                                        /**/
                                                                        $arr_content[$data_count][count_9] = $cnt9 = count_person(9, $search_condition, $addition_condition);
									$arr_content[$data_count][count_10] = $cnt10 = count_person(10, $search_condition, $addition_condition);
                                                                        $arr_content[$data_count][cnt_man] = ($cnt1+$cnt2+$cnt3+$cnt4+$cnt5+$cnt6+$cnt7+$cnt8+$cnt9+$cnt10)+0;
                                                                        //$arr_content[$data_count][cnt_man] = $CNT_TOTAL;
                                                                        /**/
                                                                        
									$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);
			
									if($rpt_order_index==$first_order){
										for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
									} // end if
			
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
									$data_count++;
								} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
							} // end if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1)
						} // end if
					break;
			
					case "ORG_1" :
						if($ORG_ID_1 != trim($data[ORG_ID_1])){
							$ORG_ID_1 = trim($data[ORG_ID_1]);
							$ORG_NAME_1 = $ORG_SHORT_1 = "����к�";
							if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_1 = $data2[ORG_NAME];
								$ORG_SHORT_1 = $data2[ORG_SHORT];
								if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
							} //end if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1)

								if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-")){
									$addition_condition = generate_condition($rpt_order_index);
						
									$arr_content[$data_count][type] = "ORG_1";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_NAME_1;
									$arr_content[$data_count][parent] = "";
									$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_SHORT_1;
                                                                        
									$arr_content[$data_count][count_1] = $cnt1 = count_person(1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = $cnt2 = count_person(2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = $cnt3 = count_person(3, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = $cnt4 = count_person(4, $search_condition, $addition_condition);
									$arr_content[$data_count][count_5] = $cnt5 = count_person(5, $search_condition, $addition_condition);
									$arr_content[$data_count][count_6] = $cnt6 = count_person(6, $search_condition, $addition_condition);
									$arr_content[$data_count][count_7] = $cnt7 = count_person(7, $search_condition, $addition_condition);
									$arr_content[$data_count][count_8] = $cnt8 = count_person(8, $search_condition, $addition_condition);
                                                                        /**/
                                                                        $arr_content[$data_count][count_9] = $cnt9 = count_person(9, $search_condition, $addition_condition);
									$arr_content[$data_count][count_10] = $cnt10 = count_person(10, $search_condition, $addition_condition);
                                                                        $arr_content[$data_count][cnt_man] = ($cnt1+$cnt2+$cnt3+$cnt4+$cnt5+$cnt6+$cnt7+$cnt8+$cnt9+$cnt10)+0;
                                                                        /**/
									$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);
			
									if($rpt_order_index==$first_order){
										for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
									} // end if
						
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);		
									$data_count++;
								} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
						} // end if
					break;
			
					case "ORG_2" :
						if($ORG_ID_2 != trim($data[ORG_ID_2])){
							$ORG_ID_2 = trim($data[ORG_ID_2]);
							$ORG_NAME_2 = $ORG_SHORT_2 = "����к�";
							if($ORG_ID_2!= "" && $ORG_ID_2 != 0 && $ORG_ID_2 != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_2 = $data2[ORG_NAME];
								$ORG_SHORT_2 = $data2[ORG_SHORT];
								if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
							} // end if($ORG_ID_2!= "" && $ORG_ID_2 != 0 && $ORG_ID_2 != -1)

								 if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-")){
									$addition_condition = generate_condition($rpt_order_index);
						
									$arr_content[$data_count][type] = "ORG_2";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_NAME_2;
									$arr_content[$data_count][parent] = "";
									$arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_SHORT_2;
									$arr_content[$data_count][count_1] = $cnt1 = count_person(1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = $cnt2 = count_person(2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = $cnt3 = count_person(3, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = $cnt4 = count_person(4, $search_condition, $addition_condition);
									$arr_content[$data_count][count_5] = $cnt5 = count_person(5, $search_condition, $addition_condition);
									$arr_content[$data_count][count_6] = $cnt6 = count_person(6, $search_condition, $addition_condition);
									$arr_content[$data_count][count_7] = $cnt7 = count_person(7, $search_condition, $addition_condition);
									$arr_content[$data_count][count_8] = $cnt8 = count_person(8, $search_condition, $addition_condition);
                                                                        /**/
                                                                        $arr_content[$data_count][count_9] = $cnt9 = count_person(9, $search_condition, $addition_condition);
									$arr_content[$data_count][count_10] = $cnt10 = count_person(10, $search_condition, $addition_condition);
                                                                        $arr_content[$data_count][cnt_man] = ($cnt1+$cnt2+$cnt3+$cnt4+$cnt5+$cnt6+$cnt7+$cnt8+$cnt9+$cnt10)+0;
                                                                        /**/
                                                                        
									$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);
			
									if($rpt_order_index==$first_order){
										for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
									} // end if
						
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
									$data_count++;
								} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
						} // end if
					break;
			
					case "LINE" :
						if($PL_CODE != trim($data[PL_CODE])){
							$PL_CODE = trim($data[PL_CODE]);
							if($PL_CODE != ""){
								$field_line = $line_name; 
								if(trim($line_short_name)){	$field_line .= ",b.". trim($line_short_name);	}
								$cmd = " select $field_line from $line_table b where trim($line_code)='$PL_CODE' ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$line_name_1=str_replace("b.","",trim($line_name));
								//	if(trim($line_short_name)){ $field_get_line=trim($line_short_name); }else{	 $field_get_line=$line_name_1;	}
								$PL_NAME = trim($data2[$line_name_1]);
							} // end if
						   if(($PL_NAME !="" && $PL_NAME !="-") || ($BKK_FLAG==1 && $PL_NAME !="" && $PL_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
					
								$arr_content[$data_count][type] = "LINE";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $PL_NAME;
								$arr_content[$data_count][parent] = "";
								$arr_content[$data_count][short_name] = "";
								$arr_content[$data_count][count_1] = $cnt1 = count_person(1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = $cnt2 = count_person(2, $search_condition, $addition_condition);
								$arr_content[$data_count][count_3] = $cnt3 = count_person(3, $search_condition, $addition_condition);
								$arr_content[$data_count][count_4] = $cnt4 = count_person(4, $search_condition, $addition_condition);
								$arr_content[$data_count][count_5] = $cnt5 = count_person(5, $search_condition, $addition_condition);
								$arr_content[$data_count][count_6] = $cnt6 = count_person(6, $search_condition, $addition_condition);
								$arr_content[$data_count][count_7] = $cnt7 = count_person(7, $search_condition, $addition_condition);
								$arr_content[$data_count][count_8] = $cnt8 = count_person(8, $search_condition, $addition_condition);
                                                                /**/
                                                                $arr_content[$data_count][count_9] = $cnt9 = count_person(9, $search_condition, $addition_condition);
								$arr_content[$data_count][count_10] = $cnt10 = count_person(10, $search_condition, $addition_condition);
                                                                $arr_content[$data_count][cnt_man] = ($cnt1+$cnt2+$cnt3+$cnt4+$cnt5+$cnt6+$cnt7+$cnt8+$cnt9+$cnt10)+0;
                                                                /**/
								$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);
		
								if($rpt_order_index==$first_order){
									for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
								} // end if
					
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
							} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
						} // end if
					break;
			
					case "LEVEL" :
						if($LEVEL_NO != trim($data[LEVEL_NO])){
							$LEVEL_NO = trim($data[LEVEL_NO]);
							$LEVEL_NAME = trim($data[LEVEL_NAME]);
							$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
							
							if(($LEVEL_NAME !="" && $LEVEL_NAME !="-") || ($BKK_FLAG==1 && $LEVEL_NAME !="" && $LEVEL_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
					
								$arr_content[$data_count][type] = "LEVEL";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . (trim($LEVEL_NAME)?"".$LEVEL_NAME:"[����к��дѺ���˹�]");
								$arr_content[$data_count][parent] = "";
								$arr_content[$data_count][short_name] = $POSITION_LEVEL;
								$arr_content[$data_count][count_1] = $cnt1= count_person(1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = $cnt2= count_person(2, $search_condition, $addition_condition);
								$arr_content[$data_count][count_3] = $cnt3= count_person(3, $search_condition, $addition_condition);
								$arr_content[$data_count][count_4] = $cnt4= count_person(4, $search_condition, $addition_condition);
								$arr_content[$data_count][count_5] = $cnt5= count_person(5, $search_condition, $addition_condition);
								$arr_content[$data_count][count_6] = $cnt6= count_person(6, $search_condition, $addition_condition);
								$arr_content[$data_count][count_7] = $cnt7= count_person(7, $search_condition, $addition_condition);
								$arr_content[$data_count][count_8] = $cnt8= count_person(8, $search_condition, $addition_condition);
                                                                /**/
                                                                $arr_content[$data_count][count_9] = $cnt9= count_person(9, $search_condition, $addition_condition);
								$arr_content[$data_count][count_10] = $cnt10= count_person(10, $search_condition, $addition_condition);
                                                                $arr_content[$data_count][cnt_man] = ($cnt1+$cnt2+$cnt3+$cnt4+$cnt5+$cnt6+$cnt7+$cnt8+$cnt9+$cnt10)+0;
                                                                /**/
								$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);
		
								if($rpt_order_index==$first_order){
									for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
								} // end if
					
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
							} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
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
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $GENDER_NAME;
							$arr_content[$data_count][parent] = "";
							$arr_content[$data_count][short_name] = "";
							$arr_content[$data_count][count_1] = $cnt1 = count_person(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = $cnt2 = count_person(2, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = $cnt3 = count_person(3, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = $cnt4 = count_person(4, $search_condition, $addition_condition);
							$arr_content[$data_count][count_5] = $cnt5 = count_person(5, $search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = $cnt6 = count_person(6, $search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = $cnt7 = count_person(7, $search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = $cnt8 = count_person(8, $search_condition, $addition_condition);
                                                        /**/
                                                        $arr_content[$data_count][count_9] = $cnt9 = count_person(9, $search_condition, $addition_condition);
							$arr_content[$data_count][count_10] = $cnt10 = count_person(10, $search_condition, $addition_condition);
                                                        $arr_content[$data_count][cnt_man] = ($cnt1+$cnt2+$cnt3+$cnt4+$cnt5+$cnt6+$cnt7+$cnt8+$cnt9+$cnt10)+0;
                                                        /**/
							$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);
	
							if($rpt_order_index==$first_order){
								for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
							} // end if
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
						
					break;
	
					case "PROVINCE" :
						if($PV_CODE != trim($data[PV_CODE])){
							$PV_CODE = trim($data[PV_CODE]);
							if($PV_CODE != ""){
								$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PV_NAME = $data2[PV_NAME];
							} // end if
							if(($PV_NAME !="" && $PV_NAME !="-") || ($BKK_FLAG==1 && $PV_NAME !="" && $PV_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
					
								$arr_content[$data_count][type] = "PROVINCE";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $PV_NAME;
								$arr_content[$data_count][parent] = "";
								$arr_content[$data_count][short_name] = "";
								$arr_content[$data_count][count_1] = $cnt1 = count_person(1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = $cnt2 = count_person(2, $search_condition, $addition_condition);
								$arr_content[$data_count][count_3] = $cnt3 = count_person(3, $search_condition, $addition_condition);
								$arr_content[$data_count][count_4] = $cnt4 = count_person(4, $search_condition, $addition_condition);
								$arr_content[$data_count][count_5] = $cnt5 = count_person(5, $search_condition, $addition_condition);
								$arr_content[$data_count][count_6] = $cnt6 = count_person(6, $search_condition, $addition_condition);
								$arr_content[$data_count][count_7] = $cnt7 = count_person(7, $search_condition, $addition_condition);
								$arr_content[$data_count][count_8] = $cnt8 = count_person(8, $search_condition, $addition_condition);
                                                                /**/
                                                                $arr_content[$data_count][count_9] = $cnt9 = count_person(9, $search_condition, $addition_condition);
								$arr_content[$data_count][count_10] = $cnt10 = count_person(10, $search_condition, $addition_condition);
                                                                $arr_content[$data_count][cnt_man] = ($cnt1+$cnt2+$cnt3+$cnt4+$cnt5+$cnt6+$cnt7+$cnt8+$cnt9+$cnt10)+0;
                                                                /**/
								$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);
		
								if($rpt_order_index==$first_order){
									for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
								} // end if
					
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
							} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
						} // end if
					break;
	
				} // end switch case
			} // end for
			} // end if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1))
		} // end while
		
		$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4;
		$GRAND_TOTAL += $GRAND_TOTAL_5 + $GRAND_TOTAL_6 + $GRAND_TOTAL_7 + $GRAND_TOTAL_8+$GRAND_TOTAL_9+$GRAND_TOTAL_10;
	
//		echo "<pre>"; print_r($arr_content); echo "</pre>";
//		echo "GRAND_TOTAL = $GRAND_TOTAL";

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
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

		if($count_data){

			$parent = "";
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$COUNT_TOTAL = 0;
				$REPORT_ORDER = $arr_content[$data_count][type];
	//			echo "REPORT_ORDER=$REPORT_ORDER<br>";
	//			echo "parent($parent) = arr_con(".$arr_content[$data_count][parent].", list_type_text=$list_type_text (".($parent != $arr_content[$data_count][parent]).")<br>";
				$f_tab = false;
				if ($list_type_text == "�����ǹ�Ҫ��� - ��ا෾��ҹ��" && $REPORT_ORDER == "ORG") {
					$f_tab = true;
					if ($parent != $arr_content[$data_count][parent] && strlen($arr_content[$data_count][parent]) > 0) {
						$parent = $arr_content[$data_count][parent];

						$arr_data = (array) null;
						$arr_data[] =$parent;
						for($i=1; $i<=$heading_column; $i++) $arr_data[] = "";
						$arr_data[] = "";

						if ($FLAG_RTF)
							$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						else
							$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					} // end if ($parent != $arr_content[$data_count][parent] && strlen($arr_content[$data_count][parent])...
				} // end if ($list_type_text == "�����ǹ�Ҫ���" && $REPORT_ORDER == "ORG")
				$NAME = $arr_content[$data_count][name];
				for($i=1; $i<=$heading_column; $i++){
					${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
					if ($arr_column_sel[$arr_column_map[$i]]==1) // check ������ͧ map ���ͨкǡ���ç �Ѻ sel map
						$COUNT_TOTAL += ${"COUNT_".$arr_column_map[$i]};
				} // end for
                                 $CNT_MAN = $arr_content[$data_count][cnt_man];
				$SUM_AGE = $arr_content[$data_count][sum_age];
				$AVG_AGE = $SUM_AGE / $COUNT_TOTAL;
				$TOTAL_AGE += $SUM_AGE;
//				if ($arr_content[$data_count][type] = "MINISTRY" || $arr_content[$data_count][type] = "DEPARTMENT") {
//					echo "total-->sum_age=$SUM_AGE, count=$COUNT_TOTAL, AVG_AGE=$AVG_AGE<br>";
//				}

				$arr_data = (array) null;
				$arr_data[] = $NAME;
				for($i=1; $i<=$heading_column; $i++) $arr_data[] = ${"COUNT_".$i};
                                $arr_data[] = $CNT_MAN;
				$arr_data[] = $AVG_AGE;

				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
				}else{
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
				} // end if
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
			
			$AVG_TOTAL = $TOTAL_AGE / $GRAND_TOTAL;
			$TOTAL_GRAND_TOTAL = 0;
/*
			$arr_data = (array) null;
			$arr_data[] = "���";
			for($i=1; $i<=$heading_column; $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					$TOTAL_GRAND_TOTAL+=${"GRAND_TOTAL_".$arr_column_map[$i]};
				}
				$arr_data[] = ${"GRAND_TOTAL_".$i};
			}
			$arr_data[] = $TOTAL_AGE / $TOTAL_GRAND_TOTAL;

			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "FF0000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>"; */
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
	}else if($export_type=="graph"){//if($export_type=="report")
		$arr_content_map = (array) null;
		$arr_series_caption = (array) null;
		$arr_content_map[] = "name";
		$arr_series_caption[] = "";
		for($i=1; $i<=$heading_column; $i++){
			$arr_content_map[] = "count_".$i;
			$arr_series_caption[] = $arr_age_duration[$i][name];
		} // end for
		$arr_content_map[] = "sum_age";
		$arr_series_caption[] = "";
//		echo "<pre>"; print_r($arr_content); echo "</pre>";
		$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
		$arr_categories = array();
		$arr_series_caption_list = array(); 
		$f_first = true;
		for($i=1;$i<count($arr_content);$i++){
			$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
			$cntseq=0;
			for($j=0; $j<count($arr_content_map); $j++){ 
//				echo "level $j:";
				if ($arr_column_sel[$arr_column_map[$j]]==1 && strpos($arr_content_map[$arr_column_map[$j]],"count_")!==false) {
					$arr_series_caption_data[$cntseq][] = $arr_content[$i][$arr_content_map[$arr_column_map[$j]]];
//					echo "-->map:".$arr_content_map[$arr_column_map[$j]]." data=".$arr_content[$i][$arr_content_map[$arr_column_map[$j]]]."";
					if ($f_first) $arr_series_caption_list[] = $arr_series_caption[$arr_column_map[$j]];
					$cntseq++;
				}
//				echo "<br>";
			} // end for $j
			$f_first=false;	// check ����Ѻ�ͺ�á��ҹ��
		} // end for $i
		$series_caption_list = implode(";",$arr_series_caption_list);
//		echo "count (arr_series_caption_data)=".count($arr_series_caption_data)."<br>";
		for($j=0;$j<count($arr_series_caption_data);$j++){
			$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]);
		}
//		echo "<pre>"; print_r($arr_series_list); echo "</pre>";
		$chart_title = $report_title;
		$chart_subtitle = $company_name;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
		$categories_list = implode(";", $arr_categories)."";
	//	$arr_age_duration
		if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") 
			$series_caption_list = $arr_age_duration[1][name].";".$arr_age_duration[2][name].";".$arr_age_duration[3][name].";".$arr_age_duration[4][name];
		else
			$series_caption_list = $arr_age_duration[1][name].";".$arr_age_duration[2][name].";".$arr_age_duration[3][name].";".$arr_age_duration[4][name].";".$arr_age_duration[5][name].";".$arr_age_duration[6][name].";".$arr_age_duration[7][name].";".$arr_age_duration[8][name];
		$categories_list = implode(";", $arr_categories)."";
		if(strtolower($graph_type)=="pie"){
			if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") 
				$series_list = $GRAND_TOTAL_1.";".$GRAND_TOTAL_2.";".$GRAND_TOTAL_3.";".$GRAND_TOTAL_4;
			else
				$series_list = $GRAND_TOTAL_1.";".$GRAND_TOTAL_2.";".$GRAND_TOTAL_3.";".$GRAND_TOTAL_4.";".$GRAND_TOTAL_5.";".$GRAND_TOTAL_6.";".$GRAND_TOTAL_7.";".$GRAND_TOTAL_8;
		}else{
			$series_list = implode("|", $arr_series_list);
		}
//		echo($series_list);
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