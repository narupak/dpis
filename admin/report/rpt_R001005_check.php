 <?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_select = "a.POS_ID";
		$position_join = "a.POS_ID=b.POS_ID"; 
		$position_no= "a.POS_NO";
		$line_table = "PER_LINE";
		$line_join = "a.PL_CODE=e.PL_CODE";
		$line_code = "a.PL_CODE";
		$line_name = "PL_NAME as PL_NAME, PL_SHORTNAME as PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title= " ��§ҹ";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_select = "a.POEM_ID";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$position_no= "a.POEM_NO";
		$line_table = "PER_POS_NAME";
		$line_join = "a.PN_CODE=e.PN_CODE";
		$line_code = "a.PN_CODE";
		$line_name = "PN_NAME as PL_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title= " ���͵��˹�";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_select = "a.POEMS_ID";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$position_no= "a.POEMS_NO";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "a.EP_CODE=e.EP_CODE";
		$line_code = "a.EP_CODE";
		$line_name = "EP_NAME as PL_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title= " ���͵��˹�";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_select = "a.POT_ID";
		$position_join = "a.POT_ID=b.POT_ID";
		$position_no= "a.POT_NO";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "a.TP_CODE=e.TP_CODE";
		$line_code = "a.TP_CODE";
		$line_name = "TP_NAME as PL_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);
		$line_title= " ���͵��˹�";
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
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "CO_LEVEL", "PROVINCE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_ID as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "g.ORG_ID";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_ID as DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_SEQ_NO, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_SEQ_NO, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= $line_code;

				$heading_name .= " $line_title";
				break;
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.CL_NAME, c.LEVEL_NO_MIN";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "c.LEVEL_NO_MIN";

				$heading_name .= " $CL_TITLE";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.PV_CODE";

				$heading_name .= " $PV_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		$order_by = "b.ORG_SEQ_NO, a.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		$select_list = "b.ORG_SEQ_NO, a.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		$arr_search_condition[] = "(trim(b.OT_CODE)='01')";
	}
if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ������Ҥ
		$list_type_text = "��ǹ��ҧ������Ҥ";
		$arr_search_condition[] = "(trim(b.OT_CODE)='02')";
	}
if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		$arr_search_condition[] = "(trim(b.OT_CODE)='03')";
	}
if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ҧ�����
		$list_type_text = "��ҧ�����";
		$arr_search_condition[] = "(trim(b.OT_CODE)='04')";
	}
if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			} // end if
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			} // end if
	}
if(in_array("PER_LINE", $list_type)){
		// ��§ҹ
		$list_type_text = "";
		if(trim($line_search_code)){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= "$line_search_name";
		} // end if
	}
if(in_array("PER_COUNTRY", $list_type)){
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			 $arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//�óշ����� ������������͡ check box list_type ���
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "�ӹǹ���˹�$PERSON_TYPE[$search_per_type] ��ṡ����дѺ���˹�";
	$report_code = "R0105";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetFont($font,'',14);
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	include ("rpt_R001005_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

	function count_position($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $position_table, $position_select;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);

		if($DPISDB=="odbc"){
			$cmd = " select	 	count($position_select ) as count_position
							from		(
												(
													$position_table a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
							where	trim(a.LEVEL_NO)='$level_no' 
									$search_condition ";
		}elseif($DPISDB=="oci8"){		
			$cmd = " select		count($position_select ) as count_position
							 from			$position_table a, PER_ORG b, PER_TYPE c, PER_ORG d, PER_ORG g
							 where		a.ORG_ID=b.ORG_ID and a.PT_CODE=c.PT_CODE
												and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID
												and trim(a.LEVEL_NO)='$level_no'
												$search_condition ";			
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count($position_select ) as count_position
							from		(
												(	
													$position_table a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
							where	trim(a.LEVEL_NO)='$level_no') 
									$search_condition ";
		} // end if
		$db_dpis2->send_cmd($cmd);
	//echo "<br>$cmd<br>";
	//$db_dpis2->show_error();
		$data = $db_dpis2->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$count_position = $data[count_position] + 0;

		return $count_position;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE, $line_code;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID) $arr_addition_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
					else $arr_addition_condition[] = "(d.ORG_ID_REF = 0 or d.ORG_ID_REF is null)";
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
					else $arr_addition_condition[] = "(a.DEPARTMENT_ID = 0 or a.DEPARTMENT_ID is null)";
				break;
				case "ORG" :	
					if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
					else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($ORG_ID_1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
					else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
				break;
				case "ORG_2" :
					if($ORG_ID_2) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
					else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
				break;
				case "CO_LEVEL" :
					if($CL_NAME) $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME')";
					else $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME' or a.CL_NAME is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE' or b.PV_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
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
				case "CO_LEVEL" :
					$CL_NAME = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//�ʴ���ª���˹��§ҹ�Ҫ���
	if($DPISDB=="odbc"){
		$cmd = " select			distinct g.ORG_SEQ_NO, d.ORG_SEQ_NO, $select_list
				   from			(
										(
											$position_table a 
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
									) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
								) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
								$search_condition
				   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct g.ORG_SEQ_NO, d.ORG_SEQ_NO, $select_list
				   from			$position_table a, PER_ORG b, PER_ORG d, PER_ORG g
				   where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID
								$search_condition
				   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct g.ORG_SEQ_NO, d.ORG_SEQ_NO, $select_list
				   from			(
										(
											$position_table a 
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
									) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
								) inner join PER_ORG g on d.ORG_ID_REF=g.ORG_ID)
								$search_condition
				   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by ";
	}

	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	unset($LEVEL_TOTAL);
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	$first_order = 1;		// order ��  0 = COUNTRY �ѧ�����ӹǳ ����� order ��� 1 (MINISTRY) ��͹
	$rpt_order_start_index = 0;
	$MINISTRY_ID = -1;			$DEPARTMENT_ID_INI = -1;
	while($data = $db_dpis->get_array()){
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		
		if (trim($data[MINISTRY_ID]) && trim($data[DEPARTMENT_ID])) {			//��������
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
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

						if ($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1) {
							$arr_content[$data_count][type] = "COUNTRY";
							$rpt_order_start_index = 0;
							$addition_condition = "";
						} else {
							$arr_content[$data_count][type] = "MINISTRY";
							$rpt_order_start_index = 1;
							$addition_condition = generate_condition(1);
						}
						if ($f_all) {   //��������		//��� rpt_R001001
							$rpt_order_index = $rpt_order_start_index;
							//$arr_content[$data_count][type] = "MINISTRY";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $MINISTRY_NAME;
							for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
								$tmp_level_no = $ARR_LEVEL_NO[$i];
								$arr_content[$data_count]["level_".$tmp_level_no] = count_position($tmp_level_no, $search_condition, $addition_condition);
								if($rpt_order_index == $first_order) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
							} // end for
	
							$data_count++;
						} // end if
					} // end if
				break;
				case "DEPARTMENT" :
				if($DEPARTMENT_ID && $DEPARTMENT_ID_INI != $DEPARTMENT_ID){
					if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
						$DEPARTMENT_ID_INI = trim($DEPARTMENT_ID);
						if($DEPARTMENT_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$rpt_order_index = 2;
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $DEPARTMENT_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["level_".$tmp_level_no] = count_position($tmp_level_no, $search_condition, $addition_condition);
							if(($rpt_order_index - $first_order) == $first_order) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for

						$data_count++;
					}
				} // end if
				break;
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["level_".$tmp_level_no] = count_position($tmp_level_no, $search_condition, $addition_condition);
							if($rpt_order_index == $first_order) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						if($ORG_ID_1 != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_NAME_1;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["level_".$tmp_level_no] = count_position($tmp_level_no, $search_condition, $addition_condition);
							if($rpt_order_index == $first_order) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != trim($data[ORG_ID_2])){
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						if($ORG_ID_2 != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $ORG_NAME_2;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["level_".$tmp_level_no] = count_position($tmp_level_no, $search_condition, $addition_condition);
							if($rpt_order_index == $first_order) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							$cmd = " select $line_name from $line_table b where trim($line_code)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
					//		$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if
					
						$addition_condition = generate_condition($rpt_order_index);
					
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $PL_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["level_".$tmp_level_no] = count_position($tmp_level_no, $search_condition, $addition_condition);
							if($rpt_order_index == $first_order) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for
					
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "CO_LEVEL" :
					if($CL_NAME != trim($data[CL_NAME])){
						$CL_NAME = trim($data[CL_NAME]);

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "CO_LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . (trim($CL_NAME)?"�дѺ $CL_NAME":"[����кت�ǧ�дѺ���˹�]");
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_position(4, $search_condition, $addition_condition);
						
						if($rpt_order_index == 1){
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
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

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "PROVINCE";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $PV_NAME;
						for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
							$tmp_level_no = $ARR_LEVEL_NO[$i];
							$arr_content[$data_count]["level_".$tmp_level_no] = count_position($tmp_level_no, $search_condition, $addition_condition);
							if($rpt_order_index == $first_order) $LEVEL_TOTAL[$tmp_level_no] += $arr_content[$data_count]["level_".$tmp_level_no];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

			} // end switch case
		} // end for
		} // end if
		} // end if
	} // end while
	
	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($export_type=="report"){
		$pdf->AutoPageBreak = false;
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		if (!$result) echo "****** error ****** on open table for $table<br>";
			
		if($count_data){
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$NAME = $arr_content[$data_count][name];
				unset($COUNT_LEVEL);
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$COUNT_LEVEL[$tmp_level_no] = $arr_content[$data_count]["level_".$tmp_level_no];
				} // end for
				$COUNT_TOTAL = array_sum($COUNT_LEVEL);
				
				if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

				$arr_data = (array) null;
				$arr_data[] = $NAME;
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$arr_data[] = $COUNT_LEVEL[$tmp_level_no];
				} // end for
				$arr_data[] = 0;	// sum level_no
				$arr_data[] = 0;	// percent level_no

				$data_align = array("L","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R");

				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}else{
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				} // end if
			} // end for
					
			$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

			$arr_data = (array) null;
			$arr_data[] = "���";
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				$arr_data[] = $LEVEL_TOTAL[$tmp_level_no];
			} // end for
			$arr_data[] = 0;	// sum level_no
			$arr_data[] = 0;	// percent level_no

			$data_align = array("L","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R");

			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "FF0000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{
			$result = $pdf->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
		} // end if
		$pdf->close_tab(""); 

		$pdf->close();
		$pdf->Output();		
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
		//echo "<pre>"; print_r($arr_content); echo "</pre>";
		$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
		$arr_categories = array();
		$arr_series_caption_list = array(); 
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
		for($i=1;$i<count($ARR_LEVEL_NO);$i++){
			$arr_series_list[$i] = implode(";", $arr_series_caption_data[$i])."";
			$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
		}

		$chart_title = trim(str_replace("|"," ",$report_title));
		$chart_subtitle = $company_name;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
		$categories_list = implode(";", $arr_categories)."";
		if(strtolower($graph_type)=="pie"){
			$series_list = $GRAND_TOTAL_1[$DEPARTMENT_ID].";".$GRAND_TOTAL_2[$DEPARTMENT_ID].";".$GRAND_TOTAL_3[$DEPARTMENT_ID].";".$GRAND_TOTAL_4[$DEPARTMENT_ID];
		}else{
			$series_list = implode("|", $arr_series_list);
		}
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