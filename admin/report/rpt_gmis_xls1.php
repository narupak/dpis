<?php
	$time1 = time();
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
        set_time_limit(0);
	ini_set("max_execution_time", 0);//ini_set("max_execution_time", $max_execution_time);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
        /*end เพิ่ม ตรวจสอบก่อนทำการแทรกคอลัมน์ 11/11/2016 *//*ตรวจสอบก่อนทำการแทรกคอลัมน์*/
        $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                    FROM USER_TAB_COLS
                    WHERE  TABLE_NAME = 'PER_SPECIAL_SKILLGRP'
                    AND UPPER(COLUMN_NAME) 
                    IN('REF_CODE')";
                    $db_dpis->send_cmd($cmdChk);
                    $dataChk = $db_dpis->get_array();
        if($dataChk[CNT]=="0"){
            $cmdA = "ALTER TABLE PER_SPECIAL_SKILLGRP ADD  REF_CODE CHAR(10)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "COMMIT";
            $db_dpis->send_cmd($cmdA);
        }  
       /*ตรวจสอบก่อนทำการลบตาราง*/
        $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                    FROM USER_TAB_COLS
                    WHERE  TABLE_NAME = 'GMIS_GPIS'
                    AND UPPER(COLUMN_NAME) 
                    IN('TEMPPERSPSKILL1')";
        $db_dpis->send_cmd($cmdChk);
        $dataChk = $db_dpis->get_array();
        if($dataChk[CNT]!="0"){
            $cmdA = "ALTER TABLE GMIS_GPIS drop  TEMPPERSPSKILL1";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS drop  TEMPPERSPSKILLDES1";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS drop  TEMPPERSPSKILL2";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS drop  TEMPPERSPSKILLDES2";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS drop  TEMPPERSPSKILL3";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS drop  TEMPPERSPSKILLDES3";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "COMMIT";
            $db_dpis->send_cmd($cmdA);
        }
        /*เพิ่ม ตรวจสอบก่อนทำการแทรกคอลัมน์ 16/12/2016 */
         $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                    FROM USER_TAB_COLS
                    WHERE  TABLE_NAME = 'GMIS_GPIS'
                    AND UPPER(COLUMN_NAME) 
                    IN('TEMPMPERSPSKILL1','TEMPSPERSPSKILL1','TEMPSKILLLEVEL1','TEMPPERSPSKILLDES1','TEMPMPERSPSKILL2','TEMPSPERSPSKILL2','TEMPSKILLLEVEL2','TEMPPERSPSKILLDES2','TEMPMPERSPSKILL3','TEMPSPERSPSKILL3','TEMPSKILLLEVEL3','TEMPPERSPSKILLDES3')";
        $db_dpis->send_cmd($cmdChk);
        $dataChk = $db_dpis->get_array();
        if($dataChk[CNT]=="0"){
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPMPERSPSKILL1 VARCHAR2(100)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSPERSPSKILL1 VARCHAR2(100)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSKILLLEVEL1 VARCHAR2(100)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPPERSPSKILLDES1 VARCHAR2(2000)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPMPERSPSKILL2 VARCHAR2(100)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSPERSPSKILL2 VARCHAR2(100)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSKILLLEVEL2 VARCHAR2(100)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPPERSPSKILLDES2 VARCHAR2(2000)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPMPERSPSKILL3 VARCHAR2(100)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSPERSPSKILL3 VARCHAR2(100)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSKILLLEVEL3 VARCHAR2(100)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPPERSPSKILLDES3 VARCHAR2(2000)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "COMMIT";
            $db_dpis->send_cmd($cmdA);
        }
         /*ตรวจสอบก่อนทำการแทรกคอลัมน์*/
        $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                    FROM USER_TAB_COLS
                    WHERE  TABLE_NAME = 'GMIS_GPIS'
                    AND UPPER(COLUMN_NAME) 
                    IN('TEMPSPS_FLAG1','TEMPSPS_FLAG2','TEMPSPS_FLAG3')";
                    $db_dpis->send_cmd($cmdChk);
                    $dataChk = $db_dpis->get_array();
        if($dataChk[CNT]=="0"){
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSPS_FLAG1 VARCHAR2(100 BYTE)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSPS_FLAG2 VARCHAR2(100 BYTE)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSPS_FLAG3 VARCHAR2(100 BYTE)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "COMMIT";
            $db_dpis->send_cmd($cmdA);
        } 
        function get_chind_special_skill($db_dpis2,$SS_CODE){
            $cmd = " SELECT REF_CODE FROM PER_SPECIAL_SKILLGRP WHERE trim(SS_CODE) = trim('$SS_CODE') START WITH REF_CODE IS null CONNECT BY NOCYCLE PRIOR trim(SS_CODE) = trim(REF_CODE)";             
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();//echo "<pre>".$cmd."<br>";
            $REF_CODE = trim($data2[REF_CODE]);//echo "Name =".$SS_NAME."<br>";
            return $REF_CODE;
        }
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
	$arr_rpt_order = explode("|", $RPTORD_LIST);//	$arr_rpt_order = array("POSNO"); 
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
		$worksheet->set_column(0, 0, 40);$worksheet->set_column(1, 1, 40);$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 15);$worksheet->set_column(4, 4, 12);$worksheet->set_column(5, 5, 40);
		$worksheet->set_column(6, 6, 40);$worksheet->set_column(7, 7, 15);$worksheet->set_column(8, 8, 40);
		$worksheet->set_column(9, 9, 25);$worksheet->set_column(10, 10, 20);$worksheet->set_column(11, 11, 40);
		$worksheet->set_column(12, 12, 20);$worksheet->set_column(13, 13, 20);$worksheet->set_column(14, 14, 20);
		$worksheet->set_column(15, 15, 20);$worksheet->set_column(16, 16, 25);$worksheet->set_column(17, 17, 25);
		$worksheet->set_column(18, 18, 20);$worksheet->set_column(19, 19, 5);$worksheet->set_column(20, 20, 5);
		$worksheet->set_column(21, 21, 5);$worksheet->set_column(22, 22, 15);$worksheet->set_column(23, 23, 15);
		$worksheet->set_column(24, 24, 15);$worksheet->set_column(25, 25, 20);$worksheet->set_column(26, 26, 40);
		$worksheet->set_column(27, 27, 40);$worksheet->set_column(28, 28, 40);$worksheet->set_column(29, 29, 40);
		$worksheet->set_column(30, 30, 40);$worksheet->set_column(31, 31, 40);$worksheet->set_column(32, 32, 20);
		$worksheet->set_column(33, 33, 15);$worksheet->set_column(34, 34, 15);$worksheet->set_column(35, 35, 15);
		$worksheet->set_column(36, 36, 15);$worksheet->set_column(37, 37, 20);$worksheet->set_column(38, 38, 20);
		$worksheet->set_column(39, 39, 20);$worksheet->set_column(40, 40, 20);$worksheet->set_column(41, 41, 20);
		$worksheet->set_column(42, 42, 20);$worksheet->set_column(43, 43, 50);$worksheet->set_column(44, 44, 50);
                $worksheet->set_column(45, 45, 50);$worksheet->set_column(46, 46, 50);$worksheet->set_column(47, 47, 50);
                $worksheet->set_column(48, 48, 50);$worksheet->set_column(49, 49, 50);$worksheet->set_column(50, 50, 50);
                $worksheet->set_column(51, 51, 50);$worksheet->set_column(52, 52, 50);/* $worksheet->set_column(53, 53, 50);$worksheet->set_column(54, 54, 50);$worksheet->set_column(55, 55, 50);$worksheet->set_column(56, 56, 50);$worksheet->set_column(57, 57, 50);*/
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "tempMinistry", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "tempOrganize", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "tempDivisionName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "tempOrganizeType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "tempPositionNo", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "tempManagePosition", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "tempLine", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "tempPositionType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "tempLevel", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 9, "tempClName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 10, "tempSkill", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 11, "tempCountry", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "tempProvince", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 13, "tempPositionStatus", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 14, "tempClass", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 15, "tempPrename", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 16, "tempFirstName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 17, "tempLastName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 18, "tempCardNo", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 19, "tempGender", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 20, "tempStatusDisability", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 21, "tempReligion", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 22, "tempBirthDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 23, "tempSalary", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 24, "tempPositionSalary", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 25, "tempEducationLevel", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 26, "tempEducationName", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 27, "tempEducationMajor", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 28, "tempGraduated", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 29, "tempEducationCountry", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 30, "tempScholarType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 31, "tempMovementType", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 32, "tempMovementDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 33, "tempStartDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 34, "tempFlowDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 35, "tempResignDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 36, "tempPromoteDate", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 37, "tempDecoration", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 38, "tempUnion", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 39, "tempResult1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 40, "tempPercentSalary1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 41, "tempResult2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 42, "tempPercentSalary2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 43, "tempMPerSPSkill1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 44, "tempSPerSPSkill1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 45, "tempSkillLevel1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 46, "tempPerSPSkillDes1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 47, "tempSPS_Flag1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 48, "tempMPerSPSkill2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 49, "tempSPerSPSkill2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 50, "tempSkillLevel2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 51, "tempPerSPSkillDes2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));     
                $worksheet->write($xlsRow, 52, "tempSPS_Flag2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));/* $worksheet->write($xlsRow, 53, "tempMPerSPSkill3", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));$worksheet->write($xlsRow, 54, "tempSPerSPSkill3", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));$worksheet->write($xlsRow, 55, "tempSkillLevel3", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));$worksheet->write($xlsRow, 56, "tempPerSPSkillDes3", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));$worksheet->write($xlsRow, 57, "tempSPS_Flag3", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));*/
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
	$cmd = " delete from GMIS_GPIS ";
	$db_dpis->send_cmd($cmd);//$db_dpis->show_error();
	$cmd = " delete from GMIS_GPIS_FLOW_IN ";
	$db_dpis->send_cmd($cmd);//$db_dpis->show_error();
	$cmd = " delete from GMIS_GPIS_FLOW_OUT ";
	$db_dpis->send_cmd($cmd);//$db_dpis->show_error();
	if($DPISDB=="odbc"){ // ===== select data =====
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
	$count_data = $db_dpis->send_cmd($cmd);//echo "<pre>$cmd<br>";//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];
			$arr_content[$data_count][type] = "ORG_REF";
			$arr_content[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_content[$data_count][org_name_ref] = $ORG_NAME_REF;
			$data_count++;
		} // end if
		$PER_TYPE = 1;
		$GPIS_FLAG = "DATA";//include("rpt_gmis_xls_dtl.php");//ตัดแปะเข้ามาจาก rpt_gmis_xls_dtl.php
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
							$CT_NAME_ORG = $PV_NAME_ORG = "";
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
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PV_CODE_ORG = trim($data3[PV_CODE]);
							$PV_NAME_ORG = trim($data3[PV_NAME]);
							$CT_CODE_ORG = trim($data3[CT_CODE]);
							$CT_NAME_ORG = trim($data3[CT_NAME]);
							unset($tmp_ORG_ID, $ORG_ID_search, $ORG_ID_3, $ORG_ID_4, $ORG_NAME_3, $ORG_NAME_4);
							$ORG_ID_3 = $tmp_ORG_ID[] = trim($data[ORG_ID_1]);
							$ORG_ID_4 = $tmp_ORG_ID[] = trim($data[ORG_ID_2]);
							if($ORG_ID_3 && $ORG_ID_4){
								$ORG_ID_search = implode(", ", $tmp_ORG_ID);
								$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_search) ";
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
											ORG_ID, MOV_CODE, PER_OCCUPYDATE, PER_POSDATE, PER_RETIREDATE, PER_DISABILITY, RE_CODE, 
											PER_UNION, PER_UNION2, PER_UNION3, PER_UNION4, PER_UNION5  
									from		PER_PERSONAL
									where	PER_TYPE=$PER_TYPE and $where ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PER_ID = $data2[PER_ID];
							$PER_GENDER = $data2[PER_GENDER];
							$PER_DISABILITY = $data2[PER_DISABILITY];
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
							$FLOWDATE = show_date_format($data2[PER_OCCUPYDATE],1);
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
							$RE_CODE = trim($data2[RE_CODE]);
							$ORG_ID_ASS = trim($data2[ORG_ID]);
							if ($ORG_ID_ASS) { // === หาจังหวัดและประเทศตามมอบหมายงาน
								if ($DPISDB == "odbc") 
									$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
											from ( PER_ORG_ASS a 
												   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
												) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
											where ORG_ID=$ORG_ID_ASS ";
								elseif ($DPISDB == "oci8") 
									$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
											from PER_ORG_ASS a, PER_PROVINCE b, PER_COUNTRY c 
											where ORG_ID=$ORG_ID_ASS and a.PV_CODE=b.PV_CODE(+) and 
													a.CT_CODE=c.CT_CODE(+) ";
								elseif($DPISDB=="mysql")
									$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
											from ( PER_ORG_ASS a 
												   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
												) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
											where ORG_ID=$ORG_ID_ASS ";
								$db_dpis3->send_cmd($cmd);
								$data3 = $db_dpis3->get_array();
								$PV_CODE_ORG_ASS = trim($data3[PV_CODE]);
								$PV_NAME_ORG_ASS = trim($data3[PV_NAME]);
								$CT_CODE_ORG_ASS = trim($data3[CT_CODE]);
								$CT_NAME_ORG_ASS = trim($data3[CT_NAME]);
							} else {
								$PV_CODE_ORG_ASS = $PV_CODE_ORG;
								$PV_NAME_ORG_ASS = $PV_NAME_ORG;						
								$CT_CODE_ORG_ASS = $CT_CODE_ORG;	
								$CT_NAME_ORG_ASS = $CT_NAME_ORG;							
							}  // end if ($ORG_ID_ASS)/*$MOV_CODE = trim($data2[MOV_CODE]);$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE' ";$db_dpis3->send_cmd($cmd);$data3 = $db_dpis3->get_array();$MOV_NAME = trim($data3[MOV_NAME]);*/
							$CLASS_NAME = "";
							$cmd = " select PN_NAME, RANK_FLAG from PER_PRENAME where PN_CODE='$PN_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PN_NAME = trim($data2[PN_NAME]);
							$RANK_FLAG = trim($data2[RANK_FLAG]);
							if ($RANK_FLAG==1) $CLASS_NAME = $PN_NAME;
							if (!$LEVEL_NO) {
								$cmd = " select LEVEL_NO_MIN from PER_CO_LEVEL where CL_NAME='$CL_NAME' ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$LEVEL_NO = $data2[LEVEL_NO_MIN];
							}
							$cmd = " select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$LEVEL_NAME = trim($data2[LEVEL_NAME]);
							$POSITION_TYPE = trim($data2[POSITION_TYPE]);
							$cmd = " select SKILL_NAME from PER_SKILL where SKILL_CODE='$SKILL_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$SKILL_NAME = trim($data2[SKILL_NAME]);
							$cmd = " select RE_NAME from PER_RELIGION where trim(RE_CODE)='$RE_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$RE_NAME = trim($data3[RE_NAME]);
							$EL_CODE = $EL_NAME = $EN_NAME = $EM_NAME = $INS_CODE = $INS_NAME = $ST_CODE = $ST_NAME = $CT_CODE_EDU = $CT_NAME_EDU = "";
							$SAH_EFFECTIVEDATE = $POH_DOCNO = $POH_DOCDATE = $POH_EFFECTIVEDATE = $SCH_NAME = $DC_NAME = "";
							$RESULT1 = $RESULT2 = $PERCENT_SALARY1 = $PERCENT_SALARY2 = $UNION_CODE = "";
							$POH_EFFECTIVEDATE = $PROMOTEDATE = $MOV_NAME = $LEVEL_NO_C = "";
							$PT_CODE_C = "('')";
							if($PER_ID){/*ปรับใหม่ เพิ่มเงือนไขใหญ่คุม http://dpis.ocsc.go.th/Service/node/1153*//*วุฒิในตำแหน่งปัจจุบัน และ วุฒิสูงสุด */
                                                            $cmd = "select a.EDU_TYPE,c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
                                                                    from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
                                                                    where a.PER_ID=$PER_ID 
                                                                    and (a.EDU_TYPE like '%2%' and a.EDU_TYPE like '%4%')
                                                                        and a.EN_CODE=b.EN_CODE(+) and trim(b.EL_CODE)=trim(c.EL_CODE(+)) 
                                                                        and a.EM_CODE=d.EM_CODE(+)";
                                                            $count_educate = $db_dpis2->send_cmd($cmd);
                                                            if(!$count_educate){ ///*วุฒิสูงสุด */
                                                                $cmd = "select a.EDU_TYPE,c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
                                                                    from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
                                                                    where a.PER_ID=$PER_ID 
                                                                    and a.EDU_TYPE like '%4%'
                                                                        and a.EN_CODE=b.EN_CODE(+) and trim(b.EL_CODE)=trim(c.EL_CODE(+)) 
                                                                        and a.EM_CODE=d.EM_CODE(+)";
                                                                $count_educate = $db_dpis2->send_cmd($cmd);
                                                            }
                                                            if(!$count_educate){/*วุฒิในตำแหน่งปัจจุบัน*/
                                                                $cmd = "select a.EDU_TYPE,c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
                                                                    from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
                                                                    where a.PER_ID=$PER_ID 
                                                                    and a.EDU_TYPE like '%2%'
                                                                        and a.EN_CODE=b.EN_CODE(+) and trim(b.EL_CODE)=trim(c.EL_CODE(+)) 
                                                                        and a.EM_CODE=d.EM_CODE(+)";
                                                                $count_educate = $db_dpis2->send_cmd($cmd);
                                                            }
                                                            if(!$count_educate){/*วุฒิที่ใช้บรรจุ */
                                                                $cmd = "select a.EDU_TYPE,c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
                                                                    from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
                                                                    where a.PER_ID=$PER_ID 
                                                                    and a.EDU_TYPE like '%1%'
                                                                        and a.EN_CODE=b.EN_CODE(+) and trim(b.EL_CODE)=trim(c.EL_CODE(+)) 
                                                                        and a.EM_CODE=d.EM_CODE(+)";
                                                            }
                                                                $db_dpis2->send_cmd($cmd);    
								$data2 = $db_dpis2->get_array();
								$EL_CODE = trim($data2[EL_CODE]);
								$EL_NAME = trim($data2[EL_NAME]);/*ที่ต้องปรับ????*/
								$EN_NAME = trim($data2[EN_NAME]);
								$EM_NAME = stripslashes(trim($data2[EM_NAME]));
								$INS_CODE = trim($data2[INS_CODE]);
								$ST_CODE = trim($data2[ST_CODE]);
								if ($INS_CODE) {
									if($DPISDB=="odbc") { // หาชื่อโรงเรียน และประเทศของโรงเรียน
										$cmd = " select INS_NAME, a.CT_CODE, CT_NAME 
                                                                                        from   PER_INSTITUTE a 
                                                                                                   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
                                                                                        where INS_CODE='$INS_CODE' ";
									} elseif ($DPISDB=="oci8") { // หาชื่อโรงเรียน และประเทศของโรงเรียน
										$cmd = " select INS_NAME, a.CT_CODE, CT_NAME from PER_INSTITUTE a, PER_COUNTRY b 
                                                                                        where INS_CODE='$INS_CODE' and a.CT_CODE=b.CT_CODE(+) ";
									}elseif($DPISDB=="mysql"){ // หาชื่อโรงเรียน และประเทศของโรงเรียน
										$cmd = " select INS_NAME, a.CT_CODE, CT_NAME 
                                                                                        from   PER_INSTITUTE a 
                                                                                                   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
                                                                                        where INS_CODE='$INS_CODE' ";
									} 			
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$INS_NAME = stripslashes(trim($data2[INS_NAME]));
									$CT_CODE_EDU = trim($data2[CT_CODE]);
									$CT_NAME_EDU = trim($data2[CT_NAME]);
								} 
                                                                else {$INS_NAME = trim($data2[EDU_INSTITUTE]);} // end if
								// === หาวันที่เงินเดือนมีผล 
								$cmd = " select SAH_EFFECTIVEDATE
										from   PER_SALARYHIS
										where PER_ID=$PER_ID 
										order by SAH_EFFECTIVEDATE desc ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$SAH_EFFECTIVEDATE = show_date_format($data2[SAH_EFFECTIVEDATE],1);
								// === หาตำแหน่งล่าสุด เลขที่คำสั่ง, วันที่ออกคำสั่ง, วันที่มีผล
								$cmd = " select POH_DOCNO, POH_DOCDATE, POH_EFFECTIVEDATE
										from   PER_POSITIONHIS a, PER_MOVMENT b
										where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
										order by POH_EFFECTIVEDATE desc ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$POH_DOCNO = trim($data2[POH_DOCNO]);
								$POH_DOCDATE = show_date_format($data2[POH_DOCDATE],1);
								$POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
								// === หาชื่อทุน และแหล่งทุน
								$cmd = "	select 	ST_NAME  
										from 	PER_EDUCATE a, PER_SCHOLARTYPE b  
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and a.ST_CODE=b.ST_CODE(+) ";
								$count_educate = $db_dpis2->send_cmd($cmd);
								if(!$count_educate){
                                                                    $cmd = "	select 	ST_NAME  
                                                                                    from 	PER_EDUCATE a, PER_SCHOLARTYPE b  
                                                                                    where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' and a.ST_CODE=b.ST_CODE(+) ";
                                                                    $count_educate = $db_dpis2->send_cmd($cmd);
								}
								if(!$count_educate){
                                                                    $cmd = "	select 	ST_NAME  
                                                                                    from 	PER_EDUCATE a, PER_SCHOLARTYPE b  
                                                                                    where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' and a.ST_CODE=b.ST_CODE(+) ";
                                                                    $count_educate = $db_dpis2->send_cmd($cmd);
								}
								if(!$count_educate){
                                                                    $cmd = " select SCH_NAME, ST_NAME
                                                                            from   PER_SCHOLAR a, PER_SCHOLARSHIP b, PER_SCHOLARTYPE c
                                                                            where PER_ID=$PER_ID and a.SCH_CODE=b.SCH_CODE and 
                                                                                       b.ST_CODE=c.ST_CODE
                                                                            order by a.SC_ID desc ";
                                                                    $db_dpis2->send_cmd($cmd);
								}
								$data2 = $db_dpis2->get_array();
								$SCH_NAME = trim($data2[SCH_NAME]);
								$ST_NAME = trim($data2[ST_NAME]);
								// หาเครื่องราชฯ
                                                                $cmd = " select DC_NAME from PER_DECORATEHIS a, PER_DECORATION b 
                                                                        where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE(+)
                                                                        order by DC_TYPE, DC_ORDER ";	
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$DC_NAME = trim($data2[DC_NAME]);
								// === หาร้อยละที่ได้รับการเลื่อนเงินเดือน 
								$cmd = " select MOV_NAME, SAH_PERCENT_UP
                                                                        from   PER_SALARYHIS a, PER_MOVMENT b
                                                                        where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and SAH_KF_YEAR = '$search_budget_year' and 
                                                                                                SAH_KF_CYCLE = 1 and substr(SAH_EFFECTIVEDATE, 6,5) = '04-01' and MOV_SUB_TYPE <> '0'
                                                                        order by SAH_EFFECTIVEDATE desc, SAH_CMD_SEQ desc, SAH_SALARY desc ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$RESULT1 = trim($data2[MOV_NAME]);
								$PERCENT_SALARY1 = trim($data2[SAH_PERCENT_UP]);
								$search_budget_year2 = $search_budget_year - 1;
								$cmd = " select MOV_NAME, SAH_PERCENT_UP
                                                                        from   PER_SALARYHIS a, PER_MOVMENT b
                                                                        where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and SAH_KF_YEAR = '$search_budget_year2' and 
                                                                                                SAH_KF_CYCLE = 2  and substr(SAH_EFFECTIVEDATE, 6,5) = '10-01' and MOV_SUB_TYPE <> '0'
                                                                        order by SAH_EFFECTIVEDATE desc, SAH_CMD_SEQ desc, SAH_SALARY desc ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$RESULT2 = trim($data2[MOV_NAME]);
								$PERCENT_SALARY2 = trim($data2[SAH_PERCENT_UP]);
								// วันเข้าสู่ระดับปัจจุบัน 
								if ($LEVEL_NO=="M2") {$LEVEL_NO_C = "('11','10')"; $PT_CODE_C = "('32')";}
								elseif ($LEVEL_NO=="M1") {$LEVEL_NO_C = "('09')"; $PT_CODE_C = "('32')";}
								elseif ($LEVEL_NO=="D2") {$LEVEL_NO_C = "('09')"; $PT_CODE_C = "('32')";}
								elseif ($LEVEL_NO=="D1") {$LEVEL_NO_C = "('08')"; $PT_CODE_C = "('31')";}
								elseif ($LEVEL_NO=="K5") {$LEVEL_NO_C = "('10')"; $PT_CODE_C = "('22','21')";}
								elseif ($LEVEL_NO=="K4") {$LEVEL_NO_C = "('09')"; $PT_CODE_C = "('22','21')";}
								elseif ($LEVEL_NO=="K3") {$LEVEL_NO_C = "('08')"; $PT_CODE_C = "('11','12', NULL)";}
								elseif ($LEVEL_NO=="K2") {$LEVEL_NO_C = "('07','06')"; $PT_CODE_C = "('11','12', NULL)";}
								elseif ($LEVEL_NO=="K1") {$LEVEL_NO_C = "('05','04','03')"; $PT_CODE_C = "('11','12', NULL)";}
								elseif ($LEVEL_NO=="O4") {$LEVEL_NO_C = "('09')"; $PT_CODE_C = "('11', NULL)";}
								elseif ($LEVEL_NO=="O3") {$LEVEL_NO_C = "('08','07')"; $PT_CODE_C = "('11', NULL)";}
								elseif ($LEVEL_NO=="O2") {$LEVEL_NO_C = "('06','05')"; $PT_CODE_C = "('11', NULL)";}
								elseif ($LEVEL_NO=="O2") {$LEVEL_NO_C = "('04','03','02','01')"; $PT_CODE_C = "('11', NULL)";}
								else {$LEVEL_NO_C = "('".$LEVEL_NO."')";}
								if ($MFA_FLAG==1 && $PM_CODE) {
                                                                    $cmd = " select POH_EFFECTIVEDATE
                                                                            from   PER_POSITIONHIS a, PER_MOVMENT b
                                                                            where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$LEVEL_NO' and PM_CODE='$PM_CODE' and  
                                                                                                    (MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
                                                                            order by POH_EFFECTIVEDATE ";
                                                                }else{
                                                                    $cmd = " select POH_EFFECTIVEDATE
                                                                            from   PER_POSITIONHIS a, PER_MOVMENT b
                                                                            where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and (LEVEL_NO='$LEVEL_NO' or 
                                                                                                    (LEVEL_NO in $LEVEL_NO_C and PT_CODE in $PT_CODE_C)) and 
                                                                                                    (MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
                                                                            order by POH_EFFECTIVEDATE ";
                                                                }    
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$PROMOTEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
								// วันดำรงตำแหน่งปัจจุบัน		
								$cmd = " select POH_EFFECTIVEDATE, MOV_NAME  
												from PER_POSITIONHIS a, PER_MOVMENT b 
												where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and 
																(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
												order by POH_EFFECTIVEDATE desc";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
								$MOV_NAME = trim($data2[MOV_NAME]);
                                                                if($PER_ID){
                                                                    /*ปรับของเก่าให้เร็วขึ้น*/// ความเชี่ยวชาญพิเศษ และ เน้นทาง  col 1 07/11/2016 by somkiet 
                                                                    $cmd = "    WITH TBCHECK AS (
                                                                                    SELECT REF_CODE , SS_CODE
                                                                                    FROM PER_SPECIAL_SKILLGRP
                                                                                    START WITH REF_CODE IS null
                                                                                    CONNECT BY NOCYCLE PRIOR trim(SS_CODE) = trim(REF_CODE)
                                                                                ),  TB_MAIN AS ( 
                                                                                    SELECT 	ROW_NUMBER() OVER (
                                                                                        PARTITION BY b.PER_ID
                                                                                        ORDER BY (CASE WHEN b.AUDIT_FLAG is NULL or b.AUDIT_FLAG='N' THEN '0' ELSE b.AUDIT_FLAG END) desc, b.SPS_SEQ_NO  ASC
                                                                                    ) ROW_NUM , 
                                                                                    b.PER_ID, c.SS_NAME, SPS_EMPHASIZE, b.SS_CODE, e.REF_CODE, b.LEVELSKILL_CODE,(CASE WHEN b.AUDIT_FLAG IS NULL OR b.AUDIT_FLAG='N' THEN '0' ELSE b.AUDIT_FLAG END) AS AUDIT_FLAG,
                                                                                    b.SPS_FLAG , d.LEVELSKILL_NAME
                                                                                    FROM   PER_SPECIAL_SKILL b ,PER_SPECIAL_SKILLGRP c ,PER_LEVELSKILL d ,TBCHECK e
                                                                                    WHERE  b.SS_CODE = c.SS_CODE and b.LEVELSKILL_CODE = d.LEVELSKILL_CODE(+) AND  trim(b.SS_CODE) = trim(e.SS_CODE(+))
                                                                                ),  TB1 AS (
                                                                                    SELECT * FROM TB_MAIN WHERE  ROW_NUM=1
                                                                                ),  TB2 AS (
                                                                                    SELECT  ROW_NUM AS ROW_NUM2 , PER_ID AS PER_ID2, SS_NAME as SS_NAME2, SPS_EMPHASIZE as SPS_EMPHASIZE2,
                                                                                        SS_CODE AS SS_CODE2, REF_CODE AS REF_CODE2, LEVELSKILL_CODE AS LEVELSKILL_CODE2, AUDIT_FLAG AS AUDIT_FLAG2,
                                                                                        SPS_FLAG AS SPS_FLAG2 , LEVELSKILL_NAME as LEVELSKILL_NAME2
                                                                                    FROM TB_MAIN WHERE  ROW_NUM=2
                                                                                ) SELECT a.* , b.* FROM TB1 a ,TB2 b WHERE a.PER_ID=b.PER_ID2(+) AND a.PER_ID=$PER_ID";
                                                                    $db_dpis2->send_cmd($cmd);//die("<pre>".$cmd);
                                                                    $data2 = $db_dpis2->get_array();
                                                                    // ความเชี่ยวชาญพิเศษ และ เน้นทาง1
                                                                    $SS_CHK_CODE = trim($data2[SS_CODE]);
                                                                    if($SS_CHK_CODE !== "-1"){
                                                                        $SS_CODE = trim($data2[SS_CODE]);
                                                                        $REF_CODE = trim($data2[REF_CODE]); //get_chind_special_skill($db_dpis2,$SS_CODE);//trim($data2[SS_NAME]);
                                                                        $SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
                                                                        $LEVELSKILL_CODE = trim($data2[LEVELSKILL_CODE]);
                                                                        $LEVELSKILL_NAME = $data2[LEVELSKILL_NAME];
                                                                    }else{
                                                                        $SS_CODE = "";
                                                                        $REF_CODE = "";
                                                                        $SPS_EMPHASIZE = "";
                                                                        $LEVELSKILL_CODE = "";
                                                                        $LEVELSKILL_NAME = "";
                                                                    }
                                                                    $AUDIT_FLAG = trim($data2[AUDIT_FLAG]);
                                                                    $SPS_FLAG_C = trim($data2[SPS_FLAG]);
                                                                    if($SPS_FLAG_C == '1'){
                                                                        $SPS_FLAG = $SPS_FLAG_C."-ความเชี่ยวชาญในงานราชการ";
                                                                    }else if($SPS_FLAG_C == '2'){
                                                                         $SPS_FLAG = $SPS_FLAG_C."-ความเชี่ยวชาญอื่น ๆ";
                                                                    }else{
                                                                         $SPS_FLAG = "";
                                                                    }  
                                                                    // ความเชี่ยวชาญพิเศษ และ เน้นทาง2
                                                                    $SS_CODE2 = trim($data2[SS_CODE2]);
                                                                    $REF_CODE2 = trim($data2[REF_CODE2]); //get_chind_special_skill($db_dpis2,$SS_CODE2);//trim($data2[SS_NAME2]);
                                                                    $SPS_EMPHASIZE2 = trim($data2[SPS_EMPHASIZE2]);
                                                                    $AUDIT_FLAG2 = trim($data2[AUDIT_FLAG2]);
                                                                    $SPS_FLAG_C2 = trim($data2[SPS_FLAG2]);
                                                                    if($SPS_FLAG_C2 == '1'){
                                                                        $SPS_FLAG2 = $SPS_FLAG_C2."-ความเชี่ยวชาญในงานราชการ";
                                                                    }else if($SPS_FLAG_C2 == '2'){
                                                                         $SPS_FLAG2 = $SPS_FLAG_C2."-ความเชี่ยวชาญอื่น ๆ";
                                                                    }else{
                                                                         $SPS_FLAG2 = "";
                                                                    }
                                                                    $LEVELSKILL_CODE2 = trim($data2[LEVELSKILL_CODE2]);
                                                                    $LEVELSKILL_NAME2 = $data2[LEVELSKILL_NAME2];                                                                        
                                                                }
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
							} // end if
							$arr_content[$data_count][type] = "CONTENT";
							$arr_content[$data_count][order] = $data_row;
							$arr_content[$data_count][org_code] = $ORG_CODE;
							$arr_content[$data_count][per_offno] = $PER_OFFNO;
							$arr_content[$data_count][per_cardno] = $PER_CARDNO;
							$arr_content[$data_count][pn_code] = $PN_CODE;
							$arr_content[$data_count][pn_name] = $PN_NAME;
							$arr_content[$data_count][class_name] = $CLASS_NAME;
							$arr_content[$data_count][per_name] = $PER_NAME;
							$arr_content[$data_count][per_surname] = $PER_SURNAME;
							$arr_content[$data_count][per_eng_name] = $PER_ENG_NAME."  ".$PER_ENG_SURNAME;
							$arr_content[$data_count][per_gender] = $PER_GENDER;
							$arr_content[$data_count][per_disability] = $PER_DISABILITY;
							$arr_content[$data_count][birthdate] = 	$BIRTHDATE;	
							$arr_content[$data_count][startdate] = $STARTDATE;	
							$arr_content[$data_count][flowdate] = $FLOWDATE;	
							$arr_content[$data_count][resigndate] = $RESIGNDATE;
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
							$arr_content[$data_count][re_code] = $RE_CODE;
							$arr_content[$data_count][re_name] = $RE_NAME;
							$arr_content[$data_count][ct_code_org] = $CT_CODE_ORG;
							$arr_content[$data_count][ct_name_org] = $CT_NAME_ORG;					
							$arr_content[$data_count][pv_code_org] = $PV_CODE_ORG;
							$arr_content[$data_count][pv_name_org] = $PV_NAME_ORG;
							$arr_content[$data_count][ct_code_org_ass] = $CT_CODE_ORG_ASS;
							$arr_content[$data_count][ct_name_org_ass] = $CT_NAME_ORG_ASS;
							$arr_content[$data_count][pv_code_org_ass] = $PV_CODE_ORG_ASS;
							$arr_content[$data_count][pv_name_org_ass] = $PV_NAME_ORG_ASS;
							$arr_content[$data_count][org_name] = $ORG_NAME;
							$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
							$arr_content[$data_count][org_name_2] = $ORG_NAME_2;
							$arr_content[$data_count][org_name_3] = $ORG_NAME_3;
							$arr_content[$data_count][org_name_4] = $ORG_NAME_4;
							$arr_content[$data_count][level_no] = level_no_format($LEVEL_NO);
							$arr_content[$data_count][level_name] = $LEVEL_NAME;
							$arr_content[$data_count][position_type] = $POSITION_TYPE;
							$arr_content[$data_count][sah_effectivedate] = $SAH_EFFECTIVEDATE;//$arr_content[$data_count][per_salary] = (($PER_SALARY)?number_format($PER_SALARY,0,'',','):"");//$arr_content[$data_count][per_mgtsalary] = (($PER_MGTSALARY)?number_format($PER_MGTSALARY,0,'',','):"");
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
							$arr_content[$data_count][dc_code] = $DC_CODE;
							$arr_content[$data_count][dc_name] = $DC_NAME;
							$arr_content[$data_count][mov_code] = $MOV_CODE;
							$arr_content[$data_count][mov_name] = $MOV_NAME; 
							$arr_content[$data_count][union_code] = $UNION_CODE; 
							$arr_content[$data_count][result1] = $RESULT1; 
							$arr_content[$data_count][percent_salary1] = $PERCENT_SALARY1; 
							$arr_content[$data_count][result2] = $RESULT2; 
							$arr_content[$data_count][percent_salary2] = $PERCENT_SALARY2; 
                                                    if($SS_CHK_CODE === "-1"){}
                                                    else{
                                                        if($REF_CODE == NULL){
                                                            $arr_content[$data_count][SS_CODE] = $SS_CODE;
                                                            $arr_content[$data_count][REF_CODE] = $REF_CODE;
                                                        }else if($REF_CODE){
                                                            $arr_content[$data_count][SS_CODE] = $REF_CODE;
                                                            $arr_content[$data_count][REF_CODE] = $SS_CODE;
                                                        }
                                                        $arr_content[$data_count][LEVELSKILL_NAME] = $LEVELSKILL_NAME;
                                                        if($AUDIT_FLAG === "0"){
                                                            $arr_content[$data_count][SPS_EMPHASIZE] = "*".$SPS_EMPHASIZE;
                                                        }else{                  
                                                            $arr_content[$data_count][SPS_EMPHASIZE] = $AUDIT_FLAG.$SPS_EMPHASIZE;
                                                        }
                                                        $arr_content[$data_count][SPS_FLAG] = $SPS_FLAG;
                                                        if($REF_CODE2 == NULL){
                                                            $arr_content[$data_count][SS_CODE2] = $SS_CODE2;
                                                            $arr_content[$data_count][REF_CODE2] = $REF_CODE2;
                                                        }else if($REF_CODE2){
                                                            $arr_content[$data_count][SS_CODE2] = $REF_CODE2;
                                                            $arr_content[$data_count][REF_CODE2] = $SS_CODE2;
                                                        }
                                                        $arr_content[$data_count][LEVELSKILL_NAME2] = $LEVELSKILL_NAME2;
                                                        if($AUDIT_FLAG2 === "0"){
                                                            $arr_content[$data_count][SPS_EMPHASIZE2] = "*".$SPS_EMPHASIZE2;
                                                        }else{
                                                            $arr_content[$data_count][SPS_EMPHASIZE2] = $AUDIT_FLAG2.$SPS_EMPHASIZE2;
                                                        }
                                                        $arr_content[$data_count][SPS_FLAG2] = $SPS_FLAG2;
                                                    }
							$arr_content[$data_count][promotedate] = $PROMOTEDATE;
							$data_count++;														
						} // end if
					} // end if
				break;
			} // end switch case
		} // end for //จบการ ตัดแปะเข้ามาจาก rpt_gmis_xls_dtl.php
	} // end while //	echo "<pre>"; print_r($arr_content); echo "</pre>";// ===== condition $count_data  from "select data" =====
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	$file_limit = 5000;//$file_limit = 1200;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";
	$arr_file = (array) null;
	$f_new = false;
	$fname= "../../Excel/tmp/rpt_gmis_xls";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnumtext="";
	$workbook = new writeexcel_workbook($fname1); //$workbook->setVersion(8);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//
	$f_org_ref_skip = false;		// มีไว้เพื่อใช้เช็ค  $REPORT_ORDER == "ORG_REF";
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
			$CLASS_NAME = $arr_content[$data_count][class_name];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_SURNAME = $arr_content[$data_count][per_surname];
			$PER_GENDER = $arr_content[$data_count][per_gender];
			$PER_DISABILITY = $arr_content[$data_count][per_disability];
			$PER_GENDER_NAME = $PER_DISABILITY_NAME = $BIRTHDATE = $STARTDATE = $FLOWDATE = "";
			if ($arr_content[$data_count][per_gender] == 1)	$PER_GENDER_NAME = "ชาย";
			elseif ($arr_content[$data_count][per_gender] == 2)	$PER_GENDER_NAME = "หญิง";	
			if ($arr_content[$data_count][per_disability] == 1)	$PER_DISABILITY_NAME = "ปกติ";
			elseif ($arr_content[$data_count][per_disability] >= 2)	$PER_DISABILITY_NAME = "พิการ";
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			if ($BIRTHDATE=="//") $BIRTHDATE = "";
			$STARTDATE = $arr_content[$data_count][startdate];
			if ($STARTDATE=="//") $STARTDATE = "";
			$FLOWDATE = $arr_content[$data_count][flowdate];
			if ($FLOWDATE=="//") $FLOWDATE = "";
			$RESIGNDATE = $arr_content[$data_count][resigndate];
			if ($RESIGNDATE=="//") $RESIGNDATE = "";
			$PROMOTEDATE = $arr_content[$data_count][promotedate];
			if ($PROMOTEDATE=="//") $PROMOTEDATE = "";
			if ($PER_NAME) $POS_STATUS = "มีคนถือครอง";
			else $POS_STATUS = "ตำแหน่งว่าง";
			$POS_NO = $arr_content[$data_count][pos_no];
			$LEVEL_GROUP = $arr_content[$data_count][level_group];
			$PM_CODE = $arr_content[$data_count][pm_code];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PL_CODE = $arr_content[$data_count][pl_code];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$CT_CODE_ORG = $arr_content[$data_count][ct_code_org];
			$CT_NAME_ORG = $arr_content[$data_count][ct_name_org];
			$PV_CODE_ORG = $arr_content[$data_count][pv_code_org];
			$PV_NAME_ORG = $arr_content[$data_count][pv_name_org];
			$SKILL_CODE = $arr_content[$data_count][skill_code];
			$SKILL_NAME = $arr_content[$data_count][skill_name];
			$RE_CODE = $arr_content[$data_count][re_code];
			$RE_NAME = $arr_content[$data_count][re_name];
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
			$POSITION_TYPE = $arr_content[$data_count][position_type];
			$SAH_EFFECTIVEDATE = $arr_content[$data_count][sah_effectivedate];
			if ($SAH_EFFECTIVEDATE=="//") $SAH_EFFECTIVEDATE = "";
			$PER_SALARY = $arr_content[$data_count][per_salary].".00";
			$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];
			if ($PER_MGTSALARY) $PER_MGTSALARY .= ".00";
			$POH_DOCNO = $arr_content[$data_count][poh_docno];
			$POH_DOCDATE = $arr_content[$data_count][poh_docdate];
			$POH_EFFECTIVEDATE = $arr_content[$data_count][poh_effectivedate];
			if ($POH_EFFECTIVEDATE=="//") $POH_EFFECTIVEDATE = "";
			$EL_CODE = $arr_content[$data_count][el_code];
			$EL_NAME = $arr_content[$data_count][el_name];
			$EN_NAME = $arr_content[$data_count][en_name];
			$EM_NAME = $arr_content[$data_count][em_name];
			$INS_NAME = $arr_content[$data_count][ins_name];
			if (!get_magic_quotes_gpc()) {$EM_NAME = addslashes(str_replace('"', "&quot;", trim($EM_NAME)));$INS_NAME = addslashes(str_replace('"', "&quot;", trim($INS_NAME)));}
			else{ $EM_NAME = addslashes(str_replace('"', "&quot;", stripslashes(trim($EM_NAME))));$INS_NAME = addslashes(str_replace('"', "&quot;", stripslashes(trim($INS_NAME))));}
			$CT_CODE_EDU = $arr_content[$data_count][ct_code_edu];
			$CT_NAME_EDU = $arr_content[$data_count][ct_name_edu];
			$ST_CODE = $arr_content[$data_count][st_code];
			$ST_NAME = $arr_content[$data_count][st_name];
			$SCH_NAME = $arr_content[$data_count][sch_name];
			$DC_CODE = $arr_content[$data_count][dc_code];
			$DC_NAME = $arr_content[$data_count][dc_name];
			$MOV_CODE = $arr_content[$data_count][mov_code];
			$MOV_NAME = $arr_content[$data_count][mov_name];
			$UNION_CODE = $arr_content[$data_count][union_code];
			$RESULT1 = $arr_content[$data_count][result1];
			$PERCENT_SALARY1 = $arr_content[$data_count][percent_salary1];
			$RESULT2 = $arr_content[$data_count][result2];
			$PERCENT_SALARY2 = $arr_content[$data_count][percent_salary2];
                        if($PER_NAME){
                                $SS_CODE = $arr_content[$data_count][SS_CODE];
                                $REF_CODE = $arr_content[$data_count][REF_CODE];
                                $LEVELSKILL_NAME = $arr_content[$data_count][LEVELSKILL_NAME];
                                $SPS_EMPHASIZE = $arr_content[$data_count][SPS_EMPHASIZE];
                                $SPS_FLAG  = $arr_content[$data_count][SPS_FLAG];
                                
                                $SS_CODE2 = $arr_content[$data_count][SS_CODE2];
                                $REF_CODE2 = $arr_content[$data_count][REF_CODE2];
                                $LEVELSKILL_NAME2 = $arr_content[$data_count][LEVELSKILL_NAME2];
                                $SPS_EMPHASIZE2 = $arr_content[$data_count][SPS_EMPHASIZE2];
                                $SPS_FLAG2  = $arr_content[$data_count][SPS_FLAG2];/* $SS_CODE3 = $arr_content[$data_count][SS_CODE3];$REF_CODE3 = $arr_content[$data_count][REF_CODE3];$LEVELSKILL_NAME3 = $arr_content[$data_count][LEVELSKILL_NAME3];$SPS_EMPHASIZE3 = $arr_content[$data_count][SPS_EMPHASIZE3];$SPS_FLAG3  = $arr_content[$data_count][SPS_FLAG3];*/    
                        }else{
                                $SS_CODE = $arr_content[SS_CODE];
                                $REF_CODE = $arr_content[REF_CODE];
                                $LEVELSKILL_NAME = $arr_content[LEVELSKILL_NAME];
                                $SPS_EMPHASIZE = $arr_content[SPS_EMPHASIZE];
                                $SPS_FLAG  = $arr_content[SPS_FLAG];
                                $SS_CODE2 = $arr_content[SS_CODE2];
                                $REF_CODE2 = $arr_content[REF_CODE2];
                                $LEVELSKILL_NAME2 = $arr_content[LEVELSKILL_NAME2];
                                $SPS_EMPHASIZE2 = $arr_content[SPS_EMPHASIZE2];
                                $SPS_FLAG2  = $arr_content[SPS_FLAG2];/*$SS_CODE3 = $arr_content[SS_CODE3];$REF_CODE3 = $arr_content[REF_CODE3];$LEVELSKILL_NAME3 = $arr_content[LEVELSKILL_NAME3];$SPS_EMPHASIZE3 = $arr_content[SPS_EMPHASIZE3];$SPS_FLAG3  = $arr_content[SPS_FLAG3];*/    
                        }//echo "= SS_NAME =".$SS_NAME."<br>";
			$PROMOTEDATE = $arr_content[$data_count][promotedate];
			$EFFECTIVEDATE = $POH_EFFECTIVEDATE;//echo "=>".$SS_CODE."<br>";// เช็คจบที่ข้อมูล $data_limit
			if ($data_count > 0 && ($data_count % $file_limit) == 0) {//				echo "$data_count>>$xls_fname>>$fname1<br>";
				$workbook->close();
				$arr_file[] = $fname1;
				$fnum++;
				$fname1=$fname."_$fnum.xls";
				$workbook = new writeexcel_workbook($fname1);
				//====================== SET FORMAT ======================//
				require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
				//====================== SET FORMAT ======================//
				$f_new = true;
			}// เช็คจบที่ข้อมูล $data_limit
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
				//====================== SET FORMAT ======================// //require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file//====================== SET FORMAT ======================//
				$arr_title = explode("||", "$ORG_NAME_REF||$report_title");
				for($i=0; $i<count($arr_title); $i++){
					$xlsRow = $i;
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 43, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                        $worksheet->write($xlsRow, 44, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 45, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                        $worksheet->write($xlsRow, 46, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 47, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                        $worksheet->write($xlsRow, 48, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 49, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                        $worksheet->write($xlsRow, 50, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 51, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                        $worksheet->write($xlsRow, 52, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*  $worksheet->write($xlsRow, 53, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 54, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 55, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 56, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 57, "", set_format("xlsFmtTitle", "B", "C", "", 1));*/    
				} // end if
				if($company_name){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 16, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 17, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 18, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 19, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 20, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 21, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 22, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 23, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 24, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 25, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 26, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 27, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 28, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 29, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 30, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 31, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 32, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 33, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 34, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 35, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 36, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 37, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 38, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 39, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 40, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 41, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 42, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 43, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                        $worksheet->write($xlsRow, 44, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 45, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                        $worksheet->write($xlsRow, 46, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 47, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                        $worksheet->write($xlsRow, 48, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 49, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                        $worksheet->write($xlsRow, 50, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 51, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                        $worksheet->write($xlsRow, 52, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));/*  $worksheet->write($xlsRow, 53, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 54, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 55, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 56, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 57, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));*/    
				} // end if
				print_header();
			} // end if
			if(!$f_org_ref_skip){ // ถ้ารายการนี้มี $REPORT_ORDER == "ORG_REF" จะไม่พิมพ์รายละเอียดข้างล่าง
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$OT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$POS_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$POSITION_TYPE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$CL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "$SKILL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "$CT_NAME_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "$PV_NAME_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, "$POS_STATUS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14, "$CLASS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15, "$PN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 16, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 17, "$PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 18, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 19, "$PER_GENDER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 20, "$PER_DISABILITY_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 21, "$RE_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 22, "$BIRTHDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 23, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 24, "$PER_MGTSALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 25, "$EL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 26, "$EN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 27, "$EM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 28, "$INS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 29, "$CT_NAME_EDU", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 30, "$ST_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 31, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 32, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 33, "$STARTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 34, "$FLOWDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 35, "$RESIGNDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 36, "$PROMOTEDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 37, "$DC_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 38, "$UNION_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 39, "$RESULT1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 40, "$PERCENT_SALARY1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 41, "$RESULT2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 42, "$PERCENT_SALARY2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 43, "$SS_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 44, "$REF_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 45, "$LEVELSKILL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 46, "$SPS_EMPHASIZE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 47, "$SPS_FLAG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 48, "$SS_CODE2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 49, "$REF_CODE2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 50, "$LEVELSKILL_NAME2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 51, "$SPS_EMPHASIZE2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 52, "$SPS_FLAG2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*  $worksheet->write_string($xlsRow, 53, "$SS_CODE3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));$worksheet->write_string($xlsRow, 54, "$REF_CODE3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));$worksheet->write_string($xlsRow, 55, "$LEVELSKILL_NAME3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));$worksheet->write_string($xlsRow, 56, "$SPS_EMPHASIZE3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));$worksheet->write_string($xlsRow, 57, "$SPS_FLAG3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));*/    
				$cmd1 = " SELECT TEMPPOSITIONNO FROM GMIS_GPIS WHERE TEMPPOSITIONNO = '$POS_NO' ";
				$db_dpis->send_cmd($cmd1);
				$data1 = $db_dpis->get_array();
				$repeat_POS_NO = $data1[TEMPPOSITIONNO]; //error because TEMPLEVEL set 10 over 
				if(!$repeat_POS_NO){
                                    $cmd = " insert into GMIS_GPIS (TEMPMINISTRY, TEMPORGANIZE, TEMPDIVISIONNAME, TEMPORGANIZETYPE, TEMPPOSITIONNO, TEMPMANAGEPOSITION, 
                                                                    TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, TEMPCLNAME, TEMPSKILL, TEMPCOUNTRY, TEMPPROVINCE, TEMPPOSITIONSTATUS, TEMPCLASS, 
                                                                    TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPCARDNO, TEMPGENDER, TEMPSTATUSDISABILITY, TEMPRELIGION, TEMPBIRTHDATE, 
                                                                    TEMPSALARY, TEMPPOSITIONSALARY, TEMPEDUCATIONLEVEL, TEMPEDUCATIONNAME, TEMPEDUCATIONMAJOR, TEMPGRADUATED, TEMPEDUCATIONCOUNTRY, 
                                                                    TEMPSCHOLARTYPE, TEMPMOVEMENTTYPE, TEMPMOVEMENTDATE, TEMPSTARTDATE, TEMPFLOWDATE, TEMPRESIGNDATE, TEMPPROMOTEDATE,
                                                                    TEMPDECORATION, TEMPUNION, TEMPRESULT1, TEMPPERCENTSALARY1, TEMPRESULT2, TEMPPERCENTSALARY2,TEMPMPERSPSKILL1,TEMPSPERSPSKILL1,TEMPSKILLLEVEL1,TEMPPERSPSKILLDES1,TEMPSPS_FLAG1,
                                                                    TEMPMPERSPSKILL2,TEMPSPERSPSKILL2,TEMPSKILLLEVEL2,TEMPPERSPSKILLDES2,TEMPSPS_FLAG2,TEMPMPERSPSKILL3,TEMPSPERSPSKILL3,TEMPSKILLLEVEL3,TEMPPERSPSKILLDES3,TEMPSPS_FLAG3)
                                                                    values ('$ORG_NAME', '$ORG_NAME_1', '$ORG_NAME_2', '$OT_NAME', '$POS_NO', '$PM_NAME', '$PL_NAME', '$POSITION_TYPE', '$LEVEL_NO', 
                                                                    '$CL_NAME', '$SKILL_NAME', '$CT_NAME_ORG', '$PV_NAME_ORG', '$POS_STATUS', '$CLASS_NAME', '$PN_NAME', '$PER_NAME', 
                                                                    '$PER_SURNAME', '$PER_CARDNO', '$PER_GENDER_NAME', '$PER_DISABILITY_NAME', '$RE_NAME', '$BIRTHDATE', '$PER_SALARY', 
                                                                    '$PER_MGTSALARY', '$EL_NAME', '$EN_NAME', '".save_quote($EM_NAME)."', '".save_quote($INS_NAME)."', '$CT_NAME_EDU', '$ST_NAME', '$MOV_NAME', '$EFFECTIVEDATE', 
                                                                    '$STARTDATE', '$FLOWDATE', '$RESIGNDATE', '$PROMOTEDATE', '$DC_NAME', '$UNION_CODE', '$RESULT1', '$PERCENT_SALARY1', 
                                                                    '$RESULT2', '$PERCENT_SALARY2','$SS_CODE','$REF_CODE','$LEVELSKILL_NAME','$SPS_EMPHASIZE','$SPS_FLAG','$SS_CODE2','$REF_CODE2','$LEVELSKILL_NAME2','$SPS_EMPHASIZE2','$SPS_FLAG2','','','','','') ";
                                    $db_dpis->send_cmd($cmd);
                                    //echo "$POS_NO => cmd ="."<pre>".$cmd."<br><hr><br>";
				}//$db_dpis->show_error();
				$repeat_POS_NO = "";/* $cmd1 = " SELECT tempPositionNo FROM GMIS_GPIS WHERE tempPositionNo = '$POS_NO' "; $count_data = $db_dpis2->send_cmd($cmd1);if (!$count_data) {echo "$cmd<br>==================<br>";$db_dpis->show_error();echo "<br>end ". ++$i  ."=======================<br>";} */
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
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 43, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 44, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 45, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 46, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 47, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 48, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 49, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 50, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 51, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 52, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 53, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 54, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
<?php  include("rpt_gmis_xls2.php"); ?>