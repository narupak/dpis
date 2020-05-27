<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$SUBPAGE) $SUBPAGE = 1;
	if($UPD_RETURN)	$UPD=trim($UPD_RETURN);
	
	if($command=="CANCEL2"){ // กดยกเลิก เเล้วกลับไปหน้าเดิม  ของ ส่วนที่ 1.2		
        $UPD="";
        $VIEW="";                     
                }
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];
	
	
	
	$cmd = " SELECT RESULT_COMMENT, COMPETENCE_COMMENT, SALARY_RESULT, SALARY_REMARK1, SALARY_REMARK2, 
                        AGREE_REVIEW1, DIFF_REVIEW1, AGREE_REVIEW2, DIFF_REVIEW2, PERFORMANCE_WEIGHT, 
                        COMPETENCE_WEIGHT, OTHER_WEIGHT ,KF_STATUS ,KF_SCORE_STATUS,SUBSTR( KF_END_DATE,0,4) AS MYKPI_YEAR
                FROM PER_KPI_FORM 
                WHERE KF_ID=$KF_ID ";
	//echo $cmd; 
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$RESULT_COMMENT = trim($data[RESULT_COMMENT]);
	$COMPETENCE_COMMENT = trim($data[COMPETENCE_COMMENT]);
	$SALARY_RESULT = trim($data[SALARY_RESULT]);
	$SALARY_REMARK1 = trim($data[SALARY_REMARK1]);
	$SALARY_REMARK2 = trim($data[SALARY_REMARK2]);

	$AGREE_REVIEW1 = trim($data[AGREE_REVIEW1]);
	$DIFF_REVIEW1 = trim($data[DIFF_REVIEW1]);
	$AGREE_REVIEW2 = trim($data[AGREE_REVIEW2]);
	$DIFF_REVIEW2 = trim($data[DIFF_REVIEW2]);
	$PERFORMANCE_WEIGHT = $data[PERFORMANCE_WEIGHT];
	$COMPETENCE_WEIGHT = $data[COMPETENCE_WEIGHT];
	$OTHER_WEIGHT = $data[OTHER_WEIGHT];
        
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
        
        $KF_STATUS = $data[KF_STATUS];/*Release 5.2.0.1*/
        $KF_SCORE_STATUS= $data[KF_SCORE_STATUS];/*<<< Release 5.2.1.5 กรณีที่ผู้ประเมินให้คะแนนแล้ว ถือว่าสิ้นสุดจะไม่ให้แก้ไขส่วนอื่นๆ*/
//	echo "$PERFORMANCE_WEIGHT / $COMPETENCE_WEIGHT / $OTHER_WEIGHT<br>";
	
	$cmd = " select IPIP_ID from PER_IPIP where KF_ID=$KF_ID ";
	$COUNT_IPIP = $db_dpis->send_cmd($cmd);
        
        /*เดิม*/
	/*if($SESS_USERGROUP == 1){  //$SESS_PER_ID == $PER_ID_REVIEW
		$USER_AUTH = TRUE; 
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID==$SESS_PER_ID && !$RESULT_COMMENT && !$COMPETENCE_COMMENT && !$SALARY_RESULT && !$SALARY_REMARK1 && !$SALARY_REMARK2 && !$COUNT_IPIP && !$AGREE_REVIEW1 && !$DIFF_REVIEW1 && !$AGREE_REVIEW2 && !$DIFF_REVIEW2){
		$USER_AUTH = TRUE;
	}else{
		$USER_AUTH = FALSE;
	}*/ // end if
        /*ปรังปรุง Release 5.2.0.1*/

        if($SESS_USERGROUP == 1){
		$USER_AUTH = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) 
                && $PER_ID==$SESS_PER_ID && empty($KF_STATUS) ){
		$USER_AUTH = TRUE;
	}else{
		$USER_AUTH = FALSE;
	}
        /**/
	
	if($SUBPAGE==1){
		if($command=="ADD" && $KF_ID && $PG_SEQ && trim($KPI_NAME)){
			$KPI_NAME = trim($KPI_NAME);
			$cmd = " select PG_SEQ, KPI_NAME from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID and KPI_NAME = '$KPI_NAME' ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				$cmd = " select max(PG_ID) as max_id from PER_PERFORMANCE_GOALS ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$PG_ID = $data[max_id] + 1;
				
				if(!$KPI_ID) $KPI_ID = "NULL";
				if(!$KPI_WEIGHT) $KPI_WEIGHT = "NULL";
				if(!$KPI_TARGET_LEVEL1) $KPI_TARGET_LEVEL1 = "NULL";
				if(!$KPI_TARGET_LEVEL2) $KPI_TARGET_LEVEL2 = "NULL";
				if(!$KPI_TARGET_LEVEL3) $KPI_TARGET_LEVEL3 = "NULL";
				if(!$KPI_TARGET_LEVEL4) $KPI_TARGET_LEVEL4 = "NULL";
				if(!$KPI_TARGET_LEVEL5) $KPI_TARGET_LEVEL5 = "NULL";
				if(!$KF_SCORE_FLAG) $KF_SCORE_FLAG = "NULL";
				
				if (!get_magic_quotes_gpc()) {
					$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL1_DESC)));
					$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL2_DESC)));
					$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL3_DESC)));
					$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL4_DESC)));
					$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL5_DESC)));
				}else{
					$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL1_DESC))));
					$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL2_DESC))));
					$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL3_DESC))));
					$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL4_DESC))));
					$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL5_DESC))));
				} // end if
		
				$cmd = " 	insert into PER_PERFORMANCE_GOALS
									(PG_ID, KF_ID, PG_SEQ, KPI_ID, KPI_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, KF_TYPE,
									KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5,
									KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, KPI_TARGET_LEVEL4_DESC, 
									KPI_TARGET_LEVEL5_DESC, KF_SCORE_FLAG, KPI_OTHER, UPDATE_USER, UPDATE_DATE)
									values
									($PG_ID, $KF_ID, $PG_SEQ, $KPI_ID, '$KPI_NAME', $KPI_WEIGHT, '$KPI_MEASURE', $KPI_PER_ID, '$KF_TYPE',
									$KPI_TARGET_LEVEL1, $KPI_TARGET_LEVEL2, $KPI_TARGET_LEVEL3, $KPI_TARGET_LEVEL4, $KPI_TARGET_LEVEL5,
									'$KPI_TARGET_LEVEL1_DESC', '$KPI_TARGET_LEVEL2_DESC', '$KPI_TARGET_LEVEL3_DESC', '$KPI_TARGET_LEVEL4_DESC', 
									'$KPI_TARGET_LEVEL5_DESC', $KF_SCORE_FLAG, '$KPI_OTHER', $SESS_USERID, '$UPDATE_DATE')   ";
				$db_dpis->send_cmd($cmd);
//echo "xxxxxxxxxx".$cmd;				
//				$db_dpis->show_error();
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มผลสำเร็จของงานที่คาดหวัง [$PG_ID : $PG_SEQ : $KPI_NAME]");
			}else{
				$data = $db_dpis->get_array();
				$KPI_NAME = $data[KPI_NAME];
				
				$err_text = "รหัสข้อมูลซ้ำ [".$PG_SEQ." - ".$KPI_NAME."]";
			} // end if
		} // end if
	
		if($command=="UPDATE" && $KF_ID && $PG_ID && $PG_SEQ && trim($KPI_NAME)){
			$KPI_NAME = trim($KPI_NAME);
			$cmd = " select PG_SEQ, KPI_NAME from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID and KPI_NAME = '$KPI_NAME' and PG_ID<>$PG_ID ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				if(!$KPI_ID) $KPI_ID = "NULL";
				if(!$KPI_WEIGHT) $KPI_WEIGHT = "NULL";
				if(!$KPI_TARGET_LEVEL1) $KPI_TARGET_LEVEL1 = "NULL";
				if(!$KPI_TARGET_LEVEL2) $KPI_TARGET_LEVEL2 = "NULL";
				if(!$KPI_TARGET_LEVEL3) $KPI_TARGET_LEVEL3 = "NULL";
				if(!$KPI_TARGET_LEVEL4) $KPI_TARGET_LEVEL4 = "NULL";
				if(!$KPI_TARGET_LEVEL5) $KPI_TARGET_LEVEL5 = "NULL";
				if(!$KF_SCORE_FLAG) $KF_SCORE_FLAG = "NULL";
				
				if (!get_magic_quotes_gpc()) {
					$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL1_DESC)));
					$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL2_DESC)));
					$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL3_DESC)));
					$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL4_DESC)));
					$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL5_DESC)));
				}else{
					$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL1_DESC))));
					$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL2_DESC))));
					$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL3_DESC))));
					$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL4_DESC))));
					$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL5_DESC))));
				} // end if

				$cmd = " UPDATE PER_PERFORMANCE_GOALS SET
									PG_SEQ=$PG_SEQ, 
									KPI_ID=$KPI_ID,
									KPI_NAME='$KPI_NAME', 
									KF_TYPE='$KF_TYPE', 
									KPI_WEIGHT=$KPI_WEIGHT,
									KPI_MEASURE='$KPI_MEASURE',
									KPI_PER_ID=$KPI_PER_ID,
									KPI_TARGET_LEVEL1=$KPI_TARGET_LEVEL1,
									KPI_TARGET_LEVEL2=$KPI_TARGET_LEVEL2,
									KPI_TARGET_LEVEL3=$KPI_TARGET_LEVEL3,
									KPI_TARGET_LEVEL4=$KPI_TARGET_LEVEL4,
									KPI_TARGET_LEVEL5=$KPI_TARGET_LEVEL5,
									KPI_TARGET_LEVEL1_DESC='$KPI_TARGET_LEVEL1_DESC',
									KPI_TARGET_LEVEL2_DESC='$KPI_TARGET_LEVEL2_DESC',
									KPI_TARGET_LEVEL3_DESC='$KPI_TARGET_LEVEL3_DESC',
									KPI_TARGET_LEVEL4_DESC='$KPI_TARGET_LEVEL4_DESC',
									KPI_TARGET_LEVEL5_DESC='$KPI_TARGET_LEVEL5_DESC',
									KF_SCORE_FLAG=$KF_SCORE_FLAG,
									KPI_OTHER='$KPI_OTHER', 
									UPDATE_USER=$SESS_USERID, 
									UPDATE_DATE='$UPDATE_DATE'
								WHERE KF_ID=$KF_ID and PG_ID=$PG_ID";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขผลสำเร็จของงานที่คาดหวัง [$PG_ID : $PG_SEQ : $PFR_NAME : $KPI_NAME]");
			}else{
				$data = $db_dpis->get_array();
				$PG_SEQ = $data[PG_SEQ];
				$KPI_NAME = $data[KPI_NAME];
				
				$err_text = "รหัสข้อมูลซ้ำ [".$PG_SEQ." - ".$KPI_NAME."]";
				$UPD = 1;
			} // end if
		} // end if
		
		if($command=="DELETE" && $KF_ID && $PG_ID){
			$cmd = " select KPI_NAME from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID and PG_ID=$PG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$KPI_NAME = $data[KPI_NAME];
			
			$cmd = " delete from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID and PG_ID=$PG_ID ";
			$db_dpis->send_cmd($cmd);
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบผลสำเร็จของงานที่คาดหวัง [$PG_ID : $PG_SEQ : $KPI_NAME]");
		} // end if
	
		// ========================================= re-calculate score_kpi, sum_kpi ==================================//
		$cmd = " select SUM(KPI_WEIGHT) as TOTAL_KPI_WEIGHT from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TOTAL_KPI_WEIGHT = $data[TOTAL_KPI_WEIGHT] + 0;

		$score_kpi = 0;
		$cmd = " select PG_EVALUATE, KPI_WEIGHT from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
		$count_kpi = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){ 
//			$score_kpi += ($data[PG_EVALUATE] + 0);
//			$score_kpi += (($data[PG_EVALUATE] + 0) * $data[KPI_WEIGHT]) / $TOTAL_KPI_WEIGHT;
			$score_kpi += (($data[PG_EVALUATE] + 0) * $data[KPI_WEIGHT]);
		} // end while
//		$sum_kpi = round(round((($score_kpi / ($count_kpi * 5)) * $PERFORMANCE_WEIGHT), 3), 2);
		$sum_kpi = round(round((($score_kpi / ($TOTAL_KPI_WEIGHT * 5)) * $PERFORMANCE_WEIGHT), 3), 2);

		if($KF_ID && $score_kpi && $sum_kpi){
			if($DPISDB=="oci8")
				$cmd = " UPDATE PER_KPI_FORM SET
									SCORE_KPI=$score_kpi, SUM_KPI=$sum_kpi, TOTAL_SCORE=$sum_kpi+nvl(SUM_COMPETENCE,0)+nvl(SUM_OTHER,0)
									WHERE KF_ID=$KF_ID ";
			else
				$cmd = " UPDATE PER_KPI_FORM SET
									SCORE_KPI=$score_kpi, SUM_KPI=$sum_kpi, TOTAL_SCORE=$sum_kpi+SUM_COMPETENCE+SUM_OTHER
									WHERE KF_ID=$KF_ID ";
                    //แก้ไข กรณีอนุญาตให้เห็นคะแนนแล้วจะไม่ให้คำนวนคะแนนอัตโนมัติ 16/02/2018                                                    
                    if($KF_SCORE_STATUS != 1){                                                    
			$db_dpis->send_cmd($cmd);
                    }    
//			$db_dpis->show_error();
		}
		
		// ========================================= re-calculate score_kpi, sum_kpi ==================================//

		if(($UPD && $KF_ID && $PG_ID) || ($VIEW && $KF_ID && $PG_ID)){
			if($DPISDB=="odbc") 
				$cmd = " SELECT 	a.PG_SEQ, a.KPI_ID, b.KPI_NAME as KPI_ORG_NAME, a.KPI_NAME, c.PFR_ID, c.PFR_ID_REF, c.PFR_NAME, a.KPI_WEIGHT, a.KPI_MEASURE, a.KPI_PER_ID,
														a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
														a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, 
														a.KPI_TARGET_LEVEL5_DESC, a.KF_TYPE, a.KF_SCORE_FLAG, a.KPI_OTHER
								   from		(
													PER_PERFORMANCE_GOALS a
													left join PER_KPI b on (a.KPI_ID=b.KPI_ID)
												) left join PER_PERFORMANCE_REVIEW c on (b.PFR_ID=c.PFR_ID)
									WHERE		a.KF_ID=$KF_ID and a.PG_ID=$PG_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " SELECT 	a.PG_SEQ, a.KPI_ID, b.KPI_NAME as KPI_ORG_NAME, a.KPI_NAME, c.PFR_ID, c.PFR_ID_REF, c.PFR_NAME, a.KPI_WEIGHT, a.KPI_MEASURE, a.KPI_PER_ID,
														a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
														a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, 
														a.KPI_TARGET_LEVEL5_DESC, a.KF_TYPE, a.KF_SCORE_FLAG, a.KPI_OTHER
									FROM		PER_PERFORMANCE_GOALS a, PER_KPI b, PER_PERFORMANCE_REVIEW c 
									WHERE		a.KF_ID=$KF_ID and a.PG_ID=$PG_ID and a.KPI_ID=b.KPI_ID(+) and b.PFR_ID=c.PFR_ID(+) ";
			elseif($DPISDB=="mysql")
				$cmd = " SELECT 	a.PG_SEQ, a.KPI_ID, b.KPI_NAME as KPI_ORG_NAME, a.KPI_NAME, c.PFR_ID, c.PFR_ID_REF, c.PFR_NAME, a.KPI_WEIGHT, a.KPI_MEASURE, a.KPI_PER_ID,
														a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
														a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, 
														a.KPI_TARGET_LEVEL5_DESC, a.KF_TYPE, a.KF_SCORE_FLAG, a.KPI_OTHER
								   from		(
													PER_PERFORMANCE_GOALS a
													left join PER_KPI b on (a.KPI_ID=b.KPI_ID)
												) left join PER_PERFORMANCE_REVIEW c on (b.PFR_ID=c.PFR_ID)
									WHERE		a.KF_ID=$KF_ID and a.PG_ID=$PG_ID ";
			//echo "<pre> -> $cmd";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PG_SEQ = $data[PG_SEQ];
			$KPI_ID = $data[KPI_ID];
			$KPI_ORG_NAME = trim($data[KPI_ORG_NAME]);
			$KPI_NAME = trim($data[KPI_NAME]);
			$PFR_ID = $data[PFR_ID];
			$PFR_ID_REF = $data[PFR_ID_REF];
			$PFR_NAME2 = trim($data[PFR_NAME]);
			if($KF_TYPE_RETURN==1 && $_POST[KF_TYPE]) {
				$KF_TYPE = $_POST[KF_TYPE];
			}else{
				$KF_TYPE = $data[KF_TYPE];
			}
			$KF_SCORE_FLAG = $data[KF_SCORE_FLAG];
			$KPI_OTHER = trim($data[KPI_OTHER]);

			$KPI_WEIGHT = $data[KPI_WEIGHT];
			$KPI_MEASURE = $data[KPI_MEASURE];
			$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
			$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
			$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
			$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
			$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];

			$KPI_TARGET_LEVEL1_DESC = stripslashes($data[KPI_TARGET_LEVEL1_DESC]);
			$KPI_TARGET_LEVEL2_DESC = stripslashes($data[KPI_TARGET_LEVEL2_DESC]);
			$KPI_TARGET_LEVEL3_DESC = stripslashes($data[KPI_TARGET_LEVEL3_DESC]);
			$KPI_TARGET_LEVEL4_DESC = stripslashes($data[KPI_TARGET_LEVEL4_DESC]);
			$KPI_TARGET_LEVEL5_DESC = stripslashes($data[KPI_TARGET_LEVEL5_DESC]);

			$KPI_PER_ID = $data[KPI_PER_ID];
			$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$KPI_PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$KPI_PER_NAME = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
			$PN_CODE = $data[PN_CODE];
	
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$KPI_PER_NAME = trim($data[PN_NAME]) . $KPI_PER_NAME;

			if ($PFR_ID!=$PFR_ID_REF) {
				$cmd = " select PFR_ID_REF, PFR_NAME from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID_REF ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$PFR_ID_REF = $data[PFR_ID_REF];
				$PFR_NAME1 = trim($data[PFR_NAME]);
				if ($PFR_ID!=$PFR_ID_REF) {
					$cmd = " select PFR_ID_REF, PFR_NAME from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID_REF ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$PFR_ID_REF = $data[PFR_ID_REF];
					$PFR_NAME = trim($data[PFR_NAME]);
				}
			}
		} // end if
		
		
		 				
		if( !$UPD && !$VIEW && !$err_text || $command=="CANCEL"){
			$PG_ID = "";
			$PG_SEQ = "";
			
			$cmd = " select max(PG_SEQ) as max_seq from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$PG_SEQ = $data[max_seq] + 1;
			
			$PFR_NAME = "";
			$KPI_ID = "";
			$KPI_ORG_NAME = "";
			$KPI_NAME = "";		
			$KPI_OTHER = "";		
			if(!$KF_TYPE)	$KF_TYPE = "1";		
			$KPI_WEIGHT = "";
			$KPI_MEASURE = "";
			$KPI_PER_ID = "";
			$KPI_PER_NAME = "";
			$KPI_TARGET_LEVEL1 = "1";
			$KPI_TARGET_LEVEL2 = "2";
			$KPI_TARGET_LEVEL3 = "3";
			$KPI_TARGET_LEVEL4 = "4";
			$KPI_TARGET_LEVEL5 = "5";
			$KPI_TARGET_LEVEL1_DESC = "";
			$KPI_TARGET_LEVEL2_DESC = "";
			$KPI_TARGET_LEVEL3_DESC = "";
			$KPI_TARGET_LEVEL4_DESC = "";
			$KPI_TARGET_LEVEL5_DESC = "";
                        if($command=="CANCEL"){ // กดยกเลิก เเล้วกลับไปหน้าเดิม
		
								$UPD="";
								$VIEW="";                     
						}
                        
		} // end if
		
		$cmd = " select KF_END_DATE from PER_KPI_FORM where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_END_DATE = substr($data[KF_END_DATE], 0, 10);
		$KF_YEAR = substr($KF_END_DATE, 0, 4) + 543;
	}elseif($SUBPAGE==2){
		$CP_CODE = trim($CP_CODE);
		
		if($command=="ADD" && $KF_ID && $CP_CODE){
			$cmd = " select CP_CODE from PER_KPI_COMPETENCE where KF_ID=$KF_ID and trim(CP_CODE)='$CP_CODE' ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				$cmd = " select max(KC_ID) as max_id from PER_KPI_COMPETENCE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$KC_ID = $data[max_id] + 1;
				
				$cmd = " 	insert into PER_KPI_COMPETENCE
									(KF_ID, KC_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, UPDATE_DATE)
									values
									($KF_ID, $KC_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE')   ";
				$db_dpis->send_cmd($cmd);
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มคุณลักษณะ/สมรรถนะที่คาดหวัง [$KC_ID : $CP_CODE : $CP_NAME]");
			}else{
				$data = $db_dpis->get_array();
				$CP_CODE = trim($data[CP_CODE]);
				
				$cmd = " select CP_NAME from PER_COMPETENCE where trim(CP_CODE)='$CP_CODE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$CP_NAME = $data[CP_NAME];
				
				$err_text = "รหัสข้อมูลซ้ำ [".$CP_CODE." ".$CP_NAME."]";
			} // end if
		} // end if

		if($command=="UPDATE" && $KF_ID && $KC_ID && $CP_CODE){
			$cmd = " select CP_CODE from PER_KPI_COMPETENCE where KF_ID=$KF_ID and trim(CP_CODE)='$CP_CODE' and KC_ID<>$KC_ID ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				$cmd = " UPDATE PER_KPI_COMPETENCE SET
									CP_CODE='$CP_CODE',
									PC_TARGET_LEVEL=$PC_TARGET_LEVEL,
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								WHERE KF_ID=$KF_ID and KC_ID=$KC_ID";
				$db_dpis->send_cmd($cmd);
	//			$db_dpis->show_error();
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขคุณลักษณะ/สมรรถนะที่คาดหวัง [$KC_ID : $CP_CODE : $CP_NAME]");
			}else{
				$data = $db_dpis->get_array();
				$CP_CODE = trim($data[CP_CODE]);
				
				$cmd = " select CP_NAME from PER_COMPETENCE where trim(CP_CODE)='$CP_CODE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$CP_NAME = $data[CP_NAME];
				
				$err_text = "รหัสข้อมูลซ้ำ [".$CP_CODE." ".$CP_NAME."]";
				$UPD = 1;
			} // end if
		} // end if

		if($command=="DELETE" && $KF_ID && $KC_ID){
			$cmd = " select CP_CODE from PER_KPI_COMPETENCE where KF_ID=$KF_ID and KC_ID=$KC_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CP_CODE = trim($data[CP_CODE]);
			
			$cmd = " select CP_NAME from PER_COMPETENCE where trim(CP_CODE)='$CP_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CP_NAME = $data[CP_NAME];
	
			$cmd = " delete from PER_KPI_COMPETENCE where KF_ID=$KF_ID and KC_ID=$KC_ID ";
			$db_dpis->send_cmd($cmd);
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบคุณลักษณะ/สมรรถนะที่คาดหวัง [$KC_ID : $CP_CODE : $CP_NAME]");
		} // end if

		// ========================================= re-calculate score_competence, sum_competence ==================================//
		$TOTAL_PC_SCORE = 0;
		$cmd = " select 	KC_EVALUATE, KC_WEIGHT, PC_TARGET_LEVEL from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
		$count_competence = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			$KC_EVALUATE = $data[KC_EVALUATE];
			$KC_WEIGHT = $data[KC_WEIGHT] + 0;
			$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL] + 0;
			$TOTAL_PC_SCORE += $KC_EVALUATE * $KC_WEIGHT / 100;
			$SUM_EVALUATE += $KC_EVALUATE;
			$SUM_TARGET_LEVEL += $PC_TARGET_LEVEL;
				
			if($KC_EVALUATE != ""){
				if($KC_EVALUATE >= $PC_TARGET_LEVEL) $tmp_arr_count[GE] += 1;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 1) $tmp_arr_count[L1] += 1;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 2) $tmp_arr_count[L2] += 1;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 3) $tmp_arr_count[L3] += 1;
				else $tmp_arr_count[L3] += 1;
			} // end if
		} // end while
		
		$arr_score[GE] = $arr_count[GE] * 3;
		$arr_score[L1] = $arr_count[L1] * 2;
		$arr_score[L2] = $arr_count[L2] * 1;
		$arr_score[L3] = $arr_count[L3] * 0;
		
		if ($COMPETENCY_SCALE==1) {
			$score_competence = array_sum($tmp_arr_score);	
			$sum_competence = round(round((($score_competence / ($count_competence * 3)) * $COMPETENCE_WEIGHT), 3), 2);
		} elseif ($COMPETENCY_SCALE==2) { 		
			$score_competence = $TOTAL_PC_SCORE;	
			$sum_competence = round(round((($score_competence / 5) * $COMPETENCE_WEIGHT), 3), 2);
		} elseif ($COMPETENCY_SCALE==3) { 		
			$score_competence = $SUM_EVALUATE;	
			$sum_competence = round(round((($score_competence / $SUM_TARGET_LEVEL) * $COMPETENCE_WEIGHT), 3), 2);
		}
		
		if($KF_ID && $score_competence && $sum_competence){
                    //เพิ่มเติ่ม กรณีที่อนุญาตให้เห็นคะแนนแล้ว จะไม่ให้ทำการคำนวณใหม่  KF_SCORE_STATUS==1 
                    //ปรับแก้ให้คำนวณแต่รายการที่ ยังไม่อนุญาตให้เห็นคะแนน KF_SCORE_STATUS!=1
			if($DPISDB=="oci8")
				/*$cmd = " UPDATE PER_KPI_FORM SET
									SCORE_COMPETENCE=$score_competence, SUM_COMPETENCE=$sum_competence, TOTAL_SCORE=nvl(SUM_KPI,0)+nvl(SUM_OTHER,0)+$sum_competence
									WHERE KF_ID=$KF_ID ";*/
                            /*ปรับแก้ ณวันที 9 มีนา 2561 */
                            $cmd = " UPDATE PER_KPI_FORM 
                                    SET SCORE_COMPETENCE=$score_competence, 
                                        SUM_COMPETENCE=$sum_competence, 
                                        TOTAL_SCORE=nvl(SUM_KPI,0)+nvl(SUM_OTHER,0)+$sum_competence
                                    WHERE NVL(KF_SCORE_STATUS,0)!=1 AND KF_ID=$KF_ID ";
			else
				$cmd = " UPDATE PER_KPI_FORM SET
									SCORE_COMPETENCE=$score_competence, SUM_COMPETENCE=$sum_competence, TOTAL_SCORE=SUM_KPI+SUM_OTHER+$sum_competence
									WHERE KF_ID=$KF_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}
		// ========================================= re-calculate score_competence, sum_competence ==================================//		
		
		if(($UPD && $KF_ID && $KC_ID) || ($VIEW && $KF_ID && $KC_ID)){
			$cmd = "	SELECT 	a.CP_CODE, b.CP_NAME, a.PC_TARGET_LEVEL
								FROM		PER_KPI_COMPETENCE a, PER_COMPETENCE b
								WHERE		a.KF_ID=$KF_ID and a.KC_ID=$KC_ID and a.CP_CODE=b.CP_CODE(+) ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CP_CODE = $data[CP_CODE];
			$CP_NAME = $data[CP_NAME];
			$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
		} // end if
		
		$cmd = " select PER_TYPE, POS_ID, POEM_ID, POEMS_ID from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE==1) $POS_ID = $data[POS_ID];
		elseif($PER_TYPE==2) $POS_ID = $data[POEM_ID];
		elseif($PER_TYPE==3) $POS_ID = $data[POEMS_ID];
		
		if( !$UPD && !$VIEW && !$err_text){
			$KC_ID = "";
			$CP_CODE = "";
			$CP_NAME = "";		
			$PC_TARGET_LEVEL = "";
		} // end if
	} // end if
?>