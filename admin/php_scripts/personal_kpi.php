<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
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
	
	if(!$search_kf_year)	$search_kf_year = $KPI_BUDGET_YEAR;
	if(!isset($search_kf_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if
	$debug=0;/*0=close,1=open*/
        
        
	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if(!$WEIGHT_OTHER) $WEIGHT_OTHER = "NULL";

	if($command == "UPDATE_SCORE"){
		foreach($TOTAL_SCORE as $update_id => $update_point) {
                        $cmd = " select TOTAL_SCORE, SALARY_FLAG, SALARY_REMARK1
                            from PER_KPI_FORM 
                            where KF_ID = $update_id ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TOTAL_SCORE_SET = $data[TOTAL_SCORE];
			$SALARY_FLAG = $data[SALARY_FLAG];
			$SALARY_REMARK1 = $data[SALARY_REMARK1];

			if ($TOTAL_SCORE_SET != $update_point || $SALARY_FLAG != $_salary_flag[$update_id] || $SALARY_REMARK1 != $salary_remark[$update_id]) {
				$cmd = " update PER_KPI_FORM set 
								TOTAL_SCORE = '$update_point',
								SALARY_FLAG = '".$_salary_flag[$update_id]."',
								SALARY_REMARK1='$salary_remark[$update_id]',
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where KF_ID=$update_id ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขคะแนน [".$KF_ID." : ".trim($KF_YEAR)." : ".$KF_CYCLE." : ".$PER_NAME."]");
			}
		}
	}
	
	if($command == "DELETE" && trim($KF_ID)){
		$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID ,KF_SCORE_STATUS
				  		 from 		PER_KPI_FORM 
						 where 	KF_ID = $KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_END_DATE = substr($data[KF_END_DATE], 0, 10);
		$KF_YEAR = substr($KF_END_DATE, 0, 4);
		$KF_CYCLE = $data[KF_CYCLE];
		$PER_ID = $data[PER_ID];
                $KF_SCORE_STATUS = $data[KF_SCORE_STATUS];
		
                if($KF_SCORE_STATUS!=1){ //จะลบได้กรณีท ี่ ยังไม่อนุญาตให้เห็นคะแนน
                    $cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    $PN_CODE = $data[PN_CODE];
                    $PER_NAME = $data[PER_NAME];
                    $PER_SURNAME = $data[PER_SURNAME];

                    $cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    $PN_NAME = $data[PN_NAME];

                    $PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;

                    $cmd = " delete from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
                    $db_dpis->send_cmd($cmd);
    //		$db_dpis->show_error();

                    $cmd = " delete from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
                    $db_dpis->send_cmd($cmd);
    //		$db_dpis->show_error();

                    $cmd = " delete from PER_IPIP where KF_ID=$KF_ID ";
                    $db_dpis->send_cmd($cmd);
    //		$db_dpis->show_error();

                    $cmd = " delete from PER_KPI_FORM where KF_ID=$KF_ID ";
                    $db_dpis->send_cmd($cmd);
    //		$db_dpis->show_error();

                    insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [". $KF_ID ." : ".$KF_YEAR." ".$KF_CYCLE." ".$PER_FULLNAME."]");
                }
		
	}
	
	if($command == 'COPY_ALL' || $command == 'COPY_TEST') {
		if($search_kf_cycle == 1){
			$KF_START_DATE = ($search_kf_year-544) . "-10-01";
			$KF_END_DATE = ($search_kf_year-543) . "-03-31";
		}elseif($search_kf_cycle == 2){
			$KF_START_DATE = ($search_kf_year-543) . "-04-01";
			$KF_END_DATE = ($search_kf_year-543) . "-09-30";
		} // end if
	
		if($search_org_id){
			if($select_org_structure==0){
				$search_con = "and b.ORG_ID = $search_org_id ";
			}else if($select_org_structure==1){
				$search_con = " and a.ORG_ID = $search_org_id ";
			}
		}elseif($search_department_id){
			$search_con = "and a.DEPARTMENT_ID = $search_department_id ";
		}elseif($search_ministry_id){
			$search_con = "and a.DEPARTMENT_ID in (select ORG_ID from PER_ORG where ORG_ID_REF = $search_ministry_id) ";
		}
		$search_con .= " and a.PER_STARTDATE <= '$KF_END_DATE' ";
		$cmd = " select max(KF_ID) as MAX_ID from PER_KPI_FORM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_ID = $data[MAX_ID] + 1;

		$cmd = " select max(KC_ID) as max_id from PER_KPI_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KC_ID = $data[max_id] + 1;
	
		if($search_per_type==1 || $search_per_type==5) 
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")
				$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POS_ID, a.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, 
									a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY 
									from PER_PERSONAL a, PER_POSITION b 
									where a.PAY_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
			else
				$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POS_ID, a.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, 
									a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY  
									from PER_PERSONAL a, PER_POSITION b 
									where a.POS_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==2) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEM_ID as POS_ID, a.DEPARTMENT_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY  
								from PER_PERSONAL a, PER_POS_EMP b 
								where a.POEM_ID = b.POEM_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==3) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEMS_ID as POS_ID, a.DEPARTMENT_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY  
								from PER_PERSONAL a, PER_POS_EMPSER b 
								where a.POEMS_ID = b.POEMS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==4) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POT_ID as POS_ID, a.DEPARTMENT_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY  
								from PER_PERSONAL a, PER_POS_TEMP b 
								where a.POT_ID = b.POT_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		 if($debug==1){ echo __LINE__.'<pre>'.$cmd;}
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
                        //echo $PER_ID.",";
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_ID_REF = $data[PER_ID_REF];
			$PER_ID_ASS_REF = $data[PER_ID_ASS_REF];
			$FRIEND_FLAG = "N";
			if ($PER_ID_REF) $FRIEND_FLAG = "Y";
			if (!$PER_ID_REF) $PER_ID_REF = "NULL";
			$POS_ID = $data[POS_ID];
			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$ORG_ID = $data[ORG_ID];
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_ASS = $data[ORG_ID_ASS];
			$ORG_ID_1_ASS = $data[ORG_ID_1_ASS];
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_ASS) $ORG_ID_ASS = "NULL";
			if (!$ORG_ID_1_ASS) $ORG_ID_1_ASS = "NULL";
			$OT_CODE = trim($data[OT_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_PROBATION_FLAG = trim($data[PER_PROBATION_FLAG]);
			$PER_SALARY = $data[PER_SALARY];
			if ($BKK_FLAG==1) $TOTAL_SCORE = rand(90,100);
			else $TOTAL_SCORE = rand(60,100);
			$cmd = " SELECT KF_ID, TOTAL_SCORE, KPI_FLAG, ORG_ID_ASS, ORG_ID_1_ASS,KF_SCORE_STATUS  
                                FROM PER_KPI_FORM 
                                WHERE PER_ID = $PER_ID AND KF_CYCLE = $search_kf_cycle AND 
                                KF_START_DATE = '$KF_START_DATE' AND KF_END_DATE = '$KF_END_DATE' ";
			$count_data = $db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			//echo $cmd."<br/>";
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd;}
			if ($count_data) {
				$data1 = $db_dpis1->get_array();
				$UPD_KF_ID = $data1[KF_ID];
				$old_TOTAL_SCORE = $data1[TOTAL_SCORE];
				$KPI_FLAG = $data1[KPI_FLAG];
				$old_ORG_ID_ASS = $data1[ORG_ID_ASS];
				$old_ORG_ID_1_ASS = $data1[ORG_ID_1_ASS];
                                $KF_SCORE_STATUS = $data1[KF_SCORE_STATUS];
                                //เพิ่มเติ่ม กรณีที่อนุญาตให้เห็นคะแนนแล้? จะไม่ให้ทำการคำนวณใหม่  KF_SCORE_STATUS==1 
                                //ปรับแก้ให้คำนวณแต่รายการที? ยังไม่อนุญาตให้เห็นคะแนน KF_SCORE_STATUS!=1
                                //if($KF_SCORE_STATUS!=1){
                                    if ($old_TOTAL_SCORE==0 && $KPI_FLAG=="Y" && $command == 'COPY_TEST') {
					$cmd = " UPDATE PER_KPI_FORM SET TOTAL_SCORE = $TOTAL_SCORE WHERE KF_ID = $UPD_KF_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error(); 
                                    }
                                    if (!$old_ORG_ID_ASS && $ORG_ID_ASS != "NULL") {
                                            $cmd = " UPDATE PER_KPI_FORM SET ORG_ID_ASS = $ORG_ID_ASS WHERE KF_ID = $UPD_KF_ID ";
                                            $db_dpis1->send_cmd($cmd);
                                            //$db_dpis1->show_error();
                                    }
                                    if (!$old_ORG_ID_1_ASS && $ORG_ID_1_ASS != "NULL") {
                                            $cmd = " UPDATE PER_KPI_FORM SET ORG_ID_1_ASS = $ORG_ID_1_ASS WHERE KF_ID = $UPD_KF_ID ";
                                            $db_dpis1->send_cmd($cmd);
                                            //$db_dpis1->show_error();
                                    }
                                //}
			} else {
				if ($command == 'COPY_ALL') $TOTAL_SCORE = 0;
				if ($PER_PROBATION_FLAG==1){
					$PERFORMANCE_WEIGHT = $WEIGHT_PROBATION;
					$COMPETENCE_WEIGHT = $WEIGHT_PROBATION;
					$OTHER_WEIGHT = "NULL";
				}elseif ($OT_CODE=="08"){
					$PERFORMANCE_WEIGHT = $WEIGHT_KPI_E;
					$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_E;
					$OTHER_WEIGHT = "NULL";
				}elseif ($OT_CODE=="09"){
					$PERFORMANCE_WEIGHT = $WEIGHT_KPI_S;
					$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_S;
					$OTHER_WEIGHT = "NULL";
				}else{
					$PERFORMANCE_WEIGHT = $WEIGHT_KPI;
					$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE;
					$OTHER_WEIGHT = $WEIGHT_OTHER;
				}
				$cmd = " insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
								PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, PER_ID_REVIEW2, DEPARTMENT_ID, UPDATE_USER, 
								UPDATE_DATE, ORG_ID, TOTAL_SCORE, SALARY_FLAG, ORG_ID_SALARY, KPI_FLAG, ORG_ID_KPI, CHIEF_PER_ID, 
								FRIEND_FLAG, ORG_ID_1_SALARY, ORG_ID_ASS, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, 
								ORG_ID_1_ASS, LEVEL_NO, PER_SALARY) 
								values ($KF_ID, $PER_ID, '$PER_CARDNO', $search_kf_cycle, '$KF_START_DATE', '$KF_END_DATE', 	
								$PER_ID_REF, 	$PER_ID_REF, $PER_ID_REF, $PER_ID_REF, $TMP_DEPARTMENT_ID, $SESS_USERID, 
								'$UPDATE_DATE', $ORG_ID, $TOTAL_SCORE, 'Y', $ORG_ID, 'Y', $ORG_ID, $PER_ID_REF, '$FRIEND_FLAG', 
								$ORG_ID_1, $ORG_ID_ASS, $PERFORMANCE_WEIGHT, $COMPETENCE_WEIGHT, $OTHER_WEIGHT, 
								$ORG_ID_1_ASS, '$LEVEL_NO', $PER_SALARY) ";
				$db_dpis1->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd;}
//				$db_dpis1->show_error();
//				echo "-> $cmd <br>";  
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมู? [".$KF_ID." : ".trim($search_kf_year)." : ".$search_kf_cycle." : ".$PER_CARDNO."]");

				// ==================== insert competence from kpi_position_competence  ==================== //
				if($search_per_type==1 || $search_per_type==5) {
					$cmd = " select		CP_CODE, PC_TARGET_LEVEL
									 from		PER_POSITION_COMPETENCE
									 where	POS_ID=$POS_ID and PC_TARGET_LEVEL > 0 and PER_TYPE=1
									 order by CP_CODE ";
					$db_dpis1->send_cmd($cmd);
//					$db_dpis1->show_error();
					while($data1 = $db_dpis1->get_array()){
						$CP_CODE = $data1[CP_CODE];
						$PC_TARGET_LEVEL = $data1[PC_TARGET_LEVEL];
				
						$cmd = " insert into PER_KPI_COMPETENCE (KC_ID, KF_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, 
											UPDATE_DATE) 
											values ($KC_ID, $KF_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$KC_ID++;
					} // end while
				} // endif($search_per_type==1) 
				$KF_ID++;
				// ==================== insert competence from kpi_position_competence  ==================== //
			}
		}
	}

	if($command == 'DELETE_TEST') {
		if($search_kf_cycle == 1){
			$KF_START_DATE = ($search_kf_year-544) . "-10-01";
			$KF_END_DATE = ($search_kf_year-543) . "-03-31";
		}elseif($search_kf_cycle == 2){
			$KF_START_DATE = ($search_kf_year-543) . "-04-01";
			$KF_END_DATE = ($search_kf_year-543) . "-09-30";
		} // end if
	
		if($search_org_id){
			if($select_org_structure==0){
				$search_con = "and b.ORG_ID = $search_org_id ";
			}else if($select_org_structure==1){
				$search_con = "and a.ORG_ID = $search_org_id ";
			}
		}elseif($search_department_id){
			$search_con = "and a.DEPARTMENT_ID = $search_department_id ";
		}elseif($search_ministry_id){
			$search_con = "and a.DEPARTMENT_ID in (select ORG_ID from PER_ORG where ORG_ID_REF = $search_ministry_id) ";
		}
		if($search_per_type==1 || $search_per_type==5) 
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")
				$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POS_ID, b.ORG_ID, b.ORG_ID_1, 
									a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS 
									from PER_PERSONAL a, PER_POSITION b where a.PAY_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
			else
				$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POS_ID, b.ORG_ID, b.ORG_ID_1, 
									a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS 
									from PER_PERSONAL a, PER_POSITION b where a.POS_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==2) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEM_ID as POS_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS 
								from PER_PERSONAL a, PER_POS_EMP b where a.POEM_ID = b.POEM_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==3) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEMS_ID as POS_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS 
								from PER_PERSONAL a, PER_POS_EMPSER b where a.POEMS_ID = b.POEMS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==4) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POT_ID as POS_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS 
								from PER_PERSONAL a, PER_POS_TEMP b where a.POT_ID = b.POT_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
                        
			$cmd = " UPDATE PER_KPI_FORM SET TOTAL_SCORE = 0 WHERE PER_ID = $PER_ID AND KF_CYCLE = $search_kf_cycle AND 
							  KF_START_DATE = '$KF_START_DATE' AND KF_END_DATE = '$KF_END_DATE' ";
                        
			$db_dpis2->send_cmd($cmd);
			$data = $db_dpis2->get_array();
		}
	}

	if($command == 'COPY_ORG_ASS') {
		if($search_kf_cycle == 1){
			$KF_START_DATE = ($search_kf_year-544) . "-10-01";
			$KF_END_DATE = ($search_kf_year-543) . "-03-31";
		}elseif($search_kf_cycle == 2){
			$KF_START_DATE = ($search_kf_year-543) . "-04-01";
			$KF_END_DATE = ($search_kf_year-543) . "-09-30";
		} // end if
	
		if($search_org_id){
			if($select_org_structure==0){
				$search_con = "and b.ORG_ID = $search_org_id ";
			}else if($select_org_structure==1){
				$search_con = "and a.ORG_ID = $search_org_id ";
			}
		}elseif($search_department_id){
			$search_con = "and a.DEPARTMENT_ID = $search_department_id ";
		}elseif($search_ministry_id){
			$search_con = "and a.DEPARTMENT_ID in (select ORG_ID from PER_ORG where ORG_ID_REF = $search_ministry_id) ";
		}
		$cmd = " select max(KF_ID) as MAX_ID from PER_KPI_FORM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_ID = $data[MAX_ID] + 1;

		$cmd = " select max(KC_ID) as max_id from PER_KPI_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KC_ID = $data[max_id] + 1;
	
		if($search_per_type==1 || $search_per_type==5) 
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")
				$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POS_ID, a.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, 
									a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY 
									from PER_PERSONAL a, PER_POSITION b where a.PAY_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
			else
				$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POS_ID, a.DEPARTMENT_ID, b.ORG_ID, b.ORG_ID_1, 
									a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY  
									from PER_PERSONAL a, PER_POSITION b where a.POS_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==2) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEM_ID as POS_ID, a.DEPARTMENT_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY  
								from PER_PERSONAL a, PER_POS_EMP b where a.POEM_ID = b.POEM_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==3) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEMS_ID as POS_ID, a.DEPARTMENT_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY  
								from PER_PERSONAL a, PER_POS_EMPSER b where a.POEMS_ID = b.POEMS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==4) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POT_ID as POS_ID, a.DEPARTMENT_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS, a.ORG_ID_1 as ORG_ID_1_ASS, a.OT_CODE, a.LEVEL_NO, a.PER_PROBATION_FLAG, PER_SALARY  
								from PER_PERSONAL a, PER_POS_TEMP b where a.POT_ID = b.POT_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_ID_REF = $data[PER_ID_REF];
			$PER_ID_ASS_REF = $data[PER_ID_ASS_REF];
			$FRIEND_FLAG = "N";
			if ($PER_ID_REF) $FRIEND_FLAG = "Y";
			if (!$PER_ID_REF) $PER_ID_REF = "NULL";
			$POS_ID = $data[POS_ID];
			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$ORG_ID = $data[ORG_ID];
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_ASS = $data[ORG_ID_ASS];
			$ORG_ID_1_ASS = $data[ORG_ID_1_ASS];
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_ASS) $ORG_ID_ASS = "NULL";
			if (!$ORG_ID_1_ASS) $ORG_ID_1_ASS = "NULL";
			$OT_CODE = trim($data[OT_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_PROBATION_FLAG = trim($data[PER_PROBATION_FLAG]);
			$PER_SALARY = $data[PER_SALARY];
			$cmd = " SELECT KF_ID, TOTAL_SCORE, KPI_FLAG, ORG_ID_ASS, ORG_ID_1_ASS FROM PER_KPI_FORM 
							  WHERE PER_ID = $PER_ID AND KF_CYCLE = $search_kf_cycle AND 
							  KF_START_DATE = '$KF_START_DATE' AND KF_END_DATE = '$KF_END_DATE' ";
			$count_data = $db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			if ($count_data) {
				$data1 = $db_dpis1->get_array();
				$UPD_KF_ID = $data1[KF_ID];
				$cmd = " UPDATE PER_KPI_FORM SET ORG_ID_ASS = $ORG_ID_ASS, ORG_ID_1_ASS = $ORG_ID_1_ASS WHERE KF_ID = $UPD_KF_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} else {
				$TOTAL_SCORE = 0;
				if ($PER_PROBATION_FLAG==1){
					$PERFORMANCE_WEIGHT = $WEIGHT_PROBATION;
					$COMPETENCE_WEIGHT = $WEIGHT_PROBATION;
					$OTHER_WEIGHT = "NULL";
				}elseif ($OT_CODE=="08"){
					$PERFORMANCE_WEIGHT = $WEIGHT_KPI_E;
					$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_E;
					$OTHER_WEIGHT = "NULL";
				}elseif ($OT_CODE=="09"){
					$PERFORMANCE_WEIGHT = $WEIGHT_KPI_S;
					$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_S;
					$OTHER_WEIGHT = "NULL";
				}else{
					$PERFORMANCE_WEIGHT = $WEIGHT_KPI;
					$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE;
					$OTHER_WEIGHT = $WEIGHT_OTHER;
				}
				$cmd = " insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
								PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, PER_ID_REVIEW2, DEPARTMENT_ID, UPDATE_USER, 
								UPDATE_DATE, ORG_ID, TOTAL_SCORE, SALARY_FLAG, ORG_ID_SALARY, KPI_FLAG, ORG_ID_KPI, CHIEF_PER_ID, 
								FRIEND_FLAG, ORG_ID_1_SALARY, ORG_ID_ASS, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, 
								ORG_ID_1_ASS, LEVEL_NO, PER_SALARY) 
								values ($KF_ID, $PER_ID, '$PER_CARDNO', $search_kf_cycle, '$KF_START_DATE', '$KF_END_DATE', 	
								$PER_ID_REF, 	$PER_ID_REF, $PER_ID_REF, $PER_ID_REF, $TMP_DEPARTMENT_ID, $SESS_USERID, 
								'$UPDATE_DATE', $ORG_ID, $TOTAL_SCORE, 'Y', $ORG_ID, 'Y', $ORG_ID, $PER_ID_REF, '$FRIEND_FLAG', 
								$ORG_ID_1, $ORG_ID_ASS, $PERFORMANCE_WEIGHT, $COMPETENCE_WEIGHT, $OTHER_WEIGHT, 
								$ORG_ID_1_ASS, '$LEVEL_NO', $PER_SALARY) ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมู? [".$KF_ID." : ".trim($search_kf_year)." : ".$search_kf_cycle." : ".$PER_CARDNO."]");

				// ==================== insert competence from kpi_position_competence  ==================== //
				if($search_per_type==1 || $search_per_type==5) {
					$cmd = " select		CP_CODE, PC_TARGET_LEVEL
									 from		PER_POSITION_COMPETENCE
									 where	POS_ID=$POS_ID and PC_TARGET_LEVEL > 0 and PER_TYPE=1
									 order by CP_CODE ";
					$db_dpis1->send_cmd($cmd);
//					$db_dpis1->show_error();
					while($data1 = $db_dpis1->get_array()){
						$CP_CODE = $data1[CP_CODE];
						$PC_TARGET_LEVEL = $data1[PC_TARGET_LEVEL];
				
						$cmd = " insert into PER_KPI_COMPETENCE (KC_ID, KF_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, 
											UPDATE_DATE) 
											values ($KC_ID, $KF_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$KC_ID++;
					} // end while
				} // endif($search_per_type==1) 
				$KF_ID++;
				// ==================== insert competence from kpi_position_competence  ==================== //
			}
		}
	}

?>