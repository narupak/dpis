<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//===tab ที่ 2 - form11 - show_topic=2
	if (($COM_SITE=="personal_workflow" && $ORG_ID) || ($SESS_USERGROUP_LEVEL < 5 && $ORG_ID)) {
		$ORG_ID_DTL = $ORG_ID;
		$ORG_NAME_DTL = $ORG_NAME;
	} else if ($SESS_USERGROUP_LEVEL < 5){
		$ORG_ID_DTL = "";	//$ORG_ID_DTL = "0";
		$ORG_NAME_DTL = "";
	}	
	
	if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";
	if (is_null($ORG_ID) || $ORG_ID=="NULL"|| trim($ORG_ID)=="") $ORG_ID=0;
	if ($_POST[ORG_ID_DTL]!="") $ORG_ID_DTL = $_POST[ORG_ID_DTL];
	if (!$ORG_ID_DTL) $ORG_ID_DTL = "NULL";

	if(!$current_page) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if(!$search_kf_year)	$search_kf_year = $KPI_BUDGET_YEAR;
	if(!isset($search_kf_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$tmp_COM_DATE =  save_date($COM_DATE);
	$COM_PER_TYPE = (trim($COM_PER_TYPE))? $COM_PER_TYPE : 3;	
	$list_type = (trim($list_type))? $list_type : 4;	
	$COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 1; 
	$COM_NOTE=trim($COM_NOTE);
	
	$KF_START_DATE = ($search_kf_year-544) . "-10-01";
	$KF_END_DATE = ($search_kf_year-543) . "-09-30";

	$_select_level_no_str = implode("','",$_select_level_no);
	
	$position_table = "PER_POS_EMPSER";
	$position_join = "b.POEMS_ID=c.POEMS_ID";
	$position_field = "c.EP_CODE as PL_CODE";
	
	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, COM_LEVEL_SALP, ORG_ID, COM_STATUS) 
						VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$tmp_COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						'$COM_TYPE', 0, $SESS_USERID, '$UPDATE_DATE', $DEPARTMENT_ID, $COM_LEVEL_SALP, $ORG_ID_DTL, '') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "<br>$cmd<br>=======================<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_ADD_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
		// ===== เริ่มต้น insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
		$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
		
		if ($list_type==5) $position_join = $in_province_join;		
		elseif ($list_type==6) $position_join = $out_province_join;		
	  if($SELECTED_CP_ID){
		$cmd1 = " select	a.CP_ID, a.PER_ID, b.PER_SALARY, a.AL_CODE, a.CD_SALARY, a.CD_PERCENT, a.CD_EXTRA_SALARY, a.CD_MIDPOINT, b.LEVEL_NO, 
										b.LEVEL_NO_SALARY, b.POS_ID, b.POEM_ID, b.POEMS_ID, $position_field, d.OT_CODE, d.PV_CODE, d.CT_CODE
						   from 	PER_COMPENSATION_TEST_DTL a, PER_PERSONAL b, $position_table c , PER_ORG d, PER_COMPENSATION_TEST e
						   where 	a.CP_ID in ($SELECTED_CP_ID) and a.CP_ID=e.CP_ID and a.PER_ID=b.PER_ID and $position_join and e.ORG_ID = d.ORG_ID(+) $where_com_level_salp ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$count_temp = $db_dpis->send_cmd($cmd1);
		//$db_dpis->show_error();
		//echo "<br>$cmd1<br>";
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$TMP_OT_CODE = trim($data[OT_CODE]) ;
			$TMP_PV_CODE = trim($data[PV_CODE]) ;
			$TMP_CT_CODE = trim($data[CT_CODE]) ;
			$show_data = 0;
			if ($list_type==1) {
				if ($TMP_OT_CODE = "01") $show_data = 1;
			} elseif ($list_type==2) { 
				if ($TMP_PV_CODE = "$PV_CODE") $show_data = 1;		
			} elseif ($list_type==3) {
				if ($TMP_CT_CODE = "$CT_CODE") $show_data = 1;
			} else {
				 $show_data = 1;
			}
			if ($show_data==1) { 
				$cmd_seq++;
				$CP_ID = $data[CP_ID];		
				$TMP_PER_ID = $data[PER_ID];		
				$CMD_OLD_SALARY = $data[PER_SALARY] + 0;
				$TMP_AL_CODE = trim($data[AL_CODE]) ;
				$CMD_SALARY = $data[CD_SALARY] + $CMD_OLD_SALARY;
				$CMD_PERCENT = $data[CD_PERCENT] + 0;
				$CMD_SPSALARY = $data[CD_EXTRA_SALARY] + 0;
				$CMD_SPSALARY = 0;
				$TMP_SALP_LEVEL = trim($data[LEVEL_NO]) ;
				$CMD_MIDPOINT = $data[CD_MIDPOINT] + 0;
				$CMD_PL_CODE = trim($data[PL_CODE]) ;

				$cmd = " select a.AM_CODE, AL_NAME, AM_NAME, AM_SHOW,a.AL_YEAR,a.AL_CYCLE from PER_ASSESS_LEVEL a, PER_ASSESS_MAIN b 
								  where a.AL_YEAR = b.AM_YEAR and a.AL_CYCLE = b.AM_CYCLE and a.AM_CODE = b.AM_CODE and AL_CODE='$TMP_AL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$AM_CODE = trim($data2[AM_CODE]);
				$AL_NAME = trim($data2[AL_NAME]);
				$AM_NAME = trim($data2[AM_NAME]);
				$AL_YEAR = trim($data2[AL_YEAR]);
				$AL_CYCLE = trim($data2[AL_CYCLE]);
				$AM_SHOW = $data2[AM_SHOW] + 0;
//echo " $cmd [ $AM_SHOW ] => $TMP_PER_ID :: $TMP_AL_CODE ( $AM_NAME  )  !! $AL_YEAR //$AL_CYCLE <br>";			

				$MOV_CODE = "21305";
				if ($AM_CODE == 32 || $AM_NAME == "พอใช้") 		$MOV_CODE = "21315";
				elseif ($AM_CODE == 33 || $AM_NAME == "ดี")		$MOV_CODE = "21325";
				elseif ($AM_CODE == 34 || $AM_NAME == "ดีมาก") 	$MOV_CODE = "21335";
				elseif ($AM_CODE == 35 || $AM_NAME == "ดีเด่น") 		$MOV_CODE = "21345";

				if ($CMD_SPSALARY  > 0)		$MOV_CODE = "21415";

				$cmd = " select CP_CYCLE, CP_END_DATE from PER_COMPENSATION_TEST where CP_ID=$CP_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CP_CYCLE = $data2[CP_CYCLE];
				$CP_END_DATE = trim($data2[CP_END_DATE]);
				$CMD_DATE = substr($CP_END_DATE, 0, 4) . "-10-01";

				$CMD_NOTE1 = $AL_NAME;
				$CMD_NOTE2 = $AM_NAME;
				
				$CMD_LEVEL = $LEVEL_NO = trim($data[LEVEL_NO])? "'".$data[LEVEL_NO]."'" : "NULL";
				$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY])? "'".$data[LEVEL_NO_SALARY]."'" : "NULL";
				$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : "NULL";
				$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
				$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
				$EN_CODE = $PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";

				$cmd = " select POEMS_NO_NAME, POEMS_NO, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 
								from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
								where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POEMS_NO = trim($data2[POEMS_NO]);					
				$EP_NAME = trim($data2[EP_NAME]);
				$CMD_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);
				$CMD_POS_NO = trim($data2[POEMS_NO]);
				$CMD_POSITION = $EP_NAME;

				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POEMS_ID=$POEMS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
			
				$PL_NAME_WORK = $EP_NAME.' '.$POSITION_LEVEL;
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
					if (!$CMD_ORG5 && !$CMD_ORG4 && $DEPARTMENT_NAME=="กรมการปกครอง") 
						$ORG_NAME_WORK = "ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
					else 
						$ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3." ".$DEPARTMENT_NAME);
				else $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);
			 
				if (!$ORG_ID_DTL || $ORG_ID_DTL == "NULL" || $ORG_ID_DTL == $ORG_ID_1) { 
					if ($COM_LEVEL_SALP==1 && $CMD_SALARY > $CMD_OLD_SALARY) {
						$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
										CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, 
										CMD_OLD_SALARY, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
										PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
										UPDATE_USER, UPDATE_DATE, LEVEL_NO_SALARY, CMD_PERCENT, AM_SHOW, CMD_MIDPOINT,
										PL_NAME_WORK, ORG_NAME_WORK, CMD_LEVEL_POS, CMD_SEQ_NO, CMD_POS_NO_NAME, CMD_POS_NO)
										values	($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', $CMD_LEVEL, 
										'$MINISTRY_NAME', '$DEPARTMENT_NAME', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', '$CMD_ORG6', 
										'$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 
										$CMD_SPSALARY, $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', 
										'$MOV_CODE', 0, 	$SESS_USERID, '$UPDATE_DATE', $LEVEL_NO_SALARY, $CMD_PERCENT, $AM_SHOW, $CMD_MIDPOINT,
										'$PL_NAME_WORK', '$ORG_NAME_WORK', '$CMD_LEVEL_POS', $cmd_seq, '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
						$db_dpis1->send_cmd($cmd);				
						//$db_dpis1->show_error();
						//echo "<br>$cmd<br>=======================<br>";
					}
				}
			}
		}	// end while
	  }
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $ADD_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
		// ===== สิ้นสุด insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) ===== 
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "ADD_ALL" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, COM_LEVEL_SALP, ORG_ID) 
						VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$tmp_COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						'$COM_TYPE', 0, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $COM_LEVEL_SALP, $ORG_ID_DTL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "<br>$cmd<br>=======================<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_ADD_TITLE$COM_TYPE_NM [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
		// ===== เริ่มต้น insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
		if ($RPT_N) {
			if ($ORG_ID_DTL && $ORG_ID_DTL != "NULL") $where .= " and c.ORG_ID = $ORG_ID_DTL ";		 
			if ($list_type==5) $where .= " and f.ORG_ID = $ORG_ID_DTL ";
			if ($list_type==6) $where .= " and f.ORG_ID != $ORG_ID_DTL ";
		}		
		$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
		if ($list_type==5 || $list_type==6) 
			$cmd1 = " select	b.PER_ID, b.PER_SALARY, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.POS_ID, b.POEM_ID, b.POEMS_ID, $position_field, d.OT_CODE, d.PV_CODE, d.CT_CODE
							   from 	PER_PERSONAL b, PER_POS_EMPSER c , PER_ORG d, PER_POS_EMPSER e, PER_ORG f
							   where 	b.PAY_ID=c.POEMS_ID and c.ORG_ID = d.ORG_ID and b.POEMS_ID=e.POEMS_ID and e.ORG_ID=f.ORG_ID $where_com_level_salp $where ";
		else 
			$cmd1 = " select	b.PER_ID, b.PER_SALARY, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.POS_ID, b.POEM_ID, b.POEMS_ID, $position_field, d.OT_CODE, d.PV_CODE, d.CT_CODE
							   from 	PER_PERSONAL b, $position_table c , PER_ORG d
							   where 	$position_join and c.ORG_ID = d.ORG_ID $where_com_level_salp $where ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$count_temp = $db_dpis->send_cmd($cmd1);
		//$db_dpis->show_error();
		//echo "<br>$cmd1<br>";
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$TMP_OT_CODE = trim($data[OT_CODE]) ;
			$TMP_PV_CODE = trim($data[PV_CODE]) ;
			$TMP_CT_CODE = trim($data[CT_CODE]) ;
			$show_data = 0;
			if ($list_type==1) {
				if ($TMP_OT_CODE = "01") $show_data = 1;
			} elseif ($list_type==2) { 
				if ($TMP_PV_CODE = "$PV_CODE") $show_data = 1;		
			} elseif ($list_type==3) {
				if ($TMP_CT_CODE = "$CT_CODE") $show_data = 1;
			} else {
				 $show_data = 1;
			}
			if ($show_data==1) { 
				$cmd_seq++;
				$TMP_PER_ID = $data[PER_ID];		
				$CMD_OLD_SALARY = $data[PER_SALARY] + 0;
				$CMD_SALARY = 0;
				$CMD_PERCENT = 0;
				$CMD_SPSALARY = 0;
				$TMP_SALP_LEVEL = trim($data[LEVEL_NO]) ;
				$CMD_MIDPOINT = 0;
				$CMD_PL_CODE = trim($data[PL_CODE]) ;
				$CMD_MIDPOINT= $CMD_OLD_SALARY;
				$AM_SHOW = 0;
				$MOV_CODE = "21345";
				$CMD_DATE = ($search_kf_year-543) . "-10-01";
				$CMD_NOTE1 = $AL_NAME;
				$CMD_NOTE2 = $AM_NAME;
				
				$CMD_LEVEL = $LEVEL_NO = trim($data[LEVEL_NO])? "'".$data[LEVEL_NO]."'" : "NULL";
				$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY])? "'".$data[LEVEL_NO_SALARY]."'" : "NULL";
				$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : "NULL";
				$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
				$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
				$EN_CODE = $PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";

				$cmd = " select POEMS_NO_NAME, POEMS_NO, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO 
								from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
								where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POEMS_NO = trim($data2[POEMS_NO]);					
				$EP_NAME = trim($data2[EP_NAME]);
				$CMD_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);
				$CMD_POS_NO = trim($data2[POEMS_NO]);
				$CMD_LEVEL_POS = trim($data2[LEVEL_NO]);
				$CMD_POSITION = $EP_NAME;

				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POEMS_ID=$POEMS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
			
				$PL_NAME_WORK = $EP_NAME.' '.$POSITION_LEVEL;
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
					if (!$CMD_ORG5 && !$CMD_ORG4 && $DEPARTMENT_NAME=="กรมการปกครอง") 
						$ORG_NAME_WORK = "ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
					else 
						$ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3." ".$DEPARTMENT_NAME);
				else $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);
			 
				$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
								CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, 
								CMD_OLD_SALARY, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
								PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
								UPDATE_USER, UPDATE_DATE, LEVEL_NO_SALARY, CMD_PERCENT, AM_SHOW, CMD_MIDPOINT,
								PL_NAME_WORK, ORG_NAME_WORK, CMD_LEVEL_POS, CMD_SEQ_NO, CMD_POS_NO_NAME, CMD_POS_NO)
								values	($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', $CMD_LEVEL, 
								'$MINISTRY_NAME', '$DEPARTMENT_NAME', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', '$CMD_ORG6', 
								'$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 
								$CMD_SPSALARY, $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', 
								'$MOV_CODE', 0, $SESS_USERID, '$UPDATE_DATE', $LEVEL_NO_SALARY, $CMD_PERCENT, $AM_SHOW, $CMD_MIDPOINT,
								'$PL_NAME_WORK', '$ORG_NAME_WORK','$CMD_LEVEL_POS', $cmd_seq, '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
				$db_dpis1->send_cmd($cmd);				
				//$db_dpis1->show_error();
				//echo "<br>$cmd<br>=======================<br>";     
			}
		}	// end while 
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $ADD_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "ADD_ALL" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "UPDATE" && trim($COM_ID) ) {

		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$tmp_COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_EDIT_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	
// ============================================================
	// เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง
	if( $command == "COMMAND" && trim($COM_ID) ) {
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
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
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);

			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = "";
			$cmd = " select POEMS_NO from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";	
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$SAH_POS_NO = trim($data2[POEMS_NO]);				
			$SAH_PAY_NO = $SAH_POS_NO;

			// update status of PER_PERSONAL 
			$cmd = " update PER_PERSONAL set MOV_CODE='$tmp_MOV_CODE', PER_SALARY=$tmp_CMD_SALARY, 
							PER_DOCNO='$COM_NO', PER_DOCDATE='$tmp_COM_DATE', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
			//echo "<br>";

			// update into PER_POS_EMPSER 
//			if ($tmp_POEMS_ID) {
//				$cmd = "	update PER_POS_EMPSER set POEM_MIN_SALARY=$tmp_CMD_SALARY, POEM_MAX_SALARY=$tmp_CMD_SALARY, 
//									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
//									where POEMS_ID=$tmp_POEMS_ID ";
//				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();	
				//echo "<br>";				
//			}
				
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
//			$db_dpis1->show_error();							
//			echo "<br>";

			$cmd = " update PER_SALARYHIS set SAH_LAST_SALARY='N' 
							where PER_ID=$tmp_PER_ID";
			$db_dpis1->send_cmd($cmd);	

			$cmd = " select sum(TOTAL_SCORE) as TOTAL_SCORE from PER_KPI_FORM where PER_ID = $tmp_PER_ID and  
							KF_START_DATE >= '$KF_START_DATE'  and  KF_END_DATE <= '$KF_END_DATE'  ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_TOTAL_SCORE = $data1[TOTAL_SCORE] + 0; 			 

			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$data1 = array_change_key_case($data1, CASE_LOWER);
			$SAH_ID = $data1[max_id] + 1; 			 

			$SAH_SEQ_NO = $search_kf_cycle = 1;
			$SAH_REMARK = "NULL";
			if ($tmp_MOV_CODE=="21415") $EX_CODE = "197";
			else $EX_CODE = "024";
			$SAH_LAST_SALARY = "Y";
			$cmd = "	insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, SAH_PERCENT_UP, SAH_SALARY_UP, 
							SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, 
							EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, 
							SAH_LAST_SALARY, SAH_CMD_SEQ,
                                                        SAH_OLD_SALARY) 
							values	($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
							'$tmp_COM_DATE', '', $SESS_USERID, '$UPDATE_DATE', $tmp_CMD_PERCENT, $tmp_CMD_DIFF, 
							$tmp_CMD_SPSALARY, $SAH_SEQ_NO, $SAH_REMARK, '$tmp_LEVEL_NO', '$SAH_POS_NO', 
							'$PL_NAME_WORK', '$ORG_NAME_WORK', '$EX_CODE', '$SAH_PAY_NO', $tmp_CMD_MIDPOINT, 
							$search_kf_year, $search_kf_cycle, $tmp_TOTAL_SCORE, '$SAH_LAST_SALARY', $tmp_CMD_SEQ_NO, 
                                                        $tmp_CMD_OLD_SALARY) ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();	
//			echo "$cmd<br>";		
		}	// 		while ($data = $db_dpis->get_array())		
		
		$cmd = " update PER_COMMAND set COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_CONFIRM_TITLE$COM_TYPE_NM [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
	}		// 	if ($COM_CONFIRM == 1 && ($command=="ADD" || $command=="UPDATE")) 	
// ============================================================
	// เมื่อมีการส่งจากภูมิภาค
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_SEND_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
	
	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID)){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $DEL_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$PER_ID."]");
		$PER_ID = "";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_DEL_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO."]");
		$COM_ID = "";
	}
	
	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID , a.ORG_ID, a.COM_LEVEL_SALP 
						from	PER_COMMAND a, PER_COMTYPE b
						where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
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
	
		$ORG_ID_DTL = $data[ORG_ID];
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$ORG_NAME_DTL = "";
		if($ORG_ID_DTL){
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_DTL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ORG_NAME_DTL = $data[ORG_NAME];
		}
		$DEPARTMENT_NAME = $MINISTRY_ID = "";
		if($DEPARTMENT_ID){
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];
		}
		$MINISTRY_NAME = "";
		if($MINISTRY_ID){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}
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
			$COM_TYPE = "5071";
			$COM_TYPE_NAME = "คำสั่งเลื่อนเงินเดือนข้าราชการ";
		} else {
			$COM_TYPE = "1005";
			$COM_TYPE_NAME = "คำสั่งเลื่อนเงินเดือน";
		}
		$search_per_name = "";
		$search_per_surname = "";		

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
		if($SESS_USERGROUP_LEVEL < 5){ 
			$ORG_ID_DTL = "";
			$ORG_NAME_DTL = "";
		} // end if
		$_select_level_no = array("E1","E2","E7","E3","E4","E5","S1","S2","S3");
	} // end if		
?>