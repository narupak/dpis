<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	/*cdgs*/
	include("php_scripts/psst_position.php");
	/*cdgs*/
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
	
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
	
	if(!isset($show_topic)) $show_topic = 1;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(!trim($ORG_ID_1)) $ORG_ID_1 = "NULL";
	if(!trim($ORG_ID_2)) $ORG_ID_2 = "NULL";
	if(!trim($ORG_ID_3)) $ORG_ID_3 = "NULL";
	if(!trim($ORG_ID_4)) $ORG_ID_4 = "NULL";
	if(!trim($ORG_ID_5)) $ORG_ID_5 = "NULL";
	if(!trim($POEM_MIN_SALARY)) $POEM_MIN_SALARY = "NULL";
	if(!trim($POEM_MAX_SALARY)) $POEM_MAX_SALARY = "NULL";
	if( !$POEMS_SOUTH ) $POEMS_SOUTH = 2;
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_POS_EMPSER set POEM_STATUS = 2 where POEMS_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update PER_POS_EMPSER set POEM_STATUS = 1 where POEMS_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ����ҹ������");
	}

	if($command=="ADD" && $POEMS_NO && $DEPARTMENT_ID){
		// ========================== This code use before add DEPARTMENT_ID column to PER_POS_EMPSER table ==========================
		// $cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and ORG_ID_REF=$DEPARTMENT_ID order by ORG_ID ";
		// $db_dpis->send_cmd($cmd);
		// unset($arr_org);
		// while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		// $cmd = " select POEMS_ID, POEMS_NO from PER_POS_EMPSER where trim(POEMS_NO)='". trim($POEMS_NO) ."' and ORG_ID in (". implode(",", $arr_org) .") ";
		// =====================================================================================================================

		// ====================== After add DEPARTMENT_ID column to PER_POS_EMPSER table use this code instead ==========================
		$cmd = " select POEMS_ID, POEMS_NO,POEM_STATUS from PER_POS_EMPSER where trim(POEMS_NO)='". trim($POEMS_NO) ."' and DEPARTMENT_ID=$DEPARTMENT_ID ";
		// =====================================================================================================================

		$count_duplicate = $db_dpis->send_cmd($cmd);
                $COMMA =","; 
                $idx = 0;
                $POEM_NO_CHKS = "";
                while ($dataCHK = $db_dpis->get_array()){
                    $CHK_STATUS .=$dataCHK[POEM_STATUS].$COMMA;
                    $POEMS_NO_CHKS = $dataCHK[POEMS_NO];
                }
                $CHK_EX = explode($COMMA,$CHK_STATUS,-1);
                
		if($count_duplicate <= 0 || !in_array("1", $CHK_EX)){
			$cmd = " select max(POEMS_ID) as max_id from PER_POS_EMPSER ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);			
			$POEMS_ID = $data[max_id] + 1;
			
			$cmd = " insert into PER_POS_EMPSER (POEMS_ID, POEMS_NO, EP_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, 
							  ORG_ID_3, ORG_ID_4, ORG_ID_5, POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS, 
							  DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, POEMS_REMARK, PPT_CODE, PEF_CODE, 
							  PPS_CODE, POEMS_SKILL, POEMS_SOUTH, POEMS_NO_NAME  )
							  values ($POEMS_ID, '$POEMS_NO', '$EP_CODE', $ORG_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							  $ORG_ID_4, $ORG_ID_5, $POEM_MIN_SALARY, $POEM_MAX_SALARY, $POEM_STATUS, 
							  $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', '$POEMS_REMARK', '$PPT_CODE', 
							  '$PEF_CODE', '$PPS_CODE', '$POEMS_SKILL', $POEMS_SOUTH, '$POEMS_NO_NAME'  ) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			/*cdgs*/
			$a=f_ins_psst_position_oth($POEMS_ID,"14",$POEMS_NO);
			/*cdgs*/
			//echo "<pre>".$cmd;
			$action_result = "<p><FONT SIZE='3' COLOR='#0000FF' align='center'>�����������Ţ�����˹� ".$POEMS_NO_NAME.$POEMS_NO." ���º��������</FONT></p>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ���������ŵ��˹觾�ѡ�ҹ�Ҫ��� [ $DEPARTMENT_ID : $POEMS_ID : $POEMS_NO ]");
		}else{
			//$data = $db_dpis->get_array();
			$err_text = "�Ţ�����˹觫�� [".$POEMS_NO_CHKS."]";

			if($ORG_ID_1 == "NULL") $ORG_ID_1 = "";
			if($ORG_ID_2 == "NULL") $ORG_ID_2 = "";
			if($ORG_ID_3 == "NULL") $ORG_ID_3 = "";
			if($ORG_ID_4 == "NULL") $ORG_ID_4 = "";
			if($ORG_ID_5 == "NULL") $ORG_ID_5 = "";
		} // end if
	} // end if


	if($command=="UPDATE" && $POEMS_ID && $POEMS_NO && $DEPARTMENT_ID){
		if($POEM_MIN_SALARY=="-")$POEM_MIN_SALARY="NULL";
		if($POEM_MAX_SALARY=="-")$POEM_MAX_SALARY="NULL";
		$cmd = " update PER_POS_EMPSER set
							POEMS_NO = '$POEMS_NO',
							EP_CODE = '$EP_CODE', 
							POEM_MIN_SALARY = $POEM_MIN_SALARY, 
							POEM_MAX_SALARY = $POEM_MAX_SALARY, 
							DEPARTMENT_ID = $DEPARTMENT_ID, 
							ORG_ID = $ORG_ID, 
							ORG_ID_1 = $ORG_ID_1, 
							ORG_ID_2 = $ORG_ID_2, 
							ORG_ID_3 = $ORG_ID_3, 
							ORG_ID_4 = $ORG_ID_4, 
							ORG_ID_5 = $ORG_ID_5, 
							POEMS_REMARK = '$POEMS_REMARK', 
							POEM_STATUS = '$POEM_STATUS', 
							PPT_CODE = '$PPT_CODE', 
							PEF_CODE = '$PEF_CODE', 
							PPS_CODE = '$PPS_CODE', 
							POEMS_SKILL = '$POEMS_SKILL', 
							POEMS_SOUTH = $POEMS_SOUTH, 
							POEMS_NO_NAME = '$POEMS_NO_NAME',
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE'
						 where POEMS_ID=$POEMS_ID ";
		$db_dpis->send_cmd($cmd);
	//echo "<pre>".$cmd;
//		$db_dpis->show_error();
		/*cdgs*/
		$a=f_ins_psst_position_oth($POEMS_ID,"14",$POEMS_NO);
		/*cdgs*/
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ��䢢����ŵ��˹觾�ѡ�ҹ�Ҫ��� [ $DEPARTMENT_ID : $POEMS_ID : $POEMS_NO ]");
	} // end if

	if($command == "DELETE" && $POEMS_ID){
		$cmd = " select POEMS_NO from PER_POS_EMPSER where POEMS_ID=$POEMS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POEMS_NO = $data[POEMS_NO];
		
		$cmd = " delete from PER_POS_EMPSER where POEMS_ID=$POEMS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		/*cdgs*/
		$a=f_del_psst_position($POEMS_ID,"14");
		/*cdgs*/
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����ŵ��˹觾�ѡ�ҹ�Ҫ��� [$DEPARTMENT_ID : $POEMS_ID : $POEMS_NO]");
	} // end if	

//��Ѻ��ǧ�Թ��͹����ѭ���ѵ���Թ��͹
if($command =="up_salary"){
	$POEMS_ID="";
	$POEM_MIN_SALARY = "";
	$POEM_MAX_SALARY = "";
	$LEVEL_NO ="";
	
	$sql = "select a.POEMS_ID, a.POEM_MIN_SALARY, a.POEM_MAX_SALARY, b.LEVEL_NO
			from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
			where a.POEM_STATUS = 1 
			and a.department_id = $DEPARTMENT_ID
			and a.EP_CODE = b.EP_CODE";
	$db_dpis3->send_cmd($sql);
	while($data3 = $db_dpis3->get_array()){
		$POEMS_ID = $data3[POEMS_ID];
		$POEM_MIN_SALARY = $data3[POEM_MIN_SALARY];
		$POEM_MAX_SALARY = $data3[POEM_MAX_SALARY];
		$LEVEL_NO = $data3[LEVEL_NO];
		
		$sql="select LAYER_SALARY_MIN, LAYER_SALARY_MAX 
				from PER_LAYER 
				where LEVEL_NO = '$LEVEL_NO' and LAYER_SALARY_MIN is not null and LAYER_SALARY_MAX is not null";		
		$db_dpis4->send_cmd($sql);
		$data4 =$db_dpis4->get_array();
		$LAYER_SALARY_MIN = $data4[LAYER_SALARY_MIN];
		$LAYER_SALARY_MAX = $data4[LAYER_SALARY_MAX];
		
		
		$cmd = " update PER_POS_EMPSER set
							POEM_MIN_SALARY = $LAYER_SALARY_MIN,
							POEM_MAX_SALARY = $LAYER_SALARY_MAX,
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE'
							where POEMS_ID=$POEMS_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��Ѻ��ǧ�Թ��͹����ѭ���ѵ���Թ��͹ [$DEPARTMENT_ID : $POEMS_ID : $POEMS_NO]");
		//echo "<pre>".$cmd;			
	}
}	
	if($UPD || $VIEW){
		$cmd = "	select	POEMS_NO, a.EP_CODE, b.EP_NAME, b.LEVEL_NO, DEPARTMENT_ID, ORG_ID, ORG_ID_1, 
											ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											POEM_STATUS, POEMS_REMARK, PPT_CODE, PEF_CODE, PPS_CODE, POEMS_SKILL, 
											POEMS_SOUTH, a.UPDATE_USER, a.UPDATE_DATE, POEMS_NO_NAME
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							where	a.EP_CODE=b.EP_CODE and POEMS_ID=$POEMS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//echo "<pre> $cmd";
		$data = $db_dpis->get_array();
		$POEMS_NO = trim($data[POEMS_NO]);
		$POEMS_NO_NAME = trim($data[POEMS_NO_NAME]);
		$EP_CODE = trim($data[EP_CODE]);
		$EP_NAME = trim($data[EP_NAME]);
		
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$LEVEL_NAME = trim($data_dpis2[LEVEL_NAME]);

		if($SESS_USERGROUP_LEVEL < 5){
			$ORG_ID = trim($data[ORG_ID]);
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data_dpis2[ORG_NAME]);
		} // end if
		
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data_dpis2[ORG_NAME];
			
			if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){
				$MINISTRY_ID = $data_dpis2[ORG_ID_REF];
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$MINISTRY_NAME = $data_dpis2[ORG_NAME];
			} // end if
		} // end if
		
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

		$ORG_ID_3 = trim($data[ORG_ID_3]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_3 = trim($data_dpis2[ORG_NAME]);

		$ORG_ID_4 = trim($data[ORG_ID_4]); 
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_4 = trim($data_dpis2[ORG_NAME]);

		$ORG_ID_5 = trim($data[ORG_ID_5]); 
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_5 = trim($data_dpis2[ORG_NAME]);

		$POEM_MIN_SALARY = (trim($data[POEM_MIN_SALARY])?trim($data[POEM_MIN_SALARY]):"-");
		$POEM_MAX_SALARY = (trim($data[POEM_MAX_SALARY])?trim($data[POEM_MAX_SALARY]):"-");
		$POEM_STATUS = trim($data[POEM_STATUS]);
		$POEMS_REMARK = trim($data[POEMS_REMARK]);
		$PPT_CODE = trim($data[PPT_CODE]);
		$PEF_CODE = trim($data[PEF_CODE]);
		$PPS_CODE = trim($data[PPS_CODE]);
		$POEMS_SKILL = trim($data[POEMS_SKILL]);
		$POEMS_SOUTH = trim($data[POEMS_SOUTH]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if

	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$POEMS_ID = "";
		$POEMS_NO = "";
		$LEVEL_NO = "";
		$LEVEL_NAME = "";
		$EP_CODE = "";
		$EP_NAME = "";
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} 
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		}
		if($SESS_USERGROUP_LEVEL < 5){
			$ORG_ID = "";
			$ORG_NAME = "";
		} 
		if($SESS_USERGROUP_LEVEL < 6){
			$ORG_ID_1 = "";
			$ORG_NAME_1 = "";
		}
		$ORG_ID_2 = "";
		$ORG_NAME_2 = "";
		$ORG_ID_3 = "";
		$ORG_NAME_3 = "";
		$ORG_ID_4 = "";
		$ORG_NAME_4 = "";
		$ORG_ID_5 = "";
		$ORG_NAME_5 = "";
		$POEM_MIN_SALARY = "";
		$POEM_MAX_SALARY = "";
		$POEM_STATUS = 1;
		$POEMS_REMARK = "";
		$PPT_CODE = "";
		$PEF_CODE = "";
		$PPS_CODE = "";
		$POEMS_SKILL = "";
		$POEMS_SOUTH = "";
		$SHOW_UPDATE_USER = "";
		$SHOW_UPDATE_DATE = "";
	} // end if	
?>