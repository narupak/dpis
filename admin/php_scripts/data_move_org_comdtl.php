<?php
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//----------------------------------------------------------------------------------
		switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
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
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
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
	//----------------------------------------------------------------------------------

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$COM_PER_TYPE = 0;
	$ES_CODE = "02";
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	// ============================================================	
	if( ($command == "CHANGE" || $command == "MOVE") && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$COM_DATE =  save_date($COM_DATE);
		$CMD_DATE =  save_date($CMD_DATE);

		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, ORG_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						'$COM_TYPE', 1, '', $DEPARTMENT_ID, NULL, $SESS_USERID, '$UPDATE_DATE') ";
		//echo $cmd;
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd1 = " select	PER_ID, PER_TYPE, PER_SALARY, LEVEL_NO, POS_ID, POEM_ID, POEMS_ID, POT_ID 
						   from 	PER_PERSONAL 
						   where 	PER_STATUS = 1 and DEPARTMENT_ID = $DEPARTMENT_ID
						  order by PER_TYPE, PER_NAME, PER_SURNAME";
		$db_dpis->send_cmd($cmd1);
///		$db_dpis->show_error();
///		echo "<br>$cmd1<br>";
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$cmd_seq++;
			$TMP_PER_ID = $data[PER_ID];		
			$PER_TYPE = $data[PER_TYPE];		
			$CMD_OLD_SALARY = $data[PER_SALARY] + 0;
			$CMD_SALARY = $CMD_OLD_SALARY;
			$CMD_SPSALARY = 0;
			$TMP_SALP_LEVEL = trim($data[LEVEL_NO]) ;
			$POS_ID = trim($data[POS_ID]); 
			$POEM_ID = trim($data[POEM_ID]); 
			$POEMS_ID = trim($data[POEMS_ID]); 
			$POT_ID = trim($data[POT_ID]); 

			$CMD_LEVEL = $LEVEL_NO = trim($data[LEVEL_NO])? "'".$data[LEVEL_NO]."'" : "NULL";
			$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY])? "'".$data[LEVEL_NO_SALARY]."'" : "NULL";
			$POS_ID = ($POS_ID)? $POS_ID : "NULL";
			$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
			$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
			$POT_ID = (trim($data[POT_ID]))? $data[POT_ID] : "NULL";
			$EN_CODE = "NULL";

			$CMD_PL_CODE = $CMD_PM_CODE = $CMD_PT_CODE = $CMD_PN_CODE = $CMD_EP_CODE = $CMD_TP_CODE = $PM_NAME = "";
			if ($PER_TYPE==1) {
				$cmd = " select POS_NO, POS_NO_NAME, a.PL_CODE, PL_NAME, PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO, PT_CODE 
								from PER_POSITION a, PER_LINE b 
								where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POS_NO]);
				$POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$PL_NAME = trim($data2[PL_NAME]);
				$CMD_PL_CODE = trim($data2[PL_CODE]) ;
				$CMD_PM_CODE = trim($data2[PM_CODE]) ;
				$CMD_LEVEL_POS = trim($data2[LEVEL_NO]);
				$CMD_PT_CODE = trim($data2[PT_CODE]);

				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$CMD_PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PM_NAME = $data1[PM_NAME];
		
				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
			} elseif ($PER_TYPE==2) {
				$cmd = " select POEM_NO, POEM_NO_NAME, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, a.LEVEL_NO 
								from PER_POS_EMP a, PER_POS_NAME b 
								where POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POEM_NO]);
				$POS_NO_NAME = trim($data2[POEM_NO_NAME]);
				$PL_NAME = trim($data2[PN_NAME]);
				$CMD_PN_CODE = trim($data2[PN_CODE]) ;
				$CMD_LEVEL_POS = trim($data2[LEVEL_NO]);
			} elseif ($PER_TYPE==3) {
				$cmd = " select POEMS_NO, POEMS_NO_NAME, a.EP_CODE, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO 
								from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
								where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POEMS_NO]);
				$POS_NO_NAME = trim($data2[POEMS_NO_NAME]);
				$PL_NAME = trim($data2[EP_NAME]);
				$CMD_EP_CODE = trim($data2[EP_CODE]) ;
				$CMD_LEVEL_POS = trim($data2[LEVEL_NO]);
			} elseif ($PER_TYPE==4) {
				$cmd = " select POT_NO, POT_NO_NAME, a.TP_CODE, TP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO 
								from PER_POS_TEMP a, PER_TEMP_POS_NAME b 
								where POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POT_NO]);
				$POS_NO_NAME = trim($data2[POT_NO_NAME]);
				$PL_NAME = trim($data2[TP_NAME]);
				$CMD_TP_CODE = trim($data2[TP_CODE]) ;
				$CMD_LEVEL_POS = trim($data2[LEVEL_NO]);
			}

			if ($POS_NO || $PL_NAME)
				$CMD_POSITION = "$POS_NO\|$PL_NAME\|$POS_NO";
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
				
			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE, OT_CODE from PER_ORG where ORG_ID=$ORG_ID_1 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = (trim($data2[ORG_ID_REF]))? trim($data2[ORG_ID_REF]) : "NULL";			
			$CT_CODE = (trim($data2[CT_CODE]))? "'".trim($data2[CT_CODE])."'" : "'140'";
			$PV_CODE = (trim($data2[PV_CODE]))? "'".trim($data2[PV_CODE])."'" : "NULL";
			$AP_CODE = (trim($data2[AP_CODE]))? "'".trim($data2[AP_CODE])."'" : "NULL";
			$OT_CODE = trim($data2[OT_CODE]);

			if ($CMD_ORG3=="-") $CMD_ORG3 = "";
			if ($CMD_ORG4=="-") $CMD_ORG4 = "";
			if ($CMD_ORG5=="-") $CMD_ORG5 = "";
			if ($OT_CODE == "03") 
				if (!$CMD_ORG5 && !$CMD_ORG4 && $search_department_name=="กรมการปกครอง") 
					$ORG_NAME_WORK = "ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
				else 
					$ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
			elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3." ".$CMD_ORG2);
			else $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);
		 
			//$CMD_ORG2 = $search_department_name;  // 11 มิ.ย. 2562 ปิดส่วนนี้ไป เพราะว่าทำให้โปรแกรมส่วนที่จะทำการเปลี่ยนชื่อกรมใหม่ได้ค่า เป็นชื่อกรมเดิมไปอัพเดท 
			if ($command == "CHANGE") $CMD_ORG1 = $search_ministry_name;

			$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, 
							CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
							CMD_ORG8, CMD_OLD_SALARY,  PL_CODE, PN_CODE, EP_CODE, TP_CODE, POS_ID, POEM_ID, POEMS_ID, POT_ID, 
							LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
							CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, UPDATE_USER, UPDATE_DATE, 
							PL_NAME_WORK, ORG_NAME_WORK, CMD_LEVEL_POS, CMD_SEQ_NO, PM_CODE)
							values	($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
							$CMD_LEVEL, '$CMD_ORG1', '$search_department_name', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
							'$CMD_ORG6', '$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, '$CMD_PL_CODE', '$CMD_PN_CODE', 
							'$CMD_EP_CODE', '$CMD_TP_CODE', $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, $LEVEL_NO, $CMD_SALARY, $CMD_SPSALARY, 
							'$CMD_PL_CODE', '$CMD_PN_CODE', '$CMD_EP_CODE', '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 
							0, $SESS_USERID, '$UPDATE_DATE', '$PL_NAME_WORK', '$ORG_NAME_WORK', '$CMD_LEVEL_POS', 
							$cmd_seq, '$CMD_PM_CODE') ";
			$db_dpis1->send_cmd($cmd);				
			//$db_dpis1->show_error();
			//echo "<br>$cmd<br>=======================<br>";

			// update status of PER_PERSONAL 
			$cmd = " 	update PER_PERSONAL set  MOV_CODE='$MOV_CODE', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								where PER_ID=$TMP_PER_ID ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();	
			//echo "<br>";

			$tmp_date = explode("-", $CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$POH_ASS_ORG = "";
			$cmd = " select ORG_NAME from PER_PERSONAL a, PER_ORG_ASS b where PER_ID=$TMP_PER_ID and a.ORG_ID=b.ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ASS_ORG = $data2[ORG_NAME];

			// update and insert into PER_POSITIONHIS	
			$cmd = " update PER_POSITIONHIS set POH_LAST_POSITION='N' where PER_ID=$TMP_PER_ID ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();

			// update and insert into PER_POSITIONHIS
			$cmd = " select POH_ID,POH_EFFECTIVEDATE  from PER_POSITIONHIS where PER_ID=$TMP_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc, POH_SEQ_NO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_POH_ID = trim($data1[POH_ID]);
			$tmp_POH_EFFECTIVEDATE = trim($data1[POH_EFFECTIVEDATE]);
			if($tmp_POH_EFFECTIVEDATE==$tmp_CMD_DATE	){		 //ถ้าวันที่มีผลที่เลือกมานี้ = วันที่มีผลของเรคคอร์ดล่าสุดก่อนหน้านี้ให้  POH_ENDDATE=วันที่มีผลเลย   //21/12/2011
				$tmp_END_DATE=$tmp_CMD_DATE;
			}else{
				$tmp_END_DATE=$before_cmd_date;
			}
			$cmd = " update PER_POSITIONHIS set POH_ENDDATE='$tmp_END_DATE' where POH_ID=$tmp_POH_ID ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();				
			//echo "$cmd <br>";
                        if ($command == "MOVE") $CMD_ORG2 = $search_department_name; //เซตกรมเป็นปัจจุบัน กรณีย้ายกรมแล้วไม่ได้กรอกช่องชื่อกรมใหม่ ทำให้ค่าของ กรมในประวัติเป็นว่าง
			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$POH_ID = $data[max_id] + 1; 			 
			$cmd = " 	insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, 
							POH_DOCDATE, POH_ENDDATE, POH_POS_NO, POH_POS_NO_NAME, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, 
							EP_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, POH_UNDER_ORG4, POH_UNDER_ORG5, 
							POH_ASS_ORG, POH_SALARY, POH_SALARY_POS, POH_REMARK, POH_REMARK1, POH_REMARK2, POH_ORG1, 
							POH_ORG2, POH_ORG3, POH_PL_NAME, POH_ORG, POH_CMD_SEQ, 
							POH_LAST_POSITION, POH_ISREAL, ES_CODE, POH_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  	values ($POH_ID, $TMP_PER_ID, '$CMD_DATE', '$MOV_CODE', '$COM_NO', '$COM_DATE', NULL, '$POS_NO', '$POS_NO_NAME', 
							'$CMD_PM_CODE', $LEVEL_NO, '$CMD_PL_CODE', '$CMD_PN_CODE', '$CMD_EP_CODE', '$CMD_PT_CODE', 
							$CT_CODE, $PV_CODE, $AP_CODE, '2', $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID_1, 
							'$ORG_NAME_4', '$ORG_NAME_5', '$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', '$POH_ASS_ORG', 
							$CMD_SALARY, $CMD_SPSALARY, '$COM_NOTE', '$CMD_NOTE1', '$CMD_NOTE2', '$CMD_ORG1', '$CMD_ORG2', 
							'$CMD_ORG3', '$PL_NAME_WORK', '$ORG_NAME_WORK', $cmd_seq, 
							'Y', 'Y', '$ES_CODE', '$CMD_LEVEL_POS', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
			$cmd1 = " SELECT POH_ID FROM PER_POSITIONHIS WHERE POH_ID = $POH_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				//echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				//echo "<br>end ". ++$i  ."=======================<br>";
			}
		}	// end while

		if ($command == "CHANGE") {
			$cmd = " update PER_ORG set ORG_NAME='$CMD_ORG2' where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();				
			//echo "$cmd <br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลบัญชีแนบท้ายคำสั่งเปลี่ยนชื่อกรม [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
		} elseif ($command == "MOVE") {
			$cmd = " update PER_ORG set ORG_ID_REF=$CMD_ORG_ID1 where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();				
			//echo "$cmd <br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลบัญชีแนบท้ายคำสั่งโอนย้ายกรม [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
		}

		$COM_DATE = show_date_format($COM_DATE, 1);
		$CMD_DATE = show_date_format($CMD_DATE, 1);

	}		// 	if( $command == "CHANGE" && trim($COM_ID) ) 
	// ============================================================	

	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_PER_TYPE = 0;
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		$CMD_DATE = "";
		$CMD_ORG1 = "";
		$CMD_ORG_ID1 = "";
		$CMD_ORG2 = "";
		$MOV_CODE = "";
		$MOV_NAME = "";
		
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if		
	} // end if		
?>