<?php
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
	$db_dpis3  = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID(+)";
		$line_table = "PER_LINE";
		$line_join = "c.PL_CODE=e.PL_CODE(+)";
		$line_code = "c.PL_CODE";
		$line_code_po = "c.POS_NO";
		$line_name = "e.PL_NAME  as PL_NAME";
		$line_short_name = "PL_SHORTNAME";
                $order_position = "to_number(replace(c.POS_NO,'-',''))";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);		
		$line_title=" ��§ҹ";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID(+)";
		$line_table = "PER_POS_NAME";
		$line_join = "c.PN_CODE=e.PN_CODE(+)";
		$line_code = "c.PN_CODE";
		$line_name = "e.PN_NAME  as PL_NAME";
		$line_code_po = "c.POEM_NO";
                $order_position = "to_number(replace(c.POEM_NO,'-',''))";
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);		
		$line_title=" ���͵��˹�";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID(+)";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "c.EP_CODE=e.EP_CODE(+)";
		$line_code = "c.EP_CODE";
		$line_name = "e.EP_NAME  as PL_NAME";
		$line_code_po = "c.POEMS_NO";
                $order_position = "to_number(replace(c.POEMS_NO,'-',''))";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);		
		$line_title=" ���͵��˹�";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID(+)";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "c.TP_CODE=e.TP_CODE(+)";
		$line_code = "c.TP_CODE";
		$line_name = "e.TP_NAME  as PL_NAME";
		$line_code_po = "c.POT_NO";
                $order_position = "to_number(replace(c.POT_NO,'-',''))";
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);		
		$line_title=" ���͵��˹�";
	} // end if	

	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	//$arr_search_condition[] = "(b.PER_STATUS = 1)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
  	if(trim($search_budget_year)){ 
		if($DPISDB=="odbc"){ 
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_budget_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_budget_year - 543)."-10-01')";
		}elseif($DPISDB=="oci8"){
			$arr_search_condition[] = "(SUBSTR(a.KF_START_DATE, 1, 10) >= '". ($search_budget_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(SUBSTR(a.KF_END_DATE, 1, 10) < '". ($search_budget_year - 543)."-10-01')";
		}elseif($DPISDB=="mysql"){
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_budget_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_budget_year - 543)."-10-01')";
		} // end if
	} // end if
	$arr_search_condition[] = "(a.KF_CYCLE in (". implode(",", $search_kf_cycle) ."))";
        
        if($order_by==1)$t_order_by = ' a.ORG_ID ,  a.TOTAL_SCORE desc, b.LEVEL_NO ';
        else if($order_by==2)$t_order_by = '  a.ORG_ID ,  a.TOTAL_SCORE asc, b.LEVEL_NO ';
        else $t_order_by = " a.ORG_ID , $order_position ,  a.KF_CYCLE, a.TOTAL_SCORE desc, b.LEVEL_NO ";
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
                        //���
			//if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
			//else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
                        $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type)){
		// ��§ҹ
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= $line_search_name;
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$search_pv_code')";
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
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
//	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||��§ҹ��û����Թ KPI ��ºؤ�� �է�����ҳ $search_budget_year";
	if(in_array(1, $search_kf_cycle)) $report_title .= " ���駷�� 1";
	if(in_array(2, $search_kf_cycle)) $report_title .= (in_array(1, $search_kf_cycle)?" ���":"")." ���駷�� 2";
	if($search_diff_type == -1) $report_title .= "||(੾���дѺ�ͧ���ö�е�ӡ��ҷ���˹�)";
	elseif($search_diff_type == 1) $report_title .= "||(੾���дѺ�ͧ���ö���٧���ҷ���˹�)";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0901";
	include ("rpt_R009001_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
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

		$fname= "rpt_R009001_rtf.rtf";

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
		$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.ORG_ID, d.ORG_NAME, $line_name,
											a.SUM_KPI, a.SUM_COMPETENCE, a.TOTAL_SCORE, a.KF_CYCLE
						 from			(
												(
													(
														(
															PER_KPI_FORM a
															inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
														) inner join $position_table c on ($position_join)
													) inner join PER_ORG d on (c.ORG_ID=d.ORG_ID)
												) inner join $line_table e on ($line_join)
											) left join PER_PRENAME f on (b.PN_CODE=f.PN_CODE)
						 $search_condition
						 order by		c.ORG_ID, a.KF_CYCLE, a.TOTAL_SCORE desc, b.LEVEL_NO ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		/*$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.ORG_ID, d.ORG_NAME, $line_name,
											a.SUM_KPI, a.SUM_COMPETENCE, a.TOTAL_SCORE, a.KF_CYCLE
						 from			PER_KPI_FORM a, PER_PERSONAL b, $position_table c, PER_ORG d, $line_table e, PER_PRENAME f
						 where		a.PER_ID=b.PER_ID and $position_join and c.ORG_ID=d.ORG_ID and $line_join 
						 					and b.PN_CODE=f.PN_CODE(+)
											$search_condition
						 order by		c.ORG_ID, a.KF_CYCLE, a.TOTAL_SCORE desc, b.LEVEL_NO ";*/
                /*Release 5.2.1.19 */
                $cmd = "    select a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, a.ORG_ID as ORG_ID, d.ORG_NAME, $line_name, $line_code_po,
                                a.SUM_KPI, a.SUM_COMPETENCE, a.TOTAL_SCORE, a.KF_CYCLE
                            from PER_KPI_FORM a, 
                                PER_PERSONAL b, 
                                $position_table c, 
                                PER_ORG d, 
                                $line_table e, 
                                PER_PRENAME f
                            where a.PER_ID=b.PER_ID 
                                and $position_join 
                                and a.ORG_ID=d.ORG_ID 
                                and $line_join 
                                and b.PN_CODE=f.PN_CODE(+)
                                $search_condition
                            order by  $t_order_by ";
                
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.ORG_ID, d.ORG_NAME, $line_name,
											a.SUM_KPI, a.SUM_COMPETENCE, a.TOTAL_SCORE, a.KF_CYCLE
						 from			(
												(
													(
														(
															PER_KPI_FORM a
															inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
														) inner join $position_table c on ($position_join)
													) inner join PER_ORG d on (c.ORG_ID=d.ORG_ID)
												) inner join $line_table e on ($line_join)
											) left join PER_PRENAME f on (b.PN_CODE=f.PN_CODE)
						 $search_condition
						 order by		c.ORG_ID, a.KF_CYCLE, a.TOTAL_SCORE desc, b.LEVEL_NO ";
	} // end if
	if($select_org_structure==1) { 
		/*$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);*/
            $cmd = str_replace("a.ORG_ID", "a.ORG_ID_ASS", $cmd);
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
    //echo "<pre>".$cmd;  
	$data_count = 0;
	$data_row = 0;
	$ORG_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($ORG_ID != $data[ORG_ID]){
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];
			if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;

			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][name] = $ORG_NAME;
			
			$data_row = 0;
			$data_count++;
		} // end if
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$LEVEL_NO = $data[LEVEL_NO];
		$PL_NAME = $data[PL_NAME];
		$SUM_KPI = $data[SUM_KPI];
		$SUM_COMPETENCE = $data[SUM_COMPETENCE];
		$TOTAL_SCORE = $data[TOTAL_SCORE];
		$KF_CYCLE = $data[KF_CYCLE];
		if($search_per_type==1){$pos_no = $data[POS_NO];}//�Ţ�����˹觢���Ҫ���
		if($search_per_type==2){$pos_no = $data[POEM_NO];}//�Ţ�����˹��١��ҧ��Ш�
		if($search_per_type==3){$pos_no = $data[POEMS_NO];}//�Ţ�����˹觾�ѡ�ҹ�Ҫ���
		if($search_per_type==4){$pos_no = $data[POT_NO];}//�Ţ�����˹��١��ҧ���Ǥ���
		
		
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
		$db_dpis3->send_cmd($cmd);
//		$db_dpis->show_error();
		$data3 = $db_dpis3->get_array();
		$LEVEL_NAME=$data3[LEVEL_NAME];
		$POSITION_LEVEL = $data3[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = $data2[PT_NAME];
		
		if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		if ($have_pic && $img_file){
			if ($FLAG_RTF)
			$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
			else
			$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
		}
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][perline] = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"".$POSITION_LEVEL;
		$arr_content[$data_count][pos_no] = $pos_no;
		$arr_content[$data_count][sum_kpi] = $SUM_KPI;
		$arr_content[$data_count][sum_competence] = $SUM_COMPETENCE;
		$arr_content[$data_count][total_score] = $TOTAL_SCORE;
		$arr_content[$data_count][kf_cycle] = $KF_CYCLE;
                
                
                //�� �дѺ�š�û����Թ Begin...
                $BudgetYear = $search_budget_year;
                /*$cmd = " SELECT AL_NAME 
                    FROM PER_ASSESS_LEVEL 
                    WHERE AL_YEAR = '$BudgetYear' AND AL_CYCLE = $KF_CYCLE ";*/
                $cmd =" select	DISTINCT AL.AL_NAME  , AM.AM_NAME
                        from	PER_ASSESS_LEVEL AL , PER_ASSESS_MAIN AM
                        where AL.AM_CODE=AM.AM_CODE(+) 
                        AND (AL.DEPARTMENT_ID = $DEPARTMENT_ID AND AL.org_id=$ORG_ID) 
                        AND (AL.PER_TYPE=$search_per_type) 
                        AND (AL.AL_YEAR = '$BudgetYear') 
                        AND (AL.AL_CYCLE = $KF_CYCLE) 
                        AND  $TOTAL_SCORE BETWEEN  AL.AL_POINT_MIN AND AL.AL_POINT_MAX";
               
//                echo "<pre>".$cmd;
//                die();
		$cnt = $db_dpis2->send_cmd($cmd);
                if(!empty($cnt)){
                    $data2 = $db_dpis2->get_array();
                    $AL_NAME = trim($data2[AL_NAME]);
                    $AM_NAME = trim($data2[AM_NAME]);
                }else{
                   $AL_NAME=""; 
                   $AM_NAME=""; 
                }
                if(empty($SUM_KPI) && empty($SUM_COMPETENCE) && empty($TOTAL_SCORE)){$AL_NAME="";$AM_NAME=""; }
                $arr_content[$data_count][EvaluationResults] = $AL_NAME;
                $arr_content[$data_count][ResultsAM_NAME] = $AM_NAME;
		// end.
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
			$ORDER = $arr_content[$data_count][order];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$PL_NAME = $arr_content[$data_count][perline];
			$SUM_KPI = $arr_content[$data_count][sum_kpi];
			$SUM_COMPETENCE = $arr_content[$data_count][sum_competence];
			$TOTAL_SCORE = $arr_content[$data_count][total_score];
			$KF_CYCLE = $arr_content[$data_count][kf_cycle];
                        $EvaluationResults=$arr_content[$data_count][EvaluationResults];
                        $ResultsAM_NAME = $arr_content[$data_count][ResultsAM_NAME];
                        if($have_am && !$have_al){
                            $AM_AL_NAME = $ResultsAM_NAME;
                        }else if($have_al && !$have_am){
                            $AM_AL_NAME = $EvaluationResults;
                        }else if($have_al && $have_am){
                            $AM_AL_NAME = ($EvaluationResults&&$ResultsAM_NAME)?$ResultsAM_NAME."/".$EvaluationResults:(($EvaluationResults&&!$ResultsAM_NAME)?$ResultsAM_NAME=$EvaluationResults:((!$EvaluationResults&&$ResultsAM_NAME)?$ResultsAM_NAME=$ResultsAM_NAME:""));
                        }
                            

			if($REPORT_ORDER == "ORG"){
				$arr_data = (array) null;
				if ($have_pic && $img_file) $arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$arr_data[] =  "<**1**>$NAME";
				$data_align = array("L","L","L","L","L","L","L");
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				if ($have_pic && $img_file) $arr_data[] = $IMAGE;
				$arr_data[] = $NAME;
				$arr_data[] = $POS_NO;
				$arr_data[] = $PL_NAME;
				$arr_data[] = $KF_CYCLE;
				$arr_data[] = $SUM_KPI;
				$arr_data[] = $SUM_COMPETENCE;
				$arr_data[] = $TOTAL_SCORE;
                                if($have_am || $have_al){
                                    $arr_data[] = $AM_AL_NAME;
                                }    
				if($have_pic)                                               //
				$data_align = array("C","C","L","C","L","C","R","R","R");       // ��� format align �١�礵ç���
				else														//
				$data_align = array("C","L","C","L","C","R","R","R");     		//
				
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "TRHL", $data_align, "", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}
		} // end for
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