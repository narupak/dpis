<?	
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$from = "PER_SCHOLAR a, PER_SCHOLARSHIP b ";		
  	if( trim($search_typecode) ) {
		$arr_search_condition[] = "(b.ST_CODE = '$search_typecode')";
	}
  	if(trim($search_date)) {
		$search_date_tmp =  save_date($search_date);
		$arr_search_condition[] = "(a.SC_STARTDATE <= '$search_date_tmp' and a.SC_ENDDATE >= '$search_date_tmp')";
	}
  	if( trim($search_status) )  {
		$from = "PER_SCHOLAR a, PER_SCHOLARSHIP b, PER_SCHOLARINC c ";
		$arr_search_condition[] = "(a.SC_ID=c.SC_ID)";
		$arr_search_condition[] = "(c.SCI_BEGINDATE <= '$search_date_tmp' and c.SC_ENDDATE >= '$search_date_tmp')";
	}

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = implode(" and ", $arr_search_condition);
	$search_condition = (trim($search_condition))?  $search_condition." and "  : "";	

	$cmd = " select 	count(SC_ID) as count_data 
					 from 		$from  
					 where	$search_condition  a.SCH_CODE=b.SCH_CODE ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$count_data = $data[count_data];	
?>