<?
include("../php_scripts/connect_database.php");
include("php_scripts/session_start.php");
include("../php_scripts/connect_file.php");

$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

if ($command=="DELETE") { 

	$table = array("PER_ORG_JOB", "PER_POSITIONHIS", "PER_SALARYHIS", "PER_EXTRAHIS", "PER_EXTRA_INCOMEHIS", "PER_EDUCATE", 
							"PER_TRAINING", "PER_ABILITY", "PER_HEIR", "PER_ABSENTHIS", "PER_PUNISHMENT", "PER_SERVICEHIS", "PER_REWARDHIS", 
							"PER_MARRHIS", "PER_NAMEHIS", "PER_DECORATEHIS", "PER_TIMEHIS", "PER_SPECIAL_SKILL", "PER_PERSONALPIC", "PER_COMDTL", 
							"PER_MOVE_REQ", "PER_PROMOTE_C", "PER_PROMOTE_P", "PER_PROMOTE_E", "PER_SALPROMOTE", "PER_BONUSPROMOTE", 
							"PER_ABSENT", "PER_INVEST1DTL", "PER_INVEST2DTL", "PER_SCHOLARSHIP", "PER_SCHOLAR", "PER_COURSEDTL", 
							"PER_DECORDTL", "PER_LETTER", "PER_KPI", "PER_KPI_FORM", "PER_PERFORMANCE_GOALS", "PER_PERFORMANCE_GOODNESS", 
							"PER_TRAIN_PROJECT", "PER_TRAIN_PROJECT_PERSONAL", "PER_COMPENSATION_TEST_DTL", "PER_WORK_CYCLEHIS", 
							"PER_TIME_ATTENDANCE", "PER_WORK_TIME", "PER_ABSENTSUM", "PER_CHILD", "PER_FAMILY", "PER_ADDRESS", "PER_ACTINGHIS", 
							"PER_PARENT", "PER_POS_MGTSALARYHIS", "PER_WORKFLOW_POSITIONHIS", "PER_WORKFLOW_SALARYHIS", "PER_WORKFLOW_EXTRAHIS", 
							"PER_WORKFLOW_EXTRA_INCOMEHIS", "PER_WORKFLOW_EDUCATE", "PER_WORKFLOW_TRAINING", "PER_WORKFLOW_ABILITY", 
							"PER_WORKFLOW_HEIR", "PER_WORKFLOW_ABSENTHIS", "PER_WORKFLOW_PUNISHMENT", "PER_WORKFLOW_SERVICEHIS", 
							"PER_WORKFLOW_REWARDHIS", "PER_WORKFLOW_MARRHIS", "PER_WORKFLOW_NAMEHIS", "PER_WORKFLOW_DECORATEHIS", 
							"PER_WORKFLOW_TIMEHIS", "PER_WORKFLOW_SPECIAL_SKILL", "PER_WORKFLOW_ADDRESS", "PER_WORKFLOW_FAMILY", 
							"PER_PERSONAL_NAMECARD", "PER_PERSONAL");
	$db_dpis->send_cmd($cmd);

	for ( $i=0; $i<count($table); $i++ ) { 
		if ($table[$i]=="PER_KPI") {
			$cmd = " delete from $table[$i] where kpi_id in (select kpi_id from PER_KPI where kpi_id_ref in 
							(select kpi_id from PER_KPI where kpi_id_ref in (select kpi_id from PER_KPI where kpi_per_id  = $PER_ID)))";
			$db_dpis->send_cmd($cmd);		

			$cmd = " delete from $table[$i] where kpi_id in (select kpi_id from PER_KPI where kpi_id_ref in (select kpi_id from PER_KPI where kpi_per_id  = $PER_ID))";
			$db_dpis->send_cmd($cmd);		

			$cmd = " delete from $table[$i] where kpi_per_id = $PER_ID ";
			$db_dpis->send_cmd($cmd);		
//			$db_dpis->show_error();
		} else
			$cmd = " delete from $table[$i] where per_id = $PER_ID ";
		$db_dpis->send_cmd($cmd);		
//		$db_dpis->show_error();
	} 	// endif for ($i=0; $i<=count($table); $i++)

} // endif command==CONVERT
?>