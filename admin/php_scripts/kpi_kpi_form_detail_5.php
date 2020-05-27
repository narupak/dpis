<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$PER_ID_REVIEW1) $PER_ID_REVIEW1 = "NULL";
	if(!$PER_ID_REVIEW2) $PER_ID_REVIEW2 = "NULL";
	if($PER_ID_REVIEW1 && ($AGREE_REVIEW1!="" || $DIFF_REVIEW1!="") ) $DATE_REVIEW1 = $UPDATE_DATE;//วันที่ เเสดงความคิดเห็นผู้บังคับบันชา เหนือขึ้นไป
	if($PER_ID_REVIEW2 && ($AGREE_REVIEW2!="" || $DIFF_REVIEW2!="") ) $DATE_REVIEW2 = $UPDATE_DATE;//วันที่ เเสดงความคิดเห็นผู้บังคับบันชา เหนือขึ้นไป 1 ขั้น
	if($command=="UPDATE" && $KF_ID){
		$cmd = " UPDATE PER_KPI_FORM SET
							PER_ID_REVIEW1=$PER_ID_REVIEW1, AGREE_REVIEW1='$AGREE_REVIEW1', DIFF_REVIEW1='$DIFF_REVIEW1', DATE_REVIEW1 ='$DATE_REVIEW1',
							PER_ID_REVIEW2=$PER_ID_REVIEW2, AGREE_REVIEW2='$AGREE_REVIEW2', DIFF_REVIEW2='$DIFF_REVIEW2', DATE_REVIEW2 ='$DATE_REVIEW2',
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
						WHERE KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
			echo "<script>";
			echo "alert('บันทึกความเห็นของผู้บังคับบัญชาเหนือขึ้นไปแล้ว')";
			echo "</script>";		
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > บันทึกความเห็นของผู้บังคับบัญชาเหนือขึ้นไป [$KF_ID : $PER_ID_REVIEW1 : $PER_ID_REVIEW2]");
	} // end if
		
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];

	$cmd = "	SELECT 	PER_ID_REVIEW1, AGREE_REVIEW1, DIFF_REVIEW1, REVIEW_PN_NAME1, 
											REVIEW_PER_NAME1, REVIEW_PL_NAME1, REVIEW_PM_NAME1, REVIEW_LEVEL_NO1, 
											PER_ID_REVIEW2, AGREE_REVIEW2, DIFF_REVIEW2, REVIEW_PN_NAME2, 
											REVIEW_PER_NAME2, REVIEW_PL_NAME2, REVIEW_PM_NAME2, REVIEW_LEVEL_NO2,
                                                                                        ACCEPT_FLAG,
                                                                                        SUBSTR( KF_END_DATE,0,4) AS MYKPI_YEAR
						FROM		PER_KPI_FORM 
						WHERE		KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
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
        
	$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
	$REVIEW_PN_NAME1 = $data[REVIEW_PN_NAME1];
	$REVIEW_PER_NAME1 = $data[REVIEW_PER_NAME1];
	$REVIEW_PL_NAME1 = $data[REVIEW_PL_NAME1];
	$REVIEW_PM_NAME1 = $data[REVIEW_PM_NAME1];
	if ($REVIEW_PM_NAME1) $REVIEW_PL_NAME1 = $REVIEW_PM_NAME1;
	$REVIEW_LEVEL_NO1 = $data[REVIEW_LEVEL_NO1];
	if ($REVIEW_PER_NAME1) $REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1;
	$AGREE_REVIEW1 = $data[AGREE_REVIEW1];
	$DIFF_REVIEW1 = $data[DIFF_REVIEW1];

	$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
	$REVIEW_PN_NAME2 = $data[REVIEW_PN_NAME2];
	$REVIEW_PER_NAME2 = $data[REVIEW_PER_NAME2];
	$REVIEW_PL_NAME2 = $data[REVIEW_PL_NAME2];
	$REVIEW_PM_NAME2 = $data[REVIEW_PM_NAME2];
	if ($REVIEW_PM_NAME2) $REVIEW_PL_NAME2 = $REVIEW_PM_NAME2;
	$REVIEW_LEVEL_NO2 = $data[REVIEW_LEVEL_NO2];
	if ($REVIEW_PER_NAME2) $REVIEW_PER_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2;
	$AGREE_REVIEW2 = $data[AGREE_REVIEW2];
	$DIFF_REVIEW2 = $data[DIFF_REVIEW2];
        
        $ACCEPT_FLAG = $data[ACCEPT_FLAG];

	if($SESS_USERGROUP == 1){
		$USER_AUTH1 = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID_REVIEW1==$SESS_PER_ID && !$AGREE_REVIEW2 && !$DIFF_REVIEW2){
		$USER_AUTH1 = TRUE;
	}else{
		$USER_AUTH1 = FALSE;
	} // end if

	if($SESS_USERGROUP == 1){
		$USER_AUTH2 = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID_REVIEW2==$SESS_PER_ID){
		$USER_AUTH2 = TRUE;
	}else{
		$USER_AUTH2 = FALSE;
	} // end if

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, DEPARTMENT_ID
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID_REVIEW1 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_DEPARTMENT_ID1 = trim($data[DEPARTMENT_ID]);
	if (!$REVIEW_PER_NAME1 || !$REVIEW_PL_NAME1 || !$REVIEW_PM_NAME1 || !$REVIEW_LEVEL_NO1) {
		$REVIEW_PN_CODE1 = trim($data[PN_CODE]);
		$REVIEW_PER_NAME1 = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME1 = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE1 = trim($data[PER_TYPE]);
		$REVIEW_POS_ID1 = trim($data[POS_ID]);
		$REVIEW_POEM_ID1 = trim($data[POEM_ID]);
		$REVIEW_POEMS_ID1 = trim($data[POEMS_ID]);
		if (!$REVIEW_LEVEL_NO1) $REVIEW_LEVEL_NO1 = trim($data[LEVEL_NO]);

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME1 = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL1 = $data2[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL1) $REVIEW_POSITION_LEVEL1 = $REVIEW_LEVEL_NAME1;
			
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME1 = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
			
		if (!$REVIEW_PL_NAME1 || !$REVIEW_PM_NAME1) {
			if($REVIEW_PER_TYPE1==1){
				$cmd = " select 	a.PM_CODE, b.PL_NAME, a.PT_CODE
								 from 		PER_POSITION a, PER_LINE b
								 where	a.POS_ID=$REVIEW_POS_ID1 and a.PL_CODE=b.PL_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$REVIEW_PL_NAME1) $REVIEW_PL_NAME1 = trim($data[PL_NAME]);
				$REVIEW_PT_CODE1 = trim($data[PT_CODE]);
				$REVIEW_PT_NAME1 = trim($data[PT_NAME]);
				if ($RPT_N)
					$REVIEW_PL_NAME1 = (trim($REVIEW_PM_NAME1) ?"$REVIEW_PM_NAME1 (":"") . (trim($REVIEW_PL_NAME1)? "$REVIEW_PL_NAME1$REVIEW_POSITION_LEVEL1" : "") . (trim($REVIEW_PM_NAME1) ?")":"");
				else
					$REVIEW_PL_NAME1 = (trim($REVIEW_PM_NAME1) ?"$REVIEW_PM_NAME1 (":"") . (trim($REVIEW_PL_NAME1)?($REVIEW_PL_NAME1 ." ". level_no_format($REVIEW_LEVEL_NO1) . (($REVIEW_PT_NAME1 != "ทั่วไป" && $REVIEW_LEVEL_NO1 >= 6)?"$REVIEW_PT_NAME1":"")):"") . (trim($REVIEW_PM_NAME1) ?")":"");

				$REVIEW_PM_CODE1 = trim($data[PM_CODE]);
				if($REVIEW_PM_CODE1 && !$REVIEW_PM_NAME1){
					$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$REVIEW_PM_CODE1'  ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
		//			$REVIEW_PL_NAME1 = $data[PM_NAME]." ($REVIEW_PL_NAME1)";
					$REVIEW_PL_NAME1 = trim($data[PM_NAME]);
				} // end if
			}elseif($REVIEW_PER_TYPE1==2){
				$cmd = " select 	b.PN_NAME
								 from 		PER_POS_EMP a, PER_POS_NAME b
								 where	a.POEM_ID=$REVIEW_POEM_ID1 and a.PN_CODE=b.PN_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$REVIEW_PL_NAME1) $REVIEW_PL_NAME1 = trim($data[PN_NAME]);
			}elseif($REVIEW_PER_TYPE1==3){
				$cmd = " select 	b.EP_NAME
								 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
								 where	a.POEMS_ID=$REVIEW_POEMS_ID1 and a.EP_CODE=b.EP_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$REVIEW_PL_NAME1) $REVIEW_PL_NAME1 = trim($data[EP_NAME]);
			} // end if
		} // end if
	} // end if
	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REVIEW_DEPARTMENT_ID1 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_DEPARTMENT_NAME1 = $data[ORG_NAME];
	$REVIEW_MINISTRY_ID1 = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REVIEW_MINISTRY_ID1 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_MINISTRY_NAME1 = $data[ORG_NAME];

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, DEPARTMENT_ID
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID_REVIEW2 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_DEPARTMENT_ID2 = trim($data[DEPARTMENT_ID]);
	if (!$REVIEW_PER_NAME2 || !$REVIEW_PL_NAME2 || !$REVIEW_PM_NAME2 || !$REVIEW_LEVEL_NO2) {
		$REVIEW_PN_CODE2 = trim($data[PN_CODE]);
		$REVIEW_PER_NAME2 = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME2 = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE2 = trim($data[PER_TYPE]);
		$REVIEW_POS_ID2 = trim($data[POS_ID]);
		$REVIEW_POEM_ID2 = trim($data[POEM_ID]);
		$REVIEW_POEMS_ID2 = trim($data[POEMS_ID]);
		if (!$REVIEW_LEVEL_NO2) $REVIEW_LEVEL_NO2 = trim($data[LEVEL_NO]);

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO2' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME2 = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL2 = $data2[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL2) $REVIEW_POSITION_LEVEL2 = $REVIEW_LEVEL_NAME2;
			
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE2' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME2 = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2 . " " . $REVIEW_PER_SURNAME2;
		
		if (!$REVIEW_PL_NAME2 || !$REVIEW_PM_NAME2) {
			if($REVIEW_PER_TYPE2==1){
				$cmd = " select 	a.PM_CODE, b.PL_NAME, a.PT_CODE
								 from 		PER_POSITION a, PER_LINE b
								 where	a.POS_ID=$REVIEW_POS_ID2 and a.PL_CODE=b.PL_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$REVIEW_PL_NAME2) $REVIEW_PL_NAME2 = trim($data[PL_NAME]);
				$REVIEW_PT_CODE2 = trim($data[PT_CODE]);
				$REVIEW_PT_NAME2 = trim($data[PT_NAME]);

				if ($RPT_N)
					$REVIEW_PL_NAME2 = (trim($REVIEW_PM_NAME2) ?"$REVIEW_PM_NAME2 (":"") . (trim($REVIEW_PL_NAME2)? "$REVIEW_PL_NAME2$REVIEW_POSITION_LEVEL2" : "") . (trim($REVIEW_PM_NAME2) ?")":"");
				else
					$REVIEW_PL_NAME2 = (trim($REVIEW_PM_NAME2) ?"$REVIEW_PM_NAME2 (":"") . (trim($REVIEW_PL_NAME2)?($REVIEW_PL_NAME2 ." ". level_no_format($REVIEW_LEVEL_NO2) . (($REVIEW_PT_NAME2 != "ทั่วไป" && $REVIEW_LEVEL_NO2 >= 6)?"$REVIEW_PT_NAME2":"")):"") . (trim($REVIEW_PM_NAME2) ?")":"");

				$REVIEW_PM_CODE2 = trim($data[PM_CODE]);
				if($REVIEW_PM_CODE2 && !$REVIEW_PM_NAME2){
					$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$REVIEW_PM_CODE2'  ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
		//			$REVIEW_PL_NAME2 = $data[PM_NAME]." ($REVIEW_PL_NAME2)";
					$REVIEW_PL_NAME2 = trim($data[PM_NAME]);
				} // end if 
			}elseif($REVIEW_PER_TYPE2==2){
				$cmd = " select 	b.PN_NAME
								 from 		PER_POS_EMP a, PER_POS_NAME b
								 where	a.POEM_ID=$REVIEW_POEM_ID2 and a.PN_CODE=b.PN_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$REVIEW_PL_NAME2) $REVIEW_PL_NAME2 = trim($data[PN_NAME]);
			}elseif($REVIEW_PER_TYPE2==3){
				$cmd = " select 	b.EP_NAME
								 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
								 where	a.POEMS_ID=$REVIEW_POEMS_ID2 and a.EP_CODE=b.EP_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$REVIEW_PL_NAME2) $REVIEW_PL_NAME2 = trim($data[EP_NAME]);
			} // end if
		} // end if
	} // end if

	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REVIEW_DEPARTMENT_ID2 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_DEPARTMENT_NAME2 = $data[ORG_NAME];
	$REVIEW_MINISTRY_ID2 = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REVIEW_MINISTRY_ID2 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_MINISTRY_NAME2 = $data[ORG_NAME];
?>