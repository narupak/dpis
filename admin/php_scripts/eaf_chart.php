<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select EAF_NAME, EAF_ROLE, DEPARTMENT_ID, EAF_DATE, EAF_YEAR, EAF_MONTH from EAF_MASTER where EAF_ID=$EAF_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$EAF_NAME = $data[EAF_NAME];
	$EAF_ROLE = $data[EAF_ROLE];
	$DEPT_ID = $data[DEPARTMENT_ID];
	$EAF_DATE = $data[EAF_DATE];
	$EAF_YEAR = $data[EAF_YEAR];
	$EAF_MONTH = $data[EAF_MONTH];
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPT_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$DEPT_NAME = $data[ORG_NAME];

	$ARR_ELS_LEVEL = array("1", "2", "3");
	foreach($ARR_ELS_LEVEL as $ELS_LEVEL){

		unset($ELS_ORG_ID);
		$cmd = " select 	a.ELS_ID, a.ORG_ID, a.ORG_ID_1, a.ELS_PERIOD, b.ELK_ID, b.ELK_NAME
						from 	EAF_LEARNING_STRUCTURE a, EAF_LEARNING_KNOWLEDGE b
						where	a.EAF_ID=$EAF_ID and a.ELS_LEVEL=$ELS_LEVEL 
									and a.EAF_ID=b.EAF_ID and a.ELS_ID=b.ELS_ID
						order by a.ELS_ID, b.ELK_ID ";
		$count_learning = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			$ORG_ID_1 = $data[ORG_ID_1];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data2[ORG_NAME];

			if($ELS_ORG_ID != $data[ORG_ID]){
				$ELS_PERIOD = $data[ELS_PERIOD];
				$ELS_DETAIL[$ELS_LEVEL]["PERIOD"] += $ELS_PERIOD;
				
				$ELS_ORG_ID = $data[ORG_ID];
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ELS_ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ELS_DETAIL[$ELS_LEVEL]["ORG"][$ELS_ORG_ID] = $data2[ORG_NAME];

				if($ELS_PERIOD < 12){
					$SHOW_ELS_PERIOD = "$ELS_PERIOD เดือน";
				}else{
					$SHOW_ELS_PERIOD = floor($ELS_PERIOD / 12)." ปี";
					$REMAIN_ELS_PERIOD = $ELS_PERIOD % 12;
					if($REMAIN_ELS_PERIOD > 0) $SHOW_ELS_PERIOD .= " $REMAIN_ELS_PERIOD เดือน";
				}
				$ELS_DETAIL[$ELS_LEVEL]["ORG_PERIOD"][$ELS_ORG_ID] = $SHOW_ELS_PERIOD;
			} // end if

			if ($ORG_NAME_1 != $ELS_ORG_NAME_1) {
				$ELK_DETAIL[$ELS_LEVEL][$ELS_ORG_ID][] = $ORG_NAME_1;
				$ELS_ORG_NAME_1 = $ORG_NAME_1;
			}

			$ELK_DETAIL[$ELS_LEVEL][$ELS_ORG_ID][] = $data[ELK_NAME];
		} // loop while
		
		if($count_learning){
//			echo "<pre>"; print_r($ELS_DETAIL[$ELS_LEVEL]["ORG"]); echo "</pre>";
			$ELS_DETAIL[$ELS_LEVEL]["ORG"] = array_reverse($ELS_DETAIL[$ELS_LEVEL]["ORG"], true);
//			echo "<pre>"; print_r($ELS_DETAIL[$ELS_LEVEL]["ORG"]); echo "</pre>";
		} // end if
		
		if( $ELS_DETAIL[$ELS_LEVEL]["PERIOD"] > 0 ){
			$ELS_PERIOD = $ELS_DETAIL[$ELS_LEVEL]["PERIOD"];
			if($ELS_PERIOD < 12){
				$SHOW_ELS_PERIOD = "$ELS_PERIOD เดือน";
			}else{
				$SHOW_ELS_PERIOD = floor($ELS_PERIOD / 12)." ปี";
				$REMAIN_ELS_PERIOD = $ELS_PERIOD % 12;
				if($REMAIN_ELS_PERIOD > 0) $SHOW_ELS_PERIOD .= " $REMAIN_ELS_PERIOD เดือน";
			}
			
			$ELS_DETAIL[$ELS_LEVEL]["SHOW_PERIOD"] = $SHOW_ELS_PERIOD;
		} // end if
	} // loop foreach

//	echo "<pre>"; print_r($ELS_DETAIL); print_r($ELK_DETAIL); echo "</pre>";
?>