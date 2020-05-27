<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");
  	if(strtoupper($dpisdb_user)=="HISDB" || strtoupper($dpisdb_user)=="SES" || strtoupper($dpisdb_user)=="ISCS") 
		$where = " AND (TEMPLEVEL IN ('09','10','11','O4','K4','K5','D1','D2','M1','M2','S1','S2','SES1','SES2'))";
  	else 
		$where = "";

	if ($search_org_id) {
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b WHERE a.POS_ID = b.POS_ID and 
											b.DEPARTMENT_ID = $search_department_id and b.ORG_ID = $search_org_id) ";
		$where_org_id = " (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id) ";
	} else {
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$where_org_id = " (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
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

	if( $command=='MASTER' ) {  // ข้อมูลหลัก 
// จังหวัด
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PROVINCE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPPROVINCE	FROM DPIS 
						  WHERE TEMPPROVINCE IS NOT NULL $where ORDER BY TEMPPROVINCE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPPROVINCE = trim($data[TEMPPROVINCE]);
			$arr_temp = explode("จังหวัด", $TEMPPROVINCE);
			$PV_NAME = $arr_temp[1];

			$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME = '$TEMPPROVINCE' or PV_NAME = '$PV_NAME' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[PV_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_PROVINCE', '$TEMPPROVINCE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_PROVINCE', '$PV_NAME', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				echo "จังหวัด $TEMPPROVINCE<br>";
//				$cmd = " INSERT INTO PER_PROVINCE (PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, UPDATE_DATE)
//								VALUES ('$TEMPPROVINCE', '$TEMPPROVINCE', '140', 1, $UPDATE_USER, '$UPDATE_DATE') ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// สังกัด  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_TYPE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPORGANIZETYPE FROM DPIS 
						  WHERE TEMPORGANIZETYPE IS NOT NULL $where ORDER BY TEMPORGANIZETYPE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPORGANIZETYPE = trim($data[TEMPORGANIZETYPE]);

			$cmd = " select OT_CODE from PER_ORG_TYPE where OT_NAME = '$TEMPORGANIZETYPE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[OT_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_ORG_TYPE', '$TEMPORGANIZETYPE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				echo "สังกัด $TEMPORGANIZETYPE<br>";
//				$cmd = " INSERT INTO PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
//								VALUES ('$OT_CODE', '$OT_NAME', $OT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ชื่อตำแหน่งในสายงาน  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_LINE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPLINE FROM DPIS WHERE TEMPLINE IS NOT NULL $where ORDER BY TEMPLINE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPLINE = trim($data[TEMPLINE]);

			$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$TEMPLINE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[PL_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_LINE', '$TEMPLINE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				if ($TEMPLINE=="ผู้อำนวยการเฉพาะด้าน วิชาการภาษี") {
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_LINE', '$TEMPLINE', '521009', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
//					$db_dpis->show_error();
				} elseif ($TEMPLINE=="ผู้อำนวยการเฉพาะด้าน วิทยาการคอมพิวเตอร์") {
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_LINE', '$TEMPLINE', '511019', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
//					$db_dpis->show_error();
				} elseif ($TEMPLINE=="เจ้าพนักงาน โสตทัศนศึกษา") {
					$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
										VALUES ('PER_LINE', '$TEMPLINE', '532512', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
//					$db_dpis->show_error();
				} else {
					echo "ชื่อตำแหน่งในสายงาน $TEMPLINE<br>";
//				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
//								PL_TYPE)
//								VALUES ('$TEMPLINE', '$TEMPLINE', '$TEMPLINE', 1, $UPDATE_USER, '$UPDATE_DATE', $PL_TYPE) ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
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
//		$db_dpis->show_error() ;

		$cmd = " select max(to_number(PM_CODE)) as MAX_ID from PER_MGT ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT DISTINCT TEMPMANAGEPOSITION	FROM DPIS 
						  WHERE TEMPMANAGEPOSITION IS NOT NULL $where ORDER BY TEMPMANAGEPOSITION ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPMANAGEPOSITION = trim($data[TEMPMANAGEPOSITION]);
			$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$TEMPMANAGEPOSITION' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[PM_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_MGT', '$TEMPMANAGEPOSITION', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				$cmd = " INSERT INTO PER_MGT (PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, 
								UPDATE_DATE)
								VALUES ('$MAX_ID', '$TEMPMANAGEPOSITION', NULL, '13', 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
				$MAX_ID++;
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภทตำแหน่ง 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TYPE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " select max(to_number(PT_CODE)) as MAX_ID from PER_TYPE ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT DISTINCT TEMPPOSITIONTYPE FROM DPIS 
						  WHERE TEMPPOSITIONTYPE IS NOT NULL $where ORDER BY TEMPPOSITIONTYPE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPPOSITIONTYPE = trim($data[TEMPPOSITIONTYPE]);

			$cmd = " select PT_CODE from PER_TYPE where PT_NAME = '$TEMPPOSITIONTYPE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[PT_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_TYPE', '$TEMPPOSITIONTYPE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				echo "ประเภทตำแหน่ง $TEMPPOSITIONTYPE<br>";
				$cmd = " INSERT INTO PER_TYPE (PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$MAX_ID', '$TEMPPOSITIONTYPE', 3, 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
				$MAX_ID++;
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// สาขาความเชี่ยวชาญ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SKILL' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " select max(SKILL_CODE) as MAX_ID from PER_SKILL ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT DISTINCT TEMPSKILL FROM DPIS WHERE TEMPSKILL IS NOT NULL $where ORDER BY TEMPSKILL ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPSKILL = trim($data[TEMPSKILL]);

			$cmd = " select SKILL_CODE from PER_SKILL where SKILL_NAME = '$TEMPSKILL' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[SKILL_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_SKILL', '$TEMPSKILL', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				echo "สาขาความเชี่ยวชาญ $TEMPSKILL<br>";
//				$cmd = " INSERT INTO PER_SKILL (SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, UPDATE_USER, UPDATE_DATE)
//								VALUES ('$MAX_ID', '$TEMPSKILL', '990', 1, $UPDATE_USER, '$UPDATE_DATE') ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ช่วงระดับตำแหน่ง  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CO_LEVEL' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPCLNAME FROM DPIS 
						  WHERE TEMPCLNAME IS NOT NULL $where ORDER BY TEMPCLNAME ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPCLNAME = trim($data[TEMPCLNAME]);

			$cmd = " select CL_NAME from PER_CO_LEVEL where CL_NAME = '$TEMPCLNAME' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[CL_NAME]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_CO_LEVEL', '$TEMPCLNAME', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				echo "ช่วงระดับตำแหน่ง $TEMPCLNAME<br>";
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE)
								VALUES ('$TEMPCLNAME', 'O1', 'O1', 1, $UPDATE_USER,	'$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// คำนำหน้าชื่อ  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PRENAME' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPPRENAME FROM DPIS WHERE TEMPPRENAME IS NOT NULL $where ORDER BY TEMPPRENAME ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPPRENAME = trim($data[TEMPPRENAME]);

			$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$TEMPPRENAME' or  PN_SHORTNAME = '$TEMPPRENAME' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[PN_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_PRENAME', '$TEMPPRENAME', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
					echo "คำนำหน้าชื่อ $TEMPPRENAME<br>";
//				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
//								PL_TYPE)
//								VALUES ('$TEMPLINE', '$TEMPLINE', '$TEMPLINE', 1, $UPDATE_USER, '$UPDATE_DATE', $PL_TYPE) ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประเภทการเคลื่อนไหว   
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MOVMENT' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPMOVEMENTTYPE FROM DPIS WHERE TEMPMOVEMENTTYPE IS NOT NULL $where ORDER BY TEMPMOVEMENTTYPE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPMOVEMENTTYPE = trim($data[TEMPMOVEMENTTYPE]);

			$cmd = " select MOV_CODE from PER_MOVMENT where MOV_NAME = '$TEMPMOVEMENTTYPE' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[MOV_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
									VALUES ('PER_MOVMENT', '$TEMPMOVEMENTTYPE', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
					echo "ประเภทการเคลื่อนไหว $TEMPMOVEMENTTYPE<br>";
//				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
//								PL_TYPE)
//								VALUES ('$TEMPLINE', '$TEMPLINE', '$TEMPLINE', 1, $UPDATE_USER, '$UPDATE_DATE', $PL_TYPE) ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	}

	if( $command=='ORG' ) { // โครงสร้างส่วนราชการ  
  		if(strtoupper($dpisdb_user)=="D07000" || strtoupper($dpisdb_user)=="SES") {
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

		}

		$table = array(	"per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", 
									"per_heir", "per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", 
									"per_decoratehis", "per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", 
									"per_coursedtl", "per_decordtl", "per_promote_p", "per_move_req", "per_absent", "per_family", "per_personal" );

		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		$cmd = " delete from per_servicehis where org_id in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_emp where org_id_1 in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_emp where org_id_2 in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_empser where org_id_1 in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_empser where org_id_2 in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_move_req where org_id_1 in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_move_req where org_id_2 in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_move_req where org_id_3 in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_move where org_id in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl2 where org_id in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl1 where org_id in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl2 where org_id in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl1 where org_id in $where_org_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_command where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		if ($search_org_id) 
			$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id ";
		else
			$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if ($search_org_id) 
			$cmd = " DELETE FROM PER_POS_EMP WHERE DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id ";
		else
			$cmd = " DELETE FROM PER_POS_EMP WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if ($search_org_id) 
			$cmd = " DELETE FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id ";
		else
			$cmd = " DELETE FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL10 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL9 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL8 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL7 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL6 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL5 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL4 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL3 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL2 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL1 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM WHERE ORG_ID IN $where_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM WHERE ORG_ID IN $where_org_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE PER_POSITIONHIS SET ORG_ID_3 = 1 WHERE ORG_ID_3 IN $where_org_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
		
		if ($search_org_id) 
			$cmd = " DELETE FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id ";
		else
			$cmd = " DELETE FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT DISTINCT DIVISIONNAME, TEMPPROVINCE FROM DPIS WHERE DIVISIONNAME IS NOT NULL ORDER BY DIVISIONNAME ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG++;
			$DIVISIONNAME = trim($data[DIVISIONNAME]);
			$TEMPPROVINCE = trim($data[TEMPPROVINCE]);
			$arr_temp = explode("จังหวัด", $TEMPPROVINCE);
			$PV_NAME = $arr_temp[1];
			$ORG_ID_REF = $search_department_id;

			if ($TEMPPROVINCE) {
				$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME = '$TEMPPROVINCE' or PV_NAME = '$PV_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PV_CODE = trim($data2[PV_CODE]);
			} else $PV_CODE = "";

			$cmd = " select ORG_ID from PER_ORG 
							  where ORG_NAME = '$DIVISIONNAME' and OL_CODE = '03' and DEPARTMENT_ID = $search_department_id ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[ORG_ID]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_ORG', '$DIVISIONNAME', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
								CT_CODE, PV_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
								VALUES ($MAX_ID, '$MAX_ID', '$DIVISIONNAME', '$DIVISIONNAME', '03', '01', 
								'140', '$PV_CODE', $ORG_ID_REF, 1, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";

				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_ORG', '$DIVISIONNAME', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				
//				$org_id = $search_org_id;
//				$search_org_id = $MAX_ID;
				$MAX_ID++;
//				$COUNT_NEW++;
			}
		} // end while						

		if ($search_org_id) 
			$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id ";
		else
			$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
	} // end if

	if( $command=='POSITION' ) { // ตำแหน่งข้าราชการ  
		$table = array(	"per_personal" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if ($search_org_id) 
			$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id ";
		else
			$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if ($search_org_id) $where = " AND DIVISIONNAME = '$search_org_name' ";
		if(strtoupper($dpis35db_name)=="D03004" || strtoupper($dpis35db_name)=="D21002")
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY 
							FROM DPIS ORDER BY TEMPPOSITIONNO ";
		elseif(strtoupper($dpis35db_name)=="D03007")
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY, OLDLEVELRAN 
							FROM DPIS ORDER BY TEMPPOSITIONNO ";
		elseif($search_pv_code)
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, 	TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY
							FROM DPIS WHERE TEMPPOSITIONNO IS NOT NULL $where AND TEMPPROVINCE = '$search_pv_name' ORDER BY TEMPPOSITIONNO ";
		else {
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY, TEMPCLNAME 
							FROM DPIS WHERE TEMPPOSITIONNO IS NOT NULL $where ORDER BY TEMPPOSITIONNO ";
			$count_data = $db_dpis35->send_cmd($cmd);
			if (!$count_data)
				$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
								TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY
								FROM DPIS WHERE TEMPPOSITIONNO IS NOT NULL $where ORDER BY TEMPPOSITIONNO ";
		}
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POSITION++;
			$DIVISIONNAME = trim($data[DIVISIONNAME]);
			$POS_NO = $data[TEMPPOSITIONNO] + 0;
			$TEMPORGANIZETYPE = trim($data[TEMPORGANIZETYPE]);
			$TEMPMANAGEPOSITION = trim($data[TEMPMANAGEPOSITION]);
			$TEMPLINE = trim($data[TEMPLINE]);
			$LEVEL_NO = trim($data[TEMPLEVEL]);
			$POS_SALARY = $data[TEMPSALARY] + 0;
			$POS_MGTSALARY = $data[TEMPPOSITIONSALARY] + 0;
			$TEMPSKILL = trim($data[TEMPSKILL]);
			$TEMPPOSITIONTYPE = trim($data[TEMPPOSITIONTYPE]);
			$TEMPPOSITIONSTATUS = $data[TEMPPOSITIONSTATUS];
			if(strtoupper($dpis35db_name)=="D03007")
				$CL_NAME = $data[OLDLEVELRAN];
			else
				$CL_NAME = $data[TEMPCLNAME];
			$TEMPPRENAME = trim($data[TEMPPRENAME]);
			$TEMPFIRSTNAME = trim($data[TEMPFIRSTNAME]);
			$TEMPLASTNAME = trim($data[TEMPLASTNAME]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DIVISIONNAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE] + 0;
			$ORG_ID_1 = 'NULL';
			$ORG_ID_2 = 'NULL';

			$PM_CODE = '';
			if ($TEMPMANAGEPOSITION) {
				$cmd = " select NEW_CODE from PER_MAP_CODE 
								  where MAP_CODE = 'PER_MGT' AND OLD_CODE = '$TEMPMANAGEPOSITION' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PM_CODE = trim($data2[NEW_CODE]);
				else echo "ชื่อตำแหน่งในการบริหารงาน $TEMPMANAGEPOSITION<br>";
			} 

			if ($TEMPLINE) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LINE' AND OLD_CODE = '$TEMPLINE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PL_CODE = trim($data2[NEW_CODE]);
				else echo "ชื่อตำแหน่งในสายงาน $TEMPLINE<br>";
			} else $PL_CODE = "NULL";

			if ($TEMPPOSITIONTYPE) {
				$cmd = " select NEW_CODE from PER_MAP_CODE 
								  where MAP_CODE = 'PER_TYPE' AND OLD_CODE = '$TEMPPOSITIONTYPE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PT_CODE = trim($data2[NEW_CODE]);
				else echo "ประเภทตำแหน่ง $TEMPPOSITIONTYPE<br>";
			} else $PT_CODE = "11";
			if ($TEMPPOSITIONTYPE=="ชช") $PT_CODE = "22"; 
			elseif ($TEMPPOSITIONTYPE=="บก") $PT_CODE = "31"; 
			elseif ($TEMPPOSITIONTYPE=="บส") $PT_CODE = "32"; 

			$SKILL_CODE = '';
			if ($TEMPSKILL) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SKILL' AND OLD_CODE = '$TEMPSKILL' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $SKILL_CODE = trim($data2[NEW_CODE]);
				else echo "สาขาความเชี่ยวชาญ $TEMPSKILL<br>";
			} 

				$POS_PER_NAME = $TEMPPRENAME . $TEMPFIRSTNAME . ' ' . $TEMPLASTNAME;
/*			} else {
				$cmd = " SELECT LEVEL_NO_MAX FROM PER_CO_LEVEL WHERE CL_NAME = '$CL_NAME' ";
				$count_data = $db_dpis352->send_cmd($cmd);
				//$db_dpis352->show_error();
				$data2 = $db_dpis352->get_array();
				$LEVEL_NO = trim($data2[LEVEL_NO_MAX]);
			} */
			if(strtoupper($dpis35db_name)=="D15003" || strtoupper($dpis35db_name)=="D10006" || strtoupper($dpis35db_name)=="D03005") {
				if ($LEVEL_NO=="S1" || $LEVEL_NO=="SES1") $LEVEL_NO = "M1";
				elseif ($LEVEL_NO=="S2" || $LEVEL_NO=="SES2") $LEVEL_NO = "M2";
				elseif ($LEVEL_NO=="M1") $LEVEL_NO = "D1";
				elseif ($LEVEL_NO=="M2") $LEVEL_NO = "D2"; 
			}

			if(strtoupper($dpis35db_name)=="D03004" || strtoupper($dpis35db_name)=="D21002") $CL_NAME = $LEVEL_NO;
			$cmd = " SELECT LV_NAME FROM PER_NEW_LEVEL WHERE LV_DESCRIPTION = '$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[LV_NAME])) $LEVEL_NO = trim($data2[LV_NAME]);
			if ($LEVEL_NO=="o1") $LEVEL_NO = "O1";
			elseif ($LEVEL_NO=="o2") $LEVEL_NO = "O2";
			elseif ($LEVEL_NO=="o3" || $LEVEL_NO=="O3-1") $LEVEL_NO = "O3";
			elseif ($LEVEL_NO=="o4") $LEVEL_NO = "O4";

			$POS_STATUS = 1;

			if ($PM_CODE && $PM_CODE != 'NULL') $PM_CODE = "'$PM_CODE'";
			else $PM_CODE = "NULL";
			if ($SKILL_CODE && $SKILL_CODE != 'NULL') $SKILL_CODE = "'$SKILL_CODE'";
			else $SKILL_CODE = "NULL";
			if (!$CL_NAME) $CL_NAME = "ปฏิบัติการ";

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, 
							PM_CODE, PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
							VALUES ($MAX_ID, $ORG_ID, '$POS_NO', '01', $ORG_ID_1, $ORG_ID_2, $PM_CODE, 
							'$PL_CODE', '$CL_NAME', $POS_SALARY, $POS_MGTSALARY, $SKILL_CODE, '$PT_CODE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
//			if ($MAX_ID == 5981) echo "$cmd<br>";

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POSITION', '$POS_NO', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		if ($search_org_id) 
			$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION where DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id ";
		else
			$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITION - $PER_POSITION - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
	} // end if

	if( $command=='PERSONAL' ) { // ข้อมูลข้าราชการ
		$table = array(	"per_personal" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if ($search_org_id) $where = " AND DIVISIONNAME = '$search_org_name' ";
		if($search_pv_code)
			$cmd = " SELECT TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPPOSITIONNO, TEMPLEVEL, TEMPSALARY, 
							TEMPPOSITIONSALARY, TEMPGENDER, TEMPCARDNO, TEMPBIRTHDATE, TEMPSTARTDATE, TEMPPROVINCE, 
							TEMPMOVEMENTTYPE 
							FROM DPIS WHERE TEMPFIRSTNAME IS NOT NULL $where AND TEMPPROVINCE = '$search_pv_name' ORDER BY TEMPPOSITIONNO ";
		else
			$cmd = " SELECT TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPPOSITIONNO, TEMPLEVEL, TEMPSALARY, 
							TEMPPOSITIONSALARY, TEMPGENDER, TEMPCARDNO, TEMPBIRTHDATE, TEMPSTARTDATE, TEMPPROVINCE, 
							TEMPMOVEMENTTYPE 
							FROM DPIS WHERE TEMPFIRSTNAME IS NOT NULL $where ORDER BY TEMPPOSITIONNO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$PER_TYPE = 1;
			$OT_CODE = '01';
			$TEMPPRENAME = trim($data[TEMPPRENAME]);
			$PER_NAME = trim($data[TEMPFIRSTNAME]);
			$PER_SURNAME = trim($data[TEMPLASTNAME]);
			$TEMPPOSITIONNO = $data[TEMPPOSITIONNO] + 0;
			$LEVEL_NO = trim($data[TEMPLEVEL]);
			$PER_SALARY = $data[TEMPSALARY] + 0;
			$PER_MGTSALARY = $data[TEMPPOSITIONSALARY] + 0;
			$TEMPGENDER = trim($data[TEMPGENDER]);
			if ($TEMPGENDER=='ชาย') $PER_GENDER = 1;
			elseif ($TEMPGENDER=='หญิง') $PER_GENDER = 2;
			$PER_CARDNO = trim($data[TEMPCARDNO]);
			$PER_BIRTHDATE = trim($data[TEMPBIRTHDATE]);
			$PER_STARTDATE = trim($data[TEMPSTARTDATE]);
			$TEMPPROVINCE = trim($data[TEMPPROVINCE]);
			$TEMPMOVEMENTTYPE = trim($data[TEMPMOVEMENTTYPE]);
			$PER_BIRTHDATE =  save_date($PER_BIRTHDATE);
			$PER_STARTDATE =  save_date($PER_STARTDATE);
			$temp_date = explode("/", trim($PER_STARTDATE));

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_PRENAME' AND OLD_CODE = '$TEMPPRENAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$TEMPPOSITIONNO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POS_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_PROVINCE' AND OLD_CODE = '$TEMPPROVINCE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_MOVMENT' AND OLD_CODE = '$TEMPMOVEMENTTYPE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MOV_CODE = trim($data2[NEW_CODE]);
			else $MOV_CODE = "101";

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
							NULL, NULL, NULL, $POS_ID, NULL, '$LEVEL_NO', 0, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '1', 
							'$PER_CARDNO', NULL, NULL, NULL, NULL, '$PER_BIRTHDATE', NULL,	'$PER_STARTDATE', '$PER_STARTDATE', 
							NULL, NULL, NULL,	NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$PV_CODE', '$MOV_CODE', NULL, NULL, NULL, 1, 
							$UPDATE_USER, '$UPDATE_DATE', $search_department_id, NULL, NULL, NULL, NULL, NULL, NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $MAX_ID ";
			$count_data = $db_dpis->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$POS_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while				

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where per_id in $where_per_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
	} // end if

?>