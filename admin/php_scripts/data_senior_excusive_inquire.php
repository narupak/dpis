<?	
	$from = "PER_SENIOR_EXCUSIVE a, PER_LEVEL b";		
  	if( trim($search_se_no) ) {
		$arr_search_condition[] = "(trim(a.SE_NO) = '$search_se_no')";
	}
	
	if(trim($ORG_ID))  {
	$arr_search_condition[] = "(f.ORG_ID = $ORG_ID)";
	}
	
	if(trim($INS_CODE_SEARCH))  {
	$arr_search_condition[] = "(a.INS_CODE = '$INS_CODE_SEARCH')";
	}
	
//	if(trim($LEVEL_NO))  {
//	$arr_search_condition[] = "(a.LEVEL_NO = '$LEVEL_NO')";
//	}
	
  	if(trim($search_date)) {
		$search_date_tmp =  save_date($search_date);
		$arr_search_condition[] = "(a.SC_STARTDATE <= '$search_date_tmp' and a.SC_ENDDATE >= '$search_date_tmp')";
	} 
	
 if ($search_se_startdate) {
		$temp_start =  save_date($search_se_startdate);
		$arr_search_condition[] = "(a.SC_STARTDATE >= '$temp_start')";
	} // end if
	
	if ($search_se_enddate) {
		$temp_end =  save_date($search_se_enddate);
		$arr_search_condition[] = "(a.SC_ENDDATE <= '$temp_end')";
	} // end if 
	
  	if( trim($search_status) )  {
		$from = "PER_SENIOR_EXCUSIVE a, PER_SCHOLARSHIP b, PER_SCHOLARINC c";
		$arr_search_condition[] = "(a.SE_ID=c.SE_ID)";
		$arr_search_condition[] = "(c.SCI_BEGINDATE <= '$search_date_tmp' and c.SC_ENDDATE >= '$search_date_tmp')";
	}
	
	

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = implode(" and ", $arr_search_condition);
	$search_condition = (trim($search_condition))?  $search_condition." and "  : "";	
	

  	if($DPISDB=="odbc"){ 
		$cmd = " select 	count(SE_ID) as count_data 
						 from 		$from  
						 where	$search_condition  a.LEVEL_NO=b.LEVEL_NO ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select 	count(SE_ID) as count_data 
   			 			 from 		$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d
						 where	$search_condition  a.LEVEL_NO=b.LEVEL_NO(+) and a.PER_ID = d.PER_ID(+) and d.POS_ID=e.POS_ID(+) and e.ORG_ID=f.ORG_ID(+) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	count(SE_ID) as count_data 
						 from 		$from  
						 where	$search_condition  a.LEVEL_NO=b.LEVEL_NO ";
	} 
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$count_data = $data[count_data];	
?>