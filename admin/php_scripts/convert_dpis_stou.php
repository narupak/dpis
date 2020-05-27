<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$UPDATE_USER = 99999;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='MASTER' ) {  // ข้อมูลหลัก 
		$table = array(	"per_child", "per_parent", "per_occupation", "per_ipip", "per_kpi_competence", "per_performance_goals", "per_kpi_form", 
			"per_position_competence", "per_competence_level", "per_job_competence", "per_competence", "per_job_family", "per_kpi", "per_performance_review", 
			"per_sum_dtl9", "per_sum_dtl8", "per_sum_dtl7", "per_sum_dtl6", "per_sum_dtl5", "per_sum_dtl4", "per_sum_dtl3", "per_sum_dtl2", "per_sum_dtl1", "per_sum", 
			"per_letter", "per_decordtl", "per_decor", "per_decorcond", "per_coursedtl", "per_course", "per_scholarinc", "per_scholar", "per_scholarship", "per_invest2dtl", 
			"per_invest2", "per_invest1dtl", "per_invest1", "per_absent_conf ", "per_absent", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", 
			"per_bonusquota", "per_salpromote", "per_salquotadtl2", "per_salquotadtl1", "per_salquota", "per_promote_e", "per_promote_p", "per_promote_c", 
			"per_move_req", "per_comdtl", "per_command", "per_transfer_req", "per_personalpic", "per_timehis", "per_decoratehis", "per_namehis", "per_marrhis", 
			"per_rewardhis", "per_servicehis", "per_punishment", "per_absenthis", "per_heir", "per_special_skill", "per_ability", "per_training", "per_educate", 
			"per_extra_incomehis", "per_extrahis", "per_salaryhis", "per_positionhis", "per_personal", "per_req1_dtl2", "per_req1_dtl1", "per_req1", "per_req2_dtl", "per_req2", 
			"per_req3_dtl", "per_req3", "per_order_dtl", "per_order", "per_assign_s", "per_assign_year", "per_assign_dtl", "per_assign", "per_pos_move", "per_pos_empser", 
			"per_pos_emp", "per_position", "per_control", "per_org_ass", "per_org_job", "per_org", "per_org_province", "per_layeremp", "per_empser_pos_name", 
			"per_pos_name", "per_pos_group", "per_comtype", "per_time", "per_divorce", "per_reward", "per_servicetitle", "per_heirtype", "per_penalty", "per_co_level", 
			"per_extratype", "per_married", "per_off_type", "per_mgtsalary", "per_org_stat", "per_org_level", "per_org_type", "per_decoration", "per_skill", 
			"per_special_skillgrp", "per_skill_group", "per_institute", "per_layer_new", "per_layer", "per_level", "per_service", "per_condition", "per_abilitygrp", "per_holiday", 
			"per_scholartype", "per_crime_dtl", "per_crime", "per_movment", "per_train", "per_educmajor", "per_educname", "per_educlevel", "per_type", "per_mgt", 
			"per_status", "per_absenttype", "per_line", "per_prename", "per_amphur", "per_province", "per_country", "per_religion", "per_blood", "per_extra_income_type");
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
		} // end for

// คำนำหน้าชื่อ  
		$cmd = " SELECT PN_CODE, PN_SHORTNAME, PN_NAME, PN_ENG_NAME, PN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_PRENAME ORDER BY PN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PRENAME++;
			$PN_CODE = trim($data[PN_CODE]);
			$PN_SHORTNAME = trim($data[PN_SHORTNAME]);
			$PN_NAME = trim($data[PN_NAME]);
			$PN_ENG_NAME = trim($data[PN_ENG_NAME]);
			$PN_ACTIVE = $data[PN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_PRENAME (PN_CODE, PN_SHORTNAME, PN_NAME, PN_ENG_NAME, PN_ACTIVE, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ('$PN_CODE', '$PN_SHORTNAME', '$PN_NAME', '$PN_ENG_NAME', $PN_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while			
		
		$cmd = " select count(PN_CODE) as COUNT_NEW from PER_PRENAME ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_PRENAME - $PER_PRENAME - $COUNT_NEW<br>";

// ศาสนา 
		$cmd = " SELECT RE_CODE, RE_NAME, RE_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_RELIGION ORDER BY RE_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_RELIGION++;
			$RE_CODE = trim($data[RE_CODE]);
			$RE_NAME = trim($data[RE_NAME]);
			$RE_ACTIVE = $data[RE_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_RELIGION (RE_CODE, RE_NAME, RE_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$RE_CODE', '$RE_NAME', $RE_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while				
		
		$cmd = " select count(RE_CODE) as COUNT_NEW from PER_RELIGION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_RELIGION - $PER_RELIGION - $COUNT_NEW<br>";

// ประเทศ
		$cmd = " SELECT CT_CODE, CT_NAME, CT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_COUNTRY ORDER BY CT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_COUNTRY++;
			$CT_CODE = trim($data[CT_CODE]);
			$CT_NAME = trim($data[CT_NAME]);
			$CT_ACTIVE = $data[CT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_COUNTRY (CT_CODE, CT_NAME, CT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$CT_CODE', '$CT_NAME', $CT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while		
		
		$cmd = " select count(CT_CODE) as COUNT_NEW from PER_COUNTRY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_COUNTRY - $PER_COUNTRY - $COUNT_NEW<br>";

// จังหวัด
		$cmd = " SELECT PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_PROVINCE ORDER BY PV_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PROVINCE++;
			$PV_CODE = trim($data[PV_CODE]);
			$PV_NAME = trim($data[PV_NAME]);
			$CT_CODE = trim($data[CT_CODE]);
			$PV_ACTIVE = $data[PV_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_PROVINCE (PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PV_CODE', '$PV_NAME', '$CT_CODE', $PV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PV_CODE) as COUNT_NEW from PER_PROVINCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_PROVINCE - $PER_PROVINCE - $COUNT_NEW<br>";

// อำเภอ
		$cmd = " SELECT AP_CODE, AP_NAME, PV_CODE, AP_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_AMPHUR ORDER BY AP_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_AMPHUR++;
			$AP_CODE = trim($data[AP_CODE]);
			$AP_NAME = trim($data[AP_NAME]);
			$PV_CODE = trim($data[PV_CODE]);
			$AP_ACTIVE = $data[AP_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_AMPHUR (AP_CODE, AP_NAME, PV_CODE, AP_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$AP_CODE', '$AP_NAME', '$PV_CODE', $AP_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(AP_CODE) as COUNT_NEW from PER_AMPHUR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_AMPHUR - $PER_AMPHUR - $COUNT_NEW<br>";

// สังกัด  
		$cmd = " SELECT OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_TYPE ORDER BY OT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG_TYPE++;
			$OT_CODE = trim($data[OT_CODE]);
			$OT_NAME = trim($data[OT_NAME]);
			$OT_ACTIVE = $data[OT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$OT_CODE', '$OT_NAME', $OT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(OT_CODE) as COUNT_NEW from PER_ORG_TYPE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ORG_TYPE - $PER_ORG_TYPE - $COUNT_NEW<br>";

// ชื่อตำแหน่งในสายงาน  
		$cmd = " SELECT PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE
						FROM PER_LINE ORDER BY PL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_LINE++;
			$PL_CODE = trim($data[PL_CODE]);
			$PL_NAME = trim($data[PL_NAME]);
			$PL_SHORTNAME = trim($data[PL_SHORTNAME]);
			$PL_ACTIVE = $data[PL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PL_CODE', '$PL_NAME', '$PL_SHORTNAME', $PL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PL_CODE) as COUNT_NEW from PER_LINE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_LINE - $PER_LINE - $COUNT_NEW<br>";

// ประเภทการลา  
		$cmd = " SELECT AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ABSENTTYPE ORDER BY AB_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ABSENTTYPE++;
			$AB_CODE = trim($data[AB_CODE]);
			$AB_NAME = trim($data[AB_NAME]);
			$AB_QUOTA = $data[AB_QUOTA] + 0;
			$AB_COUNT = $data[AB_COUNT] + 0;
			$AB_ACTIVE = $data[AB_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ('$AB_CODE', '$AB_NAME', $AB_QUOTA, $AB_COUNT, $AB_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(AB_CODE) as COUNT_NEW from PER_ABSENTTYPE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ABSENTTYPE - $PER_ABSENTTYPE - $COUNT_NEW<br>";

// ฐานะของตำแหน่ง
		$cmd = " SELECT PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_STATUS ORDER BY PS_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_STATUS++;
			$PS_CODE = trim($data[PS_CODE]);
			$PS_NAME = trim($data[PS_NAME]);
			$PS_ACTIVE = $data[PS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PS_CODE', '$PS_NAME', $PS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PS_CODE) as COUNT_NEW from PER_STATUS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_STATUS - $PER_STATUS - $COUNT_NEW<br>";

// ชื่อตำแหน่งในการบริหารงาน  
		$cmd = " SELECT PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MGT ORDER BY PM_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_MGT++;
			$PM_CODE = trim($data[PM_CODE]);
			$PM_NAME = trim($data[PM_NAME]);
			$PM_SHORTNAME = trim($data[PM_SHORTNAME]);
			$PS_CODE = trim($data[PS_CODE]);
			$PM_ACTIVE = $data[PM_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_MGT (PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ('$PM_CODE', '$PM_NAME', '$PM_SHORTNAME', '$PS_CODE', $PM_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PM_CODE) as COUNT_NEW from PER_MGT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_MGT - $PER_MGT - $COUNT_NEW<br>";

// ประเภทตำแหน่ง 
		$cmd = " SELECT PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TYPE ORDER BY PT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_TYPE++;
			$PT_CODE = trim($data[PT_CODE]);
			$PT_NAME = trim($data[PT_NAME]);
			$PT_GROUP = $data[PT_GROUP] + 0;
			$PT_ACTIVE = $data[PT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_TYPE (PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PT_CODE', '$PT_NAME', $PT_GROUP, $PT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PT_CODE) as COUNT_NEW from PER_TYPE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_TYPE - $PER_TYPE - $COUNT_NEW<br>";

// ระดับการศึกษา
		$cmd = " SELECT EL_CODE, EL_NAME, EL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EDUCLEVEL ORDER BY EL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EDUCLEVEL++;
			$EL_CODE = trim($data[EL_CODE]);
			$EL_NAME = trim($data[EL_NAME]);
			$EL_ACTIVE = $data[EL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_EDUCLEVEL (EL_CODE, EL_NAME, EL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$EL_CODE', '$EL_NAME', $EL_ACTIVE, $UPDATE_USER, 	'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(EL_CODE) as COUNT_NEW from PER_EDUCLEVEL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_EDUCLEVEL - $PER_EDUCLEVEL - $COUNT_NEW<br>";

// วุฒิการศึกษา  
		$cmd = " SELECT EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EDUCNAME ORDER BY EN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EDUCNAME++;
			$EN_CODE = trim($data[EN_CODE]);
			$EL_CODE = trim($data[EL_CODE]);
			$EN_SHORTNAME = trim($data[EN_SHORTNAME]);
			$EN_SHORTNAME=str_replace("'","''",$EN_SHORTNAME);
			$EN_SHORTNAME=str_replace("\"","&quot;",$EN_SHORTNAME);
			$EN_NAME = trim($data[EN_NAME]);
			$EN_NAME=str_replace("'","''",$EN_NAME);
			$EN_NAME=str_replace("\"","&quot;",$EN_NAME);
			$EN_ACTIVE = $data[EN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_EDUCNAME (EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ('$EN_CODE', '$EL_CODE', '$EN_SHORTNAME', '$EN_NAME', $EN_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(EN_CODE) as COUNT_NEW from PER_EDUCNAME ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_EDUCNAME - $PER_EDUCNAME - $COUNT_NEW<br>";

		$cmd = " SELECT EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EDUCNAME ORDER BY EN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EN_CODE = trim($data[EN_CODE]);

			$cmd = " SELECT EN_NAME FROM PER_EDUCNAME WHERE EN_CODE = '$EN_CODE' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$EN_CODE<br>";
		} // end while						

// สาขาวิชาเอก  
		$cmd = " SELECT EM_CODE, EM_NAME, EM_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EDUCMAJOR ORDER BY EM_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EDUCMAJOR++;
			$EM_CODE = trim($data[EM_CODE]);
			$EM_NAME = trim($data[EM_NAME]);
			$EM_ACTIVE = $data[EM_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_EDUCMAJOR (EM_CODE, EM_NAME, EM_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$EM_CODE', '$EM_NAME', $EM_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(EM_CODE) as COUNT_NEW from PER_EDUCMAJOR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_EDUCMAJOR - $PER_EDUCMAJOR - $COUNT_NEW<br>";

// หลักสูตรการฝึกอบรม/ดูงาน  
		$cmd = " SELECT TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TRAIN ORDER BY TR_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_TRAIN++;
			$TR_CODE = trim($data[TR_CODE]);
			$TR_TYPE = $data[TR_TYPE] + 0;
			$TR_NAME = trim($data[TR_NAME]);
			$TR_NAME=str_replace("'","''",$TR_NAME);
			$TR_NAME=str_replace("\"","&quot;",$TR_NAME);
			$TR_ACTIVE = $data[TR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_TRAIN (TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$TR_CODE', $TR_TYPE, '$TR_NAME', $TR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(TR_CODE) as COUNT_NEW from PER_TRAIN ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_TRAIN - $PER_TRAIN - $COUNT_NEW<br>";

		$cmd = " SELECT TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TRAIN ORDER BY TR_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TR_CODE = trim($data[TR_CODE]);

			$cmd = " SELECT TR_NAME FROM PER_TRAIN WHERE TR_CODE = '$TR_CODE' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$TR_CODE<br>";
		} // end while						

// ประเภทการเคลื่อนไหว  
		$cmd = " SELECT MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MOVMENT ORDER BY MOV_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_MOVMENT++;
			$MOV_CODE = trim($data[MOV_CODE]);
			$MOV_NAME = trim($data[MOV_NAME]);
			$MOV_TYPE = $data[MOV_TYPE] + 0;
			$MOV_ACTIVE = $data[MR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$MOV_CODE', '$MOV_NAME', $MOV_TYPE, $MOV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(MOV_CODE) as COUNT_NEW from PER_MOVMENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_MOVMENT - $PER_MOVMENT - $COUNT_NEW<br>";

// ฐานความผิด  
		$cmd = " SELECT CR_CODE, CR_NAME, CR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CRIME ORDER BY CR_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_CRIME++;
			$CR_CODE = trim($data[CR_CODE]);
			$CR_NAME = trim($data[CR_NAME]);
			$CR_ACTIVE = $data[CR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_CRIME (CR_CODE, CR_NAME, CR_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$CR_CODE', '$CR_NAME', $CR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(CR_CODE) as COUNT_NEW from PER_CRIME ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_CRIME - $PER_CRIME - $COUNT_NEW<br>";

// กรณีความผิด  
		$cmd = " SELECT CRD_CODE, CRD_NAME, CR_CODE, CRD_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CRIME_DTL ORDER BY CRD_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_CRIME_DTL++;
			$CRD_CODE = trim($data[CRD_CODE]);
			$CRD_NAME = trim($data[CRD_NAME]);
			$CR_CODE = trim($data[CR_CODE]);
			$CRD_ACTIVE = $data[CRD_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_CRIME_DTL (CRD_CODE, CRD_NAME, CR_CODE, CRD_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$CRD_CODE', '$CRD_NAME', '$CR_CODE', $CRD_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(CRD_CODE) as COUNT_NEW from PER_CRIME_DTL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_CRIME_DTL - $PER_CRIME_DTL - $COUNT_NEW<br>";

// ประเภททุน
		$cmd = " SELECT ST_CODE, ST_NAME, ST_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SCHOLARTYPE ORDER BY ST_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SCHOLARTYPE++;
			$ST_CODE = trim($data[ST_CODE]);
			$ST_NAME = trim($data[ST_NAME]);
			$ST_ACTIVE = $data[ST_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SCHOLARTYPE (ST_CODE, ST_NAME, ST_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$ST_CODE', '$ST_NAME', $ST_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(ST_CODE) as COUNT_NEW from PER_SCHOLARTYPE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SCHOLARTYPE - $PER_SCHOLARTYPE - $COUNT_NEW<br>";

// ปฏิทินวันหยุด
		$cmd = " SELECT HOL_DATE, HOL_NAME, UPDATE_USER, UPDATE_DATE 
						FROM PER_HOLIDAY ORDER BY HOL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_HOLIDAY++;
			$HOL_DATE = trim($data[HOL_DATE]);
			$HOL_NAME = trim($data[HOL_NAME]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_CRIME (HOL_DATE, HOL_NAME, UPDATE_USER, UPDATE_DATE)
							VALUES ('$HOL_DATE', '$HOL_NAME', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(HOL_DATE) as COUNT_NEW from PER_HOLIDAY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_HOLIDAY - $PER_HOLIDAY - $COUNT_NEW<br>";

// ด้านความสามารถพิเศษ  
		$cmd = " SELECT AL_CODE, AL_NAME, AL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ABILITYGRP ORDER BY AL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ABILITYGRP++;
			$AL_CODE = trim($data[AL_CODE]);
			$AL_NAME = trim($data[AL_NAME]);
			$AL_ACTIVE = $data[AL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ABILITYGRP (AL_CODE, AL_NAME, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$AL_CODE', '$AL_NAME', $AL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(AL_CODE) as COUNT_NEW from PER_ABILITYGRP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ABILITYGRP - $PER_ABILITYGRP - $COUNT_NEW<br>";

// เงื่อนไขตำแหน่ง
		$cmd = " SELECT PC_CODE, PC_NAME, PC_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CONDITION ORDER BY PC_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_CONDITION++;
			$PC_CODE = trim($data[PC_CODE]);
			$PC_NAME = trim($data[PC_NAME]);
			$PC_ACTIVE = $data[PC_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_CONDITION (PC_CODE, PC_NAME, PC_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PC_CODE', '$PC_NAME', $PC_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PC_CODE) as COUNT_NEW from PER_CONDITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_CONDITION - $PER_CONDITION - $COUNT_NEW<br>";

// ประเภทราชการพิเศษ
		$cmd = " SELECT SV_CODE, SV_NAME, SV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SERVICE ORDER BY SV_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SERVICE++;
			$SV_CODE = trim($data[SV_CODE]);
			$SV_NAME = trim($data[SV_NAME]);
			$SV_ACTIVE = $data[SV_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SERVICE (SV_CODE, SV_NAME, SV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$SV_CODE', '$SV_NAME', $SV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SV_CODE) as COUNT_NEW from PER_SERVICE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SERVICE - $PER_SERVICE - $COUNT_NEW<br>";

// ระดับตำแหน่ง
		$cmd = " SELECT LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_LEVEL ORDER BY LEVEL_NO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_LEVEL++;
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_ACTIVE = $data[LEVEL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$LEVEL_NO', $LEVEL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(LEVEL_NO) as COUNT_NEW from PER_LEVEL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_LEVEL - $PER_LEVEL - $COUNT_NEW<br>";

// บัญชีอัตราเงินเดือนข้าราชการ
		$cmd = " SELECT LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_LAYER ORDER BY LAYER_TYPE, LEVEL_NO, LAYER_NO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_LAYER++;
			$LAYER_TYPE = $data[LAYER_TYPE] + 0;
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LAYER_NO = $data[LAYER_NO] + 0;
			$LAYER_SALARY = $data[LAYER_SALARY] + 0;
			$LAYER_ACTIVE = $data[LAYER_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ($LAYER_TYPE, '$LEVEL_NO', $LAYER_NO, $LAYER_SALARY, $LAYER_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(*) as COUNT_NEW from PER_LAYER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_LAYER - $PER_LAYER - $COUNT_NEW<br>";

// สถาบันการศึกษา  
		$cmd = " SELECT INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_INSTITUTE ORDER BY INS_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_INSTITUTE++;
			$INS_CODE = trim($data[INS_CODE]);
			$INS_NAME = trim($data[INS_NAME]);
			$INS_NAME=str_replace("'","''",$INS_NAME);
			$INS_NAME=str_replace("\"","&quot;",$INS_NAME);
			$CT_CODE = trim($data[CT_CODE]);
			$INS_ACTIVE = $data[INS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_INSTITUTE (INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$INS_CODE', '$INS_NAME', '$CT_CODE', $INS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(INS_CODE) as COUNT_NEW from PER_INSTITUTE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_INSTITUTE - $PER_INSTITUTE - $COUNT_NEW<br>";

// ด้านความเชี่ยวชาญ
		$cmd = " SELECT SG_CODE, SG_NAME, PL_CODE, SG_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SKILL_GROUP ORDER BY SG_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SKILL_GROUP++;
			$SG_CODE = trim($data[SG_CODE]);
			$SG_NAME = trim($data[SG_NAME]);
			$PL_CODE = trim($data[PL_CODE]);
			$SG_ACTIVE = $data[SG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SKILL_GROUP (SG_CODE, SG_NAME, PL_CODE, SG_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$SG_CODE', '$SG_NAME', '$PL_CODE', $SG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SG_CODE) as COUNT_NEW from PER_SKILL_GROUP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SKILL_GROUP - $PER_SKILL_GROUP - $COUNT_NEW<br>";

// สาขาความเชี่ยวชาญ
		$cmd = " SELECT SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SKILL ORDER BY SKILL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SKILL++;
			$SKILL_CODE = trim($data[SKILL_CODE]);
			$SKILL_NAME = trim($data[SKILL_NAME]);
			$SG_CODE = trim($data[SG_CODE]);
			$SKILL_ACTIVE = $data[SKILL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SKILL (SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$SKILL_CODE', '$SKILL_NAME', '$SG_CODE', $SKILL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SKILL_CODE) as COUNT_NEW from PER_SKILL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SKILL - $PER_SKILL - $COUNT_NEW<br>";

// ด้านความเชี่ยวชาญพิเศษ
		$cmd = " SELECT SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SPECIAL_SKILLGRP ORDER BY SS_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SPECIAL_SKILLGRP++;
			$SS_CODE = trim($data[SS_CODE]);
			$SS_NAME = trim($data[SS_NAME]);
			$SS_ACTIVE = $data[SS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$SS_CODE', '$SS_NAME', $SS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SS_CODE) as COUNT_NEW from PER_SPECIAL_SKILLGRP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SPECIAL_SKILLGRP - $PER_SPECIAL_SKILLGRP - $COUNT_NEW<br>";

// ชั้นเครื่องราชอิสริยาภรณ์  
		$cmd = " SELECT DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_DECORATION ORDER BY DC_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_DECORATION++;
			$DC_CODE = trim($data[DC_CODE]);
			$DC_SHORTNAME = trim($data[DC_SHORTNAME]);
			$DC_NAME = trim($data[DC_NAME]);
			$DC_ORDER = $data[DC_ORDER] + 0;
			$DC_TYPE = $data[DC_TYPE] + 0;
			$DC_ACTIVE = $data[DC_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, 
							UPDATE_USER, UPDATE_DATE)
							VALUES ('$DC_CODE', '$DC_SHORTNAME', '$DC_NAME', $DC_ORDER, $DC_TYPE, $DC_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(DC_CODE) as COUNT_NEW from PER_DECORATION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_DECORATION - $PER_DECORATION - $COUNT_NEW<br>";

// ฐานะของหน่วยงาน
		$cmd = " SELECT OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_LEVEL ORDER BY OL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG_LEVEL++;
			$OL_CODE = trim($data[OL_CODE]);
			$OL_NAME = trim($data[OL_NAME]);
			$OL_ACTIVE = $data[OL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$OL_CODE', '$OL_NAME', $OL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(OL_CODE) as COUNT_NEW from PER_ORG_LEVEL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ORG_LEVEL - $PER_ORG_LEVEL - $COUNT_NEW<br>";

// สถานภาพหน่วยงาน 
		$cmd = " SELECT OS_CODE, OS_NAME, OS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_STAT ORDER BY OS_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG_STAT++;
			$OS_CODE = trim($data[OS_CODE]);
			$OS_NAME = trim($data[OS_NAME]);
			$OS_ACTIVE = $data[OS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ORG_STAT (OS_CODE, OS_NAME, OS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$OS_CODE', '$OS_NAME', $OS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(OS_CODE) as COUNT_NEW from PER_ORG_STAT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ORG_STAT - $PER_ORG_STAT - $COUNT_NEW<br>";

// เงินประจำตำแหน่ง  
		$cmd = " SELECT PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MGTSALARY ORDER BY PT_CODE, LEVEL_NO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_MGTSALARY++;
			$PT_CODE = trim($data[PT_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$MS_SALARY = $data[MS_SALARY] + 0;
			$MS_ACTIVE = $data[MS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PT_CODE', '$LEVEL_NO', $MS_SALARY, $MS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PT_CODE) as COUNT_NEW from PER_MGTSALARY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_MGTSALARY - $PER_MGTSALARY - $COUNT_NEW<br>";

// ประเภทข้าราชการ 
		$cmd = " SELECT OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_OFF_TYPE ORDER BY OT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_OFF_TYPE++;
			$OT_CODE = trim($data[OT_CODE]);
			$OT_NAME = trim($data[OT_NAME]);
			$OT_ACTIVE = $data[OT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$OT_CODE', '$OT_NAME', $OT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(OT_CODE) as COUNT_NEW from PER_OFF_TYPE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_OFF_TYPE - $PER_OFF_TYPE - $COUNT_NEW<br>";

// สถานภาพสมรส
		$cmd = " SELECT MR_CODE, MR_NAME, MR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MARRIED ORDER BY MR_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_MARRIED++;
			$MR_CODE = trim($data[MR_CODE]);
			$MR_NAME = trim($data[MR_NAME]);
			$MR_ACTIVE = $data[MR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_MARRIED (MR_CODE, MR_NAME, MR_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$MR_CODE', '$MR_NAME', $MR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(MR_CODE) as COUNT_NEW from PER_MARRIED ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_MARRIED - $PER_MARRIED - $COUNT_NEW<br>";

// หมู่โลหิต
// ประเภทเงินเพิ่มพิเศษ
		$cmd = " SELECT EX_CODE, EX_NAME, EX_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EXTRATYPE ORDER BY EX_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EXTRATYPE++;
			$EX_CODE = trim($data[EX_CODE]);
			$EX_NAME = trim($data[EX_NAME]);
			$EX_ACTIVE = $data[EX_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$EX_CODE', '$EX_NAME', $EX_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(EX_CODE) as COUNT_NEW from PER_EXTRATYPE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_EXTRATYPE - $PER_EXTRATYPE - $COUNT_NEW<br>";

// ประเภทเงินพิเศษ
		$cmd = " SELECT EXIN_CODE, EXIN_NAME, EXIN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EXTRA_INCOME_TYPE ORDER BY EXIN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EXTRA_INCOME_TYPE++;
			$EXIN_CODE = trim($data[EXIN_CODE]);
			$EXIN_NAME = trim($data[EXIN_NAME]);
			$EXIN_ACTIVE = $data[EXIN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_EXTRA_INCOME_TYPE (EXIN_CODE, EXIN_NAME, EXIN_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$EXIN_CODE', '$EXIN_NAME', $EXIN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(EXIN_CODE) as COUNT_NEW from PER_EXTRA_INCOME_TYPE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_EXTRA_INCOME_TYPE - $PER_EXTRA_INCOME_TYPE - $COUNT_NEW<br>";

// ช่วงระดับตำแหน่ง  
		$cmd = " SELECT CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CO_LEVEL ORDER BY CL_NAME ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_CO_LEVEL++;
			$CL_NAME = trim($data[CL_NAME]);
			$LEVEL_NO_MIN = trim($data[LEVEL_NO_MIN]);
			$LEVEL_NO_MAX = trim($data[LEVEL_NO_MAX]);
			$CL_ACTIVE = $data[CL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ('$CL_NAME', '$LEVEL_NO_MIN', '$LEVEL_NO_MAX', $CL_ACTIVE, $UPDATE_USER,	'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(CL_NAME) as COUNT_NEW from PER_CO_LEVEL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_CO_LEVEL - $PER_CO_LEVEL - $COUNT_NEW<br>";

// ประเภทโทษทางวินัย  
		$cmd = " SELECT PEN_CODE, PEN_NAME, PEN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_PENALTY ORDER BY PEN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PENALTY++;
			$PEN_CODE = trim($data[PEN_CODE]);
			$PEN_NAME = trim($data[PEN_NAME]);
			$PEN_ACTIVE = $data[PEN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_PENALTY (PEN_CODE, PEN_NAME, PEN_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PEN_CODE', '$PEN_NAME', $PEN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PEN_CODE) as COUNT_NEW from PER_PENALTY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_PENALTY - $PER_PENALTY - $COUNT_NEW<br>";

// ประเภททายาท
		$cmd = " SELECT HR_CODE, HR_NAME, HR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_HEIRTYPE ORDER BY HR_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_HEIRTYPE++;
			$HR_CODE = trim($data[HR_CODE]);
			$HR_NAME = trim($data[HR_NAME]);
			$HR_ACTIVE = $data[HR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_HEIRTYPE (HR_CODE, HR_NAME, HR_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$HR_CODE', '$HR_NAME', $HR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(HR_CODE) as COUNT_NEW from PER_HEIRTYPE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_HEIRTYPE - $PER_HEIRTYPE - $COUNT_NEW<br>";

// หัวข้อราชการพิเศษ 
		$cmd = " SELECT SRT_CODE, SRT_NAME, SRT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SERVICETITLE ORDER BY SRT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SERVICETITLE++;
			$SRT_CODE = trim($data[SRT_CODE]);
			$SRT_NAME = trim($data[SRT_NAME]);
			$SRT_NAME=str_replace("'","''",$SRT_NAME);
			$SRT_NAME=str_replace("\"","&quot;",$SRT_NAME);
			$SRT_ACTIVE = $data[SRT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SERVICETITLE (SRT_CODE, SRT_NAME, SRT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$SRT_CODE', '$SRT_NAME', $SRT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SRT_CODE) as COUNT_NEW from PER_SERVICETITLE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SERVICETITLE - $PER_SERVICETITLE - $COUNT_NEW<br>";

// ประเภทความดีความชอบ
		$cmd = " SELECT REW_CODE, REW_NAME, REW_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_REWARD ORDER BY REW_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_REWARD++;
			$REW_CODE = trim($data[REW_CODE]);
			$REW_NAME = trim($data[REW_NAME]);
			$REW_ACTIVE = $data[REW_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_REWARD (REW_CODE, REW_NAME, REW_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$REW_CODE', '$REW_NAME', $REW_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(REW_CODE) as COUNT_NEW from PER_REWARD ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_REWARD - $PER_REWARD - $COUNT_NEW<br>";

// เหตุที่ขาดจากสมรส
		$cmd = " SELECT DV_CODE, DV_NAME, DV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_DIVORCE ORDER BY DV_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_DIVORCE++;
			$DV_CODE = trim($data[DV_CODE]);
			$DV_NAME = trim($data[DV_NAME]);
			$DV_ACTIVE = $data[DV_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_DIVORCE (DV_CODE, DV_NAME, DV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$DV_CODE', '$DV_NAME', $DV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(DV_CODE) as COUNT_NEW from PER_DIVORCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_DIVORCE - $PER_DIVORCE - $COUNT_NEW<br>";

// เวลาทวีคูณ
		$cmd = " SELECT TIME_CODE, TIME_NAME, TIME_START, TIME_END, TIME_DAY, TIME_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TIME ORDER BY TIME_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_TIME++;
			$TIME_CODE = trim($data[TIME_CODE]);
			$TIME_NAME = trim($data[TIME_NAME]);
			$TIME_START = trim($data[TIME_START]);
			$TIME_END = trim($data[TIME_END]);
			$TIME_DAY = $data[TIME_DAY] + 0;
			$TIME_ACTIVE = $data[TIME_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_TIME (TIME_CODE, TIME_NAME, TIME_START, TIME_END, TIME_DAY, TIME_ACTIVE, 
							UPDATE_USER, UPDATE_DATE)
							VALUES ('$TIME_CODE', '$TIME_NAME', '$TIME_START', '$TIME_END', $TIME_DAY, $TIME_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(TIME_CODE) as COUNT_NEW from PER_TIME ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_TIME - $PER_TIME - $COUNT_NEW<br>";

// ประเภทคำสั่ง
		$cmd = " SELECT COM_TYPE, COM_NAME, COM_DESC, COM_GROUP 
						FROM PER_COMTYPE ORDER BY COM_TYPE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_COMTYPE++;
			$COM_TYPE = trim($data[COM_TYPE]);
			$COM_NAME = trim($data[COM_NAME]);
			$COM_DESC = trim($data[COM_DESC]);
			$COM_GROUP = trim($data[COM_GROUP]);

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('$COM_TYPE', '$COM_NAME', '$COM_DESC', '$COM_GROUP') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(COM_TYPE) as COUNT_NEW from PER_COMTYPE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_COMTYPE - $PER_COMTYPE - $COUNT_NEW<br>";

// หมวดตำแหน่งลูกจ้าง
		$cmd = " SELECT PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_POS_GROUP ORDER BY PG_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POS_GROUP++;
			$PG_CODE = trim($data[PG_CODE]);
			$PG_NAME = trim($data[PG_NAME]);
			$PG_ACTIVE = $data[PG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_POS_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PG_CODE', '$PG_NAME', $PG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PG_CODE) as COUNT_NEW from PER_POS_GROUP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_POS_GROUP - $PER_POS_GROUP - $COUNT_NEW<br>";

// บัญชีอัตราเงินเดือนลูกจ้างประจำ 
		$cmd = " SELECT PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_LAYEREMP ORDER BY PG_CODE, LAYERE_NO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_LAYEREMP++;
			$PG_CODE = trim($data[PG_CODE]);
			$LAYERE_NO = $data[LAYERE_NO] + 0;
			$LAYERE_SALARY = $data[LAYERE_SALARY] + 0;
			$LAYERE_DAY = $data[LAYERE_DAY] + 0;
			$LAYERE_HOUR = $data[LAYERE_HOUR] + 0;
			$LAYERE_ACTIVE = $data[LAYERE_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, 
							UPDATE_USER, UPDATE_DATE)
							VALUES ('$PG_CODE', $LAYERE_NO, $LAYERE_SALARY, $LAYERE_DAY, $LAYERE_HOUR, $LAYERE_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PG_CODE) as COUNT_NEW from PER_LAYEREMP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_LAYEREMP - $PER_LAYEREMP - $COUNT_NEW<br>";

// ชื่อตำแหน่งลูกจ้าง
		$cmd = " SELECT PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_POS_NAME ORDER BY PN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POS_NAME++;
			$PN_CODE = trim($data[PN_CODE]);
			$PN_NAME = trim($data[PN_NAME]);
			$PG_CODE = trim($data[PG_CODE]);
			$PN_DECOR = $data[PN_DECOR] + 0;
			$PN_ACTIVE = $data[PN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PN_CODE', '$PN_NAME', '$PG_CODE', $PN_DECOR, $PN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PN_CODE) as COUNT_NEW from PER_POS_NAME ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_POS_NAME - $PER_POS_NAME - $COUNT_NEW<br>";

// กลุ่มสำนักงานประจำจังหวัด
		$cmd = " SELECT OP_CODE, OP_NAME, OP_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_PROVINCE ORDER BY OP_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG_PROVINCE++;
			$OP_CODE = trim($data[OP_CODE]);
			$OP_NAME = trim($data[OP_NAME]);
			$OP_ACTIVE = $data[OP_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ORG_PROVINCE (OP_CODE, OP_NAME, OP_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$OP_CODE', '$OP_NAME', $OP_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(OP_CODE) as COUNT_NEW from PER_ORG_PROVINCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ORG_PROVINCE - $PER_ORG_PROVINCE - $COUNT_NEW<br>";

// บัญชีอัตราเงินเดือนข้าราชการใหม่
// ชื่อตำแหน่งพนักงานราชการ
// โครงสร้างตามกฏหมาย
		$cmd = " SELECT ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
						ORG_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG ORDER BY ORG_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG++;
			$ORG_ID = $data[ORG_ID] + 0;
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_SHORT = trim($data[ORG_SHORT]);
			$OL_CODE = trim($data[OL_CODE]);
			$OT_CODE = trim($data[OT_CODE]);
			$OP_CODE = trim($data[OP_CODE]);
			$OS_CODE = trim($data[OS_CODE]);
			$ORG_ADDR1 = trim($data[ORG_ADDR1]);
			$ORG_ADDR2 = trim($data[ORG_ADDR2]);
			$ORG_ADDR3 = trim($data[ORG_ADDR3]);
			$AP_CODE = trim($data[AP_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$ORG_DATE = trim($data[ORG_DATE]);
			$ORG_JOB = trim($data[ORG_JOB]);
			$ORG_ID_REF = $data[ORG_ID_REF] + 0;
			$ORG_ACTIVE = $data[ORG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
							ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
							ORG_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ($ORG_ID, '$ORG_CODE', '$ORG_NAME', '$ORG_SHORT', '$OL_CODE', '$OT_CODE', 
							'$OP_CODE', '$OS_CODE', '$ORG_ADDR1', '$ORG_ADDR2', '$ORG_ADDR3', '$AP_CODE', '$PV_CODE', 
							'$CT_CODE', '$ORG_DATE', '$ORG_JOB', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG - $COUNT_NEW<br>";

		$cmd = " SELECT ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
						ORG_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG ORDER BY ORG_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$ORG_ID = $data[ORG_ID];

			$cmd = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$ORG_ID<br>";
		} // end while						

// โครงสร้างตามมอบหมายงาน
		$cmd = " SELECT ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
						ORG_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_ASS ORDER BY ORG_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG_ASS++;
			$ORG_ID = $data[ORG_ID] + 0;
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_SHORT = trim($data[ORG_SHORT]);
			$OL_CODE = trim($data[OL_CODE]);
			$OT_CODE = trim($data[OT_CODE]);
			$OP_CODE = trim($data[OP_CODE]);
			$OS_CODE = trim($data[OS_CODE]);
			$ORG_ADDR1 = trim($data[ORG_ADDR1]);
			$ORG_ADDR2 = trim($data[ORG_ADDR2]);
			$ORG_ADDR3 = trim($data[ORG_ADDR3]);
			$AP_CODE = trim($data[AP_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$ORG_DATE = trim($data[ORG_DATE]);
			$ORG_JOB = trim($data[ORG_JOB]);
			$ORG_ID_REF = $data[ORG_ID_REF] + 0;
			$ORG_ACTIVE = $data[ORG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, 
							OS_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, 
							ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ($ORG_ID, '$ORG_CODE', '$ORG_NAME', '$ORG_SHORT', '$OL_CODE', '$OT_CODE', 
							'$OP_CODE', '$OS_CODE', '$ORG_ADDR1', '$ORG_ADDR2', '$ORG_ADDR3', '$AP_CODE', '$PV_CODE', 
							'$CT_CODE', '$ORG_DATE', '$ORG_JOB', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG_ASS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ORG_ASS - $PER_ORG_ASS - $COUNT_NEW<br>";

// กำหนดค่าเริ่มต้นของระบบ
		$cmd = " SELECT ORG_ID, UPDATE_USER, UPDATE_DATE 
						FROM PER_CONTROL ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_CONTROL++;
			$ORG_ID = $data[ORG_ID];
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_CONTROL (ORG_ID, UPDATE_USER, UPDATE_DATE)
							VALUES ($ORG_ID, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

// ชื่อตำแหน่งพนักงานราชการ
		$cmd = " SELECT DISTINCT A.PL_CODE, PL_NAME 
						FROM PER_POSITION A, PER_LINE B 
						WHERE A.PL_CODE = B.PL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EMPSER_POS_NAME++;
			$PL_CODE = trim($data[PL_CODE]);
			$PL_NAME = trim($data[PL_NAME]);

			$cmd = " INSERT INTO PER_EMPSER_POS_NAME (EP_CODE, EP_NAME, LEVEL_NO, EP_DECOR, EP_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PL_CODE', '$PL_NAME', '0', 0, 1, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(EP_CODE) as COUNT_NEW from PER_EMPSER_POS_NAME ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_EMPSER_POS_NAME - $PER_EMPSER_POS_NAME - $COUNT_NEW<br>";

// ตำแหน่งข้าราชการ
		$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
						POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, POS_REMARK, 
						POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE 
						FROM PER_POSITION ORDER BY POS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$POS_ID = $data[POS_ID] + 0;
			$ORG_ID = $data[ORG_ID];
			$POS_NO = trim($data[POS_NO]);
			$OT_CODE = trim($data[OT_CODE]);
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_2 = $data[ORG_ID_2];
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$POS_SALARY = $data[POS_SALARY] + 0;
			$POS_MGTSALARY = $data[POS_MGTSALARY] + 0;
			$SKILL_CODE = trim($data[SKILL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$PC_CODE = trim($data[PC_CODE]);
			$POS_CONDITION = trim($data[POS_CONDITION]);
			$POS_DOC_NO = trim($data[POS_DOC_NO]);
			$POS_REMARK = trim($data[POS_REMARK]);
			$POS_DATE = trim($data[POS_DATE]);
			$POS_GET_DATE = trim($data[POS_GET_DATE]);
			$POS_CHANGE_DATE = trim($data[POS_CHANGE_DATE]);
			$POS_STATUS = $data[POS_STATUS] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID_1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) $ORG_ID_1 = "NULL";

			$cmd = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID_2 ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) $ORG_ID_2 = "NULL";

			if ($OT_CODE=="01" || $OT_CODE=="02") {
				$PER_POSITION++;
				$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, 
								PM_CODE, PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, 
								POS_CONDITION, POS_DOC_NO, POS_REMARK, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, 
								UPDATE_USER, UPDATE_DATE)
								VALUES ($POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, '$PM_CODE', 
								'$PL_CODE', '$CL_NAME', $POS_SALARY, $POS_MGTSALARY, '$SKILL_CODE', '$PT_CODE', '$PC_CODE', 
								'$POS_CONDITION', '$POS_DOC_NO', '$POS_REMARK', '$POS_DATE', '$POS_GET_DATE', 
								'$POS_CHANGE_DATE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE') ";
			} else {
				$PER_POS_EMPSER++;
				$cmd = " INSERT INTO PER_POS_EMPSER (POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
								POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE)
								VALUES ($POS_ID, $ORG_ID, '$POS_NO', $ORG_ID_1, $ORG_ID_2, '$PL_CODE', $POS_SALARY, 
								$POS_SALARY, $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE') ";
			}
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_POSITION - $PER_POSITION - $COUNT_NEW<br>";

		$cmd = " select count(POEMS_ID) as COUNT_NEW from PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_POS_EMPSER - $PER_POS_EMPSER - $COUNT_NEW<br>";

		$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
						POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, POS_REMARK, 
						POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE 
						FROM PER_POSITION WHERE OT_CODE = '01' OR OT_CODE = '02' ORDER BY POS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$POS_ID = $data[POS_ID];

			$cmd = " SELECT POS_NO FROM PER_POSITION WHERE POS_ID = $POS_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$POS_ID<br>";
		} // end while						

		$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
						POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, POS_REMARK, 
						POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE 
						FROM PER_POSITION WHERE OT_CODE <> '01' AND  OT_CODE <> '02' ORDER BY POS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$POS_ID = $data[POS_ID];

			$cmd = " SELECT POEMS_NO FROM PER_POS_EMPSER WHERE POEMS_ID = $POS_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$POS_ID<br>";
		} // end while						

// ตำแหน่งลูกจ้างประจำ
		$cmd = " SELECT POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS, 
						UPDATE_USER, UPDATE_DATE 
						FROM PER_POS_EMP ORDER BY POEM_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POS_EMP++;
			$POEM_ID = $data[POEM_ID] + 0;
			$ORG_ID = $data[ORG_ID];
			$POEM_NO = trim($data[POEM_NO]);
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_2 = $data[ORG_ID_2];
			$PN_CODE = trim($data[PN_CODE]);
			$POEM_MIN_SALARY = $data[POEM_MIN_SALARY] + 0;
			$POEM_MAX_SALARY = $data[POEM_MAX_SALARY] + 0;
			$POEM_STATUS = $data[POEM_STATUS] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			if (!$PN_CODE) $PN_CODE = "840001";

			$cmd = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID_1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) $ORG_ID_1 = "NULL";

			$cmd = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID_2 ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POS_EMP (POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE)
							VALUES ($POEM_ID, $ORG_ID, '$POEM_NO', $ORG_ID_1, $ORG_ID_2, '$PN_CODE', 	$POEM_MIN_SALARY, 
							$POEM_MAX_SALARY, $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(POEM_ID) as COUNT_NEW from PER_POS_EMP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_POS_EMP - $PER_POS_EMP - $COUNT_NEW<br>";

		$cmd = " SELECT POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS, 
						UPDATE_USER, UPDATE_DATE 
						FROM PER_POS_EMP ORDER BY POEM_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$POEM_ID = $data[POEM_ID];

			$cmd = " SELECT POEM_NO FROM PER_POS_EMP WHERE POEM_ID = $POEM_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$POEM_ID<br>";
		} // end while						

// ข้าราชการ
		$cmd = " SELECT PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME,
						PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY,
						PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, PER_BLOOD, RE_CODE,
						PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE,
						PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME,
						PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS,
						UPDATE_USER, UPDATE_DATE 
						FROM PER_PERSONAL ORDER BY PER_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$PER_ID = $data[PER_ID] + 0;
			$PER_TYPE = $data[PER_TYPE];
			$OT_CODE = trim($data[OT_CODE]);
			$PN_CODE = trim($data[PN_CODE]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
			$PER_ENG_SURNAME = trim($data[PER_ENG_SURNAME]);
			$ORG_ID = $data[ORG_ID];
			$POS_ID = $data[POS_ID];
			$POEM_ID = $data[POEM_ID];
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_ORGMGT = $data[PER_ORGMGT] + 0;
			$PER_SALARY = $data[PER_SALARY] + 0;
			$PER_MGTSALARY = $data[PER_MGTSALARY] + 0;
			$PER_SPSALARY = $data[PER_SPSALARY] + 0;
			$PER_GENDER = $data[PER_GENDER];
			$MR_CODE = trim($data[MR_CODE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_OFFNO = trim($data[PER_OFFNO]);
			$PER_TAXNO = trim($data[PER_TAXNO]);
			$PER_BLOOD = trim($data[PER_BLOOD]);
			$RE_CODE = trim($data[RE_CODE]);
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			$PER_POSDATE = trim($data[PER_POSDATE]);
			$PER_SALDATE = trim($data[PER_SALDATE]);
			$PN_CODE_F = trim($data[PN_CODE_F]);
			$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
			$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			$PN_CODE_M = trim($data[PN_CODE_M]);
			$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
			$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			$PER_ADD1 = trim($data[PER_ADD1]);
			$PER_ADD2 = trim($data[PER_ADD2]);
			$PV_CODE = trim($data[PV_CODE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$PER_ORDAIN = $data[PER_ORDAIN];
			$PER_SOLDIER = $data[PER_SOLDIER];
			$PER_MEMBER = $data[PER_MEMBER];
			$PER_STATUS = $data[PER_STATUS];
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			if ($POS_ID) {
				$cmd = " SELECT POS_NO FROM PER_POSITION WHERE POS_ID = $POS_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) $PER_TYPE = 1;
				else {
					$cmd = " SELECT POEMS_NO FROM PER_POS_EMPSER WHERE POEMS_ID = $POS_ID ";
					$count_data = $db_dpis->send_cmd($cmd);
					if ($count_data) {
						$PER_TYPE = 3;
						$POEMS_ID = $POS_ID;
						$POS_ID = "NULL";
					}
				}
			}
			if ($POEM_ID) {
				$cmd = " SELECT POEM_NO FROM PER_POS_EMP WHERE POEM_ID = $POEM_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) $PER_TYPE = 2;
				else $POEM_ID = "NULL";
			}
			if (!$OT_CODE) $OT_CODE = "01";
			if (!$ORG_ID) $ORG_ID = "NULL";
			if (!$POS_ID) $POS_ID = "NULL";
			if (!$POEM_ID) $POEM_ID = "NULL";
			if (!$PER_GENDER) 
				if ($PN_CODE=="003") 
					$PER_GENDER = 1; 
				else 
					$PER_GENDER = 2;
			if (!$MR_CODE) $MR_CODE = "1";
			if (!$PER_STARTDATE) $PER_STARTDATE = "-";
			if (!$PER_OCCUPYDATE) $PER_OCCUPYDATE = $PER_STARTDATE;
			if (!$PER_ORDAIN) $PER_ORDAIN = "NULL";
			if (!$PER_SOLDIER) $PER_SOLDIER = "NULL";
			if (!$PER_MEMBER) $PER_MEMBER = "NULL";
			if (!$MOV_CODE) $MOV_CODE = "101";
			if (!$POS_ID) $POS_ID = "NULL";
			if (!$POEM_ID) $POEM_ID = "NULL";
			if (!$POEMS_ID) $POEMS_ID = "NULL";
			if (!$PER_TYPE) $PER_TYPE = 2;
			if (($PER_TYPE==1 && $POS_ID=="NULL") || ($PER_TYPE==2 && $POEM_ID=="NULL") || ($PER_TYPE==3 && $POEMS_ID=="NULL")) $PER_STATUS = 2;

			$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, 
							PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, 
							PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
							PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
							PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
							PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, 
							PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, UPDATE_USER, UPDATE_DATE, POEMS_ID)
							VALUES ($PER_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', '$PER_NAME', '$PER_SURNAME', 
							'$PER_ENG_NAME', '$PER_ENG_SURNAME', $ORG_ID, $POS_ID, $POEM_ID, '$LEVEL_NO', $PER_ORGMGT, 
							$PER_SALARY, $PER_MGTSALARY, $PER_SPSALARY, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', '$PER_RETIREDATE', 
							'$PER_STARTDATE', '$PER_OCCUPYDATE', '$PER_POSDATE', '$PER_SALDATE', '$PN_CODE_F', 
							'$PER_FATHERNAME', '$PER_FATHERSURNAME', '$PN_CODE_M', '$PER_MOTHERNAME', 
							'$PER_MOTHERSURNAME', '$PER_ADD1', '$PER_ADD2', '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, 
							$PER_SOLDIER, $PER_MEMBER, $PER_STATUS, $UPDATE_USER, '$UPDATE_DATE', $POEMS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while				

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";

		$cmd = " SELECT PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME,
						PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY,
						PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, PER_BLOOD, RE_CODE,
						PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE,
						PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME,
						PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS,
						UPDATE_USER, UPDATE_DATE 
						FROM PER_PERSONAL ORDER BY PER_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID];

			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$PER_ID<br>";
		} // end while						

// โครงสร้างการแบ่งงาน
		$cmd = " SELECT PER_ID, ORG_ID, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_JOB ORDER BY PER_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG_JOB++;
			$PER_ID = $data[PER_ID] + 0;
			$ORG_ID = $data[ORG_ID];
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ORG_JOB (PER_ID, ORG_ID, UPDATE_USER, UPDATE_DATE)
							VALUES ($PER_ID, $ORG_ID, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ORG_JOB - $PER_ORG_JOB - $COUNT_NEW<br>";

// ประวัติการดำรงตำแหน่ง  		
		$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
						POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, 
						ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, 
						POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE 
						FROM PER_POSITIONHIS ORDER BY POH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POSITIONHIS++;
			$POH_ID = $data[POH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$POH_ENDDATE = trim($data[POH_ENDDATE]);
			$POH_DOCNO = trim($data[POH_DOCNO]);
			$POH_DOCDATE = trim($data[POH_DOCDATE]);
			$POH_POS_NO = trim($data[POH_POS_NO]);
			$PM_CODE = trim($data[PM_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PL_CODE = trim($data[PL_CODE]);
			$PN_CODE = trim($data[PN_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$AP_CODE = trim($data[AP_CODE]);
			$POH_ORGMGT = $data[POH_ORGMGT];
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_2 = $data[ORG_ID_2];
			$ORG_ID_3 = $data[ORG_ID_3];
			$POH_UNDER_ORG1 = trim($data[POH_UNDER_ORG1]);
			$POH_UNDER_ORG2 = trim($data[POH_UNDER_ORG2]);
			$POH_ASS_ORG = trim($data[POH_ASS_ORG]);
			$POH_ASS_ORG1 = trim($data[POH_ASS_ORG1]);
			$POH_ASS_ORG2 = trim($data[POH_ASS_ORG2]);
			$POH_SALARY = $data[POH_SALARY] + 0;
			$POH_SALARY_POS = $data[POH_SALARY_POS] + 0;
			$POH_REMARK = trim($data[POH_REMARK]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			if ($POH_REMARK=="'") $POH_REMARK = "-";

			$cmd = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID_1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) $ORG_ID_1 = "NULL";

			$cmd = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID_2 ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) $ORG_ID_2 = "NULL";

			$cmd = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID_3 ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) $ORG_ID_3 = "NULL";

			$cmd = " INSERT INTO PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, 
							POH_DOCDATE, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
							POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, 
							POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE)
							VALUES ($POH_ID, $PER_ID, '$POH_EFFECTIVEDATE', '$MOV_CODE', '$POH_ENDDATE', '$POH_DOCNO', 
							'$POH_DOCDATE', '$POH_POS_NO', '$PM_CODE', '$LEVEL_NO', '$PL_CODE', '$PN_CODE', '$PT_CODE', '$CT_CODE', 
							'$PV_CODE', '$AP_CODE', $POH_ORGMGT, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$POH_UNDER_ORG1', 
							'$POH_UNDER_ORG2', '$POH_ASS_ORG', '$POH_ASS_ORG1', '$POH_ASS_ORG2', $POH_SALARY, 
							$POH_SALARY_POS, '$POH_REMARK', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						
		
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_POSITIONHIS - $PER_POSITIONHIS - $COUNT_NEW<br>";

		$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
						POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, 
						ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, 
						POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE 
						FROM PER_POSITIONHIS ORDER BY POH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$POH_ID = $data[POH_ID];

			$cmd = " SELECT PER_ID FROM PER_POSITIONHIS WHERE POH_ID = $POH_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$POH_ID<br>";
		} // end while						

// ประวัติการรับเงินเดือน  
		$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, 
						SAH_ENDDATE, UPDATE_USER,	UPDATE_DATE FROM PER_SALARYHIS 
						ORDER BY SAH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_SALARYHIS++;
				$SAH_ID = $data[SAH_ID] + 0;
				$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
				$MOV_CODE = trim($data[MOV_CODE]);
				$SAH_SALARY = $data[SAH_SALARY] + 0;
				$SAH_DOCNO = trim($data[SAH_DOCNO]);
				$SAH_DOCDATE = trim($data[SAH_DOCDATE]);
				$SAH_ENDDATE = trim($data[SAH_ENDDATE]);
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " INSERT INTO PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
								SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE)
								VALUES ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO', '$SAH_DOCDATE', 
								'$SAH_ENDDATE', 	$UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if						
		} // end while						
		
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SALARYHIS - $PER_SALARYHIS - $COUNT_NEW<br>";

		$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, 
						SAH_ENDDATE, UPDATE_USER,	UPDATE_DATE FROM PER_SALARYHIS 
						ORDER BY SAH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$SAH_ID = $data[SAH_ID];

				$cmd = " SELECT PER_ID FROM PER_SALARYHIS WHERE SAH_ID = $SAH_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$SAH_ID<br>";
			} // end if						
		} // end while						

// ประวัติการรับเงินเพิ่มพิเศษ  
		$cmd = " SELECT EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, UPDATE_USER, UPDATE_DATE
						FROM PER_EXTRAHIS 
						ORDER BY EXH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EXTRAHIS++;
			$EXH_ID = $data[EXH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EXH_EFFECTIVEDATE = trim($data[EXH_EFFECTIVEDATE]);
			$EX_CODE = trim($data[EX_CODE]);
			$EXH_AMT = $data[EXH_AMT] + 0;
			$EXH_ENDDATE = trim($data[EXH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, 
							UPDATE_USER, UPDATE_DATE)
							VALUES ($EXH_ID, $PER_ID, '$EXH_EFFECTIVEDATE', '$EX_CODE', $EXH_AMT, '$EXH_ENDDATE', $UPDATE_USER, 
							'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_EXTRAHIS - $PER_EXTRAHIS - $COUNT_NEW<br>";

// ประวัติการศึกษา  		
		$cmd = " SELECT EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, EDU_FUND, EN_CODE, 
						EM_CODE, INS_CODE,	EDU_TYPE, UPDATE_USER, UPDATE_DATE FROM PER_EDUCATE 
						ORDER BY EDU_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_EDUCATE++;
				$EDU_ID = $data[EDU_ID] + 0;
				$EDU_SEQ = trim($data[EDU_SEQ]);
				$EDU_STARTYEAR = trim($data[EDU_STARTYEAR]);
				$EDU_ENDYEAR = trim($data[EDU_ENDYEAR]);
				$ST_CODE = trim($data[ST_CODE]);
				$CT_CODE = trim($data[CT_CODE]);
				$EDU_FUND = trim($data[EDU_FUND]);
				$EN_CODE = trim($data[EN_CODE]);
				$EM_CODE = trim($data[EM_CODE]);
				$INS_CODE = trim($data[INS_CODE]);
				$EDU_TYPE = trim($data[EDU_TYPE]);
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);
				if (!$EDU_ENDYEAR) $EDU_ENDYEAR = "-";
				if (!$EDU_STARTYEAR) $EDU_STARTYEAR = $EDU_ENDYEAR;
				if (!$EDU_TYPE) $EDU_TYPE = "3";
	
				$cmd = " SELECT EN_NAME FROM PER_EDUCNAME WHERE EN_CODE = '$EN_CODE' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) $EN_CODE = "NULL"; else $EN_CODE = "'$EN_CODE'";

				$cmd = " SELECT INS_NAME FROM PER_INSTITUTE WHERE INS_CODE = '$INS_CODE' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) $INS_CODE = "NULL"; else $INS_CODE = "'$INS_CODE'";

				$cmd = " INSERT INTO PER_EDUCATE (EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
								EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE)
								VALUES ($EDU_ID, $PER_ID, $EDU_SEQ, '$EDU_STARTYEAR', '$EDU_ENDYEAR', '$ST_CODE', '$CT_CODE', 
								'$EDU_FUND', $EN_CODE, '$EM_CODE', $INS_CODE, '$EDU_TYPE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_EDUCATE - $PER_EDUCATE - $COUNT_NEW<br>";

		$cmd = " SELECT EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, EDU_FUND, EN_CODE, 
						EM_CODE, INS_CODE,	EDU_TYPE, UPDATE_USER, UPDATE_DATE FROM PER_EDUCATE 
						ORDER BY EDU_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$EDU_ID = $data[EDU_ID] + 0;

				$cmd = " SELECT PER_ID FROM PER_EDUCATE WHERE EDU_ID = $EDU_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$EDU_ID<br>";
			}
		} // end while						

// ประวัติการอบรม/ดูงาน/สัมมนา  
		$cmd = " SELECT TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, TRN_PLACE, 
						CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE FROM PER_TRAINING 
						ORDER BY TRN_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_TRAINING++;
				$TRN_ID = $data[TRN_ID] + 0;
				$TRN_TYPE = $data[TRN_TYPE] + 0;
				$TR_CODE = trim($data[TR_CODE]);
				$TRN_NO = trim($data[TRN_NO]);
				$TRN_STARTDATE = trim($data[TRN_STARTDATE]);
				$TRN_ENDDATE = trim($data[TRN_ENDDATE]);
				$TRN_ORG = trim($data[TRN_ORG]);
				$TRN_ORG=str_replace("'","''",$TRN_ORG);
				$TRN_ORG=str_replace("\"","&quot;",$TRN_ORG);
				$TRN_PLACE = trim($data[TRN_PLACE]);
				$TRN_PLACE=str_replace("'","''",$TRN_PLACE);
				$TRN_PLACE=str_replace("\"","&quot;",$TRN_PLACE);
				$CT_CODE = trim($data[CT_CODE]);
				$TRN_FUND = trim($data[TRN_FUND]);
				$CT_CODE_FUND = trim($data[CT_CODE_FUND]);
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);
				if ($TR_CODE=="100003582") $TR_CODE = "1000003582";
				if ($TR_CODE=="100003809") $TR_CODE = "1000003809";
				if ($TR_CODE=="100004083") $TR_CODE = "1000004083";
				if ($TR_CODE=="100004757") $TR_CODE = "1000004757";
				if ($TR_CODE=="100004898") $TR_CODE = "1000004898";
				if ($TR_CODE=="100004904") $TR_CODE = "1000004904";
				if ($TR_CODE=="100004911") $TR_CODE = "1000004911";
				if ($TR_CODE=="100005880") $TR_CODE = "1000005880";
				if (!$UPDATE_DATE) $UPDATE_DATE = "-";

				$cmd = " SELECT TR_NAME FROM PER_TRAIN WHERE TR_CODE = '$TR_CODE' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {
					$cmd = " INSERT INTO PER_TRAINING (TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, 
									TRN_ORG, TRN_PLACE, CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE)
									VALUES ($TRN_ID, $PER_ID, $TRN_TYPE, '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', 
									'$TRN_ORG', '$TRN_PLACE', '$CT_CODE', '$TRN_FUND', '$CT_CODE_FUND', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
			} else	echo "รหัส $TR_CODE ไม่มี";
			}
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_TRAINING - $PER_TRAINING - $COUNT_NEW<br>";

		$cmd = " SELECT TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, TRN_PLACE, 
						CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE FROM PER_TRAINING 
						ORDER BY TRN_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$TRN_ID = $data[TRN_ID] + 0;
				$TR_CODE = trim($data[TR_CODE]);

				$cmd = " SELECT TR_NAME FROM PER_TRAIN WHERE TR_CODE = '$TR_CODE' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {
					$cmd = " SELECT PER_ID FROM PER_TRAINING WHERE TRN_ID = $TRN_ID ";
					$count_data = $db_dpis->send_cmd($cmd);
					if (!$count_data) echo "$TRN_ID<br>";
				}
			}
		} // end while						

// ความสามารถพิเศษ  
		$cmd = " SELECT ABI_ID, PER_ID, AL_CODE, ABI_DESC, UPDATE_USER, UPDATE_DATE FROM PER_ABILITY 
						ORDER BY ABI_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ABILITY++;
			$ABI_ID = $data[ABI_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$AL_CODE = trim($data[AL_CODE]);
			$ABI_DESC = trim($data[ABI_DESC]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_ABILITY (ABI_ID, PER_ID, AL_CODE, ABI_DESC, UPDATE_USER, UPDATE_DATE)
							VALUES ($ABI_ID, $PER_ID, '$AL_CODE', '$ABI_DESC', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ABILITY - $PER_ABILITY - $COUNT_NEW<br>";

// ความเชี่ยวชาญพิเศษ  
		$cmd = " SELECT SPS_ID, PER_ID, SS_CODE, SPS_EMPHASIZE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SPECIAL_SKILL 
						ORDER BY SPS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SPECIAL_SKILL++;
			$SPS_ID = $data[SPS_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$SS_CODE = trim($data[SS_CODE]);
			$SPS_EMPHASIZE = trim($data[SPS_EMPHASIZE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, SS_CODE, SPS_EMPHASIZE, UPDATE_USER, UPDATE_DATE)
							VALUES ($SPS_ID, $PER_ID, '$SS_CODE', '$SPS_EMPHASIZE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SPECIAL_SKILL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SPECIAL_SKILL - $PER_SPECIAL_SKILL - $COUNT_NEW<br>";

// ทายาท  
		$cmd = " SELECT HEIR_ID, PER_ID, HR_CODE, HEIR_NAME, HEIR_STATUS, HEIR_BIRTHDAY, HEIR_TAX, UPDATE_USER, 
						UPDATE_DATE FROM PER_HEIR 
						ORDER BY HEIR_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_HEIR++;
			$HEIR_ID = $data[HEIR_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$HR_CODE = trim($data[HR_CODE]);
			$HEIR_NAME = trim($data[HEIR_NAME]);
			$HEIR_STATUS = $data[HEIR_STATUS] + 0;
			$HEIR_BIRTHDAY = trim($data[HEIR_BIRTHDAY]);
			$HEIR_TAX = trim($data[HEIR_TAX]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_HEIR (HEIR_ID, PER_ID, HR_CODE, HEIR_NAME, HEIR_STATUS, HEIR_BIRTHDAY, HEIR_TAX, 
							UPDATE_USER, UPDATE_DATE)
							VALUES ($HEIR_ID, $PER_ID, '$HR_CODE', '$HEIR_NAME', $HEIR_STATUS, '$HEIR_BIRTHDAY', '$HEIR_TAX', 
							$UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_HEIR - $PER_HEIR - $COUNT_NEW<br>";

// ประวัติการลา  		
		$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, 
						UPDATE_USER,	UPDATE_DATE FROM PER_ABSENTHIS 
						ORDER BY ABS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_ABSENTHIS++;
				$ABS_ID = $data[ABS_ID] + 0;
				$AB_CODE = trim($data[AB_CODE]);
				$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
				$ABS_STARTPERIOD = $data[ABS_STARTPERIOD] + 0;
				$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
				$ABS_ENDPERIOD = $data[ABS_ENDPERIOD] + 0;
				$ABS_DAY = $data[ABS_DAY] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " INSERT INTO PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
								ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE)
								VALUES ($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', 
								$ABS_ENDPERIOD, $ABS_DAY, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if						
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";

		$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, 
						UPDATE_USER,	UPDATE_DATE FROM PER_ABSENTHIS 
						ORDER BY ABS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$ABS_ID = $data[ABS_ID] + 0;

				$cmd = " SELECT PER_ID FROM PER_ABSENTHIS WHERE ABS_ID = $ABS_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$ABS_ID<br>";
			}
		} // end while						

// ประวัติทางวินัย  
		$cmd = " SELECT PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, PUN_STARTDATE, PUN_ENDDATE, CRD_CODE, 
						PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, UPDATE_DATE FROM PER_PUNISHMENT 
						ORDER BY PUN_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_PUNISHMENT++;
				$PUN_ID = $data[PUN_ID] + 0;
				$INV_NO = trim($data[INV_NO]);
				$PUN_NO = trim($data[PUN_NO]);
				$PUN_REF_NO = trim($data[PUN_REF_NO]);
				$PUN_TYPE = $data[PUN_TYPE] + 0;
				$PUN_STARTDATE = trim($data[PUN_STARTDATE]);
				$PUN_ENDDATE = trim($data[PUN_ENDDATE]);
				$CRD_CODE = trim($data[CRD_CODE]);
				$PEN_CODE = trim($data[PEN_CODE]);
				$PUN_PAY = $data[PUN_PAY] + 0;
				$PUN_SALARY = $data[PUN_SALARY] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " INSERT INTO PER_PUNISHMENT (PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, PUN_STARTDATE, 
								PUN_ENDDATE, CRD_CODE, PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, UPDATE_DATE)
								VALUES ($PUN_ID, $PER_ID, '$INV_NO', '$PUN_NO', '$PUN_REF_NO', $PUN_TYPE, '$PUN_STARTDATE', '$PUN_ENDDATE', 
								'$CRD_CODE', '$PEN_CODE', 	$PUN_PAY, $PUN_SALARY, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if						
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_PUNISHMENT - $PER_PUNISHMENT - $COUNT_NEW<br>";

// ประวัติราชการพิเศษ  
		$cmd = " SELECT SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, SRH_DOCNO, 
						PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE FROM PER_SERVICEHIS 
						ORDER BY SRH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_SERVICEHIS++;
				$SRH_ID = $data[SRH_ID] + 0;
				$SV_CODE = trim($data[SV_CODE]);
				$SRT_CODE = trim($data[SRT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$SRH_STARTDATE = trim($data[SRH_STARTDATE]);
				$SRH_ENDDATE = trim($data[SRH_ENDDATE]);
				$SRH_NOTE = trim($data[SRH_NOTE]);
				$SRH_DOCNO = trim($data[SRH_DOCNO]);
				$PER_ID_ASSIGN = $data[PER_ID_ASSIGN];
				$ORG_ID_ASSIGN = $data[ORG_ID_ASSIGN];
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);
				if (!$ORG_ID) $ORG_ID = 153;
				if (!$PER_ID_ASSIGN) $PER_ID_ASSIGN = "NULL";
				if (!$ORG_ID_ASSIGN) $ORG_ID_ASSIGN = "NULL";

				$cmd = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) $ORG_ID = 153;

				$cmd = " INSERT INTO PER_SERVICEHIS (SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, 
								SRH_NOTE, SRH_DOCNO, PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE)
								VALUES ($SRH_ID, $PER_ID, '$SV_CODE', '$SRT_CODE', $ORG_ID, '$SRH_STARTDATE', '$SRH_ENDDATE', 
								'$SRH_NOTE', '$SRH_DOCNO', 	$PER_ID_ASSIGN, $ORG_ID_ASSIGN, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if						
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SERVICEHIS - $PER_SERVICEHIS - $COUNT_NEW<br>";

		$cmd = " SELECT SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, SRH_DOCNO, 
						PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE FROM PER_SERVICEHIS 
						ORDER BY SRH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$SRH_ID = $data[SRH_ID] + 0;

				$cmd = " SELECT PER_ID FROM PER_SERVICEHIS WHERE SRH_ID = $SRH_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$SRH_ID<br>";
			}
		} // end while						

// ประวัติความดีความชอบ  
		$cmd = " SELECT REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, UPDATE_DATE
						FROM PER_REWARDHIS 
						ORDER BY REH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_REWARDHIS++;
			$REH_ID = $data[REH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$REW_CODE = trim($data[REW_CODE]);
			$REH_ORG = trim($data[REH_ORG]);
			$REH_DOCNO = trim($data[REH_DOCNO]);
			$REH_DATE = trim($data[REH_DATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$cmd = " INSERT INTO PER_REWARDHIS (REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ($REH_ID, $PER_ID, '$REW_CODE', '$REH_ORG', '$REH_DOCNO', '$REH_DATE', $UPDATE_USER, 
							'$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_REWARDHIS - $PER_REWARDHIS - $COUNT_NEW<br>";

// ประวัติการสมรส   
		$cmd = " SELECT MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, UPDATE_USER, 
						UPDATE_DATE FROM PER_MARRHIS 
						ORDER BY MAH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_MARRHIS++;
				$MAH_ID = $data[MAH_ID] + 0;
				$MAH_SEQ = $data[MAH_SEQ] + 0;
				$MAH_NAME = trim($data[MAH_NAME]);
				$MAH_MARRY_DATE = trim($data[MAH_MARRY_DATE]);
				$MAH_DIVORCE_DATE = trim($data[MAH_DIVORCE_DATE]);
				$DV_CODE = trim($data[DV_CODE]);
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);
				$cmd = " INSERT INTO PER_MARRHIS (MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, 
								DV_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ($MAH_ID, $PER_ID, $MAH_SEQ, '$MAH_NAME', '$MAH_MARRY_DATE', '$MAH_DIVORCE_DATE', '$DV_CODE', 
								$UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if						
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_MARRHIS - $PER_MARRHIS - $COUNT_NEW<br>";

		$cmd = " SELECT MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, UPDATE_USER, 
						UPDATE_DATE FROM PER_MARRHIS 
						ORDER BY MAH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$MAH_ID = $data[MAH_ID] + 0;

				$cmd = " SELECT PER_ID FROM PER_MARRHIS WHERE MAH_ID = $MAH_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$MAH_ID<br>";
			}
		} // end while						

// ประวัติการเปลี่ยนชื่อ-สกุล  
		$cmd = " SELECT NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, UPDATE_USER, UPDATE_DATE
						FROM PER_NAMEHIS 
						ORDER BY NH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_NAMEHIS++;
				$NH_ID = $data[NH_ID] + 0;
				$NH_DATE = trim($data[NH_DATE]);
				$PN_CODE = trim($data[PN_CODE]);
				$NH_NAME = trim($data[NH_NAME]);
				$NH_SURNAME = trim($data[NH_SURNAME]);
				$NH_DOCNO = trim($data[NH_DOCNO]);
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " INSERT INTO PER_NAMEHIS (NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
								UPDATE_USER, UPDATE_DATE)
								VALUES ($NH_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', $UPDATE_USER, 
								'$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if						
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_NAMEHIS - $PER_NAMEHIS - $COUNT_NEW<br>";

		$cmd = " SELECT NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, UPDATE_USER, UPDATE_DATE
						FROM PER_NAMEHIS 
						ORDER BY NH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$NH_ID = $data[NH_ID] + 0;

				$cmd = " SELECT PER_ID FROM PER_NAMEHIS WHERE NH_ID = $NH_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$NH_ID<br>";
			}
		} // end while						

// ประวัติรับพระราชทานเครื่องราชฯ  
		$cmd = " SELECT DEH_ID, PER_ID, DC_CODE, DEH_DATE, DEH_GAZETTE, UPDATE_USER, UPDATE_DATE 
						FROM PER_DECORATEHIS 
						ORDER BY DEH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_DECORATEHIS++;
			$DEH_ID = $data[DEH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$DC_CODE = trim($data[DC_CODE]);
			$DEH_DATE = trim($data[DEH_DATE]);
			$DEH_GAZETTE = trim($data[DEH_GAZETTE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, DEH_GAZETTE, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ($DEH_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$DEH_GAZETTE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_DECORATEHIS - $PER_DECORATEHIS - $COUNT_NEW<br>";

// เวลาทวีคูณ  
		$cmd = " SELECT TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, UPDATE_USER, UPDATE_DATE 
						FROM PER_TIMEHIS 
						ORDER BY TIMEH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_TIMEHIS++;
			$TIMEH_ID = $data[TIMEH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$TIME_CODE = trim($data[TIME_CODE]);
			$TIMEH_MINUS = $data[TIMEH_MINUS] + 0;
			$TIMEH_REMARK = trim($data[TIMEH_REMARK]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_TIMEHIS (TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ($TIMEH_ID, $PER_ID, '$TIME_CODE', $TIMEH_MINUS, '$TIMEH_REMARK', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_TIMEHIS - $PER_TIMEHIS - $COUNT_NEW<br>";

// ประวัติการรับเงินพิเศษ  		
		$cmd = " SELECT EXINH_ID, PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, EXINH_ENDDATE, UPDATE_USER, 
						UPDATE_DATE FROM PER_EXTRA_INCOMEHIS 
						ORDER BY EXINH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EXTRA_INCOMEHIS++;
			$EXINH_ID = $data[EXINH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EXINH_EFFECTIVEDATE = trim($data[EXINH_EFFECTIVEDATE]);
			$EXIN_CODE = trim($data[EXIN_CODE]);
			$EXINH_AMT = $data[EXINH_AMT] + 0;
			$EXINH_ENDDATE = trim($data[EXINH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_EXTRA_INCOMEHIS (EXINH_ID, PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, 
							EXINH_ENDDATE, UPDATE_USER, UPDATE_DATE)
							VALUES ($EXINH_ID, $PER_ID, '$EXINH_EFFECTIVEDATE', '$EXIN_CODE', $EXINH_AMT, '$EXINH_ENDDATE', 
							$UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						 

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_EXTRA_INCOMEHIS - $PER_EXTRA_INCOMEHIS - $COUNT_NEW<br>";

// คำสั่ง  		
		$cmd = " SELECT COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, COM_TYPE, COM_CONFIRM, UPDATE_USER, 
						UPDATE_DATE FROM PER_COMMAND 
						ORDER BY COM_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_COMMAND++;
			$COM_ID = $data[COM_ID] + 0;
			$COM_NO = trim($data[COM_NO]);
			$COM_NAME = trim($data[COM_NAME]);
			$COM_DATE = trim($data[COM_DATE]);
			$COM_NOTE = trim($data[COM_NOTE]);
			$COM_PER_TYPE = $data[COM_PER_TYPE] + 0;
			$COM_TYPE = trim($data[COM_TYPE]);
			$COM_CONFIRM = $data[COM_CONFIRM] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, COM_TYPE, COM_CONFIRM, 
							UPDATE_USER, UPDATE_DATE)
							VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, '$COM_TYPE', $COM_CONFIRM, 
							$UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						 

		$cmd = " select count(COM_ID) as COUNT_NEW from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_COMMAND - $PER_COMMAND - $COUNT_NEW<br>";

// ข้อมูลบัญชีแนบท้ายคำสั่ง
		$cmd = " SELECT COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3,       
						CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY, PL_CODE, PN_CODE, CMD_AC_NO, CMD_ACCOUNT, POS_ID, POEM_ID, LEVEL_NO,       
						CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, 
						UPDATE_USER, UPDATE_DATE 
						FROM PER_COMDTL ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_COMDTL++;
				$COM_ID = $data[COM_ID] + 0;
				$CMD_SEQ = $data[CMD_SEQ] + 0;
				$EN_CODE = trim($data[EN_CODE]);
				$CMD_DATE = trim($data[CMD_DATE]);
				$CMD_POSITION = trim($data[CMD_POSITION]);
				$CMD_LEVEL = trim($data[CMD_LEVEL]);
				$CMD_ORG1 = trim($data[CMD_ORG1]);
				$CMD_ORG2 = trim($data[CMD_ORG2]);
				$CMD_ORG3 = trim($data[CMD_ORG3]);
				$CMD_ORG4 = trim($data[CMD_ORG4]);
				$CMD_ORG5 = trim($data[CMD_ORG5]);
				$CMD_OLD_SALARY = $data[CMD_OLD_SALARY] + 0;
				$PL_CODE = trim($data[PL_CODE]);
				$PN_CODE = trim($data[PN_CODE]);
				$CMD_AC_NO = trim($data[CMD_AC_NO]);
				$CMD_ACCOUNT = trim($data[CMD_ACCOUNT]);
				$POS_ID = $data[POS_ID];
				$POEM_ID = $data[POEM_ID];
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$CMD_SALARY = $data[CMD_SALARY] + 0;
				$CMD_SPSALARY = $data[CMD_SPSALARY] + 0;
				$PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
				$PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
				$CMD_NOTE1 = trim($data[CMD_NOTE1]);
				$CMD_NOTE2 = trim($data[CMD_NOTE2]);
				$MOV_CODE = trim($data[MOV_CODE]);
				$CMD_SAL_CONFIRM = $data[CMD_SAL_CONFIRM] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);
				if (!$POS_ID) $POS_ID = "NULL";
				if (!$POEM_ID) $POEM_ID = "NULL";
	
				$cmd = " SELECT POEM_NO FROM PER_POS_EMP WHERE POEM_ID = $POEM_ID ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) $POEM_ID = "NULL";

				$cmd = " INSERT INTO PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, CMD_ORG1, 
								CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY, PL_CODE, PN_CODE, CMD_AC_NO, CMD_ACCOUNT, 
								POS_ID, POEM_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, CMD_NOTE1, 
								CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, UPDATE_USER, UPDATE_DATE)
								VALUES ($COM_ID, $CMD_SEQ, $PER_ID, '$EN_CODE', '$CMD_DATE', '$CMD_POSITION', '$CMD_LEVEL', '$CMD_ORG1', 
								'$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, '$PL_CODE', '$PN_CODE', '$CMD_AC_NO', 
								'$CMD_ACCOUNT', $POS_ID, $POEM_ID, '$LEVEL_NO', $CMD_SALARY, $CMD_SPSALARY, '$PL_CODE_ASSIGN', 
								'$PN_CODE_ASSIGN', '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', $CMD_SAL_CONFIRM, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if						
		} // end while						

		$cmd = " select count(COM_ID) as COUNT_NEW from PER_COMDTL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_COMDTL - $PER_COMDTL - $COUNT_NEW<br>";

		$cmd = " SELECT COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3,       
						CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY, PL_CODE, PN_CODE, CMD_AC_NO, CMD_ACCOUNT, POS_ID, POEM_ID, LEVEL_NO,       
						CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, 
						UPDATE_USER, UPDATE_DATE 
						FROM PER_COMDTL ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$COM_ID = $data[COM_ID] + 0;
				$CMD_SEQ = $data[CMD_SEQ] + 0;

				$cmd = " SELECT PER_ID FROM PER_COMDTL WHERE COM_ID = $COM_ID AND CMD_SEQ = $CMD_SEQ ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$COM_ID - $CMD_SEQ<br>";
			}
		} // end while						

// การขอย้าย
		$cmd = " SELECT MV_ID, PER_ID, MV_DATE, PL_CODE_1, PN_CODE_1, ORG_ID_1, PL_CODE_2, PN_CODE_2, ORG_ID_2, PL_CODE_3, 
						PN_CODE_3, ORG_ID_3, MV_REASON, MV_REMARK, UPDATE_USER, UPDATE_DATE 
						FROM PER_MOVE_REQ ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_MOVE_REQ++;
			$MV_ID = $data[MV_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$MV_DATE = trim($data[MV_DATE]);
			$PL_CODE_1 = trim($data[PL_CODE_1]);
			$PN_CODE_1 = trim($data[PN_CODE_1]);
			$ORG_ID_1 = $data[ORG_ID_1];
			$PL_CODE_2 = trim($data[PL_CODE_2]);
			$PN_CODE_2 = trim($data[PN_CODE_2]);
			$ORG_ID_2 = $data[ORG_ID_2];
			$PL_CODE_3 = trim($data[PL_CODE_3]);
			$PN_CODE_3 = trim($data[PN_CODE_3]);
			$ORG_ID_3 = $data[ORG_ID_3];
			$MV_REASON = trim($data[MV_REASON]);
			$MV_REMARK = trim($data[MV_REMARK]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
			if (!$ORG_ID_3) $ORG_ID_3 = "NULL";

			$cmd = " INSERT INTO PER_MOVE_REQ (MV_ID, PER_ID, MV_DATE, PL_CODE_1, PN_CODE_1, ORG_ID_1, PL_CODE_2, PN_CODE_2, 
							ORG_ID_2, PL_CODE_3, PN_CODE_3, ORG_ID_3, MV_REASON, MV_REMARK, UPDATE_USER, UPDATE_DATE)
							VALUES ($MV_ID, $PER_ID, '$MV_DATE', '$PL_CODE_1', '$PN_CODE_1', $ORG_ID_1, '$PL_CODE_2', '$PN_CODE_2', $ORG_ID_2, 
							'$PL_CODE_3', '$PN_CODE_3', $ORG_ID_3, '$MV_REASON', '$MV_REMARK', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(MV_ID) as COUNT_NEW from PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_MOVE_REQ - $PER_MOVE_REQ - $COUNT_NEW<br>";

// ข้าราชการมีคุณสมบัติได้เลื่อนตำแหน่ง
		$cmd = " SELECT PRO_DATE, POS_ID, PER_ID, PRO_SUMMARY,	UPDATE_USER, UPDATE_DATE 
						FROM PER_PROMOTE_P ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PROMOTE_P++;
			$PRO_DATE = trim($data[PRO_DATE]);
			$POS_ID = $data[POS_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$PRO_SUMMARY = trim($data[PRO_SUMMARY]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_PROMOTE_P (PRO_DATE, POS_ID, PER_ID, PRO_SUMMARY, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PRO_DATE', $POS_ID, $PER_ID, '$PRO_SUMMARY', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(*) as COUNT_NEW from PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_PROMOTE_P - $PER_PROMOTE_P - $COUNT_NEW<br>";

// โควตาเลื่อนขั้นเงินเดือน
		$cmd = " SELECT SALQ_YEAR, SALQ_TYPE, SALQ_PERCENT, SALQ_DATE,	UPDATE_USER, UPDATE_DATE 
						FROM PER_SALQUOTA ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SALQUOTA++;
			$SALQ_YEAR = trim($data[SALQ_YEAR]);
			$SALQ_TYPE = $data[SALQ_TYPE] + 0;
			$SALQ_PERCENT = $data[SALQ_PERCENT] + 0;
			$SALQ_DATE = trim($data[SALQ_DATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE, SALQ_PERCENT, SALQ_DATE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$SALQ_YEAR', $SALQ_TYPE, $SALQ_PERCENT, '$SALQ_DATE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(*) as COUNT_NEW from PER_SALQUOTA ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SALQUOTA - $PER_SALQUOTA - $COUNT_NEW<br>";

// รายละเอียดโควตาเลื่อนขั้นเงินเดือน โครงสร้างตามกฎหมาย
		$cmd = " SELECT SALQ_YEAR, SALQ_TYPE, ORG_ID, SALQD_QTY1, SALQD_QTY2, UPDATE_USER, UPDATE_DATE 
						FROM PER_SALQUOTA_DTL1 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SALQUOTA_DTL1++;
			$SALQ_YEAR = trim($data[SALQ_YEAR]);
			$SALQ_TYPE = $data[SALQ_TYPE];
			$ORG_ID = $data[ORG_ID];
			$SALQD_QTY1 = $data[SALQD_QTY1] + 0;
			$SALQD_QTY2 = $data[SALQD_QTY2] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SALQUOTA_DTL1 (SALQ_YEAR, SALQ_TYPE, ORG_ID, SALQD_QTY1, SALQD_QTY2, UPDATE_USER, UPDATE_DATE)
							VALUES ('$SALQ_YEAR', $SALQ_TYPE, $ORG_ID, $SALQD_QTY1, $SALQD_QTY2, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(*) as COUNT_NEW from PER_SALQUOTA_DTL1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SALQUOTA_DTL1 - $PER_SALQUOTA_DTL1 - $COUNT_NEW<br>";

// รายละเอียดโควตาเลื่อนขั้นเงินเดือน โครงสร้างตามมอบหมายงาน
		$cmd = " SELECT SALQ_YEAR, SALQ_TYPE, ORG_ID, SALQD_QTY1, SALQD_QTY2, UPDATE_USER, UPDATE_DATE 
						FROM PER_SALQUOTA_DTL2 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SALQUOTA_DTL2++;
			$SALQ_YEAR = trim($data[SALQ_YEAR]);
			$SALQ_TYPE = $data[SALQ_TYPE];
			$ORG_ID = $data[ORG_ID];
			$SALQD_QTY1 = $data[SALQD_QTY1] + 0;
			$SALQD_QTY2 = $data[SALQD_QTY2] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SALQUOTA_DTL2 (SALQ_YEAR, SALQ_TYPE, ORG_ID, SALQD_QTY1, SALQD_QTY2, UPDATE_USER, UPDATE_DATE)
							VALUES ('$SALQ_YEAR', $SALQ_TYPE, $ORG_ID, $SALQD_QTY1, $SALQD_QTY2, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(*) as COUNT_NEW from PER_SALQUOTA_DTL2 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SALQUOTA_DTL2 - $PER_SALQUOTA_DTL2 - $COUNT_NEW<br>";

// การเลื่อนขั้นเงินเดือน
		$cmd = " SELECT SALQ_YEAR, SALQ_TYPE, PER_ID, SALP_YN, SALP_LEVEL, SALP_SALARY_OLD, SALP_SALARY_NEW, SALP_PERCENT,   
						SALP_SPSALARY, SALP_DATE, SALP_REMARK, SALP_REASON, UPDATE_USER, UPDATE_DATE 
						FROM PER_SALPROMOTE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_SALPROMOTE++;
				$SALQ_YEAR = trim($data[SALQ_YEAR]);
				$SALQ_TYPE = $data[SALQ_TYPE] + 0;
				$SALP_YN = $data[SALP_YN] + 0;
				$SALP_LEVEL = $data[SALP_LEVEL] + 0;
				$SALP_SALARY_OLD = $data[SALP_SALARY_OLD] + 0;
				$SALP_SALARY_NEW = $data[SALP_SALARY_NEW] + 0;
				$SALP_PERCENT = $data[SALP_PERCENT] + 0;
				$SALP_SPSALARY = $data[SALP_SPSALARY] + 0;
				$SALP_DATE = trim($data[SALP_DATE]);
				$SALP_REMARK = trim($data[SALP_REMARK]);
				$SALP_REASON = $data[SALP_REASON] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " INSERT INTO PER_SALPROMOTE (SALQ_YEAR, SALQ_TYPE, PER_ID, SALP_YN, SALP_LEVEL, SALP_SALARY_OLD, 
								SALP_SALARY_NEW, SALP_PERCENT, SALP_SPSALARY, SALP_DATE, SALP_REMARK, SALP_REASON, UPDATE_USER, UPDATE_DATE)
								VALUES ('$SALQ_YEAR', $SALQ_TYPE, $PER_ID, $SALP_YN, $SALP_LEVEL, $SALP_SALARY_OLD, $SALP_SALARY_NEW, 
								$SALP_PERCENT, $SALP_SPSALARY, '$SALP_DATE', '$SALP_REMARK', $SALP_REASON, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if						
		} // end while						

		$cmd = " select count(*) as COUNT_NEW from PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SALPROMOTE - $PER_SALPROMOTE - $COUNT_NEW<br>";

// โควตาเงินรางวัลประจำปี
		$cmd = " SELECT BONUS_YEAR, BONUS_TYPE, BONUS_QUOTA,	UPDATE_USER, UPDATE_DATE 
						FROM PER_BONUSQUOTA ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_BONUSQUOTA++;
			$BONUS_YEAR = trim($data[BONUS_YEAR]);
			$BONUS_TYPE = $data[BONUS_TYPE] + 0;
			$BONUS_QUOTA = $data[BONUS_QUOTA] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE, BONUS_QUOTA, UPDATE_USER, UPDATE_DATE)
							VALUES ('$BONUS_YEAR', $BONUS_TYPE, $BONUS_QUOTA, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(*) as COUNT_NEW from PER_BONUSQUOTA ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_BONUSQUOTA - $PER_BONUSQUOTA - $COUNT_NEW<br>";

// รายละเอียดโควตาเงินรางวัลประจำปี โครงสร้างตามกฎหมาย
		$cmd = " SELECT BONUS_YEAR, BONUS_TYPE, ORG_ID, BONUSQ_QTY, UPDATE_USER, UPDATE_DATE 
						FROM PER_BONUSQUOTADTL1 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_BONUSQUOTADTL1++;
			$BONUS_YEAR = trim($data[BONUS_YEAR]);
			$BONUS_TYPE = $data[BONUS_TYPE];
			$ORG_ID = $data[ORG_ID];
			$BONUSQ_QTY = $data[BONUSQ_QTY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_BONUSQUOTADTL1 (BONUS_YEAR, BONUS_TYPE, ORG_ID, BONUSQ_QTY, UPDATE_USER, UPDATE_DATE)
							VALUES ('$BONUS_YEAR', $BONUS_TYPE, $ORG_ID, $BONUSQ_QTY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(*) as COUNT_NEW from PER_BONUSQUOTADTL1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_BONUSQUOTADTL1 - $PER_BONUSQUOTADTL1 - $COUNT_NEW<br>";

// การมาปฏิบัติราชการ
		$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY,         
						ABS_LETTER, UPDATE_USER, UPDATE_DATE 
						FROM PER_ABSENT ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$PER_ABSENT++;
				$ABS_ID = $data[ABS_ID] + 0;
				$AB_CODE = trim($data[AB_CODE]);
				$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
				$ABS_STARTPERIOD = $data[ABS_STARTPERIOD] + 0;
				$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
				$ABS_ENDPERIOD = $data[ABS_ENDPERIOD] + 0;
				$ABS_DAY = $data[ABS_DAY] + 0;
				$ABS_LETTER = $data[ABS_LETTER] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " INSERT INTO PER_ABSENT (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
								ABS_ENDPERIOD, ABS_DAY, ABS_LETTER, UPDATE_USER, UPDATE_DATE)
								VALUES ($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', 
								$ABS_ENDPERIOD, $ABS_DAY, $ABS_LETTER, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if						
		} // end while						

		$cmd = " select count(ABS_ID) as COUNT_NEW from PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_ABSENT - $PER_ABSENT - $COUNT_NEW<br>";

// ทุนการศึกษา
		$cmd = " SELECT SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SCHOLARSHIP ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SCHOLARSHIP++;
			$SCH_CODE = trim($data[SCH_CODE]);
			$SCH_NAME = trim($data[SCH_NAME]);
			$ST_CODE = trim($data[ST_CODE]);
			$SCH_OWNER = trim($data[SCH_OWNER]);
			$SCH_ACTIVE = $data[SCH_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			if (!$SCH_NAME) $SCH_NAME = $SCH_CODE;

			$cmd = " SELECT ST_NAME FROM PER_SCHOLARTYPE WHERE ST_CODE = '$ST_CODE' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) $ST_CODE = "1";

			$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$SCH_CODE', '$SCH_NAME', '$ST_CODE', '$SCH_OWNER', $SCH_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SCH_CODE) as COUNT_NEW from PER_SCHOLARSHIP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SCHOLARSHIP - $PER_SCHOLARSHIP - $COUNT_NEW<br>";

// ผู้ได้รับทุน
		$cmd = " SELECT SC_ID, SC_TYPE, PER_ID, SC_CARDNO, PN_CODE, SC_NAME, SC_SURNAME, SC_BIRTHDATE, SC_GENDER, SC_ADD1,        
						EN_CODE, EM_CODE, SCH_CODE, INS_CODE, EL_CODE, SC_STARTDATE, SC_ENDDATE, SC_FINISHDATE, SC_BACKDATE, 
						UPDATE_USER, UPDATE_DATE 
						FROM PER_SCHOLAR ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SCHOLAR++;
			$SC_ID = $data[SC_ID] + 0;
			$SC_TYPE = $data[SC_TYPE] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$SC_CARDNO = trim($data[SC_CARDNO]);
			$PN_CODE = trim($data[PN_CODE]);
			$SC_NAME = trim($data[SC_NAME]);
			$SC_SURNAME = trim($data[SC_SURNAME]);
			$SC_BIRTHDATE = trim($data[SC_BIRTHDATE]);
			$SC_GENDER = $data[SC_GENDER] + 0;
			$SC_ADD1 = trim($data[SC_ADD1]);
			$EN_CODE = trim($data[EN_CODE]);
			$EM_CODE = trim($data[EM_CODE]);
			$SCH_CODE = trim($data[SCH_CODE]);
			$INS_CODE = trim($data[INS_CODE]);
			$EL_CODE = trim($data[EL_CODE]);
			$SC_STARTDATE = trim($data[SC_STARTDATE]);
			$SC_ENDDATE = trim($data[SC_ENDDATE]);
			$SC_FINISHDATE = trim($data[SC_FINISHDATE]);
			$SC_BACKDATE = trim($data[SC_BACKDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SCHOLAR (SC_ID, SC_TYPE, PER_ID, SC_CARDNO, PN_CODE, SC_NAME, SC_SURNAME, SC_BIRTHDATE, 
							SC_GENDER, SC_ADD1, EN_CODE, EM_CODE, SCH_CODE, INS_CODE, EL_CODE, SC_STARTDATE, SC_ENDDATE, 
							SC_FINISHDATE, SC_BACKDATE, UPDATE_USER, UPDATE_DATE)
							VALUES ($SC_ID, $SC_TYPE, $PER_ID, '$SC_CARDNO', '$PN_CODE', '$SC_NAME', '$SC_SURNAME', '$SC_BIRTHDATE', 
							$SC_GENDER, '$SC_ADD1', '$EN_CODE', '$EM_CODE', '$SCH_CODE', '$INS_CODE', '$EL_CODE', '$SC_STARTDATE', 
							'$SC_ENDDATE', '$SC_FINISHDATE', '$SC_BACKDATE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SC_ID) as COUNT_NEW from PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SCHOLAR - $PER_SCHOLAR - $COUNT_NEW<br>";

// การอบรม
		$cmd = " SELECT CO_ID, TR_CODE, CO_NO, CO_STARTDATE, CO_ENDDATE, CO_PLACE, CT_CODE, CO_ORG, CO_FUND, CT_CODE_FUND,
						CO_TYPE, CO_CONFIRM, UPDATE_USER, UPDATE_DATE 
						FROM PER_COURSE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_COURSE++;
			$CO_ID = $data[CO_ID] + 0;
			$TR_CODE = trim($data[TR_CODE]);
			$CO_NO = trim($data[CO_NO]);
			$CO_STARTDATE = trim($data[CO_STARTDATE]);
			$CO_ENDDATE = trim($data[CO_ENDDATE]);
			$CO_PLACE = trim($data[CO_PLACE]);
			$CT_CODE = trim($data[CT_CODE]);
			$CO_ORG = trim($data[CO_ORG]);
			$CO_FUND = trim($data[CO_FUND]);
			$CT_CODE_FUND = trim($data[CT_CODE_FUND]);
			$CO_TYPE = $data[CO_TYPE] + 0;
			$CO_CONFIRM = $data[CO_CONFIRM] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_COURSE (CO_ID, TR_CODE, CO_NO, CO_STARTDATE, CO_ENDDATE, CO_PLACE, CT_CODE, CO_ORG, 
							CO_FUND, CT_CODE_FUND, CO_TYPE, CO_CONFIRM, UPDATE_USER, UPDATE_DATE)
							VALUES ($CO_ID, '$TR_CODE', '$CO_NO', '$CO_STARTDATE', '$CO_ENDDATE', '$CO_PLACE', '$CT_CODE', '$CO_ORG', 
							'$CO_FUND', '$CT_CODE_FUND', $CO_TYPE, $CO_CONFIRM, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(CO_ID) as COUNT_NEW from PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_COURSE - $PER_COURSE - $COUNT_NEW<br>";

// ผู้เข้าฝึกอบรม
		$cmd = " SELECT CO_ID, PER_ID, COD_RESULT, UPDATE_USER, UPDATE_DATE 
						FROM PER_COURSEDTL ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_COURSEDTL++;
			$CO_ID = $data[CO_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$COD_RESULT = $data[COD_RESULT] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_COURSEDTL (CO_ID, PER_ID, COD_RESULT, UPDATE_USER, UPDATE_DATE)
							VALUES ($CO_ID, $PER_ID, $COD_RESULT, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(CO_ID) as COUNT_NEW from PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_COURSEDTL - $PER_COURSEDTL - $COUNT_NEW<br>";

// เงื่อนไขการรับเครื่องราชฯ
		$cmd = " SELECT DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, DCON_TIME2, DC_CODE_OLD, DCON_TIME3, DCON_MGT, 
						UPDATE_USER, UPDATE_DATE 
						FROM PER_DECORCOND ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_DECORCOND++;
			$DCON_TYPE = $data[DCON_TYPE] + 0;
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$DC_CODE = trim($data[DC_CODE]);
			$DCON_TIME1 = $data[DCON_TIME1] + 0;
			$DCON_TIME2 = $data[DCON_TIME2] + 0;
			$DC_CODE_OLD = trim($data[DC_CODE_OLD]);
			$DCON_TIME3 = $data[DCON_TIME3] + 0;
			$DCON_MGT = $data[DCON_MGT] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, DCON_TIME2, DC_CODE_OLD, 
							DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES ($DCON_TYPE, '$LEVEL_NO', '$DC_CODE', $DCON_TIME1, $DCON_TIME2, '$DC_CODE_OLD', $DCON_TIME3, 
							$DCON_MGT, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(*) as COUNT_NEW from PER_DECORCOND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_DECORCOND - $PER_DECORCOND - $COUNT_NEW<br>";

// การรับเครื่องราชฯ
		$cmd = " SELECT DE_ID, DE_YEAR, DE_DATE, DE_CONF, UPDATE_USER, UPDATE_DATE 
						FROM PER_DECOR ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_DECOR++;
			$DE_ID = $data[DE_ID] + 0;
			$DE_YEAR = trim($data[DE_YEAR]);
			$DE_DATE = trim($data[DE_DATE]);
			$DE_CONF = $data[DE_CONF] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_DECOR (DE_ID, DE_YEAR, DE_DATE, DE_CONF, UPDATE_USER, UPDATE_DATE)
							VALUES ($DE_ID, '$DE_YEAR', '$DE_DATE', $DE_CONF, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(DE_ID) as COUNT_NEW from PER_DECOR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_DECOR - $PER_DECOR - $COUNT_NEW<br>";

// ผู้ได้รับเครื่องราชฯ
		$cmd = " SELECT DE_ID, DC_CODE, PER_ID, UPDATE_USER, UPDATE_DATE 
						FROM PER_DECORDTL ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_DECORDTL++;
			$DE_ID = $data[DE_ID] + 0;
			$DC_CODE = trim($data[DC_CODE]);
			$PER_ID = $data[PER_ID] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_DECORDTL (DE_ID, DC_CODE, PER_ID, UPDATE_USER, UPDATE_DATE)
							VALUES ($DE_ID, '$DC_CODE', $PER_ID, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(DE_ID) as COUNT_NEW from PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_DECORDTL - $PER_DECORDTL - $COUNT_NEW<br>";

// หนังสือรับรอง
		$cmd = " SELECT LET_ID, LET_NO, PER_ID, LET_REASON, LET_DATE, PER_ID_SIGN1, LET_POSITION, LET_ASSIGN, LET_SIGN, 
						LET_LANG, UPDATE_USER, UPDATE_DATE 
						FROM PER_LETTER ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_LETTER++;
			$LET_ID = $data[LET_ID] + 0;
			$LET_NO = trim($data[LET_NO]);
			$PER_ID = $data[PER_ID] + 0;
			$LET_REASON = trim($data[LET_REASON]);
			$LET_DATE = trim($data[LET_DATE]);
			$PER_ID_SIGN1 = $data[PER_ID_SIGN1] + 0;
			$LET_POSITION = trim($data[LET_POSITION]);
			$LET_ASSIGN = $data[LET_ASSIGN] + 0;
			$LET_SIGN = trim($data[LET_SIGN]);
			$LET_LANG = $data[LET_LANG] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_LETTER (LET_ID, LET_NO, PER_ID, LET_REASON, LET_DATE, PER_ID_SIGN1, LET_POSITION, 
							LET_ASSIGN, LET_SIGN, LET_LANG, UPDATE_USER, UPDATE_DATE)
							VALUES ($LET_ID, '$LET_NO', $PER_ID, '$LET_REASON', '$LET_DATE', $PER_ID_SIGN1, '$LET_POSITION', $LET_ASSIGN, 
							'$LET_SIGN', $LET_LANG, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(LET_ID) as COUNT_NEW from PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_LETTER - $PER_LETTER - $COUNT_NEW<br>";

// ข้อมูลสรุป
		$cmd = " SELECT SUM_ID, ORG_ID, SUM_YEAR, SUM_DATE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM++;
			$SUM_ID = $data[SUM_ID] + 0;
			$ORG_ID = $data[ORG_ID];
			$SUM_YEAR = trim($data[SUM_YEAR]);
			$SUM_DATE = trim($data[SUM_DATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM (SUM_ID, ORG_ID, SUM_YEAR, SUM_DATE, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, $ORG_ID, '$SUM_YEAR', '$SUM_DATE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM - $PER_SUM - $COUNT_NEW<br>";

// สรุปจำนวนส่วนราชการ
		$cmd = " SELECT SUM_ID, OS_CODE, OT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM_DTL1 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM_DTL1++;
			$SUM_ID = $data[SUM_ID] + 0;
			$OS_CODE = trim($data[OS_CODE]);
			$OT_CODE = trim($data[OT_CODE]);
			$SUM_QTY = $data[SUM_QTY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM_DTL1 (SUM_ID, OS_CODE, OT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, '$OS_CODE', '$OT_CODE', $SUM_QTY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM_DTL1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM_DTL1 - $PER_SUM_DTL1 - $COUNT_NEW<br>";

// สรุปจำนวนตำแหน่ง
		$cmd = " SELECT SUM_ID, SUM_TYPE, PL_CODE, CL_NAME, PT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM_DTL2 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM_DTL2++;
			$SUM_ID = $data[SUM_ID] + 0;
			$SUM_TYPE = $data[SUM_TYPE] + 0;
			$PL_CODE = trim($data[PL_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$PT_CODE = trim($data[PT_CODE]);
			$SUM_QTY = $data[SUM_QTY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM_DTL2 (SUM_ID, SUM_TYPE, PL_CODE, CL_NAME, PT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, $SUM_TYPE, '$PL_CODE', '$CL_NAME', '$PT_CODE', $SUM_QTY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM_DTL2 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM_DTL2 - $PER_SUM_DTL2 - $COUNT_NEW<br>";

// สรุปจำนวนตำแหน่งว่างมี/ไม่มีเงิน
		$cmd = " SELECT SUM_ID, PL_CODE, SUM_WITH_MONEY, SUM_NO_MONEY, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM_DTL3 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM_DTL3++;
			$SUM_ID = $data[SUM_ID] + 0;
			$PL_CODE = trim($data[PL_CODE]);
			$SUM_WITH_MONEY = $data[SUM_WITH_MONEY] + 0;
			$SUM_NO_MONEY = $data[SUM_NO_MONEY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM_DTL3 (SUM_ID, PL_CODE, SUM_WITH_MONEY, SUM_NO_MONEY, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, '$PL_CODE', $SUM_WITH_MONEY, $SUM_NO_MONEY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM_DTL3 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM_DTL3 - $PER_SUM_DTL3 - $COUNT_NEW<br>";

// สรุปหัวหน้าส่วนราชการ
		$cmd = " SELECT SUM_ID, PS_CODE, PM_CODE, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM_DTL4 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM_DTL4++;
			$SUM_ID = $data[SUM_ID] + 0;
			$PS_CODE = trim($data[PS_CODE]);
			$PM_CODE = trim($data[PM_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$SUM_QTY_M = $data[SUM_QTY_M] + 0;
			$SUM_QTY_F = $data[SUM_QTY_F] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM_DTL4 (SUM_ID, PS_CODE, PM_CODE, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, '$PS_CODE', '$PM_CODE', '$LEVEL_NO', $SUM_QTY_M, $SUM_QTY_F, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM_DTL4 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM_DTL4 - $PER_SUM_DTL4 - $COUNT_NEW<br>";

// สรุปสำนักงานประจำจังหวัด
		$cmd = " SELECT SUM_ID, OP_CODE, PM_CODE, LEVEL_NO, SUM_QTY, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM_DTL5 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM_DTL5++;
			$SUM_ID = $data[SUM_ID] + 0;
			$OP_CODE = trim($data[OP_CODE]);
			$PM_CODE = trim($data[PM_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$SUM_QTY = $data[SUM_QTY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM_DTL5 (SUM_ID, OP_CODE, PM_CODE, LEVEL_NO, SUM_QTY, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, '$OP_CODE', '$PM_CODE', '$LEVEL_NO', $SUM_QTY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM_DTL5 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM_DTL5 - $PER_SUM_DTL5 - $COUNT_NEW<br>";

// สรุปข้าราชการแต่ละสังกัด
		$cmd = " SELECT SUM_ID, OT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM_DTL6 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM_DTL6++;
			$SUM_ID = $data[SUM_ID] + 0;
			$OT_CODE = trim($data[OT_CODE]);
			$SUM_QTY = $data[SUM_QTY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM_DTL6 (SUM_ID, OT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, '$OT_CODE', $SUM_QTY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM_DTL6 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM_DTL6 - $PER_SUM_DTL6 - $COUNT_NEW<br>";

// สรุปจำนวนข้าราชการ
		$cmd = " SELECT SUM_ID, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM_DTL7 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM_DTL7++;
			$SUM_ID = $data[SUM_ID] + 0;
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$SUM_QTY_M = $data[SUM_QTY_M] + 0;
			$SUM_QTY_F = $data[SUM_QTY_F] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM_DTL7 (SUM_ID, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, '$LEVEL_NO', $SUM_QTY_M, $SUM_QTY_F, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM_DTL7 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM_DTL7 - $PER_SUM_DTL7 - $COUNT_NEW<br>";

// สรุปข้าราชการตามตำแหน่งในสายงาน
		$cmd = " SELECT SUM_ID, PL_CODE, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM_DTL8 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM_DTL8++;
			$SUM_ID = $data[SUM_ID] + 0;
			$PL_CODE = trim($data[PL_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$SUM_QTY_M = $data[SUM_QTY_M] + 0;
			$SUM_QTY_F = $data[SUM_QTY_F] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM_DTL8 (SUM_ID, PL_CODE, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, '$PL_CODE', '$LEVEL_NO', $SUM_QTY_M, $SUM_QTY_F, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM_DTL8 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM_DTL8 - $PER_SUM_DTL8 - $COUNT_NEW<br>";

// สรุปวุฒิข้าราชการ
		$cmd = " SELECT SUM_ID, PL_CODE, EL_CODE, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE 
						FROM PER_SUM_DTL9 ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SUM_DTL9++;
			$SUM_ID = $data[SUM_ID] + 0;
			$PL_CODE = trim($data[PL_CODE]);
			$EL_CODE = trim($data[EL_CODE]);
			$SUM_QTY_M = $data[SUM_QTY_M] + 0;
			$SUM_QTY_F = $data[SUM_QTY_F] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_SUM_DTL9 (SUM_ID, PL_CODE, EL_CODE, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE)
							VALUES ($SUM_ID, '$PL_CODE', '$EL_CODE', $SUM_QTY_M, $SUM_QTY_F, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " select count(SUM_ID) as COUNT_NEW from PER_SUM_DTL9 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_SUM_DTL9 - $PER_SUM_DTL9 - $COUNT_NEW<br>";

	} // end if

?>