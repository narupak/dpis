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
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(b.POS_NO), 0, CInt(b.POS_NO))";
				elseif($DPISDB=="oci8") $order_by .= "TO_NUMBER(b.POS_NO)";
				elseif($DPISDB=="mysql") $order_by .= "b.POS_NO+0";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="oci8") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="mysql") $order_by .= "a.LEVEL_NO desc";
				break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (a.PER_STATUS = 1) and (b.POS_ID >= 0) )";
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
		$arr_search_condition[] = "(d.OT_CODE='01')";
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
		$arr_search_condition[] = "(d.OT_CODE='02')";
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
		$arr_search_condition[] = "(d.OT_CODE='03')";
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
		$arr_search_condition[] = "(d.OT_CODE='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
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
				$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}elseif($list_type == "PER_LINE"){
		// ��§ҹ
		$list_type_text = "";
		if(trim($search_pl_code) && trim($search_pn_code)  && trim($search_ep_code) ){ 
			$search_pl_code = trim($search_pl_code);
			$search_pn_code = trim($search_pn_code);
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code'))";
			$list_type_text .= "$search_pl_name, $search_pn_name,$search_ep_code";
		}elseif(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code'))";
			$list_type_text .= "$search_pl_name";
		}elseif(trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%'))";
			$list_type_text .= "$search_pn_name";
		}elseif(trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%'))";
			$list_type_text .= "$search_ep_name";
		}
	}elseif($list_type == "PER_COUNTRY"){
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$search_pv_code')";
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
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	if(in_array(1, $search_per_type)) $report_title = "$DEPARTMENT_NAME||��ª��͢���Ҫ��� (����ͺ���§ҹ)";
	$report_code = "R0495";

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
		
		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 7);
		$worksheet->set_column(2, 2, 15);		
		$worksheet->set_column(3, 3, 13);
		$worksheet->set_column(4, 4, 22);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 7);
		$worksheet->set_column(8, 8, 45);
		$worksheet->set_column(9, 9, 12);
		$worksheet->set_column(10, 10, 10);
		$worksheet->set_column(11, 11, 18);
		$worksheet->set_column(12, 12, 45);
		$worksheet->set_column(13, 13, 10);
		$worksheet->set_column(14, 14, 12);
		$worksheet->set_column(15, 15, 25);
		$worksheet->set_column(16, 16, 12);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "�ӴѺ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "�Ţ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
		$worksheet->write($xlsRow, 3, "ʡ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 5, "������", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 6, "�дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 7, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 8, "˹��§ҹ���������", set_format("xlsFmtTableHeader", "B", "C", "TLB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TRB", 1));
		$worksheet->write($xlsRow, 12, "˹��§ҹ����ͺ���§ҹ", set_format("xlsFmtTableHeader", "B", "C", "TLB", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 16, "������ǡѹ/", set_format("xlsFmtTableHeader", "B", "C", "LTR", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "���˹�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));	
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "�����/����/�ӹѡ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "�ѧ��Ѵ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "�ѧ�Ѵ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "���", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "�����/����/�ӹѡ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "�ѧ��Ѵ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "�ѧ�Ѵ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 15, "���", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 16, "��ҧ���", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_CHANGE_DATE, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, d.ORG_NAME, a.PER_CARDNO, a.PER_BIRTHDATE, k.ACTH_PL_NAME_ASSIGN,k.acth_org2_AssIGN, k.ACTH_ORG3_ASSIGN, 
										p.LEVEL_NAME AS LEVEL_NAME_ASSIGN, k.ACTH_EFFECTIVEDATE, k.ACTH_ENDDATE 
						 from	(
										(
											(
												(
													( 	
														(
															(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_ORG c on (d.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
											) left join PER_PROVINCE i on (d.PV_CODE=i.PV_CODE)											
										) left join PER_ORG_TYPE j on (d.OT_CODE=j.OT_CODE)
									) left join PER_ACTINGHIS k on (a.PER_CARDNO=k.PER_CARDNO)
								) left join PER_LEVEL p on (k.LEVEL_NO_ASSIGN=p.LEVEL_NO)
								$search_condition
						 order by		$order_by";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, d.ORG_NAME, a.ORG_ID AS ORG_ID_ASSIGN
										from PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_LEVEL h, PER_PROVINCE i, PER_ORG_TYPE j, PER_LEVEL p 
										where a.POS_ID=b.POS_ID(+) and b.ORG_ID=d.ORG_ID(+) and a.LEVEL_NO=h.LEVEL_NO(+) and d.PV_CODE=i.PV_CODE(+)
										and d.OT_CODE=j.OT_CODE(+) and d.department_id=c.org_id(+)
										$search_condition
						group by a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, i.PV_NAME, j.OT_NAME,c.ORG_NAME, d.ORG_NAME, a.ORG_ID
						 order by		$order_by";
	}
	if($select_org_structure==1) { 
		//$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
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
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 1));			
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		} // end if

		print_header();

		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$ORG_NAME_2="";
			$ORG_NAME_1="";
			$ORG_NAME_0="";
			$ORG_NAME="";
			$ORG_ID_REF="";

			$ORG_NAME_ASS_2="";
			$ORG_NAME_ASS_1="";
			$ORG_NAME_ASS="";
			$ORG_NAME_ASSIGN="";
			$ORG_ID_REF_ASSIGN="";

			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];

			$POS_NO = $data[POS_NO];
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE) = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$NAME = ($PN_NAME)."$PER_NAME";
			$SURNAME = trim($data[PER_SURNAME]);

			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);

			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);

			$PL_NAME = trim($PL_NAME) . " " . level_no_format($LEVEL_NAME) . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?" $PT_NAME":"");

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$level=trim($data[LEVEL_NAME]);
			$LEVEL_NAME1 = substr($level,strpos($level,"������")+6);
			$LEVEL_NAME1 = substr($LEVEL_NAME1,0,strlen($LEVEL_NAME1)-strlen(substr($LEVEL_NAME1,strpos($LEVEL_NAME1,"�дѺ")-1)));
			$LEVEL_NAME2 = substr($level,strpos($level,"�дѺ")+5);

			$PER_SALARY = $data[PER_SALARY];
		
			//˹��§ҹ���������
			$ORG_ID_REF = $data[ORG_ID_2];
			if ($ORG_ID_REF=="") $ORG_ID_REF = $data[ORG_ID_1];
			if ($ORG_ID_REF=="") $ORG_ID_REF = $data[ORG_ID];

			//ǹ�ٻ�����Ҩ�����͡��
			$DEPARTMENT_NAME="";
			while($DEPARTMENT_NAME == ""){
				$cmd = " SELECT ORG_NAME,ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID='$ORG_ID_REF'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				
				if (trim($data2[OL_CODE]) == "05"){
					$ORG_NAME_2 = $data2[ORG_NAME];
					$ORG_ID_REF = $data2[ORG_ID_REF];
				}
				elseif (trim($data2[OL_CODE]) == "04") {
					$ORG_NAME_1 = $data2[ORG_NAME];
					$ORG_ID_REF = $data2[ORG_ID_REF];
				}
				elseif (trim($data2[OL_CODE]) == "03") {
					$ORG_NAME_0 = $data2[ORG_NAME];
					$ORG_ID_REF = $data2[ORG_ID_REF];
				}
				elseif (trim($data2[OL_CODE]) == "02") $DEPARTMENT_NAME = $data2[ORG_NAME];
			}
			
			$ORG_NAME = $ORG_NAME_2;
			if ($ORG_NAME != "") $ORG_NAME.= "/ ".$ORG_NAME_1;
			else $ORG_NAME= $ORG_NAME_1;
			if ($ORG_NAME != "") $ORG_NAME.= "/ ".$ORG_NAME_0;
			else $ORG_NAME= $ORG_NAME_0;
			$PV_NAME=trim($data[PV_NAME]);
			$OT_NAME=trim($data[OT_NAME]);
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			
			//˹��§ҹ����ͺ���§ҹ
			$ORG_ID_REF_ASSIGN = $data[ORG_ID_ASSIGN];
			$ORG_ID_ASSIGN = $data[ORG_ID_ASSIGN];
			//��������˹��§ҹ����ͺ���§ҹ����ʴ������ҧ
			if ($ORG_ID_ASSIGN==""){
				$ORG_NAME_ASSIGN="";
				$PV_NAME_ASSIGN="";
				$OT_NAME_ASSIGN="";
				$DEPARTMENT_NAME_ASSIGN="";
				$DEPARTMENT_DIFF="";
			}else{
				//ǹ�ٻ�����Ҩ�����͡��
				$DEPARTMENT_NAME_ASSIGN="";
				while($DEPARTMENT_NAME_ASSIGN == ""){
					$cmd = " SELECT ORG_NAME,ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID='$ORG_ID_REF_ASSIGN'";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();

					if (trim($data2[OL_CODE]) == "05"){
						$ORG_NAME_ASS_2 = $data2[ORG_NAME];
						$ORG_ID_REF_ASSIGN = $data2[ORG_ID_REF];
					}
					elseif (trim($data2[OL_CODE]) == "04") {
						$ORG_NAME_ASS_1 = $data2[ORG_NAME];
						$ORG_ID_REF_ASSIGN = $data2[ORG_ID_REF];
					}
					elseif (trim($data2[OL_CODE]) == "03") {
						$ORG_NAME_ASS = $data2[ORG_NAME];
						$ORG_ID_REF_ASSIGN = $data2[ORG_ID_REF];
					}
					elseif (trim($data2[OL_CODE]) == "02") $DEPARTMENT_NAME_ASSIGN = $data2[ORG_NAME];
				}
				$ORG_NAME_ASSIGN = $ORG_NAME_ASS_2;
				if ($ORG_NAME_ASSIGN != "") $ORG_NAME_ASSIGN.= "/ ".$ORG_NAME_ASS_1;
				else $ORG_NAME_ASSIGN= $ORG_NAME_ASS_1;
				if ($ORG_NAME_ASSIGN != "") $ORG_NAME_ASSIGN.= "/ ".$ORG_NAME_ASS;
				else $ORG_NAME_ASSIGN= $ORG_NAME_ASS;

				$cmd = " SELECT PV_NAME FROM PER_PROVINCE a, PER_ORG_ASS b WHERE a.PV_CODE=b.PV_CODE and b.ORG_ID='$ORG_ID_ASSIGN'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PV_NAME_ASSIGN=trim($data[PV_NAME]);

				$cmd = " SELECT OT_NAME FROM PER_ORG_TYPE a, PER_ORG_ASS b WHERE a.OT_CODE=b.OT_CODE and b.ORG_ID='$ORG_ID_ASSIGN'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$OT_NAME_ASSIGN=trim($data[OT_NAME]);

				if ($DEPARTMENT_NAME == $DEPARTMENT_NAME_ASSIGN) $DEPARTMENT_DIFF="������ǡѹ";
				else  $DEPARTMENT_DIFF="��ҧ���";
			}

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($data_count):$data_count), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));			
			$worksheet->write_string($xlsRow, 3, "$SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$LEVEL_NAME1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$LEVEL_NAME2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, ($PER_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)):number_format($PER_SALARY, ",")):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 9, "$PV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 10, "$OT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 11, "$DEPARTMENT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 12, "$ORG_NAME_ASSIGN", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 13, "$PV_NAME_ASSIGN", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 14, "$OT_NAME_ASSIGN", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 15, "$DEPARTMENT_NAME_ASSIGN", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 16, "$DEPARTMENT_DIFF", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
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