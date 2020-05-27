<?	
	// error_reporting(E_ALL);
	// ini_set('display_errors', 1);
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	/*cdgs*/
	include("php_scripts/psst_person.php");
	/*cdgs*/
        
        
//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd , null);
	// ==========================

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

//	echo "command=$command<br>";
	if($command=="REORDER"){
		foreach($ARR_ORDER as $PER_ID => $PER_SEQ_NO){
			if($PER_SEQ_NO=="") { $cmd = " update PER_PERSONAL set PER_SEQ_NO='' where PER_ID=$PER_ID "; }
			else { $cmd = " update PER_PERSONAL set PER_SEQ_NO=$PER_SEQ_NO where PER_ID=$PER_ID ";  }

			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "cmd=$cmd<br>";
		} // end foreach

		$command = "SEARCH";
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �Ѵ�ӴѺ�ؤ�ҡ� [$PER_ID : $FULL_NAME]");
	} // end if

	if($command == "UPDATE_ORG_ASS"){
		foreach($org_id_ass as $per_id_ass => $value) {
			if($value=="NULL") $value = "";
			$cmd = " update PER_PERSONAL set 
							ORG_ID= '".$value."',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						 where PER_ID=$per_id_ass ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo $cmd . "<br>";
			$command = "SEARCH";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ����ӹѡ/�ͧ����ͺ���§ҹ [".$per_id_ass." : ".$value."]");
		}
	}

	if($command == "DELETE" && $PER_ID){
		$cmd = " select PER_NAME, PER_SURNAME, PER_CARDNO from PER_PERSONAL where PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME_DEL = $data[PER_NAME];
		$PER_SURNAME_DEL = $data[PER_SURNAME];
		$PER_CARDNO_DEL = $data[PER_CARDNO];
		
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
								"PER_PERSONAL_NAMECARD", "PER_KPI", "PER_PERSONAL");
		$db_dpis->send_cmd($cmd);

		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="PER_KPI"){
				$cmd = " delete from $table[$i] where kpi_per_id = $PER_ID ";
				$db_dpis->send_cmd($cmd);
			}else{
				$cmd = " delete from $table[$i] where per_id = $PER_ID ";
				$db_dpis->send_cmd($cmd);
			}				
	//		$db_dpis->show_error();
		}
		 	// endif for ($i=0; $i<=count($table); $i++)
		/*cdgs*/
		// $aaa=f_del_psst_person($PER_ID);
		/*cdgs*/
		$cmd = "delete from USER_DETAIL where USERNAME = '$PER_CARDNO_DEL' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$command = "SEARCH";
		$PER_ID = "";		//��������
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������$PERSON_TITLE [ $PER_ID : $PER_NAME_DEL $PER_SURNAME_DEL ]");
	} // end if
?>