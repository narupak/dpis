<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	if (!$search_org_id) $search_org_id = "NULL";
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	//$COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 1; 
	$COM_PER_TYPE = (trim($COM_PER_TYPE))? $COM_PER_TYPE : 1;
	
	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
	} // end if	

	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
			$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$COM_ID = $data[max_id] + 1;
	
			$COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2);
			
			$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
							COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, COM_LEVEL_SALP, UPDATE_USER, UPDATE_DATE, ORG_ID) 
							VALUES 	($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
							'$COM_TYPE', 0, '', $DEPARTMENT_ID, 9, $SESS_USERID, '$UPDATE_DATE', $search_org_id) ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลบัญชีแนบท้ายคำสั่งปรับอัตราเงินเดือนใหม่ [$DEPARTMENT_ID : $search_org_id : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
		
			// ===== เริ่มต้น insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
			if ($COM_PER_TYPE==1) {
				include("data_salpromote_new_comdtl_layer.php");
			} elseif ($COM_PER_TYPE==2) {
				include("data_salpromote_new_comdtl_layeremp.php");
			} elseif ($COM_PER_TYPE==3) {
				include("data_salpromote_new_comdtl_layerempser.php");
			} // if
			// ===== สิ้นสุด insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) ===== 
			$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
			$count_comdtl = $db_dpis->send_cmd($cmd);
			// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
			echo "<script>";
			echo "parent.refresh_opener('2<::>$COM_ID<::>$COM_NAME<::>$search_org_id<::>$COM_PER_TYPE<::><::>$count_comdtl<::><::>?UPD=1')";
			echo "</script>";

	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )

	
	if( $command == "UPDATE" && trim($COM_ID) ) {
		$COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2);
		
		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo $cmd;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลบัญชีแนบท้ายคำสั่งปรับอัตราเงินเดือนใหม่ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	

// ============================================================
	// ===== เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง ===== 
	if( $command == "COMMAND" && trim($COM_ID) ) {
		$COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2);

		if ($COM_PER_TYPE==1) {
			include("data_salpromote_new_comdtl_confirm_layer.php");
		} elseif ($COM_PER_TYPE==2) {
			include("data_salpromote_new_comdtl_confirm_layeremp.php");
		} elseif ($COM_PER_TYPE==3) {
			include("data_salpromote_new_comdtl_confirm_layerempser.php");
		} // if
		$cmd = " update PER_COMMAND set  
						COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);		

		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป

		echo "<script>";
		echo "parent.refresh_opener('2<::>!<::>!<::>!<::>!<::><::>!<::>1<::>?UPD=1')";
		echo "</script>";
	}		// 	if ($COM_CONFIRM == 1 && ($command=="ADD" || $command=="UPDATE")) 	
// ============================================================

	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งเลื่อนขั้นเงินเดือน [".trim($COM_ID)." : ".$PER_ID."]");

		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
		echo "<script>";
		if ($count_comdtl > 0)
			echo "parent.refresh_opener('2<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::><::>')";
		else
			echo "parent.refresh_opener('3<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::><::>')";
		echo "</script>";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลบัญชีและข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งเลื่อนขั้นเงินเดือน [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$PER_ID."]");
		$COM_ID = "";
		
		echo "<script>";
		echo "parent.refresh_opener('1<::><::><::><::><::><::><::><::>')";
		echo "</script>";
	}
	
	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
										a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID 
							from	PER_COMMAND a, PER_COMTYPE b
							where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE =  substr(trim($data[COM_DATE]), 8, 2) ."/". substr(trim($data[COM_DATE]), 5, 2) ."/". (substr(trim($data[COM_DATE]), 0, 4) + 543);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
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
		$COM_PER_TYPE = "1";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		
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