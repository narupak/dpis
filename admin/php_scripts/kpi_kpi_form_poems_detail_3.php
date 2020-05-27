<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

//	print("<pre>");	print_r($_POST);	print("</pre>");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $debug=0;/*0=close,1=open*/
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$SUBPAGE) $SUBPAGE = 1;

	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];

	if($SUBPAGE==1 || $SUBPAGE==2){
	/*เดิม*/
            /*$cmd = " select PER_ID_REVIEW, REVIEW_PN_NAME, REVIEW_PER_NAME, REVIEW_PL_NAME, REVIEW_PM_NAME, 
										REVIEW_LEVEL_NO, PER_ID_REVIEW0, REVIEW_PN_NAME0, REVIEW_PER_NAME0, REVIEW_PL_NAME0, 
										REVIEW_PM_NAME0, REVIEW_LEVEL_NO0, AGREE_REVIEW1, DIFF_REVIEW1, AGREE_REVIEW2, DIFF_REVIEW2, 
										PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, KF_STATUS, KF_SCORE_STATUS 
						  from PER_KPI_FORM where KF_ID=$KF_ID ";*/
            
		/*add Release 5.1.0.6*/
            $cmd = " select PER_ID_REVIEW, REVIEW_PN_NAME, REVIEW_PER_NAME, REVIEW_PL_NAME, REVIEW_PM_NAME, 
                            REVIEW_LEVEL_NO, PER_ID_REVIEW0,
                            PER_ID_REVIEW1,PER_ID_REVIEW2,
                            REVIEW_PN_NAME0, REVIEW_PER_NAME0, REVIEW_PL_NAME0, 
                            REVIEW_PM_NAME0, REVIEW_LEVEL_NO0, AGREE_REVIEW1, DIFF_REVIEW1, AGREE_REVIEW2, DIFF_REVIEW2, 
                            PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, KF_STATUS, KF_SCORE_STATUS 
                    from PER_KPI_FORM where KF_ID=$KF_ID ";
                /*add Release 5.1.0.6*/
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID_REVIEW = $data[PER_ID_REVIEW];
		$REVIEW_PN_NAME = $data[REVIEW_PN_NAME];
		$REVIEW_PER_NAME = $data[REVIEW_PER_NAME];
		$REVIEW_PL_NAME = $data[REVIEW_PL_NAME];
		$REVIEW_PM_NAME = $data[REVIEW_PM_NAME];
		if ($REVIEW_PM_NAME) $REVIEW_PL_NAME = $REVIEW_PM_NAME;
		$REVIEW_LEVEL_NO = $data[REVIEW_LEVEL_NO];
		if ($REVIEW_PER_NAME) $REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME;
		$PER_ID_REVIEW0 = $data[PER_ID_REVIEW0];
                $PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];/*add Release 5.1.0.6*/
                $PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];/*add Release 5.1.0.6*/
                
                
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
		$PERFORMANCE_WEIGHT = $data[PERFORMANCE_WEIGHT];
		$COMPETENCE_WEIGHT = $data[COMPETENCE_WEIGHT];
		$OTHER_WEIGHT = $data[OTHER_WEIGHT];
		$KF_STATUS = $data[KF_STATUS];
		$KF_SCORE_STATUS = $data[KF_SCORE_STATUS];

		if($SESS_USERGROUP == 1){
			$USER_AUTH = TRUE;
		}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && 
			($PER_ID_REVIEW==$SESS_PER_ID || $PER_ID_REVIEW0==$SESS_PER_ID) && !$AGREE_REVIEW1 && !$DIFF_REVIEW2 && 
			!$AGREE_REVIEW2 && !$DIFF_REVIEW2){
			$USER_AUTH = TRUE;
		}else{
			$USER_AUTH = FALSE;
		} // end if
                
	} // end if

	if($SESS_USERGROUP == 1){
		$USER_AUTH2 = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && 
		($PER_ID_REVIEW==$SESS_PER_ID || $PER_ID_REVIEW0==$SESS_PER_ID) && !$AGREE_REVIEW1 && !$DIFF_REVIEW2 && 
		!$AGREE_REVIEW2 && !$DIFF_REVIEW2 && $KF_STATUS!=1){
		$USER_AUTH2 = TRUE;
	}else{
		$USER_AUTH2 = FALSE;
	} // end if
	
	if($SUBPAGE==1){	
		//if($command=="UPDATE" && $SCORE_OTHER){/*เดิม*/
                if($command=="UPDATE" ){/*เพิ่มใหม่ ปรับแก้ส่วนของ 3.อื่น ๆ	%	  ส่วนที่ 3 */
                    if(empty($SCORE_OTHER)){$SCORE_OTHER=0;}
			$cmd = " select SUM_KPI, SUM_COMPETENCE from PER_KPI_FORM where KF_ID=$KF_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SUM_KPI = $data[SUM_KPI];
			$SUM_COMPETENCE = $data[SUM_COMPETENCE];
			$SUM_OTHER = $SCORE_OTHER * $OTHER_WEIGHT;
			$SUM_TOTAL = $SUM_KPI + $SUM_COMPETENCE + $SUM_OTHER;
                        if(empty($SCORE_OTHER)){$SCORE_OTHER='NULL';}/*เพิ่มใหม่ ปรับแก้ส่วนของ 3.อื่น ๆ	%	  ส่วนที่ 3 */
			$cmd = " UPDATE PER_KPI_FORM SET
								SCORE_OTHER=$SCORE_OTHER,
								SUM_OTHER=$SUM_OTHER,
								TOTAL_SCORE=$SUM_TOTAL,
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							WHERE KF_ID=$KF_ID ";
			$db_dpis->send_cmd($cmd);
                        if($debug==1){echo __LINE__.'<pre>'.$cmd;}
//			$db_dpis->show_error();
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > บันทึกองค์ประกอบอื่นๆ [$KF_ID : $PER_ID]");
		} // end if
                       
		if ($KF_SCORE_STATUS==1 || $KPI_SCORE_CONFIRM!=1 || $SESS_USERGROUP == 1 || $PER_ID_REVIEW==$SESS_PER_ID || 
			$PER_ID_REVIEW0==$SESS_PER_ID || $PER_ID_REVIEW1==$SESS_PER_ID || $PER_ID_REVIEW2==$SESS_PER_ID) {
			$cmd = " select SCORE_KPI, SCORE_COMPETENCE, SCORE_OTHER, SUM_KPI, SUM_COMPETENCE, SUM_OTHER from PER_KPI_FORM where KF_ID=$KF_ID ";
			if($debug==1){echo '<pre>'.$cmd.'<br>';}
                        $db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SCORE_KPI = $data[SCORE_KPI];
			$SCORE_COMPETENCE = $data[SCORE_COMPETENCE];
			$SCORE_OTHER = $data[SCORE_OTHER];
			$SUM_KPI = $data[SUM_KPI];
			$SUM_COMPETENCE = $data[SUM_COMPETENCE];
			$SUM_OTHER = $data[SUM_OTHER];

			$cmd = " select PG_EVALUATE from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
			$COUNT_KPI = $db_dpis->send_cmd($cmd);
			if($debug==1){echo '<pre>'.$cmd.'<br>COUNT_KPI:'.$COUNT_KPI.'<br>';}
			$cmd = " select SUM(KPI_WEIGHT) as TOTAL_KPI_WEIGHT from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TOTAL_KPI_WEIGHT = $data[TOTAL_KPI_WEIGHT] + 0;
                        if($debug==1){echo '<pre>'.$cmd.'<br>TOTAL_KPI_WEIGHT:'.$TOTAL_KPI_WEIGHT.'<br>';}
		}

		$cmd = " select PER_TYPE, POS_ID, POEM_ID, POEMS_ID from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_TYPE = $data[PER_TYPE];
		$PM_CODE = "";
		if($PER_TYPE==1){ 
			$POS_ID = $data[POS_ID];
			
			$cmd = " select PM_CODE from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PM_CODE = trim($data[PM_CODE]);
		}elseif($PER_TYPE==2){ 
			$POS_ID = $data[POEM_ID];
		}elseif($PER_TYPE==3){ 
			$POS_ID = $data[POEMS_ID]; 
		} // end if

//		$search_condition = "and CP_MODEL in (1, 3)";
//		if($PM_CODE) $search_condition = "and CP_MODEL in (1, 2, 3)";

		$cmd = " select 	CP_CODE from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
		$COUNT_COMPETENCE = $db_dpis->send_cmd($cmd);
		$FULL_SCORE = 5;

		$cmd = " select 	sum(PC_TARGET_LEVEL) as SUM_TARGET_LEVEL from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SUM_TARGET_LEVEL =$data[SUM_TARGET_LEVEL];

//		$TMP_SCORE_KPI = round(round(($SCORE_KPI / ($COUNT_KPI * 5)), 3), 2);
		$TMP_SCORE_KPI = round(round(($SCORE_KPI / ($TOTAL_KPI_WEIGHT * 5)), 3), 2);
		if ($COMPETENCY_SCALE==1)
			$TMP_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / ($COUNT_COMPETENCE * 3)), 3), 2);
		elseif ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5 || $COMPETENCY_SCALE==6)
			$TMP_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / $FULL_SCORE), 3), 2);
		elseif ($COMPETENCY_SCALE==3)
			$TMP_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / $SUM_TARGET_LEVEL), 3), 2);

		$SUM_TOTAL = $SUM_KPI + $SUM_COMPETENCE + $SUM_OTHER;
	}elseif($SUBPAGE==2){
                $SCORE_KPI= $txtSCORE_KPI;
                $SCORE_COMPETENCE=$txtSCORE_COMPETENCE;
		if(!$SALARY_RESULT) $SALARY_RESULT = "NULL";		
		if(!$CH_KF_STATUS) $CH_KF_STATUS = "NULL";		
		if(!$CH_KF_SCORE_STATUS) $CH_KF_SCORE_STATUS = "NULL";		
		if($command=="UPDATE" && $KF_ID){
			$cmd = " UPDATE PER_KPI_FORM SET
								RESULT_COMMENT='$RESULT_COMMENT',
								COMPETENCE_COMMENT='$COMPETENCE_COMMENT',
								SALARY_RESULT=$SALARY_RESULT,
								SALARY_REMARK1='$SALARY_REMARK1',
								SALARY_REMARK2='$SALARY_REMARK2',
								KF_STATUS=$CH_KF_STATUS,
								KF_SCORE_STATUS=$CH_KF_SCORE_STATUS,
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							WHERE KF_ID=$KF_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			echo "<script>";
			echo "alert('บันทึกความเห็นของผู้บังคับบัญชาชั้นต้นแล้ว')";
			echo "</script>";		
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > บันทึกความเห็นของผู้บังคับบัญชาชั้นต้น [$KF_ID : $PER_ID]");
		} // end if

		$cmd = " select 	RESULT_COMMENT, COMPETENCE_COMMENT, SALARY_RESULT, SALARY_REMARK1, SALARY_REMARK2, KF_STATUS, KF_SCORE_STATUS
						 from		PER_KPI_FORM
						 where	KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$RESULT_COMMENT = $data[RESULT_COMMENT];
		$COMPETENCE_COMMENT = $data[COMPETENCE_COMMENT];
		$SALARY_RESULT = $data[SALARY_RESULT];
		$SALARY_REMARK1 = $data[SALARY_REMARK1];
		$SALARY_REMARK2 = $data[SALARY_REMARK2];
		$KF_STATUS = $data[KF_STATUS];
		$KF_SCORE_STATUS = $data[KF_SCORE_STATUS];
		if($KF_STATUS==1){	$USER_AUTH2 = FALSE;	}else{	$USER_AUTH2 = TRUE;	 }
	
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
	} // end if
?>