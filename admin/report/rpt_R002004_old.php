<?
//	header ('Content-type: text/html; charset=windows-874');
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	if($export_type=="report"){
		include ("../php_scripts/function_share.php");
	}else if($export_type=="graph"){
		include ("../../admin//php_scripts/function_share.php");	//���͹䢷���ͧ����ʴ���
	}	

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

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
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "LEVEL", "SEX", "PROVINCE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";	
			
				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.DEPARTMENT_ID";
				elseif($select_org_structure==1) $select_list .= "a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.DEPARTMENT_ID";
				elseif($select_org_structure==1) $order_by .= "a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID";
				
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID";

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
				$select_list .= "$line_code as PL_CODE";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "$line_code";
				
				$heading_name .=$line_title;
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, g.LEVEL_SEQ_NO";
				
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
		if($select_org_structure==0)  $order_by = "b.ORG_ID";
		elseif($select_org_structure==1)  $order_by = "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list = "b.ORG_ID";
		elseif($select_org_structure==1)  $select_list = "a.ORG_ID";
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ������Ҥ
		$list_type_text = "��ǹ��ҧ������Ҥ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ҧ�����
		$list_type_text = "��ҧ�����";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
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
	}elseif($list_type == "PER_LINE"){
		// ��§ҹ
		$list_type_text = "";
		
		if($line_search_code){
				$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
				$list_type_text .= "$line_search_name";
		}
	}elseif($list_type == "PER_COUNTRY"){
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
	}else{
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
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
	$report_title = "$DEPARTMENT_NAME||�ç���ҧ����$PERSON_TYPE[$search_per_type] � �ѹ��� ". (date("d") + 0) ." ". $month_full[(date("m") + 0)][TH] ." ". (date("Y") + 543);
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	
	$report_code = "R0204";
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

	$heading_width[0] = "107";
	$heading_width[1] = "20";
	$heading_width[2] = "20";

	if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
		$heading_column = 4;
		$arr_age_duration[1][name] = "21 - 30";
		$arr_age_duration[2][name] = "31 - 40";	
		$arr_age_duration[3][name] = "41 - 50";	
		$arr_age_duration[4][name] = "51 - 60";	
	} else {
		$heading_column = 8;
		$arr_age_duration[1][name] = "<=24";
		$arr_age_duration[2][name] = "25 - 29";	
		$arr_age_duration[3][name] = "30 - 34";	
		$arr_age_duration[4][name] = "35 - 39";	
		$arr_age_duration[5][name] = "40 - 44";	
		$arr_age_duration[6][name] = "45 - 49";	
		$arr_age_duration[7][name] = "50 - 54";	
		$arr_age_duration[8][name] = ">=55";
	}
	
	function print_header(){
		global $pdf, $heading_width, $heading_name, $SESS_DEPARTMENT_NAME, $heading_column;
		global $arr_age_duration, $NUMBER_DISPLAY;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,$heading_name,'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] * $heading_column) ,7,"��ǧ����",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"���������",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=1; $i<=$heading_column; $i++) $pdf->Cell($heading_width[1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($arr_age_duration[$i][name]):$arr_age_duration[$i][name]),'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',1,'C',1);
	} // function		

	function count_person($age_index, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type, $select_org_structure;
//		global $ORG_NAME, $arr_age_duration;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		
		if($age_index) $age_condition = generate_age_condition($age_index);
//		else $age_condition = "a.PER_BIRTHDATE is not null and trim(a.PER_BIRTHDATE) <> ''";
		else $age_condition = "a.PER_BIRTHDATE is not null";
		$search_condition = str_replace(" where ", " and ", $search_condition);

		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													PER_PERSONAL a
													left join $position_table b on ($position_join)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
							  where		$age_condition
												$search_condition
							  group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c
							 where			$position_join and b.ORG_ID=c.ORG_ID(+) and $age_condition
												$search_condition
							  group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													PER_PERSONAL a
													left join $position_table b on ($position_join)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
							  where		$age_condition
												$search_condition
							  group by	a.PER_ID ";
		} // end if
		
		if($age_index){
			if($select_org_structure==1) { 
				$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
				$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			}
			$count_person = $db_dpis2->send_cmd($cmd);
//			echo (!$db_dpis2->ERROR)?$db_dpis2->show_error():"$cmd<br>";
//			$db_dpis2->show_error(); echo "<br><hr>";
			if($count_person==1){
				$data = $db_dpis2->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if($data[count_person] == 0) $count_person = 0;
			} // end if

			return $count_person;
		}else{
			$sum_age = 0;
			
			$cmd = str_replace("count(a.PER_ID) as count_person", "a.PER_ID, a.PER_BIRTHDATE", $cmd) . ", a.PER_BIRTHDATE order by a.PER_BIRTHDATE, a.PER_ID";
			$db_dpis2->send_cmd($cmd);
//			echo (!$db_dpis2->ERROR)?$db_dpis2->show_error():"$cmd<br>";
//			$db_dpis2->show_error();
			while($data = $db_dpis2->get_array()){
				$PER_ID = $data[PER_ID];
				$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);

//				�Դ������ب�ԧ (����ѧ���֧�ѹ�Դ�����Ѻ�繻�)
//				$PER_AGE = floor(date_difference(date("Y-m-d"), $PER_BIRTHDATE, "y"));

//				�Դ੾�л��Դ �Ѻ�ջѨ�غѹ
				list($BIRTHYEAR, $BIRTHMONTH, $BIRTHDATE) = split("-", $PER_BIRTHDATE, 3);
				$PER_AGE = date("Y") - $BIRTHYEAR;
				
//				$arr_age[$ORG_NAME][$arr_age_duration[$age_index][name]][$PER_ID.":".$PER_BIRTHDATE] = $PER_AGE;
//				$arr_age[$ORG_NAME][$PER_ID.":".$PER_BIRTHDATE] = $PER_AGE;
				$sum_age += $PER_AGE;
			} // end while	
			
//			echo "<pre>"; print_r($arr_age); echo "</pre>";
			return $sum_age;
		} // end if		
	} // function
	
	function generate_age_condition($age_index){
		global $DPISDB, $arr_age_duration;
		
		if($arr_age_duration[$age_index][name]=="<=24"){
//			�Դ������ب�ԧ (����ѧ���֧�ѹ�Դ�����Ѻ�繻�)
			$birthdate_min = date_adjust(date("Y-m-d"), "y", -25);
			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) > '$birthdate_min')";
			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) > '$birthdate_min')";
			elseif($DPISDB=="mysql") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) > '$birthdate_min')";

//			�Դ੾�л��Դ �Ѻ�ջѨ�غѹ
//			$birthyear_min = date("Y") - 25;
//			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 4) > '$birthyear_min')";
//			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) > '$birthyear_min')";

//			echo "age <= 24 :: $age_condition<br>";
		}elseif($arr_age_duration[$age_index][name]==">=55"){
//			�Դ������ب�ԧ (����ѧ���֧�ѹ�Դ�����Ѻ�繻�)
			$birthdate_max = date_adjust(date("Y-m-d"), "y", -55);
			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_max')";
			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) <= '$birthdate_max')";
			elseif($DPISDB=="mysql") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_max')";

//			�Դ੾�л��Դ �Ѻ�ջѨ�غѹ
//			$birthyear_max = date("Y") - 55;
//			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 4) <= '$birthyear_max')";
//			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) <= '$birthyear_max')";

//			echo "age >= 55 :: $age_condition<br>";
		}else{
			list($min_age, $max_age) = split("-", $arr_age_duration[$age_index][name], 2);
			$min_age = (trim($min_age) + 0) * -1;
			$max_age = (trim($max_age) + 1) * -1;

//			�Դ������ب�ԧ (����ѧ���֧�ѹ�Դ�����Ѻ�繻�)
			$birthdate_max = date_adjust(date("Y-m-d"), "y", $min_age);
			$birthdate_min = date_adjust(date("Y-m-d"), "y", $max_age);
			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_max' and LEFT(trim(a.PER_BIRTHDATE), 10) > '$birthdate_min')";
			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) <= '$birthdate_max' and SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) > '$birthdate_min')";
			elseif($DPISDB=="mysql") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_max' and LEFT(trim(a.PER_BIRTHDATE), 10) > '$birthdate_min')";

//			�Դ੾�л��Դ �Ѻ�ջѨ�غѹ
//			$birthyear_max = date("Y") + $min_age;
//			$birthyear_min = date("Y") + $max_age;
//			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(a.PER_BIRTHDATE), 4) <= '$birthyear_max' and LEFT(trim(a.PER_BIRTHDATE), 4) > '$birthyear_min')";
//			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) <= '$birthyear_max' and SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) > '$birthyear_min')";

//			echo "age $min_age - $max_age :: $age_condition<br>";
		} // end if
		
		return $age_condition;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $LEVEL_NAME, $POSITION_LEVEL, $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $EP_CODE;
		global $line_code;	
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else if($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_1" :
					if($select_org_structure==0){
						if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}else  if($select_org_structure==1){
						if($ORG_ID_1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_2" :
					if($select_org_structure==0){
						if($ORG_ID_2) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
					}else  if($select_org_structure==1){
						if($ORG_ID_2) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
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
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $LEVEL_NAME, $POSITION_LEVEL, $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $EP_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
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
													PER_PERSONAL a
													left join $position_table b on ($position_join)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_LEVEL g
						 where			$position_join and b.ORG_ID=c.ORG_ID(+) and a.LEVEL_NO=g.LEVEL_NO(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													PER_PERSONAL a
													left join $position_table b on ($position_join)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join  PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
											$search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo $cmd."<BR>";
	//$db_dpis->show_error();
	$data_count = 0;
	for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} = 0;
//	$GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select a.ORG_NAME,a.ORG_SHORT,b.ORG_NAME as ORG_NAME_PARENT from PER_ORG a join PER_ORG b on (a.ORG_ID_REF=b.ORG_ID) where a.ORG_ID=$ORG_ID order by b.ORG_NAME ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							$ORG_NAME_PARENT = $data2[ORG_NAME_PARENT];
//							echo "ORG_NAME=$ORG_NAME, ORG_NAME_PARENT=$ORG_NAME_PARENT<br>";
						} // end if
           if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][parent] = $ORG_NAME_PARENT;
						$arr_content[$data_count][short_name] = $ORG_SHORT;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
						$arr_content[$data_count][count_7] = count_person(7, $search_condition, $addition_condition);
						$arr_content[$data_count][count_8] = count_person(8, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						if($ORG_ID_1 != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							$ORG_SHORT_1 = $data2[ORG_SHORT];
						} // end if
              if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][parent] = "";
						$arr_content[$data_count][short_name] = $ORG_SHORT_1;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
						$arr_content[$data_count][count_7] = count_person(7, $search_condition, $addition_condition);
						$arr_content[$data_count][count_8] = count_person(8, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);		
						$data_count++;
					} // end if
					} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != trim($data[ORG_ID_2])){
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						if($ORG_ID_2 != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							$ORG_SHORT_2 = $data2[ORG_SHORT];
						} // end if
         if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][parent] = "";
						$arr_content[$data_count][short_name] = $ORG_SHORT_2;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
						$arr_content[$data_count][count_7] = count_person(7, $search_condition, $addition_condition);
						$arr_content[$data_count][count_8] = count_person(8, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][parent] = "";
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
						$arr_content[$data_count][count_7] = count_person(7, $search_condition, $addition_condition);
						$arr_content[$data_count][count_8] = count_person(8, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
				break;
		
				case "LEVEL" :
					if($LEVEL_NO != trim($data[LEVEL_NO])){
						$LEVEL_NO = trim($data[LEVEL_NO]);
						$LEVEL_NAME = trim($data[LEVEL_NAME]);
						$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
						
            if(($LEVEL_NAME !="" && $LEVEL_NAME !="-") || ($BKK_FLAG==1 && $LEVEL_NAME !="" && $LEVEL_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($LEVEL_NAME)?"".$LEVEL_NAME:"[����к��дѺ���˹�]");
						$arr_content[$data_count][parent] = "";
						$arr_content[$data_count][short_name] = $POSITION_LEVEL;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
						$arr_content[$data_count][count_7] = count_person(7, $search_condition, $addition_condition);
						$arr_content[$data_count][count_8] = count_person(8, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
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
						$arr_content[$data_count][parent] = "";
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
						$arr_content[$data_count][count_7] = count_person(7, $search_condition, $addition_condition);
						$arr_content[$data_count][count_8] = count_person(8, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
						$arr_content[$data_count][parent] = "";
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
						$arr_content[$data_count][count_7] = count_person(7, $search_condition, $addition_condition);
						$arr_content[$data_count][count_8] = count_person(8, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_age] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=$heading_column; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					} // end 	if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
				break;

			} // end switch case
		} // end for
		} // end if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1))
	} // end while
	
	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4;
	$GRAND_TOTAL += $GRAND_TOTAL_5 + $GRAND_TOTAL_6 + $GRAND_TOTAL_7 + $GRAND_TOTAL_8;

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	echo "GRAND_TOTAL = $GRAND_TOTAL";

if($export_type=="report"){
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
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
					$pdf->SetFont($fontb,'',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

					$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

					$pdf->MultiCell($heading_width[0], 7, $parent, $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0];
					$pdf->y = $start_y;
					for($i=1; $i<=$heading_column; $i++) $pdf->Cell($heading_width[1], 7, "", $border, 0, 'R', 0);
						$pdf->Cell($heading_width[2], 7, "", $border, 0, 'R', 0);

					//================= Draw Border Line ====================
					$line_start_y = $start_y;		$line_start_x = $start_x;
					$line_end_y = $max_y;		$line_end_x = $start_x;
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
					for($i=0; $i<=$heading_column+1; $i++){
						if($i==0){
							$line_start_y = $start_y;		$line_start_x += $heading_width[0];
							$line_end_y = $max_y;		$line_end_x += $heading_width[0];
						}elseif($i==$heading_column+1){
							$line_start_y = $start_y;		$line_start_x += $heading_width[2];
							$line_end_y = $max_y;		$line_end_x += $heading_width[2];
						}else{
							$line_start_y = $start_y;		$line_start_x += $heading_width[1];
							$line_end_y = $max_y;		$line_end_x += $heading_width[1];
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
				} // end if ($parent != $arr_content[$data_count][parent] && strlen($arr_content[$data_count][parent])...
			} // end if ($list_type_text == "�����ǹ�Ҫ���" && $REPORT_ORDER == "ORG")
			$NAME = $arr_content[$data_count][name];
			for($i=1; $i<=$heading_column; $i++){
				${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
				$COUNT_TOTAL += ${"COUNT_".$i};
			} // end for
			$SUM_AGE = $arr_content[$data_count][sum_age];
			$AVG_AGE = $SUM_AGE / $COUNT_TOTAL;
			$TOTAL_AGE += $SUM_AGE;
			
			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, ($f_tab?"     ":"").(($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			for($i=1; $i<=$heading_column; $i++) $pdf->Cell($heading_width[1], 7, (${"COUNT_".$i}?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(${"COUNT_".$i})):number_format(${"COUNT_".$i})):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($AVG_AGE?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($AVG_AGE,2)):number_format($AVG_AGE, 2)):"-"), $border, 0, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=$heading_column+1; $i++){
				if($i==0){
					$line_start_y = $start_y;		$line_start_x += $heading_width[0];
					$line_end_y = $max_y;		$line_end_x += $heading_width[0];
				}elseif($i==$heading_column+1){
					$line_start_y = $start_y;		$line_start_x += $heading_width[2];
					$line_end_y = $max_y;		$line_end_x += $heading_width[2];
				}else{
					$line_start_y = $start_y;		$line_start_x += $heading_width[1];
					$line_end_y = $max_y;		$line_end_x += $heading_width[1];
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
		
		$AVG_TOTAL = $TOTAL_AGE / $GRAND_TOTAL;
		
		$border = "LTBR";
		$pdf->SetFont($fontb,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->MultiCell($heading_width[0], 7, "���", $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0];
		$pdf->y = $start_y;
		for($i=1; $i<=$heading_column; $i++){
			$TOTAL_GRAND_TOTAL+=${"GRAND_TOTAL_".$i}; $pdf->Cell($heading_width[1], 7, (${"GRAND_TOTAL_".$i}?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(${"GRAND_TOTAL_".$i})):number_format(${"GRAND_TOTAL_".$i})):"-"), $border, 0, 'R', 0);
		}
		$pdf->Cell($heading_width[2], 7, ($TOTAL_GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($TOTAL_GRAND_TOTAL,2)):number_format($TOTAL_GRAND_TOTAL, 2)):"-"), $border, 0, 'R', 0);		//__$pdf->Cell($heading_width[2], 7, ($AVG_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($AVG_TOTAL,2)):number_format($AVG_TOTAL, 2)):"-"), $border, 0, 'R', 0);
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ����բ����� **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
		}else if($export_type=="graph"){//if($export_type=="report"){
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
	$arr_categories = array();
	for($i=0;$i<count($arr_content);$i++){
		if($arr_content[$i][type]==$arr_rpt_order[0]){
			$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
			for($j=3;$j<count($arr_content_key);$j++){
				$arr_series_caption_data[$j][] = $arr_content[$i][$arr_content_key[$j]];
				}//for($j=2;$j<count($arr_content_key);$j++){
			}//if($arr_content[$i][type]==$arr_rpt_order[0]){
		}//for($i=0;$i<count($arr_content);$i++){
//	echo "<pre>"; print_r($arr_series_caption_data); echo "</pre>";
	for($j=3;$j<(count($arr_content_key)-1);$j++){
		$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]);
		}
//	echo "<pre>"; print_r($arr_series_list); echo "</pre>";
	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
	if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
	$selectedFormat = "SWF";
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
//	echo($series_list);
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