<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="UPDATE" && $PER_ID){
		$PER_DOCDATE = save_date($PER_DOCDATE); 
		$PER_EFFECTIVEDATE = save_date($PER_EFFECTIVEDATE); 
		$PER_POS_DOCDATE = save_date($PER_POS_DOCDATE); 
		$PER_BOOK_DATE = save_date($PER_BOOK_DATE); 

		$tmp_date = explode("-", $PER_EFFECTIVEDATE);
		// 86400 วินาที = 1 วัน
		$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
		$before_cmd_date = date("Y-m-d", $before_cmd_date);

		$cmd = " select POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, PER_SALARY, PER_SPSALARY, ES_CODE, PER_STATUS, PER_TYPE 
						  from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$tmp_POS_ID = trim($data2[POS_ID]);
		$tmp_POEM_ID = trim($data2[POEM_ID]);
		$tmp_POEMS_ID = trim($data2[POEMS_ID]);			
		$tmp_POT_ID = trim($data2[POT_ID]);			
		$LEVEL_NO = trim($data2[LEVEL_NO]);			
		$PER_SALARY = $data2[PER_SALARY] + 0;			
		$PER_SPSALARY = $data2[PER_SPSALARY] + 0;			
		$ES_CODE = trim($data2[ES_CODE]);			
		$PER_STATUS = trim($data2[PER_STATUS]);			
		$PER_TYPE = trim($data2[PER_TYPE]);			
		
		$PM_CODE = $PL_CODE = $PN_CODE = $EP_CODE = $TP_CODE = "";
		if ($PER_TYPE==1) {									// ตำแหน่งข้าราชการ
			//อัพเดทวันที่ตำแหน่งว่าง
			$cmd = " update PER_POSITION set POS_CHANGE_DATE='$PER_EFFECTIVEDATE', POS_VACANT_DATE='$PER_EFFECTIVEDATE' where POS_ID=$tmp_POS_ID  ";
			$db_dpis2->send_cmd($cmd);

			$cmd = "  select POS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, PL_CODE 
						   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = trim($data2[POS_NO]);
			$PM_CODE = trim($data2[PM_CODE]);
			$PL_CODE = trim($data2[PL_CODE]);
		} elseif ($PER_TYPE==2) {						// ตำแหน่งลูกจ้างประจำ
			$cmd = "  select POEM_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PN_CODE 
						   from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";				
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = trim($data2[POEM_NO]);				
			$PN_CODE = trim($data2[PN_CODE]);	
			//echo $cmd."<hr>";
		} elseif ($PER_TYPE==3) {						// ตำแหน่งพนักงานราชการ
			$cmd = "  select POEMS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, EP_CODE 
						   from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";	
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = trim($data2[POEMS_NO]);				
			$EP_CODE = trim($data2[EP_CODE]);										   
		} elseif ($PER_TYPE==4) {						// ตำแหน่งพนักงานราชการ
			$cmd = "  select POT_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, TP_CODE 
						   from PER_POS_TEMP where POT_ID=$tmp_POT_ID  ";	
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = trim($data2[POT_NO]);				
			$TP_CODE = trim($data2[TP_CODE]);										   
		}
		$ORG_ID_3 = trim($data2[ORG_ID]);		
		$ORG_ID_4 = trim($data2[ORG_ID_1]);
		$ORG_ID_5 = trim($data2[ORG_ID_2]);
		$ORG_ID_6 = trim($data2[ORG_ID_3]);		
		$ORG_ID_7 = trim($data2[ORG_ID_4]);
		$ORG_ID_8 = trim($data2[ORG_ID_5]);

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_4 = $data2[ORG_NAME];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_5 = $data2[ORG_NAME];				

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_6 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_6 = $data2[ORG_NAME];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_7 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_7 = $data2[ORG_NAME];				

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_8 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_8 = $data2[ORG_NAME];				

		$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_ID_2 = (trim($data2[ORG_ID_REF]))? trim($data2[ORG_ID_REF]) : "NULL";		
		$CT_CODE = (trim($data2[CT_CODE]))? "'".trim($data2[CT_CODE])."'" : "140";
		$PV_CODE = (trim($data2[PV_CODE]))? "'".trim($data2[PV_CODE])."'" : "NULL";
		$AP_CODE = (trim($data2[AP_CODE]))? "'".trim($data2[AP_CODE])."'" : "NULL";
		$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_ID_1 = trim($data2[ORG_ID_REF]);
		$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : "NULL";
		$ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : "NULL";
		$ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : "NULL";			
		$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
		$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3 ) ";
		$db_dpis2->send_cmd($cmd);
		while ($data2 = $db_dpis2->get_array()) {
			$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
			$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
			$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
		}			
		if ($MOV_CODE=="123" || $MOV_CODE=="12310") {
			$PL_NAME_WORK = "ถึงแก่กรรม";
			if ($PER_POS_REASON) $PL_NAME_WORK .= " เนื่องจาก " . $PER_POS_REASON;
		} elseif ($MOV_CODE=="118") {
			$PL_NAME_WORK = "ลาออกจากราชการ";
			if ($PER_POS_REASON) $PL_NAME_WORK .= " เนื่องจาก " . $PER_POS_REASON;
		} elseif ($MOV_CODE=="106" || $MOV_CODE=="10610" || $MOV_CODE=="10620") {
				$PL_NAME_WORK = "พ้นจากตำแหน่งและอัตราเงินเดือน ทาง" . $SESS_DEPARTMENT_NAME;
		} else {
			$PL_NAME_WORK = $MOV_NAME;
		}
		
		$cmd = " update PER_POSITIONHIS set POH_LAST_POSITION = 'N' where PER_ID=$PER_ID and POH_LAST_POSITION = 'Y' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if ($PER_STATUS != 2) {
			$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID=$PER_ID order by PER_ID, POH_EFFECTIVEDATE desc, POH_SEQ_NO desc ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$tmp_POH_ID = trim($data[POH_ID]);
			$cmd = " update PER_POSITIONHIS set POH_ENDDATE='$before_cmd_date' where POH_ID=$tmp_POH_ID";
			$db_dpis->send_cmd($cmd);	

			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$POH_ID = $data[max_id] + 1; 			

			$cmd = " 	insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, 
								POH_DOCDATE, POH_ENDDATE, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, EP_CODE, 
								CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, 
								POH_UNDER_ORG2, POH_UNDER_ORG3, POH_UNDER_ORG4, POH_UNDER_ORG5, POH_SALARY, 
								POH_SALARY_POS, POH_REMARK, POH_ORG1, POH_ORG2, POH_ORG3, UPDATE_USER, 
								UPDATE_DATE, POH_ORG_TRANSFER, POH_ORG, POH_PL_NAME, POH_SEQ_NO, 
								POH_LAST_POSITION, POH_CMD_SEQ, POH_ISREAL, ES_CODE, POH_LEVEL_NO )
								values ($POH_ID, $PER_ID, '$PER_EFFECTIVEDATE', '$MOV_CODE', '$PER_DOCNO', '$PER_DOCDATE', 
								NULL, '$POH_POS_NO', '$PM_CODE', '$LEVEL_NO', '$PL_CODE', '$PN_CODE', '$EP_CODE', $CT_CODE, 
								$PV_CODE, $AP_CODE, '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$ORG_NAME_4', '$ORG_NAME_5', 
								'$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', $PER_SALARY, $PER_SPSALARY, 
								'$PER_POS_REMARK', '$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', $SESS_USERID, 
								'$UPDATE_DATE', '$PER_POS_ORG', '$ORG_NAME_WORK', '$PL_NAME_WORK', 1, 'N', 1, 'N', '$ES_CODE','$LEVEL_NO') ";
			$db_dpis->send_cmd($cmd);
			
	//		$db_dpis->show_error();
		}
				
		$cmd = " update PER_SALARYHIS set SAH_LAST_SALARY = 'N' where PER_ID=$PER_ID and SAH_LAST_SALARY = 'Y' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$cmd = " update PER_PERSONAL set
							PER_STATUS = 2, 
							PER_POSDATE =  trim('$PER_EFFECTIVEDATE'), 
							MOV_CODE = trim('$MOV_CODE'), 
							PER_DOCNO = trim('$PER_DOCNO'), 
							PER_DOCDATE = trim('$PER_DOCDATE'),
							PER_EFFECTIVEDATE = trim('$PER_EFFECTIVEDATE'), 
							PER_POS_REASON = trim('$PER_POS_REASON'),
							PER_POS_YEAR = trim('$PER_POS_YEAR'), 
							PER_POS_DOCTYPE = trim('$PER_POS_DOCTYPE'),
							PER_POS_DOCNO = trim('$PER_POS_DOCNO'), 
							PER_POS_DOCDATE = trim('$PER_POS_DOCDATE'), 
							PER_POS_ORG = trim('$PER_POS_ORG'),
							PER_POS_DESC = trim('$PER_POS_DESC'),
							PER_POS_REMARK = trim('$PER_POS_REMARK'),
							PER_BOOK_NO = trim('$PER_BOOK_NO'), 
							PER_BOOK_DATE = trim('$PER_BOOK_DATE'),
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						 where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		
		// kittiphat 13/09/2561
			$cmdChk2 = "SELECT count(*)AS CNT FROM user_tables WHERE  TABLE_NAME = 'TA_REGISTERUSER'";
			$db_dpis2->send_cmd($cmdChk2);
			$dataChk2 = $db_dpis2->get_array();
			if($dataChk2[CNT]==1){
				
				$cmd = " update TA_REGISTERUSER  set
									ACTIVE_FLAG = 0, 
									UPDATE_DATE = SYSDATE
							 where PER_ID=$PER_ID	  ";
				$db_dpis->send_cmd($cmd);
		
			}

			// End kittiphat 
			
			
			
//		$db_dpis->show_error();
//		echo $cmd;

		if ($BKK_FLAG==1) {
			$cmd = " select max(CHANGEID) as max_id from DPIS_ORGCHANGE ";
			$db_dpis35->send_cmd($cmd);
			$data = $db_dpis35->get_array();
			$data = array_change_key_case($data, CASE_LOWER);		
			$CHANGEID = $data[max_id] + 1;
		
			$cmd = " insert into DPIS_ORGCHANGE (CHANGEID, PERID, MOVEPOSITION, MOVEORG, ISUSER, CHANGETYPE, CHANGEDATE, PXUPDATE, USERNAME, FULLNAME, MD5)
							values ($CHANGEID, $PER_ID, NULL, $ORG_ID_3, 1, 'DELETE', to_date('$UPDATE_DATE','yyyy-mm-dd hh24:mi:ss'), 0, '$username', '$user_name', '$passwd') ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
		}
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลพ้นจากส่วนราชการ [ $PER_ID : $PER_NAME ]");
	} // end if
	
	if($PER_ID){
		$cmd = " select	MOV_CODE, PER_DOCNO, PER_DOCDATE, PER_EFFECTIVEDATE, PER_POS_REASON, 
										PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_DOCDATE, PER_POS_ORG, 
										PER_POS_DESC, PER_POS_REMARK, PER_BOOK_NO, PER_BOOK_DATE, PER_STATUS, UPDATE_USER, UPDATE_DATE
						 from		PER_PERSONAL 
						 where	PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_STATUS = trim($data[PER_STATUS]);
		if ($PER_STATUS==2) {
			$PER_DOCNO = trim($data[PER_DOCNO]);
			$PER_DOCDATE = show_date_format(trim($data[PER_DOCDATE]), 1);
			$PER_EFFECTIVEDATE = show_date_format(trim($data[PER_EFFECTIVEDATE]), 1);
			$PER_POS_REASON = trim($data[PER_POS_REASON]);
			$PER_POS_YEAR = trim($data[PER_POS_YEAR]);
			$PER_POS_DOCTYPE = trim($data[PER_POS_DOCTYPE]);
			$PER_POS_DOCNO = trim($data[PER_POS_DOCNO]);
			$PER_POS_DOCDATE = show_date_format(trim($data[PER_POS_DOCDATE]), 1);
			$PER_POS_ORG = trim($data[PER_POS_ORG]);		
			$PER_POS_DESC = trim($data[PER_POS_DESC]);
			$PER_POS_REMARK = trim($data[PER_POS_REMARK]);
			$PER_BOOK_NO = trim($data[PER_BOOK_NO]);		
			$PER_BOOK_DATE = show_date_format(trim($data[PER_BOOK_DATE]), 1);
			$UPDATE_USER = $data[UPDATE_USER];
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
			$db->send_cmd($cmd);
			$data2 = $db->get_array();
			$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
			$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

			$MOV_CODE = trim($data[MOV_CODE]);
			$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MOV_NAME = $data[MOV_NAME];
		}			
	} 	// 	if($PER_ID){
	
?>