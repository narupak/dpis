<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
//	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	if( !$current_page ) $current_page = 1;
	$data_per_page = 12;
	$start_record = ($current_page - 1) * $data_per_page;
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_org_id && $search_org_id != 'NULL') $where_org = " and org_id = $search_org_id ";
	else $where_org = " and org_id is NULL ";

	$cmd = " select AL_CODE from PER_ASSESS_LEVEL 
					where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AM_CODE <> '1' $where_org order by AL_CODE DESC ";
	$count_data = $db_dpis->send_cmd($cmd);
	if (!$count_data) $where_org = " and org_id is NULL ";

	if($command = 'UPDATE_SCORE') {
		$cmd = "select O_SALARY,K_SALARY,D_SALARY,M_SALARY,SUM_SALARY from PER_COMPENSATION_TEST where CP_ID = $CP_ID";
		$db_dpis->send_cmd($cmd);
		$test_result = $db_dpis->get_array();
		$test_result[O_SALARY] = str_replace(",","",$test_result[O_SALARY]);
		$test_result[K_SALARY] = str_replace(",","",$test_result[K_SALARY]);
		$test_result[D_SALARY] = str_replace(",","",$test_result[D_SALARY]);
		$test_result[M_SALARY] = str_replace(",","",$test_result[M_SALARY]);
		$test_result[SUM_SALARY] = str_replace(",","",$test_result[SUM_SALARY]);
		//print_r($test_result);
		
		$t_sum_salary = explode(':',$test_result[SUM_SALARY]);
		
		foreach($t_sum_salary as $value) {
			list($xx_type,$xx_value) = explode('=',$value);
			$sum_salary_arr[$xx_type] = $xx_value;
		}
		
		foreach($CD_SALARY as $key_per_id => $new_salary) {
			if($CD_SALARY[$key_per_id] != $OLD_CD_SALARY[$key_per_id]) {
				$cmd = "update PER_COMPENSATION_TEST_DTL set CD_SALARY = '$new_salary' where PER_ID = '$key_per_id' and CP_ID = '$CP_ID'";
				$db_dpis->send_cmd($cmd);
				$DIFF_SALARY += ($CD_SALARY[$key_per_id] - $OLD_CD_SALARY[$key_per_id]);
				
				$field_salary = $OLD_LEVEL_NO[$key_per_id][0] . "_SALARY";
				$test_result[$field_salary] += ($CD_SALARY[$key_per_id] - $OLD_CD_SALARY[$key_per_id]);
				
				$xx_al_code = $OLD_AL_CODE[$key_per_id];
				$sum_salary_arr[$xx_al_code] += ($CD_SALARY[$key_per_id] - $OLD_CD_SALARY[$key_per_id]);
				
				$cmd = "update PER_COMPENSATION_TEST set  $field_salary = '$test_result[$field_salary]' where CP_ID = '$CP_ID'";
				$db_dpis->send_cmd($cmd);
			}
		}
		foreach($sum_salary_arr as $xx_key => $xx_value) {
			$update_result .=  $xx_key . "=" . $xx_value . ":";
		}
		$update_result = substr($update_result,0,-1);
		
		foreach($CD_EXTRA_SALARY as $key_per_id => $new_salary) {
			if($CD_EXTRA_SALARY[$key_per_id] != $OLD_CD_EXTRA_SALARY[$key_per_id]) {
				$cmd = "update PER_COMPENSATION_TEST_DTL set CD_EXTRA_SALARY = '$new_salary' where PER_ID = '$key_per_id' and CP_ID = '$CP_ID'";
				$db_dpis->send_cmd($cmd);
				$DIFF_SALARY += ($CD_EXTRA_SALARY[$key_per_id] - $OLD_CD_EXTRA_SALARY[$key_per_id])+0;
				
				$field_salary = $OLD_LEVEL_NO[$key_per_id][0] . "_SALARY";
				$test_result[$field_salary] += ($CD_EXTRA_SALARY[$key_per_id] - $OLD_CD_EXTRA_SALARY[$key_per_id]);
				
				$xx_al_code = $OLD_AL_CODE[$key_per_id];
				$sum_salary_arr[$xx_al_code] += ($CD_EXTRA_SALARY[$key_per_id] - $OLD_CD_EXTRA_SALARY[$key_per_id]);
				
				$cmd = "update PER_COMPENSATION_TEST set  $field_salary = '$test_result[$field_salary]' where CP_ID = '$CP_ID'";
				$db_dpis->send_cmd($cmd);
			}
		}
		foreach($sum_salary_arr as $xx_key => $xx_value) {
			if($xx_value){
				$update_result .=  $xx_key . "=" . $xx_value . ":";
			}
		}
		$update_result = substr($update_result,0,-1);
		$cmd = "select CP_BUDGET
						from PER_COMPENSATION_TEST   
						where CP_ID = $CP_ID";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_BUDGET = $data[CP_BUDGET];
		$DIFF_SALARY = $CP_BUDGET + $DIFF_SALARY;
		
		$cmd = "update PER_COMPENSATION_TEST set CP_BUDGET = $DIFF_SALARY, SUM_SALARY = '$update_result' 
						where CP_ID = $CP_ID";
		$db_dpis->send_cmd($cmd);
	}

?>