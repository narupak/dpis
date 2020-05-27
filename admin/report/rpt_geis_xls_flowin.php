<?
	$time1 = time();
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
        sort($arr_rpt_order);
	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
                        /* เพิ่มใหม่ http://dpis.ocsc.go.th/Service/node/2098*/
                        case "ORG" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_SEQ_NO, a.ORG_ID";

				//$heading_name .= " $ORG_TITLE";
				break;
			case "POSNO" :
				if($select_list) $select_list .= ", ";

				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") {
					$select_list .= "IIf(IsNull(a.POEMS_NO), 0 , CLng(a.POEMS_NO)) as POEMS_NO";				
					$order_by .= "IIf(IsNull(a.POEMS_NO), 0 , CLng(a.POEMS_NO))";
				}
				if($DPISDB=="oci8") {
					$select_list .= "a.POEMS_NO";
					$order_by .= "to_number(replace(a.POEMS_NO,'-',''))";
				}elseif($DPISDB=="mysql"){
					$select_list .= "a.POEMS_NO";				
					$order_by .= "a.POEMS_NO";
				}

				$heading_name .= " เลขที่ตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.POEMS_NO";
	if(!trim($select_list)) $select_list = "a.POEMS_NO";

	$search_condition = "";
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
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "ระบบสารสนเทศพนักงานราชการ";
	$report_code = "Flow In";

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 30);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 15);
		$worksheet->set_column(7, 7, 20);
		$worksheet->set_column(8, 8, 20);
		$worksheet->set_column(9, 9, 12);
		$worksheet->set_column(10, 10, 20);
		$worksheet->set_column(11, 11, 20);
		$worksheet->set_column(12, 12, 12);
		$worksheet->set_column(13, 13, 10);
		$worksheet->set_column(14, 14, 12);
		$worksheet->set_column(15, 15, 12);
		$worksheet->set_column(16, 16, 25);
		$worksheet->set_column(17, 17, 25);
		$worksheet->set_column(18, 18, 25);
		$worksheet->set_column(19, 19, 25);
		$worksheet->set_column(20, 20, 20);
		$worksheet->set_column(21, 21, 25);
		$worksheet->set_column(22, 22, 20);
		$worksheet->set_column(23, 23, 12);
		$worksheet->set_column(24, 24, 15);
		$worksheet->set_column(25, 25, 15);
		$worksheet->set_column(26, 26, 15);
		$worksheet->set_column(27, 27, 15);
		$worksheet->set_column(28, 28, 12);
		$worksheet->set_column(29, 29, 20);
		$worksheet->set_column(30, 30, 20);
		$worksheet->set_column(31, 31, 15);
		$worksheet->set_column(32, 32, 20);
		$worksheet->set_column(33, 33, 25);
		$worksheet->set_column(34, 34, 15);
		$worksheet->set_column(35, 35, 15);
		$worksheet->set_column(36, 36, 20);
		$worksheet->set_column(37, 37, 20);
		$worksheet->set_column(38, 38, 20);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "tempMinistry", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "Organize", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "DivisionName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "tempPositionNo", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "tempLine", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "tempPositionType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "tempOrganizeType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "tempProvince", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "tempPositionStatus", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 9, "tempPrename", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 10, "tempFirstName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 11, "tempLastName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "tempCardNo", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 13, "tempGender", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 14, "tempBirthDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 15, "tempStartDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 16, "tempEducationLevel", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 17, "tempSkill", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 18, "tempEducationName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 19, "tempGraduated", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 20, "tempEducationCountry", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 21, "tempMovementType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 22, "tempOutline", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 23, "tempsouth", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 24, "tempRewardType1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 25, "tempRewardType2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 26, "tempRewardType3", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 27, "tempContactCnt", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 28, "tempMission", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 29, "tempCurrentContactStart", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 30, "tempCurrentContactEnd", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 31, "tempGuilty", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 32, "tempPunish", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 33, "tempDecoration", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 34, "tempPercentSalary", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 35, "tempScoreKPI", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 36, "tempScoreCompetence", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 37, "tempResign", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 38, "tempStatusDisability", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $POEMS_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					if($POEMS_NO) $arr_addition_condition[] = "(trim(a.POEMS_NO) = '$POEMS_NO')";
					else $arr_addition_condition[] = "(trim(a.POEMS_NO) = '$POEMS_NO' or a.POEMS_NO is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $POEMS_NO;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					$POEMS_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function


	// ===== select data =====
	if($DPISDB=="odbc"){
		$cmd = " select		b.ORG_ID_REF, $select_list, a.POEMS_ID, b.ORG_NAME, c.EP_NAME, 	f.PV_NAME, 
											g.OT_NAME, b.ORG_CODE, a.EP_CODE, b.PV_CODE, b.OT_CODE, a.ORG_ID, 
											a.ORG_ID_1, a.ORG_ID_2, PPT_CODE, PEF_CODE, PPS_CODE, POEMS_SKILL, 
											POEMS_SOUTH, PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, 
											d.LEVEL_NO, PER_SALARY, PER_MGTSALARY, PER_OFFNO, PER_CARDNO, 
											PN_CODE, PER_NAME, PER_SURNAME, MOV_CODE, PER_OCCUPYDATE, 
											PER_POSDATE, PER_RETIREDATE, PER_CONTACT_COUNT, PER_DISABILITY  
				 from	(
								(
										(
											(
												PER_POS_EMPSER a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_EMPSER_POS_NAME c on (a.EP_CODE=c.EP_CODE)
										) inner join PER_PERSONAL d on (a.POEMS_ID=d.POEMS_ID and PER_TYPE=3)
								) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
							) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
				where a.DEPARTMENT_ID = $DEPARTMENT_ID and PER_TYPE=3 and PER_OCCUPYDATE >= '$budget_year_from' and PER_OCCUPYDATE <= '$budget_year_to'
				order by		b.ORG_ID_REF, $order_by ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		b.ORG_ID_REF, $select_list, a.POEMS_ID, b.ORG_NAME, c.EP_NAME, 	f.PV_NAME, 
											g.OT_NAME, b.ORG_CODE, a.EP_CODE, b.PV_CODE, b.OT_CODE, a.ORG_ID, 
											a.ORG_ID_1, a.ORG_ID_2, PPT_CODE, PEF_CODE, PPS_CODE, POEMS_SKILL, 
											POEMS_SOUTH, PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, 
											d.LEVEL_NO, PER_SALARY, PER_MGTSALARY, PER_OFFNO, PER_CARDNO, 
											PN_CODE, PER_NAME, PER_SURNAME, MOV_CODE, PER_OCCUPYDATE, 
											PER_POSDATE, PER_RETIREDATE, PER_CONTACT_COUNT, PER_DISABILITY  
				from			PER_POS_EMPSER a, PER_ORG b, PER_EMPSER_POS_NAME c, PER_PERSONAL d, PER_PROVINCE f, PER_ORG_TYPE g
				where		a.DEPARTMENT_ID = $DEPARTMENT_ID and a.ORG_ID=b.ORG_ID and a.EP_CODE=c.EP_CODE and a.POEMS_ID=d.POEMS_ID and PER_TYPE=3 and
									b.PV_CODE=f.PV_CODE(+) and b.OT_CODE=g.OT_CODE(+) and PER_TYPE=3 and 
									PER_OCCUPYDATE >= '$budget_year_from' and PER_OCCUPYDATE <= '$budget_year_to'
				order by		b.ORG_ID_REF, $order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		b.ORG_ID_REF, $select_list, a.POEMS_ID, b.ORG_NAME, c.EP_NAME, 	f.PV_NAME, 
											g.OT_NAME, b.ORG_CODE, a.EP_CODE, b.PV_CODE, b.OT_CODE, a.ORG_ID, 
											a.ORG_ID_1, a.ORG_ID_2, PPT_CODE, PEF_CODE, PPS_CODE, POEMS_SKILL, 
											POEMS_SOUTH, PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, 
											d.LEVEL_NO, PER_SALARY, PER_MGTSALARY, PER_OFFNO, PER_CARDNO, 
											PN_CODE, PER_NAME, PER_SURNAME, MOV_CODE, PER_OCCUPYDATE, 
											PER_POSDATE, PER_RETIREDATE, PER_CONTACT_COUNT, PER_DISABILITY  
				 from	(
								(
										(
											(
												PER_POS_EMPSER a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_EMPSER_POS_NAME c on (a.EP_CODE=c.EP_CODE)
										) inner join PER_PERSONAL d on (a.POEMS_ID=d.POEMS_ID and PER_TYPE=3)
								) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
							) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
				where a.DEPARTMENT_ID = $DEPARTMENT_ID and PER_TYPE=3 and PER_OCCUPYDATE >= '$budget_year_from' and PER_OCCUPYDATE <= '$budget_year_to'
				order by		b.ORG_ID_REF, $order_by ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "<pre>$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";
	
	$arr_file = (array) null;
	$f_new = false;
	$fname= "../../Excel/tmp/rpt_geis_xls";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnumtext="";
	$workbook = new writeexcel_workbook($fname1);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	while($data = $db_dpis->get_array()){
		// เช็คจบที่ข้อมูล $data_limit
		if ($data_count > 0 && ($data_count % $file_limit) == 0) {
//			echo "$data_count>>$xls_fname>>$fname1<br>";
			$workbook->close();
			$arr_file[] = $fname1;

			$fnum++;
			$fname1=$fname."_$fnum.xls";
			$workbook = new writeexcel_workbook($fname1);

			//====================== SET FORMAT ======================//
			require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
			//====================== SET FORMAT ======================//

			$f_new = true;
		};
		// เช็คจบที่ข้อมูล $data_limit
		if($ORG_ID_REF != $data[ORG_ID_REF] || ($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
			if ($f_new) {
				$sheet_no=0; $sheet_no_text="";
				if($data_count > 0) $count_org_ref++;
				$f_new = false;
			} else if ($ORG_ID_REF != $data[ORG_ID_REF]) {
				$ORG_ID_REF = $data[ORG_ID_REF];
				$sheet_no=0; $sheet_no_text="";
				if($data_count > 0) $count_org_ref++;
			} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
				$sheet_no++;
				$sheet_no_text = "_$sheet_no";
			}
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];

			$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text);
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);
				
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
			} // end if
		
			print_header();
			
//			$REPORT_ORDER = "ORG_REF";
		} // end if
		
//		include("rpt_geis_xls_dtl.php");
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
//			echo($rpt_order_index."<BR>");
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :
			//		echo("POEMS_NO = ".$POEMS_NO."/data[POEMS_NO] = ".$data[POEMS_NO]."<BR>");
						$POEMS_NO = trim($data[POEMS_NO]);

						$addition_condition = generate_condition($rpt_order_index);
				//		echo("arr_rpt_order count = ".count($arr_rpt_order)."<BR>");
				//		echo("rpt_order_index = ".$rpt_order_index."/arr_rpt_order = ".(count($arr_rpt_order)-1)."<BR>");
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						if($rpt_order_index == (count($arr_rpt_order) - 1)){	
							$data_row++;
							$POEMS_ID = $data[POEMS_ID];
							$EP_CODE = trim($data[EP_CODE]);
							$EP_NAME = trim($data[EP_NAME]);	
							$PV_CODE = trim($data[PV_CODE]);
							$PV_NAME = trim($data[PV_NAME]);
							$OT_CODE = trim($data[OT_CODE]);
							$OT_NAME = trim($data[OT_NAME]);
							$PPT_CODE = trim($data[PPT_CODE]);
							$PEF_CODE = trim($data[PEF_CODE]);
							$PPS_CODE = trim($data[PPS_CODE]);
							$POEMS_SKILL = trim($data[POEMS_SKILL]);
							$POEMS_SOUTH = trim($data[POEMS_SOUTH]);
							$ORG_ID_2 = trim($data[ORG_ID]);
							$ORG_NAME_2 = trim($data[ORG_NAME]);		

							unset($tmp_ORG_ID, $ORG_ID_search, $ORG_ID_3, $ORG_ID_4, $ORG_NAME_3, $ORG_NAME_4);
							$ORG_ID_3 = $tmp_ORG_ID[] = trim($data[ORG_ID_1]);
							$ORG_ID_4 = $tmp_ORG_ID[] = trim($data[ORG_ID_2]);
							if($ORG_ID_3 && $ORG_ID_4){
								$ORG_ID_search = implode(", ", $tmp_ORG_ID);
								$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_search) ";
								if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
								while ( $data2 = $db_dpis2->get_array() ) {
									$ORG_NAME_3 = ($ORG_ID_3 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_3";
									$ORG_NAME_4 = ($ORG_ID_4 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_4";
								}	// while
							}
							$cmd = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1
									from PER_ORG a, PER_ORG b, PER_ORG c
									where a.ORG_ID=$ORG_ID_2 and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
							if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array(); 
							$ORG_NAME = trim($data2[ORGNAME1]);
							$ORG_NAME_1 = trim($data2[ORGNAME2]);
				
							$PER_GENDER_NAME = $BIRTHDATE = $STARTDATE = $POH_EFFECTIVEDATE = $POH_ENDDATE = "";
							$PER_ID = $data[PER_ID];
							$PER_GENDER = $data[PER_GENDER];
							$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
							$BIRTHDATE = show_date_format($PER_BIRTHDATE,1);
							$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
							if($PER_BIRTHDATE){
								$arr_temp = explode("-", $PER_BIRTHDATE);
								$BIRTHDATE_D = $arr_temp[2];
								$BIRTHDATE_M = $arr_temp[1];
								$BIRTHDATE_Y = $arr_temp[0] + 543;
								$RETIREDATE_Y = ("$BIRTHDATE_M-$BIRTHDATE_D" >= "10-01")?($arr_temp[0] + 543 + 60):($arr_temp[0] + 543 + 61);
							} // end if
							$STARTDATE = show_date_format($data[PER_STARTDATE],1);
							$LEVEL_NO = trim($data[LEVEL_NO]);
							$PER_SALARY = $data[PER_SALARY];
							$PER_MGTSALARY = $data[PER_MGTSALARY];
							$PER_OFFNO = $data[PER_OFFNO];
							$PER_CARDNO = $data[PER_CARDNO];
							$PN_CODE = $data[PN_CODE];
							$PER_NAME = $data[PER_NAME];
							$PER_SURNAME = $data[PER_SURNAME];
							$PER_CONTACT_COUNT = $data[PER_CONTACT_COUNT];
							$PER_DISABILITY = $data[PER_DISABILITY];
							if ($PER_DISABILITY==1) $PER_DISABILITY = "ปกติ";
							elseif ($PER_DISABILITY >= 2) $PER_DISABILITY = "พิการ";
					
							$MOV_CODE = trim($data2[MOV_CODE]);
							$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE'";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$MOV_NAME = ($data3[MOV_NAME]);
					
							$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PN_NAME = $data2[PN_NAME];

							$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$LEVEL_NAME = $data2[LEVEL_NAME];

							$cmd = " select PPS_NAME from PER_POS_STATUS where PPS_CODE='$PPS_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PPS_NAME = $data2[PPS_NAME];

							$cmd = " select PPT_NAME from PER_PRACTICE where PPT_CODE='$PPT_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PPT_NAME = $data2[PPT_NAME];

							$cmd = " select PEF_NAME from PER_POS_EMPSER_FRAME where PEF_CODE='$PEF_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PEF_NAME = $data2[PEF_NAME];

							$EL_CODE = $EL_NAME = $EN_NAME = $EM_NAME = $INS_CODE = $INS_NAME = $ST_CODE = $ST_NAME = $CT_CODE_EDU = $CT_NAME_EDU = "";
							if($PER_ID){
								if($DPISDB=="odbc"){
									// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
									$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
													from 	((
																PER_EDUCATE a
																	inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
															) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
													where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' ";									
								}elseif($DPISDB=="oci8"){
									// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
									$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU  
													from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
													where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' and 
																a.EN_CODE=b.EN_CODE and b.EL_CODE=c.EL_CODE and 
																a.EM_CODE=d.EM_CODE(+) ";	
								}elseif($DPISDB=="mysql"){
									// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
									$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
													from 	((
																PER_EDUCATE a
																inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
															) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
														) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
													where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' ";	
								} // end if
								$count_educate = $db_dpis2->send_cmd($cmd);

								if(!$count_educate){
									if($DPISDB=="odbc"){
										// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
										$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
														from 	((
																	PER_EDUCATE a
																	inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
															) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
														where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' ";
									}elseif($DPISDB=="oci8"){
										// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
										$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU  
														from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
														where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and 
																	a.EN_CODE=b.EN_CODE and b.EL_CODE=c.EL_CODE and 
																	a.EM_CODE=d.EM_CODE(+) ";
									}elseif($DPISDB=="mysql"){
										// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
										$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
														from 	((
																	PER_EDUCATE a
																	inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
															) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
														where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' ";
									} // end if
									$count_educate = $db_dpis2->send_cmd($cmd);
							}

							if(!$count_educate){
								if($DPISDB=="odbc"){
									// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
									$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
													from 	((
																PER_EDUCATE a
																	inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
															) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
													where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' ";
								}elseif($DPISDB=="oci8"){
									// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
									$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU  
													from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
													where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' and 
																a.EN_CODE=b.EN_CODE and b.EL_CODE=c.EL_CODE and 
																a.EM_CODE=d.EM_CODE(+) ";
								}elseif($DPISDB=="mysql"){
									// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
									$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
													from 	((
																PER_EDUCATE a
																	inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
															) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
													where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' ";
								} // end if
								$count_educate = $db_dpis2->send_cmd($cmd);
							}

							$data2 = $db_dpis2->get_array();
							$EL_CODE = trim($data2[EL_CODE]);
							$EL_NAME = trim($data2[EL_NAME]);
							$EN_NAME = trim($data2[EN_NAME]);
							$EM_NAME = trim($data2[EM_NAME]);
							$INS_CODE = trim($data2[INS_CODE]);
							$ST_CODE = trim($data2[ST_CODE]);
							$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
						
							if ($INS_CODE) {
								$cmd = " select INS_NAME from PER_INSTITUTE where INS_CODE='$INS_CODE' ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$INS_NAME = trim($data2[INS_NAME]);
							} else {	
								$INS_NAME = trim($data2[EDU_INSTITUTE]);
							} // end if
						
							$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$CT_NAME_EDU = $data2[CT_NAME];

							// === หาวันที่เริ่มสัญญาปัจจุบัน, วันที่สิ้นสุดสัญญาปัจจุบัน
                                                        //  '23030 = ทำสัญญาจ้างครั้งแรก, 23040 = ต่อสัญญา http://dpis.ocsc.go.th/Service/node/2951
							$POH_EFFECTIVEDATE = $POH_ENDDATE = "";
							$cmd = " select POH_EFFECTIVEDATE, POH_ENDDATE
											from   PER_POSITIONHIS
											where PER_ID=$PER_ID and  mov_Code in ('23030','23040') 
											order by POH_EFFECTIVEDATE desc ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
							$POH_ENDDATE = show_date_format($data2[POH_ENDDATE],1);

							// === หาความผิดวินัย, ประเภทโทษทางวินัย 
							$cmd = " select PUN_TYPE, PEN_CODE
											from   PER_PUNISHMENT 
											where PER_ID=$PER_ID  
											order by PUN_STARTDATE desc ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PUN_TYPE = trim($data2[PUN_TYPE]);
							$PEN_CODE = trim($data2[PEN_CODE]);
							$PEN_NAME = "";
							if ($PEN_CODE) {
								$cmd = " select PEN_NAME from PER_PENALTY where PEN_CODE='$PEN_CODE' ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$PEN_NAME = trim($data2[PEN_NAME]);
							} // end if
						
							// === หาเครื่องราชอิสริยาภรณ์สูงสุดที่ได้รับ 
							$DC_NAME = "";
							$cmd = " select DC_NAME
											from   PER_DECORATEHIS a, PER_DECORATION b
											where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
											order by DC_TYPE, DC_ORDER ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$DC_NAME = trim($data2[DC_NAME]);
						
							// === หาร้อยละที่ได้รับการเลื่อนเงินเดือน 
							$cmd = " select SAH_PERCENT_UP
											from   PER_SALARYHIS
											where PER_ID=$PER_ID and SAH_KF_YEAR = '$search_budget_year'
											order by SAH_EFFECTIVEDATE desc ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$SAH_PERCENT_UP = trim($data2[SAH_PERCENT_UP]);
							if ($SAH_PERCENT_UP==0) $SAH_PERCENT_UP = "";
						
							$cmd = " select SUM_KPI, SUM_COMPETENCE from PER_KPI_FORM 
										   where PER_ID = $PER_ID and KF_START_DATE >= '$budget_year_from'  and  KF_END_DATE <= '$budget_year_to'  ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$SUM_KPI = $data2[SUM_KPI];
							$SUM_COMPETENCE = $data2[SUM_COMPETENCE];

						} // end if

						$REPORT_ORDER = "CONTENT";
						$ORDER = $data_row;
//						$PER_CARDNO = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
//						$PN_NAME = $PN_NAME;
//						$PER_NAME = $PER_NAME;
//						$PER_SURNAME = $PER_SURNAME;
						if ($PER_GENDER== 1)	$PER_GENDER_NAME = "ชาย";
						elseif ($PER_GENDER== 2)	$PER_GENDER_NAME = "หญิง";	
						if ($BIRTHDATE=="//") $BIRTHDATE = "";
						if ($STARTDATE=="//") $STARTDATE = "";
//						$POEMS_NO = $POEMS_NO;
//						$EP_NAME = $EP_NAME;
//						$OT_NAME = $OT_NAME;
//						$POEMS_SKILL = $POEMS_SKILL;
//						$PV_NAME = $PV_NAME;
//						$POEMS_SOUTH = $POEMS_SOUTH;
						if ($POEMS_SOUTH==1) $POEMS_SOUTH = "ใช่";
						else $POEMS_SOUTH = "ไม่ใช่";
//						$ORG_NAME = $ORG_NAME; 		
//						$ORG_NAME_1 = $ORG_NAME_1; 	
//						$ORG_NAME_2 = $ORG_NAME_2; 
//						$ORG_NAME_3;= $ORG_NAME_3;	
//						$ORG_NAME_4;= $ORG_NAME_4;	
//						$LEVEL_NAME = $LEVEL_NAME;
						$PER_SALARY = (($PER_SALARY)?number_format($PER_SALARY,0,'',','):"");
						$PER_MGTSALARY = (($PER_MGTSALARY)?number_format($PER_MGTSALARY,0,'',','):"");
//						$POH_EFFECTIVEDATE = $POH_EFFECTIVEDATE;
						if ($POH_EFFECTIVEDATE=="//") $POH_EFFECTIVEDATE = "";
						$POH_ENDDATE = $POH_ENDDATE;
						if ($POH_ENDDATE=="//") $POH_ENDDATE = "";
//						$EL_NAME = $EL_NAME;
//						$EN_NAME = $EN_NAME;
//						$EM_NAME = $EM_NAME;
//						$INS_NAME = $INS_NAME;
//						$CT_NAME_EDU = $CT_NAME_EDU;
//						$MOV_NAME = $MOV_NAME;
//						$PPT_NAME = $PPT_NAME;
//						$PEF_NAME = $PEF_NAME;
//						$PPS_NAME = $PPS_NAME;
//						$PER_CONTACT_COUNT = $PER_CONTACT_COUNT;
						if ($PUN_TYPE==1) $PUN_TYPE = "ความผิดวินัยอย่างร้ายแรง";
						elseif ($PUN_TYPE==2) $PUN_TYPE = "ความผิดวินัยไม่ร้ายแรง";
						elseif ($PUN_TYPE==3) $PUN_TYPE = "ไม่เป็นความผิดทางวินัย";
//						$PEN_NAME = $PEN_NAME; 
//						$DC_NAME = $DC_NAME; 
//						$SAH_PERCENT_UP = $SAH_PERCENT_UP;
//						$SUM_KPI = $SUM_KPI;
//						$SUM_COMPETENCE = $SUM_COMPETENCE;
//						$PER_DISABILITY = $PER_DISABILITY;
						if ($PER_STATUS==2) $RESIGN_NAME = $MOV_NAME;

						$xlsRow++;
						$worksheet->write_string($xlsRow, 0, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 1, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 2, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 3, "$POEMS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 4, "$EP_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 5, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 6, "$OT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 7, "$PV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 8, "$PPS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 9, "$PN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 10, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 11, "$PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));		
						$worksheet->write_string($xlsRow, 12, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 13, "$PER_GENDER_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 14, "$BIRTHDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 15, "$STARTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 16, "$EL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 17, "$POEMS_SKILL", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 18, "$EN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 19, "$INS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 20, "$CT_NAME_EDU", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 21, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 22, "$PEF_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 23, "$POEMS_SOUTH", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 24, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
						$worksheet->write_string($xlsRow, 25, "$PER_MGTSALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));	
						$worksheet->write_string($xlsRow, 26, "$PER_COST_OF_LIVE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));	
						$worksheet->write_string($xlsRow, 27, "$PER_CONTACT_COUNT", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 28, "$PPT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 29, "$POH_EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 30, "$POH_ENDDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 31, "$PUN_TYPE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 32, "$PEN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 33, "$DC_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 34, "$SAH_PERCENT_UP", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 35, "$SUM_KPI", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 36, "$SUM_COMPETENCE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 37, "$RESIGN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 38, "$PER_DISABILITY", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

						$data_count++;				
						//echo("data_count = ".$data_count."<BR>");								
				} // end if
				break;
			} // end switch case
		} // end for
		// end include		
	} // end while

	if($count_data==0){
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
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
	} // end if

	$workbook->close();
	$arr_file[] = $fname1;
?>
<html>
<head>
<title><?=$webpage_title?> - <?=$MENU_TITLE_LV0?><?if($MENU_ID_LV1){?> - <?=$MENU_TITLE_LV1?><?}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-874">
<link rel="stylesheet" href="<?="../$cssfileselected";?>" type="text/css">
<link rel="alternate stylesheet" type="text/css" media="all" href="../stylesheets/calendar-blue.css" title="winter"/>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="label_normal">
    <tr> 
	  <td align="left" valign="top">
<!--// copy from current_location.html -->
		    <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="28"><table border="0" cellspacing="0" cellpadding="0" class="header_current_location_table">
                  <? if(!$HIDE_HEADER){ ?>
		    <tr>
                    <td width="10" height="">&nbsp;</td>
                    <td class="header_current_location">&reg;&nbsp;<?=$MENU_TITLE_LV0?><? if($MENU_ID_LV1>0){ ?> &gt; <?=$MENU_TITLE_LV1?><? } ?><? if($MENU_ID_LV2>0){ ?> &gt; <?=$MENU_TITLE_LV2?><? } ?><? if($MENU_ID_LV3>0){ ?> &gt; <?=$MENU_TITLE_LV3?><? } ?><?=$OPTIONAL_TITLE?> </td>
					<td width="40" class="header_current_location_right">&nbsp;</td>
					<td align="right" valign="middle" style="background:#FFF;" nowrap>&nbsp;
                    <?
             			// หา จำนวน user ที่ยังไม่มีการ logout
						$cmd = " select distinct user_id, from_ip from user_last_access where f_logout != '1' or f_logout is null ";
						$cnt = $db->send_cmd($cmd);
                        echo "<font size=\"+1\" color=\"#0000FF\"><B>$cnt</B>&nbsp;<img src=\"../images/man_small.gif\" height=\"18\" width=\"20\">&nbsp;online</font>";
                    ?>
                    </td>
                  </tr>
		    <? }else{ ?>
		    <tr>
                    <td width="10" height="28">&nbsp;</td>
                    <td class="header_current_location">&reg;&nbsp;<?=$OPTIONAL_TITLE?> </td>
					<td width="40" class="header_current_location_right">&nbsp;</td>
					<td align="right">&nbsp;</td>
                  </tr>
		    <? } // end if ?>
                </table></td>
              </tr>
            </table>
<!--// end copy from current_location.html -->
	  </td>	
	</tr>
<tr><td>
   	<div style=" margin-top:5px; margin-bottom:5px; width:100%"><table style="border: 1px solid #666666;" width="100%">
<?
	ini_set("max_execution_time", 30);

	if (count($arr_file) > 0) {
		echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
		echo "<tr><td width='3%'>&nbsp;</td><td>แฟ้ม Excel ที่สร้าง จำนวน ".count($arr_file)." แฟ้ม</td></tr>";
		echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
		$grp = "";
		echo "<tr><td width='3%'>&nbsp;</td><td>";
		echo "<table width='95%' class='table_body'>";
		$num=0;
		for($i_file = 0; $i_file < count($arr_file); $i_file++) {
			$grp="3";
			echo "<tr><td width='18%'>data พนักงานราชการ Flow In</td>";
			$num=1;
			echo "<td><font size='-1' color='#CC7733'><B>".$num."</B></font> : <a href=\"".$arr_file[$i_file]."\">".$arr_file[$i_file]."</a></td></tr>";
			$num++;
		}
		echo "</table>";
		echo "</td></tr>";
	}
	echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	$time2 = time();
	$tdiff = $time2 - $time1;
	$s = $tdiff % 60;	// วินาที
	$m = floor($tdiff / 60);	// นาที
	$h = floor($m / 60);	// ชม.
	$m = $m % 60;	// นาที
	$show_lap = ($h?"$h ชม. ":"").($m?"$m นาที ":"")."$s วินาที";
	echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	echo "<tr><td width='3%'>&nbsp;</td><td>เริ่ม : <font color='#FF0000'>".date("d-m-Y h:i:s",$time1)."</font><font color='#000000'> จบ : </font><font color='#FF0000'>".date("d-m-Y h:i:s",$time2)."</font><font color='#000000'> ใช้เวลา </font><font color='#FF0000'>$show_lap</font> <font color='#333333'>[</font><font color='#000000'>$tdiff วินาที</font><font color='#333333'>]</font></td></tr>";
	echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	echo "<tr><td width='3%'>&nbsp;</td><td>จบรายงาน สำหรับพนักงานราชการ</td></tr>";
?>
	</table></div>
    </td></tr>
</table>
</body>
</html>
