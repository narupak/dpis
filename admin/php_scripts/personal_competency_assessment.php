<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$PER_ID_DEPARTMENT_ID = $DEPARTMENT_ID;
	
	if(!$search_kf_year)	$search_kf_year = $KPI_BUDGET_YEAR;
	if(!isset($search_kf_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	
	if($command == "UPDATE_SCORE"){
		foreach($COMPETENCE_WEIGHT as $update_id => $value) {
                    //เพิ่มเติ่ม กรณีที่อนุญาตให้เห็นคะแนนแล้ว จะไม่ให้ทำการคำนวณใหม่  KF_SCORE_STATUS==1 
                    //ปรับแก้ให้คำนวณแต่รายการที่ ยังไม่อนุญาตให้เห็นคะแนน KF_SCORE_STATUS!=1
			/*if($SCOMPETENCY[$PER_ID_DEPARTMENT_ID]=="Y"){
				$cmd = " update PER_KPI_FORM set 
								CHIEF_PER_ID = '".$CHIEF_PER_ID[$update_id]."',
								FRIEND_FLAG = '".$_friend_flag[$update_id]."',
								PERFORMANCE_WEIGHT = '".$PERFORMANCE_WEIGHT[$update_id]."',
								COMPETENCE_WEIGHT = '".$COMPETENCE_WEIGHT[$update_id]."',
								OTHER_WEIGHT = '".$OTHER_WEIGHT[$update_id]."',
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where KF_ID=$update_id ";
			}else{
				$cmd = " update PER_KPI_FORM set 
								PERFORMANCE_WEIGHT = '".$PERFORMANCE_WEIGHT[$update_id]."',
								COMPETENCE_WEIGHT = '".$COMPETENCE_WEIGHT[$update_id]."',
								OTHER_WEIGHT = '".$OTHER_WEIGHT[$update_id]."',
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where KF_ID=$update_id ";			
			}*/
                        /*ปรับแก้ ณวันที 9 มีนา 2561 */
                    if($SCOMPETENCY[$PER_ID_DEPARTMENT_ID]=="Y"){
				$cmd = " update PER_KPI_FORM set 
								CHIEF_PER_ID = '".$CHIEF_PER_ID[$update_id]."',
								FRIEND_FLAG = '".$_friend_flag[$update_id]."',
								PERFORMANCE_WEIGHT = '".$PERFORMANCE_WEIGHT[$update_id]."',
								COMPETENCE_WEIGHT = '".$COMPETENCE_WEIGHT[$update_id]."',
								OTHER_WEIGHT = '".$OTHER_WEIGHT[$update_id]."',
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where NVL(KF_SCORE_STATUS,0)!=1 AND KF_ID=$update_id ";
			}else{
				$cmd = " update PER_KPI_FORM set 
								PERFORMANCE_WEIGHT = '".$PERFORMANCE_WEIGHT[$update_id]."',
								COMPETENCE_WEIGHT = '".$COMPETENCE_WEIGHT[$update_id]."',
								OTHER_WEIGHT = '".$OTHER_WEIGHT[$update_id]."',
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where NVL(KF_SCORE_STATUS,0)!=1 AND KF_ID=$update_id ";			
			}

			$db_dpis->send_cmd($cmd);
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขสังกัด [".$search_kf_year." : ".$search_kf_cycle."]");
			
			/*Release 5.2.1.13*/
			$cmd = " select 	RESULT_COMMENT, COMPETENCE_COMMENT, SALARY_RESULT, SALARY_REMARK1, SALARY_REMARK2, 
						PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1,PER_ID_REVIEW2, AGREE_REVIEW1, DIFF_REVIEW1, AGREE_REVIEW2, DIFF_REVIEW2, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, KF_STATUS, LEVEL_NO, KF_SCORE_STATUS,OTH_DESC,SCORE_OTHER
			   from 		PER_KPI_FORM 
			   where 	KF_ID=$update_id ";
					/*Release 5.1.0.6*/
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();			
				$PERFORMANCE_WEIGHT2 = $data[PERFORMANCE_WEIGHT];
				$COMPETENCE_WEIGHT2 = $data[COMPETENCE_WEIGHT];
				$OTHER_WEIGHT2      = $data[OTHER_WEIGHT];
				$COMPETENCE_COMMENT = trim($data[COMPETENCE_COMMENT]);
				$KF_SCORE_STATUS    = $data[KF_SCORE_STATUS];
				$SCORE_OTHER        = $data[SCORE_OTHER];
                $LEVEL_NO           = $data[LEVEL_NO];
			//echo 	"<pre>".$cmd;
	
			//echo $PERFORMANCE_WEIGHT[$update_id]." != ".$PERFORMANCE_WEIGHT_HI[$update_id]."<br>";
		if($KF_SCORE_STATUS == 0 && ($PERFORMANCE_WEIGHT[$update_id] != $PERFORMANCE_WEIGHT_HID[$update_id] 
		   || $COMPETENCE_WEIGHT[$update_id] != $COMPETENCE_WEIGHT_HID[$update_id] || $OTHER_WEIGHT[$update_id] != $OTHER_WEIGHT_HID[$update_id])){
		
		
			$cmd = " select SUM(KPI_WEIGHT) as TOTAL_KPI_WEIGHT from PER_PERFORMANCE_GOALS where KF_ID=$update_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TOTAL_KPI_WEIGHT = $data[TOTAL_KPI_WEIGHT] + 0;
            
		$score_kpi = 0;
		$cmd = " select PG_EVALUATE, KPI_WEIGHT from PER_PERFORMANCE_GOALS where KF_ID=$update_id ";
		$count_kpi = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){ 
	    $score_kpi += (($data[PG_EVALUATE] + 0) * $data[KPI_WEIGHT]);
		} // end while
		if($OTHER_WEIGHT[$update_id] != ""){
		    $SUM_OTHER = $SCORE_OTHER * $OTHER_WEIGHT2;
		 }else{
			$SUM_OTHER = 0;
		}
		$sum_kpi = round(round((($score_kpi / ($TOTAL_KPI_WEIGHT * 5)) * $PERFORMANCE_WEIGHT2), 3), 2);
                //เพิ่มเติ่ม กรณีที่อนุญาตให้เห็นคะแนนแล้ว จะไม่ให้ทำการคำนวณใหม่  KF_SCORE_STATUS==1 
                //ปรับแก้ให้คำนวณแต่รายการที่ ยังไม่อนุญาตให้เห็นคะแนน KF_SCORE_STATUS!=1

				/*$cmd = " UPDATE PER_KPI_FORM SET
									SCORE_KPI=$score_kpi, 
									SUM_KPI=$sum_kpi, 
									SUM_OTHER = $SUM_OTHER,
									TOTAL_SCORE=$sum_kpi+nvl(SUM_COMPETENCE,0)+nvl(SUM_OTHER,0)
									WHERE KF_ID=$update_id ";*/
                /*ปรับแก้ ณวันที 9 มีนา 2561 */
// ========================================= re-calculate score_competence, sum_competence ==================================//
                $TOTAL_PC_SCORE = 0;
				$FULL_SCORE = 5;
				$cmd = " select 	KC_EVALUATE, KC_WEIGHT, PC_TARGET_LEVEL, KC_REMARK, KC_SELF from PER_KPI_COMPETENCE where KF_ID=$update_id ";
                                if($debug==1){echo __LINE__.'<pre>'.$cmd;}
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
					$sum_competence_total = round(round((($score_competence / ($count_competence * 3)) * $COMPETENCE_WEIGHT2), 3), 2);
				} elseif ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5 || $COMPETENCY_SCALE==6) { 		
					$score_competence = $TOTAL_PC_SCORE;	
					$sum_competence_total = round(round((($score_competence / $FULL_SCORE) * $COMPETENCE_WEIGHT2), 3), 2);
				} elseif ($COMPETENCY_SCALE==3) { 		
					$score_competence = $SUM_EVALUATE;	
					$sum_competence_total = round(round((($score_competence / $SUM_TARGET_LEVEL) * $COMPETENCE_WEIGHT2), 3), 2);
				} 
//echo $sum_competence;
            
// ========================================= re-calculate score_competence, sum_competence ==================================//            
            
                $cmd = " UPDATE PER_KPI_FORM 
                        SET SCORE_KPI=$score_kpi, 
                            SUM_KPI=$sum_kpi,
                            SUM_COMPETENCE = $sum_competence_total,
                            SUM_OTHER = $SUM_OTHER, 
                            TOTAL_SCORE=$sum_kpi+nvl(SUM_COMPETENCE,0)+nvl(SUM_OTHER,0)
                        WHERE NVL(KF_SCORE_STATUS,0)!=1 AND  KF_ID=$update_id ";
			
			$db_dpis->send_cmd($cmd);
            
			$cmd = " select SUM_KPI, SUM_COMPETENCE from PER_KPI_FORM where KF_ID=$update_id ";
			$db_dpis->send_cmd($cmd);
            //echo "<pre>".$cmd;	 
			$data = $db_dpis->get_array();
			$SUM_KPI = $data[SUM_KPI];
			$SUM_COMPETENCE = $data[SUM_COMPETENCE];
			$SUM_OTHER = $SCORE_OTHER * $OTHER_WEIGHT2;
			$SUM_TOTAL = $SUM_KPI + $SUM_COMPETENCE + $SUM_OTHER;
                        if(empty($SCORE_OTHER)){$SCORE_OTHER='NULL';}/*เพิ่มใหม่ ปรับแก้ส่วนของ 3.อื่น ๆ	%	  ส่วนที่ 3 */
			/*$cmd = " UPDATE PER_KPI_FORM SET
								SCORE_OTHER=$SCORE_OTHER,
								SUM_OTHER=$SUM_OTHER,
								TOTAL_SCORE=$SUM_TOTAL,
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							WHERE KF_ID=$update_id ";*/
                        /*ปรับแก้ ณวันที 9 มีนา 2561 */
                        $cmd = " UPDATE PER_KPI_FORM 
                                SET SCORE_OTHER=$SCORE_OTHER,
                                    SUM_OTHER=$SUM_OTHER,
                                    TOTAL_SCORE=$SUM_TOTAL,
                                    UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
                                WHERE NVL(KF_SCORE_STATUS,0)!=1 AND KF_ID=$update_id ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
            //echo "<pre>".$cmd;	 

			
		/*End Release 5.2.1.13*/
	    }
		
//			$db_dpis->show_error();
//			echo $cmd . "<br>";
			
		}
	}
	
?>