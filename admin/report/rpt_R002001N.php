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
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	 	if($search_edu) { $search_edu.= ' or '; }
		$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
	} 
	
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
		$heading_width[0] = "90";
		$heading_width[1] = "10";
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
		$heading_width[0] = "70";
		$heading_width[1] = "8";
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
		$heading_width[0] = "70";
		$heading_width[1] = "19";
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
		$heading_width[0] = "70";
		$heading_width[1] = "19";
	} // end if

//	echo "rpt_1..$RPTORD_LIST<br>";
//	if (strpos($RPTORD_LIST,"MINISTRY")===false) $RPTORD_LIST = "MINISTRY|$RPTORD_LIST";
//	else if(in_array("ALL", $list_type)) $RPTORD_LIST = "COUNTRY|$RPTORD_LIST";  

//print_r($list_type)."<br>";		//����Ҥ��� array

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
				elseif($select_org_structure==1)  $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

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
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID_1"; 

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) 	$select_list .= "b.ORG_ID_2";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_seq, $line_code as PL_CODE";
					
				if($order_by) $order_by .= ", ";
				$order_by .= "$line_seq, $line_code";
				
				$heading_name .=  $line_title;
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.LEVEL_SEQ_NO, a.LEVEL_NO";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "g.LEVEL_SEQ_NO, a.LEVEL_NO";

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
			case "EDUCNAME" :
				if($select_list) $select_list .= ", ";
					$select_list .= "d.EN_CODE";

				if($order_by) $order_by .= ", ";
					$order_by .= "d.EN_CODE";

				$heading_name .= " $EN_TITLE";
				break;
			case "EDUCMAJOR" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.EM_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.EM_CODE";

				$heading_name .= " $EM_TITLE";
				break;
			case "EDUCLEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.EL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.EL_CODE";

				$heading_name .= " $EL_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
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
	$arr_search_condition[] = "(c.ORG_ACTIVE = 1)";
	if($search_per_type == 1 || $search_per_type == 5){
		$arr_search_condition[] = "(b.POS_STATUS = 1)";
	}elseif($search_per_type == 2 || $search_per_type == 3){
		$arr_search_condition[] = "(b.POEM_STATUS = 1)";
	}elseif($search_per_type == 4){
		$arr_search_condition[] = "(b.POT_STATUS = 1)";
	}

	$list_type_text = $ALL_REPORT_TITLE;

//	echo "MINISTRY_ID=$MINISTRY_ID,  DEPARTMENT_ID=$DEPARTMENT_ID<br>";

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
		
		// ��ǹ��ҧ������Ҥ
		$list_type_text = "��ǹ��ҧ������Ҥ";
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
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";

		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
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
		
		// ��ҧ�����
		$list_type_text = "��ҧ�����";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";

		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
	}

//	echo "search_org_id=$search_org_id<br>";
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
		}
		if($select_org_structure==1) {
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
			$arr_search_condition[] = "($line_code='$line_search_code')";
			$list_type_text .= $line_search_name;
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
//	echo "list_type=".implode(",",$list_type)."<br>";

	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||�ӹǹ$PERSON_TYPE[$search_per_type] ��ṡ����дѺ���˹�";
	$report_code = "R0201";
	include ("rpt_R002001_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
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
	
		$fname= "rpt_R002001_rtf.rtf";
	
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

		$heading_width[2] = "15";
		$heading_width[3] = "15";
	}

	function count_person($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $arr_rpt_order, $search_per_type, $select_org_structure, $search_edu;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);

		if($DPISDB=="odbc"){
			if(in_array("EDUCNAME", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)  || in_array("EDUCLEVEL", $arr_rpt_order)){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(
													(	
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no'
													$search_condition and ($search_edu)
								 group by	a.PER_ID ";
			}else{
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no'
													$search_condition
								 group by	a.PER_ID ";
			  } // end if
		}elseif($DPISDB=="oci8"){
			if(in_array("EDUCNAME", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order) || in_array("EDUCLEVEL", $arr_rpt_order)){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_ORG e
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and ($search_edu)
													and trim(a.LEVEL_NO) = '$level_no'
													$search_condition
								 group by	a.PER_ID ";
			}else{
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG e
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
													and trim(a.LEVEL_NO) = '$level_no' 
													$search_condition 
								 group by	a.PER_ID ";
			} // end if
		}elseif($DPISDB=="mysql"){
			if(in_array("EDUCNAME", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order) || in_array("EDUCLEVEL", $arr_rpt_order)){
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(
													(	
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no'
													$search_condition and ($search_edu)
								 group by	a.PER_ID ";
			}else{
				$cmd = " select			count(a.PER_ID) as count_person
								 from		(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
								 where		trim(a.LEVEL_NO) = '$level_no'
													$search_condition and ($search_edu)
								 group by	a.PER_ID ";
			}
		} // end if

		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG c", "PER_ORG_ASS c", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error(); 
//		echo "count_person....cmd=$cmd ($count_person)<br>";
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE,$EL_CODE, $EP_CODE, $TP_CODE;
		global $line_code;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure == 0){ 
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else{
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_1" :
					if($select_org_structure == 0){ 
						if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}else{
						if($ORG_ID_1 && $ORG_ID_1!=-1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
					}
				break;
				case "ORG_2" :
					if($select_org_structure == 0){ 
						if($ORG_ID_2 && $ORG_ID_2!=-1) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
					}else{
						if($ORG_ID_2 && $ORG_ID_2!=-1) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
				break;
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER or a.PER_GENDER is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(c.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(c.PV_CODE) = '$PV_CODE' or c.PV_CODE is null)";
				break;
				case "EDUCNAME" :
					if($EN_CODE) $arr_addition_condition[] = "(trim(d.EN_CODE) = '$EN_CODE')";
					else $arr_addition_condition[] = "(trim(d.EN_CODE) = '$EN_CODE' or d.EN_CODE is null)";
				break;
				case "EDUCMAJOR" :
					if($EM_CODE) $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE')";
					else $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE' or d.EM_CODE is null)";
				break;
				case "EDUCLEVEL" :
					if($EL_CODE) $arr_addition_condition[] = "(trim(d.EL_CODE) = '$EL_CODE')";
					else $arr_addition_condition[] = "(trim(d.EL_CODE) = '$EL_CODE' or d.EL_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE, $EP_CODE, $EL_CODE, $TP_CODE;
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
					$PN_CODE = -1;
					$EP_CODE = -1;
					$TP_CODE=-1;
				break;
				case "SEX" :
					$PER_GENDER = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
					$PN_CODE = -1;
				break;
				case "EDUCNAME" :
					$EN_CODE = -1;
				break;
				case "EDUCMAJOR" :
					$EM_CODE = -1;
				break;
				case "EDUCLEVEL" :
					$EL_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//�ʴ���ª���˹��§ҹ
	if($DPISDB=="odbc"){
		if(in_array("EDUCNAME", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order) || in_array("EDUCLEVEL", $arr_rpt_order)){
			$cmd = " select			distinct $select_list
							 from	(
											(	
												(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
											) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
										) left join $line_table f on $line_join
												$search_condition and ($search_edu)
							 order by		$order_by ";
		}else{
			$cmd = " select			distinct $select_list
							 from	(
											(	
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
										) left join $line_table f on $line_join
											$search_condition
							 order by		$order_by ";
		} // end if
	}elseif($DPISDB=="oci8"){	
		$search_condition = str_replace(" where ", " and ", $search_condition);
		if(in_array("EDUCNAME", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)  || in_array("EDUCLEVEL", $arr_rpt_order)){
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_ORG e, $line_table f
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+)
												and a.DEPARTMENT_ID=e.ORG_ID(+) and $line_join(+) 
												$search_condition and ($search_edu)
							 order by		$order_by ";
		}else{
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG e, $line_table f
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.DEPARTMENT_ID=e.ORG_ID(+) and $line_join(+) 
												$search_condition
							 order by		$order_by ";
		} // end if
	}elseif($DPISDB=="mysql"){
		if(in_array("EDUCNAME", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order) || in_array("EDUCLEVEL", $arr_rpt_order)){
			$cmd = " select			distinct $select_list
							 from	(
											(	
												(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
											) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
										) left join $line_table f on $line_join
											$search_condition and ($search_edu)
							 order by		$order_by ";
		}else{
			$cmd = " select			distinct $select_list
							 from	(
											(	
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
										) left join $line_table f on $line_join
											$search_condition
							 order by		$order_by ";
		} // end if
	} // end if

	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG c", "PER_ORG_ASS c", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "main....cmd=$cmd ($count_data)<br>";
	//echo "$cmd ($count_data)<br>";
	//$db_dpis->show_error();

	$data_count = 0;
	unset($LEVEL_TOTAL);
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	$first_order = 1;	// order ��  0 = COUNTRY �ѧ�����ӹǳ ����� order ��� 1 (MINISTRY) ��͹
	while($data = $db_dpis->get_array()){
//		echo "data_count=$data_count | ".implode(",",$arr_rpt_order)."<br>";
//		echo "data-MINISTRY_ID=".trim($data[MINISTRY_ID])." && data-DEPARTMENT_ID=".trim($data[DEPARTMENT_ID]).", MINISTRY_ID($MINISTRY_ID)==DEPARTMENT_ID($DEPARTMENT_ID)<br>";
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
		for($rpt_order_index=$first_order; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
//				echo "REPORT_ORDER=$REPORT_ORDER<br>";
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if ($CTRL_TYPE < 3) {
						if($MINISTRY_ID != trim($data[MINISTRY_ID])){
							$MINISTRY_ID = trim($data[MINISTRY_ID]);
							if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$MINISTRY_NAME = $data2[ORG_NAME];
								$MINISTRY_SHORT = $data2[ORG_SHORT];
							
								if ($f_all) {
									$addition_condition = generate_condition($rpt_order_index);
									
									$arr_content[$data_count][type] = "MINISTRY";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $MINISTRY_NAME;
									$arr_content[$data_count][short_name] = $MINISTRY_SHORT;
									
									for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
										$tmp_level_no = $ARR_LEVEL_NO[$i];
										$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
										if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += count_person($tmp_level_no, $search_condition, $addition_condition);	// $arr_content[$data_count]["level_".$tmp_level_no];
									} //for($i=0; $i<count($ARR_LEVEL_NO); $i++)
									
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
									$data_count++;
								} // end if ($f_all)
							} // end if($MINISTRY_ID != "" && $MINISTRY_ID!=0 && $MINISTRY_ID!=-1)
						} // end if
					} // end if
					break;
					
				case "DEPARTMENT" :
					if ($CTRL_TYPE < 5) {
						if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
							$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
							if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$DEPARTMENT_NAME = $data2[ORG_NAME];
								$DEPARTMENT_ORG_SHORT = $data2[ORG_SHORT];

								$addition_condition = generate_condition($rpt_order_index);
	
								$arr_content[$data_count][type] = "DEPARTMENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $DEPARTMENT_NAME;
								$arr_content[$data_count][short_name] = $DEPARTMENT_ORG_SHORT;
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
									if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
								} // end for
	
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
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
								$ORG_SHORT = $data2[ORG_SHORT];
								if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;

								if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
									$addition_condition = generate_condition($rpt_order_index);
		
									$arr_content[$data_count][type] = "ORG";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_NAME;
									$arr_content[$data_count][short_name] = $ORG_SHORT;
									for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
										$tmp_level_no = $ARR_LEVEL_NO[$i];
										$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
										if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
//										echo "ORG..rpt_order_index($rpt_order_index)==first_order($first_order)<br>";
									} // end for
		
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
									$data_count++;
								} // end if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
						} // end if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1)
					}	
					break;
		
					case "ORG_1" :
						if($ORG_ID_1 != trim($data[ORG_ID_1])){
							$ORG_ID_1 = trim($data[ORG_ID_1]);
							$ORG_NAME_1 = $ORG_SHORT_1 = "����к�";
							if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_1 = $data2[ORG_NAME];
								$ORG_SHORT_1 = $data2[ORG_SHORT];
								if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
							} //end if($ORG_ID_1 != "" && $ORG_ID_1 != 0 && $ORG_ID_1 != -1)
								
							if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-")){
								$addition_condition = generate_condition($rpt_order_index);
				
								$arr_content[$data_count][type] = "ORG_1";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_NAME_1;
								//echo "ORG_1--data_count:$data_count, order_idx:$rpt_order_index, ".$arr_content[$data_count][name]."<br>";
								$arr_content[$data_count][short_name] = $ORG_SHORT_1;
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
									if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
								} // end for
				
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
							} // end if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-"))
					}
					break;
		
					case "ORG_2" :
						if($ORG_ID_2 != trim($data[ORG_ID_2])){
							$ORG_ID_2 = trim($data[ORG_ID_2]);
							$ORG_NAME_2 = $ORG_SHORT_2 = "����к�";
							if($ORG_ID_2!= "" && $ORG_ID_2 != 0 && $ORG_ID_2 != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME_2 = $data2[ORG_NAME];
								$ORG_SHORT_2 = $data2[ORG_SHORT];
								if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
							} // end if($ORG_ID_2!= "" && $ORG_ID_2 != 0 && $ORG_ID_2 != -1)
        
							if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-")){
									$addition_condition = generate_condition($rpt_order_index);
					
									$arr_content[$data_count][type] = "ORG_2";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_NAME_2;
									$arr_content[$data_count][short_name] = $ORG_SHORT_2;
									for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
										$tmp_level_no = $ARR_LEVEL_NO[$i];
										$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
										if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
									} // end for
					
									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
									$data_count++;
							} // end if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-"))
						}
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
							} // end if

							if(($PL_NAME !="" && $PL_NAME !="-") || ($BKK_FLAG==1 && $PL_NAME !="" && $PL_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
				
								$arr_content[$data_count][type] = "LINE";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $PL_NAME;
								$arr_content[$data_count][short_name] = "";
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
									if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
								} // end for
				
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
							} // end if
						}
					break;		
					case "SEX" :
						if($PER_GENDER != trim($data[PER_GENDER])){
							$PER_GENDER = trim($data[PER_GENDER]) + 0;

							$addition_condition = generate_condition($rpt_order_index);
			
							$arr_content[$data_count][type] = "SEX";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . (($PER_GENDER==1)?"���":(($PER_GENDER==2)?"˭ԧ":""));
							$arr_content[$data_count][short_name] = "";
							for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
								$tmp_level_no = $ARR_LEVEL_NO[$i];
								$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
								if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
							} // end for
			
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
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PV_NAME = $data2[PV_NAME];
							} // end if
							if(($PV_NAME !="" && $PV_NAME !="-") || ($BKK_FLAG==1 && $PV_NAME !="" && $PV_NAME !="-")){
								$addition_condition = generate_condition($rpt_order_index);
				
								$arr_content[$data_count][type] = "PROVINCE";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $PV_NAME;
								$arr_content[$data_count][short_name] = "";
								for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
									$tmp_level_no = $ARR_LEVEL_NO[$i];
									$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
									if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
								} // end for
				
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
							} // end if
						}
					break;

					case "EDUCNAME" :
						if($EN_CODE != trim($data[EN_CODE])){
							$EN_CODE = trim($data[EN_CODE]);
							if($EN_CODE != ""){
								$cmd = " select EN_SHORTNAME, EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
//								$EN_NAME = trim($data2[EN_SHORTNAME])?$data2[EN_SHORTNAME]:$data2[EN_NAME];
								$EN_NAME = $data2[EN_NAME];
							} // end if

							$addition_condition = generate_condition($rpt_order_index);
			
							$arr_content[$data_count][type] = "EDUCNAME";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $EN_NAME;
							$arr_content[$data_count][short_name] = "";
							for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
								$tmp_level_no = $ARR_LEVEL_NO[$i];
								$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
								if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
							} // end for
			
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					break;

					case "EDUCMAJOR" :
						if($EM_CODE != trim($data[EM_CODE])){
							$EM_CODE = trim($data[EM_CODE]);
							if($EM_CODE != ""){
								$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$EM_NAME = $data2[EM_NAME];
							} // end if

							$addition_condition = generate_condition($rpt_order_index);
			
							$arr_content[$data_count][type] = "EDUCMAJOR";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $EM_NAME;
							$arr_content[$data_count][short_name] = "";
							for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
								$tmp_level_no = $ARR_LEVEL_NO[$i];
								$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
								if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
							} // end for
			
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					break;
				case "EDUCLEVEL" :
						if($EL_CODE != trim($data[EL_CODE])){
							$EL_CODE = trim($data[EL_CODE]);
							if($EL_CODE != ""){
								$cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
//								$EN_NAME = trim($data2[EN_SHORTNAME])?$data2[EN_SHORTNAME]:$data2[EN_NAME];
								$EL_NAME = $data2[EL_NAME];
							} // end if

							$addition_condition = generate_condition($rpt_order_index);
			
							$arr_content[$data_count][type] = "EDUCLEVEL";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $EL_NAME;
							$arr_content[$data_count][short_name] = "";
							for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
								$tmp_level_no = $ARR_LEVEL_NO[$i];
								$arr_content[$data_count]["level_".$tmp_level_no] = count_person($tmp_level_no, $search_condition, $addition_condition);
								if($rpt_order_index==count($arr_rpt_order)-1) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
							} // end for
			
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					break;
					
				} // end switch case
			} // end for
		} // end if (trim($data[MINISTRY_ID]))
	} // end while
	
	//print_r($arr_rpt_order);
	if(array_search("EDUCNAME", $arr_rpt_order) !== false  && array_search("EDUCNAME", $arr_rpt_order) == 0){
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$LEVEL_TOTAL[$tmp_level_no] = count_person($tmp_level_no, $search_condition, "");
		} // end for
	} // end if
	if(array_search("EDUCMAJOR", $arr_rpt_order) !== false  && array_search("EDUCMAJOR", $arr_rpt_order) == 0){	
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$LEVEL_TOTAL[$tmp_level_no] = count_person($tmp_level_no, $search_condition, "");
		} // end for
	} // end if
	if(array_search("EDUCLEVEL", $arr_rpt_order) !== false  && array_search("EDUCLEVEL", $arr_rpt_order) == 0){	
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$LEVEL_TOTAL[$tmp_level_no] = count_person($tmp_level_no, $search_condition, "");
		} // end for
	} // end if

	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	if($export_type=="report"){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
		
//			echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
			if (!$result) echo "****** error ****** on open table for $table<br>";
		} else {
			$pdf->AutoPageBreak = false;
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
			if (!$result) echo "****** error ****** on open table for $table<br>";
		}
		
		if($count_data){

			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$NAME = $arr_content[$data_count][name];
//				echo "|$NAME|<br>";
				unset($COUNT_LEVEL);
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$COUNT_LEVEL[$tmp_level_no] = $arr_content[$data_count]["level_".$tmp_level_no];
				} // end for
				$COUNT_TOTAL = array_sum($COUNT_LEVEL);
				
				if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;
	
				$arr_data = (array) null;
				$arr_data[] = $NAME;// echo "$data_count ) $NAME";
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$arr_data[] = $COUNT_LEVEL[$tmp_level_no];
	//				echo " : ".$COUNT_LEVEL[$tmp_level_no];
				} // end for
	//			echo "<br>";
				$arr_data[] = $COUNT_TOTAL;	// sum level_no
				$arr_data[] = $PERCENT_TOTAL;	// percent level_no
					
				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}else{
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				} // end if
			} // end for

			$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;
	
			$arr_data = (array) null;
			$arr_data[] = "���";	// echo "��� : ";
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				$arr_data[] = $LEVEL_TOTAL[$tmp_level_no];
//				echo " : ".$LEVEL_TOTAL[$tmp_level_no];
			} // end for
//			echo "<br>";
			$arr_data[] = $GRAND_TOTAL;	// sum level_no
			$arr_data[] = $PERCENT_TOTAL;	// percent level_no

			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "FF0000", "");
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
		$arr_content_map = (array) null;
		$arr_series_caption = (array) null;
		$arr_content_map[] = "name";
		$arr_series_caption[] = "";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content_map[] = "level_".$tmp_level_no;
			$arr_series_caption[] = $ARR_LEVEL_SHORTNAME[$i];
		} // end for
		$arr_content_map[] = "sum";
		$arr_content_map[] = "percent";
		$arr_series_caption[] = "";
		$arr_series_caption[] = "";
//		echo "<pre>"; print_r($arr_content); echo "</pre>";
		$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
		$arr_categories = array();
		$f_first = true;
		for($i=1;$i<count($arr_content);$i++){
			$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
			$cntseq=0;
			for($j=0; $j<count($arr_content_map); $j++){ 
//				echo "level $j:";
				if ($arr_column_sel[$arr_column_map[$j]]==1 && strpos($arr_content_map[$arr_column_map[$j]],"level_")!==false) {
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
		for($j=2;$j<count($arr_content_key);$j++){
			$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j])."";
		}
		$chart_title = $report_title;
		$chart_subtitle = $company_name;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
		$categories_list = implode(";", $arr_categories)."";
		if(strtolower($graph_type)=="pie"){
			$series_list = implode(";", $LEVEL_TOTAL);
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