<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(!$current_page) $current_page = 1;
	
	if($command=="ADD" || $command=="UPDATE"){
		$EPK_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_NAME)));
		$EPK_BEHAVIOR = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_BEHAVIOR)));
		$EPK_COACH = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_COACH)));
		$EPK_TRAIN = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_TRAIN)));
		$EPK_JOB = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_JOB)));
	} // end if
	
	if($command=="ADD" && $EPK_NAME){
		$cmd = " select max(EPK_ID) as MAX_EPK_ID from EAF_PERSONAL_KNOWLEDGE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EPK_ID = $data[MAX_EPK_ID] + 1;
		
		$cmd = " insert into EAF_PERSONAL_KNOWLEDGE 
							(	EPK_ID, EP_ID, EPS_ID, EPK_NAME, EPK_BEHAVIOR, EPK_COACH, 
								EPK_TRAIN, EPK_JOB, UPDATE_USER, UPDATE_DATE )
						 values
							(	$EPK_ID, $EP_ID, $EPS_ID, '$EPK_NAME', '$EPK_BEHAVIOR', '$EPK_COACH',
								'$EPK_TRAIN', '$EPK_JOB', $SESS_USERID, '$UPDATE_DATE' )
						  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลรายละเอียดโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EP_ID : $EPS_ID : $EPK_ID ]");
	} // end if

	if($command=="UPDATE" && $EPK_ID && $EPK_NAME){
		$cmd = " update EAF_PERSONAL_KNOWLEDGE  set
							EPK_NAME = '$EPK_NAME', 
							EPK_BEHAVIOR = '$EPK_BEHAVIOR', 
							EPK_COACH = '$EPK_COACH', 
							EPK_TRAIN = '$EPK_TRAIN', 
							EPK_JOB = '$EPK_JOB', 
							UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where EP_ID=$EP_ID and EPS_ID=$EPS_ID and EPK_ID=$EPK_ID
					  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลรายละเอียดโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EP_ID : $EPS_ID : $EPK_ID ]");
	} // end if
	
	if($command=="DELETE" && $EPK_ID){
		$cmd = " delete from EAF_PERSONAL_KNOWLEDGE where EP_ID=$EP_ID and EPS_ID=$EPS_ID and EPK_ID=$EPK_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลรายละเอียดโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EP_ID : $EPS_ID : $EPK_ID ]");
	} // end if

	if($EPK_ID){
		if($DPISDB=="odbc"){
			$cmd = "	select		EPK_NAME, EPK_BEHAVIOR, EPK_COACH, EPK_TRAIN, EPK_JOB
							from			EAF_PERSONAL_KNOWLEDGE
							where		EP_ID=$EP_ID and EPS_ID=$EPS_ID and EPK_ID=$EPK_ID
					     ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		EPK_NAME, EPK_BEHAVIOR, EPK_COACH, EPK_TRAIN, EPK_JOB
							from			EAF_PERSONAL_KNOWLEDGE
							where		EP_ID=$EP_ID and EPS_ID=$EPS_ID and EPK_ID=$EPK_ID
					     ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		
		$EPK_NAME = $data[EPK_NAME];
		$EPK_BEHAVIOR = $data[EPK_BEHAVIOR];
		$EPK_COACH = $data[EPK_COACH];
		$EPK_TRAIN = $data[EPK_TRAIN];
		$EPK_JOB = $data[EPK_JOB];
	} // end if
	
	if( (!$VIEW && !$UPD && !$DEL && !$err_text) ){
		$EPK_ID = "";
		$EPK_NAME = "";
		$EPK_BEHAVIOR = "";
		$EPK_COACH = "";
		$EPK_TRAIN = "";
		$EPK_JOB = "";
	} // end if

?>