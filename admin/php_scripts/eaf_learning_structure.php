<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(!$current_page) $current_page = 1;
	
	$cmd = " select EAF_NAME from EAF_MASTER where EAF_ID=$EAF_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$EAF_NAME = $data[EAF_NAME];
	
	if(!$ELS_LEVEL) $ELS_LEVEL = 1;
		
	if($command=="ADD" || $command=="UPDATE"){
		if(!trim($ORG_ID)) $ORG_ID = "NULL";
		if(!trim($ORG_ID_1)) $ORG_ID_1 = "NULL";
		if(!trim($ORG_ID_2)) $ORG_ID_2 = "NULL";
		if(!trim($ELS_PERIOD)) $ELS_PERIOD = "NULL";
		$ELS_SEQ_NO += 0;
	} // end if
	
	if($command=="ADD" && $ELS_LEVEL && $DEPARTMENT_ID){
		$cmd = " select max(ELS_ID) as MAX_ELS_ID from EAF_LEARNING_STRUCTURE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ELS_ID = $data[MAX_ELS_ID] + 1;
		
		$cmd = " insert into EAF_LEARNING_STRUCTURE 
							(	ELS_ID, EAF_ID, MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, 
								ELS_LEVEL, ELS_PERIOD, ELS_SEQ_NO, UPDATE_USER, UPDATE_DATE )
						 values
							(	$ELS_ID, $EAF_ID, $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2,
								$ELS_LEVEL, $ELS_PERIOD, $ELS_SEQ_NO, $SESS_USERID, '$UPDATE_DATE' )
						  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EAF_ID : $ELS_ID ]");
	} // end if

	if($command=="UPDATE" && $ELS_ID && $ELS_LEVEL && $DEPARTMENT_ID){
		$cmd = " update EAF_LEARNING_STRUCTURE  set
							MINISTRY_ID = $MINISTRY_ID,
							DEPARTMENT_ID = $DEPARTMENT_ID,
							ORG_ID = $ORG_ID, 
							ORG_ID_1 = $ORG_ID_1, 
							ORG_ID_2 = $ORG_ID_2, 
							ELS_LEVEL = $ELS_LEVEL, 
							ELS_PERIOD = $ELS_PERIOD, 
							ELS_SEQ_NO = $ELS_SEQ_NO,
							UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where EAF_ID=$EAF_ID and ELS_ID=$ELS_ID
					  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EAF_ID : $ELS_ID ]");
	} // end if
	
	if($command=="DELETE" && $ELS_ID){
		$cmd = " delete from EAF_LEARNING_STRUCTURE where EAF_ID=$EAF_ID and ELS_ID=$ELS_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EAF_ID : $ELS_ID ]");
	} // end if

	if($ELS_ID){
		if($DPISDB=="odbc"){
			$cmd = "	select		MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2,
											ELS_SEQ_NO, ELS_LEVEL, ELS_PERIOD
							from			EAF_LEARNING_STRUCTURE
							where		EAF_ID=$EAF_ID and ELS_ID=$ELS_ID
					     ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2,
											ELS_SEQ_NO, ELS_LEVEL, ELS_PERIOD
							from			EAF_LEARNING_STRUCTURE
							where		EAF_ID=$EAF_ID and ELS_ID=$ELS_ID
					     ";
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
		
		$ELS_SEQ_NO = $data[ELS_SEQ_NO];
		$ELS_LEVEL = $data[ELS_LEVEL];
		$ELS_PERIOD = $data[ELS_PERIOD];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$ELS_ID = "";
		$ELS_SEQ_NO = "";
		$ELS_LEVEL = 1;
		$ELS_PERIOD = "";
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
	} // end if

?>