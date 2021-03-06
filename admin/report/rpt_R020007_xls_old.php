<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID";

				$heading_name .= " �ӹѡ/�ͧ";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_1";

				$heading_name .= " ����";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_2";

				$heading_name .= " �ҹ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				if($search_per_type==1) $select_list .= "b.PL_CODE";
				elseif($search_per_type==2) $select_list .= "b.PN_CODE";
				elseif($search_per_type==3) $select_list .= "b.EP_CODE";

				if($order_by) $order_by .= ", ";
				if($search_per_type==1) $order_by .= "b.PL_CODE";
				elseif($search_per_type==2) $order_by .= "b.PN_CODE";
				elseif($search_per_type==3) $order_by .= "b.EP_CODE";

				if($search_per_type==1) $heading_name .= " ��§ҹ";
				elseif($search_per_type==2) $heading_name .= " ���͵��˹�";
				elseif($search_per_type==3) $heading_name .= " ���͵��˹�";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "b.ORG_ID";
	if(!trim($select_list)) $select_list = "b.ORG_ID";

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
//	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(e.MOV_CODE) in ('123', '12310'))";		//������ª��͢���Ҫ��÷���ջ������������͹��Ǥ�� ���

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";

	$list_type_text = "�����ǹ�Ҫ���";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG"){
		// �ӹѡ/�ͧ , ���� , �ҹ
		$list_type_text = "";
		if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(e.ORG_ID_3 = $search_org_id)";
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
	}elseif($list_type == "PER_LINE"){
		// ��§ҹ
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(e.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(e.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		} // end if
		elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(e.EP_CODE)='$search_ep_code')";
			$list_type_text .= "$search_ep_name";
		} // end if
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
		// �����ǹ�Ҫ���
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	if($search_per_type==1) $type_name="����Ҫ���";
	elseif($search_per_type==2) $type_name="�١��ҧ��Ш�";
	elseif($search_per_type==3) $type_name="��ѡ�ҹ�Ҫ���";
	
	$report_title = "Ẻ���Ǩ$type_name ������ª��Ե㹻է�����ҳ $search_budget_year || ��ǹ�Ҫ��� $DEPARTMENT_NAME";
	$report_code = "R20007";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = &new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 5);
		$worksheet->set_column(1, 1, 8);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 40);
		$worksheet->set_column(4, 4, 12);
		$worksheet->set_column(5, 5, 20);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 12);
		$worksheet->set_column(8, 8, 12);
		$worksheet->set_column(9, 9, 12);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "�ӴѺ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$worksheet->write($xlsRow, 1, "�ӹ�", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$worksheet->write($xlsRow, 2, "����-ʡ��", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$worksheet->write($xlsRow, 3, "���͵��˹����§ҹ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$worksheet->write($xlsRow, 4, "�дѺ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$worksheet->write($xlsRow, 5, "�ѧ��Ѵ���", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$worksheet->write($xlsRow, 6, "�ѹ/��͹/��", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$worksheet->write($xlsRow, 7, "�ѹ/��͹/��", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$worksheet->write($xlsRow, 8, "�ѹ/��͹/��", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$worksheet->write($xlsRow, 9, "���˵�", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "���", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 1, "˹�ҹ��", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 5, "��軯Ժѵԧҹ", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 6, "�Դ", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 7, "����è�", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 8, "������ª��Ե", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		$worksheet->write($xlsRow, 9, "������ª��Ե", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
	} // function		

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3,$RPT_N;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $month_abbr;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($search_per_type==1){
			// ����Ҫ���
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.PT_CODE, e.MOV_CODE
								 from			(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
														) left join PER_LINE f on (e.PL_CODE=f.PL_CODE)
													) left join PER_TYPE g on (e.PT_CODE=g.PT_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, e.PT_CODE, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, 
													SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,
													MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, e.PT_CODE, e.MOV_CODE
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_LINE f, PER_TYPE g
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and a.PER_ID=e.PER_ID(+) and e.PL_CODE=f.PL_CODE(+) and e.PT_CODE=g.PT_CODE(+)
													$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, e.PT_CODE, 
								 					SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10), e.MOV_CODE
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.PT_CODE, e.MOV_CODE
								 from			(
														(
															(
																(
																	(
																		PER_PERSONAL a 
																		left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
														) left join PER_LINE f on (e.PL_CODE=f.PL_CODE)
													) left join PER_TYPE g on (e.PT_CODE=g.PT_CODE)
								$search_condition
								group by	 	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PL_NAME, e.LEVEL_NO, e.PT_CODE, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			} // end if
		}elseif($search_per_type==2){
			// �١��ҧ
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME as PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_POS_NAME f on (e.PN_CODE=f.PN_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME, e.LEVEL_NO, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME as PL_NAME, e.LEVEL_NO, 
													SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,
													MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_POS_NAME f
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and a.PER_ID=e.PER_ID(+) and e.PN_CODE=f.PN_CODE(+)
													$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME, e.LEVEL_NO, 
								 					SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10), e.MOV_CODE 
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME as PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_POS_NAME f on (e.PN_CODE=f.PN_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.PN_NAME, e.LEVEL_NO, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			} // end if
		} // end if
		elseif($search_per_type==3){
			// ��ѡ�ҹ�Ҫ���
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME as PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_EMPSER_POS_NAME f on (e.EP_CODE=f.EP_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME, e.LEVEL_NO, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME as PL_NAME, e.LEVEL_NO, 
													SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,
													MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_EMPSER_POS_NAME f
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PN_CODE=d.PN_CODE(+) and a.PER_ID=e.PER_ID(+) and e.EP_CODE=f.EP_CODE(+)
													$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME, e.LEVEL_NO, 
								 					SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10), e.MOV_CODE 
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME as PL_NAME, e.LEVEL_NO, 
													LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE),10) as PER_STARTDATE,
													MIN(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, e.MOV_CODE
								 from			(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_EMPSER_POS_NAME f on (e.EP_CODE=f.EP_CODE)
								$search_condition
								 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, f.EP_NAME, e.LEVEL_NO, 
								 					LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE),10), e.MOV_CODE
							   ";
			} // end if
		} // end if

		$db_dpis2->send_cmd($cmd);
//		echo "<br>$cmd<br>";
//		$db_dpis2->show_error();
		while($data = $db_dpis2->get_array()){
			$person_count++;
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PL_NAME = trim($data[PL_NAME]);
			
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' order by LEVEL_SEQ_NO";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$arr_temp = explode(" ", trim($data2[LEVEL_NAME]));
			//�Ҫ��͵��˹觻�����
			if($search_per_type==1){
				$POSITION_TYPE = str_replace("������", "", $arr_temp[0]);
			}elseif($search_per_type==2){
				$POSITION_TYPE = $arr_temp[0];
			}elseif($search_per_type==3){
				$POSITION_TYPE = str_replace("������ҹ", "", $arr_temp[0]);
			}
			//�Ҫ����дѺ���˹� 
			$LEVEL_NAME =  trim(str_replace("�дѺ", "",$arr_temp[1]));
			
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", $PER_BIRTHDATE);
				$PER_BIRTHDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			}
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			if($PER_STARTDATE){
				$arr_temp = explode("-", $PER_STARTDATE);
				$PER_STARTDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			}
			$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
			if($POH_EFFECTIVEDATE){
				$arr_temp = explode("-", $POH_EFFECTIVEDATE);
				$POH_EFFECTIVEDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if			

			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data2 = $db_dpis3->get_array();
			$PT_NAME = $data2[PT_NAME];
			
			$MOV_CODE = trim($data[MOV_CODE]);
			$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data2 = $db_dpis3->get_array();
			$MOV_NAME = $data2[MOV_NAME];

			/***
			//���زԡ���֡���٧�ش
			$cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%||4||%'
							 order by a.EDU_SEQ desc,a.EN_CODE
						  ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
//			echo "<br>$cmd<br>";
			$data2 = $db_dpis3->get_array();
			$EDU_MAX = $data2[EN_NAME];
			***/
			
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = $person_count .". ";
			$arr_content[$data_count][prename] = $PN_NAME;
			$arr_content[$data_count][name] = 	    $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = trim($PL_NAME)?($PL_NAME ." ". $POSITION_TYPE . (($PT_CODE != "11" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"�дѺ ".$LEVEL_NAME;
			$arr_content[$data_count][levelname] = $LEVEL_NAME;
			$arr_content[$data_count][province_work] = $PROVINCE_WORK;	
			///$arr_content[$data_count][edu_max] = $EDU_MAX;
			$arr_content[$data_count][birthdate]	= $PER_BIRTHDATE;
			$arr_content[$data_count][startdate]	= $PER_STARTDATE;			//�ѹ��è�
			$arr_content[$data_count][deaddate] = $PER_DEADDATE;			//�ѹ������ª��Ե
			$arr_content[$data_count][reason] = $MOV_NAME;						//���˵ط�����ª��Ե
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
					else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
					else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
				break;
				case "ORG_2" :
					if($ORG_ID_2) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
					else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
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
			} // end switch case
		} // end for
	} // function
	
	//�ʴ���ª���˹��§ҹ
	if($search_per_type==1){
		// ����Ҫ���
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_POSITIONHIS e
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}
	}elseif($search_per_type==2){
		// �١��ҧ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_POSITIONHIS e
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}
	} // end if
	elseif($search_per_type==3){
		// ��ѡ�ҹ�Ҫ���
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_POSITIONHIS e
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition
							 order by		$order_by
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition
							 order by		$order_by
						   ";
		}
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "<br>$cmd<br>";
	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
//					echo "ORG => $ORG_ID :: $data[ORG_ID]<br>";
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
//							$ORG_NAME = "[����к��ӹѡ/�ͧ]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 9)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
//							$ORG_NAME_1 = "[����кؽ���]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 9)) . $ORG_NAME_1;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
//							$ORG_NAME_2 = "[����кاҹ]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 9)) . $ORG_NAME_2;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);									
						list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;		
			} // end switch case
		} // end for
		
		if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition);
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$PRE_NAME = $arr_content[$data_count][prename];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL_NAME = $arr_content[$data_count][levelname];
			$PROVINCE_WORK =  $arr_content[$data_count][province_work];	
			///$EDU_MAX = $arr_content[$data_count][edu_max];	
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			$STARTDATE = $arr_content[$data_count][startdate];
			$DEADDATE = $arr_content[$data_count][deaddate];	
			$MOV_NAME = $arr_content[$data_count][reason];
			
			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PRE_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$PROVINCE_WORK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$BIRTHDATE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$STARTDATE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PRE_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$PROVINCE_WORK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$BIRTHDATE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$STARTDATE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} // end if
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>