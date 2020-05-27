<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];

	$cmd = " select PER_ID_REVIEW, REVIEW_PN_NAME, REVIEW_PER_NAME, REVIEW_PL_NAME, REVIEW_PM_NAME, 
									REVIEW_LEVEL_NO, PER_ID_REVIEW0, REVIEW_PN_NAME0, REVIEW_PER_NAME0, REVIEW_PL_NAME0, 
									REVIEW_PM_NAME0, REVIEW_LEVEL_NO0, AGREE_REVIEW1, DIFF_REVIEW1, AGREE_REVIEW2, DIFF_REVIEW2,KF_STATUS ,
                                                                        SUBSTR( KF_END_DATE,0,4) AS MYKPI_YEAR
						from PER_KPI_FORM where KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
	//echo "<pre>".$cmd;
	$data = $db_dpis->get_array();
        $MYKPI_YEAR = $data[MYKPI_YEAR]+543;
        /*Begin http://dpis.ocsc.go.th/Service/node/2017*/
        $isLockYear='UNLOCK';
        if($MYKPI_YEAR < $KPI_BUDGET_YEAR){
            $isLockYear='LOCK';
        }
        if($SESS_USERID==1 && $SESS_USERGROUP==1){
            $isLockYear='UNLOCK';
        }
        /*End */
                
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
	$REVIEW_PN_NAME = $data[REVIEW_PN_NAME];
	$REVIEW_PER_NAME = $data[REVIEW_PER_NAME];
	$REVIEW_PL_NAME = $data[REVIEW_PL_NAME];
	$REVIEW_PM_NAME = $data[REVIEW_PM_NAME];
	if ($REVIEW_PM_NAME) $REVIEW_PL_NAME = $REVIEW_PM_NAME;
	$REVIEW_LEVEL_NO = $data[REVIEW_LEVEL_NO];
	if ($REVIEW_PER_NAME) $REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME;
	$PER_ID_REVIEW0 = $data[PER_ID_REVIEW0];
	$REVIEW_PN_NAME0 = $data[REVIEW_PN_NAME0];
	$REVIEW_PER_NAME0 = $data[REVIEW_PER_NAME0];
	$REVIEW_PL_NAME0 = $data[REVIEW_PL_NAME0];
	$REVIEW_PM_NAME0 = $data[REVIEW_PM_NAME0];
	if ($REVIEW_PM_NAME0) $REVIEW_PL_NAME0 = $REVIEW_PM_NAME0;
	$REVIEW_LEVEL_NO0 = $data[REVIEW_LEVEL_NO0];
	if ($REVIEW_PER_NAME0) $REVIEW_PER_NAME0 = $REVIEW_PN_NAME0 . $REVIEW_PER_NAME0;
	$AGREE_REVIEW1 = trim($data[AGREE_REVIEW1]);
	$DIFF_REVIEW1 = trim($data[DIFF_REVIEW1]);
	$AGREE_REVIEW2 = trim($data[AGREE_REVIEW2]);
	$DIFF_REVIEW2 = trim($data[DIFF_REVIEW2]);
	$KF_STATUS = trim($data[KF_STATUS]);
	
	if($SESS_USERGROUP == 1){
		$USER_AUTH = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && 
		($PER_ID_REVIEW==$SESS_PER_ID || $PER_ID_REVIEW0==$SESS_PER_ID) && !$AGREE_REVIEW1 && !$DIFF_REVIEW2 && 
		!$AGREE_REVIEW2 && !$DIFF_REVIEW2){
		$USER_AUTH = TRUE; 
	}else{
		$USER_AUTH = FALSE;
	} // end if

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, DEPARTMENT_ID
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID_REVIEW ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	if (!$REVIEW_PER_NAME || !$REVIEW_PL_NAME || !$REVIEW_PM_NAME || !$REVIEW_LEVEL_NO) {
		$REVIEW_PN_CODE = trim($data[PN_CODE]);
		$REVIEW_PER_NAME = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE = trim($data[PER_TYPE]);
		$REVIEW_POS_ID = trim($data[POS_ID]);
		$REVIEW_POEM_ID = trim($data[POEM_ID]);
		$REVIEW_POEMS_ID = trim($data[POEMS_ID]);
		if (!$REVIEW_LEVEL_NO) $REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL) $REVIEW_POSITION_LEVEL = $REVIEW_LEVEL_NAME;
			
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
			
		if (!$REVIEW_PL_NAME || !$REVIEW_PM_NAME) {
			if($REVIEW_PER_TYPE==1){
				$cmd = " select 	a.PM_CODE, b.PL_NAME, a.PT_CODE
								 from 		PER_POSITION a, PER_LINE b
								 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$REVIEW_PL_NAME) $REVIEW_PL_NAME = trim($data[PL_NAME]);
				$REVIEW_PT_CODE = trim($data[PT_CODE]);
				$REVIEW_PT_NAME = trim($data[PT_NAME]);

				if ($RPT_N)
					$REVIEW_PL_NAME = (trim($REVIEW_PM_NAME) ?"$REVIEW_PM_NAME (":"") . (trim($REVIEW_PL_NAME)? "$REVIEW_PL_NAME$REVIEW_POSITION_LEVEL" : "") . (trim($REVIEW_PM_NAME) ?")":"");
				else
					$REVIEW_PL_NAME = (trim($REVIEW_PM_NAME) ?"$REVIEW_PM_NAME (":"") . (trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME ." ". level_no_format($REVIEW_LEVEL_NO) . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):"") . (trim($REVIEW_PM_NAME) ?")":"");

				$REVIEW_PM_CODE = trim($data[PM_CODE]);
				if($REVIEW_PM_CODE && !$REVIEW_PM_NAME){
					$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$REVIEW_PM_CODE'  ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
		//			$REVIEW_PL_NAME = $data[PM_NAME]." ($REVIEW_PL_NAME)";
					$REVIEW_PL_NAME = trim($data[PM_NAME]);
				} // end if
			}elseif($REVIEW_PER_TYPE==2){
				$cmd = " select 	b.PN_NAME
								 from 		PER_POS_EMP a, PER_POS_NAME b
								 where	a.POEM_ID=$REVIEW_POEM_ID and a.PN_CODE=b.PN_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$REVIEW_PL_NAME) $REVIEW_PL_NAME = trim($data[PN_NAME]);
			}elseif($REVIEW_PER_TYPE==3){
				$cmd = " select 	b.EP_NAME
								 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
								 where	a.POEMS_ID=$REVIEW_POEMS_ID and a.EP_CODE=b.EP_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$REVIEW_PL_NAME) $REVIEW_PL_NAME = trim($data[EP_NAME]);
			} // end if
		} // end if
	} // end if
		
	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REVIEW_DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_DEPARTMENT_NAME = $data[ORG_NAME];
	$REVIEW_MINISTRY_ID = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REVIEW_MINISTRY_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_MINISTRY_NAME = $data[ORG_NAME];

	if($command=="ADD" && $KF_ID && $DEVELOP_SEQ && $DEVELOP_COMPETENCE){
		$cmd = " select max(IPIP_ID) as max_id from PER_IPIP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$IPIP_ID = $data[max_id] + 1;
				
		$cmd = " 	insert into PER_IPIP	(KF_ID, IPIP_ID, DEVELOP_SEQ, DEVELOP_COMPETENCE, DEVELOP_METHOD, 
							DEVELOP_INTERVAL, DEVELOP_EVALUATE, UPDATE_USER, UPDATE_DATE)
							values ($KF_ID, $IPIP_ID, $DEVELOP_SEQ, '$DEVELOP_COMPETENCE', '$DEVELOP_METHOD', 
							'$DEVELOP_INTERVAL', '$DEVELOP_EVALUATE', $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มแผนพัฒนาผลการปฏิบัติงานรายบุคคล [$IPIP_ID : $DEVELOP_SEQ : $DEVELOP_COMPETENCE : $DEVELOP_METHOD : $DEVELOP_INTERVAL]");
	} // end if
	
	if($command=="UPDATE" && $KF_ID && $IPIP_ID && $DEVELOP_SEQ && $DEVELOP_COMPETENCE){
		$cmd = " UPDATE PER_IPIP SET
							DEVELOP_SEQ=$DEVELOP_SEQ,
							DEVELOP_COMPETENCE='$DEVELOP_COMPETENCE',
							DEVELOP_METHOD='$DEVELOP_METHOD',
							DEVELOP_INTERVAL='$DEVELOP_INTERVAL',
							DEVELOP_EVALUATE='$DEVELOP_EVALUATE',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						WHERE KF_ID=$KF_ID and IPIP_ID=$IPIP_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขแผนพัฒนาผลการปฏิบัติงานรายบุคคล [$IPIP_ID : $DEVELOP_SEQ : $DEVELOP_COMPETENCE : $DEVELOP_METHOD : $DEVELOP_INTERVAL]");
	} // end if
		
	if($command=="DELETE" && $KF_ID && $IPIP_ID){
		$cmd = " select 	DEVELOP_SEQ, DEVELOP_COMPETENCE, DEVELOP_METHOD, DEVELOP_INTERVAL, DEVELOP_EVALUATE 
						 from 		PER_IPIP 
						 where 	KF_ID=$KF_ID and IPIP_ID=$IPIP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEVELOP_SEQ = $data[DEVELOP_SEQ];
		$DEVELOP_COMPETENCE = $data[DEVELOP_COMPETENCE];
		$DEVELOP_METHOD = $data[DEVELOP_METHOD];
		$DEVELOP_INTERVAL = $data[DEVELOP_INTERVAL];
		$DEVELOP_EVALUATE = $data[DEVELOP_EVALUATE];
	
		$cmd = " delete from PER_IPIP where KF_ID=$KF_ID and IPIP_ID=$IPIP_ID ";
		$db_dpis->send_cmd($cmd);
	
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบแผนพัฒนาผลการปฏิบัติงานรายบุคคล [$IPIP_ID : $DEVELOP_SEQ : $DEVELOP_COMPETENCE : $DEVELOP_METHOD : $DEVELOP_INTERVAL]");
	} // end if
	
	if(($UPD && $KF_ID && $IPIP_ID) || ($VIEW && $KF_ID && $IPIP_ID)){
		$cmd = "	SELECT 	DEVELOP_SEQ, DEVELOP_COMPETENCE, DEVELOP_METHOD, DEVELOP_INTERVAL, DEVELOP_EVALUATE
							FROM		PER_IPIP 
							WHERE		KF_ID=$KF_ID and IPIP_ID=$IPIP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEVELOP_SEQ = $data[DEVELOP_SEQ];
		$DEVELOP_COMPETENCE = $data[DEVELOP_COMPETENCE];
		$DEVELOP_METHOD = $data[DEVELOP_METHOD];
		$DEVELOP_INTERVAL = $data[DEVELOP_INTERVAL];
		$DEVELOP_EVALUATE = $data[DEVELOP_EVALUATE];
	} // end if
		
	if( !$UPD && !$VIEW && !$err_text){
		$IPIP_ID = "";
		$DEVELOP_SEQ = "";
		$cmd = " select max(DEVELOP_SEQ) as max_seq from PER_IPIP where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$DEVELOP_SEQ = $data[max_seq] + 1;
		
		$DEVELOP_COMPETENCE = "";
		$DEVELOP_METHOD = "";
		$DEVELOP_INTERVAL = "";		
		$DEVELOP_EVALUATE = "";		
	} // end if
?>