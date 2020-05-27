<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
  	
	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
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
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
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

	if( $command=='MASTER' ) {  // ข้อมูลหลัก 
// คำนำหน้าชื่อ  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PRENAME' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PN_CODE, PN_SHORTNAME, PN_NAME, PN_ENG_NAME, PN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_PRENAME ORDER BY PN_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PN_CODE = trim($data[PN_CODE]);
			$PN_SHORTNAME = trim($data[PN_SHORTNAME]);
			$PN_NAME = trim($data[PN_NAME]);
			$PN_ENG_NAME = trim($data[PN_ENG_NAME]);
			$PN_ACTIVE = $data[PN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE = '$PN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PN_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_PRENAME', '$PN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_PRENAME (PN_CODE, PN_SHORTNAME, PN_NAME, PN_ENG_NAME, PN_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$PN_CODE', '$PN_SHORTNAME', '$PN_NAME', '$PN_ENG_NAME', $PN_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ศาสนา
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_RELIGION' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT RE_CODE, RE_NAME, RE_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_RELIGION ORDER BY RE_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$RE_CODE = trim($data[RE_CODE]);
			$RE_NAME = trim($data[RE_NAME]);
			$RE_ACTIVE = $data[RE_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select RE_NAME from PER_RELIGION where RE_CODE = '$RE_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select RE_CODE from PER_RELIGION where RE_NAME = '$RE_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[RE_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_RELIGION', '$RE_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_RELIGION (RE_CODE, RE_NAME, RE_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$RE_CODE', '$RE_NAME', $RE_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while				
		
// ประเทศ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_COUNTRY' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT CT_CODE, CT_NAME, CT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_COUNTRY ORDER BY CT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$CT_CODE = trim($data[CT_CODE]);
			$CT_NAME = trim($data[CT_NAME]);
			$CT_ACTIVE = $data[CT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE = '$CT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select CT_CODE from PER_COUNTRY where CT_NAME = '$CT_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[CT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_COUNTRY', '$CT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_COUNTRY (CT_CODE, CT_NAME, CT_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$CT_CODE', '$CT_NAME', $CT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while		
		
//		$cmd = " select count(CT_CODE) as COUNT_NEW from PER_COUNTRY ";
//		$db_dpis->send_cmd($cmd);
//		$data = $db_dpis->get_array();
//		$COUNT_NEW = $data[COUNT_NEW] + 0;
//		echo "PER_COUNTRY - $PER_COUNTRY - $COUNT_NEW<br>";

// จังหวัด
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PROVINCE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_PROVINCE ORDER BY PV_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PV_CODE = trim($data[PV_CODE]);
			$PV_NAME = trim($data[PV_NAME]);
			$CT_CODE = trim($data[CT_CODE]);
			$PV_ACTIVE = $data[PV_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = '$PV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME = '$PV_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PV_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_PROVINCE', '$PV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_PROVINCE (PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$PV_CODE', '$PV_NAME', '$CT_CODE', $PV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// อำเภอ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_AMPHUR' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT AP_CODE, AP_NAME, PV_CODE, AP_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_AMPHUR ORDER BY AP_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$AP_CODE = trim($data[AP_CODE]);
			$AP_NAME = trim($data[AP_NAME]);
			$PV_CODE = trim($data[PV_CODE]);
			$AP_ACTIVE = $data[AP_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE = '$AP_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select AP_CODE from PER_AMPHUR where AP_NAME = '$AP_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[AP_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_AMPHUR', '$AP_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_AMPHUR (AP_CODE, AP_NAME, PV_CODE, AP_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$AP_CODE', '$AP_NAME', '$PV_CODE', $AP_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// สังกัด  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_TYPE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_TYPE ORDER BY OT_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$OT_CODE = trim($data[OT_CODE]);
			$OT_NAME = trim($data[OT_NAME]);
			$OT_ACTIVE = $data[OT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE = '$OT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select OT_CODE from PER_ORG_TYPE where OT_NAME = '$OT_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[OT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ORG_TYPE', '$OT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$OT_CODE', '$OT_NAME', $OT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ชื่อตำแหน่งในสายงาน  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_LINE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, PL_CODE_NEW, 
						LG_CODE, CL_NAME, PL_CODE_DIRECT 
						FROM PER_LINE ORDER BY PL_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PL_CODE = trim($data[PL_CODE]);
			$PL_NAME = trim($data[PL_NAME]);
			$PL_SHORTNAME = trim($data[PL_SHORTNAME]);
			$PL_ACTIVE = $data[PL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PL_TYPE = $data[PL_TYPE] + 0;
			$PL_CODE_NEW = trim($data[PL_CODE_NEW]);
			$LG_CODE = trim($data[LG_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$PL_CODE_DIRECT = trim($data[PL_CODE_DIRECT]);
			if (!$PL_SHORTNAME) $PL_SHORTNAME = "-";

			$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$PL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_LINE', '$PL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
									PL_TYPE, PL_CODE_NEW, LG_CODE, CL_NAME, PL_CODE_DIRECT)
									VALUES ('$PL_CODE', '$PL_NAME', '$PL_SHORTNAME', $PL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', $PL_TYPE, 
									'$PL_CODE_NEW', '$LG_CODE', '$CL_NAME', '$PL_CODE_DIRECT') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ประเภทการลา  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ABSENTTYPE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ABSENTTYPE ORDER BY AB_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$AB_CODE = trim($data[AB_CODE]);
			$AB_NAME = trim($data[AB_NAME]);
			$AB_QUOTA = $data[AB_QUOTA] + 0;
			$AB_COUNT = $data[AB_COUNT] + 0;
			$AB_ACTIVE = $data[AB_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select AB_NAME from PER_ABSENTTYPE where AB_CODE = '$AB_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select AB_CODE from PER_ABSENTTYPE where AB_NAME = '$AB_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[AB_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ABSENTTYPE', '$AB_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$AB_CODE', '$AB_NAME', $AB_QUOTA, $AB_COUNT, $AB_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ฐานะของตำแหน่ง
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_STATUS' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_STATUS ORDER BY PS_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PS_CODE = trim($data[PS_CODE]);
			$PS_NAME = trim($data[PS_NAME]);
			$PS_ACTIVE = $data[PS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select PS_NAME from PER_STATUS where PS_CODE = '$PS_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select PS_CODE from PER_STATUS where PS_NAME = '$PS_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PS_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_STATUS', '$PS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$PS_CODE', '$PS_NAME', $PS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ชื่อตำแหน่งในการบริหารงาน  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MGT' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MGT ORDER BY PM_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PM_CODE = trim($data[PM_CODE]);
			$PM_NAME = trim($data[PM_NAME]);
			$PM_SHORTNAME = trim($data[PM_SHORTNAME]);
			$PS_CODE = trim($data[PS_CODE]);
			$PM_ACTIVE = $data[PM_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_STATUS' AND OLD_CODE = $PS_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PS_CODE = trim($data2[NEW_CODE]);

			$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$PM_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PM_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_MGT', '$PM_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_MGT (PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$PM_CODE', '$PM_NAME', '$PM_SHORTNAME', '$PS_CODE', $PM_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ประเภทตำแหน่ง 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TYPE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TYPE ORDER BY PT_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PT_CODE = trim($data[PT_CODE]);
			$PT_NAME = trim($data[PT_NAME]);
			$PT_GROUP = $data[PT_GROUP] + 0;
			$PT_ACTIVE = $data[PT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select PT_NAME from PER_TYPE where PT_CODE = '$PT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select PT_CODE from PER_TYPE where PT_NAME = '$PT_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_TYPE', '$PT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_TYPE (PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$PT_CODE', '$PT_NAME', $PT_GROUP, $PT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ระดับการศึกษา
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EDUCLEVEL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT EL_CODE, EL_NAME, EL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EDUCLEVEL ORDER BY EL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EL_CODE = trim($data[EL_CODE]);
			$EL_NAME = trim($data[EL_NAME]);
			$EL_ACTIVE = $data[EL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select EL_NAME from PER_EDUCLEVEL where EL_CODE = '$EL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select EL_CODE from PER_EDUCLEVEL where EL_NAME = '$EL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EDUCLEVEL', '$EL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EDUCLEVEL (EL_CODE, EL_NAME, EL_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$EL_CODE', '$EL_NAME', $EL_ACTIVE, $UPDATE_USER, 	'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// วุฒิการศึกษา  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EDUCNAME' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EDUCNAME ORDER BY EN_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EN_CODE = trim($data[EN_CODE]);
			$EL_CODE = trim($data[EL_CODE]);
			$EN_SHORTNAME = trim($data[EN_SHORTNAME]);
			$EN_SHORTNAME=str_replace("'","''",$EN_SHORTNAME);
			$EN_SHORTNAME=str_replace("\"","&quot;",$EN_SHORTNAME);
			$EN_NAME = trim($data[EN_NAME]);
			$EN_NAME=str_replace("'","''",$EN_NAME);
			$EN_NAME=str_replace("\"","&quot;",$EN_NAME);
			$EN_ACTIVE = $data[EN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCLEVEL' AND OLD_CODE = $EL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $EL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select EN_NAME from PER_EDUCNAME where EN_CODE = '$EN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME = '$EN_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EN_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EDUCNAME', '$EN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EDUCNAME (EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$EN_CODE', '$EL_CODE', '$EN_SHORTNAME', '$EN_NAME', $EN_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// สาขาวิชาเอก  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EDUCMAJOR' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT EM_CODE, EM_NAME, EM_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EDUCMAJOR ORDER BY EM_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EM_CODE = trim($data[EM_CODE]);
			$EM_NAME = trim($data[EM_NAME]);
			$EM_ACTIVE = $data[EM_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select EM_NAME from PER_EDUCMAJOR where EM_CODE = '$EM_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select EM_CODE from PER_EDUCMAJOR where EM_NAME = '$EM_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EM_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EDUCMAJOR', '$EM_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EDUCMAJOR (EM_CODE, EM_NAME, EM_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$EM_CODE', '$EM_NAME', $EM_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// หลักสูตรการฝึกอบรม/ดูงาน  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TRAIN' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TRAIN ORDER BY TR_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TR_CODE = trim($data[TR_CODE]);
			$TR_TYPE = $data[TR_TYPE] + 0;
			$TR_NAME = trim($data[TR_NAME]);
			$TR_NAME=str_replace("'","''",$TR_NAME);
			$TR_NAME=str_replace("\"","&quot;",$TR_NAME);
			$TR_ACTIVE = $data[TR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select TR_NAME from PER_TRAIN where TR_CODE = '$TR_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select TR_CODE from PER_TRAIN where TR_NAME = '$TR_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[TR_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_TRAIN', '$TR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_TRAIN (TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$TR_CODE', $TR_TYPE, '$TR_NAME', $TR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ประเภทการเคลื่อนไหว  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MOVMENT' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MOVMENT ORDER BY MOV_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$MOV_CODE = trim($data[MOV_CODE]);
			$MOV_NAME = trim($data[MOV_NAME]);
			$MOV_TYPE = $data[MOV_TYPE] + 0;
			$MOV_ACTIVE = $data[MR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select MOV_CODE from PER_MOVMENT where MOV_NAME = '$MOV_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[MOV_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_MOVMENT', '$MOV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$MOV_CODE', '$MOV_NAME', $MOV_TYPE, $MOV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ฐานความผิด  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CRIME' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT CR_CODE, CR_NAME, CR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CRIME ORDER BY CR_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$CR_CODE = trim($data[CR_CODE]);
			$CR_NAME = trim($data[CR_NAME]);
			$CR_ACTIVE = $data[CR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select CR_NAME from PER_CRIME where CR_CODE = '$CR_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select CR_CODE from PER_CRIME where CR_NAME = '$CR_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[CR_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_CRIME', '$CR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_CRIME (CR_CODE, CR_NAME, CR_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$CR_CODE', '$CR_NAME', $CR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// กรณีความผิด  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CRIME_DTL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT CRD_CODE, CRD_NAME, CR_CODE, CRD_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CRIME_DTL ORDER BY CRD_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$CRD_CODE = trim($data[CRD_CODE]);
			$CRD_NAME = trim($data[CRD_NAME]);
			$CR_CODE = trim($data[CR_CODE]);
			$CRD_ACTIVE = $data[CRD_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_CRIME' AND OLD_CODE = $CR_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $CR_CODE = trim($data2[NEW_CODE]);

			$cmd = " select CRD_NAME from PER_CRIME_DTL where CRD_CODE = '$CRD_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select CRD_CODE from PER_CRIME_DTL where CRD_NAME = '$CRD_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[CRD_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_CRIME_DTL', '$CRD_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_CRIME_DTL (CRD_CODE, CRD_NAME, CR_CODE, CRD_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$CRD_CODE', '$CRD_NAME', '$CR_CODE', $CRD_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ประเภททุน		
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SCHOLARTYPE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT ST_CODE, ST_NAME, ST_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SCHOLARTYPE ORDER BY ST_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$ST_CODE = trim($data[ST_CODE]);
			$ST_NAME = trim($data[ST_NAME]);
			$ST_ACTIVE = $data[ST_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select ST_NAME from PER_SCHOLARTYPE where ST_CODE = '$ST_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select ST_CODE from PER_SCHOLARTYPE where ST_NAME = '$ST_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[ST_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SCHOLARTYPE', '$ST_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SCHOLARTYPE (ST_CODE, ST_NAME, ST_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$ST_CODE', '$ST_NAME', $ST_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ปฏิทินวันหยุด
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_HOLIDAY' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT HOL_DATE, HOL_NAME, UPDATE_USER, UPDATE_DATE 
						FROM PER_HOLIDAY ORDER BY HOL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$HOL_DATE = trim($data[HOL_DATE]);
			$HOL_NAME = trim($data[HOL_NAME]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select HOL_NAME from PER_HOLIDAY where HOL_DATE = '$HOL_DATE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select HOL_DATE from PER_HOLIDAY where HOL_NAME = '$HOL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[HOL_DATE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_HOLIDAY', '$HOL_DATE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_CRIME (HOL_DATE, HOL_NAME, UPDATE_USER, UPDATE_DATE)
									VALUES ('$HOL_DATE', '$HOL_NAME', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ด้านความสามารถพิเศษ  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ABILITYGRP' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT AL_CODE, AL_NAME, AL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ABILITYGRP ORDER BY AL_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$AL_CODE = trim($data[AL_CODE]);
			$AL_NAME = trim($data[AL_NAME]);
			$AL_ACTIVE = $data[AL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select AL_NAME from PER_ABILITYGRP where AL_CODE = '$AL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select AL_CODE from PER_ABILITYGRP where AL_NAME = '$AL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[AL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ABILITYGRP', '$AL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ABILITYGRP (AL_CODE, AL_NAME, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$AL_CODE', '$AL_NAME', $AL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// เงื่อนไขตำแหน่ง
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CONDITION' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PC_CODE, PC_NAME, PC_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CONDITION ORDER BY PC_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PC_CODE = trim($data[PC_CODE]);
			$PC_NAME = trim($data[PC_NAME]);
			$PC_ACTIVE = $data[PC_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select PC_NAME from PER_CONDITION where PC_CODE = '$PC_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select PC_CODE from PER_CONDITION where PC_NAME = '$PC_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PC_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_CONDITION', '$PC_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_CONDITION (PC_CODE, PC_NAME, PC_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$PC_CODE', '$PC_NAME', $PC_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ประเภทราชการพิเศษ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SERVICE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT SV_CODE, SV_NAME, SV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SERVICE ORDER BY SV_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SV_CODE = trim($data[SV_CODE]);
			$SV_NAME = trim($data[SV_NAME]);
			$SV_ACTIVE = $data[SV_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select SV_NAME from PER_SERVICE where SV_CODE = '$SV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select SV_CODE from PER_SERVICE where SV_NAME = '$SV_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SV_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SERVICE', '$SV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SERVICE (SV_CODE, SV_NAME, SV_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$SV_CODE', '$SV_NAME', $SV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ระดับตำแหน่ง
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_LEVEL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO    
						FROM PER_LEVEL ORDER BY LEVEL_NO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_ACTIVE = $data[LEVEL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]);
			$PER_TYPE = $data[PER_TYPE] + 0;
			$LEVEL_SHORTNAME = trim($data[LEVEL_SHORTNAME]);
			$LEVEL_SEQ_NO = $data[LEVEL_SEQ_NO] + 0;

			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select LEVEL_NO from PER_LEVEL where LEVEL_NAME = '$LEVEL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[LEVEL_NO]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_LEVEL', '$LEVEL_NO', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, 
									LEVEL_SEQ_NO)
									VALUES ('$LEVEL_NO', $LEVEL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', '$LEVEL_NAME', $PER_TYPE, '$LEVEL_SHORTNAME', 
									$LEVEL_SEQ_NO) ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						
/*
// บัญชีอัตราเงินเดือนข้าราชการ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_LAYER' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_LAYER ORDER BY LAYER_TYPE, LEVEL_NO, LAYER_NO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$LAYER_TYPE = $data[LAYER_TYPE] + 0;
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LAYER_NO = $data[LAYER_NO] + 0;
			$LAYER_SALARY = $data[LAYER_SALARY] + 0;
			$LAYER_ACTIVE = $data[LAYER_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select LEVEL_NO from PER_LEVEL where LEVEL_NAME = '$LEVEL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[LEVEL_NO]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_LEVEL', '$LEVEL_NO', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ($LAYER_TYPE, '$LEVEL_NO', $LAYER_NO, $LAYER_SALARY, $LAYER_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						
*/
// สถาบันการศึกษา  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_INSTITUTE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_INSTITUTE ORDER BY INS_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$INS_CODE = trim($data[INS_CODE]);
			$INS_NAME = trim($data[INS_NAME]);
			$INS_NAME=str_replace("'","''",$INS_NAME);
			$INS_NAME=str_replace("\"","&quot;",$INS_NAME);
			$CT_CODE = trim($data[CT_CODE]);
			$INS_ACTIVE = $data[INS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select INS_NAME from PER_INSTITUTE where INS_CODE = '$INS_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[INS_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_INSTITUTE', '$INS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_INSTITUTE (INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$INS_CODE', '$INS_NAME', '$CT_CODE', $INS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ด้านความเชี่ยวชาญ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SKILL_GROUP' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT SG_CODE, SG_NAME, PL_CODE, SG_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SKILL_GROUP ORDER BY SG_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SG_CODE = trim($data[SG_CODE]);
			$SG_NAME = trim($data[SG_NAME]);
			$PL_CODE = trim($data[PL_CODE]);
			$SG_ACTIVE = $data[SG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LINE' AND OLD_CODE = $PL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select SG_NAME from PER_SKILL_GROUP where SG_CODE = '$SG_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select SG_CODE from PER_SKILL_GROUP where SG_NAME = '$SG_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SG_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SKILL_GROUP', '$SG_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SKILL_GROUP (SG_CODE, SG_NAME, PL_CODE, SG_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$SG_CODE', '$SG_NAME', '$PL_CODE', $SG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// สาขาความเชี่ยวชาญ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SKILL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SKILL ORDER BY SKILL_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SKILL_CODE = trim($data[SKILL_CODE]);
			$SKILL_NAME = trim($data[SKILL_NAME]);
			$SG_CODE = trim($data[SG_CODE]);
			$SKILL_ACTIVE = $data[SKILL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SKILL_GROUP' AND OLD_CODE = $SG_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $SG_CODE = trim($data2[NEW_CODE]);

			$cmd = " select SKILL_NAME from PER_SKILL where SKILL_CODE = '$SKILL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select SKILL_CODE from PER_SKILL where SKILL_NAME = '$SKILL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SKILL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SKILL', '$SKILL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SKILL (SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$SKILL_CODE', '$SKILL_NAME', '$SG_CODE', $SKILL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}  
		} // end while						

// ด้านความเชี่ยวชาญพิเศษ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SPECIAL_SKILLGRP' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SPECIAL_SKILLGRP ORDER BY SS_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SS_CODE = trim($data[SS_CODE]);
			$SS_NAME = trim($data[SS_NAME]);
			$SS_ACTIVE = $data[SS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select SS_NAME from PER_SPECIAL_SKILLGRP where SS_CODE = '$SS_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SS_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SPECIAL_SKILLGRP', '$SS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$SS_CODE', '$SS_NAME', $SS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ชั้นเครื่องราชอิสริยาภรณ์  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_DECORATION' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_DECORATION ORDER BY DC_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$DC_CODE = trim($data[DC_CODE]);
			$DC_SHORTNAME = trim($data[DC_SHORTNAME]);
			$DC_NAME = trim($data[DC_NAME]);
			$DC_ORDER = $data[DC_ORDER] + 0;
			$DC_TYPE = $data[DC_TYPE] + 0;
			$DC_ACTIVE = $data[DC_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select DC_NAME from PER_DECORATION where DC_CODE = '$DC_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select DC_CODE from PER_DECORATION where DC_NAME = '$DC_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[DC_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_DECORATION', '$DC_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, 
									UPDATE_USER, UPDATE_DATE)
									VALUES ('$DC_CODE', '$DC_SHORTNAME', '$DC_NAME', $DC_ORDER, $DC_TYPE, $DC_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ฐานะของหน่วยงาน
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_LEVEL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_LEVEL ORDER BY OL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$OL_CODE = trim($data[OL_CODE]);
			$OL_NAME = trim($data[OL_NAME]);
			$OL_ACTIVE = $data[OL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select OL_NAME from PER_ORG_LEVEL where OL_CODE = '$OL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select OL_CODE from PER_ORG_LEVEL where OL_NAME = '$OL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[OL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ORG_LEVEL', '$OL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$OL_CODE', '$OL_NAME', $OL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// สถานภาพหน่วยงาน 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_STAT' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT OS_CODE, OS_NAME, OS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_STAT ORDER BY OS_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$OS_CODE = trim($data[OS_CODE]);
			$OS_NAME = trim($data[OS_NAME]);
			$OS_ACTIVE = $data[OS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select OS_NAME from PER_ORG_STAT where OS_CODE = '$OS_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select OS_CODE from PER_ORG_STAT where OS_NAME = '$OS_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[OS_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ORG_STAT', '$OS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ORG_STAT (OS_CODE, OS_NAME, OS_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$OS_CODE', '$OS_NAME', $OS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						
/*
// เงินประจำตำแหน่ง  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MGTSALARY' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MGTSALARY ORDER BY PT_CODE, LEVEL_NO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PT_CODE = trim($data[PT_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$MS_SALARY = $data[MS_SALARY] + 0;
			$MS_ACTIVE = $data[MS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PT_CODE', '$LEVEL_NO', $MS_SALARY, $MS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						
*/
// ประเภทข้าราชการ 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_OFF_TYPE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_OFF_TYPE ORDER BY OT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$OT_CODE = trim($data[OT_CODE]);
			$OT_NAME = trim($data[OT_NAME]);
			$OT_ACTIVE = $data[OT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select OT_NAME from PER_OFF_TYPE where OT_CODE = '$OT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select OT_CODE from PER_OFF_TYPE where OT_NAME = '$OT_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[OT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_OFF_TYPE', '$OT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$OT_CODE', '$OT_NAME', $OT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// สถานภาพสมรส
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MARRIED' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT MR_CODE, MR_NAME, MR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MARRIED ORDER BY MR_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$MR_CODE = trim($data[MR_CODE]);
			$MR_NAME = trim($data[MR_NAME]);
			$MR_ACTIVE = $data[MR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select MR_NAME from PER_MARRIED where MR_CODE = '$MR_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select MR_CODE from PER_MARRIED where MR_NAME = '$MR_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[MR_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_MARRIED', '$MR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_MARRIED (MR_CODE, MR_NAME, MR_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$MR_CODE', '$MR_NAME', $MR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// หมู่โลหิต 
// ประเภทเงินเพิ่มพิเศษ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EXTRATYPE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT EX_CODE, EX_NAME, EX_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EXTRATYPE ORDER BY EX_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EX_CODE = trim($data[EX_CODE]);
			$EX_NAME = trim($data[EX_NAME]);
			$EX_ACTIVE = $data[EX_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select EX_NAME from PER_EXTRATYPE where EX_CODE = '$EX_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select EX_CODE from PER_EXTRATYPE where EX_NAME = '$EX_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EX_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EXTRATYPE', '$EX_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$EX_CODE', '$EX_NAME', $EX_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ประเภทเงินพิเศษ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EXTRA_INCOME_TYPE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT EXIN_CODE, EXIN_NAME, EXIN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EXTRA_INCOME_TYPE ORDER BY EXIN_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EXIN_CODE = trim($data[EXIN_CODE]);
			$EXIN_NAME = trim($data[EXIN_NAME]);
			$EXIN_ACTIVE = $data[EXIN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select EXIN_NAME from PER_EXTRA_INCOME_TYPE where EXIN_CODE = '$EXIN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select EXIN_CODE from PER_EXTRA_INCOME_TYPE where EXIN_NAME = '$EXIN_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EXIN_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EXTRA_INCOME_TYPE', '$EXIN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EXTRA_INCOME_TYPE (EXIN_CODE, EXIN_NAME, EXIN_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$EXIN_CODE', '$EXIN_NAME', $EXIN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ช่วงระดับตำแหน่ง  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CO_LEVEL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CO_LEVEL ORDER BY CL_NAME ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$CL_NAME = trim($data[CL_NAME]);
			$LEVEL_NO_MIN = trim($data[LEVEL_NO_MIN]);
			$LEVEL_NO_MAX = trim($data[LEVEL_NO_MAX]);
			$CL_ACTIVE = $data[CL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LEVEL' AND OLD_CODE = $LEVEL_NO_MIN ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $LEVEL_NO_MIN = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LEVEL' AND OLD_CODE = $LEVEL_NO_MAX ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $LEVEL_NO_MAX = trim($data2[NEW_CODE]);

			$cmd = " select CL_NAME from PER_CO_LEVEL where CL_NAME = '$CL_NAME' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE)
								VALUES ('$CL_NAME', '$LEVEL_NO_MIN', '$LEVEL_NO_MAX', $CL_ACTIVE, $UPDATE_USER,	'$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
			}
		} // end while						

// ประเภทโทษทางวินัย  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PENALTY' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PEN_CODE, PEN_NAME, PEN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_PENALTY ORDER BY PEN_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PEN_CODE = trim($data[PEN_CODE]);
			$PEN_NAME = trim($data[PEN_NAME]);
			$PEN_ACTIVE = $data[PEN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select PEN_NAME from PER_PENALTY where PEN_CODE = '$PEN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select PEN_CODE from PER_PENALTY where PEN_NAME = '$PEN_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PEN_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_PENALTY', '$PEN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_PENALTY (PEN_CODE, PEN_NAME, PEN_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$PEN_CODE', '$PEN_NAME', $PEN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ประเภททายาท
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_HEIRTYPE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT HR_CODE, HR_NAME, HR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_HEIRTYPE ORDER BY HR_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$HR_CODE = trim($data[HR_CODE]);
			$HR_NAME = trim($data[HR_NAME]);
			$HR_ACTIVE = $data[HR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select HR_NAME from PER_HEIRTYPE where HR_CODE = '$HR_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select HR_CODE from PER_HEIRTYPE where HR_NAME = '$HR_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[HR_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_HEIRTYPE', '$HR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_HEIRTYPE (HR_CODE, HR_NAME, HR_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$HR_CODE', '$HR_NAME', $HR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// หัวข้อราชการพิเศษ 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SERVICETITLE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT SRT_CODE, SRT_NAME, SV_CODE, SRT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SERVICETITLE ORDER BY SRT_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SRT_CODE = trim($data[SRT_CODE]);
			$SRT_NAME = trim($data[SRT_NAME]);
			$SRT_NAME=str_replace("'","''",$SRT_NAME);
			$SRT_NAME=str_replace("\"","&quot;",$SRT_NAME);
			$SV_CODE = trim($data[SV_CODE]);
			$SRT_ACTIVE = $data[SRT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SERVICE' AND OLD_CODE = $SV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $SV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select SRT_NAME from PER_SERVICETITLE where SRT_CODE = '$SRT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select SRT_CODE from PER_SERVICETITLE where SRT_NAME = '$SRT_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SRT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SERVICETITLE', '$SRT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SERVICETITLE (SRT_CODE, SRT_NAME, SV_CODE, SRT_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$SRT_CODE', '$SRT_NAME', '$SV_CODE', $SRT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ประเภทความดีความชอบ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_REWARD' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT REW_CODE, REW_NAME, REW_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_REWARD ORDER BY REW_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$REW_CODE = trim($data[REW_CODE]);
			$REW_NAME = trim($data[REW_NAME]);
			$REW_ACTIVE = $data[REW_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select REW_NAME from PER_REWARD where REW_CODE = '$REW_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select REW_CODE from PER_REWARD where REW_NAME = '$REW_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[REW_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_REWARD', '$REW_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_REWARD (REW_CODE, REW_NAME, REW_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$REW_CODE', '$REW_NAME', $REW_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// เหตุที่ขาดจากสมรส
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_DIVORCE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT DV_CODE, DV_NAME, DV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_DIVORCE ORDER BY DV_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$DV_CODE = trim($data[DV_CODE]);
			$DV_NAME = trim($data[DV_NAME]);
			$DV_ACTIVE = $data[DV_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select DV_NAME from PER_DIVORCE where DV_CODE = '$DV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select DV_CODE from PER_DIVORCE where DV_NAME = '$DV_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[DV_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_DIVORCE', '$DV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_DIVORCE (DV_CODE, DV_NAME, DV_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$DV_CODE', '$DV_NAME', $DV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// เวลาทวีคูณ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TIME' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT TIME_CODE, TIME_NAME, TIME_START, TIME_END, TIME_DAY, TIME_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TIME ORDER BY TIME_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TIME_CODE = trim($data[TIME_CODE]);
			$TIME_NAME = trim($data[TIME_NAME]);
			$TIME_START = trim($data[TIME_START]);
			$TIME_END = trim($data[TIME_END]);
			$TIME_DAY = $data[TIME_DAY] + 0;
			$TIME_ACTIVE = $data[TIME_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select TIME_NAME from PER_TIME where TIME_CODE = '$TIME_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select TIME_CODE from PER_TIME where TIME_NAME = '$TIME_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[TIME_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_TIME', '$TIME_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_TIME (TIME_CODE, TIME_NAME, TIME_START, TIME_END, TIME_DAY, TIME_ACTIVE, 
									UPDATE_USER, UPDATE_DATE)
									VALUES ('$TIME_CODE', '$TIME_NAME', '$TIME_START', '$TIME_END', $TIME_DAY, $TIME_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

// ประเภทคำสั่ง
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_COMTYPE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT COM_TYPE, COM_NAME, COM_DESC, COM_GROUP 
						FROM PER_COMTYPE ORDER BY COM_TYPE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$COM_TYPE = trim($data[COM_TYPE]);
			$COM_NAME = trim($data[COM_NAME]);
			$COM_DESC = trim($data[COM_DESC]);
			$COM_GROUP = trim($data[COM_GROUP]);

			$cmd = " select COM_NAME from PER_COMTYPE where COM_TYPE = '$COM_TYPE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select COM_TYPE from PER_COMTYPE where COM_NAME = '$COM_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[COM_TYPE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_COMTYPE', '$COM_TYPE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
									VALUES ('$COM_TYPE', '$COM_NAME', '$COM_DESC', '$COM_GROUP') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

  		if(strtoupper($dpisdb_user)=="D07000") {
// หมวดตำแหน่งลูกจ้าง
			$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_GROUP' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error() ;

			$cmd = " SELECT PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE 
							FROM PER_POS_GROUP ORDER BY PG_CODE ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$PG_CODE = trim($data[PG_CODE]);
				$PG_NAME = trim($data[PG_NAME]);
				$PG_ACTIVE = $data[PG_ACTIVE] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " select PG_NAME from PER_POS_GROUP where PG_CODE = '$PG_CODE' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if (!$count_data) {			
					$cmd = " select PG_CODE from PER_POS_GROUP where PG_NAME = '$PG_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);

					if ($count_data) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PG_CODE]);
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_POS_GROUP', '$PG_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						$db_dpis->show_error();
					} else {
						$cmd = " INSERT INTO PER_POS_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$PG_CODE', '$PG_NAME', $PG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						$db_dpis->show_error();
					}
				}
			} // end while						
/*
// บัญชีอัตราเงินเดือนลูกจ้างประจำ 
			$cmd = " SELECT PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, 	UPDATE_USER, UPDATE_DATE 
							FROM PER_LAYEREMP ORDER BY PG_CODE, LAYERE_NO ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$PG_CODE = trim($data[PG_CODE]);
				$LAYERE_NO = $data[LAYERE_NO] + 0;
				$LAYERE_SALARY = $data[LAYERE_SALARY] + 0;
				$LAYERE_DAY = $data[LAYERE_DAY] + 0;
				$LAYERE_HOUR = $data[LAYERE_HOUR] + 0;
				$LAYERE_ACTIVE = $data[LAYERE_ACTIVE] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, 
								UPDATE_USER, UPDATE_DATE)
								VALUES ('$PG_CODE', $LAYERE_NO, $LAYERE_SALARY, $LAYERE_DAY, $LAYERE_HOUR, $LAYERE_ACTIVE, $UPDATE_USER, 
								'$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end while						
*/
// ชื่อตำแหน่งลูกจ้าง
			$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_NAME' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error() ;

			$cmd = " SELECT PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE 
							FROM PER_POS_NAME ORDER BY PN_CODE ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$PN_CODE = trim($data[PN_CODE]);
				$PN_NAME = trim($data[PN_NAME]);
				$PG_CODE = trim($data[PG_CODE]);
				$PN_DECOR = $data[PN_DECOR] + 0;
				$PN_ACTIVE = $data[PN_ACTIVE] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);
	
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_GROUP' AND OLD_CODE = $PG_CODE ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PG_CODE = trim($data2[NEW_CODE]);

				$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE = '$PN_CODE' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if (!$count_data) {			
					$cmd = " select PN_CODE from PER_POS_NAME where PN_NAME = '$PN_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);

					if ($count_data) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PN_CODE]);
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_POS_NAME', '$PN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						$db_dpis->show_error();
					} else {
						$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$PN_CODE', '$PN_NAME', '$PG_CODE', $PN_DECOR, $PN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						$db_dpis->show_error();
					}
				}
			} // end while						

// ชื่อตำแหน่งพนักงานราชการ
			$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EMPSER_POS_NAME' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error() ;

			$cmd = " SELECT EP_CODE, EP_NAME, LEVEL_NO, EP_DECOR, EP_ACTIVE, UPDATE_USER, UPDATE_DATE 
							FROM PER_EMPSER_POS_NAME ORDER BY EP_CODE ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$EP_CODE = trim($data[EP_CODE]);
				$EP_NAME = trim($data[EP_NAME]);
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$EP_DECOR = $data[EP_DECOR] + 0;
				$EP_ACTIVE = $data[EP_ACTIVE] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);
	
				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE = '$EP_CODE' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if (!$count_data) {			
					$cmd = " select EP_CODE from PER_EMPSER_POS_NAME where EP_NAME = '$EP_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);

					if ($count_data) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[EP_CODE]);
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_EMPSER_POS_NAME', '$EP_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						$db_dpis->show_error();
					} else {
						$cmd = " INSERT INTO PER_EMPSER_POS_NAME (EP_CODE, EP_NAME, LEVEL_NO, EP_DECOR, EP_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$EP_CODE', '$EP_NAME', '$LEVEL_NO', $EP_DECOR, $EP_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						$db_dpis->show_error();
					}
				}
			} // end while						
		} // end if						
		
// กลุ่มสำนักงานประจำจังหวัด
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_PROVINCE' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT OP_CODE, OP_NAME, OP_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_PROVINCE ORDER BY OP_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$OP_CODE = trim($data[OP_CODE]);
			$OP_NAME = trim($data[OP_NAME]);
			$OP_ACTIVE = $data[OP_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select OP_NAME from PER_ORG_PROVINCE where OP_CODE = '$OP_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select OP_CODE from PER_ORG_PROVINCE where OP_NAME = '$OP_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[OP_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ORG_PROVINCE', '$OP_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ORG_PROVINCE (OP_CODE, OP_NAME, OP_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$OP_CODE', '$OP_NAME', $OP_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
				}
			}
		} // end while						

	}

// บัญชีอัตราเงินเดือนข้าราชการใหม่ 
	if( $command=='ORG' ) { // โครงสร้างส่วนราชการ  
		$table = array(	"per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", "per_coursedtl", "per_decordtl", "per_promote_p", "per_move_req", "per_absent", 
									"per_personal" );
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id AND $where) ";
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			$db_dpis1->show_error();
		} // end for

		$cmd = " delete from per_move_req 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_move_req 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_move_req 
						where org_id_3 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_pos_move 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_salquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_bonusquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_command where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMP WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL10 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL9 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL8 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL7 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL6 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL5 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL4 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL3 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL2 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL1 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM 
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " UPDATE PER_POSITIONHIS SET ORG_ID_3 = 1 
						WHERE ORG_ID_3 IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;
		
		$cmd = " DELETE FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
						ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO 
						FROM PER_ORG  WHERE DEPARTMENT_ID = $DEPT_ID ORDER BY OL_CODE, ORG_ID_REF ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG++;
			$ORG_ID = $data[ORG_ID] + 0;
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_SHORT = trim($data[ORG_SHORT]);
			$OL_CODE = trim($data[OL_CODE]);
			$OT_CODE = trim($data[OT_CODE]);
			$OP_CODE = trim($data[OP_CODE]);
			$OS_CODE = trim($data[OS_CODE]);
			$ORG_ADDR1 = trim($data[ORG_ADDR1]);
			$ORG_ADDR2 = trim($data[ORG_ADDR2]);
			$ORG_ADDR3 = trim($data[ORG_ADDR3]);
			$AP_CODE = trim($data[AP_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$ORG_DATE = trim($data[ORG_DATE]);
			$ORG_JOB = trim($data[ORG_JOB]);
			$ORG_ID_REF = $data[ORG_ID_REF] + 0;
			$ORG_ACTIVE = $data[ORG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$ORG_WEBSITE = trim($data[ORG_WEBSITE]);
			$ORG_SEQ_NO = $data[ORG_SEQ_NO] + 0;
			if ($OL_CODE=='03') $ORG_ID_REF = $search_department_id;
			else {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_REF ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_ID_REF = $data2[NEW_CODE] + 0;
			}

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_LEVEL' AND OLD_CODE = $OL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_TYPE' AND OLD_CODE = $OT_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OT_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_PROVINCE' AND OLD_CODE = $OP_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OP_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_STAT' AND OLD_CODE = $OS_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OS_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_AMPHUR' AND OLD_CODE = $AP_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $AP_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
							ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
							ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID)
							VALUES ($MAX_ID, '$ORG_CODE', '$ORG_NAME', '$ORG_SHORT', '$OL_CODE', '$OT_CODE', 
							'$OP_CODE', '$OS_CODE', '$ORG_ADDR1', '$ORG_ADDR2', '$ORG_ADDR3', '$AP_CODE', '$PV_CODE', 
							'$CT_CODE', '$ORG_DATE', '$ORG_JOB', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE', '$ORG_WEBSITE', $ORG_SEQ_NO, $search_department_id) ";
			$db_dpis->send_cmd($cmd);
			if ($ORG_ID==11067) {
				$db_dpis->show_error();
				echo "<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$ORG_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG - $COUNT_NEW<br>";

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_ASS' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " UPDATE PER_PERSONAL SET ORG_ID = NULL WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_ORG_JOB WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT ORG_ID FROM PER_ORG_ASS  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG_ASS ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
						ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO 
						FROM PER_ORG_ASS  WHERE DEPARTMENT_ID = $DEPT_ID ORDER BY OL_CODE, ORG_ID_REF ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG_ASS++;
			$ORG_ID = $data[ORG_ID] + 0;
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_SHORT = trim($data[ORG_SHORT]);
			$OL_CODE = trim($data[OL_CODE]);
			$OT_CODE = trim($data[OT_CODE]);
			$OP_CODE = trim($data[OP_CODE]);
			$OS_CODE = trim($data[OS_CODE]);
			$ORG_ADDR1 = trim($data[ORG_ADDR1]);
			$ORG_ADDR2 = trim($data[ORG_ADDR2]);
			$ORG_ADDR3 = trim($data[ORG_ADDR3]);
			$AP_CODE = trim($data[AP_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$ORG_DATE = trim($data[ORG_DATE]);
			$ORG_JOB = trim($data[ORG_JOB]);
			$ORG_ID_REF = $data[ORG_ID_REF] + 0;
			$ORG_ACTIVE = $data[ORG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$ORG_WEBSITE = trim($data[ORG_WEBSITE]);
			$ORG_SEQ_NO = $data[ORG_SEQ_NO] + 0;
			if ($OL_CODE=='03') $ORG_ID_REF = $search_department_id;
			else {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_ASS' AND OLD_CODE = $ORG_ID_REF ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_ID_REF = $data2[NEW_CODE] + 0;
			}

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_LEVEL' AND OLD_CODE = $OL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_TYPE' AND OLD_CODE = $OT_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OT_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_PROVINCE' AND OLD_CODE = $OP_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OP_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_STAT' AND OLD_CODE = $OS_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OS_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_AMPHUR' AND OLD_CODE = $AP_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $AP_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, 
							OS_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, 
							ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID)
							VALUES ($MAX_ID, '$ORG_CODE', '$ORG_NAME', '$ORG_SHORT', '$OL_CODE', '$OT_CODE', 
							'$OP_CODE', '$OS_CODE', '$ORG_ADDR1', '$ORG_ADDR2', '$ORG_ADDR3', '$AP_CODE', '$PV_CODE', 
							'$CT_CODE', '$ORG_DATE', '$ORG_JOB', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE', '$ORG_WEBSITE', $ORG_SEQ_NO, $search_department_id) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG_ASS', '$ORG_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG_ASS where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG_ASS - $PER_ORG_ASS - $COUNT_NEW<br>";

	} // end if

	if( $command=='POSITION' ) { // ตำแหน่งข้าราชการ  
		$table = array(	"per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", "per_coursedtl", "per_decordtl", "per_promote_p", "per_move_req", "per_absent", 
									"per_personal" );
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id AND $where) ";
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			$db_dpis1->show_error();
		} // end for

		$cmd = " delete from per_pos_move 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_salquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_bonusquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " delete from per_command where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

  		if(strtoupper($dpisdb_user)=="D07000") 
			$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
							POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
							POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
							FROM PER_POSITION WHERE DEPARTMENT_ID = $DEPT_ID AND $where ORDER BY POS_ID ";
		else
			$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
							POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
							POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
							FROM PER_POSITION WHERE POS_ID IN (SELECT POS_ID FROM PER_PERSONAL 
							WHERE DEPARTMENT_ID = $DEPT_ID AND $where) ORDER BY POS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POSITION++;
			$POS_ID = $data[POS_ID] + 0;
			$ORG_ID = $data[ORG_ID] + 0;
			$POS_NO = trim($data[POS_NO]);
			$OT_CODE = trim($data[OT_CODE]);
			$ORG_ID_1 = $data[ORG_ID_1] + 0;
			$ORG_ID_2 = $data[ORG_ID_2] + 0;
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$POS_SALARY = $data[POS_SALARY] + 0;
			$POS_MGTSALARY = $data[POS_MGTSALARY] + 0;
			$SKILL_CODE = trim($data[SKILL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$PC_CODE = trim($data[PC_CODE]);
			$POS_CONDITION = trim($data[POS_CONDITION]);
			$POS_DOC_NO = trim($data[POS_DOC_NO]);
			$POS_REMARK = trim($data[POS_REMARK]);
			$POS_DATE = trim($data[POS_DATE]);
			$POS_GET_DATE = trim($data[POS_GET_DATE]);
			$POS_CHANGE_DATE = trim($data[POS_CHANGE_DATE]);
			$POS_STATUS = $data[POS_STATUS] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_1 = $data2[NEW_CODE];
			else $ORG_ID_1 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_2 = $data2[NEW_CODE];
			else $ORG_ID_2 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MGT' AND OLD_CODE = $PM_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PM_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LINE' AND OLD_CODE = $PL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SKILL' AND OLD_CODE = $SKILL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $SKILL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TYPE' AND OLD_CODE = $PT_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PT_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_CONDITION' AND OLD_CODE = $PC_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PC_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, 
							PM_CODE, PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, 
							POS_CONDITION, POS_DOC_NO, POS_REMARK, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, 
							UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
							VALUES ($MAX_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, '$PM_CODE', 
							'$PL_CODE', '$CL_NAME', $POS_SALARY, $POS_MGTSALARY, '$SKILL_CODE', '$PT_CODE', '$PC_CODE', 
							'$POS_CONDITION', '$POS_DOC_NO', '$POS_REMARK', '$POS_DATE', '$POS_GET_DATE', 
							'$POS_CHANGE_DATE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
			$db_dpis->send_cmd($cmd);
//			if ($POS_NO=="2224") {
//				$db_dpis->show_error();
//				echo "<br>";
//			}
			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POSITION', '$POS_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITION - $PER_POSITION - $COUNT_NEW<br>";

  		if(strtoupper($dpisdb_user)=="D07000") 
			$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
							POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
							POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
							FROM PER_POSITION WHERE DEPARTMENT_ID = $DEPT_ID AND $where ORDER BY POS_ID ";
		else
			$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
							POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
							POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, 	DEPARTMENT_ID 
							FROM PER_POSITION WHERE POS_ID IN (SELECT POS_ID FROM PER_PERSONAL 
							WHERE DEPARTMENT_ID = $DEPT_ID AND $where) ORDER BY POS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$POS_NO = $data[POS_NO];

			$cmd = " SELECT POS_NO FROM PER_POSITION WHERE POS_NO = $POS_NO and DEPARTMENT_ID = $search_department_id ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$POS_NO<br>";
		} // end while						

  		if(strtoupper($dpisdb_user)=="D07000") {
// ลูกจ้างประจำ 
			$cmd = " SELECT POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
							FROM PER_POS_EMP WHERE DEPARTMENT_ID = $DEPT_ID AND $where ORDER BY POEM_ID ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$PER_POS_EMP++;
				$POEM_ID = $data[POEM_ID] + 0;
				$ORG_ID = $data[ORG_ID] + 0;
				$POEM_NO = trim($data[POEM_NO]);
				$ORG_ID_1 = $data[ORG_ID_1] + 0;
				$ORG_ID_2 = $data[ORG_ID_2] + 0;
				$PN_CODE = trim($data[PN_CODE]);
				$POEM_MIN_SALARY = $data[POEM_MIN_SALARY] + 0;
				$POEM_MAX_SALARY = $data[POEM_MAX_SALARY] + 0;
				$POEM_STATUS = $data[POEM_STATUS] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID = $data2[NEW_CODE];

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID_1 = $data2[NEW_CODE];
				else $ORG_ID_1 = 'NULL';

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID_2 = $data2[NEW_CODE];
				else $ORG_ID_2 = 'NULL';

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_NAME' AND OLD_CODE = $PN_CODE ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PN_CODE = trim($data2[NEW_CODE]);

				$cmd = " INSERT INTO PER_POS_EMP (POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
								POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
								VALUES ($MAX_ID, $ORG_ID, '$POEM_NO', $ORG_ID_1, $ORG_ID_2, '$PN_CODE', 
								$POEM_MIN_SALARY, $POEM_MAX_SALARY, $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
				$db_dpis->send_cmd($cmd);
//				if ($POEM_NO=='302' || $POEM_NO=='303' || $POEM_NO=='304' || $POEM_NO=='305' || $POEM_NO=='306') {
//					$db_dpis->show_error();
//					echo "<br>";
//				}

				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_POS_EMP', '$POEM_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//db_dpis->show_error();

				$MAX_ID++;
			} // end while						

			$cmd = " select count(POEM_ID) as COUNT_NEW from PER_POS_EMP where DEPARTMENT_ID = $search_department_id ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$COUNT_NEW = $data2[COUNT_NEW] + 0;
			echo "PER_POS_EMP - $PER_POS_EMP - $COUNT_NEW<br>";

			$cmd = " SELECT POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
							FROM PER_POS_EMP WHERE DEPARTMENT_ID = $DEPT_ID AND $where ORDER BY POEM_ID ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$POEM_NO = $data[POEM_NO];

				$cmd = " SELECT POEM_NO FROM PER_POS_EMP WHERE POEM_NO = $POEM_NO and DEPARTMENT_ID = $search_department_id ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$POEM_NO<br>";
			} // end while						

// พนักงานราชการ
			$cmd = " SELECT POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
							FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $DEPT_ID AND $where ORDER BY POEM_ID ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$PER_POS_EMPSER++;
				$POEMS_ID = $data[POEMS_ID] + 0;
				$ORG_ID = $data[ORG_ID] + 0;
				$POEMS_NO = trim($data[POEMS_NO]);
				$ORG_ID_1 = $data[ORG_ID_1] + 0;
				$ORG_ID_2 = $data[ORG_ID_2] + 0;
				$EP_CODE = trim($data[EP_CODE]);
				$POEM_MIN_SALARY = $data[POEM_MIN_SALARY] + 0;
				$POEM_MAX_SALARY = $data[POEM_MAX_SALARY] + 0;
				$POEM_STATUS = $data[POEM_STATUS] + 0;
				$UPDATE_USER = $data[UPDATE_USER] + 0;
				$UPDATE_DATE = trim($data[UPDATE_DATE]);

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID = $data2[NEW_CODE];

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID_1 = $data2[NEW_CODE];
				else $ORG_ID_1 = 'NULL';

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID_2 = $data2[NEW_CODE];
				else $ORG_ID_2 = 'NULL';

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EMPSER_POS_NAME' AND OLD_CODE = $EP_CODE ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $EP_CODE = trim($data2[NEW_CODE]);

				$cmd = " INSERT INTO PER_POS_EMPSER (POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
								POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
								VALUES ($MAX_ID, $ORG_ID, '$POEMS_NO', $ORG_ID_1, $ORG_ID_2, '$EP_CODE', 
								$POEM_MIN_SALARY, $POEM_MAX_SALARY, $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_POS_EMPSER', '$POEMS_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$MAX_ID++;
			} // end while						

			$cmd = " select count(POEMS_ID) as COUNT_NEW from PER_POS_EMPSER where DEPARTMENT_ID = $search_department_id ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$COUNT_NEW = $data2[COUNT_NEW] + 0;
			echo "PER_POS_EMPSER - $PER_POS_EMPSER - $COUNT_NEW<br>";

			$cmd = " SELECT POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
							FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $DEPT_ID AND $where ORDER BY POEM_ID ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$POEMS_NO = $data[POEMS_NO];

				$cmd = " SELECT POEMS_NO FROM PER_POS_EMPSER WHERE POEMS_NO = $POEMS_NO and DEPARTMENT_ID = $search_department_id ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$POEMS_NO<br>";
			} // end while						
		} // end if

	} // end if

	if( $command=='PERSONAL' ) { // ข้อมูลข้าราชการ
		$table = array(	"per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", "per_coursedtl", "per_decordtl", "per_promote_p", "per_move_req", "per_absent", 
									"per_personal" );
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id AND $where) ";
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT ID, เพศ, คำนำหน้าชื่อ, [ชื่อ-สกุล], รหัสตำแหน่ง, ระดับ, วันเดือนปีเกิด, กรม,
						อบรม, ตำแหน่ง, เงินเดือน, วันบรรจุ, ตำแหน่งประเภท, วันเลื่อนระดับ, วันดำรงตำแหน่งปัจจุบัน, เกษียณอายุ,
						กระทรวง, ชั้นเครื่องราชฯ, วันที่ได้เครื่องราชฯ, หมายเหตุ, ตำแหน่งเลขที่, ระดับวุฒิการศึกษา, ชื่อวุฒิการศึกษา, สาขาวิชาเอก,
						ปีที่จบการศึกษา, ชื่อสถาบันการศึกษา, ประเทศ, ระดับวุฒิการศึกษา1, ชื่อวุฒิการศึกษา1, สาขาวิชาเอก1,
						ปีที่จบการศึกษา1, ชื่อสถาบันการศึกษา1, ประเทศ1, ระดับวุฒิการศึกษา2, ชื่อวุฒิการศึกษา2, สาขาวิชาเอก2,
						ปีที่จบการศึกษา2, ชื่อสถาบันการศึกษา2, ประเทศ2, ความเชี่ยวชาญพิเศษ1, ความเชี่ยวชาญพิเศษ2, ความเชี่ยวชาญพิเศษ3, hisdate,
						hispostline, hislevel, hisdivi, hisdept, วันเลื่อนระดับก่อนระดับปัจจุบัน,
						ตำแหน่งในสายงาน, ตำแหน่งทางบริหาร
						FROM DATASES WHERE DEPARTMENT_ID = $DEPT_ID AND $where ORDER BY ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$PER_ID = $data[ID] + 0;
			$PER_TYPE = 1;
			$OT_CODE = trim($data[OT_CODE]);
			$PN_CODE = trim($data[PN_CODE]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
			$PER_ENG_SURNAME = trim($data[PER_ENG_SURNAME]);
			$ORG_ID = $data[ORG_ID] + 0;
			$POS_ID = $data[POS_ID] + 0;
			$LEVEL_NO = trim($data[ระดับ]);
			$PER_ORGMGT = $data[PER_ORGMGT] + 0;
			$PER_SALARY = $data[เงินเดือน] + 0;
			$PER_MGTSALARY = $data[PER_MGTSALARY] + 0;
			$PER_SPSALARY = $data[PER_SPSALARY] + 0;
			$PER_GENDER = $data[PER_GENDER] + 0;
			$MR_CODE = trim($data[MR_CODE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_OFFNO = trim($data[PER_OFFNO]);
			$PER_TAXNO = trim($data[PER_TAXNO]);
			$PER_BLOOD = trim($data[PER_BLOOD]);
			$RE_CODE = trim($data[RE_CODE]);
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			$PER_POSDATE = trim($data[PER_POSDATE]);
			$PER_SALDATE = trim($data[PER_SALDATE]);
			$PN_CODE_F = trim($data[PN_CODE_F]);
			$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
			$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			$PN_CODE_M = trim($data[PN_CODE_M]);
			$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
			$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			$PER_ADD1 = trim($data[PER_ADD1]);
			$PER_ADD2 = trim($data[PER_ADD2]);
			$PV_CODE = trim($data[PV_CODE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$PER_ORDAIN = $data[PER_ORDAIN] + 0;
			$PER_SOLDIER = $data[PER_SOLDIER] + 0;
			$PER_MEMBER = $data[PER_MEMBER] + 0;
			$PER_STATUS = $data[PER_STATUS] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$APPROVE_PER_ID = $data[APPROVE_PER_ID] + 0;
			$REPLACE_PER_ID = $data[REPLACE_PER_ID] + 0;
			$ABSENT_FLAG = $data[ABSENT_FLAG] + 0;
			$POEMS_ID = $data[POEMS_ID] + 0;
			$PER_HIP_FLAG = trim($data[PER_HIP_FLAG]);
			$PER_CERT_OCC = trim($data[PER_CERT_OCC]);
			$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PRENAME' AND OLD_CODE = $PN_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_ASS' AND OLD_CODE = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE] + 0;

			if ($PER_TYPE==1) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = $POS_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POS_ID = $data2[NEW_CODE] + 0;
			} elseif ($PER_TYPE==2) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMP' AND OLD_CODE = $POEM_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POEM_ID = $data2[NEW_CODE] + 0;
			} elseif ($PER_TYPE==3) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMPSER' AND OLD_CODE = $POEMS_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POEMS_ID = $data2[NEW_CODE] + 0;
			}

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LEVEL' AND OLD_CODE = $LEVEL_NO ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $LEVEL_NO = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MARRIED' AND OLD_CODE = $MR_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MR_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_RELIGION' AND OLD_CODE = $RE_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $RE_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PRENAME' AND OLD_CODE = $PN_CODE_F ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE_F = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PRENAME' AND OLD_CODE = $PN_CODE_M ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE_M = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MOVMENT' AND OLD_CODE = $MOV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MOV_CODE = trim($data2[NEW_CODE]);

			if (!$POS_ID) $POS_ID = "NULL";
			if (!$POEM_ID) $POEM_ID = "NULL";
			if (!$POEMS_ID) $POEMS_ID = "NULL";
			if (!$ORG_ID) $ORG_ID = "NULL";
			if (!$ABSENT_FLAG) $ABSENT_FLAG = "NULL";

			$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, 
							PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, 
							PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
							PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
							PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
							PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, 
							PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
							APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID, PER_HIP_FLAG, PER_CERT_OCC, 
							LEVEL_NO_SALARY)
							VALUES ($MAX_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', '$PER_NAME', '$PER_SURNAME', 
							'$PER_ENG_NAME', '$PER_ENG_SURNAME', $ORG_ID, $POS_ID, $POEM_ID, '$LEVEL_NO', $PER_ORGMGT, 
							$PER_SALARY, $PER_MGTSALARY, $PER_SPSALARY, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', '$PER_RETIREDATE', 
							'$PER_STARTDATE', '$PER_OCCUPYDATE', '$PER_POSDATE', '$PER_SALDATE', '$PN_CODE_F', 
							'$PER_FATHERNAME', '$PER_FATHERSURNAME', '$PN_CODE_M', '$PER_MOTHERNAME', 
							'$PER_MOTHERSURNAME', '$PER_ADD1', '$PER_ADD2', '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, 
							$PER_SOLDIER, $PER_MEMBER, $PER_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id, 
							$APPROVE_PER_ID, $REPLACE_PER_ID, $ABSENT_FLAG, $POEMS_ID, '$PER_HIP_FLAG', 
							'$PER_CERT_OCC', '$LEVEL_NO_SALARY') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$PER_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while				

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";

// ประวัติการดำรงตำแหน่ง  		
		$cmd = " DELETE FROM PER_POSITIONHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id)  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(POH_ID) as MAX_ID from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
						POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, 
						ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, 
						POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE,
						PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3 
						FROM PER_POSITIONHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL 
						WHERE DEPARTMENT_ID = $DEPT_ID AND $where) ORDER BY POH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POSITIONHIS++;
			$POH_ID = $data[POH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$POH_ENDDATE = trim($data[POH_ENDDATE]);
			$POH_DOCNO = trim($data[POH_DOCNO]);
			$POH_DOCDATE = trim($data[POH_DOCDATE]);
			$POH_POS_NO = trim($data[POH_POS_NO]);
			$PM_CODE = trim($data[PM_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PL_CODE = trim($data[PL_CODE]);
			$PN_CODE = trim($data[PN_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$AP_CODE = trim($data[AP_CODE]);
			$POH_ORGMGT = $data[POH_ORGMGT] + 0;
			$ORG_ID_1 = $data[ORG_ID_1] + 0;
			$ORG_ID_2 = $data[ORG_ID_2] + 0;
			$ORG_ID_3 = $data[ORG_ID_3] + 0;
			$POH_UNDER_ORG1 = trim($data[POH_UNDER_ORG1]);
			$POH_UNDER_ORG2 = trim($data[POH_UNDER_ORG2]);
			$POH_ASS_ORG = trim($data[POH_ASS_ORG]);
			$POH_ASS_ORG1 = trim($data[POH_ASS_ORG1]);
			$POH_ASS_ORG2 = trim($data[POH_ASS_ORG2]);
			$POH_SALARY = $data[POH_SALARY] + 0;
			$POH_SALARY_POS = $data[POH_SALARY_POS] + 0;
			$POH_REMARK = trim($data[POH_REMARK]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$EP_CODE = trim($data[EP_CODE]);
			$POH_ORG1 = trim($data[POH_ORG1]);
			$POH_ORG2 = trim($data[POH_ORG2]);
			$POH_ORG3 = trim($data[POH_ORG3]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MOVMENT' AND OLD_CODE = $MOV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MOV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MGT' AND OLD_CODE = $PM_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PM_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LEVEL' AND OLD_CODE = $LEVEL_NO ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $LEVEL_NO = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LINE' AND OLD_CODE = $PL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_NAME' AND OLD_CODE = $PN_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TYPE' AND OLD_CODE = $PT_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PT_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_1 = $data2[NEW_CODE];
			else $ORG_ID_1 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_2 = $data2[NEW_CODE];
			else $ORG_ID_2 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_3 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_3 = $data2[NEW_CODE];
			else $ORG_ID_3 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EMPSER_POS_NAME' AND OLD_CODE = $EP_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $EP_CODE = trim($data2[NEW_CODE]);

			if (!$POH_EFFECTIVEDATE || $POH_ENDDATE) $POH_EFFECTIVEDATE = $POH_ENDDATE;
			if (!$POH_DOCNO || $POH_DOCNO=="'") $POH_DOCNO = "-";
			if (!$POH_DOCDATE || $POH_EFFECTIVEDATE) $POH_DOCDATE = $POH_EFFECTIVEDATE;
			if (!$POH_POS_NO || $POH_POS_NO=="'") $POH_POS_NO = "-";
			if (!$POH_REMARK || $POH_REMARK=="'") $POH_REMARK = "-";

			$cmd = " INSERT INTO PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, 
							POH_DOCDATE, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
							POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, 
							POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3)
							VALUES ($MAX_ID, $PER_ID, '$POH_EFFECTIVEDATE', '$MOV_CODE', '$POH_ENDDATE', '$POH_DOCNO', 
							'$POH_DOCDATE', '$POH_POS_NO', '$PM_CODE', '$LEVEL_NO', '$PL_CODE', '$PN_CODE', '$PT_CODE', '$CT_CODE', 
							'$PV_CODE', '$AP_CODE', $POH_ORGMGT, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$POH_UNDER_ORG1', 
							'$POH_UNDER_ORG2', '$POH_ASS_ORG', '$POH_ASS_ORG1', '$POH_ASS_ORG2', $POH_SALARY, 
							$POH_SALARY_POS, '$POH_REMARK', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$EP_CODE', '$POH_ORG1', 
							'$POH_ORG2', '$POH_ORG3') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						
		
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_POSITIONHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITIONHIS - $PER_POSITIONHIS - $COUNT_NEW<br>";

// ประวัติการรับเงินเดือน  
		$cmd = " DELETE FROM PER_SALARYHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(SAH_ID) as MAX_ID from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, 
						SAH_ENDDATE, UPDATE_USER,	UPDATE_DATE, PER_CARDNO FROM PER_SALARYHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY SAH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SALARYHIS++;
			$SAH_ID = $data[SAH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$SAH_SALARY = $data[SAH_SALARY] + 0;
			$SAH_DOCNO = trim($data[SAH_DOCNO]);
			$SAH_DOCDATE = trim($data[SAH_DOCDATE]);
			$SAH_ENDDATE = trim($data[SAH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MOVMENT' AND OLD_CODE = $MOV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MOV_CODE = trim($data2[NEW_CODE]);

			if (!$SAH_EFFECTIVEDATE || $SAH_ENDDATE) $SAH_EFFECTIVEDATE = $SAH_ENDDATE;
			if (!$SAH_DOCNO || $SAH_DOCNO=="'") $SAH_DOCNO = "-";
			if (!$SAH_DOCDATE || $SAH_EFFECTIVEDATE) $SAH_DOCDATE = $SAH_EFFECTIVEDATE;

			$cmd = " INSERT INTO PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO', '$SAH_DOCDATE', 
							'$SAH_ENDDATE', 	$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						
		
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SALARYHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SALARYHIS - $PER_SALARYHIS - $COUNT_NEW<br>";

// ประวัติการรับเงินเพิ่มพิเศษ  
		$cmd = " DELETE FROM PER_EXTRAHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(EXH_ID) as MAX_ID from PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, UPDATE_USER, UPDATE_DATE,	
						PER_CARDNO FROM PER_EXTRAHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY EXH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EXTRAHIS++;
			$EXH_ID = $data[EXH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EXH_EFFECTIVEDATE = trim($data[EXH_EFFECTIVEDATE]);
			$EX_CODE = trim($data[EX_CODE]);
			$EXH_AMT = $data[EXH_AMT] + 0;
			$EXH_ENDDATE = trim($data[EXH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EXTRATYPE' AND OLD_CODE = $EX_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $EX_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$EXH_EFFECTIVEDATE', '$EX_CODE', $EXH_AMT, '$EXH_ENDDATE', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EXTRAHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EXTRAHIS - $PER_EXTRAHIS - $COUNT_NEW<br>";

// ประวัติการศึกษา  		
		$cmd = " DELETE FROM PER_EDUCATE 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(EDU_ID) as MAX_ID from PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, EDU_FUND, EN_CODE, 
						EM_CODE, INS_CODE,	EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_EDUCATE 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY EDU_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EDUCATE++;
			$EDU_ID = $data[EDU_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EDU_SEQ = trim($data[EDU_SEQ]);
			$EDU_STARTYEAR = trim($data[EDU_STARTYEAR]);
			$EDU_ENDYEAR = trim($data[EDU_ENDYEAR]);
			$ST_CODE = trim($data[ST_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$EDU_FUND = trim($data[EDU_FUND]);
			$EN_CODE = trim($data[EN_CODE]);
			$EM_CODE = trim($data[EM_CODE]);
			$INS_CODE = trim($data[INS_CODE]);
			$EDU_TYPE = trim($data[EDU_TYPE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SCHOLARTYPE' AND OLD_CODE = $ST_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ST_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCNAME' AND OLD_CODE = '$EN_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $EN_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCMAJOR' AND OLD_CODE = '$EM_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $EM_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_INSTITUTE' AND OLD_CODE = '$INS_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $INS_CODE = trim($data2[NEW_CODE]);
			
			if (!$EDU_STARTYEAR || $EDU_STARTYEAR=="'") $EDU_STARTYEAR = "-";
			if (!$EDU_ENDYEAR || $EDU_ENDYEAR=="'") $EDU_ENDYEAR = "-";

			$cmd = " INSERT INTO PER_EDUCATE (EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
							EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$EDU_SEQ', '$EDU_STARTYEAR', '$EDU_ENDYEAR', '$ST_CODE', '$CT_CODE', 
							'$EDU_FUND', '$EN_CODE', '$EM_CODE', '$INS_CODE', '$EDU_TYPE', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EDUCATE 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCATE - $PER_EDUCATE - $COUNT_NEW<br>";

// ประวัติการอบรม/ดูงาน/สัมมนา  
		$cmd = " DELETE FROM PER_TRAINING 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(TRN_ID) as MAX_ID from PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, TRN_PLACE, 
						CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_TRAINING 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY TRN_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_TRAINING++;
			$TRN_ID = $data[TRN_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$TRN_TYPE = $data[TRN_TYPE] + 0;
			$TR_CODE = trim($data[TR_CODE]);
			$TRN_NO = trim($data[TRN_NO]);
			$TRN_STARTDATE = trim($data[TRN_STARTDATE]);
			$TRN_ENDDATE = trim($data[TRN_ENDDATE]);
			$TRN_ORG = trim($data[TRN_ORG]);
			$TRN_PLACE = trim($data[TRN_PLACE]);
			$CT_CODE = trim($data[CT_CODE]);
			$TRN_FUND = trim($data[TRN_FUND]);
			$CT_CODE_FUND = trim($data[CT_CODE_FUND]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$TRN_ENDDATE) $TRN_ENDDATE = $TRN_STARTDATE;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TRAIN' AND OLD_CODE = '$TR_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $TR_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_TRAINING (TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, 
							TRN_ORG, TRN_PLACE, CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, $TRN_TYPE, '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', 
							'$TRN_ORG', '$TRN_PLACE', '$CT_CODE', '$TRN_FUND', '$CT_CODE_FUND', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TRAINING 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TRAINING - $PER_TRAINING - $COUNT_NEW<br>";

// ความสามารถพิเศษ  
		$cmd = " DELETE FROM PER_ABILITY 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(ABI_ID) as MAX_ID from PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT ABI_ID, PER_ID, AL_CODE, ABI_DESC, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_ABILITY 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY ABI_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ABILITY++;
			$ABI_ID = $data[ABI_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$AL_CODE = trim($data[AL_CODE]);
			$ABI_DESC = trim($data[ABI_DESC]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ABILITYGRP' AND OLD_CODE = '$AL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $AL_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_ABILITY (ABI_ID, PER_ID, AL_CODE, ABI_DESC, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$AL_CODE', '$ABI_DESC', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABILITY 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABILITY - $PER_ABILITY - $COUNT_NEW<br>";

// ความเชี่ยวชาญพิเศษ  
		$cmd = " DELETE FROM PER_SPECIAL_SKILL 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(SPS_ID) as MAX_ID from PER_SPECIAL_SKILL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT SPS_ID, PER_ID, SS_CODE, SPS_EMPHASIZE, UPDATE_USER, UPDATE_DATE, PER_CARDNO 
						FROM PER_SPECIAL_SKILL 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY SPS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SPECIAL_SKILL++;
			$SPS_ID = $data[SPS_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$SS_CODE = trim($data[SS_CODE]);
			$SPS_EMPHASIZE = trim($data[SPS_EMPHASIZE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SPECIAL_SKILLGRP' AND OLD_CODE = '$SS_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $SS_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, SS_CODE, SPS_EMPHASIZE, UPDATE_USER, UPDATE_DATE, 
							PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$SS_CODE', '$SPS_EMPHASIZE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SPECIAL_SKILL 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SPECIAL_SKILL - $PER_SPECIAL_SKILL - $COUNT_NEW<br>";

// ทายาท  
		$cmd = " DELETE FROM PER_HEIR 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(HEIR_ID) as MAX_ID from PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT HEIR_ID, PER_ID, HR_CODE, HEIR_NAME, HEIR_STATUS, HEIR_BIRTHDAY, HEIR_TAX, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO FROM PER_HEIR 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY HEIR_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_HEIR++;
			$HEIR_ID = $data[HEIR_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$HR_CODE = trim($data[HR_CODE]);
			$HEIR_NAME = trim($data[HEIR_NAME]);
			$HEIR_STATUS = $data[HEIR_STATUS] + 0;
			$HEIR_BIRTHDAY = trim($data[HEIR_BIRTHDAY]);
			$HEIR_TAX = trim($data[HEIR_TAX]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_HEIRTYPE' AND OLD_CODE = '$HR_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $HR_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_HEIR (HEIR_ID, PER_ID, HR_CODE, HEIR_NAME, HEIR_STATUS, HEIR_BIRTHDAY, HEIR_TAX, 
							UPDATE_USER, UPDATE_DATE, 	PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$HR_CODE', '$HEIR_NAME', $HEIR_STATUS, '$HEIR_BIRTHDAY', '$HEIR_TAX', 
							$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_HEIR 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_HEIR - $PER_HEIR - $COUNT_NEW<br>";

// ประวัติการลา  		
		$cmd = " DELETE FROM PER_ABSENTHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(ABS_ID) as MAX_ID from PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, 
						UPDATE_USER,	UPDATE_DATE, PER_CARDNO FROM PER_ABSENTHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY ABS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ABSENTHIS++;
			$ABS_ID = $data[ABS_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = $data[ABS_STARTPERIOD] + 0;
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = $data[ABS_ENDPERIOD] + 0;
			$ABS_DAY = $data[ABS_DAY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$ABS_STARTPERIOD) $ABS_STARTPERIOD = 3;
			if (!$ABS_ENDPERIOD) $ABS_ENDPERIOD = 3;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ABSENTTYPE' AND OLD_CODE = '$AB_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $AB_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
							ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', 
							$ABS_ENDPERIOD, $ABS_DAY, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";

// ประวัติทางวินัย  
		$cmd = " DELETE FROM PER_PUNISHMENT
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(PUN_ID) as MAX_ID from PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, PUN_STARTDATE, PUN_ENDDATE, CRD_CODE, 
						PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_PUNISHMENT 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY PUN_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PUNISHMENT++;
			$PUN_ID = $data[PUN_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$INV_NO = trim($data[INV_NO]);
			$PUN_NO = trim($data[PUN_NO]);
			$PUN_REF_NO = trim($data[PUN_REF_NO]);
			$PUN_TYPE = $data[PUN_TYPE] + 0;
			$PUN_STARTDATE = trim($data[PUN_STARTDATE]);
			$PUN_ENDDATE = trim($data[PUN_ENDDATE]);
			$CRD_CODE = trim($data[CRD_CODE]);
			$PEN_CODE = trim($data[PEN_CODE]);
			$PUN_PAY = $data[PUN_PAY] + 0;
			$PUN_SALARY = $data[PUN_SALARY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$PUN_STARTDATE) $PUN_STARTDATE = "NULL";
			if (!$PUN_ENDDATE) $PUN_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select CRD_NAME from PER_CRIME_DTL where CRD_CODE = '$CRD_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_CRIME_DTL' AND OLD_CODE = '$CRD_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$CRD_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " select PEN_NAME from PER_PENALTY where PEN_CODE = '$PEN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PENALTY' AND OLD_CODE = '$PEN_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PEN_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " INSERT INTO PER_PUNISHMENT (PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, PUN_STARTDATE, 
							PUN_ENDDATE, CRD_CODE, PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$INV_NO', '$PUN_NO', '$PUN_REF_NO', $PUN_TYPE, '$PUN_STARTDATE', '$PUN_ENDDATE', 
							'$CRD_CODE', '$PEN_CODE', 	$PUN_PAY, $PUN_SALARY, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PUNISHMENT 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PUNISHMENT - $PER_PUNISHMENT - $COUNT_NEW<br>";

// ประวัติราชการพิเศษ  
		$cmd = " DELETE FROM PER_SERVICEHIS
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(SRH_ID) as MAX_ID from PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, SRH_DOCNO, 
						PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_SERVICEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY SRH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SERVICEHIS++;
			$SRH_ID = $data[SRH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$SV_CODE = trim($data[SV_CODE]);
			$SRT_CODE = trim($data[SRT_CODE]);
			$ORG_ID = $data[ORG_ID] + 0;
			$SRH_STARTDATE = trim($data[SRH_STARTDATE]);
			$SRH_ENDDATE = trim($data[SRH_ENDDATE]);
			$SRH_NOTE = trim($data[SRH_NOTE]);
			$SRH_DOCNO = trim($data[SRH_DOCNO]);
			$PER_ID_ASSIGN = $data[PER_ID_ASSIGN] + 0;
			$ORG_ID_ASSIGN = $data[ORG_ID_ASSIGN] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$SRH_STARTDATE) $SRH_STARTDATE = "-";
			if (!$SRH_ENDDATE) $SRH_ENDDATE = $SRH_STARTDATE;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID_ASSIGN ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID_ASSIGN = $data2[NEW_CODE] + 0;
			if ($PER_ID_ASSIGN==0) $PER_ID_ASSIGN = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE] + 0;
			if ($ORG_ID==0) $ORG_ID = $search_department_id;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = $ORG_ID_ASSIGN ";
			$db_dpis2->send_cmd($cmd);
			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_ASSIGN = $data2[NEW_CODE] + 0;
			if ($ORG_ID_ASSIGN==0) $ORG_ID_ASSIGN = "NULL";

			$cmd = " select SV_NAME from PER_SERVICE where SV_CODE = '$SV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SERVICE' AND OLD_CODE = '$SV_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$SV_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " select SRT_NAME from PER_SERVICETITLE where SRT_CODE = '$SRT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SERVICETITLE' AND OLD_CODE = '$SRT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$SRT_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " INSERT INTO PER_SERVICEHIS (SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, 
							SRH_NOTE, SRH_DOCNO, PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$SV_CODE', '$SRT_CODE', $ORG_ID, '$SRH_STARTDATE', '$SRH_ENDDATE', 
							'$SRH_NOTE', '$SRH_DOCNO', 	$PER_ID_ASSIGN, $ORG_ID_ASSIGN, $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SERVICEHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SERVICEHIS - $PER_SERVICEHIS - $COUNT_NEW<br>";

// ประวัติความดีความชอบ  
		$cmd = " DELETE FROM PER_REWARDHIS
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(REH_ID) as MAX_ID from PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, UPDATE_DATE, 
						PER_CARDNO FROM PER_REWARDHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY REH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_REWARDHIS++;
			$REH_ID = $data[REH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$REW_CODE = trim($data[REW_CODE]);
			$REH_ORG = trim($data[REH_ORG]);
			$REH_DOCNO = trim($data[REH_DOCNO]);
			$REH_DATE = trim($data[REH_DATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select REW_NAME from PER_REWARD where REW_CODE = '$REW_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_REWARD' AND OLD_CODE = '$REW_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$REW_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " INSERT INTO PER_REWARDHIS (REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$REW_CODE', '$REH_ORG', '$REH_DOCNO', '$REH_DATE', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_REWARDHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_REWARDHIS - $PER_REWARDHIS - $COUNT_NEW<br>";

// ประวัติการสมรส   
		$cmd = " DELETE FROM PER_MARRHIS
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(MAH_ID) as MAX_ID from PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO FROM PER_MARRHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY MAH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_MARRHIS++;
			$MAH_ID = $data[MAH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$MAH_SEQ = $data[MAH_SEQ] + 0;
			$MAH_NAME = trim($data[MAH_NAME]);
			$MAH_MARRY_DATE = trim($data[MAH_MARRY_DATE]);
			$MAH_DIVORCE_DATE = trim($data[MAH_DIVORCE_DATE]);
			$DV_CODE = trim($data[DV_CODE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$MAH_NAME) $MAH_NAME = "NULL";
//			if (!$MAH_MARRY_DATE) $MAH_MARRY_DATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select DV_NAME from PER_DIVORCE where DV_CODE = '$DV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_DIVORCE' AND OLD_CODE = '$DV_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$DV_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " INSERT INTO PER_MARRHIS (MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, 
							DV_CODE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, $MAH_SEQ, '$MAH_NAME', '$MAH_MARRY_DATE', '$MAH_DIVORCE_DATE', '$DV_CODE', 
							$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_MARRHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_MARRHIS - $PER_MARRHIS - $COUNT_NEW<br>";

// ประวัติการเปลี่ยนชื่อ-สกุล  
		$cmd = " DELETE FROM PER_NAMEHIS
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(NH_ID) as MAX_ID from PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, UPDATE_USER, UPDATE_DATE, 
						PER_CARDNO FROM PER_NAMEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY NH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_NAMEHIS++;
			$NH_ID = $data[NH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$NH_DATE = trim($data[NH_DATE]);
			$PN_CODE = trim($data[PN_CODE]);
			$NH_NAME = trim($data[NH_NAME]);
			$NH_SURNAME = trim($data[NH_SURNAME]);
			$NH_DOCNO = trim($data[NH_DOCNO]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE = '$PN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PRENAME' AND OLD_CODE = '$PN_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PN_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " INSERT INTO PER_NAMEHIS (NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_NAMEHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_NAMEHIS - $PER_NAMEHIS - $COUNT_NEW<br>";

// ประวัติรับพระราชทานเครื่องราชฯ  
		$cmd = " DELETE FROM PER_DECORATEHIS
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(DEH_ID) as MAX_ID from PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT DEH_ID, PER_ID, DC_CODE, DEH_DATE, DEH_GAZETTE, UPDATE_USER, UPDATE_DATE, PER_CARDNO 
						FROM PER_DECORATEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY DEH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_DECORATEHIS++;
			$DEH_ID = $data[DEH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$DC_CODE = trim($data[DC_CODE]);
			$DEH_DATE = trim($data[DEH_DATE]);
			$DEH_GAZETTE = trim($data[DEH_GAZETTE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select DC_NAME from PER_DECORATION where DC_CODE = '$DC_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_DECORATION' AND OLD_CODE = '$DC_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$DC_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " INSERT INTO PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, DEH_GAZETTE, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$DEH_GAZETTE', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_DECORATEHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_DECORATEHIS - $PER_DECORATEHIS - $COUNT_NEW<br>";

// เวลาทวีคูณ  
		$cmd = " DELETE FROM PER_TIMEHIS
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(TIMEH_ID) as MAX_ID from PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, UPDATE_USER, UPDATE_DATE, PER_CARDNO 
						FROM PER_TIMEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY TIMEH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_TIMEHIS++;
			$TIMEH_ID = $data[TIMEH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$TIME_CODE = trim($data[TIME_CODE]);
			$TIMEH_MINUS = $data[TIMEH_MINUS] + 0;
			$TIMEH_REMARK = trim($data[TIMEH_REMARK]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select TIME_NAME from PER_TIME where TIME_CODE = '$TIME_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TIME' AND OLD_CODE = '$TIME_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$TIME_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " INSERT INTO PER_TIMEHIS (TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$TIME_CODE', $TIMEH_MINUS, '$TIMEH_REMARK', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TIMEHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TIMEHIS - $PER_TIMEHIS - $COUNT_NEW<br>";

// ประวัติการรับเงินพิเศษ  		
		$cmd = " DELETE FROM PER_EXTRA_INCOMEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(EXINH_ID) as MAX_ID from PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT EXINH_ID, PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, EXINH_ENDDATE, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO FROM PER_EXTRA_INCOMEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID AND $where) 
						ORDER BY EXINH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EXTRA_INCOMEHIS++;
			$EXINH_ID = $data[EXINH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EXINH_EFFECTIVEDATE = trim($data[EXINH_EFFECTIVEDATE]);
			$EXIN_CODE = trim($data[EXIN_CODE]);
			$EXINH_AMT = $data[EXINH_AMT] + 0;
			$EXINH_ENDDATE = trim($data[EXINH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select EXIN_NAME from PER_EXTRA_INCOME_TYPE where EXIN_CODE = '$EXIN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EXTRA_INCOME_TYPE' AND OLD_CODE = 
								'$EXIN_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$EXIN_CODE = trim($data2[NEW_CODE]);
			}

			$cmd = " INSERT INTO PER_EXTRA_INCOMEHIS (EXINH_ID, PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, 
							EXINH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$EXINH_EFFECTIVEDATE', '$EXIN_CODE', $EXINH_AMT, '$EXINH_ENDDATE', 
							$UPDATE_USER, '$UPDATE_DATE',	'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						 

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EXTRA_INCOMEHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EXTRA_INCOMEHIS - $PER_EXTRA_INCOMEHIS - $COUNT_NEW<br>";

// select * from per_educate where per_id in (select per_id from per_personal where department_id = 3134) order by edu_id
	} // end if

?>