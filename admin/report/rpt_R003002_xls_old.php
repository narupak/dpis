<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "e.PL_CODE";
		$line_name = "e.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		 $line_search_code=trim($search_pl_code);
		 $line_search_name=trim($search_pl_name);
		 $line_title=" ��§ҹ";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "e.PN_CODE";
		$line_name = "e.PN_NAME";	
		 $line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ���͵��˹�";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "e.EP_CODE";
		$line_name = "e.EP_NAME";	
		 $line_search_code=trim($search_ep_code);
		 $line_search_name=trim($search_ep_name);
		$line_title=" ���͵��˹�";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "e.TP_CODE";
		$line_name = "e.TP_NAME";	
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
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "LEVEL", "SEX", "PROVINCE", "EDUCLEVEL", "EDUCMAJOR"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_ORG3 as ORG_ID_1";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID as ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_ORG3";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :		
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_UNDER_ORG1";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_UNDER_ORG1";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_UNDER_ORG2";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_UNDER_ORG2";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";
					
				if($order_by) $order_by .= ", ";
				$order_by .= "$line_code";
				
				$heading_name .=  $line_title;
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO, g.LEVEL_NAME";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO, g.LEVEL_NAME";

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
				$select_list .= "a.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PV_CODE";

				$heading_name .= " $PV_TITLE";
				break;
			case "EDUCLEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.EL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.EL_CODE";

				$heading_name .= " $EL_TITLE";
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
		if($select_org_structure==0) $order_by = "e.POH_ORG3";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list = "e.POH_ORG3";
		else if($select_org_structure==1)  $select_list = "a.ORG_ID";
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
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
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
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
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.OT_CODE)='02')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
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
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
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
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.OT_CODE)='04')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(e.POH_ORG3 = $search_org_name)";
				$list_type_text .= "$search_org_name";
			} 
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(e.POH_UNDER_ORG1 = $search_org_name_1)";
				$list_type_text .= " - $search_org_name_1";
			} // end if
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(e.POH_UNDER_ORG2 = $search_org_name_2)";
				$list_type_text .= " - $search_org_name_2";
			} // end if
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			}
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			}
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}elseif($list_type == "PER_LINE"){
		// ��§ҹ
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
	}elseif($list_type == "PER_COUNTRY"){
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(e.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(e.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
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
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
/*********	
	if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) > '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
 	$search_condition .= " and (a.PER_STATUS=2 or a.PER_STATUS=3) ";		//i�ͺ�è� (����Ѻ�͹�͡) ���� �鹨ҡ��ǹ�Ҫ��� (����Ѻ���³/���͡)     
********/
	//��� rpt_R003011.php

	$company_name = "�ٻẺ����͡��§ҹ : ". ($select_org_structure==1?"�ç���ҧ����ͺ���§ҹ - ":"�ç���ҧ��������� - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||�ӹǹ$PERSON_TYPE[$search_per_type]����͡�ҡ�Ҫ���㹻է�����ҳ $search_budget_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0302";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 50);
		$worksheet->set_column(1, 1, 8);
		$worksheet->set_column(2, 2, 10);
		$worksheet->set_column(3, 3, 8);
		$worksheet->set_column(4, 4, 8);
		$worksheet->set_column(5, 5, 8);
		$worksheet->set_column(6, 6, 8);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "$heading_name", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "���³", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "���³", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "���͡", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "�͹�͡", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "����", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "��͹��˹�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	function count_person($movement_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $arr_rpt_order, $search_per_type, $search_budget_year, $BKK_FLAG;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		switch($movement_type){	//***�� MOV_CODE ��� R003011***//
			case 1 :	//���³����
				if ($BKK_FLAG==1)
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('17','201'))";
				else
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('119','11910'))";
				if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
					and a.PER_POSDATE = a.PER_RETIREDATE)";
				elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($search_budget_year - 543)."-10-01' 
					and a.PER_POSDATE = a.PER_RETIREDATE)";
				elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
					and a.PER_POSDATE = a.PER_RETIREDATE)";
			break;
			case 2 :	//���³��͹��˹�
				if ($BKK_FLAG==1)
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('35', '208'))";
				else
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('11830'))";
				if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_POSDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and SUBSTR(trim(a.PER_POSDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
			break;
			case 3 :	//���͡
				if ($BKK_FLAG==1)
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('13', '27', '28', '29', '59', '20101', '20102', '20103', '20104', '20105', '20106', '20107', '20108', '20109', '20110', '20112', '203', '204', '205', '209'))";
				else
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('118', '11810', '11820', '120', '12010', '12020', '12030', '121', '12110', '122', '12210'))";
				if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_POSDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and SUBSTR(trim(a.PER_POSDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
			break;
			case 4 :	//���
				if ($BKK_FLAG==1)
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('30', '206'))";
				else
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('123', '12310'))";
				if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_POSDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and SUBSTR(trim(a.PER_POSDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
			break;
			case 5 :	//�͹�͡
				if ($BKK_FLAG==1)
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('5', '202'))";
				else
					$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('106', '10610', '10620'))";
				if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_POSDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and SUBSTR(trim(a.PER_POSDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
			break;
		} // end switch case

		if($DPISDB=="odbc"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select		count(a.PER_ID) as count_person
								 from			(
								 						(
															PER_PERSONAL a 
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID ";
			}else{
				$cmd = " select		count(a.PER_ID) as count_person
								 from			(
														PER_PERSONAL a 
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID ";
			} // end if
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select		count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_EDUCATE d, PER_POSITIONHIS e
								 where		a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
								 					and a.PER_ID=e.PER_ID(+)
													$search_condition
								 group by	a.PER_ID ";
			}else{
				$cmd = " select		count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITIONHIS e
								 where		a.PER_ID=e.PER_ID(+)
													$search_condition
								 group by	a.PER_ID ";
			} // end if
		}elseif($DPISDB=="mysql"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select		count(a.PER_ID) as count_person
								 from			(
								 						(
															PER_PERSONAL a 
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID ";
			}else{
				$cmd = " select		count(a.PER_ID) as count_person
								 from			(
														PER_PERSONAL a 
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID ";
			} // end if
		} // end if

		$count_person = $db_dpis2->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $ORG_NAME, $ORG_NAME_1, $ORG_NAME_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE, $EP_CODE, $TP_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID){
						if($select_org_structure==0){	
							$arr_addition_condition[] = "(e.POH_ORG3 = '$ORG_NAME')";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						}
					}	
				break;
				case "ORG_1" : 
					if($ORG_ID_1){
						if($select_org_structure==0){	 
							$arr_addition_condition[] = "(e.POH_UNDER_ORG1 = '$ORG_NAME_1')";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_1)";
						}
					}
				break;
				case "ORG_2" :
					if($ORG_ID_2){
						if($select_org_structure==0){	 
							$arr_addition_condition[] = "(e.POH_UNDER_ORG2 = '$ORG_NAME_2')";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_2)";
						}
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
					if($PV_CODE) $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE' or a.PV_CODE is null)";
				break;
				case "EDUCLEVEL" :
					if($EL_CODE>0) $arr_addition_condition[] = "(trim(d.EL_CODE) = '$EL_CODE')";
					//else $arr_addition_condition[] = "(trim(d.EL_CODE) = '$EL_CODE' or d.EL_CODE is null)";
				break;
				case "EDUCMAJOR" :
					if($EM_CODE) $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE')";
					else $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE' or d.EM_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE;
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
				case "SEX" :
					$PER_GENDER = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
					$PN_CODE = -1;
				break;
				case "EDUCLEVEL" :
					$EL_CODE = -1;
				break;
				case "EDUCMAJOR" :
					$EM_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//�ʴ�������		
	if ($BKK_FLAG==1)
		$cond_where = "and (trim(e.MOV_CODE) in ('17','201', '35', '30', '206', '208', '5', '202', '13', '27', '28', '29', '59', '20101', '20102', '20103', '20104', '20105', '20106', '20107', '20108', '20109', '20110', '20112', '203', '204', '205', '209'))";
	else
		$cond_where = "and (trim(e.MOV_CODE) in ('11830','118', '11810', '11820', '120', '12010', '12020', '12030', '121', '12110', '122', '12210','123', '12310','106', '10610', '10620'))";
	if($DPISDB=="odbc") $cond_where .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $cond_where .= " and (SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) > '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $cond_where .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
 	$cond_where .= " and (a.PER_STATUS=2 or a.PER_STATUS=3) ";		//i�ͺ�è� (����Ѻ�͹�͡) ���� �鹨ҡ��ǹ�Ҫ��� (����Ѻ���³/���͡)     
	if($DPISDB=="odbc"){
		if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
			$cmd = " select		distinct $select_list
							 from			(
							 						(
														PER_PERSONAL a 
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition	$cond_where
							 order by		$order_by ";
		}else{
			$cmd = " select		distinct $select_list
							 from			(
													PER_PERSONAL a 
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $cond_where
							 order by		$order_by ";
		} // end if
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
			$cmd = " select		distinct $select_list
							 from			PER_PERSONAL a, PER_EDUCATE d, PER_POSITIONHIS e
							 where		a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition $cond_where
							 order by		$order_by ";
		}else{
			$cmd = " select		distinct $select_list
							 from			PER_PERSONAL a, PER_POSITIONHIS e
							 where		a.PER_ID=e.PER_ID(+)
												$search_condition $cond_where
							 order by		$order_by ";
		} // end if
	}elseif($DPISDB=="mysql"){
		if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
			$cmd = " select		distinct $select_list
							 from			(
							 						(
														PER_PERSONAL a 
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $cond_where
							 order by		$order_by ";
		}else{
			$cmd = " select		distinct $select_list
							 from			(
													PER_PERSONAL a 
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $cond_where
							 order by		$order_by ";
		} // end if
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo $cmd;
	//$db_dpis->show_error();
	$data_count = 0;
	$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = $GRAND_TOTAL_4 = $GRAND_TOTAL_5 = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if(trim($data[ORG_ID_1]) && $ORG_ID != trim($data[ORG_ID_1])){	//ORG_ID_1
							$ORG_ID = trim($data[ORG_ID_1]);
							if($ORG_ID!=0 || $ORG_ID != ""){
								if($select_org_structure==0) 
									$ORG_NAME = $ORG_ID;
								elseif($select_org_structure==1) {
									$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
									$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
									$db_dpis2->send_cmd($cmd);
			//						$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$ORG_NAME = $data2[ORG_NAME];
									$ORG_SHORT = $data2[ORG_SHORT];
								}else{
									$ORG_NAME = "[����к�$ORG_TITLE]";
									$ORG_SHORT = "[����к�]";
								}
							}
                    if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][short_name] = $ORG_SHORT;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;
		
				case "ORG_1" :
					if(trim($data[ORG_ID_1]) && $ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						if($ORG_ID_1!=0 || $ORG_ID_1 != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							$ORG_SHORT_1 = $data2[ORG_SHORT];
						}else{
								$ORG_NAME_1 = "[����к�$ORG_TITLE1]";
								$ORG_SHORT_1 = "[����к�]";
						}
                        if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][short_name] = $ORG_SHORT_1;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;
		
				case "ORG_2" :
					if(trim($data[ORG_ID_2]) && $ORG_ID_2 != trim($data[ORG_ID_2])){	
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						if($ORG_ID_2!=0||$ORG_ID_2 != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							$ORG_SHORT_2 = $data2[ORG_SHORT];
						}else{
							$ORG_NAME_2 = "[����к�$ORG_TITLE2]";
							$ORG_SHORT_2 = "[����к�]";
						}
                     if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][short_name] = $ORG_SHORT_2;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;
		
				case "LINE" :
					if(trim($data[PL_CODE]) && $PL_CODE != trim($data[PL_CODE])){	
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							if($search_per_type==1){
								$cmd = " select $line_name as PL_NAME, $line_short_name from $line_table e where trim($line_code)='$PL_CODE' ";
							}else{
								$cmd = " select $line_name as PL_NAME from $line_table e where trim($line_code)='$PL_CODE' ";
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
						} // end if
			
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (($PER_GENDER==1)?"���":(($PER_GENDER==2)?"˭ԧ":""));
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "PROVINCE" :
					if(trim($data[PV_CODE]) && $PV_CODE != trim($data[PV_CODE])){	
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
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;

				case "EDUCLEVEL" :
					if(trim($data[EL_CODE]) && $EL_CODE != trim($data[EL_CODE])){	
						$EL_CODE = trim($data[EL_CODE]);
						if($EL_CODE !=0||$EL_CODE != ""){
							$cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
//							$EL_NAME= trim($data2[EN_SHORTNAME])?$data2[EN_SHORTNAME]:$data2[EL_NAME];
							$EL_NAME= $data2[EL_NAME];
						}else{
							$EL_NAME= "[����к��дѺ����֡��]";
						}

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCLEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EL_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCMAJOR" :
					if(trim($data[EM_CODE]) && $EM_CODE != trim($data[EM_CODE])){	
						$EM_CODE = trim($data[EM_CODE]);
						if($EM_CODE !=0||$EM_CODE != ""){
							$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EM_NAME = $data2[EM_NAME];
						}else{
							$EM_NAME = "[����к��Ң��Ԫ��͡]";
						}

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCMAJOR";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EM_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
		}
	} // end while
	
	if(array_search("EDUCLEVEL", $arr_rpt_order) !== false  && array_search("EDUCLEVEL", $arr_rpt_order) == 0){
		$GRAND_TOTAL_1 = count_person(1, $search_condition, "");
		$GRAND_TOTAL_2 = count_person(2, $search_condition, "");
		$GRAND_TOTAL_3 = count_person(3, $search_condition, "");
		$GRAND_TOTAL_4 = count_person(4, $search_condition, "");
		$GRAND_TOTAL_5 = count_person(5, $search_condition, "");
	} // end if
	if(array_search("EDUCMAJOR", $arr_rpt_order) !== false  && array_search("EDUCMAJOR", $arr_rpt_order) == 0){
		$GRAND_TOTAL_1 = count_person(1, $search_condition, "");
		$GRAND_TOTAL_2 = count_person(2, $search_condition, "");
		$GRAND_TOTAL_3 = count_person(3, $search_condition, "");
		$GRAND_TOTAL_4 = count_person(4, $search_condition, "");
		$GRAND_TOTAL_5 = count_person(5, $search_condition, "");
	} // end if

	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4 + $GRAND_TOTAL_5;
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
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			$COUNT_2 = $arr_content[$data_count][count_2];
			$COUNT_3 = $arr_content[$data_count][count_3];
			$COUNT_4 = $arr_content[$data_count][count_4];
			$COUNT_5 = $arr_content[$data_count][count_5];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4 + $COUNT_5;
			
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 1, ($COUNT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_1)):number_format($COUNT_1)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 2, ($COUNT_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_2)):number_format($COUNT_2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 3, ($COUNT_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_3)):number_format($COUNT_3)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 4, ($COUNT_4?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_4)):number_format($COUNT_4)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 5, ($COUNT_5?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_5)):number_format($COUNT_5)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 6, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 1, ($COUNT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_1)):number_format($COUNT_1)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 2, ($COUNT_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_2)):number_format($COUNT_2)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 3, ($COUNT_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_3)):number_format($COUNT_3)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 4, ($COUNT_4?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_4)):number_format($COUNT_4)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 5, ($COUNT_5?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_5)):number_format($COUNT_5)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 6, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			} // end if
		} // end for
				
		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "���", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		$worksheet->write($xlsRow, 1, ($GRAND_TOTAL_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_1)):number_format($GRAND_TOTAL_1)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 2, ($GRAND_TOTAL_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_2)):number_format($GRAND_TOTAL_2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 3, ($GRAND_TOTAL_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_3)):number_format($GRAND_TOTAL_3)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 4, ($GRAND_TOTAL_4?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_4)):number_format($GRAND_TOTAL_4)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 5, ($GRAND_TOTAL_5?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_5)):number_format($GRAND_TOTAL_5)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 6, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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