<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
		
	$budget_year = $search_budget_year - 543; 
	$budget_year_from = $budget_year - 1; 
	$budget_year_from = $budget_year_from.'-10-01'; 
	$budget_year_to = $budget_year.'-09-30';

	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("POSNO"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POSNO" :
				if($select_list) $select_list .= ", ";

				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") {
					$select_list .= "IIf(IsNull(a.POS_NO), 0 , CLng(a.POS_NO)) as POS_NO";				
					$order_by .= "IIf(IsNull(a.POS_NO), 0 , CLng(a.POS_NO))";
				}
				if($DPISDB=="oci8") {
					$select_list .= "a.POS_NO";
					$order_by .= "to_number(replace(a.POS_NO,'-',''))";
				}elseif($DPISDB=="mysql"){
					$select_list .= "a.POS_NO";				
					$order_by .= "a.POS_NO";
				}

				$heading_name .= " �Ţ�����˹�";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.POS_NO";
	if(!trim($select_list)) $select_list = "a.POS_NO";

	$search_condition = "";
	$arr_search_condition[] = "(a.POS_STATUS=1)";
	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "�ٻẺ�ҵðҹ��ҧ�ͧ File ����Ѻ������к� �ͧ�ӹѡ�ҹ �.�.";
	$report_code = "GPIS Format File";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
/*
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
*/
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 5);
		$worksheet->set_column(1, 1, 15);
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 40);
		$worksheet->set_column(4, 4, 40);
		$worksheet->set_column(5, 5, 12);
		$worksheet->set_column(6, 6, 20);
		$worksheet->set_column(7, 7, 40);
		$worksheet->set_column(8, 8, 20);
		$worksheet->set_column(9, 9, 15);
		$worksheet->set_column(10, 10, 10);
		$worksheet->set_column(11, 11, 40);
		$worksheet->set_column(12, 12, 20);
		$worksheet->set_column(13, 13, 40);
		$worksheet->set_column(14, 14, 20);
		$worksheet->set_column(15, 15, 20);
		$worksheet->set_column(16, 16, 10);
		$worksheet->set_column(17, 17, 20);
		$worksheet->set_column(18, 18, 25);
		$worksheet->set_column(19, 19, 20);
		$worksheet->set_column(20, 20, 20);
		$worksheet->set_column(21, 21, 20);
		$worksheet->set_column(22, 22, 25);
		$worksheet->set_column(23, 23, 25);
		$worksheet->set_column(24, 24, 20);
		$worksheet->set_column(25, 25, 10);
		$worksheet->set_column(26, 26, 5);
		$worksheet->set_column(27, 27, 15);
		$worksheet->set_column(28, 28, 15);
		$worksheet->set_column(29, 29, 15);
		$worksheet->set_column(30, 30, 15);
		$worksheet->set_column(31, 31, 20);
		$worksheet->set_column(32, 32, 20);
		$worksheet->set_column(33, 33, 40);
		$worksheet->set_column(34, 34, 40);
		$worksheet->set_column(35, 35, 40);
		$worksheet->set_column(36, 36, 25);
		$worksheet->set_column(37, 37, 40);
		$worksheet->set_column(38, 38, 20);
		$worksheet->set_column(39, 39, 40);
		$worksheet->set_column(40, 40, 20);
		$worksheet->set_column(41, 41, 40);
		$worksheet->set_column(42, 42, 20);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "�ӴѺ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "���ʡ�з�ǧ+���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "����$MINISTRY_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "����$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "����$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "�Ţ�����˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "���ʵ��˹����§ҹ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "���͵��˹����§ҹ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "��������������˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "���������˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "�����дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 11, "�����дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "���ʵ��˹�㹡�ú����çҹ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 13, "���͵��˹�㹡�ú����çҹ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 14, "�����ҢҤ�������Ǫҭ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 15, "�����ҢҤ�������Ǫҭ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 16, "���ʨѧ��Ѵ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 17, "���ͨѧ��Ѵ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 18, "�ѧ�Ѵ�Ҫ�����ǹ��ҧ/��ǹ�����Ҥ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 19, "ʶҹ�Ҿ�ͧ���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 20, "���ʤӹ�˹�Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 21, "�ӹ�˹�Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 22, "���͵��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 23, "����ʡ��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 24, "�Ţ��Шӵ�ǻ�ЪҪ�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 25, "������", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 26, "��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 27, "�ѹ ��͹ ���Դ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 28, "�ѹ ��͹ �պ�è�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 29, "�Թ��͹�Ѩ�غѹ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 30, "�Թ��Шӵ��˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 31, "�����дѺ����֡���٧�ش", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 32, "�����дѺ����֡���٧�ش", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 33, "�����زԡ���֡���٧�ش", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 34, "�����Ң��Ԫ��͡", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 35, "����ʶҺѹ����֡��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 36, "���ʻ���ȷ������稡���֡��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 37, "���ͻ���ȷ������稡���֡��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 38, "���ʻ������ع����֡��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 39, "���ͻ������ع����֡��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 40, "���ʡ������͹��Ǣͧ����Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 41, "���͡������͹��Ǣͧ����Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 42, "�ѹ��������ռźѧ�Ѻ��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $POS_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					if($POS_NO) $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO')";
					else $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO' or a.POS_NO is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $POS_NO;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					$POS_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function


	// ===== select data =====
	if($DPISDB=="odbc"){
		$cmd = " select		distinct 
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, e.PT_NAME, 
							a.CL_NAME, f.PV_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, 
							b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
				 from		(
								(
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
										) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
									) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
								) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
							) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
							$search_condition
				 order by		b.ORG_ID_REF, $order_by
					   ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " select		distinct 
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, e.PT_NAME, 
							a.CL_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, f.PV_NAME, 
							b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
				from			PER_POSITION a, PER_ORG b, PER_LINE c, PER_MGT d, PER_TYPE e, PER_PROVINCE f, 
							PER_ORG_TYPE g
				where		a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE and a.PM_CODE=d.PM_CODE(+) and 
							a.PT_CODE=e.PT_CODE(+) and b.PV_CODE=f.PV_CODE(+) and 
							b.OT_CODE=g.OT_CODE(+)	
							$search_condition
				order by		b.ORG_ID_REF, $order_by   ";
// 							and POS_ID<2000				
	}elseif($DPISDB=="mysql"){
		$cmd = " select		distinct 
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, e.PT_NAME, 
							a.CL_NAME, f.PV_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, 
							b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
				 from		(
								(
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
										) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
									) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
								) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
							) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
							$search_condition
				 order by		b.ORG_ID_REF, $order_by
					   ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];

			$arr_content[$data_count][type] = "ORG_REF";
			$arr_content[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_content[$data_count][org_name_ref] = $ORG_NAME_REF;
			
			$data_count++;
		} // end if
		
		include("rpt_gpis_xls_dtl.php");
		
	} // end while
	
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	
	// ===== condition $count_data  from "select data" =====
	if($count_data){
		$xlsRow = 0;
		$count_org_ref = 0;

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$ORG_CODE = $arr_content[$data_count][org_code];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$PN_CODE = $arr_content[$data_count][pn_code];
			$PN_NAME = $arr_content[$data_count][pn_name];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_SURNAME = $arr_content[$data_count][per_surname];
			$PER_GENDER = $arr_content[$data_count][per_gender];
			$PER_GENDER_NAME = $BIRTHDATE = $STARTDATE = "";
			if ($arr_content[$data_count][per_gender] == 1)	$PER_GENDER_NAME = "���";
			elseif ($arr_content[$data_count][per_gender] == 2)	$PER_GENDER_NAME = "˭ԧ";	
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			if ($BIRTHDATE=="//") $BIRTHDATE = "";
			$STARTDATE = $arr_content[$data_count][startdate];
			if ($STARTDATE=="//") $STARTDATE = "";
			if ($PER_NAME) $POS_STATUS = "�դ���ͤ�ͧ";
			else $POS_STATUS = "���˹���ҧ";
			$POS_NO = $arr_content[$data_count][pos_no];
			$LEVEL_GROUP = $arr_content[$data_count][level_group];
			$PM_CODE = $arr_content[$data_count][pm_code];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PL_CODE = $arr_content[$data_count][pl_code];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$PV_CODE_ORG = $arr_content[$data_count][pv_code_org];
			$PV_NAME_ORG = $arr_content[$data_count][pv_name_org];
			$SKILL_CODE = $arr_content[$data_count][skill_code];
			$SKILL_NAME = $arr_content[$data_count][skill_name];
			$ORG_NAME = $arr_content[$data_count][org_name]; 		
			$ORG_NAME_1 = $arr_content[$data_count][org_name_1]; 	
			$ORG_NAME_2 = $arr_content[$data_count][org_name_2]; 	
			$CL_NAME = $arr_content[$data_count][cl_name];
			$OT_CODE = $arr_content[$data_count][ot_code];
			$OT_NAME = $arr_content[$data_count][ot_name];
			$PV_CODE = $arr_content[$data_count][pv_code];
			$PV_NAME = $arr_content[$data_count][pv_name];
			$RETIREDATE_Y = $arr_content[$data_count][retiredate_y];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$SAH_EFFECTIVEDATE = $arr_content[$data_count][sah_effectivedate];
			if ($SAH_EFFECTIVEDATE=="//") $SAH_EFFECTIVEDATE = "";
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];
			$POH_DOCNO = $arr_content[$data_count][poh_docno];
			$POH_DOCDATE = $arr_content[$data_count][poh_docdate];
			$POH_EFFECTIVEDATE = $arr_content[$data_count][poh_effectivedate];
			if ($POH_EFFECTIVEDATE=="//") $POH_EFFECTIVEDATE = "";
			$EL_CODE = $arr_content[$data_count][el_code];
			$EL_NAME = $arr_content[$data_count][el_name];
			$EN_NAME = $arr_content[$data_count][en_name];
			$EM_NAME = $arr_content[$data_count][em_name];
			$INS_NAME = $arr_content[$data_count][ins_name];
			$CT_CODE_EDU = $arr_content[$data_count][ct_code_edu];
			$CT_NAME_EDU = $arr_content[$data_count][ct_name_edu];
			$ST_CODE = $arr_content[$data_count][st_code];
			$ST_NAME = $arr_content[$data_count][st_name];
			$SCH_NAME = $arr_content[$data_count][sch_name];
			$MOV_CODE = $arr_content[$data_count][mov_code];
			$MOV_NAME = $arr_content[$data_count][mov_name];
			$EFFECTIVEDATE = "";
			if (substr($MOV_CODE,0,1)=="1") $EFFECTIVEDATE = $POH_EFFECTIVEDATE;
			elseif (substr($MOV_CODE,0,1)=="2") $EFFECTIVEDATE = $SAH_EFFECTIVEDATE;
			
			if($REPORT_ORDER == "ORG_REF"){
				if($data_count > 0) $count_org_ref++;

				$ORG_ID_REF = $arr_content[$data_count][org_id_ref];
				$ORG_NAME_REF = $arr_content[$data_count][org_name_ref];

				$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":""));
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
				
				//====================== SET FORMAT ======================//
				require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
				//====================== SET FORMAT ======================//

				$arr_title = explode("||", "$ORG_NAME_REF||$report_title");
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
					$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
					$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 16, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 17, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 18, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 19, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 20, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 21, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 22, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 23, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 24, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 25, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 26, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 27, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 28, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 29, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 30, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 31, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 32, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 33, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 34, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 35, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 36, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 37, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 38, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 39, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 40, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 41, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 42, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				} // end if
		
				print_header();
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$ORG_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$POS_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$PL_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$LEVEL_GROUP", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$PT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "$LEVEL_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "$PM_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14, "$SKILL_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15, "$SKILL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 16, "$PV_CODE_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 17, "$PV_NAME_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 18, "$OT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 19, "$POS_STATUS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 20, "$PN_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 21, "$PN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 22, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 23, "$PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 24, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 25, "$PER_GENDER", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 26, "$PER_GENDER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 27, "$BIRTHDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 28, "$STARTDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 29, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 30, "$PER_MGTSALARY", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 31, "$EL_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 32, "$EL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 33, "$EN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 34, "$EM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 35, "$INS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 36, "$CT_CODE_EDU", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 37, "$CT_NAME_EDU", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 38, "$ST_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 39, "$ST_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 40, "$MOV_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 41, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 42, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

			} // end if
		} 	// end for				
	}else{	// if($count_data)
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

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
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"gpis.xls\"");
	header("Content-Disposition: inline; filename=\"gpis.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);

?>