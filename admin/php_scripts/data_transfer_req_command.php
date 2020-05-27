<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if ($COM_DATE) $tmp_COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2);
	
	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = "	select COM_ID from PER_COMMAND where COM_NO='".trim($COM_NO)."'";
		$count_com_id = $db_dpis->send_cmd($cmd);
		if ($count_com_id) { 
			$alert_adding_command = "alert('ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากเลขที่คำสั่งซ้ำ ')";
			
		} else {
			$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$COM_ID = $data[max_id] + 1;
	
			$cmd = " insert into PER_COMMAND 
							(COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
							COM_TYPE, COM_CONFIRM, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
							 VALUES 
							($COM_ID, '$COM_NO', '$COM_NAME', '$tmp_COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
							'$COM_TYPE', $COM_CONFIRM, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลบัญชีแนบท้ายคำสั่งบรรจุ/รับโอน [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]"); 	
		} // if 			
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "UPDATE" && trim($COM_ID) ) {
		$COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2);

		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$tmp_COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE', COM_CONFIRM=$COM_CONFIRM, 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลบัญชีแนบท้ายคำสั่งบรรจุ/รับโอน [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	
	
// ============================================================
	// เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง (CONFIRM บัญชีแนบท้ายคำสั่ง)
	if ($COM_CONFIRM == 1 && ($command == "ADD" || $command == "UPDATE")) {	
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, 
									CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
									EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2 
				from		PER_COMDTL 
				where	COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = trim($data[POS_ID]);
			$tmp_POEM_ID = trim($data[POEM_ID]);
			$tmp_POEMS_ID = trim($data[POEMS_ID]);
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? trim($data[CMD_NOTE2]) : "&rsquo;";
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = "";
			if (trim($tmp_POS_ID)) {									// ตำแหน่งช้าราชการ
				$cmd = "  select POS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE 
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POS_NO]);
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
			} elseif (trim($tmp_POEM_ID)) {						// ตำแหน่งลูกจ้างประจำ
				$cmd = "  select POEM_NO, ORG_ID, ORG_ID_1, ORG_ID_2, PN_CODE 
							   from PER_POS_EMP where POS_ID=$tmp_POEM_ID  ";				
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEM_NO]);				
				$PN_CODE = trim($data2[PN_CODE]);	
			} elseif (trim($tmp_POEMS_ID)) {						// ตำแหน่งพนักงานราชการ
				$cmd = "  select POEMS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, EP_CODE 
							   from PER_POS_EMPSER where POS_ID=$tmp_POEMS_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEMS_NO]);				
				$EP_CODE = trim($data2[EP_CODE]);										   
			}
			$ORG_ID_3 = trim($data2[ORG_ID]);		
			if (trim($data2[ORG_ID_1])) $ORG_ID_45[] = trim($data2[ORG_ID_1]);
			if (trim($data2[ORG_ID_2])) $ORG_ID_45[] = trim($data2[ORG_ID_2]);
			$search_org_id = implode(", ", $ORG_ID_45);
			$cmd = "  select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($search_org_id)";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_4 = ($data2[ORG_ID] == $ORG_ID_45[0])? "$data2[ORG_NAME]" : "";
				$ORG_NAME_5 = ($data2[ORG_ID] == $ORG_ID_45[1])? "$data2[ORG_NAME]" : "";				
			} 			
			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = trim($data2[ORG_ID_REF]);
			$CT_CODE = trim($data2[CT_CODE]);
			$PV_CODE = trim($data2[PV_CODE]);
			$AP_CODE = trim($data2[AP_CODE]);
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			// ===== หาชื่อของกระทรวง กรม กรอง
			$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : 0;
			$ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : 0;
			$ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : 0;				
			$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
			$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3 ) ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
				$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
				$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
			}			
			
			// update status of PER_PERSONAL 
			$cmd = " update PER_PERSONAL set  
						LEVEL_NO = '$tmp_LEVEL_NO', 
						PER_SALARY = $tmp_CMD_SALARY,
						PER_SPSALARY = $tmp_CMD_SPSALARY, 
						PER_OCCUPYDATE = '$tmp_CMD_DATE', 
						MOV_CODE='$tmp_MOV_CODE', 
						PER_STATUS=1  
					where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();	
//echo "<br>";
			$cmd = " update PER_POSITION set POS_CHANGE_DATE='$tmp_CMD_DATE' where POS_ID=$tmp_POS_ID";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();				
//echo "<br>";
			
			// update and insert into PER_POSITIONHIS	
			$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID=$tmp_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_POH_ID = trim($data1[POH_ID]);
			$cmd = " update PER_POSITIONHIS set POH_ENDDATE='$before_cmd_date' where POH_ID=$tmp_POH_ID";
			//$db_dpis1->send_cmd($cmd);	
			// === ยืนยันคำสั่งบรรจุ/รับโอนในกรณีที่มีประวัติการดำรงตำแหน่งเดิม ไม่ต้องปิด record ก่อนหน้าและให้ write record ใหม่ด้วย
			// === ที่ให้แก้ตามคำพูดด้านบน จึง comment คำสั่งไม่ให้ส่ง sql ไป query 
			
			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$POH_ID = $data[max_id] + 1; 	
			$cmd = " 	insert into PER_POSITIONHIS 
						  	(POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, POH_DOCDATE, POH_ENDDATE, 
						   	POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, EP_CODE, 
							CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							POH_UNDER_ORG1, POH_UNDER_ORG2, 
						   	POH_SALARY, POH_SALARY_POS, POH_REMARK, 
							POH_ORG1, POH_ORG2, POH_ORG3, 
							UPDATE_USER, UPDATE_DATE)
						  	values 
						  	($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$COM_DATE', '', 
						   	'$POH_POS_NO', '$PM_CODE', '$tmp_LEVEL_NO', '$PL_CODE', '$PN_CODE', '$EP_CODE',  
							'$CT_CODE', '$PV_CODE', '$AP_CODE', '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							'$ORG_NAME_4', '$ORG_NAME_5', 
						   	$tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$tmp_CMD_NOTE2', 
							'$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', 
							$SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			//echo "<br>";

			// update and insert into PER_SALARYHIS 
			$cmd = " select SAH_ID from PER_SALARYHIS where PER_ID=$tmp_PER_ID order by PER_ID, SAH_EFFECTIVEDATE desc, SAH_SALARY desc, SAH_DOCNO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date' where SAH_ID=$tmp_SAH_ID";
			//$db_dpis1->send_cmd($cmd);	
			// === ยืนยันคำสั่งบรรจุ/รับโอนในกรณีที่มีประวัติการรับเงินเดือนเดิม ไม่ต้องปิด record ก่อนหน้าและให้ write record ใหม่ด้วย
			// === ที่ให้แก้ตามคำพูดด้านบน จึง comment คำสั่งไม่ให้ส่ง sql ไป query 	

			// === การบรรจุ/รับโอน ให้ตั้งค่าการเคลื่อนไหวเงินเดือนเป็น 21390 (เงินเดือนแรกบรรจุ)
			$tmp_SALARY_MOV_CODE = "21390";
			
			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SAH_ID = $data[max_id] + 1; 			 
			$cmd = "	insert into PER_SALARYHIS 
							(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE) 
							values 
							($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_SALARY_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
							'$tmp_COM_DATE', '', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
//echo "$cmd<br>";		
//$db_dpis1->show_error();	
		}				

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการดำรงตำแหน่ง และเพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งบรรจุ/รับโอน [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
	}		// 	if ($COM_CONFIRM == 1 && ($command=="ADD" || $command=="UPDATE")) 	
// ============================================================

	if($command == "DELETE" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลบัญชีแนบท้ายคำสั่งบรรจุ/รับโอน [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}

	if($UPD || $VIEW){
		$cmd = " select 	COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, COM_TYPE, COM_CONFIRM, DEPARTMENT_ID
				from 	PER_COMMAND 
				where 	COM_ID=$COM_ID "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);

		$COM_DATE =  substr(trim($data[COM_DATE]), 8, 2) ."/". substr(trim($data[COM_DATE]), 5, 2) ."/". (substr(trim($data[COM_DATE]), 0, 4) + 543);

		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = "";
		$cmd = "select COM_NAME from PER_COMTYPE where COM_TYPE='$COM_TYPE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COM_TYPE_NAME = trim($data2[COM_NAME]);

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
	} // end if
	
	if( !$UPD && !$DEL && !$VIEW ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_PER_TYPE = "";
		$COM_CONFIRM = "";
				
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";

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