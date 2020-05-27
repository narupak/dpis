<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	
	include("../php_scripts/connect_file.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$COM_DATE =  save_date($COM_DATE);
		
		$cmd = " insert into PER_COMMAND 
						(COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
				 VALUES 
						($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', 1, 
						'$COM_TYPE', 0, '', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลบัญชีแนบท้ายคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
		// ===== เริ่มต้น insert ข้อมูลจาก PER_POS_EMP เพื่อจัดระบบตำแหน่งลูกจ้างประจำ เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
		$cmd = " select a.POEM_ID, a.POEM_NO_NAME, a.POEM_NO, a.PN_CODE, b.PN_NAME, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, b.PN_CODE_NEW, b.LEVEL_NO 
				 from PER_POS_EMP a, PER_POS_NAME b 
				 where a.POEM_STATUS = 1 and a.PN_CODE=b.PN_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID
				order by a.POEM_ID ";				  
		$count_temp = $db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$POEM_ID = $data[POEM_ID];
			$cmd = " select PER_ID, PER_SALARY, LEVEL_NO as PER_LEVEL, PER_CARDNO
					 from PER_PERSONAL where POEM_ID=$POEM_ID and PER_STATUS = 1 ";				  
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POEM_NO_NAME = trim($data[POEM_NO_NAME]);
			$POEM_NO = trim($data[POEM_NO]);
			$PN_CODE = trim($data[PN_CODE]);
			$PN_NAME = trim($data[PN_NAME]);
			$PN_CODE_NEW = trim($data[PN_CODE_NEW]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$CMD_POSITION = $PN_NAME;
			$ORG_ID_1 = (trim($data[ORG_ID]))? trim($data[ORG_ID]) : 0;
			$ORG_ID_2 = (trim($data[ORG_ID_1]))? trim($data[ORG_ID_1]) : 0;
			$ORG_ID_3 = (trim($data[ORG_ID_2]))? trim($data[ORG_ID_2]) : 0;

			$cmd_seq++;
			$TMP_PER_ID = trim($data2[PER_ID]);
			$CMD_OLD_SALARY = trim($data2[PER_SALARY]);
			$CMD_SALARY = trim($data2[PER_SALARY]);
			$CMD_SPSALARY = 0;
			$TMP_SALP_LEVEL = trim($data2[PER_LEVEL]);
			$PER_CARDNO = trim($data2[PER_CARDNO]);
			if($BKK_FLAG==1) $MOV_CODE = "40";
			else $MOV_CODE = "10710";
			$CMD_DATE = trim($COM_DATE);
				
			$CMD_LEVEL = $data2[PER_LEVEL];
			$POEM_ID = (trim($POEM_ID))? $POEM_ID : 'NULL';
			$TMP_PER_ID = (trim($TMP_PER_ID))? $TMP_PER_ID : $POEM_ID+900000000;
			$POS_ID = 'NULL';
			$POEMS_ID = 'NULL';		
			$PN_CODE = trim($PN_CODE)? "'".$PN_CODE."'" : "NULL";
			$PN_CODE_NEW = trim($PN_CODE_NEW)? "'".$PN_CODE_NEW."'" : "NULL";
			$PL_CODE = $EP_CODE = "NULL";
			$PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";	
			$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
				$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
				$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
			}
				
			$cmd = " select EN_CODE from PER_EDUCATE where PER_ID=$TMP_PER_ID and EDU_TYPE like '%1%' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EN_CODE = (trim($data2[EN_CODE]))? "'".$data2[EN_CODE]."'" : "NULL";
				
			$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
			if (!$CMD_OLD_SALARY) $CMD_OLD_SALARY = 0; 
			if (!$CMD_SALARY) $CMD_SALARY = 0; 

			$cmd = " insert into PER_COMDTL
						(	COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
							CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, 
							CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
							POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
							PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
							CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, CMD_SAL_CONFIRM,   
							PER_CARDNO, CMD_POS_NO_NAME, CMD_POS_NO, UPDATE_USER, UPDATE_DATE )
					 values
						(	$COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
							'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
							$CMD_OLD_SALARY, $PL_CODE, $PN_CODE, $EP_CODE, '$CMD_AC_NO', '$CMD_ACCOUNT', 
							$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 0, 
							$PL_CODE_ASSIGN, $PN_CODE_NEW, $EP_CODE_ASSIGN, 
							'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', '$CMD_DATE2', 0, 
							'$PER_CARDNO', '$POEM_NO_NAME', '$POEM_NO', $SESS_USERID, '$UPDATE_DATE' ) ";			  
			$db_dpis1->send_cmd($cmd);
			//echo "$cmd<br>==================<br>";
			//$db_dpis1->show_error();
			//echo "<br>end ". ++$i  ."=======================<br>";
		}	// end while
		
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลรายละเอียดบัญชีแนบท้ายคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");

		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
		echo "<script>";
		echo "parent.refresh_opener('2<::>$COM_ID<::>$COM_NAME<::>$search_org_id<::>$COM_PER_TYPE<::><::>$count_comdtl<::>!<::>?UPD=1<::>')";
		echo "</script>";
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "UPDATE" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);

		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_TYPE='$COM_TYPE', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลบัญชีแนบท้ายคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	
// ============================================================
	// เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง
	if( $command == "COMMAND" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);

		include ("php_scripts/data_map_emp_comdtl_confirm_check.php");	
		if (!trim($error_move_personal)) {
			include ("php_scripts/data_map_emp_comdtl_confirm.php");
			$cmd = " update PER_COMMAND set  
							COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
					where COM_ID=$COM_ID  ";
			$db_dpis->send_cmd($cmd);		

			$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
			$count_comdtl = $db_dpis->send_cmd($cmd);
			// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป

			echo "<script>";
			echo "parent.refresh_opener('2<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::>1<::>?UPD=1<::>')";
			echo "</script>";
		} elseif (trim($error_move_personal)) {
			$cmd = " update PER_COMMAND set  
							COM_CONFIRM=0, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
					where COM_ID=$COM_ID  ";
			$db_dpis->send_cmd($cmd);		
		}
	}		// 	if( $command == "COMMAND" && trim($COM_ID) ) 	

// ============================================================
	// เมื่อมีการส่งจากภูมิภาค
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ส่งข้อมูลบัญชีแนบท้ายคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
	
// ============================================================	
	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลลูกจ้างแนบท้ายบัญชีคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ [".trim($COM_ID)." : ".$PER_ID."]");

		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
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

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลบัญชีและลูกจ้างแนบท้ายบัญชีคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$PER_ID."]");
		$COM_ID = "";
		
		echo "<script>";
		echo "parent.refresh_opener('1<::><::><::><::><::><::><::><::><::>')";
		echo "</script>";
	}

	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, 
						a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID 
				from		PER_COMMAND a, PER_COMTYPE b
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
		
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	}

	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		
		$SELECT_LEVEL_NO = 11;		
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";
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
	} // end if		
?>