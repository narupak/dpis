<?php
add_field("PER_KPI_FORM", "SCORE_OTHER","NUMBER", "6,2", "NULL");
			add_field("PER_KPI_FORM", "SUM_OTHER","NUMBER", "6,2", "NULL");

			$cmd = " update PER_KPI_FORM set SUM_OTHER = SCORE_KPI ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SCORE_KPI = NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ALTER SCORE_KPI NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY SCORE_KPI NUMBER(6,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY SCORE_KPI DECIMAL(6,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SCORE_KPI = SUM_OTHER ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SUM_OTHER = NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SUM_OTHER = SCORE_COMPETENCE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SCORE_COMPETENCE = NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ALTER SCORE_COMPETENCE NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY SCORE_COMPETENCE NUMBER(6,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY SCORE_COMPETENCE DECIMAL(6,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SCORE_COMPETENCE = SUM_OTHER ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SUM_OTHER = NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_CONTACT_PERSON","MEMO", "2000", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL1_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL1_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL1_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL2_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL2_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL2_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL3_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL3_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL3_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL4_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL4_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL4_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL5_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL5_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL5_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER PG_RESULT MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY PG_RESULT VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER PG_RESULT TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMMAND", "ORG_ID","INTEGER", "10", "NULL");
			add_field("PER_COURSE", "CO_DAY","NUMBER", "6,2", "NULL");
			add_field("PER_COURSE", "CO_REMARK","VARCHAR", "255", "NULL");
			add_field("PER_COURSEDTL", "COD_PASS","SINGLE", "1", "NULL");
			add_field("PER_TRAINING", "TRN_DAY","NUMBER", "6,2", "NULL");
			add_field("PER_TRAINING", "TRN_REMARK","VARCHAR", "255", "NULL");
			add_field("PER_TRAINING", "TRN_PASS","SINGLE", "1", "NULL");
			add_field("PER_KPI_FORM", "PERFORMANCE_WEIGHT","INTEGER2", "2", "NULL");
			add_field("PER_KPI_FORM", "COMPETENCE_WEIGHT","INTEGER2", "2", "NULL");
			add_field("PER_KPI_FORM", "OTHER_WEIGHT","INTEGER2", "2", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG ADD POS_LAT DOUBLE ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG ADD POS_LAT FLOAT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG ADD POS_LONG DOUBLE ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG ADD POS_LONG FLOAT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD POS_LAT DOUBLE ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD POS_LAT FLOAT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD POS_LONG DOUBLE ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD POS_LONG FLOAT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAIN ALTER TR_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAIN MODIFY TR_CODE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAIN MODIFY TR_CODE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TR_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TR_CODE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TR_CODE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COURSE ALTER TR_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE MODIFY TR_CODE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COURSE MODIFY TR_CODE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SCHOLARSHIP", "SCH_START_DATE2","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_END_DATE2","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_PLACE2","VARCHAR", "200", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_DEAD_LINE","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLAR", "SC_TEST_DATE","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLAR", "SC_TEST_RESULT","NUMBER", "6,2", "NULL");
			add_field("PER_SCHOLAR", "SC_REMARK","VARCHAR", "200", "NULL");
			add_field("PER_COMPETENCE", "CP_ASSESSMENT","CHAR", "1", "NULL");

			$cmd = " ALTER TABLE SYSTEM_CONFIG MODIFY CONFIG_VALUE TEXT ";
			$db->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMPENSATION_TEST", "ORG_ID_1","INTEGER", "10", "NULL");
			add_field("PER_PERSONAL", "PER_REMARK","MEMO", "2000", "NULL");
			add_field("PER_PERSONAL", "PER_START_ORG","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_COOPERATIVE","SINGLE", "1", "NULL");
			add_field("PER_PERSONAL", "PER_COOPERATIVE_NO","VARCHAR", "25", "NULL");
			add_field("PER_PERSONAL", "PER_MEMBERDATE","VARCHAR", "19", "NULL");
			add_field("PER_POSITIONHIS", "POH_ORG","MEMO", "2000", "NULL");
			add_field("PER_POSITIONHIS", "POH_PM_NAME","VARCHAR", "255", "NULL");
			add_field("PER_POSITIONHIS", "POH_PL_NAME","VARCHAR", "255", "NULL");
			add_field("PER_POSITIONHIS", "POH_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_SALARYHIS", "SAH_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_SALARYHIS", "SAH_REMARK","MEMO", "2000", "NULL");
			add_field("PER_SALARYHIS", "LEVEL_NO","VARCHAR", "10", "NULL");
			add_field("PER_SALARYHIS", "SAH_POS_NO","VARCHAR", "15", "NULL");
			add_field("PER_SALARYHIS", "SAH_POSITION","VARCHAR", "255", "NULL");
			add_field("PER_SALARYHIS", "SAH_ORG","VARCHAR", "255", "NULL");
			add_field("PER_SALARYHIS", "EX_CODE","VARCHAR", "10", "NULL");
			add_field("PER_SALARYHIS", "SAH_PAY_NO","VARCHAR", "15", "NULL");
			add_field("PER_ASSESS_MAIN", "AM_SHOW","SINGLE", "1", "NULL");
			add_field("PER_PRENAME", "PN_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_RELIGION", "RE_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_COUNTRY", "CT_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_PROVINCE", "PV_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_AMPHUR", "AP_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_LINE", "PL_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_LINE_GROUP", "LG_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_ABSENTTYPE", "AB_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_STATUS", "PS_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_MGT", "PM_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_TYPE", "PT_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_EDUCLEVEL", "EL_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_EDUCNAME", "EN_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_EDUCMAJOR", "EM_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_TRAIN", "TR_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_MOVMENT", "MOV_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_CRIME", "CR_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_CRIME_DTL", "CRD_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_SCHOLARTYPE", "ST_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_HOLIDAY", "HOL_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_ABILITYGRP", "AL_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_CONDITION", "PC_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_SERVICE", "SV_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_LAYER", "LAYER_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_INSTITUTE", "INS_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_SKILL_GROUP", "SG_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_SKILL", "SKILL_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_SPECIAL_SKILLGRP", "SS_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_DECORATION", "DC_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_ORG_TYPE", "OT_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_ORG_LEVEL", "OL_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_ORG_STAT", "OS_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_MGTSALARY", "MS_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_OFF_TYPE", "OT_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_MARRIED", "MR_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_BLOOD", "BL_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_LAYEREMP", "LAYERE_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_LAYEREMP_NEW", "LAYERE_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_EXTRATYPE", "EX_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_EXTRA_INCOME_TYPE", "EXIN_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_CO_LEVEL", "CL_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_PENALTY", "PEN_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_HEIRTYPE", "HR_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_SERVICETITLE", "SRT_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_REWARD", "REW_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_DIVORCE", "DV_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_TIME", "TIME_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_COMTYPE", "COM_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_POS_GROUP", "PG_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_POS_NAME", "PN_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_ORG_PROVINCE", "OP_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_LAYER_NEW", "LAYER_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_EMPSER_POS_NAME", "EP_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_OCCUPATION", "OC_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_POSITION", "POS_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_POS_EMP", "POEM_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_POS_EMPSER", "POEMS_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_PERSONAL", "PN_SEQ_NO","PER_SEQ_NO", "5", "NULL");
			add_field("PER_WORK_LOCATION", "WL_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_WORK_CYCLE", "WC_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_PERFORMANCE", "PF_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_JOB_FAMILY", "JF_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_GOODNESS", "GN_SEQ_NO","INTEGER2", "5", "NULL");

			$cmd = " update PER_CONTROL set CTRL_ALTER = 7 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>