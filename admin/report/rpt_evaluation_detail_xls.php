<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($ORG_ID){
		$arr_search_condition[] = "(c.ORG_ID = $ORG_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME - $ORG_NAME";
	}elseif($DEPARTMENT_ID){
		$arr_search_condition[] = "(d.ORG_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$arr_search_condition[] = "(e.ORG_ID = $MINISTRY_ID)";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(c.PV_CODE = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if	
	if($search_pl_code){
		$arr_search_condition[] = "(b.PL_CODE = '$search_pl_code')";
	} // end if
	if($search_lv_code){
		$arr_search_condition[] = "(b.LEVEL_NO = '$search_lv_code')";
	} // end if
	$search_month = str_pad($search_month, 2, "0", STR_PAD_LEFT);
	$search_year = str_pad($search_year, 4, "0", STR_PAD_LEFT);

	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = " 	select		a.JOB_EVA_ID, b.POS_ID, b.POS_NO, 
											b.PM_CODE, b.PL_CODE, b.CL_NAME, b.LEVEL_NO,
											e.ORG_NAME as MINISTRY_NAME, d.ORG_NAME as DEPARTMENT_NAME, 
											c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2, b.POS_STATUS,
											c.AP_CODE, c.PV_CODE, c.CT_CODE, c.ORG_JOB, a.TESTER_ID, a.TEST_TIME, a.SCORE, a.CONSISTENCY
							from		(
												(
													(
														JOB_EVALUATION a
														inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
													) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
											) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
							where		a.ISPASSED='Y'
											$search_condition
							order by	e.ORG_SEQ_NO, e.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, 
											c.ORG_SEQ_NO, c.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, cLng(b.POS_NO) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " 	select		a.JOB_EVA_ID, b.POS_ID, b.POS_NO, 
											b.PM_CODE, b.PL_CODE, b.CL_NAME, b.LEVEL_NO,
											e.ORG_NAME as MINISTRY_NAME, d.ORG_NAME as DEPARTMENT_NAME, 
											c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2, b.POS_STATUS,
											c.AP_CODE, c.PV_CODE, c.CT_CODE, c.ORG_JOB, a.TESTER_ID, a.TEST_TIME, a.SCORE, a.CONSISTENCY
							from		JOB_EVALUATION a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e
							where		a.ISPASSED='Y' and a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID 
											and c.ORG_ID_REF=d.ORG_ID_REF and d.ORG_ID_REF=e.ORG_ID_REF
											$search_condition
							order by	e.ORG_SEQ_NO, e.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, 
											c.ORG_SEQ_NO, c.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, to_number(replace(b.POS_NO,'-','')) ";
	}elseif($DPISDB=="mssql"){
		$cmd = " 	select		a.JOB_EVA_ID, b.POS_ID, b.POS_NO, 
											b.PM_CODE, b.PL_CODE, b.CL_NAME, b.LEVEL_NO,
											e.ORG_NAME as MINISTRY_NAME, d.ORG_NAME as DEPARTMENT_NAME, 
											c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2, b.POS_STATUS,
											c.AP_CODE, c.PV_CODE, c.CT_CODE, c.ORG_JOB, a.TESTER_ID, a.TEST_TIME, a.SCORE, a.CONSISTENCY
							from		(
												(
													(
														JOB_EVALUATION a
														inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
													) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
											) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
							where		a.ISPASSED='Y'
											$search_condition
							order by	e.ORG_SEQ_NO, e.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, 
											c.ORG_SEQ_NO, c.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_NO ";
	}
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;

	while($data = $db_dpis->get_array()){
		$JOB_EVA_ID = $data[JOB_EVA_ID];
		$POS_ID = $data[POS_ID];
		$POS_NO = trim($data[POS_NO]);
		$PL_CODE = trim($data[PL_CODE]);
		$CL_NAME = trim($data[CL_NAME]);
		$PM_CODE = trim($data[PM_CODE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$MINISTRY_NAME = trim($data[MINISTRY_NAME]);
		$DEPARTMENT_NAME = trim($data[DEPARTMENT_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$ORG_ID_2 = trim($data[ORG_ID_2]);
		$AP_CODE = trim($data[AP_CODE]);
		$PV_CODE = trim($data[PV_CODE]);
		$CT_CODE = trim($data[CT_CODE]);
		$ORG_JOB = trim($data[ORG_JOB]);
	
		$SCORE = trim($data[SCORE]);
		$CONSISTENCY = trim($data[CONSISTENCY]);
		$TESTER_ID = $data[TESTER_ID];
		$TEST_TIME = substr($data[TEST_TIME], 0, 10);
		$arr_temp = explode("-", $TEST_TIME);
		$TEST_TIME = ($arr_temp[2].$arr_temp[1]);
		$SHOW_TIME = ($arr_temp[0] + 0) ." ". $month_abbr[($arr_temp[1] + 0)]["TH"] ." ". $arr_temp[2];
				
		if( $TEST_TIME >= ($search_year.$search_month) ){
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='".$PL_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PL_NAME = $data_dpis2[PL_NAME];
		
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='".$PM_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PM_NAME = $data_dpis2[PM_NAME];
			
			if($LEVEL_NO == "NOT SPECIFY" || !$LEVEL_NO){ 
				$LEVEL_NAME = "ยังไม่มีการกำหนดตำแหน่งงานใหม่";
			}elseif($LEVEL_NO){
				$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
			} // end if

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data_dpis2[ORG_NAME];
		
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_2 = $data_dpis2[ORG_NAME];
			
			$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='".$AP_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$AP_NAME = $data_dpis2[AP_NAME];
		
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='".$PV_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PV_NAME = $data_dpis2[PV_NAME];
		
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='".$CT_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$CT_NAME = $data_dpis2[CT_NAME];
	
			$cmd = " select FULLNAME from USER_DETAIL where ID=$TESTER_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TESTER_NAME = $data2[FULLNAME];
						
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$EVALUATE_LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EVALUATE_LEVEL_NAME = $data2[LEVEL_NAME];
	
			$arr_content[$data_count][JOB_EVA_ID] = $JOB_EVA_ID;
			$arr_content[$data_count][POS_NO] = $POS_NO;
			$arr_content[$data_count][PM_NAME] = $PM_NAME;
			$arr_content[$data_count][PL_NAME] = $PL_NAME;
			$arr_content[$data_count][CL_NAME] = $CL_NAME;
			$arr_content[$data_count][LEVEL_NO] = $LEVEL_NAME;
			$arr_content[$data_count][MINISTRY_NAME] = $MINISTRY_NAME;
			$arr_content[$data_count][DEPARTMENT_NAME] = $DEPARTMENT_NAME;
			$arr_content[$data_count][ORG_NAME] = $ORG_NAME;
			$arr_content[$data_count][ORG_NAME_1] = $ORG_NAME_1;
			$arr_content[$data_count][ORG_NAME_2] = $ORG_NAME_2;
			$arr_content[$data_count][AP_NAME] = $AP_NAME;
			$arr_content[$data_count][PV_NAME] = $PV_NAME;
			$arr_content[$data_count][CT_NAME] = $CT_NAME;
			$arr_content[$data_count][ORG_JOB] = $ORG_JOB;
			$arr_content[$data_count][TESTER_NAME] = $TESTER_NAME;
			$arr_content[$data_count][TEST_TIME] = $SHOW_TIME;
			
			// ========================= EVALUATION ANALYSIS ===========================
			$cmd = " select		PIORITY, WORKLOAD, KNOWLEDGE, SKILL, EXP, COMPETENCY,
											ACCOUNTABILITY1, ACCOUNTABILITY2, ACCOUNTABILITY3, ACCOUNTABILITY4,
											ACCOUNTABILITY5, ACCOUNTABILITY6, ACCOUNTABILITY7, ACCOUNTABILITY8
							 from		JOB_ANALYSIS
							 where	JOB_EVA_ID=$JOB_EVA_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PIORITY = trim($data2[PIORITY]);
			$WORKLOAD = trim($data2[WORKLOAD]);
			$KNOWLEDGE = trim($data2[KNOWLEDGE]);
			$SKILL = trim($data2[SKILL]);
			$EXP = trim($data2[EXP]);
			$COMPETENCY = trim($data2[COMPETENCY]);
			$ACCOUNTABILITY1 = trim($data2[ACCOUNTABILITY1]);
			$ACCOUNTABILITY2 = trim($data2[ACCOUNTABILITY2]);
			$ACCOUNTABILITY3 = trim($data2[ACCOUNTABILITY3]);
			$ACCOUNTABILITY4 = trim($data2[ACCOUNTABILITY4]);
			$ACCOUNTABILITY5 = trim($data2[ACCOUNTABILITY5]);
			$ACCOUNTABILITY6 = trim($data2[ACCOUNTABILITY6]);
			$ACCOUNTABILITY7 = trim($data2[ACCOUNTABILITY7]);
			$ACCOUNTABILITY8 = trim($data2[ACCOUNTABILITY8]);

			$arr_analysis[$JOB_EVA_ID][PIORITY] = $PIORITY;
			$arr_analysis[$JOB_EVA_ID][WORKLOAD] = $WORKLOAD;
			$arr_analysis[$JOB_EVA_ID][KNOWLEDGE] = $KNOWLEDGE;
			$arr_analysis[$JOB_EVA_ID][SKILL] = $SKILL;
			$arr_analysis[$JOB_EVA_ID][EXP] = $EXP;
			$arr_analysis[$JOB_EVA_ID][COMPETENCY] = $COMPETENCY;
			$arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY1] = $ACCOUNTABILITY1;
			$arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY2] = $ACCOUNTABILITY2;
			$arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY3] = $ACCOUNTABILITY3;
			$arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY4] = $ACCOUNTABILITY4;
			$arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY5] = $ACCOUNTABILITY5;
			$arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY6] = $ACCOUNTABILITY6;
			$arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY7] = $ACCOUNTABILITY7;
			$arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY8] = $ACCOUNTABILITY8;

			// ========================= EVALUATION RESULT ===========================
			$cmd = " select 	ISPASSED_FIRST, REASON_FIRST, APPROVE_FIRST_BY, APPROVE_FIRST_TIME,
											ISPASSED_SECOND, REASON_SECOND, APPROVE_SECOND_BY, APPROVE_SECOND_TIME
							 from 		JOB_EVALUATION_APPROVED 
							 where 	JOB_EVA_ID=$JOB_EVA_ID ";
			$db_dpis2->send_cmd($cmd);
			$data = $db_dpis2->get_array();
			$ISPASSED_FIRST = trim($data2[ISPASSED_FIRST]);
			$REASON_FIRST = trim($data2[REASON_FIRST]);
			$APPROVE_FIRST_BY = trim($data2[APPROVE_FIRST_BY]);
			$APPROVE_FIRST_TIME = trim($data2[APPROVE_FIRST_TIME]);
		
			$ISPASSED_SECOND = trim($data2[ISPASSED_SECOND]);
			$REASON_SECOND = trim($data2[REASON_SECOND]);
			$APPROVE_SECOND_BY = trim($data2[APPROVE_SECOND_BY]);
			$APPROVE_SECOND_TIME = trim($data2[APPROVE_SECOND_TIME]);

			$arr_result[$JOB_EVA_ID][ISPASSED_FIRST] = $ISPASSED_FIRST;
			$arr_result[$JOB_EVA_ID][REASON_FIRST] = $REASON_FIRST;
			$arr_result[$JOB_EVA_ID][APPROVE_FIRST_BY] = $APPROVE_FIRST_BY;
			$arr_result[$JOB_EVA_ID][APPROVE_FIRST_TIME] = $APPROVE_FIRST_TIME;

			$arr_result[$JOB_EVA_ID][ISPASSED_SECOND] = $ISPASSED_SECOND;
			$arr_result[$JOB_EVA_ID][REASON_SECOND] = $REASON_SECOND;
			$arr_result[$JOB_EVA_ID][APPROVE_SECOND_BY] = $APPROVE_SECOND_BY;
			$arr_result[$JOB_EVA_ID][APPROVE_SECOND_TIME] = $APPROVE_SECOND_TIME;

			// ========================= EVALUATION SCORE ===========================
			$ARR_SCORE = explode(",", $SCORE);
			$KH1 = $ARR_SCORE[0];
			$KH2 = $ARR_SCORE[1];
			$KH3 = $ARR_SCORE[2];
			$KH_SCORE = $ARR_SCORE[3];
			
			$PS1 = $ARR_SCORE[4];
			$PS2 = $ARR_SCORE[5];
			$PS_SCORE = $ARR_SCORE[6];
			$PS_KH_SCORE = $ARR_SCORE[7];
			
			$ACC1 = $ARR_SCORE[8];
			$ACC2 = $ARR_SCORE[9];
			$ACC3 = $ARR_SCORE[10];
			$ACC_SCORE = $ARR_SCORE[11];
			
			$TOTAL_POINTS = $ARR_SCORE[12];
			$PROFILE_CHECK = $ARR_SCORE[13];
			$EVALUATE_LEVEL_NO = $ARR_SCORE[14];

			$arr_score[$JOB_EVA_ID][KH1] = $KH1;
			$arr_score[$JOB_EVA_ID][KH2] = $KH2;
			$arr_score[$JOB_EVA_ID][KH3] = $KH3;
			$arr_score[$JOB_EVA_ID][KH_SCORE] = $KH_SCORE;
	
			$arr_score[$JOB_EVA_ID][PS1] = $PS1;
			$arr_score[$JOB_EVA_ID][PS2] = $PS2;
			$arr_score[$JOB_EVA_ID][PS_SCORE] = $PS_SCORE;
			$arr_score[$JOB_EVA_ID][PS_KH_SCORE] = $PS_KH_SCORE;

			$arr_score[$JOB_EVA_ID][ACC1] = $ACC1;
			$arr_score[$JOB_EVA_ID][ACC2] = $ACC2;
			$arr_score[$JOB_EVA_ID][ACC3] = $ACC3;
			$arr_score[$JOB_EVA_ID][ACC_SCORE] = $ACC_SCORE;

			$arr_score[$JOB_EVA_ID][TOTAL_POINTS] = $TOTAL_POINTS;
			$arr_score[$JOB_EVA_ID][PROFILE_CHECK] = $PROFILE_CHECK;
			$arr_score[$JOB_EVA_ID][EVALUATE_LEVEL_NO] = $EVALUATE_LEVEL_NAME;

			$ARR_CONSISTENCY = explode(",", $CONSISTENCY);
			$KH1_CONSISTENCY = $ARR_CONSISTENCY[0];
			$KH2_CONSISTENCY = $ARR_CONSISTENCY[1];
			$KH3_CONSISTENCY = $ARR_CONSISTENCY[2];
			$KH_CONSISTENCY = $ARR_CONSISTENCY[3];	
		
			$PS1_CONSISTENCY = $ARR_CONSISTENCY[4];
			$PS2_CONSISTENCY = $ARR_CONSISTENCY[5];
			$PS_CONSISTENCY = $ARR_CONSISTENCY[6];	
			
			$PC_CONSISTENCY = $ARR_CONSISTENCY[7];

			$arr_consistency_result[$JOB_EVA_ID][KH1] = $KH1_CONSISTENCY;
			$arr_consistency_result[$JOB_EVA_ID][KH2] = $KH2_CONSISTENCY;
			$arr_consistency_result[$JOB_EVA_ID][KH3] = $KH3_CONSISTENCY;
			$arr_consistency_result[$JOB_EVA_ID][KH] = $KH_CONSISTENCY;

			$arr_consistency_result[$JOB_EVA_ID][PS1] = $PS1_CONSISTENCY;
			$arr_consistency_result[$JOB_EVA_ID][PS2] = $PS2_CONSISTENCY;
			$arr_consistency_result[$JOB_EVA_ID][PS] = $PS_CONSISTENCY;

			$arr_consistency_result[$JOB_EVA_ID][PC] = $PC_CONSISTENCY;

			$cmd = " select 		a.QUESTION_ID, b.TOPIC, c.ANSWER_INFO, a.DESCRIPTION 
							from 		JOB_EVALUATION_ANSWER_HISTORY a, JEM_QUESTION_INFO b, JEM_ANSWER_INFO c
							where 		a.JOB_EVA_ID=$JOB_EVA_ID and a.QUESTION_ID=b.ID 
											and a.QUESTION_ID=c.QUESTION_NO and a.ANSWER_ID=c.ID
							order by 	b.ID ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$QUESTION_ID = $data2[QUESTION_ID];
				$QUESTION_TOPIC = trim($data2[TOPIC]);
				$ANSWER_INFO = trim($data2[ANSWER_INFO]);
				$ANSWER_DESCRIPTION = trim($data2[DESCRIPTION]);
				
				$arr_answer[$JOB_EVA_ID][$QUESTION_ID][QUESTION_TOPIC] = $QUESTION_TOPIC;
				$arr_answer[$JOB_EVA_ID][$QUESTION_ID][ANSWER_INFO] = $ANSWER_INFO;
				$arr_answer[$JOB_EVA_ID][$QUESTION_ID][ANSWER_DESCRIPTION] = $ANSWER_DESCRIPTION;
			} // loop while

			$data_count++;
		} // end if
	} // end while
	
	$count_data = count($arr_content);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	echo "<pre>"; print_r($arr_analysis); echo "</pre>";
//	echo "<pre>"; print_r($arr_result); echo "</pre>";
//	echo "<pre>"; print_r($arr_score); echo "</pre>";
//	echo "<pre>"; print_r($arr_answer); echo "</pre>";
	
	$company_name = "";
	$report_title = "";
	$report_code = "รายละเอียดการปรับปรุงการกำหนดตำแหน่ง";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/jethro_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format or set_format_new funtion , help is in file
	//====================== SET FORMAT ======================//

	if($count_data){
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$JOB_EVA_ID = $arr_content[$data_count][JOB_EVA_ID];
			$POS_NO = $arr_content[$data_count][POS_NO];
			$PM_NAME = $arr_content[$data_count][PM_NAME];
			$PL_NAME = $arr_content[$data_count][PL_NAME];
			$CL_NAME = $arr_content[$data_count][CL_NAME];
			$LEVEL_NO = $arr_content[$data_count][LEVEL_NO];
			$MINISTRY_NAME = $arr_content[$data_count][MINISTRY_NAME];
			$DEPARTMENT_NAME = $arr_content[$data_count][DEPARTMENT_NAME];
			$ORG_NAME = $arr_content[$data_count][ORG_NAME];
			$ORG_NAME_1 = $arr_content[$data_count][ORG_NAME_1];
			$ORG_NAME_2 = $arr_content[$data_count][ORG_NAME_2];
			$AP_NAME = $arr_content[$data_count][AP_NAME];
			$PV_NAME = $arr_content[$data_count][PV_NAME];
			$CT_NAME = $arr_content[$data_count][CT_NAME];
			$ORG_JOB = $arr_content[$data_count][ORG_JOB];
			$TESTER_NAME = $arr_content[$data_count][TESTER_NAME];
			$TEST_TIME = $arr_content[$data_count][TEST_TIME];

			$PIORITY = $arr_analysis[$JOB_EVA_ID][PIORITY];
			$WORKLOAD = $arr_analysis[$JOB_EVA_ID][WORKLOAD];
			$KNOWLEDGE = $arr_analysis[$JOB_EVA_ID][KNOWLEDGE];
			$SKILL = $arr_analysis[$JOB_EVA_ID][SKILL];
			$EXP = $arr_analysis[$JOB_EVA_ID][EXP];
			$COMPETENCY = $arr_analysis[$JOB_EVA_ID][COMPETENCY];
			$ACCOUNTABILITY1 = $arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY1];
			$ACCOUNTABILITY2 = $arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY2];
			$ACCOUNTABILITY3 = $arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY3];
			$ACCOUNTABILITY4 = $arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY4];
			$ACCOUNTABILITY5 = $arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY5];
			$ACCOUNTABILITY6 = $arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY6];
			$ACCOUNTABILITY7 = $arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY7];
			$ACCOUNTABILITY8 = $arr_analysis[$JOB_EVA_ID][ACCOUNTABILITY8];

			$worksheet = &$workbook->addworksheet("ข้อมูลตำแหน่ง ($POS_NO)");
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);
		
			$worksheet->set_column(0, 0, 8);
			$worksheet->set_column(1, 1, 12);
			$worksheet->set_column(2, 2, 12);	
			$worksheet->set_column(3, 3, 12);	
			$worksheet->set_column(4, 4, 12);	
			$worksheet->set_column(5, 5, 12);	
			$worksheet->set_column(6, 6, 12);	
			$worksheet->set_column(7, 7, 12);	
			$worksheet->set_column(8, 8, 12);	
			$worksheet->set_column(9, 9, 12);	
			$worksheet->set_column(10, 10, 12);	
			$worksheet->set_column(11, 11, 12);	
			$worksheet->set_column(12, 12, 12);	
			$worksheet->set_column(13, 13, 25);	
		
			$xlsRow = 0;
			$worksheet->set_row($xlsRow, 20);
			$worksheet->write($xlsRow, 0, "ตารางแสดงตำแหน่งงานที่ต้องการประเมินค่างาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "เลขที่", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "ตำแหน่งใน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "ตำแหน่งใน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "ประเภท /", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "ประเภท /", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "ชื่อหน่วยงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 6, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 7, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 8, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 9, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 5, $xlsRow, 9);
			$worksheet->write($xlsRow, 10, "ที่ตั้ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 11, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 12, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 10, $xlsRow, 12);
			$worksheet->write($xlsRow, 13, "หน้าที่รับผิดชอบ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "การบริหาร", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "สายงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "ระดับตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "ระดับตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "ส่วนราชการ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 6, "ส่วนราชการ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 7, "$ORG_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 8, "ต่ำกว่ากอง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 9, "ต่ำกว่ากอง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 10, "อำเภอ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 11, "จังหวัด", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 12, "ประเทศ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 13, "ของหน่วยงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "(เดิม)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "(ใหม่)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "ระดับ$MINISTRY_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 6, "ระดับ$DEPARTMENT_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 7, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 8, "1 ระดับ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 9, "2 ระดับ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 10, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 11, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 12, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 13, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$POS_NO", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "$PM_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "$PL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "$CL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "$LEVEL_NO", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "$MINISTRY_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 6, "$DEPARTMENT_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 7, "$ORG_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 8, "$ORG_NAME_1", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 9, "$ORG_NAME_2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 10, "$AP_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 11, "$PV_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 12, "$CT_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 13, "$ORG_JOB", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "บทวิเคราะห์งานที่ต้องการประเมินค่างาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=red&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ความสำคัญของงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$PIORITY", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ภาระงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$WORKLOAD", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ความรับผิดชอบหลักของงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			for($i=1; $i<=8; $i++){
				$xlsRow++;
				$worksheet->set_row($xlsRow, 18);
				$worksheet->write($xlsRow, 0, ($i.". ".${"ACCOUNTABILITY".$i}), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
				$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
			} // loop for
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ความรู้ที่จำเป็นในงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$KNOWLEDGE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ทักษะที่จำเป็นในงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$SKILL", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ประสบการณ์ที่จำเป็นในงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$EXP", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "สมรรถนะที่จำเป็นในงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$COMPETENCY", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);
		
			$ISPASSED_FIRST = $arr_result[$JOB_EVA_ID][ISPASSED_FIRST];
			$REASON_FIRST = $arr_result[$JOB_EVA_ID][REASON_FIRST];
			$APPROVE_FIRST_BY = $arr_result[$JOB_EVA_ID][APPROVE_FIRST_BY];
			$APPROVE_FIRST_TIME = $arr_result[$JOB_EVA_ID][APPROVE_FIRST_TIME];

			$ISPASSED_SECOND = $arr_result[$JOB_EVA_ID][ISPASSED_SECOND];
			$REASON_SECOND = $arr_result[$JOB_EVA_ID][REASON_SECOND];
			$APPROVE_SECOND_BY = $arr_result[$JOB_EVA_ID][APPROVE_SECOND_BY];
			$APPROVE_SECOND_TIME = $arr_result[$JOB_EVA_ID][APPROVE_SECOND_TIME];

			$KH1 = $arr_score[$JOB_EVA_ID][KH1];
			$KH2 = $arr_score[$JOB_EVA_ID][KH2];
			$KH3 = $arr_score[$JOB_EVA_ID][KH3];
			$KH_SCORE = $arr_score[$JOB_EVA_ID][KH_SCORE];
	
			$PS1 = $arr_score[$JOB_EVA_ID][PS1];
			$PS2 = $arr_score[$JOB_EVA_ID][PS2];
			$PS_SCORE = $arr_score[$JOB_EVA_ID][PS_SCORE];
			$PS_KH_SCORE = $arr_score[$JOB_EVA_ID][PS_KH_SCORE];

			$ACC1 = $arr_score[$JOB_EVA_ID][ACC1];
			$ACC2 = $arr_score[$JOB_EVA_ID][ACC2];
			$ACC3 = $arr_score[$JOB_EVA_ID][ACC3];
			$ACC_SCORE = $arr_score[$JOB_EVA_ID][ACC_SCORE];

			$TOTAL_POINTS = $arr_score[$JOB_EVA_ID][TOTAL_POINTS];
			$PROFILE_CHECK = $arr_score[$JOB_EVA_ID][PROFILE_CHECK];
			$EVALUATE_LEVEL_NO = $arr_score[$JOB_EVA_ID][EVALUATE_LEVEL_NO];

			$KH1_CONSISTENCY = $arr_consistency_result[$JOB_EVA_ID][KH1];
			$KH2_CONSISTENCY = $arr_consistency_result[$JOB_EVA_ID][KH2];
			$KH3_CONSISTENCY = $arr_consistency_result[$JOB_EVA_ID][KH3];
			$KH_CONSISTENCY = $arr_consistency_result[$JOB_EVA_ID][KH];

			$PS1_CONSISTENCY = $arr_consistency_result[$JOB_EVA_ID][PS1];
			$PS2_CONSISTENCY = $arr_consistency_result[$JOB_EVA_ID][PS2];
			$PS_CONSISTENCY = $arr_consistency_result[$JOB_EVA_ID][PS];

			$PC_CONSISTENCY = $arr_consistency_result[$JOB_EVA_ID][PC];

			$worksheet = &$workbook->addworksheet("ผลการประเมินค่างาน ($POS_NO)");
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);
			
			$worksheet->set_column(0, 0, 30);
			$worksheet->set_column(1, 1, 7);
			$worksheet->set_column(2, 2, 30);	
			$worksheet->set_column(3, 3, 7);	
			$worksheet->set_column(4, 4, 30);	
			$worksheet->set_column(5, 5, 7);	
		
			$xlsRow = 0;
			$worksheet->set_row($xlsRow, 20);
			$worksheet->write($xlsRow, 0, "ตารางแสดงผลคะแนนของการประเมินค่างานโดยละเอียดประจำ ($TEST_TIME)", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 5);
		
			$xlsRow++;
			for($i=0; $i<=5; $i++){
				$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			} // loop for
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ตำแหน่งในสายงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "$PL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 1, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ตำแหน่งในการบริหาร", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "$PM_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 1, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ประเภท/ระดับตำแหน่ง (ใหม่)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "$LEVEL_NO", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 1, $xlsRow, 5);
		
			$xlsRow++;
			for($i=0; $i<=5; $i++){
				$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			} // loop for
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ความรู้", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 1);
			$worksheet->write($xlsRow, 2, "การแก้ปัญหา", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 2, $xlsRow, 3);
			$worksheet->write($xlsRow, 4, "ความรับผิดชอบ", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 4, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "(Know - how)", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 1);
			$worksheet->write($xlsRow, 2, "(Problem Solving)", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 2, $xlsRow, 3);
			$worksheet->write($xlsRow, 4, "(Accountability)", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 4, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ความกว้างและลึกของความรู้", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "$KH1", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "อิสระในความคิด", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "$PS1", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "อิสระในการปฏิบัติงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "$ACC1", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "การบริหารจัดการ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "$KH2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "ความท้าทายในความคิด", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "$PS2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "ขอบเขตผลกระทบของงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "$ACC2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "การปฏิสัมพันธ์กับผู้อื่น", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "$KH3", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "ร้อยละของความรู้ (%)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "$PS_SCORE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "ชนิดของผลกระทบ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "$ACC3", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "คะแนน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "$KH_SCORE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "คะแนน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "$PS_KH_SCORE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "คะแนน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "$ACC_SCORE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ความสอดคล้องของการวิเคราะห์", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, (($KH_CONSISTENCY=="Y")?"ผ่าน":"ไม่ผ่าน"), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "ความสอดคล้องของการวิเคราะห์", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, (($PS_CONSISTENCY=="Y")?"ผ่าน":"ไม่ผ่าน"), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "ความสอดคล้องของการวิเคราะห์", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "ผ่าน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "(Consistency)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
			$worksheet->merge_cells(($xlsRow - 1), 1, $xlsRow, 1);
			$worksheet->write($xlsRow, 2, "(Consistency)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
			$worksheet->merge_cells(($xlsRow - 1), 3, $xlsRow, 3);
			$worksheet->write($xlsRow, 4, "(Consistency)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
			$worksheet->merge_cells(($xlsRow - 1), 5, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "คะแนนรวม (CSC Points)  ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
			$worksheet->write($xlsRow, 4, "$TOTAL_POINTS", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 4, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "Profile Check  ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
			$worksheet->write($xlsRow, 4, "$PROFILE_CHECK", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, (($PC_CONSISTENCY=="Y")?"ผ่าน":"ไม่ผ่าน"), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			for($i=0; $i<=5; $i++){
				$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			} // loop for
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ผลสรุปการประเมิน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			for($i=1; $i<=5; $i++){
				$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			} // loop for
			$worksheet->merge_cells($xlsRow, 1, $xlsRow, 5);
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ผู้ทดสอบและวันที่ประเมินค่างาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "ผู้ทดสอบและวันที่ประเมินครั้งแรก", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "ผู้ทดสอบและวันที่ประเมินครั้งที่ 2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "$APPROVE_FIRST_NAME $APPROVE_FIRST_TIME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "$APPROVE_SECOND_NAME $APPROVE_SECOND_TIME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "เหตุผลในการอนุมัติ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "เหตุผลในการอนุมัติ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 2, "$REASON_FIRST", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 4, "$REASON_SECOND", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
		
			$worksheet = &$workbook->addworksheet("เหตุผลประกอบ ($POS_NO)");
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);
		
			$worksheet->set_column(0, 0, 35);
			$worksheet->set_column(1, 1, 50);
			$worksheet->set_column(2, 2, 50);
		
			$xlsRow = 0;
			$worksheet->set_row($xlsRow, 20);
			$worksheet->write($xlsRow, 0, "เหตุผลประกอบการตอบคำถามการประเมินค่างาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 2);
		
			for($i=1; $i<=20; $i++){
				$QUESTION_TOPIC = $arr_answer[$JOB_EVA_ID][$i][QUESTION_TOPIC];
				$ANSWER_INFO = $arr_answer[$JOB_EVA_ID][$i][ANSWER_INFO];
				$ANSWER_DESCRIPTION = $arr_answer[$JOB_EVA_ID][$i][ANSWER_DESCRIPTION];

				$xlsRow++;
				$worksheet->write($xlsRow, 0, ($i.". ".$QUESTION_TOPIC), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));	
				$worksheet->write($xlsRow, 1, ($ANSWER_INFO), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));	
				$worksheet->write($xlsRow, 2, ($ANSWER_DESCRIPTION), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));	
			} // loop for
		
		} // loop for $arr_content
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format_new("xlsFmtTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, 0, $xlsRow, 10);
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