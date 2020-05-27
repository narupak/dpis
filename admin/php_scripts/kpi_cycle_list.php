<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
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
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$search_per_type = (isset($search_per_type))?  $search_per_type : 1;
    if($search_org_id){
		$arr_search_sub[] = "(a.ORG_ID=$search_org_id OR (ORG_ID_REF=$search_org_id AND OL_CODE='03'))";
  	}elseif($search_department_id){
		$arr_search_sub[] = "(a.ORG_ID = $search_department_id OR (ORG_ID_REF=$search_department_id AND OL_CODE='03'))";
	}elseif($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_sub[] = "(a.ORG_ID in (". implode(",", $arr_org) .") OR (ORG_ID_REF= in (". implode(",", $arr_org) .") AND OL_CODE='03'))";
	} // end if
	$search_sub_condition = ""; // สำหรับค้นหา สังกัด ใน sql ย่อย
	if(count($arr_search_sub)) $search_sub_condition = implode(" and ", $arr_search_sub);

	if ($command == "SEARCHALL") 
		$search_kf_cycle = "";
	elseif (!$search_kf_cycle && $command != "SEARCHALL") 
		if (substr($UPDATE_DATE,5,2) > "09" || substr($UPDATE_DATE,5,2) < "04") $search_kf_cycle = 1;
		elseif (substr($UPDATE_DATE,5,2) > "03" && substr($UPDATE_DATE,5,2) < "10") $search_kf_cycle = 2;

	if(!isset($search_kf_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if
	
  	if(trim($search_kf_year)){ 
		if($DPISDB=="odbc"){ 
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_kf_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_kf_year - 543)."-10-01')";
		}elseif($DPISDB=="oci8"){
			$arr_search_condition[] = "(SUBSTR(a.KF_START_DATE, 1, 10) >= '". ($search_kf_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(SUBSTR(a.KF_END_DATE, 1, 10) < '". ($search_kf_year - 543)."-10-01')";
		}elseif($DPISDB=="mysql"){
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_kf_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_kf_year - 543)."-10-01')";
		} // end if
	} // end if
	if(trim($search_per_type)) 	$arr_search_condition[] = "(c.PER_TYPE = $search_per_type)";
	if ($search_kf_cycle)    $arr_search_condition[] = "(a.KF_CYCLE = $search_kf_cycle)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = implode(" and ", $arr_search_condition);

	$arr_content = (array) null;
	
	$cmd = " select DISTINCT KF_CYCLE, KF_START_DATE, KF_END_DATE, a.DEPARTMENT_ID , a.ORG_ID
					 from PER_KPI_FORM a, PER_COMPETENCY_FORM b, PER_PERSONAL c, PER_ORG d
				   where a.KF_ID = b.KF_ID and a.PER_ID=c.PER_ID and a.ORG_ID=d.ORG_ID
				   				and $search_condition and $search_sub_condition
				   order by KF_CYCLE DESC, KF_START_DATE DESC ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd<br>";
	$datacount = 0;
	while ($data = $db_dpis->get_array()) {
		$KF_CYCLE = trim($data[KF_CYCLE]);
		if($KF_CYCLE==1){	//ตรวจสอบรอบการประเมิน
			$KF_START_DATE_1 = show_date_format($data[KF_START_DATE], 1);
			$KF_END_DATE_1 = show_date_format($data[KF_END_DATE], 1);
			$KF_YEAR = substr($KF_END_DATE_1, 6, 4);
		}else if($KF_CYCLE==2){
			$KF_START_DATE_2 = show_date_format($data[KF_START_DATE], 1);
			$KF_END_DATE_2 = show_date_format($data[KF_END_DATE], 1);
			$KF_YEAR = substr($KF_END_DATE_2, 6, 4);
		} // end if $KF_CYCLE
		$KF_START = $data[KF_START_DATE];
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$ORG_ID = $data[ORG_ID];
		if ($ORG_ID) { $srhorg=$ORG_ID; } else { $srhorg=$DEPARTMENT_ID; }
		$cmd = "  select ORG_ID, ORG_NAME from PER_ORG a 
						where  ORG_ID = $srhorg
						order by ORG_ID
					 ";
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
//		echo "$cmd<br>";
		while($data1 = $db_dpis1->get_array()) {
			if ($DEPARTMENT_ID == $data1[ORG_ID]) {
				$DEPARTMENT_NAME = $data1[ORG_NAME];
				$arr_content[$datacount][org_name] = "";
			} else {
				$arr_content[$datacount][org_name] = $data1[ORG_NAME];
			}
			$arr_content[$datacount][kfcycle] = $KF_CYCLE;
			$arr_content[$datacount][kfyear] = $KF_YEAR;
			$arr_content[$datacount][kfstart] = $KF_START;
			$arr_content[$datacount][dept_id] = $DEPARTMENT_ID;
			$arr_content[$datacount][org_id] = $data1[ORG_ID];
		
			$datacount++;
		} // end while $data1
	} // end while $data
	$count_data = count($arr_content);
?>