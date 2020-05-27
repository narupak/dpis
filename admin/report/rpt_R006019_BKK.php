<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_name = "e.PL_NAME";
		$position_no= "b.POS_NO";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		 $line_title=" สายงาน";
		 $type_code ="b.PT_CODE";
		 $select_type_code =",b.PT_CODE";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_name = "e.PN_NAME";
		$position_no= "b.POEM_NO";
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_name = "e.EP_NAME";
		$position_no= "b.POEMS_NO";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_join = "b.TP_CODE=e.TP_CODE";
		$line_name = "e.TP_NAME";
		$position_no= "b.POT_NO";
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if 		
	
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
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure == 0) $order_by .= "b.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "b.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "a.ORG_ID";
	}

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	}elseif($MINISTRY_ID){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ $PERSON_TYPE[$search_per_type]ที่ได้รับการเลื่อน $search_layer_no ขั้น ในช่วง 5 ปี||นับตั้งแต่ปีงบประมาณ $search_budget_year";
	$report_code = "R0602";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "85";
	$heading_width[1] = "20";
	$heading_width[2] = "82";
	$heading_width[3] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $search_budget_year;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"หน่วยงาน/ชื่อ - นามสกุล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขที่ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[3] * 5) ,7,"ปีงบประมาณ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		for($i=0; $i<5; $i++){ 
			if($i<4) $pdf->Cell($heading_width[3] ,7,($search_budget_year - $i),'LTBR',0,'C',1);
			else $pdf->Cell($heading_width[3] ,7,($search_budget_year - $i),'LTBR',1,'C',1);
		} // end for
	} // function		

	//PER_POSITION a
	//$PERCENT_NAME = array("น้อยกว่า 2%", "ตั้งแต่ 2% แต่ไม่ถึง 3%", "ตั้งแต่ 3% แต่ไม่ถึง 4%", "ตั้งแต่ 4% แต่ไม่ถึง 5%", "ตั้งแต่ 5% แต่ไม่ถึง 6%", "6%");
	function count_promote($search_budget_year,$percentname,$name){
		global $DPISDB, $db_dpis2;
		global $percent,$search_condition;

		if($name=="1"){	//คำนวณเงินงบประมาณเปรียบเทียบการเลื่อนเงินเดือนระบบเดิมกับระบบใหม่ว่าใช้วงเงินงบประมาณมากหรือน้อยกว่าเดิม
		
		}else if($name=="2"){	//เปรียบเทียบอัตราการเลื่อนเงินเดือนรายบุคคลตามระบบเดิมกับระบบใหม่
				if($percent==0){	//น้อยกว่า 2%   
					$percent_condition = " and ((SAH_SALARY - SAH_OLD_SALARY)/100) < 2";
				}else if($percent==1){	//ตั้งแต่ 2% แต่ไม่ถึง 3%		   
					$percent_condition = " and (((SAH_SALARY - SAH_OLD_SALARY)/100) >= 2 and ((SAH_SALARY - SAH_OLD_SALARY)/100)<3)";
				}else if($percent==2){	//ตั้งแต่ 3% แต่ไม่ถึง 4%		  
					$percent_condition = " and (((SAH_SALARY - SAH_OLD_SALARY)/100) >= 3 and ((SAH_SALARY - SAH_OLD_SALARY)/100)<4)";
				}else if($percent==3){	//ตั้งแต่ 4% แต่ไม่ถึง 5%		   
					$percent_condition = " and (((SAH_SALARY - SAH_OLD_SALARY)/100) >= 4 and ((SAH_SALARY - SAH_OLD_SALARY)/100)<5)";
				}else if($percent==4){	//ตั้งแต่ 5% แต่ไม่ถึง 6%			
					$percent_condition = " and (((SAH_SALARY - SAH_OLD_SALARY)/100) >= 5 and ((SAH_SALARY - SAH_OLD_SALARY)/100)<6)";
				}else if($percent==5){	//6%											
					$percent_condition = " and ((SAH_SALARY - SAH_OLD_SALARY)/100) = 6";
				}
				
				//if ($percent==0)
		/*
		select        PER_ID, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SALARY, ((nvl(SAH_SALARY_UP,0) + nvl(SAH_SALARY_EXTRA,0)) * 100 / nvl(SAH_SALARY,0)) as count_salaryhis
									 from            PER_SALARYHIS
									where        SAH_EFFECTIVEDATE >= '2008-10-01' and 
														SAH_EFFECTIVEDATE < '2009-10-01' and (((nvl(SAH_SALARY_UP,0) + nvl(SAH_SALARY_EXTRA,0)) * 100 / nvl(SAH_SALARY,0)) >= 0 and ((nvl(SAH_SALARY_UP,0) + nvl(SAH_SALARY_EXTRA,0)) * 100 / nvl(SAH_SALARY,0)) < 10)
														and nvl(SAH_SALARY,0) > 0 and (nvl(SAH_SALARY_UP,0) > 0 or nvl(SAH_SALARY_EXTRA,0) > 0)
		*/
					if($DPISDB=="odbc"){	//4006
						$cmd = " select		sum(SAH_SALARY - SAH_OLD_SALARY) as count_salaryhis
										from		PER_SALARYHIS inner join
													(	
														( 	
															(
															PER_PERSONAL a 
															left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
															) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
														) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
													) on (PER_SALARYHIS.PER_ID=a.PER_ID)
										where		(SAH_EFFECTIVEDATE >= '".($search_budget_year - 543 - 1)."-10-01' and SAH_EFFECTIVEDATE < '".($search_budget_year - 543)."-10-01') 
										$search_condition $percent_condition";
					}elseif($DPISDB=="oci8"){
						$search_condition = str_replace(" where ", " and ", $search_condition);
						/* $cmd = " select		sum(SAH_SALARY - SAH_OLD_SALARY) as count_salaryhis
										from			PER_SALARYHIS,PER_PERSONAL a, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
										where		PER_SALARYHIS.PER_ID=a.PER_ID and  a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and
														(SAH_EFFECTIVEDATE >= '".($search_budget_year - 543 - 1)."-10-01' and 
														SAH_EFFECTIVEDATE < '".($search_budget_year - 543)."-10-01') 
										$search_condition $percent_condition";*/
						$cmd = " select		count(a.PER_ID) as count_salaryhis
										from			PER_SALARYHIS,PER_PERSONAL a, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
										where		PER_SALARYHIS.PER_ID=a.PER_ID and  a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and
														(SAH_EFFECTIVEDATE >= '".($search_budget_year - 543 - 1)."-10-01' and SAH_EFFECTIVEDATE < '".($search_budget_year - 543)."-10-01') 
										$search_condition $percent_condition";
					}elseif($DPISDB=="mysql"){
						$cmd = " select		sum(SAH_SALARY - SAH_OLD_SALARY) as count_salaryhis
										from		PER_SALARYHIS inner join
													(	
														( 	
															(
															PER_PERSONAL a 
															left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
															) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
														) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
													) on (PER_SALARYHIS.PER_ID=a.PER_ID)
										where		(SAH_EFFECTIVEDATE >= '".($search_budget_year - 543 - 1)."-10-01' and SAH_EFFECTIVEDATE < '".($search_budget_year - 543)."-10-01') 
										$search_condition $percent_condition";
					}
					$count_salaryhis = $db_dpis2->send_cmd($cmd);
		//		$db_dpis2->show_error();
				if($count_salaryhis==1){
					$data = $db_dpis2->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					if($data[count_salaryhis] == 0) $count_salaryhis = 0;
				} // end if
		} //end else if		
		
		//echo "<br>".$count_salaryhis."::".$data[count_salaryhis]."=[ ".$percentname." ] : ".$cmd."<br>";
		return $count_salaryhis;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function

	$data_count = $data_row = 0;
	initialize_parameter(0);	
	$arrlevelsystem=array("เดิม (ข้อมูลจริง)","กรณีระบบใหม่");	$budget_year = array();
	$PERCENT_NAME = array("น้อยกว่า 2%", "ตั้งแต่ 2% แต่ไม่ถึง 3%", "ตั้งแต่ 3% แต่ไม่ถึง 4%", "ตั้งแต่ 4% แต่ไม่ถึง 5%", "ตั้งแต่ 5% แต่ไม่ถึง 6%", "6%");
	$percent_min = array(	0, 2, 3, 4, 5, 6);
	$percent_max = array( 2, 3, 4, 5, 6, 12);
	
	if($data_count > 0){	
		$data_row++;
		$arr_content[$data_count][type] = "DETAIL1";
		for($i=1; $i<=11; $i++) {
				for($j=0;$j<count($arrlevelsystem);$j++){
					for($k=0; $k<6; $k++){
						$arr_content[$data_count][name] = "ปี ".($search_budget_year - $k)."=="."==".$arrlevelsystem[$j];
					}
				}
		} 

		for ($percent=0; $percent < 6; $percent++){
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
				$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
				switch($REPORT_ORDER){
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
						} // end if
		
						if($rpt_order_index == (count($arr_rpt_order) - 1)){
							$data_row++;

							$arr_content[$data_count][type] = "DETAIL2";
							//$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 6)) . "$data_row.". $PERCENT_NAME[$percent];
							$arr_content[$data_count][name] = $PERCENT_NAME[$percent];
							for($i=1; $i<=6; $i++) $arr_content[$data_count]["count_".$i] = count_promote(($search_budget_year - ($i - 1)),$PERCENT_NAME[$percent],"2");

							$data_count++;

/****
							$arr_content[$data_count][type] = "DETAIL";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . "$data_row.". $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
							$arr_content[$data_count][pos_no] = $POS_NO;
							$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
							for($i=1; $i<=5; $i++) $arr_content[$data_count]["count_".$i] = count_promote(($search_budget_year - ($i - 1)), $PER_ID);
	
							$data_count++;
*****/
						} // end if
					break;
				} // end switch case
			} // end for
		} // end for
	
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>