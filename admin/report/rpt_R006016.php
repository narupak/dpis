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
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "a.PL_CODE";
		$line_join = "a.PL_CODE=e.PL_CODE";
		$line_name = "e.PL_NAME";
		$line_name_a = "a.PL_NAME";
		$position_no_name = "a.POS_NO_NAME";
		$position_no = "a.POS_NO";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);
		 $line_title = " ���˹�";
		 $type_code = "a.PT_CODE";
		 $select_type_code = ", a.PT_CODE";
		 $mgt_code = "a.PM_CODE";
		 $select_mgt_code = ", a.PM_CODE";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "a.PN_CODE";
		$line_join = "a.PN_CODE=e.PN_CODE";
		$line_name = "e.PN_NAME";
		$line_name_a = "a.PN_NAME";
		$position_no_name = "a.POEM_NO_NAME";
		$position_no = "a.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);
		$line_title = " ���˹�";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "a.EP_CODE";
		$line_join = "a.EP_CODE=e.EP_CODE";
		$line_name = "e.EP_NAME";
		$line_name_a = "a.EP_NAME";
		$position_no_name = "a.POEMS_NO_NAME";
		$position_no = "a.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);
		$line_title = " ���˹�";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "a.TP_CODE";
		$line_join = "a.TP_CODE=e.TP_CODE";
		$line_name = "e.TP_NAME";
		$line_name_a = "a.TP_NAME";
		$position_no_name = "a.POT_NO_NAME";
		$position_no = "a.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);
		$line_title = " ���˹�";
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "POSITION";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "POSITION"); 

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
				if($select_org_structure == 0) $select_list .= "a.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "b.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "a.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "b.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "POSITION" :
				if($select_list) $select_list .= ", ";
				$select_list .= $line_code;
				
				if($order_by) $order_by .= ", ";
				$order_by .= "f.LEVEL_SEQ_NO DESC, b.LEVEL_NO DESC";
				
				$heading_name .= $line_title;
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure == 0) $order_by .= "a.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "b.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "a.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "b.ORG_ID";
	}

	$search_per_type = 1;
	$search_per_status[] = 1;

	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	
	$list_type_text = $ALL_REPORT_TITLE;

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
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

//	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	if ($BKK_FLAG==1)
		$report_title = "�ѭ�ն�ͨ����Թ��Шӵ��˹�$PERSON_TYPE[$search_per_type]||$DEPARTMENT_NAME";
	else
		$report_title = "�ѭ�ն�ͨ����Թ��Шӵ��˹�$PERSON_TYPE[$search_per_type]||$DEPARTMENT_NAME||�ԡ���·ҧ ����ѭ�ա�ҧ";
	$report_code = "R0616";
	include ("rpt_R006016_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ
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

		$fname= "rpt_R006016_rtf.rtf";

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

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID,$ORG_ID, $PL_CODE,$line_code;
				
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
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}
				break;
				case "POSITION" :						
					if($PL_CODE){ 
						$arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
					}else{ 
						$arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
					} // end if
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order, $search_per_type;
		global $MINISTRY_ID, $DEPARTMENT_ID,$ORG_ID, $PL_CODE;
		
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
				case "POSITION" :	
					$PL_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select		$select_list, b.POS_ID, b.PER_ID, d.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_OFFNO, b.PER_SALARY, b.PER_MGTSALARY, $position_no_name as POS_NO_NAME,
											IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, $line_code as PL_CODE, $line_name as PL_NAME, b.LEVEL_NO $select_type_code  $select_mgt_code
						 from		(	
						 					(
												(
													(
														(
															$position_table a 
															left join PER_PERSONAL b on ($position_join) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (b.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) left join PER_LEVEL f on (b.LEVEL_NO=f.LEVEL_NO)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
										$search_condition
						 order by		$order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select		$select_list, b.POS_ID, b.PER_ID, d.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_OFFNO, b.PER_SALARY, b.PER_MGTSALARY, $position_no_name as POS_NO_NAME, 
											to_number(replace($position_no,'-','')) as POS_NO, $line_code as PL_CODE, $line_name as PL_NAME, b.LEVEL_NO $select_type_code  $select_mgt_code
						 from			$position_table a, PER_PERSONAL b, PER_ORG c, PER_PRENAME d, $line_table e, PER_LEVEL f, PER_ORG g
						 where		$position_join(+) and a.ORG_ID=c.ORG_ID(+)
											and b.PN_CODE=d.PN_CODE(+) and $line_join(+) and b.LEVEL_NO=f.LEVEL_NO(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
											$search_condition
						 order by		$order_by, $position_no_name, to_number(replace($position_no,'-','')) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		$select_list, b.POS_ID, b.PER_ID, d.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_OFFNO, b.PER_SALARY, b.PER_MGTSALARY, $position_no_name as POS_NO_NAME,
											$position_no as POS_NO, $line_code as PL_CODE, $line_name as PL_NAME, b.LEVEL_NO $select_type_code  $select_mgt_code
						 from		(	
						 					(
												(
													(
														(
															$position_table a 
															left join PER_PERSONAL b on ($position_join) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (b.PN_CODE=d.PN_CODE)
												) left join $line_table e on ($line_join)
											) left join PER_LEVEL f on (b.LEVEL_NO=f.LEVEL_NO)
										) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
										$search_condition
						 order by		$order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)) ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("a.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;
	$data_count = $data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){
						$MINISTRY_ID = trim($data[MINISTRY_ID]);
						if($MINISTRY_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
					} // end if
					break;
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
						if($DEPARTMENT_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
					} // end if
					break;
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
					} // end if

					if($rpt_order_index == (count($arr_rpt_order) - 1)){
						$data_row++;
						
						$PER_ID = $data[PER_ID];
						$POS_ID = $data[POS_ID];
						$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);
						$LEVEL_NO = $data[LEVEL_NO];
						$PER_SALARY = $data[PER_SALARY];
						$PER_MGTSALARY = $data[PER_MGTSALARY];

						$PT_CODE = trim($data[PT_CODE]);
						$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$PT_NAME = trim($data3[PT_NAME]);	
					
						$PM_CODE = trim($data[PM_CODE]);
						$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$PM_NAME = trim($data3[PM_NAME]);
						
						$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
						$db_dpis3->send_cmd($cmd);
//						$db_dpis->show_error();
						$data3 = $db_dpis3->get_array();
						$LEVEL_NAME=$data3[LEVEL_NAME];
						$POSITION_LEVEL = $data3[POSITION_LEVEL];
						if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
					
						$PER_MGTSALARY = $PER_TOTALSALARY = "";
						$cmd = " select EX_NAME, PMH_AMT from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
										  where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and PMH_ACTIVE = 1 and 
										(PMH_ENDDATE is NULL or PMH_ENDDATE >= '$UPDATE_DATE')
										  order by EX_SEQ_NO, b.EX_CODE ";
						$db_dpis2->send_cmd($cmd);
						while($data2 = $db_dpis2->get_array()){
							$EX_SEQ++;
							$EX_NAME = trim($data2[EX_NAME]);
							$PMH_AMT = $data2[PMH_AMT];
							$PER_TOTALSALARY += $PMH_AMT;
						}
						$PER_MGTSALARY = number_format($PER_TOTALSALARY,2);
	
						if ($PER_MGTSALARY > 0) {
							$arr_content[$data_count][type] = "DETAIL";
							$arr_content[$data_count][order] = $data_row;
							$arr_content[$data_count][pos_no] = $POS_NO;
							$arr_content[$data_count][position] = ($PM_CODE?"$PM_NAME ( ":"") . $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"") . ($PM_CODE?" )":"");
							$arr_content[$data_count][level_no] = $POSITION_LEVEL;
							$arr_content[$data_count][per_salary] = $PER_SALARY;
							$arr_content[$data_count][per_type] = $PT_NAME;
							$arr_content[$data_count][per_mgtsalary] = $PER_MGTSALARY;

							$data_count++;
						}
					} // end if
				break;
				case "POSITION" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
								$cmd = " select $line_name_a from $line_table a where trim($line_code)='$PL_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PL_NAME = $data2[PL_NAME];
						} // end if
						$addition_condition = generate_condition($rpt_order_index);
					} // end if

					if($rpt_order_index == (count($arr_rpt_order) - 1)){
						$data_row++;
						
						$PER_ID = $data[PER_ID];
						$POS_ID = $data[POS_ID];
						$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);
						$LEVEL_NO = $data[LEVEL_NO];
						$PER_SALARY = $data[PER_SALARY];
						$PER_MGTSALARY = $data[PER_MGTSALARY];
						
						$PT_CODE = trim($data[PT_CODE]);
						$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$PT_NAME = trim($data3[PT_NAME]);	
					
						$PM_CODE = trim($data[PM_CODE]);
						$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$PM_NAME = trim($data3[PM_NAME]);
						
						$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
						$db_dpis3->send_cmd($cmd);
//						$db_dpis->show_error();
						$data3 = $db_dpis3->get_array();
						$LEVEL_NAME=$data3[LEVEL_NAME];
						$POSITION_LEVEL = $data3[POSITION_LEVEL];
						if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
	
						$PER_MGTSALARY = $PER_TOTALSALARY = "";
						$cmd = " select EX_NAME, PMH_AMT from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
										  where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and PMH_ACTIVE = 1 and 
										(PMH_ENDDATE is NULL or PMH_ENDDATE >= '$UPDATE_DATE')
										  order by EX_SEQ_NO, b.EX_CODE ";
						$db_dpis2->send_cmd($cmd);
						while($data2 = $db_dpis2->get_array()){
							$EX_SEQ++;
							$EX_NAME = trim($data2[EX_NAME]);
							$PMH_AMT = $data2[PMH_AMT];
							$PER_TOTALSALARY += $PMH_AMT;
						}
						$PER_MGTSALARY = number_format($PER_TOTALSALARY,2);

						if ($PER_MGTSALARY > 0) {
							$arr_content[$data_count][type] = "DETAIL";
							$arr_content[$data_count][order] = $data_row;
							$arr_content[$data_count][pos_no] = $POS_NO;
							$arr_content[$data_count][position] = ($PM_CODE?"$PM_NAME (":"") . $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"") . ($PM_CODE?")":"");
							$arr_content[$data_count][level_no] = $POSITION_LEVEL;
							$arr_content[$data_count][per_salary] = $PER_SALARY;
							$arr_content[$data_count][per_type] = $PT_NAME;
							$arr_content[$data_count][per_mgtsalary] = $PER_MGTSALARY;

							$data_count++;
						}
					} // end if
				break;
			} // end switch case
		} // end for		
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
			$POSITION = $arr_content[$data_count][position];
			$POS_NO = $arr_content[$data_count][pos_no];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$PER_TYPE = $arr_content[$data_count][per_type];
			$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $POSITION;
			$arr_data[] = $POS_NO;
			$arr_data[] = $LEVEL_NO;
			$arr_data[] = $PER_SALARY;
			$arr_data[] = $PER_TYPE ;
			$arr_data[] = $PER_MGTSALARY;
			$arr_data[] ="";

			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		if (!$FLAG_RTF)
			$pdf->add_data_tab("", 7, "RHBL", $data_align, "", "12", "", "000000", "");		// ��鹻Դ��÷Ѵ
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