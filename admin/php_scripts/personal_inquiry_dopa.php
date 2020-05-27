<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
				
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
	
	if(!isset($search_per_type)) $search_per_type = 1;
	if(!isset($search_per_status) && $command != "SEARCH") $search_per_status = array(1);
	if(!isset($search_per_gender) && $command != "SEARCH") $search_per_gender = array(1, 2);
	if ($BKK_FLAG!=1) if(!isset($search_per_member) && $command != "SEARCH") $search_per_member = array(0, 1);
	if(!isset($search_per_cooperative) && $command != "SEARCH") $search_per_cooperative = array(0, 1);
	if(!isset($search_last_position) && $command != "SEARCH") $search_last_position = array('Y');
	if(!isset($search_last_salary) && $command != "SEARCH") $search_last_salary = array('Y');
	if(!isset($EDU_TYPE) && $command != "SEARCH") $EDU_TYPE = array(1, 2, 3, 4, 5);
	if(!isset($EDU_SHOW) && $command != "SEARCH") $EDU_SHOW = 2;
	if(!isset($POS_ORGMGT) && $command != "SEARCH") $POS_ORGMGT = 1;
	if(!isset($TRN_TYPE) && $command != "SEARCH") $TRN_TYPE = array(1, 2, 3);
	
  	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if
	
	if(trim($search_org_id)){ 
		if($SESS_ORG_STRUCTURE==1){	
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}else{	
			if($search_per_type == 1 || $search_per_type == 5)
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			elseif($search_per_type == 2)
				$arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
			elseif($search_per_type == 3)
				$arr_search_condition[] = "(d.ORG_ID = $search_org_id)";
		}
	} // end if
	if(trim($search_org_id_1)){ 
		if($SESS_ORG_STRUCTURE==1){	
			$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
		}else{	
			if($search_per_type == 1 || $search_per_type == 5)
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			elseif($search_per_type == 2)
				$arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
			elseif($search_per_type == 3)
				$arr_search_condition[] = "(d.ORG_ID_1 = $search_org_id_1)";
		}
	} // end if
	if(trim($search_org_id_2)){ 
		if($SESS_ORG_STRUCTURE==1){	
			$arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
		}else{	
			if($search_per_type == 1 || $search_per_type == 5)
				$arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			elseif($search_per_type == 2)
				$arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
			elseif($search_per_type == 3)
				$arr_search_condition[] = "(d.ORG_ID_2 = $search_org_id_2)";
		}
	} // end if

	/* ================= 	ข้อมูลทั่วไป    ===================== */
	if(trim($search_per_type)) $arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	if(trim($search_pv_code)) $arr_search_condition[] = "(a.PV_CODE='$search_pv_code')";
	if(trim($search_es_code)) $arr_search_condition[] = "(a.ES_CODE='$search_es_code')";
	if(trim($search_per_startyear_min)){
		$search_per_startyear = $search_per_startyear_min - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 4) >= '$search_per_startyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 4) >= '$search_per_startyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 4) >= '$search_per_startyear')";
	} // end if
	if(trim($search_per_startyear_max)){
		$search_per_startyear = $search_per_startyear_max - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 4) <= '$search_per_startyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 4) <= '$search_per_startyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 4) <= '$search_per_startyear')";
	} // end if
	if(trim($search_per_occupyyear_min)){
		$search_per_occupyyear = $search_per_occupyyear_min - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 4) >= '$search_per_occupyyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_OCCUPYDATE, 1, 4) >= '$search_per_occupyyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 4) >= '$search_per_occupyyear')";
	} // end if
	if(trim($search_per_occupyyear_max)){
		$search_per_occupyyear = $search_per_occupyyear_max - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 4) <= '$search_per_occupyyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_OCCUPYDATE, 1, 4) <= '$search_per_occupyyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 4) <= '$search_per_occupyyear')";
	} // end if
	if(trim($search_per_retireyear_min)){
		$search_per_retireyear = $search_per_retireyear_min - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_RETIREDATE, 4) >= '$search_per_retireyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_RETIREDATE, 1, 4) >= '$search_per_retireyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_RETIREDATE, 4) >= '$search_per_retireyear')";
	} // end if
	if(trim($search_per_retireyear_max)){
		$search_per_retireyear = $search_per_retireyear_max - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_RETIREDATE, 4) <= '$search_per_retireyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_RETIREDATE, 1, 4) <= '$search_per_retireyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_RETIREDATE, 4) <= '$search_per_retireyear')";
	} // end if
	if(trim($search_per_posyear_min)){
		$search_per_posyear = $search_per_posyear_min - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_POSDATE, 4) >= '$search_per_posyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_POSDATE, 1, 4) >= '$search_per_posyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_POSDATE, 4) >= '$search_per_posyear')";
	} // end if
	if(trim($search_per_posyear_max)){
		$search_per_posyear = $search_per_posyear_max - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_POSDATE, 4) <= '$search_per_posyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_POSDATE, 1, 4) <= '$search_per_posyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_POSDATE, 4) <= '$search_per_posyear')";
	} // end if

	$arr_search_condition[] = "(a.PER_GENDER in (". implode(",", $search_per_gender) ."))";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	if ($BKK_FLAG!=1) $arr_search_condition[] = "(a.PER_MEMBER in (". implode(",", $search_per_member) ."))";
	$arr_search_condition[] = "(a.PER_COOPERATIVE in (". implode(",", $search_per_cooperative) ."))";
	if ($POS_ORGMGT==2) 
		if (trim($search_pos_orgmgt))
			$arr_search_condition[] = "(a.PER_POS_ORGMGT like '%$search_pos_orgmgt%')";
		else
			$arr_search_condition[] = "(a.PER_POS_ORGMGT is NULL)";

	/* ================= 	ตำแหน่งปัจจุบัน    ===================== */
  	if(trim($search_pos_no))  {	
		if ($search_per_type == 1 || $search_per_type == 5)
			$arr_search_condition[] = "(b.POS_NO like '$search_pos_no%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(c.POEM_NO like '$search_pos_no%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(d.POEMS_NO like '$search_pos_no%')";			
	}
	if(trim($search_pl_code)){
		if($search_per_type == 1)
			$arr_search_condition[] = "(b.PL_CODE = '$search_pl_code')";
		elseif($search_per_type == 2)
			$arr_search_condition[] = "(c.PN_CODE = '$search_pl_code')";
		elseif($search_per_type == 3)
			$arr_search_condition[] = "(d.EP_CODE = '$search_pl_code')";
	} // end if
	if(trim($search_pm_code)) $arr_search_condition[] = "(b.PM_CODE = '$search_pm_code')";
	if(trim($search_pt_code)) $arr_search_condition[] = "(b.PT_CODE = '$search_pt_code')";
	if(trim($search_level_no_min)) $arr_search_condition[] = "(trim(a.LEVEL_NO) >= trim('$search_level_no_min'))";
	if(trim($search_level_no_max)) $arr_search_condition[] = "(trim(a.LEVEL_NO) <= trim('$search_level_no_max'))";
	if(trim($search_per_salary_min)) $arr_search_condition[] = "(a.PER_SALARY >= $search_per_salary_min)";
	if(trim($search_per_salary_max)) $arr_search_condition[] = "(a.PER_SALARY <= $search_per_salary_max)";

//	if(trim($search_pos_ot_code)) $arr_search_condition[] = "(b.OT_CODE='$search_pos_ot_code')";
	/* ================= 	ประวัติการดำรงตำแหน่ง    ===================== */
	if(trim($search_poh_effectivedate)){
		$search_poh_effectivedate =  save_date($search_poh_effectivedate);
		if($DPISDB=="odbc") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) <= '$search_poh_effectivedate')";
		elseif($DPISDB=="oci8") $arr_search_positionhis_condition[] = "(SUBSTR(POH_EFFECTIVEDATE, 1, 10) <= '$search_poh_effectivedate')";
		elseif($DPISDB=="mysql") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) <= '$search_poh_effectivedate')";
		$search_poh_effectivedate = show_date_format($search_poh_effectivedate, 1);
	} // end if
	if(trim($search_poh_es_code)) $arr_search_positionhis_condition[] = "(a.ES_CODE = '$search_poh_es_code')";
	if(trim($search_poh_position_type)=="ทั่วไป") $arr_search_positionhis_condition[] = "(a.LEVEL_NO like 'O%')";
	elseif(trim($search_poh_position_type)=="วิชาการ") $arr_search_positionhis_condition[] = "(a.LEVEL_NO like 'K%')";
	elseif(trim($search_poh_position_type)=="อำนวยการ") $arr_search_positionhis_condition[] = "(a.LEVEL_NO like 'D%')";
	elseif(trim($search_poh_position_type)=="บริหาร") $arr_search_positionhis_condition[] = "(a.LEVEL_NO like 'M%')";
	if(trim($search_poh_pm_code)) $arr_search_positionhis_condition[] = "(b.PM_CODE = '$search_poh_pm_code')";
	if(trim($search_poh_pl_code)){
		if($search_per_type == 1) $arr_search_positionhis_condition[] = "(b.PL_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 2) $arr_search_positionhis_condition[] = "(PN_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 3) $arr_search_positionhis_condition[] = "(EP_CODE = '$search_poh_pl_code')";
	} // end if
	if(trim($search_poh_cl_name)) $arr_search_positionhis_condition[] = "(b.CL_NAME = '$search_poh_cl_name')";
	if(trim($search_poh_level_no)) $arr_search_positionhis_condition[] = "(a.LEVEL_NO = '$search_poh_level_no')";
	if(trim($search_poh_org_id)) $arr_search_positionhis_condition[] = "(b.ORG_ID = $search_poh_org_id)";
	if(trim($search_per_level_no)) $arr_search_positionhis_condition[] = "(a.POH_LEVEL_NO = '$search_per_level_no')";
	if(trim($search_poh_ot_code)) $arr_search_positionhis_condition[] = "(c.OT_CODE = '$search_poh_ot_code')";
	if(trim($search_pos_org)) $arr_search_positionhis_condition[] = "(a.POH_ORG like '%$search_pos_org%')";
	
	if(count($arr_search_positionhis_condition)){
		if($search_per_type == 1) {
			$table = "PER_POSITION";
			$field = "b.POS_NO";
		} elseif($search_per_type == 2) {
			$table = "PER_POS_EMP";
			$field = "b.POEM_NO";
		} elseif($search_per_type == 3) {
			$table = "PER_POS_EMPSER";
			$field = "b.POEMS_NO";
		}
		$search_position = "";
		for ($i=0;$i<count($search_last_position);$i++) {
			if($search_position) { $search_position.= ' or '; }
			$search_position.= "a.POH_LAST_POSITION = '$search_last_position[$i]' "; 
		} 
		if ($search_position) $arr_search_positionhis_condition[] = "(".$search_position.")";
		$cmd = " select distinct PER_ID from PER_POSITIONHIS a, $table b, PER_ORG c where a.POH_POS_NO = $field and b.ORG_ID = c.ORG_ID and ". 
						  implode(" and ", $arr_search_positionhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_positionhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_positionhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_positionhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_positionhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_positionhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_positionhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_positionhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_positionhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_positionhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_positionhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_positionhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_positionhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_positionhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_positionhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_positionhis15[] = $data[PER_ID];
			else $arr_positionhis16[] = $data[PER_ID];
		}
		
		if (count($arr_positionhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis15) .")) or (a.PER_ID in (". implode(",", $arr_positionhis16) .")))";
		elseif (count($arr_positionhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis15) .")))";
		elseif (count($arr_positionhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")))";
		elseif (count($arr_positionhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")))";
		elseif (count($arr_positionhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")))";
		elseif (count($arr_positionhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")))";
		elseif (count($arr_positionhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")))";
		elseif (count($arr_positionhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")))";
		elseif (count($arr_positionhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")))";
		elseif (count($arr_positionhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")))";
		elseif (count($arr_positionhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")))";
		elseif (count($arr_positionhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")))";
		elseif (count($arr_positionhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")))";
		elseif (count($arr_positionhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")))";
		elseif (count($arr_positionhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_positionhis) ."))";
	} // end if
	
	/* ================= 	ประวัติการรับเงินเดือน    ===================== */
	if(trim($search_sah_effectivedate)){
		$search_sah_effectivedate =  save_date($search_sah_effectivedate);
		if($DPISDB=="odbc") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_sah_effectivedate')";
		elseif($DPISDB=="oci8") $arr_search_salaryhis_condition[] = "(SUBSTR(SAH_EFFECTIVEDATE, 1, 10) <= '$search_sah_effectivedate')";
		elseif($DPISDB=="mysql") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_sah_effectivedate')";
		$search_sah_effectivedate = show_date_format($search_sah_effectivedate, 1);
	} // end if
	if(trim($search_sah_position_type)=="ทั่วไป") $arr_search_salaryhis_condition[] = "(b.LEVEL_NO like 'O%')";
	elseif(trim($search_sah_position_type)=="วิชาการ") $arr_search_salaryhis_condition[] = "(b.LEVEL_NO like 'K%')";
	elseif(trim($search_sah_position_type)=="อำนวยการ") $arr_search_salaryhis_condition[] = "(b.LEVEL_NO like 'D%')";
	elseif(trim($search_sah_position_type)=="บริหาร") $arr_search_salaryhis_condition[] = "(b.LEVEL_NO like 'M%')";
	if(trim($search_sah_pm_code)) $arr_search_salaryhis_condition[] = "(b.PM_CODE = '$search_sah_pm_code')";
	if(trim($search_sah_pl_code)){
		if($search_per_type == 1) $arr_search_salaryhis_condition[] = "(b.PL_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 2) $arr_search_salaryhis_condition[] = "(PN_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 3) $arr_search_salaryhis_condition[] = "(EP_CODE = '$search_sah_pl_code')";
	} // end if
	if(trim($search_sah_cl_name)) $arr_search_positionhis_condition[] = "(b.CL_NAME = '$search_sah_cl_name')";
	if(trim($search_sah_org_id)) $arr_search_salaryhis_condition[] = "(b.ORG_ID = $search_sah_org_id)";
	if(trim($search_sah_level_no)) $arr_search_salaryhis_condition[] = "(b.LEVEL_NO = '$search_sah_level_no')";
	if(trim($search_sah_salary_min)) $arr_search_salaryhis_condition[] = "(SAH_SALARY >= $search_sah_salary_min)";
	if(trim($search_sah_salary_max)) $arr_search_salaryhis_condition[] = "(SAH_SALARY <= $search_sah_salary_max)";
	if(trim($search_sah_ot_code)) $arr_search_positionhis_condition[] = "(c.OT_CODE = '$search_sah_ot_code')";
	
	if(count($arr_search_salaryhis_condition)){
		if($search_per_type == 1) {
			$table = "PER_POSITION";
			$field = "b.POS_NO";
		} elseif($search_per_type == 2) {
			$table = "PER_POS_EMP";
			$field = "b.POEM_NO";
		} elseif($search_per_type == 3) {
			$table = "PER_POS_EMPSER";
			$field = "b.POEMS_NO";
		}
		$search_salary = "";
		for ($i=0;$i<count($search_last_salary);$i++) {
			if($search_salary) { $search_salary.= ' or '; }
			$search_salary.= "a.SAH_LAST_SALARY = '$search_last_salary[$i]' "; 
		} 
		if ($search_salary) $arr_search_salaryhis_condition[] = "(".$search_salary.")";
		$cmd = " select distinct PER_ID from PER_SALARYHIS a, $table b, PER_ORG c where a.SAH_PAY_NO = $field and b.ORG_ID = c.ORG_ID and ". 
						  implode(" and ", $arr_search_salaryhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_salaryhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_salaryhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_salaryhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_salaryhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_salaryhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_salaryhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_salaryhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_salaryhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_salaryhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_salaryhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_salaryhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_salaryhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_salaryhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_salaryhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_salaryhis15[] = $data[PER_ID];
			elseif ($count < 16000) $arr_salaryhis16[] = $data[PER_ID];
			elseif ($count < 17000) $arr_salaryhis17[] = $data[PER_ID];
			elseif ($count < 18000) $arr_salaryhis18[] = $data[PER_ID];
			elseif ($count < 19000) $arr_salaryhis19[] = $data[PER_ID];
			else $arr_salaryhis20[] = $data[PER_ID];
		}
		
		if (count($arr_salaryhis20)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis20) .")))";
		elseif (count($arr_salaryhis19)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")))";
		elseif (count($arr_salaryhis18)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")))";
		elseif (count($arr_salaryhis17)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")))";
		elseif (count($arr_salaryhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")))";
		elseif (count($arr_salaryhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")))";
		elseif (count($arr_salaryhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")))";
		elseif (count($arr_salaryhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")))";
		elseif (count($arr_salaryhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")))";
		elseif (count($arr_salaryhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")))";
		elseif (count($arr_salaryhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")))";
		elseif (count($arr_salaryhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")))";
		elseif (count($arr_salaryhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")))";
		elseif (count($arr_salaryhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")))";
		elseif (count($arr_salaryhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")))";
		elseif (count($arr_salaryhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")))";
		elseif (count($arr_salaryhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")))";
		elseif (count($arr_salaryhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")))";
		elseif (count($arr_salaryhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_salaryhis) ."))";
	} // end if
	
	/* ================= 	ประวัติการศึกษา    ===================== */
	$search_edu = "";
	if(trim($search_el_code)) $arr_search_educatehis_condition[] = "(trim(a.EL_CODE) = trim('$search_el_code'))";
	if(trim($search_en_code)) $arr_search_educatehis_condition[] = "(a.EN_CODE = '$search_en_code')";
	if(trim($search_em_code)) $arr_search_educatehis_condition[] = "(a.EM_CODE = '$search_em_code')";
	if(trim($search_ins_code)) $arr_search_educatehis_condition[] = "(a.INS_CODE = '$search_ins_code')";
	if(trim($search_ins_ct_code)) $arr_search_educatehis_condition[] = "(b.CT_CODE = '$search_ins_ct_code')";

	if(count($arr_search_educatehis_condition)){
		for ($i=0;$i<count($EDU_TYPE);$i++) {
			if($search_edu) { $search_edu.= ' or '; }
			$search_edu.= "a.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
		} 
		if ($search_edu) $arr_search_educatehis_condition[] = "(".$search_edu.")";
		$cmd = " select 	distinct a.PER_ID 
						 from 		PER_EDUCATE a, PER_INSTITUTE b
						 where 	a.INS_CODE=b.INS_CODE(+)
						 				and ". implode(" and ", $arr_search_educatehis_condition) ." 
						 order by a.PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_educatehis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_educatehis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_educatehis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_educatehis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_educatehis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_educatehis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_educatehis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_educatehis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_educatehis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_educatehis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_educatehis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_educatehis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_educatehis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_educatehis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_educatehis15[] = $data[PER_ID];
			else $arr_educatehis16[] = $data[PER_ID];
		}
		
		if (count($arr_educatehis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis15) .")) or (a.PER_ID in (". implode(",", $arr_educatehis16) .")))";
		elseif (count($arr_educatehis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis15) .")))";
		elseif (count($arr_educatehis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")))";
		elseif (count($arr_educatehis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")))";
		elseif (count($arr_educatehis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")))";
		elseif (count($arr_educatehis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")))";
		elseif (count($arr_educatehis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")))";
		elseif (count($arr_educatehis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")))";
		elseif (count($arr_educatehis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")))";
		elseif (count($arr_educatehis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")))";
		elseif (count($arr_educatehis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")))";
		elseif (count($arr_educatehis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis5) .")))";
		elseif (count($arr_educatehis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")))";
		elseif (count($arr_educatehis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")))";
		elseif (count($arr_educatehis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_educatehis) ."))";
	} // end if

	/* ================= 	ประวัติการอบรม/ดูงาน    ===================== */
	$search_trn = "";
	if(trim($search_tr_code)) $arr_search_trainhis_condition[] = "(TR_CODE = '$search_tr_code')";
	if(trim($search_trn_no)) $arr_search_trainhis_condition[] = "(TRN_NO = '$search_trn_no')";
	if(trim($search_trn_org)) $arr_search_trainhis_condition[] = "(TRN_ORG like '$search_trn_org%')";
	if(trim($search_tr_ct_code)) $arr_search_trainhis_condition[] = "(CT_CODE = '$search_tr_ct_code')";

	if(count($arr_search_trainhis_condition)){
		for ($i=0;$i<count($TRN_TYPE);$i++) {
			if($search_trn) { $search_trn.= ' or '; }
			$search_trn.= "TRN_TYPE like '%$TRN_TYPE[$i]%' "; 
		} 
		if ($search_trn) $arr_search_trainhis_condition[] = "(".$search_trn.")";
		$cmd = " select distinct PER_ID from PER_TRAINING where ". implode(" and ", $arr_search_trainhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_trainhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_trainhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_trainhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_trainhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_trainhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_trainhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_trainhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_trainhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_trainhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_trainhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_trainhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_trainhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_trainhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_trainhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_trainhis15[] = $data[PER_ID];
			else $arr_trainhis16[] = $data[PER_ID];
		}
		
		if (count($arr_trainhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis15) .")) or (a.PER_ID in (". implode(",", $arr_trainhis16) .")))";
		elseif (count($arr_trainhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis15) .")))";
		elseif (count($arr_trainhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")))";
		elseif (count($arr_trainhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")))";
		elseif (count($arr_trainhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")))";
		elseif (count($arr_trainhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")))";
		elseif (count($arr_trainhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")))";
		elseif (count($arr_trainhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")))";
		elseif (count($arr_trainhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")))";
		elseif (count($arr_trainhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")))";
		elseif (count($arr_trainhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")))";
		elseif (count($arr_trainhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis5) .")))";
		elseif (count($arr_trainhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")))";
		elseif (count($arr_trainhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")))";
		elseif (count($arr_trainhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_trainhis) ."))";
	} // end if

	/* ======================================================== */

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "DELETE" && $PER_ID){
		$cmd = " select PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		
		$cmd = " delete from PER_PERSONAL where PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล$PERSON_TITLE [ $PER_ID : $PER_NAME $PER_SURNAME ]");
	} // end if
?>