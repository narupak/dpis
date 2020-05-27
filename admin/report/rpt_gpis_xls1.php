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

				$heading_name .= " เลขที่ตำแหน่ง";
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
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "รูปแบบมาตรฐานกลางของ File สำหรับนำเข้าระบบ ของสำนักงาน ก.พ.";
	$report_code = "data ข้าราชการ";

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 40);
		$worksheet->set_column(1, 1, 40);
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 40);
		$worksheet->set_column(5, 5, 15);
		$worksheet->set_column(6, 6, 40);
		$worksheet->set_column(7, 7, 40);
		$worksheet->set_column(8, 8, 20);
		$worksheet->set_column(9, 9, 15);
		$worksheet->set_column(10, 10, 20);
		$worksheet->set_column(11, 11, 20);
		$worksheet->set_column(12, 12, 20);
		$worksheet->set_column(13, 13, 25);
		$worksheet->set_column(14, 14, 25);
		$worksheet->set_column(15, 15, 20);
		$worksheet->set_column(16, 16, 5);
		$worksheet->set_column(17, 17, 15);
		$worksheet->set_column(18, 18, 15);
		$worksheet->set_column(19, 19, 15);
		$worksheet->set_column(20, 20, 15);
		$worksheet->set_column(21, 21, 20);
		$worksheet->set_column(22, 22, 40);
		$worksheet->set_column(23, 23, 40);
		$worksheet->set_column(24, 24, 40);
		$worksheet->set_column(25, 25, 40);
		$worksheet->set_column(26, 26, 40);
		$worksheet->set_column(27, 27, 40);
		$worksheet->set_column(28, 28, 20);
		$worksheet->set_column(29, 29, 25);
		$worksheet->set_column(30, 30, 15);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "tempMinistry", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "Organzie", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "DivisionName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "tempPositionNo", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "tempLine", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "tempPositionType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "tempLevel", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "tempManagePosition", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "tempSkill", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "tempOrganizeType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "tempProvince", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 11, "tempPositionStatus", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "tempPrename", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 13, "tempFirstName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 14, "tempLastName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 15, "tempCardNo", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 16, "tempGender", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 17, "tempBirthDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 18, "tempStartDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 19, "tempSalary", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 20, "tempPositionSalary", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 21, "tempEducationLevel", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 22, "tempEducationName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 23, "tempEducationMajor", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 24, "tempGraduated", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 25, "tempEducationCountry", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 26, "tempScholarType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 27, "tempMovementType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 28, "tempMovementDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 29, "tempClName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 30, "tempFlowDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
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
		global $POS_NO, $SESS_ORG_STRUCTURE;
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
		$cmd = " select		
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, a.PT_CODE, 
							a.CL_NAME, f.PV_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, 
							b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
				 from		(
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
										) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
									) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
								) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
							$search_condition
				 order by		b.ORG_ID_REF, $order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " select		
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, a.PT_CODE, 
							a.CL_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, f.PV_NAME, 
							b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
				from			PER_POSITION a, PER_ORG b, PER_LINE c, PER_MGT d, PER_PROVINCE f, PER_ORG_TYPE g
				where		a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE and a.PM_CODE=d.PM_CODE(+) and 
							b.PV_CODE=f.PV_CODE(+) and b.OT_CODE=g.OT_CODE(+)	
							$search_condition
				order by		b.ORG_ID_REF, $order_by   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, a.PT_CODE, 
							a.CL_NAME, f.PV_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, 
							b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
				 from		(
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
										) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
									) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
								) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
							$search_condition
				 order by		b.ORG_ID_REF, $order_by ";
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
			$ORG_NAME_REF = "";
			if($ORG_ID_REF){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_REF = $data2[ORG_NAME];
			}
			$arr_content[$data_count][type] = "ORG_REF";
			$arr_content[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_content[$data_count][org_name_ref] = $ORG_NAME_REF;
			
			$data_count++;
		} // end if
		
		$PER_TYPE = 1;
		$GPIS_FLAG = "DATA";
		
//		include("rpt_gpis_xls_dtl.php");
//		ตัดแปะเข้ามาจาก rpt_gpis_xls_dtl.php
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :
					if($POS_NO != trim($data[POS_NO])){
						if ($PER_TYPE == 1) $POS_NO = trim($data[POS_NO]);
						elseif ($PER_TYPE == 2) $POEM_NO = trim($data[POEM_NO]);
						elseif ($PER_TYPE == 3) $POEMS_NO = trim($data[POEMS_NO]);

						$addition_condition = generate_condition($rpt_order_index);
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						if($rpt_order_index == (count($arr_rpt_order) - 1)){	
							$data_row++;
							$POS_ID = $data[POS_ID];
							$CL_NAME = trim($data[CL_NAME]);
							$PT_CODE = trim($data[PT_CODE]);
							$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PT_NAME = ($data3[PT_NAME]);
							
							$PM_CODE = trim($data[PM_CODE]);
							$PM_NAME = trim($data[PM_NAME]);					
							$PL_CODE = trim($data[PL_CODE]);
							$PL_NAME = trim($data[PL_NAME]);	
							$PV_CODE = trim($data[PV_CODE]);
							$PV_NAME = trim($data[PV_NAME]);
							$OT_CODE = trim($data[OT_CODE]);
							$OT_NAME = trim($data[OT_NAME]);
							$SKILL_CODE = trim($data[SKILL_CODE]);
							$SKILL_NAME = trim($data[SKILL_NAME]);					
							
							$ORG_CODE = substr(trim($data[ORG_CODE]),0,5);	
							$ORG_ID_2 = trim($data[ORG_ID]);
							$ORG_NAME_2 = trim($data[ORG_NAME]);	
							// === หาจังหวัดและประเทศตามโครงสร้าง
							if ($DPISDB == "odbc") 
								$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
										from ( PER_ORG a 
											   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
											) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
										where ORG_ID=$ORG_ID_2 ";
							elseif ($DPISDB == "oci8") 
								$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
										from PER_ORG a, PER_PROVINCE b, PER_COUNTRY c 
										where ORG_ID=$ORG_ID_2 and a.PV_CODE=b.PV_CODE(+) and 
												a.CT_CODE=c.CT_CODE(+) ";
							elseif($DPISDB=="mysql")
								$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
										from ( PER_ORG a 
											   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
											) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
										where ORG_ID=$ORG_ID_2 ";
							if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PV_CODE_ORG = trim($data3[PV_CODE]);
							$PV_NAME_ORG = trim($data3[PV_NAME]);
							$CT_CODE_ORG = trim($data3[CT_CODE]);
							$CT_NAME_ORG = trim($data3[CT_NAME]);
												
							unset($tmp_ORG_ID, $ORG_ID_search, $ORG_ID_3, $ORG_ID_4, $ORG_NAME_3, $ORG_NAME_4);
							$ORG_ID_3 = $tmp_ORG_ID[] = trim($data[ORG_ID_1]);
							$ORG_ID_4 = $tmp_ORG_ID[] = trim($data[ORG_ID_2]);
							if($ORG_ID_3 && $ORG_ID_4) { 
								$ORG_ID_search = implode(",", $tmp_ORG_ID);
								$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_search) ";
								if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
								while ( $data2 = $db_dpis2->get_array() ) {
									$ORG_NAME_3 = ($ORG_ID_3 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_3";
									$ORG_NAME_4 = ($ORG_ID_4 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_4";
								}	// while
							}
							if ($DPISDB == "odbc") 
								$cmd = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1 
										from ( PER_ORG a 
											   left join PER_ORG b on (a.ORG_ID_REF=b.ORG_ID)
											) left join PER_ORG c  on (b.ORG_ID_REF=c.ORG_ID)
										where a.ORG_ID=$ORG_ID_2  and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
							elseif ($DPISDB == "oci8") 
								$cmd = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1
										from PER_ORG a, PER_ORG b, PER_ORG c
										where a.ORG_ID=$ORG_ID_2 and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
							elseif($DPISDB=="mysql")
								$cmd = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1 
										from ( PER_ORG a 
											   left join PER_ORG b on (a.ORG_ID_REF=b.ORG_ID)
											) left join PER_ORG c  on (b.ORG_ID_REF=c.ORG_ID)
										where a.ORG_ID=$ORG_ID_2  and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
							if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array(); 
							$ORG_NAME = trim($data2[ORGNAME1]);
							$ORG_NAME_1 = trim($data2[ORGNAME2]);
						
							$BIRTHDATE = "";
							if ($DEPARTMENT_NAME=="กรมการปกครอง") $where = "PAY_ID=$POS_ID ";
							else $where = "POS_ID=$POS_ID ";
							if ($GPIS_FLAG == "DATA") $where .= " and PER_STATUS=1";
							$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
											PER_SALARY, PER_MGTSALARY, PER_OFFNO, PER_CARDNO, PN_CODE, 
											PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, 
											ORG_ID, MOV_CODE, PER_OCCUPYDATE, PER_POSDATE, PER_RETIREDATE, 
											PER_UNION, PER_UNION2, PER_UNION3, PER_UNION4, PER_UNION5  
									from		PER_PERSONAL
									where	PER_TYPE=$PER_TYPE and $where  ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PER_ID = $data2[PER_ID];
							$PER_GENDER = $data2[PER_GENDER];
							$PER_BIRTHDATE = substr(trim($data2[PER_BIRTHDATE]), 0, 10);
							$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
							if($PER_BIRTHDATE){
								$arr_temp = explode("-", $PER_BIRTHDATE);
								$BIRTHDATE_D = $arr_temp[2];
								$BIRTHDATE_M = $arr_temp[1];
								$BIRTHDATE_Y = $arr_temp[0] + 543;
								$RETIREDATE_Y = ("$BIRTHDATE_M-$BIRTHDATE_D" >= "10-01")?($arr_temp[0] + 543 + 60):($arr_temp[0] + 543 + 61);
								$BIRTHDATE = show_date_format($PER_BIRTHDATE,1);
							} // end if
							$STARTDATE = show_date_format($data2[PER_STARTDATE],1);
							$LEVEL_NO = trim($data2[LEVEL_NO]);
							if (substr($LEVEL_NO,0,1)=='O')	$LEVEL_GROUP = 'ทั่วไป';
							elseif (substr($LEVEL_NO,0,1)=='K')	$LEVEL_GROUP = 'วิชาการ';
							elseif (substr($LEVEL_NO,0,1)=='D')	$LEVEL_GROUP = 'อำนวยการ';
							elseif (substr($LEVEL_NO,0,1)=='M') 	$LEVEL_GROUP = 'บริหาร';
							$PER_SALARY = $data2[PER_SALARY];
							$PER_MGTSALARY = $data2[PER_MGTSALARY];
							$PER_OFFNO = $data2[PER_OFFNO];
							$PER_CARDNO = $data2[PER_CARDNO];
							$PN_CODE = $data2[PN_CODE];
							$PER_NAME = $data2[PER_NAME];
							$PER_SURNAME = $data2[PER_SURNAME];
							$PER_ENG_NAME = $data2[PER_ENG_NAME];
							$PER_ENG_SURNAME = $data2[PER_ENG_SURNAME];
							$PER_UNION = $data2[PER_UNION];
							$PER_UNION2 = $data2[PER_UNION2];
							$PER_UNION3 = $data2[PER_UNION3];
							$PER_UNION4 = $data2[PER_UNION4];
							$PER_UNION5 = $data2[PER_UNION5];
							
							$ORG_ID_ASS = trim($data2[ORG_ID]);
							if ($ORG_ID_ASS) {
								// === หาจังหวัดและประเทศตามมอบหมายงาน
								if ($DPISDB == "odbc") 
									$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
											from ( PER_ORG a 
												   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
												) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
											where ORG_ID=$ORG_ID_ASS ";
								elseif ($DPISDB == "oci8") 
									$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
											from PER_ORG a, PER_PROVINCE b, PER_COUNTRY c 
											where ORG_ID=$ORG_ID_ASS and a.PV_CODE=b.PV_CODE(+) and 
													a.CT_CODE=c.CT_CODE(+) ";
								elseif($DPISDB=="mysql")
									$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
											from ( PER_ORG a 
												   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
												) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
											where ORG_ID=$ORG_ID_ASS ";
								if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis3->send_cmd($cmd);
								$data3 = $db_dpis3->get_array();
								$PV_CODE_ORG = trim($data3[PV_CODE]);
								$PV_NAME_ORG = trim($data3[PV_NAME]);
								$CT_CODE_ORG = trim($data3[CT_CODE]);
								$CT_NAME_ORG = trim($data3[CT_NAME]);
							} else {
								$PV_CODE_ORG_ASS = $PV_CODE_ORG;
								$PV_NAME_ORG_ASS = $PV_NAME_ORG;						
								$CT_CODE_ORG_ASS = $CT_CODE_ORG;	
								$CT_NAME_ORG_ASS = $CT_NAME_ORG;							
							}  // end if ($ORG_ID_ASS)
							
							$MOV_CODE = trim($data2[MOV_CODE]);
							$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$MOV_NAME = ($data3[MOV_NAME]);
							
							$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PN_NAME = $data2[PN_NAME];

							$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$LEVEL_NAME = $data2[LEVEL_NAME];

							$cmd = " select SKILL_NAME from PER_SKILL where SKILL_CODE='$SKILL_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$SKILL_NAME = $data2[SKILL_NAME];

							$EL_CODE = $EL_NAME = $EN_NAME = $EM_NAME = $INS_CODE = $INS_NAME = $ST_CODE = $ST_NAME = $CT_CODE_EDU = $CT_NAME_EDU = "";
							if($PER_ID){
								if($DPISDB=="odbc"){
									// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
									$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
											from 	((
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
													) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
											where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' ";									
								}elseif($DPISDB=="oci8"){
									// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
									$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
											from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
											where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' and 
													a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and 
													a.EM_CODE=d.EM_CODE(+) ";	
								}elseif($DPISDB=="mysql"){
									// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
									$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
											from 	((
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
													) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
											where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' ";	
								} // end if
								$count_educate = $db_dpis2->send_cmd($cmd);

								if(!$count_educate){
									if($DPISDB=="odbc"){
										// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
										$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
												from 	((
															PER_EDUCATE a
															left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
														) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
														) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
												where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' ";
									}elseif($DPISDB=="oci8"){
										// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
										$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
												from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
												where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and 
														a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and 
														a.EM_CODE=d.EM_CODE(+) ";
									}elseif($DPISDB=="mysql"){
										// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
										$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
												from 	((
															PER_EDUCATE a
															left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
														) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
														) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
												where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' ";
									} // end if
									$count_educate = $db_dpis2->send_cmd($cmd);
								}

								if(!$count_educate){
									if($DPISDB=="odbc"){
										// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
										$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
												from 	((
															PER_EDUCATE a
															left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
														) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
														) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
												where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' ";
									}elseif($DPISDB=="oci8"){
										// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
										$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
												from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
												where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' and 
														a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and 
														a.EM_CODE=d.EM_CODE(+) ";
									}elseif($DPISDB=="mysql"){
										// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
										$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
												from 	((
															PER_EDUCATE a
															left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
														) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
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
								
								if ($INS_CODE) {
									if($DPISDB=="odbc") {				
										// หาชื่อโรงเรียน และประเทศของโรงเรียน
										$cmd = " select INS_NAME, a.CT_CODE, CT_NAME 
												from   PER_INSTITUTE a 
													   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
												where INS_CODE='$INS_CODE' ";
									} elseif ($DPISDB=="oci8") { 
										// หาชื่อโรงเรียน และประเทศของโรงเรียน
										$cmd = " select INS_NAME, a.CT_CODE, CT_NAME from PER_INSTITUTE a, PER_COUNTRY b 
												where INS_CODE='$INS_CODE' and a.CT_CODE=b.CT_CODE(+) ";
									}elseif($DPISDB=="mysql"){
										// หาชื่อโรงเรียน และประเทศของโรงเรียน
										$cmd = " select INS_NAME, a.CT_CODE, CT_NAME 
												from   PER_INSTITUTE a 
													   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
												where INS_CODE='$INS_CODE' ";
									} 			
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$INS_NAME = trim($data2[INS_NAME]);
									$CT_CODE_EDU = trim($data2[CT_CODE]);
									$CT_NAME_EDU = trim($data2[CT_NAME]);
								} else {	
									$INS_NAME = trim($data2[EDU_INSTITUTE]);
								} // end if
								
								// === หาวันที่เงินเดือนมีผล 
								$SAH_EFFECTIVEDATE = "";
								$cmd = " select SAH_EFFECTIVEDATE
										from   PER_SALARYHIS
										where PER_ID=$PER_ID 
										order by SAH_EFFECTIVEDATE desc ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$SAH_EFFECTIVEDATE = show_date_format($data2[SAH_EFFECTIVEDATE],1);
								
								// === หาตำแหน่งล่าสุด เลขที่คำสั่ง, วันที่ออกคำสั่ง, วันที่มีผล
								$POH_DOCNO = $POH_DOCDATE = $POH_EFFECTIVEDATE = "";
								$cmd = " select POH_DOCNO, POH_DOCDATE, POH_EFFECTIVEDATE
										from   PER_POSITIONHIS
										where PER_ID=$PER_ID 
										order by POH_EFFECTIVEDATE desc ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$POH_DOCNO = trim($data2[POH_DOCNO]);
								$POH_DOCDATE = show_date_format($data2[POH_DOCDATE],1);
								$POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
								
								// === หาชื่อทุน และแหล่งทุน
								$SCH_NAME = $ST_NAME = "";
								if ($DPISDB == "odbc") 
									$cmd = " select SCH_NAME, ST_NAME 
											from ( PER_SCHOLAR a 
												   left join PER_SCHOLARSHIP b on (a.SCH_CODE=b.SCH_CODE)
												) left join PER_SCHOLARTYPE c  on (b.ST_CODE=c.ST_CODE)
											where PER_ID=$PER_ID ";
								elseif ($DPISDB == "oci8") 
									$cmd = " select SCH_NAME, ST_NAME
										from   PER_SCHOLAR a, PER_SCHOLARSHIP b, PER_SCHOLARTYPE c
										where PER_ID=$PER_ID and a.SCH_CODE=b.SCH_CODE and 
											   b.ST_CODE=c.ST_CODE
										order by a.SC_ID desc ";
								elseif($DPISDB=="mysql")
									$cmd = " select SCH_NAME, ST_NAME 
											from ( PER_SCHOLAR a 
												   left join PER_SCHOLARSHIP b on (a.SCH_CODE=b.SCH_CODE)
												) left join PER_SCHOLARTYPE c  on (b.ST_CODE=c.ST_CODE)
											where PER_ID=$PER_ID ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$SCH_NAME = trim($data2[SCH_NAME]);
								$ST_NAME = trim($data2[ST_NAME]);
							} // end if

							// === หารหัสสหภาพข้าราชการ
							$UNION_CODE = "22222";
							if ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "12222";
							elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "21222";
							elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "22122";
							elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "22212";
							elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5==1) $UNION_CODE = "22221";
							elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "11112";
							elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "11122";
							elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "11222";
							elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "11212";
							elseif ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "12122";
							elseif ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "12112";
							elseif ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "12212";
							elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "21112";
							elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "21122";
							elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "21212";
							elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "22112";

							$arr_content[$data_count][type] = "CONTENT";
							$arr_content[$data_count][order] = $data_row;
							$arr_content[$data_count][org_code] = $ORG_CODE;
							$arr_content[$data_count][per_offno] = $PER_OFFNO;
							$arr_content[$data_count][per_cardno] = $PER_CARDNO;
							$arr_content[$data_count][pn_code] = $PN_CODE;
							$arr_content[$data_count][pn_name] = $PN_NAME;
							$arr_content[$data_count][per_name] = $PER_NAME;
							$arr_content[$data_count][per_surname] = $PER_SURNAME;
							$arr_content[$data_count][per_eng_name] = $PER_ENG_NAME."  ".$PER_ENG_SURNAME;
							$arr_content[$data_count][per_gender] = $PER_GENDER;
							$arr_content[$data_count][birthdate] = 	$BIRTHDATE;	
							$arr_content[$data_count][startdate] = $STARTDATE;	
							$arr_content[$data_count][pos_no] = $POS_NO;
							$arr_content[$data_count][level_group] = $LEVEL_GROUP;
							$arr_content[$data_count][pm_code] = $PM_CODE;
							$arr_content[$data_count][pm_name] = $PM_NAME;
							$arr_content[$data_count][pl_code] = $PL_CODE;
							$arr_content[$data_count][pl_name] = $PL_NAME;
							$arr_content[$data_count][pt_name] = $PT_NAME;
							$arr_content[$data_count][ot_code] = $OT_CODE;
							$arr_content[$data_count][ot_name] = $OT_NAME;
							$arr_content[$data_count][cl_code] = $CL_CODE;
							$arr_content[$data_count][cl_name] = $CL_NAME;
							$arr_content[$data_count][skill_code] = $SKILL_CODE;
							$arr_content[$data_count][skill_name] = $SKILL_NAME;
							$arr_content[$data_count][pv_code_org] = $PV_CODE_ORG;
							$arr_content[$data_count][pv_name_org] = $PV_NAME_ORG;
							$arr_content[$data_count][ct_code_org] = $CT_CODE_ORG;
							$arr_content[$data_count][ct_name_org] = $CT_NAME_ORG;					
							$arr_content[$data_count][pv_code_org_ass] = $PV_CODE_ORG_ASS;
							$arr_content[$data_count][pv_name_org_ass] = $PV_NAME_ORG_ASS;
							$arr_content[$data_count][ct_code_org_ass] = $CT_CODE_ORG_ASS;
							$arr_content[$data_count][ct_name_org_ass] = $CT_NAME_ORG_ASS;
							$arr_content[$data_count][org_name] = $ORG_NAME;
							$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
							$arr_content[$data_count][org_name_2] = $ORG_NAME_2;
							$arr_content[$data_count][org_name_3] = $ORG_NAME_3;
							$arr_content[$data_count][org_name_4] = $ORG_NAME_4;
							$arr_content[$data_count][level_no] = level_no_format($LEVEL_NO);
							$arr_content[$data_count][level_name] = $LEVEL_NAME;
							$arr_content[$data_count][sah_effectivedate] = $SAH_EFFECTIVEDATE;
		//					$arr_content[$data_count][per_salary] = (($PER_SALARY)?number_format($PER_SALARY,0,'',','):"");
		//					$arr_content[$data_count][per_mgtsalary] = (($PER_MGTSALARY)?number_format($PER_MGTSALARY,0,'',','):"");
							$arr_content[$data_count][per_salary] = $PER_SALARY;
							$arr_content[$data_count][per_mgtsalary] = $PER_MGTSALARY;
							$arr_content[$data_count][poh_docno] = $POH_DOCNO;
							$arr_content[$data_count][poh_docdate] = $POH_DOCDATE;
							$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE;
							$arr_content[$data_count][el_code] = $EL_CODE;
							$arr_content[$data_count][el_name] = $EL_NAME;
							$arr_content[$data_count][en_name] = $EN_NAME;
							$arr_content[$data_count][em_name] = $EM_NAME;
							$arr_content[$data_count][ins_name] = $INS_NAME;
							$arr_content[$data_count][ct_code_edu] = $CT_CODE_EDU;
							$arr_content[$data_count][ct_name_edu] = $CT_NAME_EDU;
							$arr_content[$data_count][st_code] = $ST_CODE;
							$arr_content[$data_count][st_name] = $ST_NAME;
							$arr_content[$data_count][sch_name] = $SCH_NAME;
							$arr_content[$data_count][mov_code] = $MOV_CODE;
							$arr_content[$data_count][mov_name] = $UNION_CODE; // $MOV_NAME

							$data_count++;														
						} // end if
					} // end if
				break;
			} // end switch case
		} // end for
//		จบการ ตัดแปะเข้ามาจาก rpt_gpis_xls_dtl.php
		
	} // end while
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	// ===== condition $count_data  from "select data" =====
	
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	$file_limit = 1200;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";
	
	$arr_file = (array) null;
	$f_new = false;
	$fname= "../../Excel/tmp/rpt_gpis_xls";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnumtext="";
	$workbook = new writeexcel_workbook($fname1);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	$f_org_ref_skip = false;		// มีไว้เพื่อใช้เช็ค  $REPORT_ORDER == "ORG_REF"
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
			if ($arr_content[$data_count][per_gender] == 1)	$PER_GENDER_NAME = "ชาย";
			elseif ($arr_content[$data_count][per_gender] == 2)	$PER_GENDER_NAME = "หญิง";	
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			if ($BIRTHDATE=="//") $BIRTHDATE = "";
			$STARTDATE = $arr_content[$data_count][startdate];
			if ($STARTDATE=="//") $STARTDATE = "";
			if ($PER_NAME) $POS_STATUS = "มีคนถือครอง";
			else $POS_STATUS = "ตำแหน่งว่าง";
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
			if (!$LEVEL_NO) {
				$cmd = " select LEVEL_NO_MIN from PER_CO_LEVEL where CL_NAME='$CL_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$LEVEL_NO = $data2[LEVEL_NO_MIN];
			}

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
			
			// เช็คจบที่ข้อมูล $data_limit
			if ($data_count > 0 && ($data_count % $file_limit) == 0) {
//				echo "$data_count>>$xls_fname>>$fname1<br>";
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
			if($REPORT_ORDER == "ORG_REF" || ($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
				if ($f_new) {
					$sheet_no=0; $sheet_no_text="";
					if($data_count > 0) $count_org_ref++;
					$f_new = false;
				} else if ($REPORT_ORDER == "ORG_REF") {
					$f_org_ref_skip = true;
					$ORG_ID_REF = $arr_content[$data_count][org_id_ref];
					$ORG_NAME_REF = $arr_content[$data_count][org_name_ref];
					$sheet_no=0; $sheet_no_text="";
					if($data_count > 0) $count_org_ref++;
				} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
					$sheet_no++;
					$sheet_no_text = "_$sheet_no";
				}

				$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text);
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
				
				//====================== SET FORMAT ======================//
//				require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
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
				} // end if
		
				print_header();
			} // end if

			if(!$f_org_ref_skip){ // ถ้ารายการนี้มี $REPORT_ORDER == "ORG_REF" จะไม่พิมพ์รายละเอียดข้างล่าง
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$POS_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$PT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$LEVEL_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$SKILL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$OT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "$PV_NAME_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "$POS_STATUS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "$PN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14, "$PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 16, "$PER_GENDER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 17, "$BIRTHDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 18, "$STARTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 19, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 20, "$PER_MGTSALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 21, "$EL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 22, "$EN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 23, "$EM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 24, "$INS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 25, "$CT_NAME_EDU", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 26, "$ST_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 27, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 28, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 29, "$CL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 30, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				
				$cmd = "select TEMPCARDNO from GPIS1 where TEMPCARDNO = '$PER_CARDNO'";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$repeat_CARDNO = $data[TEMPCARDNO];
				//echo "$repeat_CARDNO<br>";
				if(!$repeat_CARDNO && $PER_CARDNO){
					$cmd = " insert into GPIS1 (TEMPMINISTRY, ORGANZIE, DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
									TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, TEMPPRENAME, TEMPFIRSTNAME, 
									TEMPLASTNAME, TEMPCARDNO, TEMPGENDER, TEMPBIRTHDATE, TEMPSTARTDATE, TEMPSALARY, TEMPPOSITIONSALARY, TEMPEDUCATIONLEVEL,
									TEMPEDUCATIONNAME, TEMPEDUCATIONMAJOR, TEMPGRADUATED, TEMPEDUCATIONCOUNTRY, TEMPSCHOLARTYPE, TEMPMOVEMENTTYPE,
									TEMPMOVEMENTDATE, TEMPCLNAME, TEMPFLOWDATE)
									values ('$ORG_NAME', '$ORG_NAME_1', '$ORG_NAME_2', '$POS_NO', '$PL_NAME', '$PT_NAME', '$LEVEL_NO', '$PM_NAME', 
									'$SKILL_NAME', '$OT_NAME', '$PV_NAME_ORG', '$POS_STATUS', '$PN_NAME', '$PER_NAME', '$PER_SURNAME', '$PER_CARDNO', 
									'$PER_GENDER_NAME', '$BIRTHDATE', '$STARTDATE', $PER_SALARY, $PER_MGTSALARY, '$EL_NAME', '$EN_NAME', 
									'$EM_NAME', '".save_quote($INS_NAME)."', '$CT_NAME_EDU', '$ST_NAME', '$MOV_NAME', '$EFFECTIVEDATE', '$CL_NAME', '') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
				$repeat_CARDNO = "";
			} // end if
			$f_org_ref_skip = false;
		} 	// end for				
	}else{	// if($count_data)
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

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
	} // end if

	$workbook->close();
	$arr_file[] = $fname1;
?>
<html>
<head>
<title><?=$webpage_title?> - <?=$MENU_TITLE_LV0?><?if($MENU_ID_LV1){?> - <?=$MENU_TITLE_LV1?><?}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-874">
<link rel="stylesheet" href="<?=$cssfileselected?>" type="text/css">
<link rel="alternate stylesheet" type="text/css" media="all" href="stylesheets/calendar-blue.css" title="winter"/>
</head>
<?

//	echo "ทำต่อรายงานชุด 2<br>";

	include("rpt_gpis_xls2.php");

?>