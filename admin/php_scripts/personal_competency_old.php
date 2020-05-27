<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!isset($search_kf_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if($KF_CYCLE == 1){
		$arr_temp = explode("/", $KF_START_DATE_1);
		$KF_START_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];

		$arr_temp = explode("/", $KF_END_DATE_1);
		$KF_END_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	}elseif($KF_CYCLE == 2){
		$arr_temp = explode("/", $KF_START_DATE_2);
		$KF_START_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];

		$arr_temp = explode("/", $KF_END_DATE_2);
		$KF_END_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	} // end if
	
	if($command == "UPDATE_SCORE"){
		foreach($TOTAL_SCORE as $update_id => $update_point) {
			$cmd = " update PER_KPI_FORM set 
							KPI_FLAG = '".$_kpi_flag[$update_id]."',
							ORG_ID_KPI = '$update_org_kpi',
							SALARY_FLAG = '".$_salary_flag[$update_id]."',
							ORG_ID_SALARY = '$update_org_salary',
							CHIEF_PER_ID = '$update_chief',
							FRIEND_FLAG = '".$_friend_flag[$update_id]."',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						 where KF_ID=$update_id ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > α‘ιδ’€ΠαΉΉ [".$KF_ID." : ".trim($KF_YEAR)." : ".$KF_CYCLE." : ".$PER_NAME."]");
		}
	}
	
	if($command == 'COPY_ALL' || $command == 'COPY_TEST') {
		if($search_kf_cycle == 1){
			$KF_START_DATE = ($search_kf_year-544) . "-10-01";
			$KF_END_DATE = ($search_kf_year-543) . "-03-31";
		}elseif($search_kf_cycle == 2){
			$KF_START_DATE = ($search_kf_year-543) . "-04-01";
			$KF_END_DATE = ($search_kf_year-543) . "-09-30";
		} // end if
	
		if($search_org_id) $search_con = "b.ORG_ID = $search_org_id ";
		elseif($search_department_id) $search_con = "a.DEPARTMENT_ID = $search_department_id ";

		$cmd = " select max(KF_ID) as MAX_ID from PER_KPI_FORM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_ID = $data[MAX_ID] + 1;

		if($search_per_type==1) 
			$cmd = "	select PER_ID, b.ORG_ID from PER_PERSONAL a, PER_POSITION b where a.POS_ID = b.POS_ID and 
							PER_STATUS = 1 and $search_con ";
		elseif($search_per_type==2) 
			$cmd = "	select PER_ID, b.ORG_ID from PER_PERSONAL a, PER_POS_EMP b where a.POEM_ID = b.POEM_ID and 
							PER_STATUS = 1 and $search_con ";
		elseif($search_per_type==3) 
			$cmd = "	select PER_ID, b.ORG_ID from PER_PERSONAL a, PER_POS_EMPSER b where a.POEMS_ID = b.POEMS_ID and 
							PER_STATUS = 1 and $search_con ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$ORG_ID = $data[ORG_ID] + 0;
			if($command == 'COPY_TEST') $TOTAL_SCORE = rand(60,100);
			else $TOTAL_SCORE = 0;
			$cmd = " SELECT KF_ID FROM PER_KPI_FORM WHERE PER_ID = $PER_ID AND KF_CYCLE = $search_kf_cycle AND 
							KF_START_DATE = '$KF_START_DATE' AND KF_END_DATE = '$KF_END_DATE' ";
			$count_data = $db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			if ($count_data) {
				$data1 = $db_dpis1->get_array();
				$KF_ID = $data1[KF_ID];
				$cmd = " UPDATE PER_KPI_FORM SET TOTAL_SCORE = $TOTAL_SCORE WHERE KF_ID = $KF_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} else {
				$cmd = " insert into PER_KPI_FORM (KF_ID, PER_ID, KF_CYCLE, KF_START_DATE, KF_END_DATE, DEPARTMENT_ID, ORG_ID,		
								TOTAL_SCORE, SALARY_FLAG, UPDATE_USER, UPDATE_DATE)
								values ($KF_ID, $PER_ID, $search_kf_cycle, '$KF_START_DATE', '$KF_END_DATE', $search_department_id, $ORG_ID, 
								$TOTAL_SCORE, 'Y', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$KF_ID++;
			}
		}
	}
?>