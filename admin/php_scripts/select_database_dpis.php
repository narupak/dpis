<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
//  	if(strtoupper($dpisdb_user)=="KP" || strtoupper($dpisdb_user)=="EXCISE") 
  	if(strtoupper($dpisdb_user)=="HIPPS") 
		$where = "AND (PER_HIP_FLAG like '%1%')";
  	elseif(strtoupper($dpisdb_user)=="HISDB" || strtoupper($dpisdb_user)=="SES" || strtoupper($dpisdb_user)=="ISCS") 
		$where = "AND (LEVEL_NO IN ('09','10','11','O4','K4','K5','D1','D2','M1','M2'))";
  	elseif(strtoupper($dpisdb_user)=="D07000" || $search_pv_code) 
		$where = "";

	if ($search_org_id) {
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b WHERE a.POS_ID = b.POS_ID and 
											b.DEPARTMENT_ID = $search_department_id and b.ORG_ID = $search_org_id) ";
		$where_org_id = " AND (ORG_ID = $search_org_id) ";
	} else {
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$where_org_id = "";
	}

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

	if ($search_pv_code) {
		$cmd = " SELECT PV_CODE FROM PER_PROVINCE	WHERE PV_NAME = '$search_pv_name' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$search_pv_code = trim($data[PV_CODE]);
	}
		
	$cmd = " SELECT ORG_ID, ORG_CODE FROM PER_ORG  
					WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' AND ORG_CODE = '$search_department_code' ";
	$db_dpis35->send_cmd($cmd);
	//$db_dpis35->show_error();
	$data = $db_dpis35->get_array();
	$DEPT_ID = $data[ORG_ID] + 0;
	$ORG_CODE = trim($data[ORG_CODE]);
	if ($DEPT_ID==0) $DEPT_ID = $search_department_id;
	
	$cmd = " SELECT ORG_ID FROM PER_CONTROL ";
	$db_dpis35->send_cmd($cmd);
	//$db_dpis35->show_error();
	$data = $db_dpis35->get_array();
	$DEPT_ID = $data[ORG_ID] + 0;
	if ($DEPT_ID==0) $DEPT_ID = $search_department_id;
	
	// กำหนดค่าเริ่มต้นของ PER_ID ==================
	//$DPIS35DB	 = ถ่ายโอนจาก
	//$DPISDB      = ชี้ไปที่ เพื่อนำข้อมูลเข้า
	$where_per_id="";
	//--สำหรับ mysql
	if($DPISDB=="mysql"){	
		$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id $where ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis35->get_array()){
			$where_per_id .= $data[PER_ID].",";
		}
		echo $where_per_id = "(".substr($where_per_id,0,-1).")";
	}else{	//--สำหรับอื่นๆ
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id $where) ";
	}
	//===================================

	if( $command=='MASTER' ) {  // ข้อมูลหลัก 
// คำนำหน้าชื่อ  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PRENAME' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT PN_CODE, PN_SHORTNAME, PN_NAME, PN_ENG_NAME, PN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_PRENAME ORDER BY PN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PN_CODE = trim($data[PN_CODE]);
			$PN_SHORTNAME = trim($data[PN_SHORTNAME]);
			$PN_NAME = trim($data[PN_NAME]);
			$PN_ENG_NAME = trim($data[PN_ENG_NAME]);
			$PN_ACTIVE = $data[PN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE = '$PN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[PN_NAME]);
				if ($NEW_NAME != $PN_NAME) {
					$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' or instr(PN_OTHERNAME, '||$PN_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PN_CODE]);
 						if ($NEW_CODE != $PN_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_PRENAME', '$PN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "คำนำหน้าชื่อ $PN_CODE - $NEW_CODE : $PN_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' or instr(PN_OTHERNAME, '||$PN_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PN_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_PRENAME', '$PN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "<br>";
				} else {
					$cmd = " INSERT INTO PER_PRENAME (PN_CODE, PN_SHORTNAME, PN_NAME, PN_ENG_NAME, PN_ACTIVE, 
									UPDATE_USER, UPDATE_DATE)
									VALUES ('$PN_CODE', '$PN_SHORTNAME', '$PN_NAME', '$PN_ENG_NAME', $PN_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ศาสนา
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_RELIGION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

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
 			$NEW_CODE = "";

			$cmd = " select RE_NAME from PER_RELIGION where RE_CODE = '$RE_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[RE_NAME]);
				if ($NEW_NAME != $RE_NAME) {
					$cmd = " select RE_CODE from PER_RELIGION where RE_NAME = '$RE_NAME' or instr(RE_OTHERNAME, '||$RE_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[RE_CODE]);
 						if ($NEW_CODE != $RE_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_RELIGION', '$RE_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ศาสนา $RE_CODE - $NEW_CODE : $RE_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select RE_CODE from PER_RELIGION where RE_NAME = '$RE_NAME' or instr(RE_OTHERNAME, '||$RE_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[RE_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_RELIGION', '$RE_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "<br>";
				} else {
					$cmd = " INSERT INTO PER_RELIGION (RE_CODE, RE_NAME, RE_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$RE_CODE', '$RE_NAME', $RE_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while				
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		
// ประเทศ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_COUNTRY' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(CT_CODE) as MAX_CODE from PER_COUNTRY WHERE SUBSTR(CT_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

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
			$NEW_CODE = "";

			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE = '$CT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[CT_NAME]);
				if ($NEW_NAME != $CT_NAME) {
					$cmd = " select CT_CODE from PER_COUNTRY where CT_NAME = '$CT_NAME' or instr(CT_OTHERNAME, '||$CT_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[CT_CODE]);
 						if ($NEW_CODE != $CT_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_COUNTRY', '$CT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเทศ $CT_CODE - $NEW_CODE : $CT_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_COUNTRY (CT_CODE, CT_NAME, CT_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$CT_NAME', $CT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_COUNTRY', '$CT_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select CT_CODE from PER_COUNTRY where CT_NAME = '$CT_NAME' or instr(CT_OTHERNAME, '||$CT_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[CT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_COUNTRY', '$CT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_COUNTRY (CT_CODE, CT_NAME, CT_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$CT_CODE', '$CT_NAME', $CT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		
//		$cmd = " select count(CT_CODE) as COUNT_NEW from PER_COUNTRY ";
//		$db_dpis->send_cmd($cmd);
//		$data = $db_dpis->get_array();
//		$COUNT_NEW = $data[COUNT_NEW] + 0;
//		echo "PER_COUNTRY - $PER_COUNTRY - $COUNT_NEW<br>";

// จังหวัด
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PROVINCE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(PV_CODE) as MAX_CODE from PER_PROVINCE WHERE SUBSTR(PV_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_PROVINCE ORDER BY PV_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PV_CODE = trim($data[PV_CODE]);
			$PV_NAME = trim($data[PV_NAME]);
			$CT_CODE = trim($data[CT_CODE]);
			$PV_ACTIVE = $data[PV_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = '$PV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[PV_NAME]);
				if ($NEW_NAME != $PV_NAME) {
					$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME = '$PV_NAME' or instr(PV_OTHERNAME, '||$PV_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PV_CODE]);
 						if ($NEW_CODE != $PV_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_PROVINCE', '$PV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "จังหวัด $PV_CODE - $NEW_CODE : $PV_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_PROVINCE (PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$PV_NAME', '$CT_CODE', $PV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_PROVINCE', '$PV_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME = '$PV_NAME' or instr(PV_OTHERNAME, '||$PV_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PV_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_PROVINCE', '$PV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_PROVINCE (PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$PV_CODE', '$PV_NAME', '$CT_CODE', $PV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// อำเภอ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_AMPHUR' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(AP_CODE) as MAX_CODE from PER_AMPHUR WHERE SUBSTR(AP_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT AP_CODE, AP_NAME, PV_CODE, AP_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_AMPHUR ORDER BY AP_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$AP_CODE = trim($data[AP_CODE]);
			$AP_NAME = trim($data[AP_NAME]);
			$PV_CODE = trim($data[PV_CODE]);
			$AP_ACTIVE = $data[AP_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PROVINCE' AND OLD_CODE = $PV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE = '$AP_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[AP_NAME]);
				if ($NEW_NAME != $AP_NAME) {
					$cmd = " select AP_CODE from PER_AMPHUR where AP_NAME = '$AP_NAME' or instr(AP_OTHERNAME, '||$AP_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[AP_CODE]);
 						if ($NEW_CODE != $AP_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_AMPHUR', '$AP_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "อำเภอ $AP_CODE - $NEW_CODE : $AP_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_AMPHUR (AP_CODE, AP_NAME, PV_CODE, AP_ACTIVE, UPDATE_USER, 	
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$AP_NAME', '$PV_CODE', $AP_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_AMPHUR', '$AP_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select AP_CODE from PER_AMPHUR where AP_NAME = '$AP_NAME' or instr(AP_OTHERNAME, '||$AP_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[AP_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_AMPHUR', '$AP_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_AMPHUR (AP_CODE, AP_NAME, PV_CODE, AP_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$AP_CODE', '$AP_NAME', '$PV_CODE', $AP_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// สังกัด  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_TYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(OT_CODE) as MAX_CODE from PER_ORG_TYPE WHERE SUBSTR(OT_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ORG_TYPE ORDER BY OT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$OT_CODE = trim($data[OT_CODE]);
			$OT_NAME = trim($data[OT_NAME]);
			$OT_ACTIVE = $data[OT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE = '$OT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[OT_NAME]);
				if ($NEW_NAME != $OT_NAME) {
					$cmd = " select OT_CODE from PER_ORG_TYPE where OT_NAME = '$OT_NAME' or instr(OT_OTHERNAME, '||$OT_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[OT_CODE]);
 						if ($NEW_CODE != $OT_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_ORG_TYPE', '$OT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "สังกัด $OT_CODE - $NEW_CODE : $OT_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$OT_NAME', $OT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_ORG_TYPE', '$OT_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select OT_CODE from PER_ORG_TYPE where OT_NAME = '$OT_NAME' or instr(OT_OTHERNAME, '||$OT_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[OT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ORG_TYPE', '$OT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$OT_CODE', '$OT_NAME', $OT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ชื่อตำแหน่งในสายงาน  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_LINE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(PL_CODE) as MAX_CODE from PER_LINE WHERE SUBSTR(PL_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, 
						PL_CODE_NEW, LG_CODE, CL_NAME, PL_CODE_DIRECT 
						FROM PER_LINE ORDER BY PL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
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
			$NEW_CODE = "";

			$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$PL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[PL_NAME]);
				if ($NEW_NAME != $PL_NAME) {
					$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' or instr(PL_OTHERNAME, '||$PL_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PL_CODE]);
 						if ($NEW_CODE != $PL_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_LINE', '$PL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ชื่อตำแหน่งในสายงาน $PL_CODE - $NEW_CODE : $PL_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, 
										UPDATE_DATE, PL_TYPE, PL_CODE_NEW, LG_CODE, CL_NAME, PL_CODE_DIRECT)
										VALUES ('$MAX_CODE', '$PL_NAME', '$PL_SHORTNAME', $PL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
										$PL_TYPE, '$PL_CODE_NEW', '$LG_CODE', '$CL_NAME', '$PL_CODE_DIRECT') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_LINE', '$PL_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' or instr(PL_OTHERNAME, '||$PL_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_LINE', '$PL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, 
									UPDATE_DATE, PL_TYPE, PL_CODE_NEW, LG_CODE, CL_NAME, PL_CODE_DIRECT)
									VALUES ('$PL_CODE', '$PL_NAME', '$PL_SHORTNAME', $PL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
									$PL_TYPE, '$PL_CODE_NEW', '$LG_CODE', '$CL_NAME', '$PL_CODE_DIRECT') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภทการลา  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ABSENTTYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(AB_CODE) as MAX_CODE from PER_ABSENTTYPE WHERE SUBSTR(AB_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ABSENTTYPE ORDER BY AB_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$AB_CODE = trim($data[AB_CODE]);
			$AB_NAME = trim($data[AB_NAME]);
			$AB_QUOTA = $data[AB_QUOTA] + 0;
			$AB_COUNT = $data[AB_COUNT] + 0;
			$AB_ACTIVE = $data[AB_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select AB_NAME from PER_ABSENTTYPE where AB_CODE = '$AB_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[AB_NAME]);
				if ($NEW_NAME != $AB_NAME) {
					$cmd = " select AB_CODE from PER_ABSENTTYPE where AB_NAME = '$AB_NAME' or instr(AB_OTHERNAME, '||$AB_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[AB_CODE]);
 						if ($NEW_CODE != $AB_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_ABSENTTYPE', '$AB_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทการลา $AB_CODE - $NEW_CODE : $AB_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, 
										UPDATE_USER, 	UPDATE_DATE)
										VALUES ('$MAX_CODE', '$AB_NAME', $AB_QUOTA, $AB_COUNT, $AB_ACTIVE, $UPDATE_USER, 
										'$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_ABSENTTYPE', '$AB_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select AB_CODE from PER_ABSENTTYPE where AB_NAME = '$AB_NAME' or instr(AB_OTHERNAME, '||$AB_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[AB_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ABSENTTYPE', '$AB_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, 
									UPDATE_USER, 	UPDATE_DATE)
									VALUES ('$AB_CODE', '$AB_NAME', $AB_QUOTA, $AB_COUNT, $AB_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ฐานะของตำแหน่ง
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_STATUS' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(PS_CODE) as MAX_CODE from PER_STATUS WHERE SUBSTR(PS_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

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
			$NEW_CODE = "";

			$cmd = " select PS_NAME from PER_STATUS where PS_CODE = '$PS_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[PS_NAME]);
				if ($NEW_NAME != $PS_NAME) {
					$cmd = " select PS_CODE from PER_STATUS where PS_NAME = '$PS_NAME' or instr(PS_OTHERNAME, '||$PS_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PS_CODE]);
 						if ($NEW_CODE != $PS_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_STATUS', '$PS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ฐานะของตำแหน่ง $PS_CODE - $NEW_CODE : $PS_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$PS_NAME', $PS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_STATUS', '$PS_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select PS_CODE from PER_STATUS where PS_NAME = '$PS_NAME' or instr(PS_OTHERNAME, '||$PS_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PS_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_STATUS', '$PS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$PS_CODE', '$PS_NAME', $PS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ชื่อตำแหน่งในการบริหารงาน  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MGT' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(PM_CODE) as MAX_CODE from PER_MGT WHERE SUBSTR(PM_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MGT ORDER BY PM_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PM_CODE = trim($data[PM_CODE]);
			$PM_NAME = trim($data[PM_NAME]);
			$PM_SHORTNAME = trim($data[PM_SHORTNAME]);
			$PS_CODE = trim($data[PS_CODE]);
			$PM_ACTIVE = $data[PM_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_STATUS' AND OLD_CODE = $PS_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PS_CODE = trim($data2[NEW_CODE]);

			$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$PM_CODE' or instr(PM_OTHERNAME, '||$PM_NAME||') > 0 ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[PM_NAME]);
				if ($NEW_NAME != $PM_NAME) {
					$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PM_CODE]);
 						if ($NEW_CODE != $PM_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_MGT', '$PM_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ชื่อตำแหน่งในการบริหารงาน $PM_CODE - $NEW_CODE : $PM_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_MGT (PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$PM_NAME', '$PM_SHORTNAME', '$PS_CODE', $PM_ACTIVE, $UPDATE_USER, 
										'$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_MGT', '$PM_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' or instr(PM_OTHERNAME, '||$PM_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PM_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_MGT', '$PM_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_MGT (PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$PM_CODE', '$PM_NAME', '$PM_SHORTNAME', '$PS_CODE', $PM_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภทตำแหน่ง 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(PT_CODE) as MAX_CODE from PER_TYPE WHERE SUBSTR(PT_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TYPE ORDER BY PT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PT_CODE = trim($data[PT_CODE]);
			$PT_NAME = trim($data[PT_NAME]);
			$PT_GROUP = $data[PT_GROUP] + 0;
			$PT_ACTIVE = $data[PT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select PT_NAME from PER_TYPE where PT_CODE = '$PT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[PT_NAME]);
				if ($NEW_NAME != $PT_NAME) {
					$cmd = " select PT_CODE from PER_TYPE where PT_NAME = '$PT_NAME' or instr(PT_OTHERNAME, '||$PT_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PT_CODE]);
 						if ($NEW_CODE != $PT_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_TYPE', '$PT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทตำแหน่ง $PT_CODE - $NEW_CODE : $PT_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_TYPE (PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$PT_NAME', $PT_GROUP, $PT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_TYPE', '$PT_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select PT_CODE from PER_TYPE where PT_NAME = '$PT_NAME' or instr(PT_OTHERNAME, '||$PT_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_TYPE', '$PT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_TYPE (PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$PT_CODE', '$PT_NAME', $PT_GROUP, $PT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ระดับการศึกษา
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EDUCLEVEL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(EL_CODE) as MAX_CODE from PER_EDUCLEVEL WHERE SUBSTR(EL_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

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
			$NEW_CODE = "";

			$cmd = " select EL_NAME from PER_EDUCLEVEL where EL_CODE = '$EL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[EL_NAME]);
				if ($NEW_NAME != $EL_NAME) {
					$cmd = " select EL_CODE from PER_EDUCLEVEL where EL_NAME = '$EL_NAME' or instr(EL_OTHERNAME, '||$EL_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[EL_CODE]);
 						if ($NEW_CODE != $EL_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_EDUCLEVEL', '$EL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ระดับการศึกษา $EL_CODE - $NEW_CODE : $EL_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_EDUCLEVEL (EL_CODE, EL_NAME, EL_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$EL_NAME', $EL_ACTIVE, $UPDATE_USER, 	'$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_EDUCLEVEL', '$EL_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select EL_CODE from PER_EDUCLEVEL where EL_NAME = '$EL_NAME' or instr(EL_OTHERNAME, '||$EL_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EDUCLEVEL', '$EL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EDUCLEVEL (EL_CODE, EL_NAME, EL_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$EL_CODE', '$EL_NAME', $EL_ACTIVE, $UPDATE_USER, 	'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// วุฒิการศึกษา  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EDUCNAME' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(EN_CODE) as MAX_CODE from PER_EDUCNAME WHERE SUBSTR(EN_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EDUCNAME ORDER BY EN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
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
			$NEW_CODE = "";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCLEVEL' AND OLD_CODE = $EL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $EL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select EN_NAME from PER_EDUCNAME where EN_CODE = '$EN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[EN_NAME]);
				$NEW_NAME=str_replace("'","''",$NEW_NAME);
				$NEW_NAME=str_replace("\"","&quot;",$NEW_NAME);
				if ($NEW_NAME != $EN_NAME) {
					$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME = '$EN_NAME' or instr(EN_OTHERNAME, '||$EN_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[EN_CODE]);
 						if ($NEW_CODE != $EN_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_EDUCNAME', '$EN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "วุฒิการศึกษา $EN_CODE - $NEW_CODE : $EN_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_EDUCNAME (EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, 
										UPDATE_USER, 	UPDATE_DATE)
										VALUES ('$MAX_CODE', '$EL_CODE', '$EN_SHORTNAME', '$EN_NAME', $EN_ACTIVE, $UPDATE_USER, 
										'$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_EDUCNAME', '$EN_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME = '$EN_NAME' or instr(EN_OTHERNAME, '||$EN_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EN_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EDUCNAME', '$EN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EDUCNAME (EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, 
									UPDATE_USER, 	UPDATE_DATE)
									VALUES ('$EN_CODE', '$EL_CODE', '$EN_SHORTNAME', '$EN_NAME', $EN_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// สาขาวิชาเอก  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EDUCMAJOR' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(EM_CODE) as MAX_CODE from PER_EDUCMAJOR WHERE SUBSTR(EM_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT EM_CODE, EM_NAME, EM_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EDUCMAJOR ORDER BY EM_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EM_CODE = trim($data[EM_CODE]);
			$EM_NAME = trim($data[EM_NAME]);
			$EM_ACTIVE = $data[EM_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select EM_NAME from PER_EDUCMAJOR where EM_CODE = '$EM_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[EM_NAME]);
				if ($NEW_NAME != $EM_NAME) {
					$cmd = " select EM_CODE from PER_EDUCMAJOR where EM_NAME = '$EM_NAME' or instr(EM_OTHERNAME, '||$EM_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[EM_CODE]);
 						if ($NEW_CODE != $EM_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_EDUCMAJOR', '$EM_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "สาขาวิชาเอก $EM_CODE - $NEW_CODE : $EM_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_EDUCMAJOR (EM_CODE, EM_NAME, EM_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$EM_NAME', $EM_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_EDUCMAJOR', '$EM_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select EM_CODE from PER_EDUCMAJOR where EM_NAME = '$EM_NAME' or instr(EM_OTHERNAME, '||$EM_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EM_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EDUCMAJOR', '$EM_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EDUCMAJOR (EM_CODE, EM_NAME, EM_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$EM_CODE', '$EM_NAME', $EM_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// หลักสูตรการฝึกอบรม/ดูงาน  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TRAIN' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(TR_CODE) as MAX_CODE from PER_TRAIN WHERE SUBSTR(TR_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TRAIN ORDER BY TR_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TR_CODE = trim($data[TR_CODE]);
			$TR_TYPE = $data[TR_TYPE] + 0;
			$TR_NAME = trim($data[TR_NAME]);
			$TR_NAME=str_replace("'","''",$TR_NAME);
			$TR_NAME=str_replace("\"","&quot;",$TR_NAME);
			$TR_ACTIVE = $data[TR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select TR_NAME from PER_TRAIN where TR_CODE = '$TR_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[TR_NAME]);
				$NEW_NAME=str_replace("'","''",$NEW_NAME);
				$NEW_NAME=str_replace("\"","&quot;",$NEW_NAME);

				if ($NEW_NAME != $TR_NAME) {
					$cmd = " select TR_CODE from PER_TRAIN where TR_NAME = '$TR_NAME' or instr(TR_OTHERNAME, '||$TR_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[TR_CODE]);
 						if ($NEW_CODE != $TR_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_TRAIN', '$TR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "หลักสูตรการฝึกอบรม/ดูงาน $NEW_CODE $TR_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_TRAIN (TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', $TR_TYPE, '$TR_NAME', $TR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_TRAIN', '$TR_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select TR_CODE from PER_TRAIN where TR_NAME = '$TR_NAME' or instr(TR_OTHERNAME, '||$TR_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[TR_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_TRAIN', '$TR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_TRAIN (TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$TR_CODE', $TR_TYPE, '$TR_NAME', $TR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภทการเคลื่อนไหว  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MOVMENT' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(MOV_CODE) as MAX_CODE from PER_MOVMENT WHERE SUBSTR(MOV_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MOVMENT ORDER BY MOV_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$MOV_CODE = trim($data[MOV_CODE]);
			$MOV_NAME = trim($data[MOV_NAME]);
			$MOV_TYPE = $data[MOV_TYPE] + 0;
			$MOV_ACTIVE = $data[MR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[MOV_NAME]);
				if ($NEW_NAME != $MOV_NAME) {
					$cmd = " select MOV_CODE from PER_MOVMENT where MOV_NAME = '$MOV_NAME' or instr(MOV_OTHERNAME, '||$MOV_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[MOV_CODE]);
 						if ($NEW_CODE != $MOV_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_MOVMENT', '$MOV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทการเคลื่อนไหว $MOV_CODE - $NEW_CODE : $MOV_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
									VALUES ('$MAX_CODE', '$MOV_NAME', $MOV_TYPE, $MOV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_MOVMENT', '$MOV_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select MOV_CODE from PER_MOVMENT where MOV_NAME = '$MOV_NAME' or instr(MOV_OTHERNAME, '||$MOV_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[MOV_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_MOVMENT', '$MOV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$MOV_CODE', '$MOV_NAME', $MOV_TYPE, $MOV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ฐานความผิด  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CRIME' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(CR_CODE) as MAX_CODE from PER_CRIME WHERE SUBSTR(CR_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT CR_CODE, CR_NAME, CR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CRIME ORDER BY CR_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$CR_CODE = trim($data[CR_CODE]);
			$CR_NAME = trim($data[CR_NAME]);
			$CR_ACTIVE = $data[CR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select CR_NAME from PER_CRIME where CR_CODE = '$CR_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[CR_NAME]);
				if ($NEW_NAME != $CR_NAME) {
					$cmd = " select CR_CODE from PER_CRIME where CR_NAME = '$CR_NAME' or instr(CR_OTHERNAME, '||$CR_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[CR_CODE]);
 						if ($NEW_CODE != $CR_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_CRIME', '$CR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ฐานความผิด $CR_CODE - $NEW_CODE : $CR_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_CRIME (CR_CODE, CR_NAME, CR_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$CR_NAME', $CR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_CRIME', '$CR_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select CR_CODE from PER_CRIME where CR_NAME = '$CR_NAME' or instr(CR_OTHERNAME, '||$CR_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[CR_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_CRIME', '$CR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_CRIME (CR_CODE, CR_NAME, CR_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$CR_CODE', '$CR_NAME', $CR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// กรณีความผิด  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CRIME_DTL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(CRD_CODE) as MAX_CODE from PER_CRIME_DTL WHERE SUBSTR(CRD_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT CRD_CODE, CRD_NAME, CR_CODE, CRD_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CRIME_DTL ORDER BY CRD_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$CRD_CODE = trim($data[CRD_CODE]);
			$CRD_NAME = trim($data[CRD_NAME]);
			$CR_CODE = trim($data[CR_CODE]);
			$CRD_ACTIVE = $data[CRD_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_CRIME' AND OLD_CODE = $CR_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $CR_CODE = trim($data2[NEW_CODE]);

			$cmd = " select CRD_NAME from PER_CRIME_DTL where CRD_CODE = '$CRD_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[CRD_NAME]);
				if ($NEW_NAME != $CRD_NAME) {
					$cmd = " select CRD_CODE from PER_CRIME_DTL where CRD_NAME = '$CRD_NAME' or instr(CRD_OTHERNAME, '||$CRD_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[CRD_CODE]);
 						if ($NEW_CODE != $CRD_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_CRIME_DTL', '$CRD_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "กรณีความผิด $CRD_CODE - $NEW_CODE : $CRD_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_CRIME_DTL (CRD_CODE, CRD_NAME, CR_CODE, CRD_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$CRD_NAME', '$CR_CODE', $CRD_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_CRIME_DTL', '$CRD_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select CRD_CODE from PER_CRIME_DTL where CRD_NAME = '$CRD_NAME' or instr(CRD_OTHERNAME, '||$CRD_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[CRD_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_CRIME_DTL', '$CRD_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_CRIME_DTL (CRD_CODE, CRD_NAME, CR_CODE, CRD_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$CRD_CODE', '$CRD_NAME', '$CR_CODE', $CRD_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภททุน		
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SCHOLARTYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(ST_CODE) as MAX_CODE from PER_SCHOLARTYPE WHERE SUBSTR(ST_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

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
			$NEW_CODE = "";

			$cmd = " select ST_NAME from PER_SCHOLARTYPE where ST_CODE = '$ST_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[ST_NAME]);
				if ($NEW_NAME != $ST_NAME) {
					$cmd = " select ST_CODE from PER_SCHOLARTYPE where ST_NAME = '$ST_NAME' or instr(ST_OTHERNAME, '||$ST_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[ST_CODE]);
 						if ($NEW_CODE != $ST_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_SCHOLARTYPE', '$ST_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภททุน $ST_CODE - $NEW_CODE : $ST_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_SCHOLARTYPE (ST_CODE, ST_NAME, ST_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$ST_NAME', $ST_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_SCHOLARTYPE', '$ST_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select ST_CODE from PER_SCHOLARTYPE where ST_NAME = '$ST_NAME' or instr(ST_OTHERNAME, '||$ST_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[ST_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SCHOLARTYPE', '$ST_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SCHOLARTYPE (ST_CODE, ST_NAME, ST_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$ST_CODE', '$ST_NAME', $ST_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ปฏิทินวันหยุด
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_HOLIDAY' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT HOL_DATE, HOL_NAME, UPDATE_USER, UPDATE_DATE 
						FROM PER_HOLIDAY ORDER BY HOL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$HOL_DATE = trim($data[HOL_DATE]);
			$HOL_NAME = trim($data[HOL_NAME]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select HOL_NAME from PER_HOLIDAY where HOL_DATE = '$HOL_DATE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[HOL_NAME]);
				if ($NEW_NAME != $HOL_NAME) {
					$cmd = " select HOL_DATE from PER_HOLIDAY where HOL_NAME = '$HOL_NAME' ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[HOL_DATE]);
 						if ($NEW_CODE != $HOL_DATE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_HOLIDAY', '$HOL_DATE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ปฏิทินวันหยุด $HOL_DATE - $NEW_CODE : $HOL_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select HOL_DATE from PER_HOLIDAY where HOL_NAME = '$HOL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[HOL_DATE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_HOLIDAY', '$HOL_DATE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_CRIME (HOL_DATE, HOL_NAME, UPDATE_USER, UPDATE_DATE)
									VALUES ('$HOL_DATE', '$HOL_NAME', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ด้านความสามารถพิเศษ  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ABILITYGRP' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(AL_CODE) as MAX_CODE from PER_ABILITYGRP WHERE SUBSTR(AL_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT AL_CODE, AL_NAME, AL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_ABILITYGRP ORDER BY AL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$AL_CODE = trim($data[AL_CODE]);
			$AL_NAME = trim($data[AL_NAME]);
			$AL_ACTIVE = $data[AL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select AL_NAME from PER_ABILITYGRP where AL_CODE = '$AL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[AL_NAME]);
				if ($NEW_NAME != $AL_NAME) {
					$cmd = " select AL_CODE from PER_ABILITYGRP where AL_NAME = '$AL_NAME' or instr(AL_OTHERNAME, '||$AL_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[AL_CODE]);
 						if ($NEW_CODE != $AL_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_ABILITYGRP', '$AL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ด้านความสามารถพิเศษ $AL_CODE - $NEW_CODE : $AL_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_ABILITYGRP (AL_CODE, AL_NAME, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$AL_NAME', $AL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_ABILITYGRP', '$AL_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select AL_CODE from PER_ABILITYGRP where AL_NAME = '$AL_NAME' or instr(AL_OTHERNAME, '||$AL_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[AL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ABILITYGRP', '$AL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ABILITYGRP (AL_CODE, AL_NAME, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$AL_CODE', '$AL_NAME', $AL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// เงื่อนไขตำแหน่ง
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CONDITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(PC_CODE) as MAX_CODE from PER_CONDITION WHERE SUBSTR(PC_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT PC_CODE, PC_NAME, PC_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CONDITION ORDER BY PC_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PC_CODE = trim($data[PC_CODE]);
			$PC_NAME = trim($data[PC_NAME]);
			$PC_ACTIVE = $data[PC_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select PC_NAME from PER_CONDITION where PC_CODE = '$PC_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[PC_NAME]);
				if ($NEW_NAME != $PC_NAME) {
					$cmd = " select PC_CODE from PER_CONDITION where PC_NAME = '$PC_NAME' or instr(PC_OTHERNAME, '||$PC_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PC_CODE]);
 						if ($NEW_CODE != $PC_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_CONDITION', '$PC_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "เงื่อนไขตำแหน่ง $PC_CODE - $NEW_CODE : $PC_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_CONDITION (PC_CODE, PC_NAME, PC_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$PC_NAME', $PC_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_CONDITION', '$PC_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select PC_CODE from PER_CONDITION where PC_NAME = '$PC_NAME' or instr(PC_OTHERNAME, '||$PC_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PC_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_CONDITION', '$PC_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_CONDITION (PC_CODE, PC_NAME, PC_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$PC_CODE', '$PC_NAME', $PC_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภทราชการพิเศษ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SERVICE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT SV_CODE, SV_NAME, SV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SERVICE ORDER BY SV_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SV_CODE = trim($data[SV_CODE]);
			$SV_NAME = trim($data[SV_NAME]);
			$SV_ACTIVE = $data[SV_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select SV_NAME from PER_SERVICE where SV_CODE = '$SV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[SV_NAME]);
				if ($NEW_NAME != $SV_NAME) {
					$cmd = " select SV_CODE from PER_SERVICE where SV_NAME = '$SV_NAME' or instr(SV_OTHERNAME, '||$SV_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[SV_CODE]);
 						if ($NEW_CODE != $SV_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_SERVICE', '$SV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทราชการพิเศษ $SV_CODE - $NEW_CODE : $SV_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select SV_CODE from PER_SERVICE where SV_NAME = '$SV_NAME' or instr(SV_OTHERNAME, '||$SV_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SV_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SERVICE', '$SV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SERVICE (SV_CODE, SV_NAME, SV_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$SV_CODE', '$SV_NAME', $SV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ระดับตำแหน่ง
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_LEVEL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE, 
						LEVEL_SHORTNAME, LEVEL_SEQ_NO    
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
			$NEW_CODE = "";

			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[LEVEL_NAME]);
				if ($NEW_NAME != $LEVEL_NAME) {
					$cmd = " select LEVEL_NO from PER_LEVEL where LEVEL_NAME = '$LEVEL_NAME' or instr(LEVEL_OTHERNAME, '||$LEVEL_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[LEVEL_NO]);
 						if ($NEW_CODE != $LEVEL_NO) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_LEVEL', '$LEVEL_NO', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ระดับตำแหน่ง $LEVEL_NO - $NEW_CODE : $LEVEL_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select LEVEL_NO from PER_LEVEL where LEVEL_NAME = '$LEVEL_NAME' or instr(LEVEL_OTHERNAME, '||$LEVEL_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[LEVEL_NO]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_LEVEL', '$LEVEL_NO', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, 
									PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
									VALUES ('$LEVEL_NO', $LEVEL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', '$LEVEL_NAME', $PER_TYPE, 
									'$LEVEL_SHORTNAME', $LEVEL_SEQ_NO) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while			
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		
/*
// บัญชีอัตราเงินเดือนข้าราชการ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_LAYER' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

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
			$NEW_CODE = "";

			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[RE_NAME]);
				if ($NEW_NAME != $RE_NAME) {
					$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PN_CODE]);
 						if ($NEW_CODE != $PN_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_PRENAME', '$PN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "คำนำหน้าชื่อ $PN_CODE - $NEW_CODE : $PN_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select LEVEL_NO from PER_LEVEL where LEVEL_NAME = '$LEVEL_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[LEVEL_NO]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_LEVEL', '$LEVEL_NO', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ($LAYER_TYPE, '$LEVEL_NO', $LAYER_NO, $LAYER_SALARY, $LAYER_ACTIVE, $UPDATE_USER, 
									'$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
*/

// สถาบันการศึกษา  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_INSTITUTE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(INS_CODE) as MAX_CODE from PER_INSTITUTE WHERE SUBSTR(INS_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_INSTITUTE ORDER BY INS_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$INS_CODE = trim($data[INS_CODE]);
			$INS_NAME = trim($data[INS_NAME]);
			$INS_NAME=str_replace("'","''",$INS_NAME);
			$INS_NAME=str_replace("\"","&quot;",$INS_NAME);
			$CT_CODE = trim($data[CT_CODE]);
			$INS_ACTIVE = $data[INS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select INS_NAME from PER_INSTITUTE where INS_CODE = '$INS_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[INS_NAME]);
				if ($NEW_NAME != $INS_NAME) {
					$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' or instr(INS_OTHERNAME, '||$INS_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[INS_CODE]);
 						if ($NEW_CODE != $INS_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_INSTITUTE', '$INS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "สถาบันการศึกษา $INS_CODE - $NEW_CODE : $INS_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_INSTITUTE (INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$INS_NAME', '$CT_CODE', $INS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_INSTITUTE', '$INS_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' or instr(INS_OTHERNAME, '||$INS_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[INS_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_INSTITUTE', '$INS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_INSTITUTE (INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$INS_CODE', '$INS_NAME', '$CT_CODE', $INS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ด้านความเชี่ยวชาญ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SKILL_GROUP' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(SG_CODE) as MAX_CODE from PER_SKILL_GROUP WHERE SUBSTR(SG_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT SG_CODE, SG_NAME, PL_CODE, SG_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SKILL_GROUP ORDER BY SG_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SG_CODE = trim($data[SG_CODE]);
			$SG_NAME = trim($data[SG_NAME]);
			$PL_CODE = trim($data[PL_CODE]);
			$SG_ACTIVE = $data[SG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LINE' AND OLD_CODE = $PL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select SG_NAME from PER_SKILL_GROUP where SG_CODE = '$SG_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[SG_NAME]);
				if ($NEW_NAME != $SG_NAME) {
					$cmd = " select SG_CODE from PER_SKILL_GROUP where SG_NAME = '$SG_NAME' or instr(SG_OTHERNAME, '||$SG_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[SG_CODE]);
 						if ($NEW_CODE != $SG_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_SKILL_GROUP', '$SG_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ด้านความเชี่ยวชาญ $SG_CODE - $NEW_CODE : $SG_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_SKILL_GROUP (SG_CODE, SG_NAME, PL_CODE, SG_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$SG_NAME', '$PL_CODE', $SG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_SKILL_GROUP', '$SG_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select SG_CODE from PER_SKILL_GROUP where SG_NAME = '$SG_NAME' or instr(SG_OTHERNAME, '||$SG_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SG_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SKILL_GROUP', '$SG_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SKILL_GROUP (SG_CODE, SG_NAME, PL_CODE, SG_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$SG_CODE', '$SG_NAME', '$PL_CODE', $SG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// สาขาความเชี่ยวชาญ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SKILL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(SKILL_CODE) as MAX_CODE from PER_SKILL WHERE SUBSTR(SKILL_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SKILL ORDER BY SKILL_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SKILL_CODE = trim($data[SKILL_CODE]);
			$SKILL_NAME = trim($data[SKILL_NAME]);
			$SG_CODE = trim($data[SG_CODE]);
			$SKILL_ACTIVE = $data[SKILL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SKILL_GROUP' AND OLD_CODE = $SG_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $SG_CODE = trim($data2[NEW_CODE]);

			$cmd = " select SKILL_NAME from PER_SKILL where SKILL_CODE = '$SKILL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[SKILL_NAME]);
				if ($NEW_NAME != $SKILL_NAME) {
					$cmd = " select SKILL_CODE from PER_SKILL where SKILL_NAME = '$SKILL_NAME' or instr(SKILL_OTHERNAME, '||$SKILL_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[SKILL_CODE]);
 						if ($NEW_CODE != $SKILL_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_SKILL', '$SKILL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "สาขาความเชี่ยวชาญ $SKILL_CODE - $NEW_CODE : $SKILL_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_SKILL (SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$SKILL_NAME', '$SG_CODE', $SKILL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_SKILL', '$SKILL_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select SKILL_CODE from PER_SKILL where SKILL_NAME = '$SKILL_NAME' or instr(SKILL_OTHERNAME, '||$SKILL_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SKILL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SKILL', '$SKILL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SKILL (SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$SKILL_CODE', '$SKILL_NAME', '$SG_CODE', $SKILL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}  
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ด้านความเชี่ยวชาญพิเศษ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SPECIAL_SKILLGRP' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(SS_CODE) as MAX_CODE from PER_SPECIAL_SKILLGRP WHERE SUBSTR(SS_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SPECIAL_SKILLGRP ORDER BY SS_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SS_CODE = trim($data[SS_CODE]);
			$SS_NAME = trim($data[SS_NAME]);
			$SS_ACTIVE = $data[SS_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select SS_NAME from PER_SPECIAL_SKILLGRP where SS_CODE = '$SS_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[SS_NAME]);
				if ($NEW_NAME != $SS_NAME) {
					$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' or instr(SS_OTHERNAME, '||$SS_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[SS_CODE]);
 						if ($NEW_CODE != $SS_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_SPECIAL_SKILLGRP', '$SS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ด้านความเชี่ยวชาญพิเศษ $SS_CODE - $NEW_CODE : $SS_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$SS_NAME', $SS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_SPECIAL_SKILLGRP', '$SS_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' or instr(SS_OTHERNAME, '||$SS_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SS_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SPECIAL_SKILLGRP', '$SS_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$SS_CODE', '$SS_NAME', $SS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ชั้นเครื่องราชอิสริยาภรณ์  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_DECORATION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, 
						UPDATE_DATE FROM PER_DECORATION ORDER BY DC_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$DC_CODE = trim($data[DC_CODE]);
			$DC_SHORTNAME = trim($data[DC_SHORTNAME]);
			$DC_NAME = trim($data[DC_NAME]);
			$DC_ORDER = $data[DC_ORDER] + 0;
			$DC_TYPE = $data[DC_TYPE] + 0;
			$DC_ACTIVE = $data[DC_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select DC_NAME from PER_DECORATION where DC_CODE = '$DC_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[DC_NAME]);
				if ($NEW_NAME != $DC_NAME) {
					$cmd = " select DC_CODE from PER_DECORATION where DC_NAME = '$DC_NAME' or instr(DC_OTHERNAME, '||$DC_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[DC_CODE]);
 						if ($NEW_CODE != $DC_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_DECORATION', '$DC_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ชั้นเครื่องราชอิสริยาภรณ์ $DC_CODE - $NEW_CODE : $DC_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select DC_CODE from PER_DECORATION where DC_NAME = '$DC_NAME' or instr(DC_OTHERNAME, '||$DC_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[DC_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_DECORATION', '$DC_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, 
									DC_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$DC_CODE', '$DC_SHORTNAME', '$DC_NAME', $DC_ORDER, $DC_TYPE, $DC_ACTIVE, 
									$UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ฐานะของหน่วยงาน
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_LEVEL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(OL_CODE) as MAX_CODE from PER_ORG_LEVEL WHERE SUBSTR(OL_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

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
			$NEW_CODE = "";

			$cmd = " select OL_NAME from PER_ORG_LEVEL where OL_CODE = '$OL_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[OL_NAME]);
				if ($NEW_NAME != $OL_NAME) {
					$cmd = " select OL_CODE from PER_ORG_LEVEL where OL_NAME = '$OL_NAME' or instr(OL_OTHERNAME, '||$OL_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[OL_CODE]);
 						if ($NEW_CODE != $OL_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_ORG_LEVEL', '$OL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ฐานะของหน่วยงาน $OL_CODE - $NEW_CODE : $OL_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$OL_NAME', $OL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_ORG_LEVEL', '$OL_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select OL_CODE from PER_ORG_LEVEL where OL_NAME = '$OL_NAME' or instr(OL_OTHERNAME, '||$OL_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[OL_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_ORG_LEVEL', '$OL_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$OL_CODE', '$OL_NAME', $OL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

/*
// เงินประจำตำแหน่ง  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MGTSALARY' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

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
			$NEW_CODE = "";

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PT_CODE', '$LEVEL_NO', $MS_SALARY, $MS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
*/

// ประเภทข้าราชการ 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_OFF_TYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(OT_CODE) as MAX_CODE from PER_OFF_TYPE WHERE SUBSTR(OT_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

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
			$NEW_CODE = "";

			$cmd = " select OT_NAME from PER_OFF_TYPE where OT_CODE = '$OT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[OT_NAME]);
				if ($NEW_NAME != $OT_NAME) {
					$cmd = " select OT_CODE from PER_OFF_TYPE where OT_NAME = '$OT_NAME' or instr(OT_OTHERNAME, '||$OT_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[OT_CODE]);
 						if ($NEW_CODE != $OT_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_OFF_TYPE', '$OT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทข้าราชการ $OT_CODE - $NEW_CODE : $OT_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$OT_NAME', $OT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_OFF_TYPE', '$OT_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select OT_CODE from PER_OFF_TYPE where OT_NAME = '$OT_NAME' or instr(OT_OTHERNAME, '||$OT_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[OT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_OFF_TYPE', '$OT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$OT_CODE', '$OT_NAME', $OT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// สถานภาพสมรส
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MARRIED' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT MR_CODE, MR_NAME, MR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_MARRIED ORDER BY MR_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$MR_CODE = trim($data[MR_CODE]);
			$MR_NAME = trim($data[MR_NAME]);
			$MR_ACTIVE = $data[MR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select MR_NAME from PER_MARRIED where MR_CODE = '$MR_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[MR_NAME]);
				if ($NEW_NAME != $MR_NAME) {
					$cmd = " select MR_CODE from PER_MARRIED where MR_NAME = '$MR_NAME' or instr(MR_OTHERNAME, '||$MR_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[MR_CODE]);
 						if ($NEW_CODE != $MR_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_MARRIED', '$MR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "สถานภาพสมรส $MR_CODE - $NEW_CODE : $MR_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select MR_CODE from PER_MARRIED where MR_NAME = '$MR_NAME' or instr(MR_OTHERNAME, '||$MR_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[MR_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_MARRIED', '$MR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_MARRIED (MR_CODE, MR_NAME, MR_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$MR_CODE', '$MR_NAME', $MR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// หมู่โลหิต 
// ประเภทเงินเพิ่มพิเศษ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EXTRATYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT EX_CODE, EX_NAME, EX_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EXTRATYPE ORDER BY EX_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EX_CODE = trim($data[EX_CODE]);
			$EX_NAME = trim($data[EX_NAME]);
			$EX_ACTIVE = $data[EX_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select EX_NAME from PER_EXTRATYPE where EX_CODE = '$EX_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[EX_NAME]);
				if ($NEW_NAME != $EX_NAME) {
					$cmd = " select EX_CODE from PER_EXTRATYPE where EX_NAME = '$EX_NAME' or instr(EX_OTHERNAME, '||$EX_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[EX_CODE]);
 						if ($NEW_CODE != $EX_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_EXTRATYPE', '$EX_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทเงินเพิ่มพิเศษ $EX_CODE - $NEW_CODE : $EX_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select EX_CODE from PER_EXTRATYPE where EX_NAME = '$EX_NAME' or instr(EX_OTHERNAME, '||$EX_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EX_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EXTRATYPE', '$EX_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$EX_CODE', '$EX_NAME', $EX_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
/*
// ประเภทเงินพิเศษ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EXTRA_INCOME_TYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(EXIN_CODE) as MAX_CODE from PER_EXTRA_INCOME_TYPE 
						WHERE SUBSTR(EXIN_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT EXIN_CODE, EXIN_NAME, EXIN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EXTRA_INCOME_TYPE ORDER BY EXIN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EXIN_CODE = trim($data[EXIN_CODE]);
			$EXIN_NAME = trim($data[EXIN_NAME]);
			$EXIN_ACTIVE = $data[EXIN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select EXIN_NAME from PER_EXTRA_INCOME_TYPE where EXIN_CODE = '$EXIN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[EXIN_NAME]);
				if ($NEW_NAME != $EXIN_NAME) {
					$cmd = " select EXIN_CODE from PER_EXTRA_INCOME_TYPE where EXIN_NAME = '$EXIN_NAME' ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[EXIN_CODE]);
 						if ($NEW_CODE != $EXIN_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_EXTRA_INCOME_TYPE', '$EXIN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทเงินพิเศษ $EXIN_CODE - $NEW_CODE : $EXIN_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_EXTRA_INCOME_TYPE (EXIN_CODE, EXIN_NAME, EXIN_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$EXIN_NAME', $EXIN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_EXTRA_INCOME_TYPE', '$EXIN_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select EXIN_CODE from PER_EXTRA_INCOME_TYPE where EXIN_NAME = '$EXIN_NAME' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[EXIN_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_EXTRA_INCOME_TYPE', '$EXIN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_EXTRA_INCOME_TYPE (EXIN_CODE, EXIN_NAME, EXIN_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$EXIN_CODE', '$EXIN_NAME', $EXIN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
*/
// ช่วงระดับตำแหน่ง  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CO_LEVEL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_CO_LEVEL ORDER BY CL_NAME ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$CL_NAME = trim($data[CL_NAME]);
			$LEVEL_NO_MIN = trim($data[LEVEL_NO_MIN]);
			$LEVEL_NO_MAX = trim($data[LEVEL_NO_MAX]);
			$CL_ACTIVE = $data[CL_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

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

			$cmd = " select CL_NAME from PER_CO_LEVEL where CL_NAME = '$CL_NAME' or instr(CL_OTHERNAME, '||$CL_NAME||') > 0 ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[CL_NAME]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_CO_LEVEL', '$CL_NAME', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} else {
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE)
								VALUES ('$CL_NAME', '$LEVEL_NO_MIN', '$LEVEL_NO_MAX', $CL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภทโทษทางวินัย  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PENALTY' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT PEN_CODE, PEN_NAME, PEN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_PENALTY ORDER BY PEN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PEN_CODE = trim($data[PEN_CODE]);
			$PEN_NAME = trim($data[PEN_NAME]);
			$PEN_ACTIVE = $data[PEN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select PEN_NAME from PER_PENALTY where PEN_CODE = '$PEN_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[PEN_NAME]);
				if ($NEW_NAME != $PEN_NAME) {
					$cmd = " select PEN_CODE from PER_PENALTY where PEN_NAME = '$PEN_NAME' or instr(PEN_OTHERNAME, '||$PEN_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PEN_CODE]);
 						if ($NEW_CODE != $PEN_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_PENALTY', '$PEN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทโทษทางวินัย $PEN_CODE - $NEW_CODE : $PEN_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select PEN_CODE from PER_PENALTY where PEN_NAME = '$PEN_NAME' or instr(PEN_OTHERNAME, '||$PEN_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[PEN_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_PENALTY', '$PEN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_PENALTY (PEN_CODE, PEN_NAME, PEN_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$PEN_CODE', '$PEN_NAME', $PEN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภททายาท
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_HEIRTYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT HR_CODE, HR_NAME, HR_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_HEIRTYPE ORDER BY HR_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$HR_CODE = trim($data[HR_CODE]);
			$HR_NAME = trim($data[HR_NAME]);
			$HR_ACTIVE = $data[HR_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select HR_NAME from PER_HEIRTYPE where HR_CODE = '$HR_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[HR_NAME]);
				if ($NEW_NAME != $HR_NAME) {
					$cmd = " select HR_CODE from PER_HEIRTYPE where HR_NAME = '$HR_NAME' or instr(HR_OTHERNAME, '||$HR_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[HR_CODE]);
 						if ($NEW_CODE != $HR_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_HEIRTYPE', '$HR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภททายาท $HR_CODE - $NEW_CODE : $HR_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select HR_CODE from PER_HEIRTYPE where HR_NAME = '$HR_NAME' or instr(HR_OTHERNAME, '||$HR_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[HR_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_HEIRTYPE', '$HR_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_HEIRTYPE (HR_CODE, HR_NAME, HR_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$HR_CODE', '$HR_NAME', $HR_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// หัวข้อราชการพิเศษ 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SERVICETITLE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(SRT_CODE) as MAX_CODE from PER_SERVICETITLE WHERE SUBSTR(SRT_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT SRT_CODE, SRT_NAME, SV_CODE, SRT_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_SERVICETITLE ORDER BY SRT_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SRT_CODE = trim($data[SRT_CODE]);
			$SRT_NAME = trim($data[SRT_NAME]);
			$SRT_NAME=str_replace("'","''",$SRT_NAME);
			$SRT_NAME=str_replace("\"","&quot;",$SRT_NAME);
			$SV_CODE = trim($data[SV_CODE]);
			$SRT_ACTIVE = $data[SRT_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SERVICE' AND OLD_CODE = $SV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $SV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select SRT_NAME from PER_SERVICETITLE where SRT_CODE = '$SRT_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[SRT_NAME]);
				if ($NEW_NAME != $SRT_NAME) {
					$cmd = " select SRT_CODE from PER_SERVICETITLE where SRT_NAME = '$SRT_NAME' or instr(SRT_OTHERNAME, '||$SRT_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[SRT_CODE]);
 						if ($NEW_CODE != $SRT_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_SERVICETITLE', '$SRT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "หัวข้อราชการพิเศษ $SRT_CODE - $NEW_CODE : $SRT_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_SERVICETITLE (SRT_CODE, SRT_NAME, SV_CODE, SRT_ACTIVE, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$MAX_CODE', '$SRT_NAME', '$SV_CODE', $SRT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_SERVICETITLE', '$SRT_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select SRT_CODE from PER_SERVICETITLE where SRT_NAME = '$SRT_NAME' or instr(SRT_OTHERNAME, '||$SRT_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[SRT_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_SERVICETITLE', '$SRT_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_SERVICETITLE (SRT_CODE, SRT_NAME, SV_CODE, SRT_ACTIVE, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$SRT_CODE', '$SRT_NAME', '$SV_CODE', $SRT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภทความดีความชอบ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_REWARD' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT REW_CODE, REW_NAME, REW_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_REWARD ORDER BY REW_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$REW_CODE = trim($data[REW_CODE]);
			$REW_NAME = trim($data[REW_NAME]);
			$REW_ACTIVE = $data[REW_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select REW_NAME from PER_REWARD where REW_CODE = '$REW_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[REW_NAME]);
				if ($NEW_NAME != $REW_NAME) {
					$cmd = " select REW_CODE from PER_REWARD where REW_NAME = '$REW_NAME' or instr(REW_OTHERNAME, '||$REW_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[REW_CODE]);
 						if ($NEW_CODE != $REW_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_REWARD', '$REW_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทความดีความชอบ $REW_CODE - $NEW_CODE : $REW_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select REW_CODE from PER_REWARD where REW_NAME = '$REW_NAME' or instr(REW_OTHERNAME, '||$REW_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[REW_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_REWARD', '$REW_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_REWARD (REW_CODE, REW_NAME, REW_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$REW_CODE', '$REW_NAME', $REW_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// เหตุที่ขาดจากสมรส
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_DIVORCE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(DV_CODE) as MAX_CODE from PER_DIVORCE WHERE SUBSTR(DV_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT DV_CODE, DV_NAME, DV_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_DIVORCE ORDER BY DV_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$DV_CODE = trim($data[DV_CODE]);
			$DV_NAME = trim($data[DV_NAME]);
			$DV_ACTIVE = $data[DV_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select DV_NAME from PER_DIVORCE where DV_CODE = '$DV_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[DV_NAME]);
				if ($NEW_NAME != $DV_NAME) {
					$cmd = " select DV_CODE from PER_DIVORCE where DV_NAME = '$DV_NAME' or instr(DV_OTHERNAME, '||$DV_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[DV_CODE]);
 						if ($NEW_CODE != $DV_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_DIVORCE', '$DV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "เหตุที่ขาดจากสมรส $DV_CODE - $NEW_CODE : $DV_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_DIVORCE (DV_CODE, DV_NAME, DV_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$DV_NAME', $DV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_DIVORCE', '$DV_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select DV_CODE from PER_DIVORCE where DV_NAME = '$DV_NAME' or instr(DV_OTHERNAME, '||$DV_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[DV_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_DIVORCE', '$DV_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_DIVORCE (DV_CODE, DV_NAME, DV_ACTIVE, UPDATE_USER, UPDATE_DATE)
									VALUES ('$DV_CODE', '$DV_NAME', $DV_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// เวลาทวีคูณ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TIME' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(TIME_CODE) as MAX_CODE from PER_TIME WHERE SUBSTR(TIME_CODE,1,5) = '$ORG_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_CODE = $data1[MAX_CODE];
		if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

		$cmd = " SELECT TIME_CODE, TIME_NAME, TIME_START, TIME_END, TIME_DAY, TIME_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_TIME ORDER BY TIME_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TIME_CODE = trim($data[TIME_CODE]);
			$TIME_NAME = trim($data[TIME_NAME]);
			$TIME_START = trim($data[TIME_START]);
			$TIME_END = trim($data[TIME_END]);
			$TIME_DAY = $data[TIME_DAY] + 0;
			$TIME_ACTIVE = $data[TIME_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$NEW_CODE = "";

			$cmd = " select TIME_NAME from PER_TIME where TIME_CODE = '$TIME_CODE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[TIME_NAME]);
				if ($NEW_NAME != $TIME_NAME) {
					$cmd = " select TIME_CODE from PER_TIME where TIME_NAME = '$TIME_NAME' or instr(TIME_OTHERNAME, '||$TIME_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[TIME_CODE]);
 						if ($NEW_CODE != $TIME_CODE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_TIME', '$TIME_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "เวลาทวีคูณ $TIME_CODE - $NEW_CODE : $TIME_NAME - $NEW_NAME<br>";
						$MAX_CODE++;
						$cmd = " INSERT INTO PER_TIME (TIME_CODE, TIME_NAME, TIME_START, TIME_END, TIME_DAY, TIME_ACTIVE, 
										UPDATE_USER, UPDATE_DATE)
										VALUES ('$MAX_CODE', '$TIME_NAME', '$TIME_START', '$TIME_END', $TIME_DAY, $TIME_ACTIVE, 
										$UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_TIME', '$TIME_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "<br>";
					}
				}
			} else {
				$cmd = " select TIME_CODE from PER_TIME where TIME_NAME = '$TIME_NAME' or instr(TIME_OTHERNAME, '||$TIME_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[TIME_CODE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_TIME', '$TIME_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_TIME (TIME_CODE, TIME_NAME, TIME_START, TIME_END, TIME_DAY, TIME_ACTIVE, 
									UPDATE_USER, UPDATE_DATE)
									VALUES ('$TIME_CODE', '$TIME_NAME', '$TIME_START', '$TIME_END', $TIME_DAY, $TIME_ACTIVE, 
									$UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภทคำสั่ง
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_COMTYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT COM_TYPE, COM_NAME, COM_DESC, COM_GROUP 
						FROM PER_COMTYPE ORDER BY COM_TYPE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$COM_TYPE = trim($data[COM_TYPE]);
			$COM_NAME = trim($data[COM_NAME]);
			$COM_DESC = trim($data[COM_DESC]);
			$COM_GROUP = trim($data[COM_GROUP]);
			$NEW_CODE = "";

			$cmd = " select COM_NAME from PER_COMTYPE where COM_TYPE = '$COM_TYPE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_NAME = trim($data2[COM_NAME]);
				if ($NEW_NAME != $COM_NAME) {
					$cmd = " select COM_TYPE from PER_COMTYPE where COM_NAME = '$COM_NAME' or instr(COM_OTHERNAME, '||$COM_NAME||') > 0 ";
					$count_data1 = $db_dpis2->send_cmd($cmd);

					if ($count_data1) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[COM_TYPE]);
 						if ($NEW_CODE != $COM_TYPE) {
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_COMTYPE', '$COM_TYPE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					} else {
						echo "ประเภทคำสั่ง $COM_TYPE - $NEW_CODE : $COM_NAME - $NEW_NAME<br>";
					}
				}
			} else {
				$cmd = " select COM_TYPE from PER_COMTYPE where COM_NAME = '$COM_NAME' or instr(COM_OTHERNAME, '||$COM_NAME||') > 0 ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_CODE = trim($data2[COM_TYPE]);
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_COMTYPE', '$COM_TYPE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
									VALUES ('$COM_TYPE', '$COM_NAME', '$COM_DESC', '$COM_GROUP') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

  		if(strtoupper($dpisdb_user)=="D07000") {
// หมวดตำแหน่งลูกจ้าง
			$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_GROUP' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " select max(PG_CODE) as MAX_CODE from PER_POS_GROUP WHERE SUBSTR(PG_CODE,1,5) = '$ORG_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$MAX_CODE = $data1[MAX_CODE];
			if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

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
				$NEW_CODE = "";

				$cmd = " select PG_NAME from PER_POS_GROUP where PG_CODE = '$PG_CODE' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_NAME = trim($data2[PG_NAME]);
					if ($NEW_NAME != $PG_NAME) {
						$cmd = " select PG_CODE from PER_POS_GROUP where PG_NAME = '$PG_NAME' or instr(PG_OTHERNAME, '||$PG_NAME||') > 0 ";
						$count_data1 = $db_dpis2->send_cmd($cmd);

						if ($count_data1) {			
							$data2 = $db_dpis2->get_array();
							$NEW_CODE = trim($data2[PG_CODE]);
 							if ($NEW_CODE != $PG_CODE) {
								$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
												VALUES ('PER_POS_GROUP', '$PG_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
								$db_dpis->send_cmd($cmd);
								//$db_dpis->show_error();
								//echo "<br>";
							}
						} else {
							echo "หมวดตำแหน่งลูกจ้าง $PG_CODE - $NEW_CODE : $PG_NAME - $NEW_NAME<br>";
							$MAX_CODE++;
							$cmd = " INSERT INTO PER_POS_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE)
											VALUES ('$MAX_CODE', '$PG_NAME', $PG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_POS_GROUP', '$PG_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					}
				} else {
					$cmd = " select PG_CODE from PER_POS_GROUP where PG_NAME = '$PG_NAME' or instr(PG_OTHERNAME, '||$PG_NAME||') > 0 ";
					$count_data = $db_dpis2->send_cmd($cmd);

					if ($count_data) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PG_CODE]);
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_POS_GROUP', '$PG_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					} else {
						$cmd = " INSERT INTO PER_POS_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE)
										VALUES ('$PG_CODE', '$PG_NAME', $PG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					}
				}
			} // end while	
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		
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
				$NEW_CODE = "";

				$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, 
								UPDATE_USER, UPDATE_DATE)
								VALUES ('$PG_CODE', $LAYERE_NO, $LAYERE_SALARY, $LAYERE_DAY, $LAYERE_HOUR, $LAYERE_ACTIVE, $UPDATE_USER, 
								'$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
*/

// ชื่อตำแหน่งลูกจ้าง
			$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_NAME' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " select max(PN_CODE) as MAX_CODE from PER_POS_NAME WHERE SUBSTR(PN_CODE,1,5) = '$ORG_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$MAX_CODE = $data1[MAX_CODE];
			if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

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
				$NEW_CODE = "";

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_GROUP' AND OLD_CODE = $PG_CODE ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PG_CODE = trim($data2[NEW_CODE]);

				$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE = '$PN_CODE' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_NAME = trim($data2[PN_NAME]);
					if ($NEW_NAME != $PN_NAME) {
						$cmd = " select PN_CODE from PER_POS_NAME where PN_NAME = '$PN_NAME' or instr(PN_OTHERNAME, '||$PN_NAME||') > 0 ";
						$count_data1 = $db_dpis2->send_cmd($cmd);

						if ($count_data1) {			
							$data2 = $db_dpis2->get_array();
							$NEW_CODE = trim($data2[PN_CODE]);
 							if ($NEW_CODE != $PN_CODE) {
								$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
												VALUES ('PER_POS_NAME', '$PN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
								$db_dpis->send_cmd($cmd);
								//$db_dpis->show_error();
								//echo "<br>";
							}
						} else {
							echo "ชื่อตำแหน่งลูกจ้าง $PN_CODE - $NEW_CODE : $PN_NAME - $NEW_NAME<br>";
							$MAX_CODE++;
							$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, 
											UPDATE_USER,	UPDATE_DATE)
											VALUES ('$MAX_CODE', '$PN_NAME', '$PG_CODE', $PN_DECOR, $PN_ACTIVE, $UPDATE_USER, 
											'$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_POS_NAME', '$PN_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					}
				} else {
					$cmd = " select PN_CODE from PER_POS_NAME where PN_NAME = '$PN_NAME' or instr(PN_OTHERNAME, '||$PN_NAME||') > 0 ";
					$count_data = $db_dpis2->send_cmd($cmd);

					if ($count_data) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[PN_CODE]);
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_POS_NAME', '$PN_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					} else {
						$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER,
										UPDATE_DATE)
										VALUES ('$PN_CODE', '$PN_NAME', '$PG_CODE', $PN_DECOR, $PN_ACTIVE, $UPDATE_USER, 
										'$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					}
				}
			} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ชื่อตำแหน่งพนักงานราชการ
			$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EMPSER_POS_NAME' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " select max(EP_CODE) as MAX_CODE from PER_EMPSER_POS_NAME 
							WHERE SUBSTR(EP_CODE,1,5) = '$ORG_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$MAX_CODE = $data1[MAX_CODE];
			if ($MAX_CODE) $MAX_CODE++; else $MAX_CODE = $ORG_CODE.'00000';

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
				$NEW_CODE = "";

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE = '$EP_CODE' ";
				$count_data = $db_dpis2->send_cmd($cmd);

				if ($count_data) {			
					$data2 = $db_dpis2->get_array();
					$NEW_NAME = trim($data2[EP_NAME]);
					if ($NEW_NAME != $EP_NAME) {
						$cmd = " select EP_CODE from PER_EMPSER_POS_NAME where EP_NAME = '$EP_NAME' or instr(EP_OTHERNAME, '||$EP_NAME||') > 0 ";
						$count_data1 = $db_dpis2->send_cmd($cmd);

						if ($count_data1) {			
							$data2 = $db_dpis2->get_array();
							$NEW_CODE = trim($data2[EP_CODE]);
 							if ($NEW_CODE != $EP_CODE) {
								$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
												VALUES ('PER_EMPSER_POS_NAME', '$EP_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
								$db_dpis->send_cmd($cmd);
								//$db_dpis->show_error();
								//echo "<br>";
							}
						} else {
							echo "ชื่อตำแหน่งพนักงานราชการ $EP_CODE - $NEW_CODE : $EP_NAME - $NEW_NAME<br>";
							$MAX_CODE++;
							$cmd = " INSERT INTO PER_EMPSER_POS_NAME (EP_CODE, EP_NAME, LEVEL_NO, EP_DECOR, EP_ACTIVE, 
											UPDATE_USER, UPDATE_DATE)
											VALUES ('$MAX_CODE', '$EP_NAME', '$LEVEL_NO', $EP_DECOR, $EP_ACTIVE, $UPDATE_USER, 
											'$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
											VALUES ('PER_EMPSER_POS_NAME', '$EP_CODE', '$MAX_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "<br>";
						}
					}
				} else {
					$cmd = " select EP_CODE from PER_EMPSER_POS_NAME where EP_NAME = '$EP_NAME' or instr(EP_OTHERNAME, '||$EP_NAME||') > 0 ";
					$count_data = $db_dpis2->send_cmd($cmd);

					if ($count_data) {			
						$data2 = $db_dpis2->get_array();
						$NEW_CODE = trim($data2[EP_CODE]);
						$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_EMPSER_POS_NAME', '$EP_CODE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					} else {
						$cmd = " INSERT INTO PER_EMPSER_POS_NAME (EP_CODE, EP_NAME, LEVEL_NO, EP_DECOR, EP_ACTIVE, 
										UPDATE_USER, UPDATE_DATE)
										VALUES ('$EP_CODE', '$EP_NAME', '$LEVEL_NO', $EP_DECOR, $EP_ACTIVE, $UPDATE_USER, 
										'$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					}
				}
			} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		} // end if						
		
	}

// บัญชีอัตราเงินเดือนข้าราชการใหม่ 
	if( $command=='ORG' ) { // โครงสร้างส่วนราชการ  
//  		if(strtoupper($dpisdb_user)=="D07000" || strtoupper($dpisdb_user)=="SES") {
			$cmd = " create index idx_per_org_department on per_org (department_id) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			$cmd = " create index idx_per_position_department on per_position (department_id) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			$cmd = " create index idx_per_pos_emp_department on per_pos_emp (department_id) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			$cmd = " create index idx_per_pos_empser_department on per_pos_empser (department_id) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			$cmd = " create index idx_per_personal_department on per_personal (department_id) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

//		}

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote", "per_family", "per_personal", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl", "per_kpi", "per_personal" );
/*		$table = array(	"per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", "per_coursedtl", "per_decordtl", 
									"per_promote_p", "per_move_req", "per_absent", "per_scholar", "per_family", 
									"per_personal" ); */

		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_kpi")
				$cmd = " delete from $table[$i] where kpi_per_id in $where_per_id ";
			else
				$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		
			$cmd = "  delete from $table[$i] where per_id in (SELECT PER_ID FROM PER_PERSONAL WHERE POS_ID IN (SELECT POS_ID FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			if ($table[$i]=="per_servicehis") {	
				$cmd = " delete from per_servicehis where per_id_assign in (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}

			if ($table[$i]=="per_scholar") {	
				$cmd = " delete from per_scholarinc where sc_id in (select sc_id from per_scholar where per_id in $where_per_id) ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();

				$cmd = " delete from per_scholar where per_id in $where_per_id ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
		} // end for
		$cmd = " delete from per_servicehis 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_emp 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_emp 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_empser 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_empser 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_move_req 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_move_req 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_move_req 
						where org_id_3 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_move 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_command where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " DELETE FROM PER_PERSONAL WHERE POS_ID IN (SELECT POS_ID FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

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
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL9 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL8 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL7 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL6 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL5 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL4 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL3 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL2 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL1 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM 
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE PER_POSITIONHIS SET ORG_ID_3 = 1 
						WHERE ORG_ID_3 IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
		
		$cmd = " DELETE FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

		if ($CTRL_TYPE > 2)
			$cmd = " SELECT ORG_ID FROM PER_CONTROL ";
		else
			$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' AND ORG_CODE = '$search_department_code' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
						VALUES ('PER_ORG', '$DEPT_ID', '$search_department_id', 1, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
						ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE 
						FROM PER_ORG  WHERE DEPARTMENT_ID = $DEPT_ID AND ORG_ID > 1 ORDER BY OL_CODE, ORG_ID_REF ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ORG++;
			$ORG_ID = $data[ORG_ID] + 0;
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_SHORT = trim($data[ORG_SHORT]);
			$OL_CODE = trim($data[OL_CODE]);
			$OT_CODE = trim($data[OT_CODE]);
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
			$ORG_ENG_NAME = trim($data[ORG_ENG_NAME]);
			$POS_LAT = $data[POS_LAT] + 0;
			$POS_LONG = $data[POS_LONG] + 0;
			$ORG_DOPA_CODE = trim($data[ORG_DOPA_CODE]);
			if ($OL_CODE=='03') $ORG_ID_REF = $search_department_id;
			else {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_REF' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_ID_REF = $data2[NEW_CODE] + 0;
//				if ($MAX_ID>=39280) echo "$cmd<br>";
			}

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_LEVEL' AND OLD_CODE = '$OL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_TYPE' AND OLD_CODE = '$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OT_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PROVINCE' AND OLD_CODE = '$PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_AMPHUR' AND OLD_CODE = '$AP_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $AP_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
							ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
							ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE)
							VALUES ($MAX_ID, '$ORG_CODE', '$ORG_NAME', '$ORG_SHORT', '$OL_CODE', '$OT_CODE', 
							'$ORG_ADDR1', '$ORG_ADDR2', '$ORG_ADDR3', '$AP_CODE', '$PV_CODE', 
							'$CT_CODE', '$ORG_DATE', '$ORG_JOB', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE', '$ORG_WEBSITE', $ORG_SEQ_NO, $search_department_id, '$ORG_ENG_NAME', $POS_LAT, $POS_LONG, '$ORG_DOPA_CODE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select ORG_ID from PER_ORG where ORG_ID = $MAX_ID ";
			$count_data = $db_dpis->send_cmd($cmd1);
			if (!$count_data)	echo "$cmd<br>";

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

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_ASS' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE PER_PERSONAL SET ORG_ID = NULL WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_ORG_JOB WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

//		$cmd = " SELECT ORG_ID FROM PER_ORG_ASS  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' AND ORG_CODE = '$search_department_code' ";
		$cmd = " SELECT ORG_ID FROM PER_CONTROL ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG_ASS ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
						ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE 
						FROM PER_ORG_ASS  WHERE DEPARTMENT_ID = $DEPT_ID ORDER BY OL_CODE, ORG_ID_REF ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG_ASS++;
			$ORG_ID = $data[ORG_ID] + 0;
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_SHORT = trim($data[ORG_SHORT]);
			$OL_CODE = trim($data[OL_CODE]);
			$OT_CODE = trim($data[OT_CODE]);
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
			$ORG_ENG_NAME = trim($data[ORG_ENG_NAME]);
			$POS_LAT = $data[POS_LAT] + 0;
			$POS_LONG = $data[POS_LONG] + 0;
			$ORG_DOPA_CODE = trim($data[ORG_DOPA_CODE]);
			if ($OL_CODE=='03') $ORG_ID_REF = $search_department_id;
			else {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_ASS' AND OLD_CODE = '$ORG_ID_REF' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_ID_REF = $data2[NEW_CODE] + 0;
			}

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_LEVEL' AND OLD_CODE = '$OL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_TYPE' AND OLD_CODE = '$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $OT_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PROVINCE' AND OLD_CODE = '$PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_AMPHUR' AND OLD_CODE = '$AP_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $AP_CODE = trim($data2[NEW_CODE]);
			if ($ORG_ID_REF==0) $ORG_ID_REF = $search_department_id;

			$cmd = " INSERT INTO PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, 
							ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE)
							VALUES ($MAX_ID, '$ORG_CODE', '$ORG_NAME', '$ORG_SHORT', '$OL_CODE', '$OT_CODE', 
							'$ORG_ADDR1', '$ORG_ADDR2', '$ORG_ADDR3', '$AP_CODE', '$PV_CODE', 
							'$CT_CODE', '$ORG_DATE', '$ORG_JOB', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE', '$ORG_WEBSITE', $ORG_SEQ_NO, $search_department_id, '$ORG_ENG_NAME', $POS_LAT, $POS_LONG, '$ORG_DOPA_CODE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select ORG_ID from PER_ORG_ASS where ORG_ID = $MAX_ID ";
			$count_data = $db_dpis->send_cmd($cmd1);
			if (!$count_data)	echo "$cmd<br>";

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

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
	} // end if

	if( $command=='POSITION' ) { // ตำแหน่งข้าราชการ  
		$table = array(	"per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", "per_coursedtl", "per_decordtl", "per_promote_p", "per_move_req", "per_absent", 
									"per_personal" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		$cmd = " delete from per_servicehis 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_emp 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_emp 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_empser 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_empser 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_move 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_command where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_PERSONAL WHERE POS_ID IN (SELECT POS_ID FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

		if ($CTRL_TYPE > 2)
			$cmd = " SELECT ORG_ID FROM PER_CONTROL ";
		else
			$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' AND ORG_CODE = '$search_department_code' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

  		if(strtoupper($dpisdb_user)=="D07000") 
			$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
							POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
							POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
							FROM PER_POSITION WHERE DEPARTMENT_ID = $DEPT_ID $where ORDER BY POS_ID ";
		elseif($search_pv_code)
			$cmd = " SELECT POS_ID, a.ORG_ID, POS_NO, a.OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
							POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
							POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID 
							FROM PER_POSITION a, PER_ORG b WHERE a.ORG_ID = b.ORG_ID $where and b.PV_CODE = '$search_pv_code' and POS_ID IN (SELECT POS_ID FROM PER_PERSONAL 
							WHERE DEPARTMENT_ID = $DEPT_ID) ORDER BY POS_ID ";
		else
			if ($where_org_id)
				$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
								POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
								POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POS_SEQ_NO, PAY_NO, POS_ORGMGT, LEVEL_NO, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_NO_NAME 
								FROM PER_POSITION WHERE POS_ID IN (SELECT POS_ID FROM PER_PERSONAL 
								WHERE DEPARTMENT_ID = $DEPT_ID $where) $where_org_id ORDER BY POS_ID ";
			else
				$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
								POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
								POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POS_SEQ_NO, PAY_NO, POS_ORGMGT, LEVEL_NO, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_NO_NAME 
								FROM PER_POSITION WHERE POS_ID IN (SELECT POS_ID FROM PER_PERSONAL 
								WHERE DEPARTMENT_ID = $DEPT_ID $where) ORDER BY POS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "$cmd<br>";
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
			$POS_SEQ_NO = $data[POS_SEQ_NO] + 0;
			$PAY_NO = trim($data[PAY_NO]);
			$POS_ORGMGT = trim($data[POS_ORGMGT]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$ORG_ID_3 = $data[ORG_ID_3] + 0;
			$ORG_ID_4 = $data[ORG_ID_4] + 0;
			$ORG_ID_5 = $data[ORG_ID_5] + 0;
			$POS_NO_NAME = trim($data[POS_NO_NAME]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_1' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_1 = $data2[NEW_CODE];
			else $ORG_ID_1 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_2' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_2 = $data2[NEW_CODE];
			else $ORG_ID_2 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MGT' AND OLD_CODE = '$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PM_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LINE' AND OLD_CODE = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SKILL' AND OLD_CODE = '$SKILL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $SKILL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TYPE' AND OLD_CODE = '$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PT_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_CONDITION' AND OLD_CODE = '$PC_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PC_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, 
							PM_CODE, PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, 
							POS_CONDITION, POS_DOC_NO, POS_REMARK, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, 
							UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POS_SEQ_NO, PAY_NO, POS_ORGMGT, LEVEL_NO, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_NO_NAME)
							VALUES ($MAX_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, '$PM_CODE', 
							'$PL_CODE', '$CL_NAME', $POS_SALARY, $POS_MGTSALARY, '$SKILL_CODE', '$PT_CODE', '$PC_CODE', 
							'$POS_CONDITION', '$POS_DOC_NO', '$POS_REMARK', '$POS_DATE', '$POS_GET_DATE', 
							'$POS_CHANGE_DATE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id, $POS_SEQ_NO, '$PAY_NO', '$POS_ORGMGT', '$LEVEL_NO', $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, '$POS_NO_NAME') ";
			$db_dpis->send_cmd($cmd);
			if ($POS_NO=="810" || $POS_NO=="820" || $POS_NO=="821" || $POS_NO=="1889") {
				$db_dpis->show_error();
				echo "<br>";
			}
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
							POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POS_SEQ_NO, PAY_NO, POS_ORGMGT, LEVEL_NO, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_NO_NAME 
							FROM PER_POSITION WHERE DEPARTMENT_ID = $DEPT_ID $where ORDER BY POS_ID ";
		elseif($search_pv_code)
			$cmd = " SELECT POS_ID, a.ORG_ID, POS_NO, a.OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
							POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
							POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID, POS_SEQ_NO, PAY_NO, POS_ORGMGT, LEVEL_NO, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_NO_NAME 
							FROM PER_POSITION a, PER_ORG b WHERE a.ORG_ID = b.ORG_ID $where and b.PV_CODE = '$search_pv_code' and POS_ID IN (SELECT POS_ID FROM PER_PERSONAL 
							WHERE DEPARTMENT_ID = $DEPT_ID) ORDER BY POS_ID ";
		else
			$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
							POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
							POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, 	DEPARTMENT_ID, POS_SEQ_NO, PAY_NO, POS_ORGMGT, LEVEL_NO, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_NO_NAME 
							FROM PER_POSITION WHERE POS_ID IN (SELECT POS_ID FROM PER_PERSONAL 
							WHERE DEPARTMENT_ID = $DEPT_ID $where) ORDER BY POS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$POS_NO = $data[POS_NO];

			$cmd = " SELECT POS_NO FROM PER_POSITION WHERE POS_NO = $POS_NO and DEPARTMENT_ID = $search_department_id ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "$POS_NO<br>";
		} // end while						

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

		if(strtoupper($dpisdb_user)=="D07000") {
// ลูกจ้างประจำ 
			$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_EMP' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " SELECT POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEM_SEQ_NO, POEM_REMARK, PG_CODE_SALARY, ORG_ID_3, ORG_ID_4, ORG_ID_5, POEM_NO_NAME 
							FROM PER_POS_EMP WHERE DEPARTMENT_ID = $DEPT_ID $where ORDER BY POEM_ID ";
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
				$POEM_SEQ_NO = $data[POEM_SEQ_NO] + 0;
				$POEM_REMARK = trim($data[POEM_REMARK]);
				$PG_CODE_SALARY = trim($data[PG_CODE_SALARY]);
				$ORG_ID_3 = $data[ORG_ID_3] + 0;
				$ORG_ID_4 = $data[ORG_ID_4] + 0;
				$ORG_ID_5 = $data[ORG_ID_5] + 0;
				$POEM_NO_NAME = trim($data[POEM_NO_NAME]);

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID = $data2[NEW_CODE];

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_1' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID_1 = $data2[NEW_CODE];
				else $ORG_ID_1 = 'NULL';

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_2' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID_2 = $data2[NEW_CODE];
				else $ORG_ID_2 = 'NULL';

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_NAME' AND OLD_CODE = '$PN_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PN_CODE = trim($data2[NEW_CODE]);

				$cmd = " INSERT INTO PER_POS_EMP (POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
								POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEM_SEQ_NO, POEM_REMARK, PG_CODE_SALARY, ORG_ID_3, ORG_ID_4, ORG_ID_5, POEM_NO_NAME)
								VALUES ($MAX_ID, $ORG_ID, '$POEM_NO', $ORG_ID_1, $ORG_ID_2, '$PN_CODE', 
								$POEM_MIN_SALARY, $POEM_MAX_SALARY, $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id, $POEM_SEQ_NO, '$POEM_REMARK', '$PG_CODE_SALARY', $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, '$POEM_NO_NAME') ";
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
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEM_SEQ_NO, POEM_REMARK, PG_CODE_SALARY, ORG_ID_3, ORG_ID_4, ORG_ID_5, POEM_NO_NAME 
							FROM PER_POS_EMP WHERE DEPARTMENT_ID = $DEPT_ID $where ORDER BY POEM_ID ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$POEM_NO = $data[POEM_NO];

				$cmd = " SELECT POEM_NO FROM PER_POS_EMP WHERE POEM_NO = $POEM_NO and DEPARTMENT_ID = $search_department_id ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$POEM_NO<br>";
			} // end while						

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// พนักงานราชการ
			$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_EMPSER' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " SELECT POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEMS_SEQ_NO, POEMS_REMARK, PPT_CODE, PEF_CODE, PPS_CODE, ORG_ID_3, ORG_ID_4, ORG_ID_5, POEMS_SKILL, POEMS_SOUTH, POEMS_NO_NAME 
							FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $DEPT_ID $where ORDER BY POEM_ID ";
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
				$POEMS_SEQ_NO = $data[POEMS_SEQ_NO] + 0;
				$POEMS_REMARK = trim($data[POEMS_REMARK]);
				$PPT_CODE = trim($data[PPT_CODE]);
				$PEF_CODE = trim($data[PEF_CODE]);
				$PPS_CODE = trim($data[PPS_CODE]);
				$ORG_ID_3 = $data[ORG_ID_3] + 0;
				$ORG_ID_4 = $data[ORG_ID_4] + 0;
				$ORG_ID_5 = $data[ORG_ID_5] + 0;
				$POEMS_SKILL = trim($data[POEMS_SKILL]);
				$POEMS_SOUTH = $data[POEMS_SOUTH] + 0;
				$POEMS_NO_NAME = trim($data[POEMS_NO_NAME]);

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID = $data2[NEW_CODE];

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_1' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID_1 = $data2[NEW_CODE];
				else $ORG_ID_1 = 'NULL';

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_2' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $ORG_ID_2 = $data2[NEW_CODE];
				else $ORG_ID_2 = 'NULL';

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EMPSER_POS_NAME' AND OLD_CODE = '$EP_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $EP_CODE = trim($data2[NEW_CODE]);

				$cmd = " INSERT INTO PER_POS_EMPSER (POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
								POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEMS_SEQ_NO, POEMS_REMARK, PPT_CODE, PEF_CODE, PPS_CODE, ORG_ID_3, ORG_ID_4, ORG_ID_5, POEMS_SKILL, POEMS_SOUTH, POEMS_NO_NAME)
								VALUES ($MAX_ID, $ORG_ID, '$POEMS_NO', $ORG_ID_1, $ORG_ID_2, '$EP_CODE', 
								$POEM_MIN_SALARY, $POEM_MAX_SALARY, $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id, $POEMS_SEQ_NO, '$POEMS_REMARK', '$PPT_CODE', '$PEF_CODE', '$PPS_CODE', $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, '$POEMS_SKILL', $POEMS_SOUTH, '$POEMS_NO_NAME') ";
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
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEMS_SEQ_NO, POEMS_REMARK, PPT_CODE, PEF_CODE, PPS_CODE, ORG_ID_3, ORG_ID_4, ORG_ID_5, POEMS_SKILL, POEMS_SOUTH, POEMS_NO_NAME 
							FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $DEPT_ID $where ORDER BY POEM_ID ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
			while($data = $db_dpis35->get_array()){
				$POEMS_NO = $data[POEMS_NO];

				$cmd = " SELECT POEMS_NO FROM PER_POS_EMPSER WHERE POEMS_NO = $POEMS_NO and DEPARTMENT_ID = $search_department_id ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) echo "$POEMS_NO<br>";
			} // end while						

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		} // end if

	} // end if

	if( $command=='PERSONAL' ) { // ข้อมูลข้าราชการ
		$table = array(	"per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", "per_coursedtl", "per_decordtl", "per_promote_p", "per_move_req", "per_absent", 
									"per_personal" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

		if ($CTRL_TYPE > 2)
			$cmd = " SELECT ORG_ID FROM PER_CONTROL ";
		else
			$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' AND ORG_CODE = '$search_department_code' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT PER_ID, PER_TYPE, a.OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME,
							PER_ENG_SURNAME, a.ORG_ID, a.POS_ID, POEM_ID, a.LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY,
							PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, PER_BLOOD, RE_CODE,
							PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE,
							PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME,
							PER_ADD1, PER_ADD2, a.PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS,
							a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID, APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID,
							PER_HIP_FLAG, PER_CERT_OCC, LEVEL_NO_SALARY, PER_NICKNAME, PER_HOME_TEL, PER_OFFICE_TEL, PER_FAX, 
							PER_MOBILE, PER_EMAIL, PER_FILE_NO, PER_BANK_ACCOUNT, PER_CONTACT_PERSON, 
							PER_REMARK, PER_START_ORG, PER_COOPERATIVE, PER_COOPERATIVE_NO, 
							PER_MEMBERDATE, ES_CODE, PAY_ID, PL_NAME_WORK, ORG_NAME_WORK, 
							PER_DOCNO, PER_DOCDATE, PER_EFFECTIVEDATE, PER_POS_REASON, 
							PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG, 
							PER_ORDAIN_DETAIL, PER_POS_ORGMGT, PER_CONTACT_COUNT, PER_DISABILITY, 
							PER_UNION, PER_UNIONDATE, PER_UNION2, PER_UNIONDATE2, PER_UNION3, PER_UNIONDATE3, 
							PER_UNION4, PER_UNIONDATE4, PER_UNION5, PER_UNIONDATE5, PER_JOB, 
							a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, a.ORG_ID_4, a.ORG_ID_5 
							FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code'
							ORDER BY PER_ID ";
		else
			$cmd = " SELECT PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME,
							PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY,
							PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, PER_BLOOD, RE_CODE,
							PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE,
							PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME,
							PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS,
							UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID,
							PER_HIP_FLAG, PER_CERT_OCC, LEVEL_NO_SALARY, PER_NICKNAME, PER_HOME_TEL, PER_OFFICE_TEL, PER_FAX, 
							PER_MOBILE, PER_EMAIL, PER_FILE_NO, PER_BANK_ACCOUNT, PER_CONTACT_PERSON, 
							PER_REMARK, PER_START_ORG, PER_COOPERATIVE, PER_COOPERATIVE_NO, 
							PER_MEMBERDATE, ES_CODE, PAY_ID, PL_NAME_WORK, ORG_NAME_WORK, 
							PER_DOCNO, PER_DOCDATE, PER_EFFECTIVEDATE, PER_POS_REASON, 
							PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG, 
							PER_ORDAIN_DETAIL, PER_POS_ORGMGT, PER_CONTACT_COUNT, PER_DISABILITY, 
							PER_UNION, PER_UNIONDATE, PER_UNION2, PER_UNIONDATE2, PER_UNION3, PER_UNIONDATE3, 
							PER_UNION4, PER_UNIONDATE4, PER_UNION5, PER_UNIONDATE5, PER_JOB, 
							ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 
							FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where ORDER BY PER_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$PER_ID = $data[PER_ID] + 0;
			$PER_TYPE = $data[PER_TYPE] + 0;
			$OT_CODE = trim($data[OT_CODE]);
			$PN_CODE = trim($data[PN_CODE]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
			$PER_ENG_SURNAME = trim($data[PER_ENG_SURNAME]);
			$ORG_ID = $data[ORG_ID] + 0;
			$POS_ID = $data[POS_ID] + 0;
			$POEM_ID = $data[POEM_ID] + 0;
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_ORGMGT = $data[PER_ORGMGT] + 0;
			$PER_SALARY = $data[PER_SALARY] + 0;
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
			$PER_NICKNAME = trim($data[PER_NICKNAME]);
			$PER_HOME_TEL = trim($data[PER_HOME_TEL]);
			$PER_OFFICE_TEL = trim($data[PER_OFFICE_TEL]);
			$PER_FAX = trim($data[PER_FAX]);
			$PER_MOBILE = trim($data[PER_MOBILE]);
			$PER_EMAIL = trim($data[PER_EMAIL]);
			$PER_FILE_NO = trim($data[PER_FILE_NO]);
			$PER_BANK_ACCOUNT = trim($data[PER_BANK_ACCOUNT]);
			$PER_CONTACT_PERSON = trim($data[PER_CONTACT_PERSON]);
			$PER_REMARK = trim($data[PER_REMARK]);
			$PER_START_ORG = trim($data[PER_START_ORG]);
			$PER_COOPERATIVE = $data[PER_COOPERATIVE] + 0;
			$PER_COOPERATIVE_NO = trim($data[PER_COOPERATIVE_NO]);
			$PER_MEMBERDATE = trim($data[PER_MEMBERDATE]);
			$PAY_ID = $data[PAY_ID] + 0;
			$ES_CODE = trim($data[ES_CODE]);		
			$PL_NAME_WORK = trim($data[PL_NAME_WORK]);
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);
			$PER_DOCNO = trim($data[PER_DOCNO]);
			$PER_DOCDATE = trim($data[PER_DOCDATE]);
			$PER_EFFECTIVEDATE = trim($data[PER_EFFECTIVEDATE]);
			$PER_POS_REASON = trim($data[PER_POS_REASON]);
			$PER_POS_YEAR = trim($data[PER_POS_YEAR]);
			$PER_POS_DOCTYPE = trim($data[PER_POS_DOCTYPE]);
			$PER_POS_DOCNO = trim($data[PER_POS_DOCNO]);
			$PER_POS_ORG = trim($data[PER_POS_ORG]);
			$PER_ORDAIN_DETAIL = trim($data[PER_ORDAIN_DETAIL]);
			$PER_POS_ORGMGT = trim($data[PER_POS_ORGMGT]);
			$PER_CONTACT_COUNT = $data[PER_CONTACT_COUNT] + 0;
			$PER_DISABILITY = $data[PER_DISABILITY] + 0;
			$PER_UNION = $data[PER_UNION] + 0;
			$PER_UNIONDATE = trim($data[PER_UNIONDATE]);
			$PER_UNION2 = $data[PER_UNION2] + 0;
			$PER_UNIONDATE2 = trim($data[PER_UNIONDATE2]);
			$PER_UNION3 = $data[PER_UNION3] + 0;
			$PER_UNIONDATE3 = trim($data[PER_UNIONDATE3]);
			$PER_UNION4 = $data[PER_UNION4] + 0;
			$PER_UNIONDATE4 = trim($data[PER_UNIONDATE4]);
			$PER_UNION5 = $data[PER_UNION5] + 0;
			$PER_UNIONDATE5 = trim($data[PER_UNIONDATE5]);
			$PER_JOB = trim($data[PER_JOB]);
			$ORG_ID_1 = $data[ORG_ID_1] + 0;
			$ORG_ID_2 = $data[ORG_ID_2] + 0;
			$ORG_ID_3 = $data[ORG_ID_3] + 0;
			$ORG_ID_4 = $data[ORG_ID_4] + 0;
			$ORG_ID_5 = $data[ORG_ID_5] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PRENAME' AND OLD_CODE = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_ASS' AND OLD_CODE = '$ORG_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE] + 0;

			if ($PER_TYPE==1) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$POS_ID' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POS_ID = $data2[NEW_CODE] + 0;
			} elseif ($PER_TYPE==2) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMP' AND OLD_CODE = '$POEM_ID' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POEM_ID = $data2[NEW_CODE] + 0;
			} elseif ($PER_TYPE==3) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMPSER' AND OLD_CODE = '$POEMS_ID' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POEMS_ID = $data2[NEW_CODE] + 0;
			}

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LEVEL' AND OLD_CODE = '$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $LEVEL_NO = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MARRIED' AND OLD_CODE = '$MR_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MR_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_RELIGION' AND OLD_CODE = '$RE_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $RE_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PRENAME' AND OLD_CODE = '$PN_CODE_F' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE_F = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PRENAME' AND OLD_CODE = '$PN_CODE_M' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE_M = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MOVMENT' AND OLD_CODE = '$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MOV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PROVINCE' AND OLD_CODE = '$PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PV_CODE = trim($data2[NEW_CODE]);

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
							LEVEL_NO_SALARY, PER_NICKNAME, PER_HOME_TEL, PER_OFFICE_TEL, PER_FAX, 
							PER_MOBILE, PER_EMAIL, PER_FILE_NO, PER_BANK_ACCOUNT, PER_CONTACT_PERSON, 
							PER_REMARK, PER_START_ORG, PER_COOPERATIVE, PER_COOPERATIVE_NO, 
							PER_MEMBERDATE, ES_CODE, PAY_ID, PL_NAME_WORK, ORG_NAME_WORK, 
							PER_DOCNO, PER_DOCDATE, PER_EFFECTIVEDATE, PER_POS_REASON, 
							PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG, 
							PER_ORDAIN_DETAIL, PER_POS_ORGMGT, PER_CONTACT_COUNT, PER_DISABILITY, 
							PER_UNION, PER_UNIONDATE, PER_UNION2, PER_UNIONDATE2, PER_UNION3, PER_UNIONDATE3, 
							PER_UNION4, PER_UNIONDATE4, PER_UNION5, PER_UNIONDATE5, PER_JOB,  
							ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5)
							VALUES ($MAX_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', '$PER_NAME', '$PER_SURNAME', 
							'$PER_ENG_NAME', '$PER_ENG_SURNAME', $ORG_ID, $POS_ID, $POEM_ID, '$LEVEL_NO', $PER_ORGMGT, 
							$PER_SALARY, $PER_MGTSALARY, $PER_SPSALARY, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', '$PER_RETIREDATE', 
							'$PER_STARTDATE', '$PER_OCCUPYDATE', '$PER_POSDATE', '$PER_SALDATE', '$PN_CODE_F', 
							'$PER_FATHERNAME', '$PER_FATHERSURNAME', '$PN_CODE_M', '$PER_MOTHERNAME', 
							'$PER_MOTHERSURNAME', '$PER_ADD1', '$PER_ADD2', '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, 
							$PER_SOLDIER, $PER_MEMBER, $PER_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id, 
							$APPROVE_PER_ID, $REPLACE_PER_ID, $ABSENT_FLAG, $POEMS_ID, '$PER_HIP_FLAG', 
							'$PER_CERT_OCC', '$LEVEL_NO_SALARY', '$PER_NICKNAME', '$PER_HOME_TEL', '$PER_OFFICE_TEL', '$PER_FAX', 
							'$PER_MOBILE', '$PER_EMAIL', '$PER_FILE_NO', '$PER_BANK_ACCOUNT', '$PER_CONTACT_PERSON', 
							'$PER_REMARK', '$PER_START_ORG', $PER_COOPERATIVE, '$PER_COOPERATIVE_NO', 
							'$PER_MEMBERDATE', '$ES_CODE', $PAY_ID, '$PL_NAME_WORK', '$ORG_NAME_WORK', 
							'$PER_DOCNO', '$PER_DOCDATE', '$PER_EFFECTIVEDATE', '$PER_POS_REASON', 
							'$PER_POS_YEAR', '$PER_POS_DOCTYPE', '$PER_POS_DOCNO', '$PER_POS_ORG', 
							'$PER_ORDAIN_DETAIL', '$PER_POS_ORGMGT', $PER_CONTACT_COUNT, $PER_DISABILITY, 
							$PER_UNION, '$PER_UNIONDATE', $PER_UNION2, '$PER_UNIONDATE2', $PER_UNION3, '$PER_UNIONDATE3', 
							$PER_UNION4, '$PER_UNIONDATE4', $PER_UNION5, '$PER_UNIONDATE5', '$PER_JOB', 
							$ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5) ";
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

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	} // end if

	if( $command=='POSITIONHIS' ) { // ประวัติการดำรงตำแหน่ง
		$cmd = " delete from per_positionhis where per_id in $where_per_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		if ($CTRL_TYPE > 2)
			$cmd = " SELECT ORG_ID FROM PER_CONTROL ";
		else
			$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' AND ORG_CODE = '$search_department_code' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(POH_ID) as MAX_ID from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
							POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, 
							ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, 
							POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE,
							PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3, POH_ORG_TRANSFER, POH_ORG, POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, 
							POH_LAST_POSITION, POH_CMD_SEQ, POH_ISREAL, POH_ORG_DOPA_CODE, ES_CODE,  POH_LEVEL_NO, TP_CODE, POH_UNDER_ORG3, POH_UNDER_ORG4, 
							POH_UNDER_ORG5, POH_ASS_ORG3, POH_ASS_ORG4, POH_ASS_ORG5, POH_REMARK1, POH_REMARK2, POH_POS_NO_NAME, POH_DOCNO_EDIT, 
							POH_DOCDATE_EDIT
							FROM PER_POSITIONHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY POH_ID ";
		else
			$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
							POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, 
							ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, 
							POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE,
							PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3, POH_ORG_TRANSFER, POH_ORG, POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, 
							POH_LAST_POSITION, POH_CMD_SEQ, POH_ISREAL, POH_ORG_DOPA_CODE, ES_CODE,  POH_LEVEL_NO, TP_CODE, POH_UNDER_ORG3, POH_UNDER_ORG4, 
							POH_UNDER_ORG5, POH_ASS_ORG3, POH_ASS_ORG4, POH_ASS_ORG5, POH_REMARK1, POH_REMARK2, POH_POS_NO_NAME, POH_DOCNO_EDIT, 
							POH_DOCDATE_EDIT
							FROM PER_POSITIONHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL 
							WHERE DEPARTMENT_ID = $DEPT_ID $where) ORDER BY POH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "$cmd<br>";
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
			$POH_ORG_TRANSFER = trim($data[POH_ORG_TRANSFER]);
			$POH_ORG = trim($data[POH_ORG]);
			$POH_ORG3 = trim($data[POH_ORG3]);
			$POH_PM_NAME = trim($data[POH_PM_NAME]);
			$POH_PL_NAME = trim($data[POH_PL_NAME]);
			$POH_SEQ_NO = $data[POH_SEQ_NO] + 0;
			$POH_LAST_POSITION = trim($data[POH_LAST_POSITION]);
			$POH_CMD_SEQ = $data[POH_CMD_SEQ] + 0;
			$POH_ISREAL = trim($data[POH_ISREAL]);
			$POH_ORG_DOPA_CODE = trim($data[POH_ORG_DOPA_CODE]);
			$ES_CODE = trim($data[ES_CODE]);
			$POH_LEVEL_NO = trim($data[POH_LEVEL_NO]);
			$TP_CODE = trim($data[TP_CODE]);
			$POH_UNDER_ORG3 = trim($data[POH_UNDER_ORG3]);
			$POH_UNDER_ORG4 = trim($data[POH_UNDER_ORG4]);
			$POH_UNDER_ORG5 = trim($data[POH_UNDER_ORG5]);
			$POH_ASS_ORG3 = trim($data[POH_ASS_ORG3]);
			$POH_ASS_ORG4 = trim($data[POH_ASS_ORG4]);
			$POH_ASS_ORG5 = trim($data[POH_ASS_ORG5]);
			$POH_REMARK1 = trim($data[POH_REMARK1]);
			$POH_REMARK2 = trim($data[POH_REMARK2]);
			$POH_POS_NO_NAME = trim($data[POH_POS_NO_NAME]);
			$POH_DOCNO_EDIT = trim($data[POH_DOCNO_EDIT]);
			$POH_DOCDATE_EDIT = trim($data[POH_DOCDATE_EDIT]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MOVMENT' AND OLD_CODE = '$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MOV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MGT' AND OLD_CODE = '$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PM_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LEVEL' AND OLD_CODE = '$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $LEVEL_NO = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LINE' AND OLD_CODE = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PL_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_NAME' AND OLD_CODE = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TYPE' AND OLD_CODE = '$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PT_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_1' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_1 = $data2[NEW_CODE];
			else $ORG_ID_1 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_2' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_2 = $data2[NEW_CODE];
			else $ORG_ID_2 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_3' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $ORG_ID_3 = $data2[NEW_CODE];
			else $ORG_ID_3 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EMPSER_POS_NAME' AND OLD_CODE = '$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $EP_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PROVINCE' AND OLD_CODE = '$PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PV_CODE = trim($data2[NEW_CODE]);

			if (!$POH_EFFECTIVEDATE && $POH_ENDDATE) $POH_EFFECTIVEDATE = $POH_ENDDATE;
			if (!$POH_DOCNO || $POH_DOCNO=="'") $POH_DOCNO = "-";
			if (!$POH_DOCDATE && $POH_EFFECTIVEDATE) $POH_DOCDATE = $POH_EFFECTIVEDATE;
			if (!$POH_POS_NO || $POH_POS_NO=="'") $POH_POS_NO = "-";
			if (!$POH_REMARK || $POH_REMARK=="'") $POH_REMARK = "-";

			$cmd = " INSERT INTO PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, 
							POH_DOCDATE, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
							POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, 
							POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3, POH_ORG_TRANSFER, POH_ORG, POH_PM_NAME, POH_PL_NAME, 
							POH_SEQ_NO, POH_LAST_POSITION, POH_CMD_SEQ, POH_ISREAL, POH_ORG_DOPA_CODE, ES_CODE,  POH_LEVEL_NO, TP_CODE, POH_UNDER_ORG3, 
							POH_UNDER_ORG4, POH_UNDER_ORG5, POH_ASS_ORG3, POH_ASS_ORG4, POH_ASS_ORG5, POH_REMARK1, POH_REMARK2, POH_POS_NO_NAME, 
							POH_DOCNO_EDIT, POH_DOCDATE_EDIT)
							VALUES ($MAX_ID, $PER_ID, '$POH_EFFECTIVEDATE', '$MOV_CODE', '$POH_ENDDATE', '$POH_DOCNO', 
							'$POH_DOCDATE', '$POH_POS_NO', '$PM_CODE', '$LEVEL_NO', '$PL_CODE', '$PN_CODE', '$PT_CODE', '$CT_CODE', 
							'$PV_CODE', '$AP_CODE', $POH_ORGMGT, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$POH_UNDER_ORG1', 
							'$POH_UNDER_ORG2', '$POH_ASS_ORG', '$POH_ASS_ORG1', '$POH_ASS_ORG2', $POH_SALARY, 
							$POH_SALARY_POS, '$POH_REMARK', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$EP_CODE', '$POH_ORG1', 
							'$POH_ORG2', '$POH_ORG3', '$POH_ORG_TRANSFER', '$POH_ORG', '$POH_PM_NAME', '$POH_PL_NAME', $POH_SEQ_NO, '$POH_LAST_POSITION', $POH_CMD_SEQ, 
							'$POH_ISREAL', '$POH_ORG_DOPA_CODE', '$ES_CODE',  '$POH_LEVEL_NO', '$TP_CODE', '$POH_UNDER_ORG3', '$POH_UNDER_ORG4', '$POH_UNDER_ORG5', 
							'$POH_ASS_ORG3', '$POH_ASS_ORG4', '$POH_ASS_ORG5', '$POH_REMARK1', '$POH_REMARK2', '$POH_POS_NO_NAME', '$POH_DOCNO_EDIT', 
							'$POH_DOCDATE_EDIT') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_POSITIONHIS where POH_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						
		
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_POSITIONHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITIONHIS - $PER_POSITIONHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	} // end if

	if( $command=='SALARYHIS' ) { // ประวัติการรับเงินเดือน
		$cmd = " delete from per_salaryhis where per_id in $where_per_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		if ($CTRL_TYPE > 2)
			$cmd = " SELECT ORG_ID FROM PER_CONTROL ";
		else
			$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' AND ORG_CODE = '$search_department_code' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(SAH_ID) as MAX_ID from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, 
							SAH_ENDDATE, UPDATE_USER,	UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, 
							LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_KF_YEAR, 
							SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY, SAH_POS_NO_NAME FROM PER_SALARYHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY SAH_ID ";
		else
			$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, 
							SAH_ENDDATE, UPDATE_USER,	UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, 
							LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_KF_YEAR, 
							SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY, SAH_POS_NO_NAME FROM PER_SALARYHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$SAH_PERCENT_UP = $data[SAH_PERCENT_UP] + 0;
			$SAH_SALARY_UP = $data[SAH_SALARY_UP] + 0;
			$SAH_SALARY_EXTRA = $data[SAH_SALARY_EXTRA] + 0;
			$SAH_SEQ_NO = $data[SAH_SEQ_NO] + 0;
			$SAH_REMARK = trim($data[SAH_REMARK]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$SAH_POS_NO = trim($data[SAH_POS_NO]);
			$SAH_POSITION = trim($data[SAH_POSITION]);
			$SAH_ORG = trim($data[SAH_ORG]);
			$EX_CODE = trim($data[EX_CODE]);
			$SAH_PAY_NO = trim($data[SAH_PAY_NO]);
			$SAH_SALARY_MIDPOINT = $data[SAH_SALARY_MIDPOINT] + 0;
			$SAH_KF_CYCLE = $data[SAH_KF_CYCLE] + 0;
			$SAH_TOTAL_SCORE = $data[SAH_TOTAL_SCORE] + 0;
			$SAH_KF_YEAR = trim($data[SAH_KF_YEAR]);
			$SAH_LAST_SALARY = trim($data[SAH_LAST_SALARY]);
			$SM_CODE = trim($data[SM_CODE]);
			$SAH_CMD_SEQ = $data[SAH_CMD_SEQ] + 0;
			$SAH_ORG_DOPA_CODE = trim($data[SAH_ORG_DOPA_CODE]);
			$SAH_OLD_SALARY = $data[SAH_OLD_SALARY] + 0;
			$SAH_POS_NO_NAME = trim($data[SAH_POS_NO_NAME]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MOVMENT' AND OLD_CODE = '$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MOV_CODE = trim($data2[NEW_CODE]);

			if (!$SAH_EFFECTIVEDATE && $SAH_ENDDATE) $SAH_EFFECTIVEDATE = $SAH_ENDDATE;
			if (!$SAH_DOCNO || $SAH_DOCNO=="'") $SAH_DOCNO = "-";
			if (!$SAH_DOCDATE && $SAH_EFFECTIVEDATE) $SAH_DOCDATE = $SAH_EFFECTIVEDATE;

			$cmd = " INSERT INTO PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_KF_YEAR, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY, SAH_POS_NO_NAME)
							VALUES ($MAX_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO', '$SAH_DOCDATE', 
							'$SAH_ENDDATE', 	$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP, $SAH_SALARY_UP, $SAH_SALARY_EXTRA, $SAH_SEQ_NO, '$SAH_REMARK', '$LEVEL_NO', '$SAH_POS_NO', '$SAH_POSITION', '$SAH_ORG', '$EX_CODE', '$SAH_PAY_NO', $SAH_SALARY_MIDPOINT, $SAH_KF_CYCLE, $SAH_TOTAL_SCORE, '$SAH_KF_YEAR', '$SAH_LAST_SALARY', '$SM_CODE', $SAH_CMD_SEQ, '$SAH_ORG_DOPA_CODE', $SAH_OLD_SALARY, '$SAH_POS_NO_NAME') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_SALARYHIS where SAH_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						
		
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SALARYHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SALARYHIS - $PER_SALARYHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	} // end if

	if( $command=='HISTORY' ) { // ประวัติอื่น ๆ
		$table = array(	"per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		if ($CTRL_TYPE > 2)
			$cmd = " SELECT ORG_ID FROM PER_CONTROL ";
		else
			$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' AND ORG_CODE = '$search_department_code' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

// ประวัติการรับเงินเพิ่มพิเศษ  
		$cmd = " select max(EXH_ID) as MAX_ID from PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, UPDATE_USER, UPDATE_DATE,	
							PER_CARDNO, EXH_ORG_NAME, EXH_SALARY, EXH_REMARK, EXH_DOCNO, EXH_DOCDATE, EXH_ACTIVE FROM PER_EXTRAHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY EXH_ID ";
		else
			$cmd = " SELECT EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, UPDATE_USER, UPDATE_DATE,	
							PER_CARDNO, EXH_ORG_NAME, EXH_SALARY, EXH_REMARK, EXH_DOCNO, EXH_DOCDATE, EXH_ACTIVE FROM PER_EXTRAHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$EXH_ORG_NAME = trim($data[EXH_ORG_NAME]);
			$EXH_SALARY = $data[EXH_SALARY] + 0;
			$EXH_REMARK = trim($data[EXH_REMARK]);
			$EXH_DOCNO = trim($data[EXH_DOCNO]);
			$EXH_DOCDATE = trim($data[EXH_DOCDATE]);
			$EXH_ACTIVE = $data[EXH_ACTIVE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EXTRATYPE' AND OLD_CODE = '$EX_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $EX_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO, EXH_ORG_NAME, EXH_SALARY, EXH_REMARK, EXH_DOCNO, EXH_DOCDATE, EXH_ACTIVE)
							VALUES ($MAX_ID, $PER_ID, '$EXH_EFFECTIVEDATE', '$EX_CODE', $EXH_AMT, '$EXH_ENDDATE', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO', '$EXH_ORG_NAME', $EXH_SALARY, '$EXH_REMARK', '$EXH_DOCNO', '$EXH_DOCDATE', $EXH_ACTIVE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_EXTRAHIS where EXH_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EXTRAHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EXTRAHIS - $PER_EXTRAHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการศึกษา  		
		$cmd = " select max(EDU_ID) as MAX_ID from PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, EDU_FUND, EN_CODE, 
							EM_CODE, INS_CODE,	EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, EL_CODE, EDU_ENDDATE, EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, 
							EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, CT_CODE_EDU FROM PER_EDUCATE 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY EDU_ID ";
		else
			$cmd = " SELECT EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, EDU_FUND, EN_CODE, 
							EM_CODE, INS_CODE,	EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, EL_CODE, EDU_ENDDATE, EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, 
							EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, CT_CODE_EDU FROM PER_EDUCATE 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$EL_CODE = trim($data[EL_CODE]);
			$EDU_ENDDATE = trim($data[EDU_ENDDATE]);
			$EDU_GRADE = $data[EDU_GRADE] + 0;
			$EDU_HONOR = trim($data[EDU_HONOR]);
			$EDU_BOOK_NO = trim($data[EDU_BOOK_NO]);
			$EDU_BOOK_DATE = trim($data[EDU_BOOK_DATE]);
			$EDU_REMARK = trim($data[EDU_REMARK]);
			$EDU_INSTITUTE = trim($data[EDU_INSTITUTE]);
			$CT_CODE_EDU = trim($data[CT_CODE_EDU]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SCHOLARTYPE' AND OLD_CODE = '$ST_CODE' ";
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
							EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, EL_CODE, EDU_ENDDATE, EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, CT_CODE_EDU)
							VALUES ($MAX_ID, $PER_ID, '$EDU_SEQ', '$EDU_STARTYEAR', '$EDU_ENDYEAR', '$ST_CODE', '$CT_CODE', 
							'$EDU_FUND', '$EN_CODE', '$EM_CODE', '$INS_CODE', '$EDU_TYPE', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO', '$EL_CODE', '$EDU_ENDDATE', $EDU_GRADE, '$EDU_HONOR', '$EDU_BOOK_NO', '$EDU_BOOK_DATE', '$EDU_REMARK', '$EDU_INSTITUTE', '$CT_CODE_EDU') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_EDUCATE where EDU_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EDUCATE 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCATE - $PER_EDUCATE - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการอบรม/ดูงาน/สัมมนา  
		$cmd = " select max(TRN_ID) as MAX_ID from PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, TRN_PLACE, 
							CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO, TRN_DAY, TRN_REMARK, TRN_PASS, TRN_BOOK_NO, TRN_BOOK_DATE, 
							TRN_PROJECT_NAME, TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_POINT, TRN_OBJECTIVE FROM PER_TRAINING 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY TRN_ID ";
		else
			$cmd = " SELECT TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, TRN_PLACE, 
							CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO, TRN_DAY, TRN_REMARK, TRN_PASS, TRN_BOOK_NO, TRN_BOOK_DATE, 
							TRN_PROJECT_NAME, TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_POINT, TRN_OBJECTIVE FROM PER_TRAINING 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$TRN_DAY = $data[TRN_DAY] + 0;
			$TRN_REMARK = trim($data[TRN_REMARK]);
			$TRN_PASS = $data[TRN_PASS] + 0;
			$TRN_BOOK_NO = trim($data[TRN_BOOK_NO]);
			$TRN_BOOK_DATE = trim($data[TRN_BOOK_DATE]);
			$TRN_PROJECT_NAME = trim($data[TRN_PROJECT_NAME]);
			$TRN_COURSE_NAME = trim($data[TRN_COURSE_NAME]);
			$TRN_DEGREE_RECEIVE = trim($data[TRN_DEGREE_RECEIVE]);
			$TRN_POINT = trim($data[TRN_POINT]);
			$TRN_OBJECTIVE = trim($data[TRN_OBJECTIVE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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
							TRN_ORG, TRN_PLACE, CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO, TRN_DAY, TRN_REMARK, TRN_PASS, 
							TRN_BOOK_NO, TRN_BOOK_DATE, TRN_PROJECT_NAME, TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_POINT, TRN_OBJECTIVE)
							VALUES ($MAX_ID, $PER_ID, $TRN_TYPE, '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', 
							'$TRN_ORG', '$TRN_PLACE', '$CT_CODE', '$TRN_FUND', '$CT_CODE_FUND', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO', $TRN_DAY, '$TRN_REMARK', $TRN_PASS, '$TRN_BOOK_NO', '$TRN_BOOK_DATE', '$TRN_PROJECT_NAME', '$TRN_COURSE_NAME', 
							'$TRN_DEGREE_RECEIVE', '$TRN_POINT', '$TRN_OBJECTIVE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_TRAINING where TRN_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TRAINING 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TRAINING - $PER_TRAINING - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ความสามารถพิเศษ  
		$cmd = " select max(ABI_ID) as MAX_ID from PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT ABI_ID, PER_ID, AL_CODE, ABI_DESC, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABI_REMARK FROM PER_ABILITY 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY ABI_ID ";
		else
			$cmd = " SELECT ABI_ID, PER_ID, AL_CODE, ABI_DESC, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABI_REMARK FROM PER_ABILITY 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$ABI_REMARK = trim($data[ABI_REMARK]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ABILITYGRP' AND OLD_CODE = '$AL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $AL_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_ABILITY (ABI_ID, PER_ID, AL_CODE, ABI_DESC, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABI_REMARK)
							VALUES ($MAX_ID, $PER_ID, '$AL_CODE', '$ABI_DESC', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABI_REMARK') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_ABILITY where ABI_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABILITY 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABILITY - $PER_ABILITY - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ความเชี่ยวชาญพิเศษ  
		$cmd = " select max(SPS_ID) as MAX_ID from PER_SPECIAL_SKILL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT SPS_ID, PER_ID, SS_CODE, SPS_EMPHASIZE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SPS_REMARK 
							FROM PER_SPECIAL_SKILL 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY SPS_ID ";
		else
			$cmd = " SELECT SPS_ID, PER_ID, SS_CODE, SPS_EMPHASIZE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SPS_REMARK 
							FROM PER_SPECIAL_SKILL 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$SPS_REMARK = trim($data[SPS_REMARK]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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
							PER_CARDNO, SPS_REMARK)
							VALUES ($MAX_ID, $PER_ID, '$SS_CODE', '$SPS_EMPHASIZE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$SPS_REMARK') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_SPECIAL_SKILL where SPS_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SPECIAL_SKILL 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SPECIAL_SKILL - $PER_SPECIAL_SKILL - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ทายาท  
		$cmd = " select max(HEIR_ID) as MAX_ID from PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT HEIR_ID, PER_ID, HR_CODE, HEIR_NAME, HEIR_STATUS, HEIR_BIRTHDAY, HEIR_TAX, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO, HEIR_REMARK FROM PER_HEIR 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY HEIR_ID ";
		else
			$cmd = " SELECT HEIR_ID, PER_ID, HR_CODE, HEIR_NAME, HEIR_STATUS, HEIR_BIRTHDAY, HEIR_TAX, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO, HEIR_REMARK FROM PER_HEIR 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$HEIR_REMARK = trim($data[HEIR_REMARK]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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
							UPDATE_USER, UPDATE_DATE, 	PER_CARDNO, HEIR_REMARK)
							VALUES ($MAX_ID, $PER_ID, '$HR_CODE', '$HEIR_NAME', $HEIR_STATUS, '$HEIR_BIRTHDAY', '$HEIR_TAX', 
							$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$HEIR_REMARK') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_HEIR where HEIR_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_HEIR 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_HEIR - $PER_HEIR - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการลา  		
		$cmd = " select max(ABS_ID) as MAX_ID from PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, 
							UPDATE_USER,	UPDATE_DATE, PER_CARDNO, ABS_REMARK FROM PER_ABSENTHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY ABS_ID ";
		else
			$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, 
							UPDATE_USER,	UPDATE_DATE, PER_CARDNO, ABS_REMARK FROM PER_ABSENTHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$ABS_REMARK = trim($data[ABS_REMARK]);
			if (!$ABS_STARTPERIOD) $ABS_STARTPERIOD = 3;
			if (!$ABS_ENDPERIOD) $ABS_ENDPERIOD = 3;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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
							ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
							VALUES ($MAX_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', 
							$ABS_ENDPERIOD, $ABS_DAY, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_ABSENTHIS where ABS_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติทางวินัย  
		$cmd = " select max(PUN_ID) as MAX_ID from PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, PUN_STARTDATE, PUN_ENDDATE, CRD_CODE, 
							PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, PUN_REMARK, PUN_RECEIVE_NO, PUN_SEND_NO, PUN_NOTICE, 
							PUN_REPORTDATE, PUN_VIOLATEDATE, PUN_DOCDATE FROM PER_PUNISHMENT 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY PUN_ID ";
		else
			$cmd = " SELECT PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, PUN_STARTDATE, PUN_ENDDATE, CRD_CODE, 
							PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, PUN_REMARK, PUN_RECEIVE_NO, PUN_SEND_NO, PUN_NOTICE, 
							PUN_REPORTDATE, PUN_VIOLATEDATE, PUN_DOCDATE FROM PER_PUNISHMENT 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$PUN_REMARK = trim($data[PUN_REMARK]);
			$PUN_RECEIVE_NO = trim($data[PUN_RECEIVE_NO]);
			$PUN_SEND_NO = trim($data[PUN_SEND_NO]);
			$PUN_NOTICE = trim($data[PUN_NOTICE]);
			$PUN_REPORTDATE = trim($data[PUN_REPORTDATE]);
			$PUN_VIOLATEDATE = trim($data[PUN_VIOLATEDATE]);
			$PUN_DOCDATE = trim($data[PUN_DOCDATE]);
			if (!$PUN_STARTDATE) $PUN_STARTDATE = "NULL";
			if (!$PUN_ENDDATE) $PUN_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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
							PUN_ENDDATE, CRD_CODE, PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, PUN_REMARK, PUN_RECEIVE_NO, 
							PUN_SEND_NO, PUN_NOTICE, PUN_REPORTDATE, PUN_VIOLATEDATE, PUN_DOCDATE)
							VALUES ($MAX_ID, $PER_ID, '$INV_NO', '$PUN_NO', '$PUN_REF_NO', $PUN_TYPE, '$PUN_STARTDATE', '$PUN_ENDDATE', 
							'$CRD_CODE', '$PEN_CODE', 	$PUN_PAY, $PUN_SALARY, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$PUN_REMARK', '$PUN_RECEIVE_NO', 
							'$PUN_SEND_NO', '$PUN_NOTICE', '$PUN_REPORTDATE', '$PUN_VIOLATEDATE', '$PUN_DOCDATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_PUNISHMENT where PUN_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PUNISHMENT 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PUNISHMENT - $PER_PUNISHMENT - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติราชการพิเศษ  
		$cmd = " select max(SRH_ID) as MAX_ID from PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, SRH_DOCNO, 
							PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SRH_ORG FROM PER_SERVICEHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY SRH_ID ";
		else
			$cmd = " SELECT SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, SRH_DOCNO, 
							PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SRH_ORG FROM PER_SERVICEHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$SRH_ORG = trim($data[SRH_ORG]);
			if (!$SRH_STARTDATE) $SRH_STARTDATE = "-";
			if (!$SRH_ENDDATE) $SRH_ENDDATE = $SRH_STARTDATE;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID_ASSIGN' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID_ASSIGN = $data2[NEW_CODE] + 0;
			if ($PER_ID_ASSIGN==0) $PER_ID_ASSIGN = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID' ";
			$db_dpis2->send_cmd($cmd);
			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE] + 0;
			if ($ORG_ID==0) $ORG_ID = $search_department_id;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_ASSIGN' ";
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
							SRH_NOTE, SRH_DOCNO, PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SRH_ORG)
							VALUES ($MAX_ID, $PER_ID, '$SV_CODE', '$SRT_CODE', $ORG_ID, '$SRH_STARTDATE', '$SRH_ENDDATE', 
							'$SRH_NOTE', '$SRH_DOCNO', 	$PER_ID_ASSIGN, $ORG_ID_ASSIGN, $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO', '$SRH_ORG') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_SERVICEHIS where SRH_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SERVICEHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SERVICEHIS - $PER_SERVICEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติความดีความชอบ  
		$cmd = " select max(REH_ID) as MAX_ID from PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, UPDATE_DATE, 
							PER_CARDNO, REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK FROM PER_REWARDHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY REH_ID ";
		else
			$cmd = " SELECT REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, UPDATE_DATE, 
							PER_CARDNO, REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK FROM PER_REWARDHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$REH_YEAR = trim($data[REH_YEAR]);
			$REH_PERFORMANCE = trim($data[REH_PERFORMANCE]);
			$REH_OTHER_PERFORMANCE = trim($data[REH_OTHER_PERFORMANCE]);
			$REH_REMARK = trim($data[REH_REMARK]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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
							UPDATE_DATE, PER_CARDNO, REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK)
							VALUES ($MAX_ID, $PER_ID, '$REW_CODE', '$REH_ORG', '$REH_DOCNO', '$REH_DATE', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO', '$REH_YEAR', '$REH_PERFORMANCE', '$REH_OTHER_PERFORMANCE', '$REH_REMARK') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_REWARDHIS where REH_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_REWARDHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_REWARDHIS - $PER_REWARDHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการสมรส   
		$cmd = " select max(MAH_ID) as MAX_ID from PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO, PN_CODE, MAH_MARRY_NO, MAH_MARRY_ORG, PV_CODE, MR_CODE, MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK 
							FROM PER_MARRHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY MAH_ID ";
		else
			$cmd = " SELECT MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO, PN_CODE, MAH_MARRY_NO, MAH_MARRY_ORG, PV_CODE, MR_CODE, MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK 
							FROM PER_MARRHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$PN_CODE = trim($data[PN_CODE]);
			$MAH_MARRY_NO = trim($data[MAH_MARRY_NO]);
			$MAH_MARRY_ORG = trim($data[MAH_MARRY_ORG]);
			$PV_CODE = trim($data[PV_CODE]);
			$MR_CODE = trim($data[MR_CODE]);
			$MAH_BOOK_NO = trim($data[MAH_BOOK_NO]);
			$MAH_BOOK_DATE = trim($data[MAH_BOOK_DATE]);
			$MAH_REMARK = trim($data[MAH_REMARK]);
//			if (!$MAH_NAME) $MAH_NAME = "NULL";
//			if (!$MAH_MARRY_DATE) $MAH_MARRY_DATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PROVINCE' AND OLD_CODE = '$PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PV_CODE = trim($data2[NEW_CODE]);

			$cmd = " INSERT INTO PER_MARRHIS (MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, 
							DV_CODE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, PN_CODE, MAH_MARRY_NO, MAH_MARRY_ORG, PV_CODE, MR_CODE, MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK)
							VALUES ($MAX_ID, $PER_ID, $MAH_SEQ, '$MAH_NAME', '$MAH_MARRY_DATE', '$MAH_DIVORCE_DATE', '$DV_CODE', 
							$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$PN_CODE', '$MAH_MARRY_NO', '$MAH_MARRY_ORG', '$PV_CODE', '$MR_CODE', '$MAH_BOOK_NO', '$MAH_BOOK_DATE', '$MAH_REMARK') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_MARRHIS where MAH_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_MARRHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_MARRHIS - $PER_MARRHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการเปลี่ยนชื่อ-สกุล  
		$cmd = " select max(NH_ID) as MAX_ID from PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, UPDATE_USER, UPDATE_DATE, 
							PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK FROM PER_NAMEHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY NH_ID ";
		else
			$cmd = " SELECT NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, UPDATE_USER, UPDATE_DATE, 
							PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK FROM PER_NAMEHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$NH_ORG = trim($data[NH_ORG]);
			$PN_CODE_NEW = trim($data[PN_CODE_NEW]);
			$NH_NAME_NEW = trim($data[NH_NAME_NEW]);
			$NH_SURNAME_NEW = trim($data[NH_SURNAME_NEW]);
			$NH_BOOK_NO = trim($data[NH_BOOK_NO]);
			$NH_BOOK_DATE = trim($data[NH_BOOK_DATE]);
			$NH_REMARK = trim($data[NH_REMARK]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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
							UPDATE_USER, UPDATE_DATE, PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK)
							VALUES ($MAX_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO', '$NH_ORG', '$PN_CODE_NEW', '$NH_NAME_NEW', '$NH_SURNAME_NEW', '$NH_BOOK_NO', '$NH_BOOK_DATE', '$NH_REMARK') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_NAMEHIS where NH_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_NAMEHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_NAMEHIS - $PER_NAMEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติรับพระราชทานเครื่องราชฯ  
		$cmd = " select max(DEH_ID) as MAX_ID from PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT DEH_ID, PER_ID, DC_CODE, DEH_DATE, DEH_GAZETTE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, 
							DEH_RETURN_DATE, DEH_RETURN_TYPE, DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION, DEH_ORG 
							FROM PER_DECORATEHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY DEH_ID ";
		else
			$cmd = " SELECT DEH_ID, PER_ID, DC_CODE, DEH_DATE, DEH_GAZETTE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, 
							DEH_RETURN_DATE, DEH_RETURN_TYPE, DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION, DEH_ORG 
							FROM PER_DECORATEHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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
			$DEH_RECEIVE_FLAG = $data[DEH_RECEIVE_FLAG] + 0;
			$DEH_RETURN_FLAG = $data[DEH_RETURN_FLAG] + 0;
			$DEH_RETURN_DATE = trim($data[DEH_RETURN_DATE]);
			$DEH_RETURN_TYPE = $data[DEH_RETURN_TYPE] + 0;
			$DEH_RECEIVE_DATE = trim($data[DEH_RECEIVE_DATE]);
			$DEH_BOOK_NO = trim($data[DEH_BOOK_NO]);
			$DEH_BOOK_DATE = trim($data[DEH_BOOK_DATE]);
			$DEH_REMARK = trim($data[DEH_REMARK]);
			$DEH_POSITION = trim($data[DEH_POSITION]);
			$DEH_ORG = trim($data[DEH_ORG]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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
							UPDATE_DATE, PER_CARDNO, DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION, DEH_ORG)
							VALUES ($MAX_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$DEH_GAZETTE', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO', $DEH_RECEIVE_FLAG, $DEH_RETURN_FLAG, '$DEH_RETURN_DATE', $DEH_RETURN_TYPE, '$DEH_RECEIVE_DATE', '$DEH_BOOK_NO', '$DEH_BOOK_DATE', '$DEH_REMARK', '$DEH_POSITION', '$DEH_ORG') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_DECORATEHIS where DEH_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_DECORATEHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_DECORATEHIS - $PER_DECORATEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// เวลาทวีคูณ  
		$cmd = " select max(TIMEH_ID) as MAX_ID from PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if($search_pv_code)
			$cmd = " SELECT TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, UPDATE_USER, UPDATE_DATE, PER_CARDNO, TIMEH_BOOK_NO, TIMEH_BOOK_DATE 
							FROM PER_TIMEHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b, PER_ORG c
							WHERE a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPT_ID $where and c.PV_CODE = '$search_pv_code') 
							ORDER BY TIMEH_ID ";
		else
			$cmd = " SELECT TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, UPDATE_USER, UPDATE_DATE, PER_CARDNO, TIMEH_BOOK_NO, TIMEH_BOOK_DATE 
							FROM PER_TIMEHIS 
							WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $DEPT_ID $where) 
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

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_ID' ";
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
							UPDATE_DATE, PER_CARDNO, TIMEH_BOOK_NO, TIMEH_BOOK_DATE)
							VALUES ($MAX_ID, $PER_ID, '$TIME_CODE', $TIMEH_MINUS, '$TIMEH_REMARK', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO', '$TIMEH_BOOK_NO', '$TIMEH_BOOK_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " select PER_ID from PER_TIMEHIS where TIMEH_ID = $MAX_ID ";
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TIMEHIS 
						where PER_ID in (select PER_ID from PER_PERSONAL where DEPARTMENT_ID = $search_department_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TIMEHIS - $PER_TIMEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// select * from per_educate where per_id in (select per_id from per_personal where department_id = 3134) order by edu_id
	} // end if

?>