<?php
        /*$varz = array(array("aaa"=>"1"),array("aaa"=>"2"));
        $i=0;
        while($i < count($varz)){
            echo $varz[$i][aaa];
            $i++;
        }
        exit();*/
        //=============================================== test ===========================================================
	$time1 = time();
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
        ini_set('display_errors', 1);
        error_reporting(E_USER_ERROR);
        
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
        set_time_limit(0);
	ini_set("max_execution_time", 0);//ini_set("max_execution_time", $max_execution_time);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
        $cmd = " TRUNCATE TABLE GMIS_GPIS_FLOW_IN ";
	$db_dpis->send_cmd($cmd);//$db_dpis->show_error();
	$cmd = " TRUNCATE TABLE GMIS_GPIS_FLOW_OUT ";
	$db_dpis->send_cmd($cmd);//$db_dpis->show_error();
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
        if(count($arr_search_condition)){ $search_condition = " where ". implode(" and ", $arr_search_condition);}
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
	
	//$data_count = $data_row = 0;
	$file_limit = $file_limits;//50000;//$file_limit = 1200;
	$data_limit = $file_limits;//50000;
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
        
        $strSQL = "SELECT a.*,'' as STATUS_ERR FROM GMIS_GPIS a ORDER BY TO_NUMBER(REPLACE(a.TEMPPOSITIONNO,'-','')) ASC";
        $count_data = $db_dpis->send_cmd($strSQL);
        $arrdata= array();
        while ($data = $db_dpis->get_array()) {
            array_push($arrdata, $data);
        }
        //var_dump($arrdata);
        
        $filename = '../../Excel/tmp/results.txt';
        $get_arr_data = file_get_contents($filename);
       // echo $get_arr_data;
        $array_data = json_decode($get_arr_data, true);
        //var_dump($array_data);
        foreach ($array_data as $key => $value) {
            array_push($arrdata, $array_data[$key]);
        }
        //$count_data = $db_dpis->send_cmd($strSQL);
	$data_count = $data_row = 0;
        $f_org_ref_skip = false;		// มีไว้เพื่อใช้เช็ค  $REPORT_ORDER == "ORG_REF";
        $REPORT_ORDER = "ORG_REF";
	if(count($arrdata)>0){
            $xlsRow = 0;
            $count_org_ref = 0;

            $idx_count=0;
            $chk_createhead_texterr = 0;
            $data = $arrdata;
            while($idx_count<count($data)){
                if($data[$idx_count][STATUS_ERR]=="ERROR"){
                    $ORG_NAME_REF = @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPMINISTRY]));
                    $ORG_NAME  =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPMINISTRY]));
                    $ORG_NAME_1 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPORGANIZE]));
                    $ORG_NAME_2 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPDIVISIONNAME]));
                    $OT_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPORGANIZETYPE]));
                    $POS_NO =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPOSITIONNO]));
                    $PM_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPMANAGEPOSITION]));
                    $PL_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPLINE]));
                    $POSITION_TYPE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPOSITIONTYPE]));
                    if($POSITION_TYPE=='บริหาร' || $POSITION_TYPE=='อำนวยการ'){
                        $LEVEL_NAME =  'ประเภท'.$POSITION_TYPE.' ระดับ'.str_replace($POSITION_TYPE,"",@iconv("utf-8","tis-620",trim($data[$idx_count][TEMPLEVEL])));
                    }else{
                        $LEVEL_NAME =  'ประเภท'.$POSITION_TYPE.' ระดับ'.@iconv("utf-8","tis-620",trim($data[$idx_count][TEMPLEVEL]));
                    }
                    $CL_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPCLNAME]));
                    $SKILL_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSKILL]));
                    $CT_NAME_ORG =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPCOUNTRY]));
                    $PV_NAME_ORG =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPROVINCE]));
                    $POS_STATUS =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPOSITIONSTATUS]));
                    $CLASS_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPCLASS]));
                    $PN_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPRENAME]));
                    $PER_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPFIRSTNAME]));
                    $PER_SURNAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPLASTNAME]));
                    $PER_CARDNO =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPCARDNO]));
                    $PER_GENDER_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPGENDER]));
                    $PER_DISABILITY_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSTATUSDISABILITY]));
                    $RE_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPRELIGION]));
                    $BIRTHDATE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPBIRTHDATE]));
                    $PER_SALARY =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSALARY]));
                    $PER_MGTSALARY =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPOSITIONSALARY]));
                    $EL_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPEDUCATIONLEVEL]));
                    $EN_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPEDUCATIONNAME]));
                    $EM_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPEDUCATIONMAJOR]));
                    $INS_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPGRADUATED]));
                    $CT_NAME_EDU =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPEDUCATIONCOUNTRY]));
                    $ST_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSCHOLARTYPE]));
                    $MOV_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPMOVEMENTTYPE]));
                    $EFFECTIVEDATE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPMOVEMENTDATE]));
                    $STARTDATE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSTARTDATE]));
                    $FLOWDATE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPFLOWDATE]));
                    $RESIGNDATE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPRESIGNDATE]));
                    $PROMOTEDATE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPROMOTEDATE]));
                    $DC_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPDECORATION]));
                    $UNION_CODE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPUNION]));
                    $RESULT1 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPRESULT1]));
                    $PERCENT_SALARY1 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPERCENTSALARY1]));
                    $RESULT2 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPRESULT2]));
                    $PERCENT_SALARY2 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPERCENTSALARY2]));
                    $SS_CODE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPMPERSPSKILL1]));
                    $REF_CODE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSPERSPSKILL1]));
                    $LEVELSKILL_NAME =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSKILLLEVEL1]));
                    $SPS_EMPHASIZE =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPERSPSKILLDES1]));
                    $SPS_FLAG =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSPS_FLAG1]));
                    $SS_CODE2 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPMPERSPSKILL2]));
                    $REF_CODE2 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSPERSPSKILL2]));
                    $LEVELSKILL_NAME2 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSKILLLEVEL2]));
                    $SPS_EMPHASIZE2 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPPERSPSKILLDES2]));
                    $SPS_FLAG2 =  @iconv("utf-8","tis-620",trim($data[$idx_count][TEMPSPS_FLAG2]));
                }else{
                    $ORG_NAME_REF = trim($data[$idx_count][TEMPMINISTRY]);
                    $ORG_NAME  =  trim($data[$idx_count][TEMPMINISTRY]);
                    $ORG_NAME_1 =  trim($data[$idx_count][TEMPORGANIZE]);
                    $ORG_NAME_2 =  trim($data[$idx_count][TEMPDIVISIONNAME]);
                    $OT_NAME =  trim($data[$idx_count][TEMPORGANIZETYPE]);
                    $POS_NO =  trim($data[$idx_count][TEMPPOSITIONNO]);
                    $PM_NAME =  trim($data[$idx_count][TEMPMANAGEPOSITION]);
                    $PL_NAME =  trim($data[$idx_count][TEMPLINE]);
                    $POSITION_TYPE =  trim($data[$idx_count][TEMPPOSITIONTYPE]);
                    if($POSITION_TYPE=='บริหาร' || $POSITION_TYPE=='อำนวยการ'){
                        $LEVEL_NAME =  'ประเภท'.$POSITION_TYPE.' ระดับ'.str_replace($POSITION_TYPE,"",trim($data[$idx_count][TEMPLEVEL]));
                    }else{
                        $LEVEL_NAME =  'ประเภท'.$POSITION_TYPE.' ระดับ'.trim($data[$idx_count][TEMPLEVEL]);
                    }
                    $CL_NAME =  trim($data[$idx_count][TEMPCLNAME]);
                    $SKILL_NAME =  trim($data[$idx_count][TEMPSKILL]);
                    $CT_NAME_ORG =  trim($data[$idx_count][TEMPCOUNTRY]);
                    $PV_NAME_ORG =  trim($data[$idx_count][TEMPPROVINCE]);
                    $POS_STATUS =  trim($data[$idx_count][TEMPPOSITIONSTATUS]);
                    $CLASS_NAME =  trim($data[$idx_count][TEMPCLASS]);
                    $PN_NAME =  trim($data[$idx_count][TEMPPRENAME]);
                    $PER_NAME =  trim($data[$idx_count][TEMPFIRSTNAME]);
                    $PER_SURNAME =  trim($data[$idx_count][TEMPLASTNAME]);
                    $PER_CARDNO =  trim($data[$idx_count][TEMPCARDNO]);
                    $PER_GENDER_NAME =  trim($data[$idx_count][TEMPGENDER]);
                    $PER_DISABILITY_NAME =  trim($data[$idx_count][TEMPSTATUSDISABILITY]);
                    $RE_NAME =  trim($data[$idx_count][TEMPRELIGION]);
                    $BIRTHDATE =  trim($data[$idx_count][TEMPBIRTHDATE]);
                    $PER_SALARY =  trim($data[$idx_count][TEMPSALARY]);
                    $PER_MGTSALARY =  trim($data[$idx_count][TEMPPOSITIONSALARY]);
                    $EL_NAME =  trim($data[$idx_count][TEMPEDUCATIONLEVEL]);
                    $EN_NAME =  trim($data[$idx_count][TEMPEDUCATIONNAME]);
                    $EM_NAME =  trim($data[$idx_count][TEMPEDUCATIONMAJOR]);
                    $INS_NAME =  trim($data[$idx_count][TEMPGRADUATED]);
                    $CT_NAME_EDU =  trim($data[$idx_count][TEMPEDUCATIONCOUNTRY]);
                    $ST_NAME =  trim($data[$idx_count][TEMPSCHOLARTYPE]);
                    $MOV_NAME =  trim($data[$idx_count][TEMPMOVEMENTTYPE]);
                    $EFFECTIVEDATE =  trim($data[$idx_count][TEMPMOVEMENTDATE]);
                    $STARTDATE =  trim($data[$idx_count][TEMPSTARTDATE]);
                    $FLOWDATE =  trim($data[$idx_count][TEMPFLOWDATE]);
                    $RESIGNDATE =  trim($data[$idx_count][TEMPRESIGNDATE]);
                    $PROMOTEDATE =  trim($data[$idx_count][TEMPPROMOTEDATE]);
                    $DC_NAME =  trim($data[$idx_count][TEMPDECORATION]);
                    $UNION_CODE =  trim($data[$idx_count][TEMPUNION]);
                    $RESULT1 =  trim($data[$idx_count][TEMPRESULT1]);
                    $PERCENT_SALARY1 =  trim($data[$idx_count][TEMPPERCENTSALARY1]);
                    $RESULT2 =  trim($data[$idx_count][TEMPRESULT2]);
                    $PERCENT_SALARY2 =  trim($data[$idx_count][TEMPPERCENTSALARY2]);
                    $SS_CODE =  trim($data[$idx_count][TEMPMPERSPSKILL1]);
                    $REF_CODE =  trim($data[$idx_count][TEMPSPERSPSKILL1]);
                    $LEVELSKILL_NAME =  trim($data[$idx_count][TEMPSKILLLEVEL1]);
                    $SPS_EMPHASIZE =  trim($data[$idx_count][TEMPPERSPSKILLDES1]);
                    $SPS_FLAG =  trim($data[$idx_count][TEMPSPS_FLAG1]);
                    $SS_CODE2 =  trim($data[$idx_count][TEMPMPERSPSKILL2]);
                    $REF_CODE2 =  trim($data[$idx_count][TEMPSPERSPSKILL2]);
                    $LEVELSKILL_NAME2 =  trim($data[$idx_count][TEMPSKILLLEVEL2]);
                    $SPS_EMPHASIZE2 =  trim($data[$idx_count][TEMPPERSPSKILLDES2]);
                    $SPS_FLAG2 =  trim($data[$idx_count][TEMPSPS_FLAG2]);
                }
                   
                
                // เช็คจบที่ข้อมูล $data_limit
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
                    $REPORT_ORDER = "CONTENT";
                    if ($f_new) {
                        $sheet_no=0; $sheet_no_text="";
                        if($data_count > 0) { $count_org_ref++;}
                        $f_new = false;
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
                            $worksheet->write($xlsRow, 52, "", set_format("xlsFmtTitle", "B", "C", "", 1));// $worksheet->write($xlsRow, 53, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 54, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 55, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 56, "", set_format("xlsFmtTitle", "B", "C", "", 1));$worksheet->write($xlsRow, 57, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
                            $worksheet->write($xlsRow, 52, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 53, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 54, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 55, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 56, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));$worksheet->write($xlsRow, 57, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                    } // end if
                    print_header();
                } // end if
                if(!$f_org_ref_skip){ // ถ้ารายการนี้มี $REPORT_ORDER == "ORG_REF" จะไม่พิมพ์รายละเอียดข้างล่าง
                        $xlsRow++;
                        /*if($data[$idx_count][STATUS_ERR]=="ERROR" && $chk_createhead_texterr==0){
                            //set_format($fmtName, $fontFormat="", $alignment="C", $border="", $isMerge=0, $wrapText=0, $rotate=0, $color="", $bgcolor="")
                            $worksheet->write($xlsRow, 0, "Data Error Not Insert To Database", set_format("xlsFmtTitle", "B", "C", "", 1, 0, 0, "#FF6666", ""));
                            $chk_createhead_texterr=1;
                            $xlsRow++;
                        }*/
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
                        $worksheet->write_string($xlsRow, 52, "$SPS_FLAG2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                } // end if
                $idx_count++;
                $f_org_ref_skip = false;$data_count++;
            } 			
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