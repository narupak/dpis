<?

	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	ini_set("max_execution_time", "1800");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "CO_LEVEL", "PROVINCE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_SEQ_NO, a.ORG_ID";
				elseif($select_org_structure==1) $select_list .= "e.ORG_SEQ_NO, d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_SEQ_NO, a.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "e.ORG_SEQ_NO, d.ORG_ID";

				$heading_name .= " �ӹѡ/�ͧ";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.ORG_ID_1";

				$heading_name .= " ����";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.ORG_ID_2";

				$heading_name .= " �ҹ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

				$heading_name .= " ��§ҹ";
				break;
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.CL_NAME, c.LEVEL_NO_MIN";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "c.LEVEL_NO_MIN";

				$heading_name .= " ��ǧ�дѺ���˹�";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.PV_CODE";
				elseif($select_org_structure==1) $select_list .= "e.PV_CODE";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.PV_CODE";
				elseif($select_org_structure==1) $order_by .= "e.PV_CODE";

				$heading_name .= " �ѧ��Ѵ";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if($select_org_structure==0) $order_by = "b.ORG_SEQ_NO, a.ORG_ID";
		elseif($select_org_structure==1) $order_by = "e.ORG_SEQ_NO, d.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if($select_org_structure==0) $select_list = "b.ORG_SEQ_NO, a.ORG_ID";
		elseif($select_org_structure==1) $select_list = "e.ORG_SEQ_NO, d.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = "�����ǹ�Ҫ���";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ������Ҥ
		$list_type_text = "��ǹ��ҧ������Ҥ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='02')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ҧ�����
		$list_type_text = "��ҧ�����";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='04')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		// �ӹѡ/�ͧ , ���� , �ҹ
		$list_type_text = "";
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(d.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}elseif($list_type == "PER_LINE"){
		// ��§ҹ
		$list_type_text = "";
		if(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		} // end if
	}elseif($list_type == "PER_COUNTRY"){
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}else{
		// �����ǹ�Ҫ���
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	function count_position($position_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;		
		global $select_org_structure, $DEPARTMENT_ID;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);

		// $position_type = 1 ==> ���˹� �����
		// $position_type = 2 ==> ���˹� �ԪҪվ੾��
		// $position_type = 3 ==> ���˹� ������
		if($select_org_structure == 0){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.POS_ID) as count_position
						   from			(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)																												
										) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
						   where		d.PT_GROUP = $position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
										$search_condition
					   	";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			count(a.POS_ID) as count_position
								 from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_TYPE d
								 where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.PT_CODE=d.PT_CODE
													and d.PT_GROUP=$position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
													$search_condition
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.POS_ID) as count_position
						   from			(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)																												
										) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
						   where		d.PT_GROUP = $position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
										$search_condition
					   	";
			} // end if
		}elseif($select_org_structure == 1){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.POS_ID) as count_position
								 from			(
														(
															(
																(
																	PER_POSITION a
																	inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
																) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
															) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
														) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
													) inner join PER_TYPE f on (a.PT_CODE=f.PT_CODE)
								 where		f.PT_GROUP = $position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
													$search_condition
							   ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			count(a.POS_ID) as count_position
								 from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d, PER_ORG_ASS e, PER_TYPE f
								 where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.ORG_ID=e.ORG_ID and a.PT_CODE=f.PT_CODE
													and f.PT_GROUP=$position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
													$search_condition
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.POS_ID) as count_position
								 from			(
														(
															(
																(
																	PER_POSITION a
																	inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
																) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
															) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
														) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
													) inner join PER_TYPE f on (a.PT_CODE=f.PT_CODE)
								 where		f.PT_GROUP = $position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
													$search_condition
							   ";
			} // end if
		} // end if

		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		//echo $cmd;
		
		$data = $db_dpis2->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$count_position = $data[count_position] + 0;
		return $count_position;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0)
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					elseif($select_org_structure==1)
						if($ORG_ID) $arr_addition_condition[] = "(d.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(d.ORG_ID = 0 or d.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($select_org_structure==0)
						if($ORG_ID_1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
				break;
				case "ORG_2" :
					if($select_org_structure==0)
						if($ORG_ID_2) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
				break;
				case "CO_LEVEL" :
					if($CL_NAME) $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME')";
					else $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME' or a.CL_NAME is null)";
				break;
				case "PROVINCE" :
					if($select_org_structure==0)
						if($PV_CODE) $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE')";
						else $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE' or b.PV_CODE is null)";
					elseif($select_org_structure==1)
						if($PV_CODE) $arr_addition_condition[] = "(trim(e.PV_CODE) = '$PV_CODE')";
						else $arr_addition_condition[] = "(trim(e.PV_CODE) = '$PV_CODE' or e.PV_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
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
				case "CO_LEVEL" :
					$CL_NAME = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($select_org_structure == 0){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												PER_POSITION a 
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by
					";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_ORG d, PER_ORG g
					   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												PER_POSITION a 
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by
					";
		}
	}elseif($select_org_structure == 1){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, f.ORG_SEQ_NO, f.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												(
													(
														PER_POSITION a 
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
										) inner join PER_ORG_ASS f on (e.ORG_ID_REF=f.ORG_ID)
									) inner join PER_ORG_ASS g on (f.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, f.ORG_SEQ_NO, f.ORG_ID, $order_by
					";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, f.ORG_SEQ_NO, f.ORG_ID as DEPARTMENT_ID, $select_list
					   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d, PER_ORG_ASS e, PER_ORG_ASS f, PER_ORG_ASS g
					   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.ORG_ID=e.ORG_ID 
					   				and e.ORG_ID_REF=f.ORG_ID and f.ORG_ID_REF=g.ORG_ID
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, f.ORG_SEQ_NO, f.ORG_ID, $order_by
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, f.ORG_SEQ_NO, f.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												(
													(
														PER_POSITION a 
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
										) inner join PER_ORG_ASS f on (e.ORG_ID_REF=f.ORG_ID)
									) inner join PER_ORG_ASS g on (f.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, f.ORG_SEQ_NO, f.ORG_ID, $order_by
					";
		}
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	initialize_parameter(0);
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){

		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){	
			if($data_count > 1){
				$GRAND_TOTAL_1[$DEPARTMENT_ID] = count_position(1, $search_condition, "");
				$GRAND_TOTAL_2[$DEPARTMENT_ID] = count_position(2, $search_condition, "");
				$GRAND_TOTAL_3[$DEPARTMENT_ID] = count_position(3, $search_condition, "");
				
				$GRAND_TOTAL[$DEPARTMENT_ID] = $GRAND_TOTAL_1[$DEPARTMENT_ID] + $GRAND_TOTAL_2[$DEPARTMENT_ID] + $GRAND_TOTAL_3[$DEPARTMENT_ID];
			} // end if
			
			$MINISTRY_ID = $data[MINISTRY_ID];
			if($select_org_structure==0)
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];

			$DEPARTMENT_ID = $data[DEPARTMENT_ID];			
			if($select_org_structure==0)
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data2[ORG_NAME];

			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][id] = $DEPARTMENT_ID;
			$arr_content[$data_count][ref_name] = $MINISTRY_NAME;
			$arr_content[$data_count][name] = $DEPARTMENT_NAME;
			
			$data_count++;
		} // end if
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[����к��ӹѡ/�ͧ]";
						}else{
							if($select_org_structure==0)
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							elseif($select_org_structure==1)
								$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ORG_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
							$ORG_NAME_1 = "[����кؽ���]";
						}else{
							if($select_org_structure==0)
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
							$ORG_NAME_2 = "[����кاҹ]";
						}else{
							if($select_org_structure==0)
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
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
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "CO_LEVEL" :
					if($CL_NAME != trim($data[CL_NAME])){
						$CL_NAME = trim($data[CL_NAME]);

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "CO_LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($CL_NAME)?"�дѺ $CL_NAME":"[����кت�ǧ�дѺ���˹�]");
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
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
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
	$GRAND_TOTAL_1[$DEPARTMENT_ID] = count_position(1, $search_condition, "");
	$GRAND_TOTAL_2[$DEPARTMENT_ID] = count_position(2, $search_condition, "");
	$GRAND_TOTAL_3[$DEPARTMENT_ID] = count_position(3, $search_condition, "");
	
	$GRAND_TOTAL[$DEPARTMENT_ID] = $GRAND_TOTAL_1[$DEPARTMENT_ID] + $GRAND_TOTAL_2[$DEPARTMENT_ID] + $GRAND_TOTAL_3[$DEPARTMENT_ID];

	//echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	header('content-type:text/xml');
	$data = '<data>';

		// Open series node 

			//for($data_count=1; $data_count<10; $data_count++){
			//for($data_count=1; $data_count<count($arr_content); $data_count++){
				$data .= '<series name="��ͤ�ͧ">';
				$data .= '	<point name="'.$arr_content[1][name].'" y="'.$arr_content[1][count_1].'" />';
				$data .= '	<point name="'.$arr_content[2][name].'" y="'.$arr_content[2][count_1].'" />';
				$data .= '	<point name="'.$arr_content[3][name].'" y="'.$arr_content[3][count_1].'" />';
				$data .= '	<point name="'.$arr_content[4][name].'" y="'.$arr_content[4][count_1].'" />';
				$data .= '	<point name="'.$arr_content[5][name].'" y="'.$arr_content[5][count_1].'" />';
				$data .= '	<point name="'.$arr_content[6][name].'" y="'.$arr_content[6][count_1].'" />';
				$data .= '	<point name="'.$arr_content[7][name].'" y="'.$arr_content[7][count_1].'" />';
				$data .= '	<point name="'.$arr_content[8][name].'" y="'.$arr_content[8][count_1].'" />';
				$data .= '	<point name="'.$arr_content[9][name].'" y="'.$arr_content[9][count_1].'" />';
				$data .= '</series>';
				
				$data .= '<series name="��ҧ">';
				$data .= '	<point name="'.$arr_content[1][name].'" y="'.$arr_content[1][count_2].'" />';
				$data .= '	<point name="'.$arr_content[2][name].'" y="'.$arr_content[2][count_2].'" />';
				$data .= '	<point name="'.$arr_content[3][name].'" y="'.$arr_content[3][count_2].'" />';
				$data .= '	<point name="'.$arr_content[4][name].'" y="'.$arr_content[4][count_2].'" />';
				$data .= '	<point name="'.$arr_content[5][name].'" y="'.$arr_content[5][count_2].'" />';
				$data .= '	<point name="'.$arr_content[6][name].'" y="'.$arr_content[6][count_2].'" />';
				$data .= '	<point name="'.$arr_content[7][name].'" y="'.$arr_content[7][count_2].'" />';
				$data .= '	<point name="'.$arr_content[8][name].'" y="'.$arr_content[8][count_2].'" />';
				$data .= '	<point name="'.$arr_content[9][name].'" y="'.$arr_content[9][count_2].'" />';
				$data .= '</series>';
				
				$data .= '<series name="��ҧ���">';
				$data .= '	<point name="'.$arr_content[1][name].'" y="'.$arr_content[1][count_3].'" />';
				$data .= '	<point name="'.$arr_content[2][name].'" y="'.$arr_content[2][count_3].'" />';
				$data .= '	<point name="'.$arr_content[3][name].'" y="'.$arr_content[3][count_3].'" />';
				$data .= '	<point name="'.$arr_content[4][name].'" y="'.$arr_content[4][count_3].'" />';
				$data .= '	<point name="'.$arr_content[5][name].'" y="'.$arr_content[5][count_3].'" />';
				$data .= '	<point name="'.$arr_content[6][name].'" y="'.$arr_content[6][count_3].'" />';
				$data .= '	<point name="'.$arr_content[7][name].'" y="'.$arr_content[7][count_3].'" />';
				$data .= '	<point name="'.$arr_content[8][name].'" y="'.$arr_content[8][count_3].'" />';
				$data .= '	<point name="'.$arr_content[9][name].'" y="'.$arr_content[9][count_3].'" />';
				$data .= '</series>';
			//}
		// Close series node

	// Close data node
	$data .= '</data>';

?>
<anychart>
	<settings>
		<animation enabled="True"/>
	</settings>
	<charts>
		<chart plot_type="CategorizedVertical">
			<chart_settings>
				<title enabled="true">
					<text>Multi-Series: Logarithmic Y-Axis</text>
				</title>
				<axes>
					<x_axis>
						<title enabled="true">
							<text>Arguments</text>
						</title>
					</x_axis>
					<y_axis>
                    <scale minimum="1" type="Logarithmic" log_base="5" /> 
						<title enabled="true">
							<text>Values:  {%AxisMin} - {%AxisMax}</text>
						</title>
						<labels>
							<format>{%Value}{numDecimals:0}</format>
						</labels>
					</y_axis>
				</axes>
			</chart_settings>
			<data_plot_settings default_series_type="Bar" enable_3d_mode="True" z_aspect="2">
				<bar_series point_padding="0.2" group_padding="1">
					<label_settings enabled="True"  rotation="90">
					   <position  anchor="Center" halign="Center" valign="Center"/>
					   <format>{%Value}{numDecimals:0}</format>
					   <font bold="False" color="White">
					   		<effects>
								<drop_shadow enabled="True" opacity="0.5" distance="2" blur_x="1" blur_y="1"/>
							</effects>
					   </font>
					   <background enabled="False"/>
					</label_settings>
					<tooltip_settings enabled="True">
						<format>Value: {%YValue}{numDecimals:2}
Argument: {%Name}</format>
						<background>
							<border color="DarkColor(%Color)"/>
							<font>
								<effects enabled="true">
									<drop_shadow enabled="true" color="Black" opacity="1"/>
								</effects>
							</font>
						</background>
						<font color="DarkColor(%Color)"/>
					</tooltip_settings>
					<bar_style>
						<states>
							<normal>
								<border color="DarkColor(%Color)" thickness="1"/>
							</normal>
							<hover>
								<border thickness="2"/>
							</hover>
						</states>
					</bar_style>
				</bar_series>
			</data_plot_settings>
			<?php
		
			// Output data section

			echo $data; 

			?>
		</chart>
	</charts>
</anychart>