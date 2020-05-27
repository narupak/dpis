<?	
	//include("php_scripts/session_start.php");				//มาจาก include("php_scripts/personal_file.php"); 
	//include("php_scripts/function_share.php");
        $debug=0;/*0=close ,1=open*/
       
        
	$cmd = " select a.can_attach from user_privilege a,backoffice_menu_bar_lv1 b 
					where a.group_id in ($ACCESSIBLE_GROUP) and a.page_id=1 and a.menu_id_lv0=40 	and 
					a.menu_id_lv2=0 and a.menu_id_lv3=0 and a.menu_id_lv1=b.menu_id and b.flag_show='S' and 
					b.langcode='TH' and b.fid=1 and b.linkto_web = 'kpi_kpi_form.html' 
					order by a.menu_id_lv1 ";
	$db->send_cmd($cmd);
	//$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$PAGE_AUTH["attach"] = ($PAGE_AUTH["attach"]=="Y")?"Y":(($data[can_attach])?$data[can_attach]:"N");
        
        
	if($ATTACH_FILE==1){
		$CATEGORY="PER_ATTACHMENT";
	}else{
		$CATEGORY="PER_PERFORMANCE_GOALS";
	} 
	include("php_scripts/personal_file.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$SUBPAGE) $SUBPAGE = 1;

	$cmd = " select KC_EVALUATE, KC_WEIGHT, PC_TARGET_LEVEL, KC_REMARK, KC_SELF from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
	$count_competence = $db_dpis->send_cmd($cmd);
	$MAX_WEIGHT = 100;
	if	($COMPETENCY_SCALE==6){	$MAX_WEIGHT = 30;	}
	 if ($COMPETENCY_SCALE==5 || $COMPETENCY_SCALE==6) { 
		$TOTAL_SHOW_KC_WEIGHT = $MAX_WEIGHT;
	}
	$BALANCE_WEIGHT = $MAX_WEIGHT / $count_competence;

	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];

	/*$cmd = " select 	RESULT_COMMENT, COMPETENCE_COMMENT, SALARY_RESULT, SALARY_REMARK1, SALARY_REMARK2, 
						PER_ID_REVIEW, PER_ID_REVIEW0, AGREE_REVIEW1, DIFF_REVIEW1, AGREE_REVIEW2, DIFF_REVIEW2, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, KF_STATUS, LEVEL_NO, KF_SCORE_STATUS 
			   from 		PER_KPI_FORM 
			   where 	KF_ID=$KF_ID ";*//*เดิม*/
        /*Release 5.1.0.6*/
        $cmd = " select 	RESULT_COMMENT, COMPETENCE_COMMENT, SALARY_RESULT, SALARY_REMARK1, SALARY_REMARK2, 
						PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1,PER_ID_REVIEW2, AGREE_REVIEW1, DIFF_REVIEW1, AGREE_REVIEW2, DIFF_REVIEW2, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, KF_STATUS, LEVEL_NO, KF_SCORE_STATUS 
			   from 		PER_KPI_FORM 
			   where 	KF_ID=$KF_ID ";
        /*Release 5.1.0.6*/
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$RESULT_COMMENT = trim($data[RESULT_COMMENT]);
	$COMPETENCE_COMMENT = trim($data[COMPETENCE_COMMENT]);
	$SALARY_RESULT = trim($data[SALARY_RESULT]);
	$SALARY_REMARK1 = trim($data[SALARY_REMARK1]);
	$SALARY_REMARK2 = trim($data[SALARY_REMARK2]);

	$PER_ID_REVIEW = trim($data[PER_ID_REVIEW]);
	$PER_ID_REVIEW0 = trim($data[PER_ID_REVIEW0]);
        $PER_ID_REVIEW1 = trim($data[PER_ID_REVIEW1]);/*add Release 5.1.0.6*/
        $PER_ID_REVIEW2 = trim($data[PER_ID_REVIEW2]);/*add Release 5.1.0.6*/
        
	$AGREE_REVIEW1 = trim($data[AGREE_REVIEW1]);
	$DIFF_REVIEW1 = trim($data[DIFF_REVIEW1]);
	$AGREE_REVIEW2 = trim($data[AGREE_REVIEW2]);
	$DIFF_REVIEW2 = trim($data[DIFF_REVIEW2]);
	$PERFORMANCE_WEIGHT = $data[PERFORMANCE_WEIGHT];
	$COMPETENCE_WEIGHT = $data[COMPETENCE_WEIGHT];
	$OTHER_WEIGHT = $data[OTHER_WEIGHT];
	$KF_STATUS = $data[KF_STATUS];
	$LEVEL_NO = $data[LEVEL_NO];
	$KF_SCORE_STATUS = $data[KF_SCORE_STATUS];
	
	$cmd = " select IPIP_ID from PER_IPIP where KF_ID=$KF_ID ";
	$COUNT_IPIP = $db_dpis->send_cmd($cmd);

	if($SESS_USERGROUP == 1){
		$USER_AUTH1 = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && 
		$PER_ID==$SESS_PER_ID && !$RESULT_COMMENT && !$COMPETENCE_COMMENT && !$SALARY_RESULT && !$SALARY_REMARK1 
		&& !$SALARY_REMARK2 && !$COUNT_IPIP && !$AGREE_REVIEW1 && !$DIFF_REVIEW1 && !$AGREE_REVIEW2 && 
		!$DIFF_REVIEW2){
		$USER_AUTH1 = TRUE;
	}else{
		$USER_AUTH1 = FALSE;
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
	
//	echo "USER_AUTH1 :: ".(($USER_AUTH1)?"TRUE":"FALSE")."<br>";
//	echo "USER_AUTH2 :: ".(($USER_AUTH2)?"TRUE":"FALSE")."<br>";

	if($SUBPAGE==1){	
		if($PER_ID_REVIEW==$SESS_PER_ID || $USER_AUTH2){
			if(!$PG_EVALUATE) $PG_EVALUATE = "NULL";
			$UPD_PG_EVALUATE = "PG_EVALUATE=$PG_EVALUATE,";
		} // end if

		if($command=="UPDATE" && $KF_ID && $PG_ID){			
			$cmd = " UPDATE PER_PERFORMANCE_GOALS SET
								PG_RESULT='$PG_RESULT', PG_REMARK='$PG_REMARK', $UPD_PG_EVALUATE
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							WHERE KF_ID=$KF_ID and PG_ID=$PG_ID";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			if($PER_ID_REVIEW==$SESS_PER_ID || $USER_AUTH2){
				// ========================================= re-calculate score_kpi, sum_kpi ==================================//
				$cmd = " select SUM(KPI_WEIGHT) as TOTAL_KPI_WEIGHT from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID and KF_SCORE_FLAG=1 ";
				$db_dpis->send_cmd($cmd);
                                if($debug==1){echo __LINE__.'<pre>'.$cmd.'<br>';}
				$data = $db_dpis->get_array();
				$TOTAL_KPI_WEIGHT = $data[TOTAL_KPI_WEIGHT] + 0;

				$score_kpi = 0;
				$cmd = " select PG_EVALUATE, KPI_WEIGHT from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID and KF_SCORE_FLAG=1 ";
				$count_kpi = $db_dpis->send_cmd($cmd);
                                if($debug==1){echo __LINE__.'<pre>'.$cmd.'<br>';}
				while($data = $db_dpis->get_array()){ 
//					$score_kpi += ($data[PG_EVALUATE] + 0);
//					$score_kpi += (($data[PG_EVALUATE] + 0) * $data[KPI_WEIGHT]) / $TOTAL_KPI_WEIGHT;
					$score_kpi += (($data[PG_EVALUATE] + 0) * $data[KPI_WEIGHT]);
				} // end while
//				$sum_kpi = round(round((($score_kpi / ($count_kpi * 5)) * $PERFORMANCE_WEIGHT), 3), 2);
				$sum_kpi = round(round((($score_kpi / ($TOTAL_KPI_WEIGHT * 5)) * $PERFORMANCE_WEIGHT), 3), 2);
                                if($debug==1){
                                    echo __LINE__.'<br>score_kpi:'.$score_kpi.',TOTAL_KPI_WEIGHT:'.$TOTAL_KPI_WEIGHT.',PERFORMANCE_WEIGHT:'.$PERFORMANCE_WEIGHT.'<br>';
                                    echo '<br>round(round((('.$score_kpi.' / ('.$TOTAL_KPI_WEIGHT.' * 5)) * '.$PERFORMANCE_WEIGHT.'), 3), 2)<br>';
                                    }
			
				if($KF_ID){
					if($DPISDB=="oci8")
						$cmd = " UPDATE PER_KPI_FORM SET
										SCORE_KPI=$score_kpi, SUM_KPI=$sum_kpi, TOTAL_SCORE=$sum_kpi+nvl(SUM_COMPETENCE,0)+nvl(SUM_OTHER,0),
										UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
										WHERE KF_ID=$KF_ID ";
					else
						$cmd = " UPDATE PER_KPI_FORM SET
										SCORE_KPI=$score_kpi, SUM_KPI=$sum_kpi, TOTAL_SCORE=$sum_kpi+SUM_COMPETENCE+SUM_OTHER,
										UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
										WHERE KF_ID=$KF_ID ";
					$db_dpis->send_cmd($cmd);
                                        if($debug==1){echo __LINE__.'<pre>'.$cmd;}
//					$db_dpis->show_error();
				}
				// ========================================= re-calculate score_kpi, sum_kpi ==================================//
			} // end if

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > บันทึกผลสำเร็จของงานจริง [$PG_ID : $PG_SEQ : $PFR_NAME : $KPI_NAME]");
		} // end if
		
		if(($UPD && $KF_ID && $PG_ID) || ($VIEW && $KF_ID && $PG_ID)){
			if($DPISDB=="odbc") 
				$cmd = " SELECT 	a.PG_SEQ, a.KPI_ID, a.KPI_NAME, b.KPI_NAME as KPI_ORG_NAME, c.PFR_ID, c.PFR_ID_REF, c.PFR_NAME, a.KPI_TARGET_LEVEL1, 
														a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5, a.KPI_TARGET_LEVEL1_DESC, 
														a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC, 
														a.PG_RESULT, a.PG_EVALUATE, a.PG_REMARK, a.KF_TYPE, a.KF_SCORE_FLAG, a.KPI_OTHER
								   from		(
													PER_PERFORMANCE_GOALS a
													left join PER_KPI b on (a.KPI_ID=b.KPI_ID)
												) left join PER_PERFORMANCE_REVIEW c on (b.PFR_ID=c.PFR_ID)
								WHERE		a.KF_ID=$KF_ID and a.PG_ID=$PG_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = "	SELECT 	a.PG_SEQ, a.KPI_ID, a.KPI_NAME, b.KPI_NAME as KPI_ORG_NAME, c.PFR_ID, c.PFR_ID_REF, c.PFR_NAME, a.KPI_TARGET_LEVEL1, 
														a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5, a.KPI_TARGET_LEVEL1_DESC, 
														a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC, 
														a.PG_RESULT, a.PG_EVALUATE, a.PG_REMARK, a.KF_TYPE, a.KF_SCORE_FLAG, a.KPI_OTHER
								FROM		PER_PERFORMANCE_GOALS a, PER_KPI b, PER_PERFORMANCE_REVIEW c 
								WHERE		a.KF_ID=$KF_ID and a.PG_ID=$PG_ID and a.KPI_ID=b.KPI_ID(+) and b.PFR_ID=c.PFR_ID(+) ";
			elseif($DPISDB=="mysql")
				$cmd = " SELECT 	a.PG_SEQ, a.KPI_ID, a.KPI_NAME, b.KPI_NAME as KPI_ORG_NAME, c.PFR_ID, c.PFR_ID_REF, c.PFR_NAME, a.KPI_TARGET_LEVEL1, 
														a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5, a.KPI_TARGET_LEVEL1_DESC, 
														a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC, 
														a.PG_RESULT, a.PG_EVALUATE, a.PG_REMARK, a.KF_TYPE, a.KF_SCORE_FLAG, a.KPI_OTHER
								   from		(
													PER_PERFORMANCE_GOALS a
													left join PER_KPI b on (a.KPI_ID=b.KPI_ID)
												) left join PER_PERFORMANCE_REVIEW c on (b.PFR_ID=c.PFR_ID)
								WHERE		a.KF_ID=$KF_ID and a.PG_ID=$PG_ID ";
			$db_dpis->send_cmd($cmd);
//			echo "--> $cmd ";
			$data = $db_dpis->get_array();
			$PG_SEQ = $data[PG_SEQ];
			$KPI_ID = $data[KPI_ID];
			$KPI_NAME = $data[KPI_NAME];
			$KPI_ORG_NAME = $data[KPI_ORG_NAME];
			$PFR_ID = $data[PFR_ID];
			$PFR_ID_REF = $data[PFR_ID_REF];
			$PFR_NAME2 = $data[PFR_NAME];
			$KF_SCORE_FLAG = $data[KF_SCORE_FLAG];
			$KPI_OTHER = $data[KPI_OTHER];

			$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
			$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
			$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
			$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
			$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
			$KPI_TARGET_LEVEL1_DESC = $data[KPI_TARGET_LEVEL1_DESC];
			$KPI_TARGET_LEVEL2_DESC = $data[KPI_TARGET_LEVEL2_DESC];
			$KPI_TARGET_LEVEL3_DESC = $data[KPI_TARGET_LEVEL3_DESC];
			$KPI_TARGET_LEVEL4_DESC = $data[KPI_TARGET_LEVEL4_DESC];
			$KPI_TARGET_LEVEL5_DESC = $data[KPI_TARGET_LEVEL5_DESC];
			
			if ($KF_SCORE_STATUS==1 || $KPI_SCORE_CONFIRM!=1 || $SESS_USERGROUP == 1 || $PER_ID_REVIEW==$SESS_PER_ID || 
				$PER_ID_REVIEW0==$SESS_PER_ID || $PER_ID_REVIEW1==$SESS_PER_ID || $PER_ID_REVIEW2==$SESS_PER_ID) {
				$PG_EVALUATE = $data[PG_EVALUATE];		
			}
			$PG_RESULT = $data[PG_RESULT];
			$PG_REMARK = $data[PG_REMARK];			
			$KF_TYPE = $data[KF_TYPE];
			
			if ($PFR_ID!=$PFR_ID_REF) {
				$cmd = " select PFR_ID_REF, PFR_NAME from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID_REF ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$PFR_ID_REF = $data[PFR_ID_REF];
				$PFR_NAME1 = $data[PFR_NAME];
				if ($PFR_ID!=$PFR_ID_REF) {
					$cmd = " select PFR_ID_REF, PFR_NAME from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID_REF ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$PFR_ID_REF = $data[PFR_ID_REF];
					$PFR_NAME = $data[PFR_NAME];
				}
			}
		} // end if
		
		$cmd = " select SUM(KPI_WEIGHT) as TOTAL_KPI_WEIGHT from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID and KF_SCORE_FLAG=1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TOTAL_KPI_WEIGHT = $data[TOTAL_KPI_WEIGHT] + 0;
	}elseif($SUBPAGE==2){ 
		$cmd = " select PER_TYPE, POS_ID, POEM_ID, POEMS_ID from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE==1){ 
			$POS_ID = $data[POS_ID];
			
			$cmd = " select PM_CODE from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PM_CODE = trim($data[PM_CODE]);
		}elseif($PER_TYPE==2){ 
			$POS_ID = $data[POEM_ID];
			$PM_CODE = "";
		}elseif($PER_TYPE==3){ 
			$POS_ID = $data[POEMS_ID]; 
			$PM_CODE = "";
		} // end if

		if(!isset($KC_EVALUATE) || $KC_EVALUATE == "") $KC_EVALUATE = "NULL";
		if(!isset($KC_SELF) || $KC_SELF == "") $KC_SELF = "NULL";
		if(!isset($KC_WEIGHT) || $KC_WEIGHT == "") $KC_WEIGHT = "NULL";
		if($command=="UPDATE" && $KF_ID && $KC_ID){				//ตัวเก่าที่เป็น form xxxx ใส่คะแนนประเมินสมรรถนะ
			if (!is_null($KC_EVALUATE) && $KC_EVALUATE != "NULL" && $KC_EVALUATE != "") {
				if (is_null($KC_WEIGHT) || $KC_WEIGHT == "NULL" || $KC_WEIGHT == "") {
					$KC_WEIGHT = 0;
				}
			}
			$cmd = " UPDATE PER_KPI_COMPETENCE SET
								KC_EVALUATE=$KC_EVALUATE,
								KC_WEIGHT=$KC_WEIGHT,
								KC_REMARK='$KC_REMARK',
								KC_SELF=$KC_SELF,
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							WHERE KF_ID=$KF_ID and KC_ID=$KC_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			// ========================================= re-calculate score_competence, sum_competence ==================================//
			$TOTAL_PC_SCORE = 0;
			$FULL_SCORE = 5;
			$cmd = " select 	KC_EVALUATE, KC_WEIGHT, PC_TARGET_LEVEL, KC_REMARK, KC_SELF from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
			$count_competence = $db_dpis->send_cmd($cmd);
			$BALANCE_WEIGHT = $MAX_WEIGHT / $count_competence;
			while($data = $db_dpis->get_array()){
				$KC_EVALUATE = $data[KC_EVALUATE];
				$KC_SELF = $data[KC_SELF];
				$KC_WEIGHT = $data[KC_WEIGHT] + 0;
				$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL] + 0;
				if ($COMPETENCY_SCALE==5) {
					$TOTAL_PC_SCORE += $KC_EVALUATE * $BALANCE_WEIGHT / 100;
				}else if ($COMPETENCY_SCALE==6) {
					if ($LEVEL_NO=='O1' || $LEVEL_NO=='O2' || $LEVEL_NO=='K1') {
						if ($KC_EVALUATE==0) $PC_SCORE = 0;
						elseif ($KC_EVALUATE==1) $PC_SCORE = 4;
						elseif ($KC_EVALUATE==2) $PC_SCORE = 5;
						elseif ($KC_EVALUATE==3) $PC_SCORE = 5;
						elseif ($KC_EVALUATE==4) $PC_SCORE = 5;
						elseif ($KC_EVALUATE==5) $PC_SCORE = 5;
					} elseif ($LEVEL_NO=='O3' || $LEVEL_NO=='K2') {
						if ($KC_EVALUATE==0) $PC_SCORE = 0;
						elseif ($KC_EVALUATE==1) $PC_SCORE = 3;
						elseif ($KC_EVALUATE==2) $PC_SCORE = 4;
						elseif ($KC_EVALUATE==3) $PC_SCORE = 5;
						elseif ($KC_EVALUATE==4) $PC_SCORE = 5;
						elseif ($KC_EVALUATE==5) $PC_SCORE = 5;
					} elseif ($LEVEL_NO=='O4' || $LEVEL_NO=='K3' || $LEVEL_NO=='K4' || $LEVEL_NO=='K5' || 
						$LEVEL_NO=='D1' || $LEVEL_NO=='D2' || $LEVEL_NO=='M1' || $LEVEL_NO=='M2') {
						if ($KC_EVALUATE==0) $PC_SCORE = 0;
						elseif ($KC_EVALUATE==1) $PC_SCORE = 2;
						elseif ($KC_EVALUATE==2) $PC_SCORE = 3;
						elseif ($KC_EVALUATE==3) $PC_SCORE = 4;
						elseif ($KC_EVALUATE==4) $PC_SCORE = 5;
						elseif ($KC_EVALUATE==5) $PC_SCORE = 5;
					} 
					
					$PC_SCORE = $PC_SCORE /  5;
					$TOTAL_PC_SCORE += $PC_SCORE;
				}else {
					$TOTAL_PC_SCORE += $KC_EVALUATE * $KC_WEIGHT / 100;
				}
				$SUM_EVALUATE += $KC_EVALUATE;
				$SUM_SELF += $KC_SELF;
				$SUM_TARGET_LEVEL += $PC_TARGET_LEVEL;
				
				if($KC_EVALUATE != ""){
					if($KC_EVALUATE >= $PC_TARGET_LEVEL) $tmp_arr_count[GE] += 1;
					elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 1) $tmp_arr_count[L1] += 1;
					elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 2) $tmp_arr_count[L2] += 1;
					elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 3) $tmp_arr_count[L3] += 1;
					else $tmp_arr_count[L3] += 1;
				} // end if
			} // end while
			
			$tmp_arr_score[GE] = $tmp_arr_count[GE] * 3;
			$tmp_arr_score[L1] = $tmp_arr_count[L1] * 2;
			$tmp_arr_score[L2] = $tmp_arr_count[L2] * 1;
			$tmp_arr_score[L3] = $tmp_arr_count[L3] * 0;
			
			if ($COMPETENCY_SCALE==1) {
				$score_competence = array_sum($tmp_arr_score);	
				$sum_competence = round(round((($score_competence / ($count_competence * 3)) * $COMPETENCE_WEIGHT), 3), 2);
			} elseif ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5 || $COMPETENCY_SCALE==6) { 		
				$score_competence = $TOTAL_PC_SCORE;	
				$sum_competence = round(round((($score_competence / $FULL_SCORE) * $COMPETENCE_WEIGHT), 3), 2);
			} elseif ($COMPETENCY_SCALE==3) { 		
				$score_competence = $SUM_EVALUATE;	
				$sum_competence = round(round((($score_competence / $SUM_TARGET_LEVEL) * $COMPETENCE_WEIGHT), 3), 2);
			}

			if($KF_ID){
				if($DPISDB=="oci8")
					$cmd = " UPDATE PER_KPI_FORM SET
										SCORE_COMPETENCE=$score_competence, SUM_COMPETENCE=$sum_competence, 
										TOTAL_SCORE=nvl(SUM_KPI,0)+nvl(SUM_OTHER,0)+$sum_competence,
										UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
										WHERE KF_ID=$KF_ID ";
				else
					$cmd = " UPDATE PER_KPI_FORM SET
										SCORE_COMPETENCE=$score_competence, SUM_COMPETENCE=$sum_competence, 
										TOTAL_SCORE=SUM_KPI+SUM_OTHER+$sum_competence,
										UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
										WHERE KF_ID=$KF_ID ";
				$db_dpis->send_cmd($cmd);
                                if($debug==1){echo __LINE__.'<pre>'.$cmd;}
//				$db_dpis->show_error();
			}
			// ========================================= re-calculate score_competence, sum_competence ==================================//

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > บันทึกผลการประเมิน [$KC_ID : $CP_CODE : $CP_NAME]");
		} // end if
		
		//============UPDATE_KC_WEIGHT (น้ำหนัก) และUPDATE_KC_EVALUATE (ผลการประเมินสมรรถนะ)===============//
                //echo $command.",KF_ID=".$KF_ID;
		if($command=="UPDATE_KC_WEIGHT_EVALUATE" && $KF_ID){
                   // echo $command;
			foreach($SHOW_KC_EVALUATE as $kc_id => $kc_val) {	//หา indext
				if(!isset($SHOW_KC_EVALUATE[$kc_id]) || $SHOW_KC_EVALUATE[$kc_id] == "") $SHOW_KC_EVALUATE[$kc_id] = "NULL";
				if(!isset($SHOW_KC_SELF[$kc_id]) || $SHOW_KC_SELF[$kc_id] == "") $SHOW_KC_SELF[$kc_id] = "NULL";
				if(!isset($SHOW_KC_WEIGHT[$kc_id]) || $SHOW_KC_WEIGHT[$kc_id] == "") $SHOW_KC_WEIGHT[$kc_id] = "NULL";
				if (!is_null($SHOW_KC_EVALUATE[$kc_id]) && $SHOW_KC_EVALUATE[$kc_id] != "NULL" && $SHOW_KC_EVALUATE[$kc_id] != "") {
					if (is_null($SHOW_KC_WEIGHT[$kc_id]) || $SHOW_KC_WEIGHT[$kc_id] == "NULL" || $SHOW_KC_WEIGHT[$kc_id] == "") {
						$SHOW_KC_WEIGHT[$kc_id] = 0;
					}
				}

				//===============================================================//
				if ($PER_ID==$SESS_PER_ID) 
					$cmd = " UPDATE PER_KPI_COMPETENCE SET
										KC_SELF=$SHOW_KC_SELF[$kc_id],
										KC_WEIGHT=$SHOW_KC_WEIGHT[$kc_id],
										UPDATE_USER=$SESS_USERID, 
										UPDATE_DATE='$UPDATE_DATE'
									WHERE KF_ID=$KF_ID and KC_ID=$kc_id ";
				elseif ($PER_ID_REVIEW==$SESS_PER_ID || $USER_AUTH2)
					$cmd = " UPDATE PER_KPI_COMPETENCE SET
										KC_EVALUATE=$SHOW_KC_EVALUATE[$kc_id],
										KC_WEIGHT=$SHOW_KC_WEIGHT[$kc_id],
										KC_REMARK='$SHOW_KC_REMARK[$kc_id]',
										KC_SELF=$SHOW_KC_SELF[$kc_id],
										UPDATE_USER=$SESS_USERID, 
										UPDATE_DATE='$UPDATE_DATE'
									WHERE KF_ID=$KF_ID and KC_ID=$kc_id ";
				//echo "===> $cmd <=== <br>";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > บันทึกน้ำหนัก และผลการประเมินสมรรถนะ [$kc_id : $CP_CODE : $CP_NAME]");
			} //end for

			// ========================================= re-calculate score_competence, sum_competence ==================================//
			if ($PER_ID_REVIEW==$SESS_PER_ID || $USER_AUTH2) {
				$TOTAL_PC_SCORE = 0;
				$FULL_SCORE = 5;
				$cmd = " select 	KC_EVALUATE, KC_WEIGHT, PC_TARGET_LEVEL, KC_REMARK, KC_SELF from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
				$count_competence = $db_dpis->send_cmd($cmd);
				$BALANCE_WEIGHT = $MAX_WEIGHT / $count_competence;
				while($data = $db_dpis->get_array()){
					$KC_EVALUATE = $data[KC_EVALUATE];
					$KC_SELF = $data[KC_SELF];
					$KC_WEIGHT = $data[KC_WEIGHT] + 0;
					$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL] + 0;
					if ($COMPETENCY_SCALE==5) {
						$TOTAL_PC_SCORE += $KC_EVALUATE * $BALANCE_WEIGHT / 100;
					} else if ($COMPETENCY_SCALE==6) {
						if ($LEVEL_NO=='O1' || $LEVEL_NO=='O2' || $LEVEL_NO=='K1') {
							if ($KC_EVALUATE==0) $PC_SCORE = 0;
							elseif ($KC_EVALUATE==1) $PC_SCORE = 4;
							elseif ($KC_EVALUATE==2) $PC_SCORE = 5;
							elseif ($KC_EVALUATE==3) $PC_SCORE = 5;
							elseif ($KC_EVALUATE==4) $PC_SCORE = 5;
							elseif ($KC_EVALUATE==5) $PC_SCORE = 5;
						} elseif ($LEVEL_NO=='O3' || $LEVEL_NO=='K2') {
							if ($KC_EVALUATE==0) $PC_SCORE = 0;
							elseif ($KC_EVALUATE==1) $PC_SCORE = 3;
							elseif ($KC_EVALUATE==2) $PC_SCORE = 4;
							elseif ($KC_EVALUATE==3) $PC_SCORE = 5;
							elseif ($KC_EVALUATE==4) $PC_SCORE = 5;
							elseif ($KC_EVALUATE==5) $PC_SCORE = 5;
						} elseif ($LEVEL_NO=='O4' || $LEVEL_NO=='K3' || $LEVEL_NO=='K4' || $LEVEL_NO=='K5' || 
							$LEVEL_NO=='D1' || $LEVEL_NO=='D2' || $LEVEL_NO=='M1' || $LEVEL_NO=='M2') {
							if ($KC_EVALUATE==0) $PC_SCORE = 0;
							elseif ($KC_EVALUATE==1) $PC_SCORE = 2;
							elseif ($KC_EVALUATE==2) $PC_SCORE = 3;
							elseif ($KC_EVALUATE==3) $PC_SCORE = 4;
							elseif ($KC_EVALUATE==4) $PC_SCORE = 5;
							elseif ($KC_EVALUATE==5) $PC_SCORE = 5;
						} 
						
						$PC_SCORE = $PC_SCORE /  5;
						$TOTAL_PC_SCORE += $PC_SCORE;
					} else {
						$TOTAL_PC_SCORE += $KC_EVALUATE * $KC_WEIGHT / 100;
					}
					$SUM_EVALUATE += $KC_EVALUATE;
					$SUM_SELF += $KC_SELF;
					$SUM_TARGET_LEVEL += $PC_TARGET_LEVEL;
					
					if($KC_EVALUATE != ""){
						if($KC_EVALUATE >= $PC_TARGET_LEVEL) $tmp_arr_count[GE] += 1;
						elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 1) $tmp_arr_count[L1] += 1;
						elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 2) $tmp_arr_count[L2] += 1;
						elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 3) $tmp_arr_count[L3] += 1;
						else $tmp_arr_count[L3] += 1;
					} // end if
				} // end while
				
				$tmp_arr_score[GE] = $tmp_arr_count[GE] * 3;
				$tmp_arr_score[L1] = $tmp_arr_count[L1] * 2;
				$tmp_arr_score[L2] = $tmp_arr_count[L2] * 1;
				$tmp_arr_score[L3] = $tmp_arr_count[L3] * 0;
				
				if ($COMPETENCY_SCALE==1) {
					$score_competence = array_sum($tmp_arr_score);	
					$sum_competence = round(round((($score_competence / ($count_competence * 3)) * $COMPETENCE_WEIGHT), 3), 2);
				} elseif ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5 || $COMPETENCY_SCALE==6) { 		
					$score_competence = $TOTAL_PC_SCORE;	
					$sum_competence = round(round((($score_competence / $FULL_SCORE) * $COMPETENCE_WEIGHT), 3), 2);
				} elseif ($COMPETENCY_SCALE==3) { 		
					$score_competence = $SUM_EVALUATE;	
					$sum_competence = round(round((($score_competence / $SUM_TARGET_LEVEL) * $COMPETENCE_WEIGHT), 3), 2);
				} 

				if($KF_ID){
					if($DPISDB=="oci8"){
						$cmd = " UPDATE PER_KPI_FORM SET
											SCORE_COMPETENCE=$score_competence, SUM_COMPETENCE=$sum_competence, 
											TOTAL_SCORE=nvl(SUM_KPI,0)+nvl(SUM_OTHER,0)+$sum_competence,
											UPDATE_USER=$SESS_USERID, 
											UPDATE_DATE='$UPDATE_DATE'
											WHERE KF_ID=$KF_ID ";
					}else{
						$cmd = " UPDATE PER_KPI_FORM SET
											SCORE_COMPETENCE=$score_competence, SUM_COMPETENCE=$sum_competence, 
											TOTAL_SCORE=SUM_KPI+SUM_OTHER+$sum_competence,
											UPDATE_USER=$SESS_USERID, 
											UPDATE_DATE='$UPDATE_DATE'
											WHERE KF_ID=$KF_ID ";
					}
                                       if($debug==1){echo __LINE__.'<pre>'.$cmd;}
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				}
				// ========================================= re-calculate score_competence, sum_competence ==================================//
			}
		} // end if

		$cmd = " select KC_EVALUATE, KC_WEIGHT, PC_TARGET_LEVEL, KC_REMARK, KC_SELF from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			$tmp_KC_EVALUATE = $data[KC_EVALUATE];
			$tmp_KC_SELF = $data[KC_SELF];
			$tmp_KC_WEIGHT = $data[KC_WEIGHT] + 0;
			$tmp_PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL] + 0;
			
			if($tmp_KC_EVALUATE != ""){
				if($tmp_KC_EVALUATE >= $tmp_PC_TARGET_LEVEL) $arr_count[GE] += 1;
				elseif(($tmp_PC_TARGET_LEVEL - $tmp_KC_EVALUATE) <= 1) $arr_count[L1] += 1;
				elseif(($tmp_PC_TARGET_LEVEL - $tmp_KC_EVALUATE) <= 2) $arr_count[L2] += 1;
				elseif(($tmp_PC_TARGET_LEVEL - $tmp_KC_EVALUATE) <= 3) $arr_count[L3] += 1;
				else $arr_count[L3] += 1;
			} // end if
		} // end while
		
		$arr_score[GE] = $arr_count[GE] * 3;
		$arr_score[L1] = $arr_count[L1] * 2;
		$arr_score[L2] = $arr_count[L2] * 1;
		$arr_score[L3] = $arr_count[L3] * 0;
		
		$total_score = "";
		if($arr_count[GE] || $arr_count[L1] || $arr_count[L2] || $arr_count[L3]) $total_score = array_sum($arr_score);

		if(($UPD && $KF_ID && $KC_ID) || ($VIEW && $KF_ID && $KC_ID)){
			$cmd = "	SELECT 	a.CP_CODE, b.CP_NAME, a.KC_EVALUATE, a.KC_WEIGHT, a.PC_TARGET_LEVEL, a.KC_REMARK, a.KC_SELF
								FROM		PER_KPI_COMPETENCE a, PER_COMPETENCE b
								WHERE		a.KF_ID=$KF_ID and a.KC_ID=$KC_ID and a.CP_CODE=b.CP_CODE(+) and b.DEPARTMENT_ID=$DEPARTMENT_ID  ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CP_CODE = trim($data[CP_CODE]);
			$CP_NAME = $data[CP_NAME];
			$KC_EVALUATE = $data[KC_EVALUATE];
			$KC_SELF = $data[KC_SELF];
			$KC_WEIGHT = $data[KC_WEIGHT];
			$PC_TARGET_LEVEL = abs($data[PC_TARGET_LEVEL]);
			$KC_REMARK = $data[KC_REMARK];
		} // end if
				
		if( !$UPD && !$VIEW && !$err_text){
			$KC_ID = "";
			$CP_CODE = "";
			$CP_NAME = "";		
		} // end if
	} // end if
	
	//หารายละเอียด	PER_COMPETENCE_LEVEL --------------------------
	if($DPISDB=="odbc"){
		$cmd = " select a.CP_CODE, a.CL_NO, CL_NAME, CL_MEANING, CP_NAME 
						from PER_COMPETENCE_LEVEL a, PER_COMPETENCE b 
						where a.CP_CODE=b.CP_CODE and a.DEPARTMENT_ID=b.DEPARTMENT_ID and a.DEPARTMENT_ID=$DEPARTMENT_ID 
						order by CP_NAME asc, a.CL_NO "; 
	}elseif($DPISDB=="oci8"){
		$cmd = " select a.CP_CODE, a.CL_NO, CL_NAME, CL_MEANING, CP_NAME 
						from PER_COMPETENCE_LEVEL a, PER_COMPETENCE b 
						where a.CP_CODE=b.CP_CODE(+) and a.DEPARTMENT_ID=b.DEPARTMENT_ID and a.DEPARTMENT_ID=$DEPARTMENT_ID 
						order by CP_NAME asc, a.CL_NO "; 
	}elseif($DPISDB=="mysql"){
		$cmd = " select a.CP_CODE, a.CL_NO, CL_NAME, CL_MEANING, CP_NAME 
						from PER_COMPETENCE_LEVEL a, PER_COMPETENCE b 
						where a.CP_CODE=b.CP_CODE and a.DEPARTMENT_ID=b.DEPARTMENT_ID and a.DEPARTMENT_ID=$DEPARTMENT_ID 
						order by CP_NAME asc, a.CL_NO "; 
	} // end if
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$TMP_CP_CODE = $data[CP_CODE];
		$TMP_CP_NAME = trim($data[CP_NAME]);
		$TMP_CL_NO = trim($data[CL_NO]);
		$TMP_CL_NAME = trim($data[CL_NAME]);
		$TMP_CL_MEANING = trim($data[CL_MEANING]);

		$DETAIL_CP_CODE[$TMP_CP_CODE] .= "&nbsp;&nbsp;&nbsp;<u>ระดับที่ ".$TMP_CL_NO."</u> ".$TMP_CL_NAME."<br>"; //แยกเฉพาะ สมรรถนะ นั้น
		$DETAIL_COMPETENCE_CP_NAME[$TMP_CP_CODE] = $DETAIL_CP_CODE[$TMP_CP_CODE];
		$DETAIL_COMPETENCE_CL_NO[$TMP_CP_CODE][$TMP_CL_NO] = "&nbsp;".$TMP_CL_NAME; //แยกเฉพาะ สมรรถนะ และระดับ นั้น
	}
	
	//echo "<br>$UPD / $VIEW / $err_text ";
	if( !$UPD && !$VIEW && !$err_text){ //ให้การแสดง ผลงานจริง ขึ้น  if( !$UPD || !$VIEW || !$err_text){
		$PG_ID = "";
		$PG_SEQ = "";			
		$PFR_NAME = "";
		$KPI_ID = "";
		$KPI_NAME = "";		
		$KPI_ORG_NAME = "";		
		$KPI_OTHER = "";		
		
		$KPI_TARGET_LEVEL1 = "";
		$KPI_TARGET_LEVEL2 = "";
		$KPI_TARGET_LEVEL3 = "";
		$KPI_TARGET_LEVEL4 = "";
		$KPI_TARGET_LEVEL5 = "";
		$KPI_TARGET_LEVEL1_DESC = "";
		$KPI_TARGET_LEVEL2_DESC = "";
		$KPI_TARGET_LEVEL3_DESC = "";
		$KPI_TARGET_LEVEL4_DESC = "";
		$KPI_TARGET_LEVEL5_DESC = "";
		
		$PG_RESULT = "";
		$PG_EVALUATE = "";
		$PG_REMARK = "";
		$KF_TYPE = "";
	} // end if
?>