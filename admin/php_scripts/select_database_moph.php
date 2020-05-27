<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis352 = new connect_dpis35($dpis35db_host, $dpis35db_name, $dpis35db_user, $dpis35db_pwd);
	$UPDATE_USER = 1;
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
// จังหวัด
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PROVINCE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPPROVINCE	FROM DPIS 
						  WHERE TEMPPROVINCE IS NOT NULL ORDER BY TEMPPROVINCE ";
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

// สังกัด  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_TYPE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPORGANIZETYPE FROM DPIS 
						  WHERE TEMPORGANIZETYPE IS NOT NULL ORDER BY TEMPORGANIZETYPE ";
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

// ชื่อตำแหน่งในสายงาน  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_LINE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPLINE FROM DPIS WHERE TEMPLINE IS NOT NULL ORDER BY TEMPLINE ";
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
				echo "ชื่อตำแหน่งในสายงาน $TEMPLINE<br>";
//				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
//								PL_TYPE)
//								VALUES ('$TEMPLINE', '$TEMPLINE', '$TEMPLINE', 1, $UPDATE_USER, '$UPDATE_DATE', $PL_TYPE) ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						

// ชื่อตำแหน่งในการบริหารงาน  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MGT' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPMANAGEPOSITION	FROM DPIS 
						  WHERE TEMPMANAGEPOSITION IS NOT NULL ORDER BY TEMPMANAGEPOSITION ";
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
				echo "ชื่อตำแหน่งในการบริหารงาน $TEMPMANAGEPOSITION<br>";
//				$cmd = " INSERT INTO PER_MGT (PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, 
//								UPDATE_DATE)
//								VALUES ('$TEMPMANAGEPOSITION', '$TEMPMANAGEPOSITION', '$TEMPMANAGEPOSITION', '$PS_CODE', 1, $UPDATE_USER, 
//								'$UPDATE_DATE') ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						

// ประเภทตำแหน่ง 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TYPE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPPOSITIONTYPE FROM DPIS 
						  WHERE TEMPPOSITIONTYPE IS NOT NULL ORDER BY TEMPPOSITIONTYPE ";
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
//				$cmd = " INSERT INTO PER_TYPE (PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, UPDATE_USER, UPDATE_DATE)
//								VALUES ('$PT_CODE', '$PT_NAME', $PT_GROUP, $PT_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						

// สาขาความเชี่ยวชาญ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SKILL' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " select max(SKILL_CODE) as MAX_ID from PER_SKILL ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT DISTINCT TEMPSKILL FROM DPIS WHERE TEMPSKILL IS NOT NULL ORDER BY TEMPSKILL ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TEMPSKILL = trim($data[TEMPSKILL]);

			$cmd = " select SKILL_CODE from PER_SKILL where PC_NAME = '$TEMPSKILL' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[SKILL_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_CONDITION', '$TEMPSKILL', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
			} else {
				echo "สาขาความเชี่ยวชาญ $TEMPSKILL<br>";
//				$cmd = " INSERT INTO PER_SKILL (SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, UPDATE_USER, UPDATE_DATE)
//								VALUES ('$MAX_ID', '$TEMPSKILL', '990', 1, $UPDATE_USER, '$UPDATE_DATE') ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						

// ช่วงระดับตำแหน่ง  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_CO_LEVEL' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT TEMPCLNAME FROM DPIS WHERE TEMPCLNAME IS NOT NULL ORDER BY TEMPCLNAME ";
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
//				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
//								UPDATE_DATE)
//								VALUES ('$CL_NAME', '$LEVEL_NO_MIN', '$LEVEL_NO_MAX', $CL_ACTIVE, $UPDATE_USER,	'$UPDATE_DATE') ";
//				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						

	}

	if( $command=='ORG' ) { // โครงสร้างส่วนราชการ  
		$table = array(	"per_personal" );
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " delete from PER_ORG WHERE DEPARTMENT_ID = $search_department_id AND ORG_CODE IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT DISTINCT DIVISIONNAME FROM DPIS WHERE DIVISIONNAME IS NOT NULL ORDER BY DIVISIONNAME ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ORG++;
			$DIVISIONNAME = trim($data[DIVISIONNAME]);
			$ORG_ID_REF = $search_department_id;

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
								CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
								VALUES ($MAX_ID, '$ORG_CODE', '$DIVISIONNAME', '$DIVISIONNAME', '03', '01', 
								'140', $ORG_ID_REF, 1, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				echo "$cmd<br>";

				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_ORG', '$DIVISIONNAME', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$MAX_ID++;
				$COUNT_NEW++;
			}
		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG - $COUNT_NEW<br>";

	} // end if

	if( $command=='POSITION' ) { // ตำแหน่งข้าราชการ  
		$table = array(	"per_personal" );
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

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

		if(strtoupper($dpis35db_name)=="D03004" || strtoupper($dpis35db_name)=="D21002")
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY 
							FROM DPIS ORDER BY TEMPPOSITIONNO ";
		elseif(strtoupper($dpis35db_name)=="D03007")
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY, OLDLEVELRAN 
							FROM DPIS ORDER BY TEMPPOSITIONNO ";
		else
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY, TEMPCLNAME 
							FROM DPIS ORDER BY TEMPPOSITIONNO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POSITION++;
			$DIVISIONNAME = trim($data[DIVISIONNAME]);
			$POS_NO = trim($data[TEMPPOSITIONNO]);
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

			if ($TEMPMANAGEPOSITION) {
				$cmd = " select NEW_CODE from PER_MAP_CODE 
								  where MAP_CODE = 'PER_MGT' AND OLD_CODE = '$TEMPMANAGEPOSITION' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PM_CODE = trim($data2[NEW_CODE]);
				else echo "ชื่อตำแหน่งในการบริหารงาน $TEMPMANAGEPOSITION<br>";
			} else $PM_CODE = "NULL";

			if ($TEMPLINE=="เจ้าพนักงานวิทยาศาสตร์ การแพทย์") $TEMPLINE="เจ้าพนักงานวิทยาศาสตร์การแพทย์";	
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
			} else $PT_CODE = "NULL";
			if ($TEMPPOSITIONTYPE=="ชช") $PT_CODE = "22"; 
			elseif ($TEMPPOSITIONTYPE=="บก") $PT_CODE = "31"; 
			elseif ($TEMPPOSITIONTYPE=="บส") $PT_CODE = "32"; 

			if ($TEMPSKILL) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SKILL' AND OLD_CODE = '$TEMPSKILL' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $SKILL_CODE = trim($data2[NEW_CODE]);
				else echo "สาขาความเชี่ยวชาญ $TEMPSKILL<br>";
			} else $SKILL_CODE = "NULL";

				$POS_PER_NAME = $TEMPPRENAME . $TEMPFIRSTNAME . ' ' . $TEMPLASTNAME;
/*			} else {
				$cmd = " SELECT LEVEL_NO_MAX FROM PER_CO_LEVEL WHERE CL_NAME = '$CL_NAME' ";
				$count_data = $db_dpis352->send_cmd($cmd);
				//$db_dpis352->show_error();
				$data2 = $db_dpis352->get_array();
				$LEVEL_NO = trim($data2[LEVEL_NO_MAX]);
			} */
			if(strtoupper($dpis35db_name)=="D03004" || strtoupper($dpis35db_name)=="D03007" || strtoupper($dpis35db_name)=="D10") {
				if ($LEVEL_NO=="D1") $LEVEL_NO = "M1";
				elseif ($LEVEL_NO=="D2") $LEVEL_NO = "M2";
				elseif ($LEVEL_NO=="M1") $LEVEL_NO = "SES1";
				elseif ($LEVEL_NO=="M2") $LEVEL_NO = "SES2"; 
			}

			if(strtoupper($dpis35db_name)=="D03004" || strtoupper($dpis35db_name)=="D21002") $CL_NAME = $LEVEL_NO;
			$cmd = " SELECT LV_NAME FROM PER_NEW_LEVEL WHERE LV_DESCRIPTION = '$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[LV_NAME])) $LEVEL_NO = trim($data2[LV_NAME]);
			if ($LEVEL_NO=="S1") $LEVEL_NO = "SES1";
			elseif ($LEVEL_NO=="S2") $LEVEL_NO = "SES2";
			elseif ($LEVEL_NO=="o1") $LEVEL_NO = "O1";
			elseif ($LEVEL_NO=="o2") $LEVEL_NO = "O2";
			elseif ($LEVEL_NO=="o3" || $LEVEL_NO=="O3-1") $LEVEL_NO = "O3";
			elseif ($LEVEL_NO=="o4") $LEVEL_NO = "O4";

			$POS_STATUS = 1;

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO_INT, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, 
							PM_CODE, PL_CODE, CL_NAME, LV_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, LEVEL_NO, POS_PER_NAME)
							VALUES ($MAX_ID, $ORG_ID, $POS_NO, '$POS_NO', '01', $ORG_ID_1, $ORG_ID_2, '$PM_CODE', 
							'$PL_CODE', '$CL_NAME', '$LEVEL_NO', $POS_SALARY, $POS_MGTSALARY, '$SKILL_CODE', '$PT_CODE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id, '$LEVEL_NO', 
							'$POS_PER_NAME') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($POS_NO == "7") echo "$cmd<br>";

			$MAX_ID++;
		} // end while						

		$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITION - $PER_POSITION - $COUNT_NEW<br>";

	} // end if

?>