<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
		
	if($command=="ADD" || $command=="UPDATE"){
		$EAF_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EAF_NAME)));
		$EAF_ROLE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EAF_ROLE)));
		$EAF_REMARK = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EAF_REMARK)));
	
		if(!trim($ORG_ID)) $ORG_ID = "NULL";
		if(trim($PM_CODE)) $PM_CODE = "'$PM_CODE'";
		else $PM_CODE = "NULL";
		
		if(!$EAF_NAME) $EAF_NAME = ($PM_NAME)?"$PM_NAME ($PL_NAME $LEVEL_NO $PT_NAME)":"$PL_NAME ($LEVEL_NO $PT_NAME)";
		$EAF_DATE =  save_date($EAF_DATE);
	} // end if
	
	if($command=="ADD"  && $DEPARTMENT_ID){
		$cmd = " select max(EAF_ID) as MAX_EAF_ID from EAF_MASTER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EAF_ID = $data[MAX_EAF_ID] + 1;
		
		if(!$EAF_REF_ID) $EAF_REF_ID = 0;
		
		$cmd = " insert into EAF_MASTER 
							(	EAF_ID, PL_CODE, LEVEL_NO, PM_CODE, PT_CODE, DEPARTMENT_ID, ORG_ID, 
								EAF_NAME, EAF_ROLE, EAF_REMARK, EAF_REF_ID, EAF_ACTIVE, 
								UPDATE_USER, UPDATE_DATE, EAF_DATE, EAF_YEAR, EAF_MONTH )
						 values
							(	$EAF_ID, '$PL_CODE', '$LEVEL_NO', $PM_CODE, '$PT_CODE', $DEPARTMENT_ID, $ORG_ID,
								'$EAF_NAME', '$EAF_ROLE', '$EAF_REMARK', $EAF_REF_ID, $EAF_ACTIVE,
								$SESS_USERID, '$UPDATE_DATE', '$EAF_DATE', $EAF_YEAR, $EAF_MONTH ) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//die;
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลกรอบการสั่งสมประสบการณ์ [ $DEPARTMENT_ID : $EAF_ID : $EAF_NAME ]");
	} // end if

	if($command=="UPDATE" && $EAF_ID && $EAF_NAME && $DEPARTMENT_ID){
		$cmd = " update EAF_MASTER  set
							PL_CODE = '$PL_CODE', 
							LEVEL_NO = '$LEVEL_NO', 
							PM_CODE = $PM_CODE, 
							PT_CODE = '$PT_CODE', 
							DEPARTMENT_ID = $DEPARTMENT_ID,
							ORG_ID = $ORG_ID, 
							EAF_NAME = '$EAF_NAME', 
							EAF_ROLE = '$EAF_ROLE', 
							EAF_REMARK = '$EAF_REMARK',
							EAF_ACTIVE = $EAF_ACTIVE,
							EAF_DATE = '$EAF_DATE',
							EAF_YEAR = $EAF_YEAR,
							EAF_MONTH = $EAF_MONTH,
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE'
						 where EAF_ID=$EAF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลกรอบการสั่งสมประสบการณ์ [ $DEPARTMENT_ID : $EAF_ID : $EAF_NAME ]");
	} // end if
	
	if($command=="DELETE" && $EAF_ID){
		$cmd = " select	 EAF_NAME from EAF_MASTER where EAF_ID=$EAF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EAF_NAME = $data[EAF_NAME];

		$cmd = " delete from EAF_MASTER where EAF_ID=$EAF_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลกรอบการสั่งสมประสบการณ์ [ $DEPARTMENT_ID : $EAF_ID : $EAF_NAME ]");
	} // end if

	if($EAF_ID){
		$cmd = "	select		PL_CODE, LEVEL_NO, PT_CODE, PM_CODE, DEPARTMENT_ID, ORG_ID,
										EAF_NAME, EAF_ROLE, EAF_REMARK, EAF_REF_ID, EAF_ACTIVE, EAF_DATE, EAF_YEAR, EAF_MONTH
						from		EAF_MASTER
						where		EAF_ID=$EAF_ID ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		
		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PL_NAME = trim($data_dpis2[PL_NAME]);

		$PM_CODE = trim($data[PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PM_NAME = trim($data_dpis2[PM_NAME]);
		
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PT_NAME = trim($data_dpis2[PT_NAME]);

		if($CTRL_TYPE < 4){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];

			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = trim($data_dpis2[ORG_NAME]);
			
			if($CTRL_TYPE < 3){
				$MINISTRY_ID = $data_dpis2[ORG_ID_REF];
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$MINISTRY_NAME = trim($data_dpis2[ORG_NAME]);
			} // end if
		} // end if
		
		$ORG_ID = trim($data[ORG_ID]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME = trim($data_dpis2[ORG_NAME]);
		
		$EAF_NAME = trim($data[EAF_NAME]);
		$EAF_ROLE = trim($data[EAF_ROLE]);
		$EAF_REMARK = trim($data[EAF_REMARK]);
		$EAF_REF_ID = $data[EAF_REF_ID];
		$EAF_ACTIVE = $data[EAF_ACTIVE];
		$EAF_DATE = show_date_format($data[EAF_DATE], 1);
		$EAF_YEAR = $data[EAF_YEAR];
		$EAF_MONTH = $data[EAF_MONTH];

	} // end if
?>