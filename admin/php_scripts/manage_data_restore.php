<?
include("../php_scripts/connect_database.php");
include("php_scripts/session_start.php");
include("../php_scripts/connect_file.php");

$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
ini_set("max_execution_time", 36000);

// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
$DIVIDE_TEXTFILE = "|#|";
$path_toshow = "C:\\dpis\\db_data\\";
$path_tosave = (trim($path_tosave))? $path_tosave : $path_toshow;
$path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);
$path_toshow = $path_tosave;
if ($DPISDB == "odbc") {
	$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
	$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
} elseif ($DPISDB == "oci8") {
	$TYPE_TEXT_STR = array("VARCHAR", "VARCHAR2", "CHAR");
	$TYPE_TEXT_INT = array("NUMBER","DECIMAL");
}
// =======================================================


if ($command=="CONVERT") { 

$table = array(
"per_extra_income_type",
"per_blood",
"per_religion",
"per_country",
"per_province",
"per_amphur",
"per_prename",
"per_line_group", 
"per_line",
"per_absenttype",
"per_status",
"per_mgt",
"per_type",
"per_educlevel",
"per_educname",
"per_educmajor",
"per_institute",
"per_train",
"per_movment",
"per_crime",
"per_crime_dtl",
"per_scholartype",
"per_holiday", 
"per_holiday_group",
"per_holidayhis",
"per_abilitygrp",
"per_condition",
"per_service",
"per_level",
"per_layer",
"per_layer_new",
"per_skill_group",
"per_special_skillgrp",
"per_skill",
"per_decoration",
"per_org_type",
"per_org_level",
"per_org_stat",
"per_mgtsalary",
"per_off_type",
"per_married",
"per_extratype",
"per_co_level",
"per_penalty",
"per_heirtype",
"per_servicetitle",
"per_reward",
"per_divorce",
"per_time",
"per_comtype",
"per_pos_group",
"per_pos_name", 
"per_empser_pos_name",
"per_layeremp",
"per_org_province",
"per_org",
"per_org_job",
"per_org_ass",
"per_control",
"per_position",
"per_pos_emp",
"per_pos_empser",
"per_pos_move",
"per_assign",
"per_assign_dtl",
"per_assign_year",
"per_assign_s",
"per_order",
"per_order_dtl",
"per_req3",
"per_req3_dtl",
"per_req2",
"per_req2_dtl",
"per_req1",
"per_req1_dtl1",
"per_req1_dtl2",
"PER_PERSONAL",
"per_spouse",
"per_positionhis",
"per_salaryhis",
"per_extrahis",
"per_extra_incomehis",
"per_educate",
"per_training", 
"per_ability",
"per_special_skill",
"per_heir",
"per_absenthis",
"per_punishment",
"per_servicehis",
"per_rewardhis",
"per_marrhis",
"per_namehis",
"per_decoratehis",
"per_timehis",
"per_personalpic",
"per_transfer_req",
"per_command",
"per_comdtl",
"per_move_req",
"per_promote_c",
"per_promote_p",
"per_promote_e",
"per_salquota",
"per_salquotadtl1",
"per_salquotadtl2",
"per_salpromote",
"per_bonusquota",
"per_bonusquotadtl1",
"per_bonusquotadtl2",
"per_bonuspromote",
"per_absent",
"per_absent_conf ",
"per_invest1",
"per_invest1dtl",
"per_invest2",
"per_invest2dtl",
"per_scholarship",
"per_scholar",
"per_scholarinc",
"per_course",
"per_coursedtl",
"per_decorcond",
"per_decor",
"per_decordtl",
"per_letter",
"per_sum",
"per_sum_dtl1",
"per_sum_dtl2",
"per_sum_dtl3",
"per_sum_dtl4",
"per_sum_dtl5",
"per_sum_dtl6",
"per_sum_dtl7",
"per_sum_dtl8",
"per_sum_dtl9",
"per_performance_review",
"per_kpi",
"per_job_family",
"per_competence",
"per_job_competence",
"per_competence_level",
"per_position_competence",
"per_kpi_form",
"per_performance_goals",
"per_kpi_competence",
"per_ipip",
"per_occupation",
"per_parent",
"per_child",
"per_performance",
"per_goodness",
"per_performance_goodness",
"per_performance_dtl",
"per_goodness_dtl",
"per_training_dtl",
"per_actinghis",
"per_address", 
"per_assess_level",
"per_assess_main",
"per_attachment",
"per_comgroup",
"per_compensation_test",
"per_compensation_test_dtl",
"per_develope_guide",
"per_develope_plan",
"per_emp_status",
"per_family",
"per_layeremp_new",
"per_message",
"per_message_user",
"per_namecard",
"per_pos_empser_frame",
"per_pos_mgtsalary",
"per_pos_mgtsalaryhis",
"per_pos_status",
"per_pos_type",
"per_practice",
"per_project_group",
"per_project_payment",
"per_salary_movment",
"per_sql",
"per_standard_competence",
"per_sum_dtl10",
"per_train_plan",
"per_train_project",
"per_train_project_dtl",
"per_train_project_payment",
"per_train_project_personal",
"per_trainner",
"per_workflow_ability",
"per_workflow_absenthis",
"per_workflow_address",
"per_workflow_decoratehis",
"per_workflow_educate",
"per_workflow_family",
"per_workflow_marrhis",
"per_workflow_namehis",
"per_workflow_positionhis",
"per_workflow_punishment",
"per_workflow_rewardhis",
"per_workflow_salaryhis",
"per_workflow_servicehis",
"per_workflow_special_skill",
"per_workflow_timehis",
"per_workflow_training",
"ACCOUNTABILITY_INFO_PRIMARY",
"ACCOUNTABILITY_INFO_SECONDARY",
"ACCOUNTABILITY_LEVEL_TYPE",
"ACCOUNTABILITY_TYPE",
"BACKOFFICE_MENU_BAR_LV0",
"BACKOFFICE_MENU_BAR_LV1",
"BACKOFFICE_MENU_BAR_LV2",
"BACKOFFICE_MENU_BAR_LV3",
"COMPETENCY_INFO",
"COMPETENCY_LEVEL",
"CONFIG_JOB_EVALUATION",
"CONFIG_ROLE_PROFILE",
"CONFIG_WORKFLOW",
"EAF_COMPETENCE",
"EAF_LEARNING_KNOWLEDGE",
"EAF_MASTER",
"EAF_PERSONAL",
"EAF_PERSONAL_DETAIL",
"EAF_PERSONAL_KNOWLEDGE",
"EAF_PERSONAL_STRUCTURE",
"EDITOR_ATTACHMENT",
"EDITOR_IMAGE",
"EXP_INFO",
"GROUP_INFO",
"JEM_ACC",
"JEM_ANSWER_INFO",
"JEM_CONSISTENCY_CHECK",
"JEM_GRADE",
"JEM_KH",
"JEM_MAPPING",
"JEM_PROFILE_CHECK",
"JEM_PS",
"JEM_PS_KH",
"JEM_QUESTION_INFO",
"JEM_STEP",
"JOB_EVALUATION",
"JOB_EVALUATION_ANSWER_HISTORY",
"KNOWLEDGE_INFO",
"KNOWLEDGE_LEVEL",
"PER_PERSONAL_NAMECARD",
"PER_SLIP",
"PER_LINE_COMPETENCE",
"PER_TEMP_POS_GROUP",
"PER_TEMP_POS_NAME",
"PER_POS_TEMP",
"PER_DISTRICT",
"PER_POS_LEVEL_SALARY",
"POS_DES_INFO",
"POS_JOB_DES_INFO",
"POS_JOB_DES_PRIMARY",
"POS_JOB_DES_SECONDARY",
"SITE_INFO",
"SKILL_INFO",
"SKILL_LEVEL",
"SYSTEM_CONFIG",
"USER_DETAIL",
"USER_GROUP",
"USER_LAST_ACCESS",
"USER_LOG",
"USER_PRIVILEGE", 
"USER_SECTION",
"PER_PERCARD",
"PER_PERSONAL_CARD",
"PER_SPECIAL_HOLIDAY",
"PER_ABSENTSUM",
"PER_EXPENSE_BUDGET",
"OTH_SERVER",
"PER_PROJECT",
"PER_BONUS_RULE",
"RPT_ADJUST_FORMAT",
"PER_SENIOR_EXCUSIVE",
"PER_COMPETENCY_ASSESSMENT",
"PER_MGT_COMPETENCY_ASSESSMENT",
"PER_MGT_COMPETENCY_ASSESSMENT_VER",
"PER_LICENSE_TYPE",
"PER_LICENSEHIS",
"PER_APPROVE_RESOLUTION",
"PER_EXCELLENT_PERFORMANCE",
"PER_SOLDIERHIS",
"PER_OTHER_OCCUPATION",
"PER_TEST_COURSE",
"PER_TEST_COURSEHIS",
"PER_MINISTRY_GROUP",
"PER_PROVINCE_GROUP",
"PER_MAIN_JOB_TYPE",
"PER_MAIN_JOB",
"HELPTAB",
"PER_MGT_COMPETENCE",
"PER_QUESTION_STOCK",
"PER_COMPETENCY_TEST",
"PER_COMPETENCY_DTL",
"PER_COMPETENCY_FORM",
"PER_COMPETENCY_ANSWER",
"PER_WORK_LOCATION",
"PER_WORK_CYCLE",
"PER_TIME_ATT",
"PER_WORK_LATE",
"PER_WORK_CYCLEHIS",
"PER_TIME_ATTENDANCE",
"PER_WORK_TIME"
);
//$table = array("per_line");
// ===========================================================================
// ===== วนลูปตาม array table ที่เหลือโดยอิง PER_ID กับ PER_PERSONAL ที่ insert ด้านบ้าน =====
for ( $i=0; $i<count($table); $i++ ) { 
	// ===== select ชื่อ fields จาก $table ===== 
	$cmd = " select * from $table[$i] ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();	
	$field_list = $db_dpis->list_fields($table);

	// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
	unset($data, $arr_fields);
	if ($DPISDB=="odbc" || $DPISDB=="oci8") {
		for($j=1; $j<=count($field_list); $j++) : 
			$arr_fields[] = $tmp_name = $field_list[$j]["name"];
			$arr_fields_type[$tmp_name] = $field_list[$j]["type"];
		endfor;
	} // end if

	// ===== นำข้อมูล fields จาก textfile write ลง db
	$tmp_num = 0;
	$db_textfile = new connect_file("$table[$i]", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
	while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
		// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields ตาม texfile 
		unset ($field_name, $field_value);
		foreach ($data as $key => $fieldvalue) {	
			$fieldvalue = str_replace("<br>", "\n", $fieldvalue);
			if ($key=='UPDATE_DATE' && $fieldvalue=='NULL') {
				$fieldvalue = date("Y-m-d H:i:s");
				$fieldvalue = "'$fieldvalue'";
			}
			if (($key=='MAH_MARRY_DATE' || $key=='MV_DATE' || $key=='POH_EFFECTIVEDATE' || $key=='POH_DOCDATE' || $key=='SAH_EFFECTIVEDATE' || $key=='SAH_DOCDATE') && $fieldvalue=='NULL') {
				$fieldvalue = "1957-01-01";
				$fieldvalue = "'$fieldvalue'";
			}
			if (($key=='POH_REMARK' || $key=='POH_POS_NO' || $key=='POH_DOCNO' || $key=='SAH_DOCNO') && $fieldvalue=='NULL') {
				$fieldvalue = "-";
				$fieldvalue = "'$fieldvalue'";
			}
			if ($key=='ORG_JOB') {
				$fieldvalue = "NULL";
			}
//			if ($key=='INS_NAME') $fieldvalue = str_replace("'", "\'", $fieldvalue);
//				$TMP_INS_NAME_RPL = str_replace('\'', '&rsquo;', $TMP_INS_NAME);
			$field_name .= (trim($field_name) != "")? ", " . trim($key) : trim($key);
			$field_value .= (trim($field_value) != "")? ", " . trim($fieldvalue) : trim($fieldvalue);
		}		
		// ===== insert ข้อมูลลง database =====
		$cmd = "	INSERT INTO $table[$i] ( $field_name ) VALUES ( $field_value )";
		$db_dpis->send_cmd($cmd);		
		if ($table[$i]=="per_org") 
			echo "$cmd<br>";
		
		//  echo query to check
		if ($table[$i] == "per_layerx") {
			++$tmp_num;
			echo "<br><b>$table[$i] :: $tmp_num :: </b>";
			$db_dpis->show_error(); 
			echo "<br>======================<br>";
			//echo "<br><b>$table[$i] :: $tmp_num :: </b> $cmd<br>======================<br>";	
		}
		
	}  // end while  
} 	// endif for ($i=0; $i<=count($table); $i++)

unset ($data, $arr_fields, $field_name, $field_value);
} // endif command==CONVERT
?>