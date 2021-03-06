<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	if($search_edu) { $search_edu.= ' or '; }
	$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; } 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(trim($RPTORD_LIST)) $arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("EDUCLEVEL", "ORG");

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
	if($search_en_code) $arr_search_condition[] = "(trim(d.EN_CODE) = trim('$search_en_code'))";
	if($search_em_code) $arr_search_condition[] = "(trim(d.EM_CODE) = trim('$search_em_code'))";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			}		
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
			} 
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}else{
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
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "�ٻẺ����͡��§ҹ : ". ($select_org_structure==1?"�ç���ҧ����ͺ���§ҹ - ":"�ç���ҧ��������� - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||��§ҹ������$PERSON_TYPE[$search_per_type]��ṡ����زԡ���֡��/�Ң��Ԫ��͡";
	$report_code = "R0209";
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

	$heading_width[0] = "100";
	$heading_width[1] = "12";
	$heading_width[2] = "15";
	$heading_width[3] = "20";
	$heading_width[4] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,$heading_name,'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] * 11) ,7,"�дѺ���˹�",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"���",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"�ӹǹ �á.",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"������",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=1; $i<=11; $i++){
			$pdf->Cell($heading_width[1] ,7,"$i",'LTBR',0,'C',1);
		} // end if
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"������",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LBR',1,'C',1);
	} // function		

	function count_person($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_search_condition, $search_el_code, $search_per_type, $select_org_structure, $search_edu;
		
//		echo "addition :: $addition_condition<br>";
		if(!$level_no){
			$search_condition = "";
			for($i=0; $i<count($arr_search_condition); $i++){
				if(strpos($arr_search_condition[$i], "trim(d.EN_CODE)")===false && strpos($arr_search_condition[$i], "trim(d.EM_CODE)")===false){
					if($search_condition) $search_condition .= " and ";
					$search_condition .= $arr_search_condition[$i];
				} // end if
			} // end for
			if(trim($search_condition)) $search_condition = " where ". $search_condition;
		} // end if

		if($level_no){ 
			$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
			if($DPISDB=="odbc") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="oci8") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
			elseif($DPISDB=="mysql") $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
		} // end if

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) ".(($level_no)?"inner":"left")." join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PER_ID=d.PER_ID".(($level_no)?"":"(+)")." and ($search_edu)
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) ".(($level_no)?"inner":"left")." join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID ";
			} // end if
		}elseif($search_per_type==2){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) ".(($level_no)?"inner":"left")." join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) 
								 					and a.PER_ID=d.PER_ID".(($level_no)?"":"(+)")." and ($search_edu)
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) ".(($level_no)?"inner":"left")." join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID ";
			} // end if
		}elseif($search_per_type==3){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) ".(($level_no)?"inner":"left")." join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_EDUCATE d
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
								 					and a.PER_ID=d.PER_ID".(($level_no)?"":"(+)")."  and ($search_edu)
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) ".(($level_no)?"inner":"left")." join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID ";
			} // end if
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID, $EN_CODE, $EM_CODE;
				
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
				case "EDUCNAME" :
					if($EN_CODE) $arr_addition_condition[] = "(trim(d.EN_CODE) = '$EN_CODE')";
					else $arr_addition_condition[] = "(trim(d.EN_CODE) = '' or d.EN_CODE is null)";
				break;
				case "EDUCMAJOR" :
					if($EM_CODE) $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE')";
					else $arr_addition_condition[] = "(trim(d.EM_CODE) = '' or d.EM_CODE is null)";
				break;
			} // end switch case
		} // end for
		
//		echo "<pre>"; print_r($arr_addition_condition); echo "</pre>";

		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $EN_CODE, $EM_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "EDUCNAME" :
					$EN_CODE = -1;
				break;
				case "EDUCMAJOR" :
					$EM_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($search_per_type==1){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(	
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												$search_condition  and ($search_edu)
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and ($search_edu)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(	
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												$search_condition  and ($search_edu)
							 order by		$order_by ";
		}
	}elseif($search_per_type==2){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(	
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												$search_condition  and ($search_edu)
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and ($search_edu)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(	
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												$search_condition  and ($search_edu)
							 order by		$order_by ";
		}
	}elseif($search_per_type==3){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(	
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												$search_condition  and ($search_edu)
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_EDUCATE d
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID)
												$search_condition  and ($search_edu)
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(	
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												$search_condition  and ($search_edu)
							 order by		$order_by ";
		}
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	unset($LEVEL_TOTAL);
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){		
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[����к�$ORG_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						for($i=1; $i<=11; $i++){
							$arr_content[$data_count]["level_". $i] = count_person($i, $search_condition, $addition_condition);
//							$LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_". $i];
						} // end for
						if($select_org_structure==0){
							if($ORG_ID == "") $arr_content[$data_count][count_all] = count_person(0, $search_condition, "(b.ORG_ID = 0 or b.ORG_ID is null)");
							else $arr_content[$data_count][count_all] = count_person(0, $search_condition, "(b.ORG_ID=$ORG_ID)");
						}elseif($select_org_structure==1){
							if($ORG_ID == "") $arr_content[$data_count][count_all] = count_person(0, $search_condition, "(a.ORG_ID = 0 or a.ORG_ID is null)");
							else $arr_content[$data_count][count_all] = count_person(0, $search_condition, "(a.ORG_ID=$ORG_ID)");
						}
							
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCNAME" :
					if($EN_CODE != trim($data[EN_CODE])){
						$EN_CODE = trim($data[EN_CODE]);
						if($EN_CODE == ""){
							$EN_NAME = "[����к��زԡ���֡��]";
						}else{
							$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EN_NAME = $data2[EN_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCNAME";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EN_NAME;
//						for($i=1; $i<=11; $i++){
//							$arr_content[$data_count]["level_". $i] = count_person($i, $search_condition, $addition_condition);
//							$LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_". $i];
//						} // end for
//						if($ORG_ID == "") $arr_content[$data_count][count_all] = count_person(0, $search_condition, "(b.ORG_ID = 0 or b.ORG_ID is null)");
//						else $arr_content[$data_count][count_all] = count_person(0, $search_condition, "(b.ORG_ID=$ORG_ID)");
			
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
						for($i=1; $i<=11; $i++){
							$arr_content[$data_count]["level_". $i] = count_person($i, $search_condition, $addition_condition);
							$LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_". $i];
						} // end for
//						if($ORG_ID == "") $arr_content[$data_count][count_all] = count_person(0, $search_condition, "(b.ORG_ID = 0 or b.ORG_ID is null)");
//						else $arr_content[$data_count][count_all] = count_person(0, $search_condition, "(b.ORG_ID=$ORG_ID)");
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for		
	} // end while
	
//	for($i=1; $i<=11; $i++) $LEVEL_TOTAL[$i] = count_person($i, $search_condition, "");	
	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
	$GRAND_ALL = count_person(0, $search_condition, "");
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			unset($COUNT_LEVEL);
			for($i=1; $i<=11; $i++) $COUNT_LEVEL[$i] = $arr_content[$data_count]["level_". $i];
			$COUNT_TOTAL = array_sum($COUNT_LEVEL);
			$COUNT_ALL = $arr_content[$data_count][count_all];
			
			$PERCENT_TOTAL = 0;
			if($COUNT_ALL) $PERCENT_TOTAL = ($COUNT_TOTAL / $COUNT_ALL) * 100;
			
			if($REPORT_ORDER=="ORG"){
				for($i=1; $i<=11; $i++) $COUNT_LEVEL[$i] = ($COUNT_LEVEL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_LEVEL[$i])):number_format($COUNT_LEVEL[$i])):"-");
				$COUNT_TOTAL = ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-");
				$COUNT_ALL = ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_ALL)):number_format($COUNT_ALL)):"-");				
				$PERCENT_TOTAL = ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-");
			}elseif($REPORT_ORDER=="EDUCMAJOR"){
				for($i=1; $i<=11; $i++) $COUNT_LEVEL[$i] = ($COUNT_LEVEL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_LEVEL[$i])):number_format($COUNT_LEVEL[$i])):"-");
				$COUNT_TOTAL = ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-");
				$COUNT_ALL = ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_ALL)):number_format($COUNT_ALL)):"");	
				$PERCENT_TOTAL = ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"");
			}else{
				for($i=1; $i<=11; $i++) $COUNT_LEVEL[$i] = ($COUNT_LEVEL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_LEVEL[$i])):number_format($COUNT_LEVEL[$i])):"");
				$COUNT_TOTAL = ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"");
				$COUNT_ALL = ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_ALL)):number_format($COUNT_ALL)):"");	
				$PERCENT_TOTAL = ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"");
			} // end if

			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$pdf->SetFont($font,'b','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			for($i=1; $i<=11; $i++) $pdf->Cell($heading_width[1], 7, $COUNT_LEVEL[$i], $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, $COUNT_TOTAL, $border, 0, 'R', 0);
			$pdf->Cell($heading_width[3], 7, $COUNT_ALL, $border, 0, 'R', 0);
			$pdf->Cell($heading_width[4], 7, $PERCENT_TOTAL, $border, 0, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=14; $i++){
				if($i>=1 && $i<=11){
					$line_start_y = $start_y;		$line_start_x += $heading_width[1];
					$line_end_y = $max_y;		$line_end_x += $heading_width[1];
				}elseif($i > 11){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i - 10];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i - 10];
				}else{
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				} // end if
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for
				
		$PERCENT_TOTAL = 0;
		if($GRAND_ALL) $PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_ALL) * 100;

		$border = "LTBR";
		$pdf->SetFont($font,'b','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->MultiCell($heading_width[0], 7, "���", $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0];
		$pdf->y = $start_y;
		for($i=1; $i<=11; $i++) $pdf->Cell($heading_width[1], 7, ($LEVEL_TOTAL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($LEVEL_TOTAL[$i])):number_format($LEVEL_TOTAL[$i])):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[3], 7, ($GRAND_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_ALL)):number_format($GRAND_ALL)):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[4], 7, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), $border, 1, 'R', 0);
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ����բ����� **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
	
?>