<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(!$current_page) $current_page = 1;
	
	if(!$EPS_LEVEL) $EPS_LEVEL = 1;
		
	if($command=="ADD" || $command=="UPDATE"){
		if(!trim($ORG_ID)) $ORG_ID = "NULL";
		if(!trim($ORG_ID_1)) $ORG_ID_1 = "NULL";
		if(!trim($ORG_ID_2)) $ORG_ID_2 = "NULL";
		if(!trim($EPS_PERIOD)) $EPS_PERIOD = "NULL";
		$EPS_SEQ_NO += 0;
		$START_DATE =  save_date($START_DATE);
		$END_DATE =  save_date($END_DATE);
	} // end if
	
	if($command=="ADD" && $EPS_LEVEL && $DEPARTMENT_ID){
		$cmd = " select max(EPS_ID) as MAX_EPS_ID from EAF_PERSONAL_STRUCTURE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EPS_ID = $data[MAX_EPS_ID] + 1;
		
		$cmd = " insert into EAF_PERSONAL_STRUCTURE 
							(	EPS_ID, EP_ID, MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, 
								EPS_LEVEL, EPS_PERIOD, EPS_SEQ_NO, UPDATE_USER, UPDATE_DATE, START_DATE, END_DATE )
						 values
							(	$EPS_ID, $EP_ID, $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2,
								$EPS_LEVEL, $EPS_PERIOD, $EPS_SEQ_NO, $SESS_USERID, '$UPDATE_DATE', '$START_DATE', '$END_DATE' ) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EP_ID : $EPS_ID ]");
	} // end if

	if($command=="UPDATE" && $EPS_ID && $EPS_LEVEL && $DEPARTMENT_ID){
		$cmd = " update EAF_PERSONAL_STRUCTURE  set
							MINISTRY_ID = $MINISTRY_ID,
							DEPARTMENT_ID = $DEPARTMENT_ID,
							ORG_ID = $ORG_ID, 
							ORG_ID_1 = $ORG_ID_1, 
							ORG_ID_2 = $ORG_ID_2, 
							EPS_LEVEL = $EPS_LEVEL, 
							EPS_PERIOD = $EPS_PERIOD, 
							EPS_SEQ_NO = $EPS_SEQ_NO,
							START_DATE= '$START_DATE', 
							END_DATE= '$END_DATE', 
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE'
						 where EP_ID=$EP_ID and EPS_ID=$EPS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EP_ID : $EPS_ID ]");
	} // end if
	
	if($command=="DELETE" && $EPS_ID){
		$cmd = " delete from EAF_PERSONAL_STRUCTURE where EP_ID=$EP_ID and EPS_ID=$EPS_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EP_ID : $EPS_ID ]");
	} // end if

	if($EPS_ID){
		if($DPISDB=="odbc"){
			$cmd = "	select		MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2,
											EPS_SEQ_NO, EPS_LEVEL, EPS_PERIOD, START_DATE, END_DATE
							from			EAF_PERSONAL_STRUCTURE
							where		EP_ID=$EP_ID and EPS_ID=$EPS_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2,
											EPS_SEQ_NO, EPS_LEVEL, EPS_PERIOD, START_DATE, END_DATE
							from			EAF_PERSONAL_STRUCTURE
							where		EP_ID=$EP_ID and EPS_ID=$EPS_ID ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		
		$MINISTRY_ID = $data[MINISTRY_ID];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$MINISTRY_NAME = trim($data_dpis2[ORG_NAME]);

		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$DEPARTMENT_NAME = trim($data_dpis2[ORG_NAME]);			
		
		$ORG_ID = trim($data[ORG_ID]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME = trim($data_dpis2[ORG_NAME]);

		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_1 = trim($data_dpis2[ORG_NAME]);

		$ORG_ID_2 = trim($data[ORG_ID_2]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_2 = trim($data_dpis2[ORG_NAME]);
		
		$EPS_SEQ_NO = $data[EPS_SEQ_NO];
		$EPS_LEVEL = $data[EPS_LEVEL];
		$EPS_PERIOD = $data[EPS_PERIOD];
		$START_DATE = show_date_format($data[START_DATE], 1);
		$END_DATE = show_date_format($data[END_DATE], 1);
	} // end if
	
	if( (!$VIEW && !$UPD && !$DEL && !$err_text) ){
		$EPS_ID = "";
		$EPS_SEQ_NO = "";
		$EPS_LEVEL = 1;
		$EPS_PERIOD = "";
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
		$ORG_ID = "";
		$ORG_NAME = "";
		$ORG_ID_1 = "";
		$ORG_NAME_1 = "";
		$ORG_ID_2 = "";
		$ORG_NAME_2 = "";
		$START_DATE = "";
		$END_DATE = "";
	} // end if

?>