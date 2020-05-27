<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(!$current_page) $current_page = 1;
	
	$cmd = " select EAF_NAME from EAF_MASTER where EAF_ID=$EAF_ID ";
	$db->send_cmd($cmd);
	$data = $db->get_array();
	$EAF_NAME = $data[EAF_NAME];
	
	if($command=="ADD" || $command=="UPDATE"){
		$ELK_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ELK_NAME)));
		$ELK_BEHAVIOR = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ELK_BEHAVIOR)));
		$ELK_COACH = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ELK_COACH)));
		$ELK_TRAIN = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ELK_TRAIN)));
		$ELK_JOB = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ELK_JOB)));
	} // end if
	
	if($command=="ADD" && $ELK_NAME){
		$cmd = " select max(ELK_ID) as MAX_ELK_ID from EAF_LEARNING_KNOWLEDGE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ELK_ID = $data[MAX_ELK_ID] + 1;
		
		$cmd = " insert into EAF_LEARNING_KNOWLEDGE 
							(	ELK_ID, EAF_ID, ELS_ID, ELK_NAME, ELK_BEHAVIOR, ELK_COACH, 
								ELK_TRAIN, ELK_JOB, UPDATE_USER, UPDATE_DATE )
						 values
							(	$ELK_ID, $EAF_ID, $ELS_ID, '$ELK_NAME', '$ELK_BEHAVIOR', '$ELK_COACH',
								'$ELK_TRAIN', '$ELK_JOB', $SESS_USERID, '$UPDATE_DATE' )
						  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลรายละเอียดโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EAF_ID : $ELS_ID : $ELK_ID ]");
	} // end if

	if($command=="UPDATE" && $ELK_ID && $ELK_NAME){
		$cmd = " update EAF_LEARNING_KNOWLEDGE  set
							ELK_NAME = '$ELK_NAME', 
							ELK_BEHAVIOR = '$ELK_BEHAVIOR', 
							ELK_COACH = '$ELK_COACH', 
							ELK_TRAIN = '$ELK_TRAIN', 
							ELK_JOB = '$ELK_JOB', 
							UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where EAF_ID=$EAF_ID and ELS_ID=$ELS_ID and ELK_ID=$ELK_ID
					  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลรายละเอียดโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EAF_ID : $ELS_ID : $ELK_ID ]");
	} // end if
	
	if($command=="DELETE" && $ELK_ID){
		$cmd = " delete from EAF_LEARNING_KNOWLEDGE where EAF_ID=$EAF_ID and ELS_ID=$ELS_ID and ELK_ID=$ELK_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลรายละเอียดโครงสร้างประสบการณ์ [ $DEPARTMENT_ID : $EAF_ID : $ELS_ID : $ELK_ID ]");
	} // end if

	if($ELK_ID){
		if($DPISDB=="odbc"){
			$cmd = "	select		ELK_NAME, ELK_BEHAVIOR, ELK_COACH, ELK_TRAIN, ELK_JOB
							from			EAF_LEARNING_KNOWLEDGE
							where		EAF_ID=$EAF_ID and ELS_ID=$ELS_ID and ELK_ID=$ELK_ID
					     ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		ELK_NAME, ELK_BEHAVIOR, ELK_COACH, ELK_TRAIN, ELK_JOB
							from			EAF_LEARNING_KNOWLEDGE
							where		EAF_ID=$EAF_ID and ELS_ID=$ELS_ID and ELK_ID=$ELK_ID
					     ";
		} // end if
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		
		$ELK_NAME = $data[ELK_NAME];
		$ELK_BEHAVIOR = $data[ELK_BEHAVIOR];
		$ELK_COACH = $data[ELK_COACH];
		$ELK_TRAIN = $data[ELK_TRAIN];
		$ELK_JOB = $data[ELK_JOB];
	} // end if
	
	if( (!$VIEW && !$UPD && !$DEL && !$err_text) ){
		$ELK_ID = "";
		$ELK_NAME = "";
		$ELK_BEHAVIOR = "";
		$ELK_COACH = "";
		$ELK_TRAIN = "";
		$ELK_JOB = "";
	} // end if

?>