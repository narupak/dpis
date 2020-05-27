<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");
	/*cdgs*/
	include("php_scripts/psst_position.php");
	/*cdgs*/
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
	
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
	if(!trim($POEM_MIN_SALARY)) $POEM_MIN_SALARY = 0;
	if(!trim($POEM_MAX_SALARY)) $POEM_MAX_SALARY = 0;

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_POS_EMP set POEM_STATUS = 0 where POEM_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update PER_POS_EMP set POEM_STATUS = 1 where POEM_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ï¿½ï¿½é§¤ï¿½Ò¡ï¿½ï¿½ï¿½ï¿½Ò¹ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½");
	}
	
	if($command=="ADD" && $POEM_NO && $DEPARTMENT_ID){
		// ============================ This code use before add DEPARTMENT_ID column to PER_POS_EMP table ============================
		// $cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and ORG_ID_REF=$DEPARTMENT_ID order by ORG_ID ";
		// $db_dpis->send_cmd($cmd);
		// unset($arr_org);
		// while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		// $cmd = " select POEM_ID, POEM_NO from PER_POS_EMP where trim(POEM_NO)='". trim($POEM_NO) ."' and ORG_ID in (". implode(",", $arr_org) .") ";
		// =====================================================================================================================

		// ======================== After add DEPARTMENT_ID column to PER_POS_EMP table use this code instead ============================
		$cmd = " select POEM_ID, POEM_NO,POEM_STATUS from PER_POS_EMP where trim(POEM_NO)='". trim($POEM_NO) ."' and DEPARTMENT_ID=$DEPARTMENT_ID ";
		// =====================================================================================================================
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo $cmd;
                $COMMA =","; 
                $idx = 0;
                $POEM_NO_CHKS = "";
                while ($dataCHK = $db_dpis->get_array()){
                    $CHK_STATUS .=$dataCHK[POEM_STATUS].$COMMA;
                    $POEM_NO_CHKS = $dataCHK[POEM_NO];
                }
                $CHK_EX = explode($COMMA,$CHK_STATUS,-1);
		if($count_duplicate <= 0 || !in_array("1", $CHK_EX)){
			$cmd = " select max(POEM_ID) as max_id from PER_POS_EMP ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$POEM_ID = $data[max_id] + 1;
			
			$cmd = " insert into PER_POS_EMP (POEM_ID, POEM_NO, PN_CODE, DEPARTMENT_ID, ORG_ID, 
							  ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, POEM_MIN_SALARY, POEM_MAX_SALARY, 
							  POEM_STATUS, UPDATE_USER, UPDATE_DATE, POEM_REMARK, PG_CODE_SALARY, POEM_NO_NAME, LEVEL_NO )
							  values ($POEM_ID, '$POEM_NO', '$PN_CODE', $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, 
							  $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, $POEM_MIN_SALARY, $POEM_MAX_SALARY, 
							  $POEM_STATUS, $SESS_USERID, '$UPDATE_DATE', '$POEM_REMARK', '$PG_CODE_SALARY', '$POEM_NO_NAME', '$LEVEL_NO' ) ";
			$db_dpis->send_cmd($cmd);
			//echo $cmd;
//			$db_dpis->show_error();
			/*cdgs*/
			// $a=f_ins_psst_position_oth($POEM_ID,"21",$POEM_NO); method à¸ÿà¸µà¹ÿà¹ÿà¸¡à¹ÿà¸¡à¸µà¹ÿà¸ÿ psst_person.php
			/*cdgs*/
			$action_result = "<p><FONT SIZE='3' COLOR='#0000FF' align='center'>ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Å¢ï¿½ï¿½ï¿½ï¿½ï¿½Ë¹ï¿½ ".$POEM_NO_NAME.$POEM_NO." ï¿½ï¿½ï¿½Âºï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½</FONT></p>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Åµï¿½ï¿½Ë¹ï¿½ [ $DEPARTMENT_ID : $POEM_ID : $POEM_NO ]");
			
		}else{
			//$data = $db_dpis->get_array();
			$err_text = "ï¿½Å¢ï¿½ï¿½ï¿½ï¿½ï¿½Ë¹è§«ï¿½ï¿½ [".$POEM_NO_CHKS."]";

			if($ORG_ID_1 == "NULL") $ORG_ID_1 = "";
			if($ORG_ID_2 == "NULL") $ORG_ID_2 = "";
			if($ORG_ID_3 == "NULL") $ORG_ID_3 = "";
			if($ORG_ID_4 == "NULL") $ORG_ID_4 = "";
			if($ORG_ID_5 == "NULL") $ORG_ID_5 = "";
		} // end if
	} // end if

	if($command=="UPDATE" && $POEM_ID && $POEM_NO && $DEPARTMENT_ID){
		$cmd = " update PER_POS_EMP set
							POEM_NO = '$POEM_NO',
							PN_CODE = '$PN_CODE', 
							POEM_MIN_SALARY = $POEM_MIN_SALARY, 
							POEM_MAX_SALARY = $POEM_MAX_SALARY, 
							DEPARTMENT_ID = $DEPARTMENT_ID, 
							ORG_ID = $ORG_ID, 
							ORG_ID_1 = $ORG_ID_1, 
							ORG_ID_2 = $ORG_ID_2, 
							ORG_ID_3 = $ORG_ID_3, 
							ORG_ID_4 = $ORG_ID_4, 
							ORG_ID_5 = $ORG_ID_5, 
							POEM_REMARK = '$POEM_REMARK', 
							POEM_STATUS = '$POEM_STATUS', 
							PG_CODE_SALARY = '$PG_CODE_SALARY', 
							POEM_NO_NAME = '$POEM_NO_NAME', 
							LEVEL_NO = '$LEVEL_NO', 
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE'
						 where POEM_ID=$POEM_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		/*cdgs*/
		// $a=f_ins_psst_position_oth($POEM_ID,"21",$POEM_NO); method à¸ÿà¸µà¹ÿà¹ÿà¸¡à¹ÿà¸¡à¸µà¹ÿà¸ÿ psst_person.php
		/*cdgs*/
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ï¿½ï¿½ä¢¢ï¿½ï¿½ï¿½ï¿½Åµï¿½ï¿½Ë¹ï¿½ [ $DEPARTMENT_ID : $POEM_ID : $POEM_NO ]");
	} // end if

	if($command == "DELETE" && $POEM_ID){
		$cmd = " select POEM_NO from PER_POS_EMP where POEM_ID=$POEM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POEM_NO = $data[POEM_NO];
		
		$cmd = " delete from PER_POS_EMP where POEM_ID=$POEM_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		/*cdgs*/
		$a=f_del_psst_position($POEM_ID,"21");
		/*cdgs*/
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > Åºï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ [$DEPARTMENT_ID : $POEM_ID : $POEM_NO]");
	} // end if		
	
	if($UPD || $VIEW){
		$cmd = "	select	POEM_NO, a.PN_CODE, b.PN_NAME, b.PG_CODE, DEPARTMENT_ID, ORG_ID, ORG_ID_1, 
											ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											POEM_STATUS, POEM_REMARK, PG_CODE_SALARY, a.UPDATE_USER, a.UPDATE_DATE, POEM_NO_NAME, a.LEVEL_NO
							from		PER_POS_EMP a, PER_POS_NAME b
							where	a.PN_CODE=b.PN_CODE and POEM_ID=$POEM_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POEM_NO = trim($data[POEM_NO]);
		$POEM_NO_NAME = trim($data[POEM_NO_NAME]);
		$PN_CODE = trim($data[PN_CODE]);
		$PN_NAME = trim($data[PN_NAME]);
		
		$PG_CODE = trim($data[PG_CODE]);
		$cmd = " select PG_NAME from PER_POS_GROUP where trim(PG_CODE)='$PG_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PG_NAME = trim($data_dpis2[PG_NAME]);

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
				$MINISTRY_NAME = "";
				if($MINISTRY_ID){
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$MINISTRY_NAME = $data_dpis2[ORG_NAME];
				}
			} // end if
		} // end if
		$ORG_NAME_1 = "";
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		if($ORG_ID_1){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data_dpis2[ORG_NAME]);
		}
		$ORG_NAME_2 = "";
		$ORG_ID_2 = trim($data[ORG_ID_2]); 
		if($ORG_ID_2){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data_dpis2[ORG_NAME]);
		}
		$ORG_NAME_3 = "";
		$ORG_ID_3 = trim($data[ORG_ID_3]);
		if($ORG_ID_3){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_3 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_3 = trim($data_dpis2[ORG_NAME]);
		}
		$ORG_NAME_4 = "";
		$ORG_ID_4 = trim($data[ORG_ID_4]); 
		if($ORG_ID_4){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_4 = trim($data_dpis2[ORG_NAME]);
		}
		$ORG_NAME_5 = "";
		$ORG_ID_5 = trim($data[ORG_ID_5]); 
		if($ORG_ID_5){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_5 = trim($data_dpis2[ORG_NAME]);
		}
                
                /**/
                $cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS, a.LEVEL_NO 
							 from 		PER_PERSONAL a, PER_PRENAME b
                                                where	a.PN_CODE=b.PN_CODE(+) and a.POEM_ID=$POEM_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=2 ";
               $db_dpis2->send_cmd($cmd);
               $data_dpis2 = $db_dpis2->get_array();
               $LEVEL_NO_TM = $data_dpis2[LEVEL_NO];

               $cmd = " select MIN_SALARY, MAX_SALARY, GROUP_SALARY, UP_SALARY, LEVEL_NO from PER_POS_LEVEL_SALARY where PN_CODE='$PN_CODE' and LEVEL_NO='$LEVEL_NO_TM' ";
               $db_dpis2->send_cmd($cmd);
               $data_dpis2 = $db_dpis2->get_array();
               $MIN_SALARY = $data_dpis2[MIN_SALARY];
               $MAX_SALARY = $data_dpis2[MAX_SALARY];
               $GROUP_SALARY = $data_dpis2[GROUP_SALARY];
               $UP_SALARY = $data_dpis2[UP_SALARY];
               $LEVEL_NO1 = $data_dpis2[LEVEL_NO];
               $grp = explode(",", $GROUP_SALARY);
               $grp_end = end($grp);
               $num_grp = strlen($GROUP_SALARY);
               if($num_grp == 5){
                $GROUP_SALARY_POS = $grp[0]."-".$grp_end;
               }else{
                $GROUP_SALARY_POS = str_replace(",", "-", $GROUP_SALARY);
               }
               //echo $cmd;
            
               //$POEM_SALARY = number_format($MIN_SALARY) . (trim($MAX_SALARY)?(" - ".number_format($MAX_SALARY)):"");

               $POEM_MIN_SALARY = $MIN_SALARY;
               $POEM_MAX_SALARY = $MAX_SALARY;
                
                
                /*ï¿½ï¿½ï¿½*/
		/*$POEM_MIN_SALARY = trim($data[POEM_MIN_SALARY]);
		$POEM_MAX_SALARY = trim($data[POEM_MAX_SALARY]);*/
                $cmd = "";
		$POEM_STATUS = trim($data[POEM_STATUS]);
		$POEM_REMARK = trim($data[POEM_REMARK]);
		$PG_CODE_SALARY = trim($data[PG_CODE_SALARY]);
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if

	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$POEM_ID = "";
		$POEM_NO = "";
		$PG_CODE = "";
		$PG_NAME = "";
		$PN_CODE = "";
		$PN_NAME = "";
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
		$POEM_REMARK = "";
		$PG_CODE_SALARY = "";
		$LEVEL_NO = "";
		$SHOW_UPDATE_USER = "";
		$SHOW_UPDATE_DATE = "";
	} // end if	

	if($command == "PG_CODE_SALARY"){
	    $cmd = " select a.POEM_ID, PER_SALARY, PG_CODE_SALARY from PER_POS_EMP a, PER_PERSONAL b 
					where a.POEM_ID = b.POEM_ID and PER_TYPE = 2 and PER_STATUS = 1 order by to_number(POEM_NO) ";
		$db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
		while ($data2 = $db_dpis2->get_array()) {
			$POEM_ID = $data2[POEM_ID];
			$PER_SALARY = $data2[PER_SALARY];
			$PG_CODE_SALARY = trim($data2[PG_CODE_SALARY]);
			$cmd = " select LAYERE_SALARY from PER_LAYEREMP where PG_CODE='$PG_CODE_SALARY' and LAYERE_SALARY=$PER_SALARY ";
			$count_data = $db_dpis1->send_cmd($cmd);
			if (!$count_data) {
				$cmd = " select PG_CODE from PER_LAYEREMP where LAYERE_SALARY=$PER_SALARY and PG_CODE in ('1000', '2000', '3000', '4000') ";
				$count_data = $db_dpis1->send_cmd($cmd);
				if ($count_data==1) {
					$data1 = $db_dpis1->get_array();
					$PG_CODE = trim($data1[PG_CODE]);
					$cmd = " update PER_POS_EMP set PG_CODE_SALARY = '$PG_CODE' where POEM_ID = $POEM_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					//echo "$cmd<br>";
				} // end if	
			} // end if	
		}
	} // end if	

?>