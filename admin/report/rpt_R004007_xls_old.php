<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("POS_NO", "NAME", "LINE", "LEVEL", "ORG", "ORG_1", "ORG_2"); 
	$order_by = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POS_NO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "b.POS_NO_NAME, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO)), c.POEM_NO_NAME, IIf(IsNull(c.POEM_NO), 0, CLng(c.POEM_NO)), 
																						f.POEMS_NO_NAME, IIf(IsNull(f.POEMS_NO), 0, CLng(f.POEMS_NO)), i.POT_NO_NAME, IIf(IsNull(i.POT_NO), 0, CLng(i.POT_NO))";
				elseif($DPISDB=="oci8") $order_by .= "b.POS_NO_NAME, TO_NUMBER(b.POS_NO), c.POEM_NO_NAME, TO_NUMBER(c.POEM_NO), 
																							f.POEMS_NO_NAME, TO_NUMBER(f.POEMS_NO), i.POT_NO_NAME, TO_NUMBER(i.POT_NO)";
				elseif($DPISDB=="mysql") $order_by .= "b.POS_NO_NAME, b.POS_NO+0, c.POEM_NO_NAME, c.POEM_NO+0, f.POEMS_NO_NAME, f.POEMS_NO+0, i.POT_NO_NAME, i.POT_NO+0";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE, c.PN_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO desc";
				break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1, c.ORG_ID_1";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID, c.ORG_ID_1";				
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2, c.ORG_ID_2";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID, c.ORG_ID_2";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

	$search_condition = "";
	if(trim($search_salary_date)){
		$search_salary_date =  save_date($search_salary_date);
		$show_salary_date = show_date_format($search_salary_date, 3);
	} // end if
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (a.PER_STATUS = 1) and (b.POS_ID >= 0 or c.POEM_ID >= 0 or f.POEMS_ID >=0 or i.POT_ID >=0) )";
//	$arr_search_condition[] = "(a.PER_TYPE=1)";	
		
	$list_type_text = $ALL_REPORT_TITLE;
/*	if($select_org_structure==0){ $list_type_text .= " �ç���ҧ���������"; 	}
	elseif($select_org_structure==1){ $list_type_text .= " �ç���ҧ����ͺ���§ҹ"; }  */		
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
		$arr_search_condition[] = "(d.OT_CODE='01' or e.OT_CODE='01')";
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
		$arr_search_condition[] = "(d.OT_CODE='02' or e.OT_CODE='02')";
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
		$arr_search_condition[] = "(d.OT_CODE='03' or e.OT_CODE='03')";
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
		$arr_search_condition[] = "(d.OT_CODE='04' or e.OT_CODE='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id or c.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			}
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1 or c.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			}
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_2 or c.ORG_ID_1 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			}
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id or c.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			}
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_1 or c.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			}
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id or c.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_2";
			}
		}
	}elseif($list_type == "PER_LINE"){
		// ��§ҹ
		$list_type_text = "";
		if(trim($search_pl_code) && trim($search_pn_code)  && trim($search_ep_code) ){ 
			$search_pl_code = trim($search_pl_code);
			$search_pn_code = trim($search_pn_code);
			$search_ep_code = trim($search_ep_code);
			$search_tp_code = trim($search_tp_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code') or (a.PER_TYPE=2 and trim(c.PN_CODE)='$search_pn_code') or (a.PER_TYPE=3 and trim(f.EP_CODE)='$search_ep_code')  or (a.PER_TYPE=4 and trim(i.TP_CODE)='$search_tp_code'))";
			$list_type_text .= "$search_pl_name, $search_pn_name,$search_ep_code,$search_tp_code";
		}elseif(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code') or (a.PER_TYPE=2 and trim(c.PN_CODE) like '%') or (a.PER_TYPE=3 and trim(f.EP_CODE) like '%') or (a.PER_TYPE=4 and trim(i.TP_CODE) like '%'))";
			$list_type_text .= "$search_pl_name";
		}elseif(trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%') or (a.PER_TYPE=2 and trim(c.PN_CODE)='$search_pn_code') or (a.PER_TYPE=3 and trim(f.EP_CODE) like '%') or (a.PER_TYPE=4 and trim(i.TP_CODE) like '%'))";
			$list_type_text .= "$search_pn_name";
		}elseif(trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%') or (a.PER_TYPE=2 and trim(c.PN_CODE) like '%') or (a.PER_TYPE=3 and trim(f.EP_CODE) ='$search_ep_code') or (a.PER_TYPE=4 and trim(i.TP_CODE) ='$search_tp_code'))";
			$list_type_text .= "$search_ep_name";
		}elseif(trim($search_tp_code)){
			$search_tp_code = trim($search_tp_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%') or (a.PER_TYPE=2 and trim(c.PN_CODE) like '%') or (a.PER_TYPE=3 and trim(f.EP_CODE) like '%') or (a.PER_TYPE=4 and trim(i.TP_CODE) ='$search_tp_code'))";
			$list_type_text .= "$search_tp_name";
		}
	}elseif($list_type == "PER_COUNTRY"){
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE) = '$search_ct_code' or trim(e.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$search_pv_code' or trim(e.PV_CODE) = '$search_pv_code')";
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
			if($select_org_structure==0) $arr_search_condition[] = "(trim(d.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "�ٻẺ����͡��§ҹ : ". ($select_org_structure==1?"�ç���ҧ����ͺ���§ҹ - ":"�ç���ҧ��������� - ") ."$list_type_text";
	if(in_array(1, $search_per_type) && in_array(2, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��͢���Ҫ�������١��ҧ��Ш� ���˹� �ѧ�Ѵ";
	else if(in_array(1, $search_per_type) && in_array(3, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��͢���Ҫ�����о�ѡ�ҹ�Ҫ��� ���˹� �ѧ�Ѵ";	
	else if(in_array(1, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��͢���Ҫ�������١��ҧ���Ǥ��� ���˹� �ѧ�Ѵ";	
	
	else if(in_array(2, $search_per_type) && in_array(3, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª����١��ҧ��Ш���о�ѡ�ҹ�Ҫ��� ���˹� �ѧ�Ѵ";
	else if(in_array(2, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª����١��ҧ��Ш�����١��ҧ���Ǥ��� ���˹� �ѧ�Ѵ";
	
	else if(in_array(3, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��;�ѡ�ҹ�Ҫ�������١��ҧ���Ǥ��� ���˹� �ѧ�Ѵ";

	else if(in_array(1, $search_per_type) && in_array(2, $search_per_type) && in_array(3, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��͢���Ҫ��� , �١��ҧ��Ш� ��о�ѡ�ҹ�Ҫ��� ���˹� �ѧ�Ѵ";	
	else if(in_array(1, $search_per_type) && in_array(2, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��͢���Ҫ��� , �١��ҧ��Ш� ����١��ҧ���Ǥ��� ���˹� �ѧ�Ѵ";	
	else if(in_array(1, $search_per_type) && in_array(3, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��͢���Ҫ���, ��ѡ�ҹ�Ҫ���  ����١��ҧ���Ǥ��� ���˹� �ѧ�Ѵ";	
	else if(in_array(2, $search_per_type) && in_array(3, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª����١��ҧ��Ш�,��ѡ�ҹ�Ҫ���  ����١��ҧ���Ǥ��� ���˹� �ѧ�Ѵ";	
	
	else if(in_array(1, $search_per_type) && in_array(2, $search_per_type) && in_array(3, $search_per_type) && in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��͢���Ҫ��� , �١��ҧ��Ш� ��ѡ�ҹ�Ҫ��� ����١��ҧ���Ǥ��� ���˹� �ѧ�Ѵ";
	
	elseif(in_array(1, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��͢���Ҫ��� ���˹� �ѧ�Ѵ";
	elseif(in_array(2, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª����١��ҧ��Ш� ���˹� �ѧ�Ѵ";
	elseif(in_array(3, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��;�ѡ�ҹ�Ҫ��� ���˹� �ѧ�Ѵ";
	elseif(in_array(4, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª����١��ҧ���Ǥ���  ���˹� �ѧ�Ѵ";
	$report_code = "R0407";

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
		global $heading_name, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2, $SESS_DEPARTMENT_NAME;
		
		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 20);		
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 20);
		$worksheet->set_column(5, 5, 20);
		$worksheet->set_column(6, 6, 20);
		$worksheet->set_column(7, 7, 20);
		$worksheet->set_column(8, 8, 8);
		$worksheet->set_column(9, 9, 25);
		if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
			$worksheet->set_column(10, 10, 8);
			$worksheet->set_column(11, 11, 20);
			$worksheet->set_column(12, 12, 20);
			$worksheet->set_column(13, 13, 10);
			$worksheet->set_column(14, 14, 12);
			$worksheet->set_column(15, 15, 12);
			$worksheet->set_column(16, 16, 10);
			$worksheet->set_column(17, 17, 18);
			$worksheet->set_column(18, 18, 18);
			$worksheet->set_column(19, 19, 8);
			$worksheet->set_column(20, 20, 20);
			$worksheet->set_column(21, 21, 8);
		} else {
			$worksheet->set_column(10, 10, 20);
			$worksheet->set_column(11, 11, 20);
			$worksheet->set_column(12, 12, 10);
			$worksheet->set_column(13, 13, 12);
			$worksheet->set_column(14, 14, 12);
			$worksheet->set_column(15, 15, 10);
			$worksheet->set_column(16, 16, 18);
			$worksheet->set_column(17, 17, 18);
			$worksheet->set_column(18, 18, 12);
			$worksheet->set_column(19, 19, 20);
		}

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "�ӴѺ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "���� - ʡ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "�Ţ��Шӵ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
		$worksheet->write($xlsRow, 3, "�ѹ ��͹ ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "���˹觻�����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "�дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "��ǧ�дѺ���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "�Ţ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
			$worksheet->write($xlsRow, 10, "���ʨѧ��Ѵ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 11, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 12, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 13, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 14, "�ѹ��è�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 15, "�ѹ�������дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 16, "����ͧ�Ҫ�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 17, "����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 18, "�����Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 19, "�Ţ��ͨ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 20, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 21, "���ʨѧ��Ѵ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		} else {
			$worksheet->write($xlsRow, 10, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 11, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 12, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 13, "�ѹ��è�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 14, "�ѹ�������дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 15, "����ͧ�Ҫ�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 16, "����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 17, "�����Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 18, "�ҹ㹡��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 19, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		}

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "��ЪҪ�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "�Դ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));	
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "���˹�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
			$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		} else {
			$worksheet->write($xlsRow, 18, "�ӹǳ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 19, "�ͺ���§ҹ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		}
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, h.POSITION_TYPE, h.POSITION_LEVEL, 
										a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, b.PL_CODE, b.PT_CODE, b.POS_NO_NAME, b.POS_NO, b.ORG_ID, b.ORG_ID_1, 
										b.ORG_ID_2, b.CL_NAME, d.ORG_NAME, a.PER_CARDNO,  a.PER_BIRTHDATE, c.PN_CODE as EMP_PL_CODE, 
										c.POEM_NO_NAME as EMP_POS_NO_NAME, c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID,
										e.ORG_NAME as EMP_ORG_NAME, f.EP_CODE as EMPSER_PL_CODE, f.POEMS_NO_NAME as EMPSER_POS_NO_NAME, 
										f.POEMS_NO as EMPSER_POS_NO, f.ORG_ID as EMPSER_ORG_ID, g.ORG_NAME as EMPSER_ORG_NAME,
										i.TP_CODE as POT_PL_CODE, i.POT_NO_NAME as POT_POS_NO_NAME, i.POT_NO as POT_POS_NO, i.ORG_ID as POT_ORG_ID,
										j.ORG_NAME as POT_ORG_NAME, d.PV_CODE, a.ORG_ID as ORG_ID_ASS										
						 from (
									(
										(
											( 	
												(
													(	
														(
															(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)
												) left join PER_ORG e on (c.ORG_ID=e.ORG_ID)
											) left join PER_POS_EMPSER f on (a.POEMS_ID=f.POEMS_ID)
										) left join PER_ORG g on (f.ORG_ID=g.ORG_ID)
									) left join PER_POS_TEMP i on (a.POT_ID=i.POT_ID)	
								) left join PER_ORG j on (i.ORG_ID=j.ORG_ID)
							) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
										$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, h.POSITION_TYPE, h.POSITION_LEVEL, 
										a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, b.PL_CODE, b.PT_CODE, b.POS_NO_NAME, b.POS_NO, b.ORG_ID, b.ORG_ID_1, 
										b.ORG_ID_2, b.CL_NAME, d.ORG_NAME, a.PER_CARDNO,  a.PER_BIRTHDATE, c.PN_CODE as EMP_PL_CODE, 
										c.POEM_NO_NAME as EMP_POS_NO_NAME, c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID,
										e.ORG_NAME as EMP_ORG_NAME, f.EP_CODE as EMPSER_PL_CODE, f.POEMS_NO_NAME as EMPSER_POS_NO_NAME, 
										f.POEMS_NO as EMPSER_POS_NO, f.ORG_ID as EMPSER_ORG_ID, g.ORG_NAME as EMPSER_ORG_NAME,
										i.TP_CODE as POT_PL_CODE, i.POT_NO_NAME as POT_POS_NO_NAME, i.POT_NO as POT_POS_NO, i.ORG_ID as POT_ORG_ID,
										j.ORG_NAME as POT_ORG_NAME, d.PV_CODE, a.ORG_ID as ORG_ID_ASS										
						 from		PER_PERSONAL a, PER_POSITION b, PER_POS_EMP c, PER_ORG d, PER_ORG e, 
						 				PER_POS_EMPSER f, PER_ORG g, PER_LEVEL h, PER_POS_TEMP i, PER_ORG j
						 where	a.POS_ID=b.POS_ID(+) and b.ORG_ID=d.ORG_ID(+) and a.POEM_ID=c.POEM_ID(+) and c.ORG_ID=e.ORG_ID(+) and a.POEMS_ID=f.POEMS_ID(+) and f.ORG_ID=g.ORG_ID(+) and a.POT_ID=i.POT_ID(+) and i.ORG_ID=j.ORG_ID(+)
						 				and a.LEVEL_NO=h.LEVEL_NO(+)
										$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, h.POSITION_TYPE, h.POSITION_LEVEL, 
										a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, b.PL_CODE, b.PT_CODE, b.POS_NO_NAME, b.POS_NO, b.ORG_ID, b.ORG_ID_1, 
										b.ORG_ID_2, b.CL_NAME, d.ORG_NAME, a.PER_CARDNO,  a.PER_BIRTHDATE, c.PN_CODE as EMP_PL_CODE, 
										c.POEM_NO_NAME as EMP_POS_NO_NAME, c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID,
										e.ORG_NAME as EMP_ORG_NAME, f.EP_CODE as EMPSER_PL_CODE, f.POEMS_NO_NAME as EMPSER_POS_NO_NAME, 
										f.POEMS_NO as EMPSER_POS_NO, f.ORG_ID as EMPSER_ORG_ID, g.ORG_NAME as EMPSER_ORG_NAME,
										i.TP_CODE as POT_PL_CODE, i.POT_NO_NAME as POT_POS_NO_NAME, i.POT_NO as POT_POS_NO, i.ORG_ID as POT_ORG_ID,
										j.ORG_NAME as POT_ORG_NAME, d.PV_CODE, a.ORG_ID as ORG_ID_ASS										
						 from (
									(
										(
											( 	
												(
													(	
														(
															(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)
												) left join PER_ORG e on (c.ORG_ID=e.ORG_ID)
											) left join PER_POS_EMPSER f on (a.POEMS_ID=f.POEMS_ID)
										) left join PER_ORG g on (f.ORG_ID=g.ORG_ID)
									) left join PER_POS_TEMP i on (a.POT_ID=i.POT_ID)	
								) left join PER_ORG j on (i.ORG_ID=j.ORG_ID)
							) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
										$search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

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
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
				$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
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
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
				$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
		} // end if

		print_header();

		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]);
			$CL_NAME = trim($data[CL_NAME]);
			$POSITION_TYPE = trim($data[POSITION_TYPE]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_BIRTHDATE =  show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			
			$TMP_PER_BIRTHDATE = substr($data[PER_BIRTHDATE], 0, 10);
		    $TMP_AGE = date_difference(date("Y-m-d"), $TMP_PER_BIRTHDATE, "full");
			
			$cmd = " select 	LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2
							from	PER_LAYER 
							where LAYER_TYPE=0 and LEVEL_NO='$LEVEL_NO' and LAYER_NO=0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LAYER_SALARY_MIDPOINT = $data2[LAYER_SALARY_MIDPOINT] + 0;
			$LAYER_SALARY_MIDPOINT1 = $data2[LAYER_SALARY_MIDPOINT1] + 0;
			$LAYER_SALARY_MIDPOINT2 = $data2[LAYER_SALARY_MIDPOINT2] + 0;
			if ($data[PER_SALARY] > $LAYER_SALARY_MIDPOINT) $SALARY_MIDPOINT = number_format($LAYER_SALARY_MIDPOINT2);
			else $SALARY_MIDPOINT = number_format($LAYER_SALARY_MIDPOINT1);
			
			$ORG_ID_ASS = $data[ORG_ID_ASS];
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG_ASS where ORG_ID=$ORG_ID_ASS ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_ASS = $data2[ORG_NAME];
			
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$ORG_ID_2 = trim($data[ORG_ID_2]);
			
			if($PER_TYPE==1){
				$POS_NO = $data[POS_NO_NAME].$data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_NAME = $data[ORG_NAME];
				$PV_CODE = $data[PV_CODE];
				
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = '$ORG_ID_1' ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_1 = trim($data2[ORG_NAME]);
				
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = '$ORG_ID_2' ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_2 = trim($data2[ORG_NAME]);

				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_NO = $data[EMP_POS_NO_NAME].$data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_NAME = $data[EMP_ORG_NAME];
				
				
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = '$ORG_ID_1' ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_1 = trim($data2[ORG_NAME]);
				
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = '$ORG_ID_2' ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_2 = trim($data2[ORG_NAME]);

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
				
				
			}elseif($PER_TYPE==3){
				$POS_NO = $data[EMPSER_POS_NO_NAME].$data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_NAME = $data[EMPSER_ORG_NAME];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			} elseif($PER_TYPE==4){
				$POS_NO = $data[POT_POS_NO_NAME].$data[POT_POS_NO];
				$PL_CODE = trim($data[POT_PL_CODE]);
				$ORG_ID = $data[POT_ORG_ID];
				$ORG_NAME = $data[POT_ORG_NAME];
				
				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
		//	$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
			} 
			$PL_NAME = trim($PL_NAME);
			

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE) = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";

			$PER_SALARY = number_format($data[PER_SALARY]);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
			
			$TMP_PER_STARTDATE = substr($data[PER_STARTDATE], 0, 10);
		    $TMP_OFFICER_TIME = date_difference(date("Y-m-d"), $TMP_PER_STARTDATE, "full");
			
			$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);

			if(trim($search_salary_date)){
				$cmd = " select SAH_SALARY, SAH_POS_NO, SAH_PAY_NO from PER_SALARYHIS 
								  where PER_ID=$PER_ID and SAH_EFFECTIVEDATE <= '$search_salary_date' 
								 order by SAH_SALARY desc, SAH_EFFECTIVEDATE desc ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_SALARY = $data2[SAH_SALARY];
//				$POS_NO = $data2[SAH_POS_NO];
				$PAY_NO = $data2[SAH_PAY_NO];

				$cmd = " select ORG_NAME, PV_CODE from PER_POSITION a, PER_ORG b where a.ORG_ID = b.ORG_ID and POS_NO = '$PAY_NO' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_ORG_NAME = trim($data2[ORG_NAME]);
				$PAY_PV_CODE = trim($data2[PV_CODE]);
			}

			$cmd = " select 	a.DC_CODE, b.DC_SHORTNAME, b.DC_NAME
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.DC_CODE=b.DC_CODE and a.PER_ID=$PER_ID and b.DC_TYPE not in (3)
							 order by b.DC_ORDER desc ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DC_CODE = trim($data2[DC_CODE]);
			$DC_NAME = "";
			if($DC_CODE) $DC_NAME = (trim($data2[DC_SHORTNAME])?$data2[DC_SHORTNAME]:$data2[DC_NAME]);
			$PER_CARDNO = card_no_format($PER_CARDNO, $CARD_NO_DISPLAY);

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($data_count):$data_count), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$FULLNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_CARDNO):$PER_CARDNO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));			
			$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_BIRTHDATE):$PER_BIRTHDATE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$POSITION_TYPE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$POSITION_LEVEL", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, "$CL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORG_NAME):$ORG_NAME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") { 
				$worksheet->write_string($xlsRow, 10, (($NUMBER_DISPLAY==2)?convert2thaidigit($PV_CODE):$PV_CODE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORG_NAME_1):$ORG_NAME_1), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORG_NAME_2):$ORG_NAME_2), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 13, ($PER_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_STARTDATE):$PER_STARTDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15, (($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_EFFECTIVEDATE):$LEVEL_EFFECTIVEDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 16, "$DC_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 17, (($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_AGE):$TMP_AGE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 18, (($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_OFFICER_TIME):$TMP_OFFICER_TIME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 19, (($NUMBER_DISPLAY==2)?convert2thaidigit($PAY_NO):$PAY_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 20, (($NUMBER_DISPLAY==2)?convert2thaidigit($PAY_ORG_NAME):$PAY_ORG_NAME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 21, (($NUMBER_DISPLAY==2)?convert2thaidigit($PAY_PV_CODE):$PAY_PV_CODE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} else {
				$worksheet->write_string($xlsRow, 10, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORG_NAME_1):$ORG_NAME_1), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORG_NAME_2):$ORG_NAME_2), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 12, ($PER_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_STARTDATE):$PER_STARTDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14, (($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_EFFECTIVEDATE):$LEVEL_EFFECTIVEDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15, "$DC_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 16, (($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_AGE):$TMP_AGE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 17, (($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_OFFICER_TIME):$TMP_OFFICER_TIME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 18, (($NUMBER_DISPLAY==2)?convert2thaidigit($SALARY_MIDPOINT):$SALARY_MIDPOINT), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 19, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORG_NAME_ASS):$ORG_NAME_ASS), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} 
		} // end while
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
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));		
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));		
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));		
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
		if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
			$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
			$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
		}
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