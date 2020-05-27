<?
	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	unset($ARR_LEVEL_NO);
	unset($ARR_POSITION_TYPE);
	//$cmd = "select LEVEL_NO, LEVEL_NAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO ,LEVEL_NO";
	$cmd = "select LEVEL_NO, LEVEL_NAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (LEVEL_NAME LIKE '%$search_pt_name%') and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO ,LEVEL_NO";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) {
		$arr_temp = explode(" ", trim($data[LEVEL_NAME]));
		//�Ҫ��͵��˹觻�����
		if($search_per_type==1){
			$POSITION_TYPE = str_replace("������", "", $arr_temp[0]);
		}elseif($search_per_type==2){
			$POSITION_TYPE = $arr_temp[0];
		}elseif($search_per_type==3){
			$POSITION_TYPE = str_replace("������ҹ", "", $arr_temp[0]);
		}
		//�Ҫ����дѺ���˹� 
		$arr_temp[1]=str_replace("�дѺ", "", $arr_temp[1]);
		$LEVEL_NAME =  trim($arr_temp[1]);
		$ARR_POSITION_TYPE[$POSITION_TYPE][] = $LEVEL_NAME;
	
		$ARR_LEVEL_NO[$LEVEL_NAME] = trim($data[LEVEL_NO]);
	}
//--------------------------------------------------------------------------------------------------------
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	//print_r($arr_rpt_order);
	//�¡������͹䢷�����͡
	$select_list = "";		$order_by = "";		$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID_REF";

//				$heading_name .= " ��ǹ�Ҫ���";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.DEPARTMENT_ID";

//				$heading_name .= " ��ǹ�Ҫ���";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID";

//				$heading_name .= " ��ǹ�Ҫ���";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

//				$heading_name .= " ��§ҹ";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID) $order_by = "d.ORG_ID_REF";
		elseif(!$DEPARTMENT_ID) $order_by = "a.DEPARTMENT_ID";
		else $order_by = "a.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID) $select_list = "d.ORG_ID_REF as MINISTRY_ID";
		elseif(!$DEPARTMENT_ID) $select_list = "a.DEPARTMENT_ID";
		else $select_list = "a.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = "�����ǹ�Ҫ���";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='1')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='1'";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='2')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='2')";
	//}elseif($list_type == "PER_LINE"){
	}elseif($list_type == "PER_TYPE"){
		$list_type_text = "";
		// ���˹觻����� ��е��˹����§ҹ
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " $PROVINCE_NAME";
		} // end if
		
		if($search_pt_name){ $list_type_text .=" ���˹觻����� : $search_pt_name"; }
		if($search_per_type==1 && trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= " / ���˹����§ҹ : $search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
			$list_type_text .= " / ���˹����§ҹ : $search_pn_name";
		}elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
			$list_type_text .= " / ���˹����§ҹ : $search_ep_name";
		} // end if
	}elseif($list_type == "PER_COUNTRY"){
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(a.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}else{
		// �����ǹ�Ҫ���
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(d.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";

	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "�ӹǹ����Ҫ��þ����͹ �¡����дѺ���˹�";
	$report_title .= " ��Шӻէ�����ҳ  �.�. $search_year";
	$report_code = "R1009";
	$orientation='L';
	
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	$count_position_type=count($ARR_POSITION_TYPE[$search_pt_name]);
	//�Ҩӹǹ����Ҫ��÷����� other case
	function count_person($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type;
		global $select_org_structure;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);

		//�Ҫ�����ǹ�Ҫ��� 
		if($search_per_type==1){
			// ����Ҫ���
			$pos_tb="PER_POSITION";
			$join_tb="a.POS_ID=b.POS_ID";
		}elseif($search_per_type==2){	
			// �١��ҧ
			$pos_tb="PER_POS_EMP";
			$join_tb="a.POEM_ID=b.POEM_ID";
		}elseif($search_per_type==3){ 
			//��ѡ�ҹ�Ҫ���
			$pos_tb="PER_POS_EMPSER";
			$join_tb="a.POEMS_ID=b.POEMS_ID";
		}
		if($DPISDB=="odbc"){
			$cmd = " select			count(b.PER_ID) as count_person
							from				(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													)	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
								 where			(b.LEVEL_NO='$level_no') 
												$search_condition
							 group by		b.PER_ID
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select		count(b.PER_ID) as count_person
							 from			$pos_tb a, PER_PERSONAL b,PER_ORG c, PER_ORG d
							 where		$join_tb(+) and	a.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=d.ORG_ID(+) 
												and (b.LEVEL_NO='$level_no') 
												$search_condition
							 group by		b.PER_ID
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(b.PER_ID) as count_person
							from				(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													)	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
								 where			(b.LEVEL_NO='$level_no') 
												$search_condition
							 group by		b.PER_ID
						   ";
		}

		if($select_org_structure==1){
			 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			 $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
		//echo "<br>X2::: $cmd<br>";
		//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if
return $count_person;
} // function

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
				
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
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
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
				case "LINE" :
					$PL_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	//�Ҫ�����ǹ�Ҫ��� 
	if($search_per_type==1){
		// ����Ҫ���
		$pos_tb="PER_POSITION";
		$join_tb="a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){	
		// �١��ҧ
		$pos_tb="PER_POS_EMP";
		$join_tb="a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		//��ѡ�ҹ�Ҫ���
		$pos_tb="PER_POS_EMPSER";
		$join_tb="a.POEMS_ID=b.POEMS_ID";
	}
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
							 from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													) 	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
												$search_condition
							 order by		$order_by
						   ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
							 from			$pos_tb a,PER_PERSONAL  b, PER_ORG c, PER_ORG  d
							 where		$join_tb(+) and a.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=d.ORG_ID(+)
												$search_condition
							 order by		$order_by
						   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
							 from			(
													(
														$pos_tb a
														left join PER_PERSONAL b on ($join_tb)
													) 	left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												) left join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
												$search_condition
							 order by		$order_by
						   ";
	}
	if($select_org_structure==1){
		 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		 $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
 // echo "<br>X1::: $cmd<br>";
//	$db_dpis->show_error();

	$data_count = 0;
	for($i=0;$i<$count_position_type; $i++){
		  $tmp_position_type = $ARR_POSITION_TYPE[$search_pt_name][$i];
		  $LEVEL_NO = $ARR_LEVEL_NO[$tmp_position_type];
    	 $LEVEL_GRAND_TOTAL[$LEVEL_NO] = 0;
	}
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
	
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[����кء�з�ǧ]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

							for($i=0;$i<$count_position_type; $i++){
								$tmp_position_type = $ARR_POSITION_TYPE[$search_pt_name][$i];
								$LEVEL_NO = $ARR_LEVEL_NO[$tmp_position_type];
	
								$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person($LEVEL_NO,$search_condition, $addition_condition);
								$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
									
								if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
							}

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[����кء��]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						for($i=0;$i<$count_position_type; $i++){
							$tmp_position_type = $ARR_POSITION_TYPE[$search_pt_name][$i];
							$LEVEL_NO = $ARR_LEVEL_NO[$tmp_position_type];
							
							$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person($LEVEL_NO,$search_condition, $addition_condition);
							$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
							
							if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
						}

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[����к��ӹѡ/�ͧ]";
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

						for($i=0;$i<$count_position_type; $i++){
							$tmp_position_type = $ARR_POSITION_TYPE[$search_pt_name][$i];
							$LEVEL_NO = $ARR_LEVEL_NO[$tmp_position_type];
							
							$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person($LEVEL_NO,$search_condition, $addition_condition);
							$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
							
							if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
						}

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
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;

						for($i=0;$i<$count_position_type; $i++){
							$tmp_position_type = $ARR_POSITION_TYPE[$search_pt_name][$i];
							$LEVEL_NO = $ARR_LEVEL_NO[$tmp_position_type];
							
							$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person($LEVEL_NO,$search_condition, $addition_condition);
							$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
							
							if($rpt_order_index == 0) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
						}

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	$GRAND_TOTAL = array_sum($LEVEL_GRAND_TOTAL);
	//print_r($arr_content);echo("<BR>");
	//print_r($ARR_LEVEL_NO);echo("<BR>");
	//print_r($ARR_POSITION_TYPE[$search_pt_name]);echo("<BR>");
	for($i=0;$i<count($ARR_POSITION_TYPE[$search_pt_name]);$i++){
		echo($ARR_POSITION_TYPE[$search_pt_name][$i]);echo("<BR>");
		}
	$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
	$arr_categories = array();
	for($i=0;$i<count($arr_content);$i++){
		$arr_categories[$i] = $arr_content[$i][name];
		for($j=0;$j<count($arr_content);$j++){
			$arr_series_caption_data[$i][] = $arr_content[$i]["count_".];
			}
		}
	$arr_series_list[2] = implode(";", $arr_series_caption_data[2]).";$GRAND_TOTAL_S";
//	print_r($LEVEL_GRAND_TOTAL_F);echo("<BR>");

	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	if(!$setWidth) $setWidth = "800";
	if(!$setHeight) $setHeight = "600";
	$selectedFormat = "SWF";
	$series_caption_list = implode(";", $ARR_LEVEL_NO);
	$categories_list = implode(";", $arr_categories).";���������";
	if(strtolower($graph_type)=="pie"){
		$series_list = $GRAND_TOTAL_M.";".$GRAND_TOTAL_F.";".$GRAND_TOTAL_S;
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
		} // end switch case
?>