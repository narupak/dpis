<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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

	$COM_SEND_STATUS = "";
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
			$search_org_id1 = $ORG_ID;		//��ͧ����
			$search_org_name1 = $ORG_NAME;	
			$COM_SEND_STATUS = "S";
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id1 = $ORG_ID;		//��ͧ����
			$search_org_name1 = $ORG_NAME;	
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case
	
	if (is_null($ORG_ID) || $ORG_ID=="NULL") $ORG_ID=0;
	if (($COM_SITE=="personal_workflow" && $ORG_ID) || ($SESS_USERGROUP_LEVEL < 5 && $ORG_ID)) {
		$search_org_id = $ORG_ID;
		$search_org_name = $ORG_NAME;
	} else if ($SESS_USERGROUP_LEVEL < 5){
//		$search_org_id = "0";
//		$search_org_name = "";
	}	
	//----------------------------------------------------------------------------------

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	if (!$search_org_id) $search_org_id = "NULL";
	
	if(!isset($search_kf_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$COM_PER_TYPE = (trim($COM_PER_TYPE))? $COM_PER_TYPE : 1;	
	$list_type = (trim($list_type))? $list_type : 4;	
	$COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 6; 
	
	if($search_kf_cycle == 1){
		$KF_START_DATE = ($search_kf_year-544) . "-10-01";
		$KF_END_DATE = ($search_kf_year-543) . "-03-31";
		$CMD_DATE = ($search_kf_year-543) . "-04-01";
	}elseif($search_kf_cycle == 2){
		$KF_START_DATE = ($search_kf_year-543) . "-04-01";
		$KF_END_DATE = ($search_kf_year-543) . "-09-30";
		$CMD_DATE = ($search_kf_year-543) . "-10-01";
	} // end if

	$_select_level_no_str = implode("','",$_select_level_no);
	if ($SESS_DEPARTMENT_NAME=="�����û���ͧ")	$position_join = "b.PAY_ID=c.POS_ID";
	else $position_join = "b.POS_ID=c.POS_ID";
	
	if (!$search_department_id) $search_department_id = "NULL";

//echo "$command  - $COM_ID -  $COM_NO";

	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
			$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$COM_ID = $data[max_id] + 1;
	
			if(trim($COM_DATE)){	$COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2);	}
			if (!$search_org_id) $search_org_id = "NULL";

			if (trim($search_org_id)=="" || $search_org_id=="NULL" || is_null($search_org_id)) {
				$search_org_id=0;
			}
			$COM_NOTE=trim($COM_NOTE);
		
			$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE,
							COM_TYPE, COM_CONFIRM, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, COM_LEVEL_SALP, ORG_ID, COM_STATUS) 
							VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE','$COM_NOTE', $COM_PER_TYPE,
							'$COM_TYPE', 0, $SESS_USERID, '$UPDATE_DATE', $search_department_id, $COM_LEVEL_SALP, $search_org_id,'') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br>$cmd<br>=======================<br>";
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������źѭ��Ṻ���¤��������͹����Թ��͹ [$search_department_id : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
		
			// ===== ������� insert �����Ũҡ��������������͹����Թ��͹ ����������źѭ��Ṻ���¤���� (table PER_COMDTL) =====
			if ($RPT_N) {
				if ($list_type==1) $where = " and d.OT_CODE = '01' ";		
				elseif ($list_type==2) $where = " and d.PV_CODE = '$PV_CODE' ";		
				elseif ($list_type==3) $where = " and d.CT_CODE = '$CT_CODE' ";		
			}		
			if ($RPT_N) {
				if ($COM_LEVEL_SALP == 1)					// 1=�Թ��͹
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY=0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
				elseif ($COM_LEVEL_SALP == 3) 				// 3=�Թ���������
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";	
				elseif ($COM_LEVEL_SALP == 5)					// 5=���³
					$where_com_level_salp = " and b.PER_STATUS=0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";					
				elseif ($COM_LEVEL_SALP == 6)					// 6=����Ҫ��÷�����
					$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
			} else {
				if ($COM_LEVEL_SALP == 1)					// 1=�дѺ 8 ŧ��
					$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO_SALARY <= '08' ";		
				elseif ($COM_LEVEL_SALP == 2)					// 2=�дѺ 9 ����
					$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO_SALARY >= '09' ";
				elseif ($COM_LEVEL_SALP == 3) 				// 3=�Թ��������� �дѺ 8 ŧ��
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO_SALARY <= '08' ";	
				elseif ($COM_LEVEL_SALP == 4)					// 4=�Թ��������� �дѺ 9 ����
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO_SALARY >= '09' ";	
				elseif ($COM_LEVEL_SALP == 5)					// 5=���³
					$where_com_level_salp = " and b.PER_STATUS=0 ";					
				elseif ($COM_LEVEL_SALP == 6)					// 6=����Ҫ��÷�����
					$where_com_level_salp = "";		
				elseif ($COM_LEVEL_SALP == 7)					// 7=����
					$where_com_level_salp = " and b.PER_STATUS=1 ";
				elseif ($COM_LEVEL_SALP == 8)					// 8=�Թ���������
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 ";
				elseif ($COM_LEVEL_SALP == 9)					// 9=���³
					$where_com_level_salp = " and b.PER_STATUS=0 ";
				elseif ($COM_LEVEL_SALP == 10)				// 10=�١��ҧ��Ш����;�ѡ�ҹ�Ҫ��÷�����
					$where_com_level_salp = "";
			}		
			
			$cmd1 = " select	a.CP_ID, a.PER_ID, b.PER_SALARY, a.AL_CODE, a.CD_SALARY, a.CD_PERCENT, a.CD_EXTRA_SALARY, a.CD_MIDPOINT, b.LEVEL_NO, 
											b.LEVEL_NO_SALARY, b.PAY_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID, c.PL_CODE, c.PM_CODE 
							   from 	PER_COMPENSATION_TEST_DTL a, PER_PERSONAL b, PER_POSITION c , PER_ORG d, PER_COMPENSATION_TEST e
							   where 	a.CP_ID in ($SELECTED_CP_ID) and a.CP_ID=e.CP_ID and a.PER_ID=b.PER_ID and $position_join and e.ORG_ID = d.ORG_ID(+) $where_com_level_salp $where ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$count_temp = $db_dpis->send_cmd($cmd1);
//			$db_dpis->show_error();
//			echo "<br>$cmd1<br>";
			$cmd_seq = 0;
			while ($data = $db_dpis->get_array()) {
				$cmd_seq++;
				$CP_ID = $data[CP_ID];		
				$TMP_PER_ID = $data[PER_ID];		
				$CMD_OLD_SALARY = $data[PER_SALARY] + 0;
				$TMP_AL_CODE = trim($data[AL_CODE]) ;
				$CMD_SALARY = $data[CD_SALARY] + $CMD_OLD_SALARY;
				$CMD_PERCENT = $data[CD_PERCENT] + 0;
				$CMD_SPSALARY = $data[CD_EXTRA_SALARY] + 0;
				if ($COM_LEVEL_SALP == 1) $CMD_SPSALARY = 0;
				$TMP_SALP_LEVEL = trim($data[LEVEL_NO]) ;
				$CMD_MIDPOINT = $data[CD_MIDPOINT] + 0;
				$CMD_PL_CODE = trim($data[PL_CODE]) ;
				$CMD_PM_CODE = trim($data[PM_CODE]) ;
				if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") $POS_ID = trim($data[PAY_ID]); 
				else $POS_ID = trim($data[POS_ID]); 

				$cmd = " select LAYER_TYPE from PER_LINE where trim(PL_CODE)='$CMD_PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$LAYER_TYPE = $data2[LAYER_TYPE] + 0;

				$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
						 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
						 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$TMP_SALP_LEVEL' and LAYER_NO = 0 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				if ($LAYER_TYPE==1 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
					$LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
					$SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
					$SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
					$SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];
				} else {
					$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
					$SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
					$SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
					$SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];
				}
	
				if ($CMD_MIDPOINT==0) {
					if($SALARY_POINT_MID > $CMD_OLD_SALARY) {
						$CMD_MIDPOINT = $SALARY_POINT_MID1;
					} else {
						$CMD_MIDPOINT = $SALARY_POINT_MID2;
					}
				}

				if ($COM_LEVEL_SALP == 3) $CMD_SALARY = $LAYER_SALARY_MAX;

				$cmd = " select a.AM_CODE, AL_NAME, AM_NAME, AM_SHOW from PER_ASSESS_LEVEL a, PER_ASSESS_MAIN b 
								  where a.AM_CODE = b.AM_CODE and AL_CODE='$TMP_AL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$AM_CODE = trim($data2[AM_CODE]);
				$AL_NAME = trim($data2[AL_NAME]);
				$AM_NAME = trim($data2[AM_NAME]);
				$AM_SHOW = $data2[AM_SHOW] + 0;

				$MOV_CODE = "21305";
				if ($AM_CODE == 2 || $AM_NAME == "����") 		$MOV_CODE = "21315";
				elseif ($AM_CODE == 3 || $AM_NAME == "��")		$MOV_CODE = "21325";
				elseif ($AM_CODE == 4 || $AM_NAME == "���ҡ") 	$MOV_CODE = "21335";
				elseif ($AM_CODE == 5 || $AM_NAME == "����") 		$MOV_CODE = "21345";

				if ($CMD_SPSALARY  > 0)		$MOV_CODE = "21415";
				
				$cmd = " select CP_CYCLE, CP_END_DATE from PER_COMPENSATION_TEST where CP_ID=$CP_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CP_CYCLE = $data2[CP_CYCLE];
				$CP_END_DATE = trim($data2[CP_END_DATE]);

				$CMD_NOTE1 = $AL_NAME;
				$CMD_NOTE2 = $AM_NAME;
				
				$CMD_LEVEL = $LEVEL_NO = trim($data[LEVEL_NO])? "'".$data[LEVEL_NO]."'" : "NULL";
				$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY])? "'".$data[LEVEL_NO_SALARY]."'" : "NULL";
				$POS_ID = ($POS_ID)? $POS_ID : "NULL";
				$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
				$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
				$EN_CODE = $PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";

				$cmd = " select POS_NO_NAME, POS_NO, PL_NAME, PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO 
								from PER_POSITION a, PER_LINE b 
								where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POS_NO]);
				$PL_NAME = trim($data2[PL_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				$CMD_LEVEL_POS = trim($data2[LEVEL_NO]);
				$CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$CMD_POS_NO = trim($data2[POS_NO]);

				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PM_NAME = $data1[PM_NAME];
		
				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$CMD_POSITION = $PL_NAME;
				if ($PM_NAME)
					$CMD_POSITION .= "\|$PM_NAME";
				if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
				else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;

				$ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
				$ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
				$ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
				$ORG_ID_4 = (trim($data2[ORG_ID_3]))? trim($data2[ORG_ID_3]) : 0;
				$ORG_ID_5 = (trim($data2[ORG_ID_4]))? trim($data2[ORG_ID_4]) : 0;
				$ORG_ID_6 = (trim($data2[ORG_ID_5]))? trim($data2[ORG_ID_5]) : 0;
				$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = $CMD_ORG6 = $CMD_ORG7 = $CMD_ORG8 = "";
				$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, $ORG_ID_6) ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				while ( $data2 = $db_dpis2->get_array() ) {
					$temp_id = trim($data2[ORG_ID]);
					$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
					$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
					$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
					$CMD_ORG6 = ($temp_id == $ORG_ID_4)?  trim($data2[ORG_NAME]) : $CMD_ORG6;
					$CMD_ORG7 = ($temp_id == $ORG_ID_5)?  trim($data2[ORG_NAME]) : $CMD_ORG7;
					$CMD_ORG8 = ($temp_id == $ORG_ID_6)?  trim($data2[ORG_NAME]) : $CMD_ORG8;						
				}
					
				$cmd = " select OT_CODE from PER_ORG where ORG_ID=$ORG_ID_1 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$OT_CODE = trim($data2[OT_CODE]);
		
				if ($OT_CODE == "03") 
					if (!$CMD_ORG5 && !$CMD_ORG4 && $search_department_name=="�����û���ͧ") 
						$ORG_NAME_WORK = "���ӡ�û���ͧ".$CMD_ORG3." ".$CMD_ORG3;
					else 
						$ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3." ".$search_department_name);
				else $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);
			 
				if (!$search_org_id || $search_org_id == "NULL" || $search_org_id == $ORG_ID_1) { 
					if (($COM_LEVEL_SALP==1 && $CMD_SALARY > $CMD_OLD_SALARY) || ($COM_LEVEL_SALP==3 && $CMD_SPSALARY > 0) ||
						$COM_LEVEL_SALP==6) {
						$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
										CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, 
										CMD_OLD_SALARY, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
										PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
										UPDATE_USER, UPDATE_DATE, LEVEL_NO_SALARY, CMD_PERCENT, AM_SHOW, CMD_MIDPOINT,
										PL_NAME_WORK, ORG_NAME_WORK, CMD_LEVEL_POS, CMD_SEQ_NO, PL_CODE, PM_CODE, CMD_POS_NO_NAME, CMD_POS_NO)
										values	($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', $CMD_LEVEL, 
										'$search_ministry_name', '$search_department_name', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', '$CMD_ORG6', 
										'$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 
										$CMD_SPSALARY, $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', 
										'$MOV_CODE', 0, $SESS_USERID, '$UPDATE_DATE', $LEVEL_NO_SALARY, $CMD_PERCENT, $AM_SHOW, $CMD_MIDPOINT,
										'$PL_NAME_WORK', '$ORG_NAME_WORK', '$CMD_LEVEL_POS', $cmd_seq, '$CMD_PL_CODE', '$CMD_PM_CODE', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
						$db_dpis1->send_cmd($cmd);				
						//$db_dpis1->show_error();
						//echo "<br>$cmd<br>=======================<br>";
					}
				}
			}	// end while
			if ($count_temp)
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ������������������´�ѭ��Ṻ���¤��������͹����Թ��͹ [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
			// ===== ����ش insert �����Ũҡ��������������͹����Թ��͹ ����������źѭ��Ṻ���¤���� (table PER_COMDTL) ===== 

			$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
			$count_comdtl = $db_dpis->send_cmd($cmd);
			// ��������������� ���º���� ���觤�ҡ�Ѻ�������ѡ �������� mode ������ǵ���
			echo "<script>";
			echo "parent.refresh_opener('2<::>$COM_ID<::>$COM_NAME<::>$search_org_id<::>$COM_PER_TYPE<::><::>$count_comdtl<::><::>?UPD=1<::>')";
			echo "</script>";
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "ADD_ALL" && trim(!$COM_ID) && trim($COM_NO) ){
			$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$COM_ID = $data[max_id] + 1;
	
			if(trim($COM_DATE)){ $COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2); }
	
			$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
							COM_TYPE, COM_CONFIRM, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, COM_LEVEL_SALP, ORG_ID) 
							VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
							'$COM_TYPE', 0, $search_department_id, $SESS_USERID, '$UPDATE_DATE', $COM_LEVEL_SALP, $search_org_id) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br>$cmd<br>=======================<br>";
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������źѭ��Ṻ���¤��������͹����Թ��͹ [$search_department_id : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
		
			// ===== ������� insert �����Ũҡ��������������͹����Թ��͹ ����������źѭ��Ṻ���¤���� (table PER_COMDTL) =====
			if ($RPT_N) {
				if ($list_type==1) $where = " and d.OT_CODE = '01' ";		
				elseif ($list_type==2) $where = " and d.PV_CODE = '$PV_CODE' ";		
				elseif ($list_type==3) $where = " and d.CT_CODE = '$CT_CODE' ";		
				if ($search_org_id && $search_org_id != "NULL") $where .= " and c.ORG_ID = $search_org_id ";		 
				if ($list_type==5) $where .= " and f.ORG_ID = $search_org_id ";
				if ($list_type==6) $where .= " and f.ORG_ID != $search_org_id ";
			}		
			if ($RPT_N) {
				if ($COM_LEVEL_SALP == 1)					// 1=�Թ��͹
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY=0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
				elseif ($COM_LEVEL_SALP == 3) 				// 3=�Թ�ͺ᷹�����
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";	
				elseif ($COM_LEVEL_SALP == 5)					// 5=���³
					$where_com_level_salp = " and b.PER_STATUS=0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";					
				elseif ($COM_LEVEL_SALP == 6)					// 6=�Թ��͹����Թ�ͺ᷹�����
					$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
			} else {
				if ($COM_LEVEL_SALP == 1)					// 1=�дѺ 8 ŧ��
					$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO_SALARY <= '08' ";		
				elseif ($COM_LEVEL_SALP == 2)					// 2=�дѺ 9 ����
					$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO_SALARY >= '09' ";
				elseif ($COM_LEVEL_SALP == 3) 				// 3=�Թ��������� �дѺ 8 ŧ��
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO_SALARY <= '08' ";	
				elseif ($COM_LEVEL_SALP == 4)					// 4=�Թ��������� �дѺ 9 ����
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO_SALARY >= '09' ";	
				elseif ($COM_LEVEL_SALP == 5)					// 5=���³
					$where_com_level_salp = " and b.PER_STATUS=0 ";					
				elseif ($COM_LEVEL_SALP == 6)					// 6=����Ҫ��÷�����
					$where_com_level_salp = "";		
				elseif ($COM_LEVEL_SALP == 7)					// 7=����
					$where_com_level_salp = " and b.PER_STATUS=1 ";
				elseif ($COM_LEVEL_SALP == 8)					// 8=�Թ���������
					$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 ";
				elseif ($COM_LEVEL_SALP == 9)					// 9=���³
					$where_com_level_salp = " and b.PER_STATUS=0 ";
				elseif ($COM_LEVEL_SALP == 10)				// 10=�١��ҧ��Ш����;�ѡ�ҹ�Ҫ��÷�����
					$where_com_level_salp = "";
			}		
			if ($list_type==5 || $list_type==6) 
				$cmd1 = " select	b.PER_ID, b.PER_SALARY, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.PAY_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID, c.PL_CODE, c.PM_CODE
								   from 	PER_PERSONAL b, PER_POSITION c , PER_ORG d, PER_POSITION e, PER_ORG f
								   where 	b.PAY_ID=c.POS_ID and c.ORG_ID = d.ORG_ID and b.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID $where_com_level_salp $where ";
			else 
				$cmd1 = " select	b.PER_ID, b.PER_SALARY, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.PAY_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID, c.PL_CODE, c.PM_CODE
								   from 	PER_PERSONAL b, PER_POSITION c , PER_ORG d
								   where 	$position_join and c.ORG_ID = d.ORG_ID $where_com_level_salp $where ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);					   
			$count_temp = $db_dpis->send_cmd($cmd1);
			//$db_dpis->show_error();
			//echo "<br>$cmd1<br>";
			$cmd_seq = 0;
			while ($data = $db_dpis->get_array()) {
				$cmd_seq++;
				$TMP_PER_ID = $data[PER_ID];		
				$CMD_OLD_SALARY = $data[PER_SALARY] + 0;
				$CMD_SALARY = 0;
				$CMD_PERCENT = 0;
				$CMD_SPSALARY = 0;
				$TMP_SALP_LEVEL = trim($data[LEVEL_NO]) ;
				$CMD_MIDPOINT = 0;
				$CMD_PL_CODE = trim($data[PL_CODE]) ;
				$CMD_PM_CODE = trim($data[PM_CODE]) ;
				if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") $POS_ID = trim($data[PAY_ID]); 
				else $POS_ID = trim($data[POS_ID]); 

				if ($CMD_MIDPOINT==0) {
					$cmd = " select LAYER_TYPE from PER_LINE where trim(PL_CODE)='$CMD_PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$LAYER_TYPE = $data2[LAYER_TYPE] + 0;

					$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
						 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
						 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$TMP_SALP_LEVEL' and LAYER_NO = 0 ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					if ($LAYER_TYPE==1 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
						$LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
						$SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
						$SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
						$SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];
					} else {
						$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
						$SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
						$SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
						$SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];
					}
	
					if($SALARY_POINT_MID > $CMD_OLD_SALARY) {
						$CMD_MIDPOINT = $SALARY_POINT_MID1;
					} else {
						$CMD_MIDPOINT = $SALARY_POINT_MID2;
					}
				}

				$AM_SHOW = 0;
				$MOV_CODE = "21345";
				$CMD_NOTE1 = $AL_NAME;
				$CMD_NOTE2 = $AM_NAME;
				
				$CMD_LEVEL = $LEVEL_NO = trim($data[LEVEL_NO])? "'".$data[LEVEL_NO]."'" : "NULL";
				$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY])? "'".$data[LEVEL_NO_SALARY]."'" : "NULL";
				$POS_ID = ($POS_ID)? $POS_ID : "NULL";
				$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
				$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
				$EN_CODE = $PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";
				
				$cmd = " select POS_NO_NAME, POS_NO, PL_NAME, PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO 
								from PER_POSITION a, PER_LINE b 
								where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POS_NO]);
				$PL_NAME = trim($data2[PL_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				$CMD_LEVEL_POS = trim($data2[LEVEL_NO]);
				$CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$CMD_POS_NO = trim($data2[POS_NO]);

				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PM_NAME = $data1[PM_NAME];
	
				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$CMD_POSITION = $PL_NAME;
				if ($PM_NAME)
					$CMD_POSITION .= "\|$PM_NAME";
				if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
				else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;

				$ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
				$ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
				$ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
				$ORG_ID_4 = (trim($data2[ORG_ID_3]))? trim($data2[ORG_ID_3]) : 0;
				$ORG_ID_5 = (trim($data2[ORG_ID_4]))? trim($data2[ORG_ID_4]) : 0;
				$ORG_ID_6 = (trim($data2[ORG_ID_5]))? trim($data2[ORG_ID_5]) : 0;
				$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = $CMD_ORG6 = $CMD_ORG7 = $CMD_ORG8 = "";
				$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, $ORG_ID_6) ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				while ( $data2 = $db_dpis2->get_array() ) {
					$temp_id = trim($data2[ORG_ID]);
					$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
					$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
					$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
					$CMD_ORG6 = ($temp_id == $ORG_ID_4)?  trim($data2[ORG_NAME]) : $CMD_ORG6;
					$CMD_ORG7 = ($temp_id == $ORG_ID_5)?  trim($data2[ORG_NAME]) : $CMD_ORG7;
					$CMD_ORG8 = ($temp_id == $ORG_ID_6)?  trim($data2[ORG_NAME]) : $CMD_ORG8;						
				}
					
				$cmd = " select OT_CODE from PER_ORG where ORG_ID=$ORG_ID_1 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$OT_CODE = trim($data2[OT_CODE]);
		
				if ($OT_CODE == "03") 
					if (!$CMD_ORG5 && !$CMD_ORG4 && $search_department_name=="�����û���ͧ") 
						$ORG_NAME_WORK = "���ӡ�û���ͧ".$CMD_ORG3." ".$CMD_ORG3;
					else 
						$ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3." ".$search_department_name);
				else $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);
			 
						$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
										CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, 
										CMD_OLD_SALARY, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
										PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
										UPDATE_USER, UPDATE_DATE, LEVEL_NO_SALARY, CMD_PERCENT, AM_SHOW, CMD_MIDPOINT,
										PL_NAME_WORK, ORG_NAME_WORK, CMD_LEVEL_POS, CMD_SEQ_NO, PL_CODE, PM_CODE, CMD_POS_NO_NAME, CMD_POS_NO)
										values	($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', $CMD_LEVEL, 
										'$search_ministry_name', '$search_department_name', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', '$CMD_ORG6', 
										'$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 
										$CMD_SPSALARY, $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', 
										'$MOV_CODE', 0, $SESS_USERID, '$UPDATE_DATE', $LEVEL_NO_SALARY, $CMD_PERCENT, $AM_SHOW, $CMD_MIDPOINT,
										'$PL_NAME_WORK', '$ORG_NAME_WORK', '$CMD_LEVEL_POS', $cmd_seq, '$CMD_PL_CODE', '$CMD_PM_CODE', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
						$db_dpis1->send_cmd($cmd);				
						//$db_dpis1->show_error();
						//echo "<br>$cmd<br>=======================<br>";     
			}	// end while 
			if ($count_temp)
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ������������������´�ѭ��Ṻ���¤��������͹����Թ��͹ [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
			// ===== ����ش insert �����Ũҡ��������������͹����Թ��͹ ����������źѭ��Ṻ���¤���� (table PER_COMDTL) ===== 

			$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
			$count_comdtl = $db_dpis->send_cmd($cmd);
			// ��������������� ���º���� ���觤�ҡ�Ѻ�������ѡ �������� mode ������ǵ���
			echo "<script>";
			echo "parent.refresh_opener('2<::>$COM_ID<::>$COM_NAME<::>$search_org_id<::>$COM_PER_TYPE<::><::>$count_comdtl<::><::>?UPD=1<::>')";
			echo "</script>";
	}			// 	if( $command == "ADD_ALL" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "UPDATE" && trim($COM_ID) ) {
		if(trim($COM_DATE)){ $COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2); }

		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����źѭ��Ṻ���¤��������͹����Թ��͹ [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	
// ============================================================
	// ������ա���׹�ѹ�����Ţͧ�ѭ��Ṻ���¤����
	if( $command == "COMMAND" && trim($COM_ID) ) {
		if(trim($COM_DATE)){ $COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2); }

		// ��� insert �����Ũҡ per_comdtl � per_personal ��� per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_OLD_SALARY, 
							CMD_SALARY, CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
							EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, LEVEL_NO_SALARY, CMD_PERCENT, 
							CMD_MIDPOINT, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ_NO, CMD_SEQ 
							from		PER_COMDTL 
							where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = $data[PER_ID] + 0;
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = $data[POS_ID] + 0;
			$tmp_POEM_ID = $data[POEM_ID] + 0;
			$tmp_POEMS_ID = $data[POEMS_ID] + 0;
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY]);
			$tmp_CMD_OLD_SALARY = $data[CMD_OLD_SALARY] + 0;
			$tmp_CMD_SALARY = $data[CMD_SALARY] + 0;
			$tmp_CMD_SPSALARY = $data[CMD_SPSALARY] + 0;
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE2])) : "";
			$tmp_CMD_PERCENT = $data[CMD_PERCENT] + 0;
			$tmp_CMD_DIFF = $tmp_CMD_SALARY - $tmp_CMD_OLD_SALARY;
			$tmp_CMD_MIDPOINT = $data[CMD_MIDPOINT] + 0;
			$PL_NAME_WORK = trim($data[PL_NAME_WORK]);
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);
			$tmp_CMD_SEQ_NO = $data[CMD_SEQ_NO] + 0;
			$tmp_CMD_SEQ = $data[CMD_SEQ] + 0;
			if ($tmp_CMD_SEQ_NO==0) $tmp_CMD_SEQ_NO = $tmp_CMD_SEQ;
						
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 �Թҷ� = 1 �ѹ
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);

			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = "";
			$cmd = " select POS_NO from PER_POSITION where POS_ID=$tmp_POS_ID  ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$SAH_POS_NO = trim($data1[POS_NO]);
			$SAH_PAY_NO = $SAH_POS_NO;

			// update status of PER_PERSONAL 
			$cmd = " update PER_PERSONAL set MOV_CODE='$tmp_MOV_CODE', PER_SALARY=$tmp_CMD_SALARY, 
							PER_DOCNO='$COM_NO', PER_DOCDATE='$COM_DATE', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
			//echo "<br>";

			// update into PER_POSITION 
			if ($tmp_POS_ID) {
				$cmd = "	update PER_POSITION set POS_SALARY=$tmp_CMD_SALARY, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
									where POS_ID=$tmp_POS_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();	
				//echo "<br>";				
			}
				
			// update and insert into PER_SALARYHIS 
			$cmd = " select SAH_ID, SAH_EFFECTIVEDATE from PER_SALARYHIS where PER_ID=$tmp_PER_ID 
							order by SAH_EFFECTIVEDATE desc, SAH_SEQ_NO desc, SAH_SALARY desc, SAH_DOCNO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_EFFECTIVEDATE = trim($data1[SAH_EFFECTIVEDATE]);
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			if (substr($before_cmd_date,0,10) < substr($tmp_SAH_EFFECTIVEDATE,0,10)) $before_cmd_date = $tmp_SAH_EFFECTIVEDATE;
			$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date', 
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'			
							where SAH_ID=$tmp_SAH_ID";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();							
			//echo "<br>";

			$cmd = " select TOTAL_SCORE, SALARY_REMARK1 from PER_KPI_FORM where PER_ID = $tmp_PER_ID and KF_CYCLE = $search_kf_cycle and 
							KF_START_DATE = '$KF_START_DATE'  and  KF_END_DATE = '$KF_END_DATE'  ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_TOTAL_SCORE = $data1[TOTAL_SCORE];
			if (!$tmp_TOTAL_SCORE) $tmp_TOTAL_SCORE = "NULL";
			$SAH_REMARK = trim($data1[SALARY_REMARK1]);

			$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);

			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$data1 = array_change_key_case($data1, CASE_LOWER);
			$SAH_ID = $data1[max_id] + 1; 			 

			$SAH_SEQ_NO = 1;
			if ($tmp_MOV_CODE=="21415") $EX_CODE = "197";
			else $EX_CODE = "024";
			$cmd = "	insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, SAH_PERCENT_UP, SAH_SALARY_UP, 
							SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, 
							EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE,
							SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY) 
							values	($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
							'$COM_DATE', '', $SESS_USERID, '$UPDATE_DATE', $tmp_CMD_PERCENT, $tmp_CMD_DIFF, 
							$tmp_CMD_SPSALARY, $SAH_SEQ_NO, '$SAH_REMARK', '$tmp_LEVEL_NO', '$SAH_POS_NO', 
							'$PL_NAME_WORK', '$ORG_NAME_WORK', '$EX_CODE', '$SAH_PAY_NO', $tmp_CMD_MIDPOINT, 
							$search_kf_year, $search_kf_cycle, $tmp_TOTAL_SCORE, 'Y', '$SM_CODE', $tmp_CMD_SEQ_NO, 
							'$SAH_ORG_DOPA_CODE', $tmp_CMD_OLD_SALARY) ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();	
//			echo "$cmd<br>";		
		}	// 		while ($data = $db_dpis->get_array())		
		
		$cmd = " update PER_COMMAND set COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);		

		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// ��������������� ���º���� ���觤�ҡ�Ѻ�������ѡ �������� mode ������ǵ���

		echo "<script>";
		echo "parent.refresh_opener('2<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::>1<::>?UPD=1<::>')";
		echo "</script>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����š�ô�ç���˹� �����������ѵԡ�ô�ç���˹�������׹�ѹ�����źѭ��Ṻ���¤��������͹����Թ��͹ [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
	}		// 	if ($COM_CONFIRM == 1 && ($command=="ADD" || $command=="UPDATE")) 	
// ============================================================
	// ������ա���觨ҡ�����Ҥ
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �觢����źѭ��Ṻ���¤�������� [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
	
	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����Ţ���Ҫ���/�١��ҧṺ���ºѭ�դ��������͹����Թ��͹ [".trim($COM_ID)." : ".$PER_ID."]");

		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// ��������������� ���º���� ���觤�ҡ�Ѻ�������ѡ �������� mode ������ǵ���
		echo "<script>";
		if ($count_comdtl > 0)
			echo "parent.refresh_opener('2<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::><::>!<::>')";
		else
			echo "parent.refresh_opener('3<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::><::>!<::>')";
		echo "</script>";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����źѭ����Т���Ҫ���/�١��ҧṺ���ºѭ�դ��������͹����Թ��͹ [$search_department_id : ".trim($COM_ID)." : ".$PER_ID."]");
//$COM_ID = "";
		echo "<script>";
		echo "parent.refresh_opener('1<::><::><::><::><::><::><::><::><::>')";
		echo "</script>";	
	}
	
	//echo "$command ==== $COM_ID ";
	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID, a.COM_LEVEL_SALP 
						from		PER_COMMAND a, PER_COMTYPE b
						where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
//echo "---->".$cmd;
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE = show_date_format($data[COM_DATE], 1);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);
		$COM_STATUS = trim($data[COM_STATUS]);
		
		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = trim($data[COM_DESC]);
		$COM_LEVEL_SALP = $data[COM_LEVEL_SALP];
	
		$search_department_id = $data[DEPARTMENT_ID];
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$search_department_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$search_department_name = $data[ORG_NAME];
		$search_ministry_id = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$search_ministry_name = $data[ORG_NAME];

	}

	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		$SELECTED_CP_ID = "";		

		if ($RPT_N) {
			$COM_TYPE = "5071";	//$COM_TYPE = "5142";  //ʻ.�֡�Ҹԡ��
			$COM_TYPE_NAME = "���������͹�Թ��͹����Ҫ���";
		} else {
			$COM_TYPE = "1005";
			$COM_TYPE_NAME = "���������͹�Թ��͹";
		}
		$search_per_name = "";
		$search_per_surname = "";		

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$search_ministry_id = "";
			$search_ministry_name = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$search_department_id = "";
			$search_department_name = "";
		} // end if
		if($SESS_USERGROUP_LEVEL < 5){ 
			$search_org_id = "";
			$search_org_name = "";
		} // end if
		$_select_level_no = array("O1","O2","O3","O4","K1","K2","K3","K4","K5","D1","D2","M1","M2");
	} // end if		
?>