<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	
	
	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
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
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
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

	if(!isset($search_cp_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_cp_year = date("Y") + 543;
		else $search_cp_year = (date("Y") + 543) + 1;
	} // end if
	
	if(!isset($search_cp_cycle)){
		$search_cp_cycle = array(1, 2);
	} // end if
	
	$_select_level_no_str = implode("','",$_select_level_no);
	
	$where = ""; 
	if($search_org_id && $search_org_id != 'NULL') $where = " and b.org_id_salary = $search_org_id ";
	if($search_org_id_1 && $search_org_id_1 != 'NULL') $where = "and b.org_id_1_salary = $search_org_id_1 ";
	if($search_org_id && $search_org_id != 'NULL') $where_org = " and org_id = $search_org_id ";
	else $where_org = " and org_id is NULL ";

	$cmd = " select AL_CODE from PER_ASSESS_LEVEL where AM_CODE <> '1' $where_org order by AL_CODE DESC ";
	$count_data = $db_dpis->send_cmd($cmd);
	if (!$count_data) $where_org = " and org_id is NULL ";

	$array_xx = array('O'=>'o','K'=>'k','D'=>'d','M'=>'m');
	foreach($array_xx as $key => $value) {
		$cmd = " select AL_CODE from PER_ASSESS_LEVEL where AM_CODE <> '1' $where_org order by AL_CODE DESC ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) {	
			$temp_code = $data[AL_CODE];
			$tmp_arr = "code_".$value."_".$data[AL_CODE] ; 
				$code_arr = $$tmp_arr;
			$sf_code[$key][$temp_code] = $code_arr;
			$val_sf_code[$key][$temp_code] = "$temp_code,$code_arr";
		}	
	}

	$cmd = " select LEVEL_NO, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
					 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
					 from PER_LAYER where LAYER_TYPE = 0 and LAYER_NO = 0 ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) {
		$xx_level_no = $data[LEVEL_NO];
		$SALARY_POINT[$xx_level_no][mid]  = $data[LAYER_SALARY_MIDPOINT];
		$SALARY_POINT[$xx_level_no][mid1] = $data[LAYER_SALARY_MIDPOINT1];
		$SALARY_POINT[$xx_level_no][mid2] = $data[LAYER_SALARY_MIDPOINT2];
		$SALARY_POINT[$xx_level_no][full]  = $data[LAYER_SALARY_FULL];
		$SALARY_POINT[$xx_level_no][extra]  = $data[LAYER_EXTRA_MIDPOINT];
		$SALARY_POINT[$xx_level_no][extra1] = $data[LAYER_EXTRA_MIDPOINT1];
		$SALARY_POINT[$xx_level_no][extra2] = $data[LAYER_EXTRA_MIDPOINT2];
	}

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if($CP_CYCLE == 1){
		$arr_temp = explode("/", $CP_START_DATE_1);
		$CP_START_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];

		$arr_temp = explode("/", $CP_END_DATE_1);
		$CP_END_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	}elseif($CP_CYCLE == 2){
		$arr_temp = explode("/", $CP_START_DATE_2);
		$CP_START_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];

		$arr_temp = explode("/", $CP_END_DATE_2);
		$CP_END_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	} // end if

//	echo $command . "+++";  
	if($command == "ADD"){
		$cmd = " select 	CP_ID,CP_NAME from PER_COMPENSATION_TEST	where CP_ID = $CP_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " select max(CP_ID) as MAX_ID from PER_COMPENSATION_TEST ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CP_ID = $data[MAX_ID] + 1;
			if (!$search_org_id) $search_org_id = "NULL";
			
			$HOLD_SALARY_STR = str_replace(",","",$HOLD_SALARY);
			//$QTY_KEY_SUM_STR = implode(':',$QTY_KEY_SUM);
			foreach($QTY_KEY_SUM as $key => $value) $QTY_KEY_SUM_STR .= "$key=$value:";
			$QTY_KEY_SUM_STR = substr($QTY_KEY_SUM_STR,0,-1);
			
			//$SALARY_KEY_SUM_STR = implode(':',$SALARY_KEY_SUM);
			foreach($SALARY_KEY_SUM as $key => $value) $SALARY_KEY_SUM_STR .= "$key=$value:";
			$SALARY_KEY_SUM_STR = substr($SALARY_KEY_SUM_STR,0,-1);
						
			// insert header
			$cmd = " insert into PER_COMPENSATION_TEST (CP_ID, CP_NAME, CP_DATE, CP_CYCLE, CP_START_DATE, 
							  CP_END_DATE,	CP_BUDGET, ORG_ID, DEPARTMENT_ID, SF_CODE_O, SF_CODE_K, SF_CODE_D, SF_CODE_M, 
							  CP_RESULT, O_QTY, O_SALARY, K_QTY, K_SALARY, D_QTY, D_SALARY, M_QTY, M_SALARY, SUM_QTY, 
							  SUM_SALARY, HOLD_SALARY, UPDATE_USER, UPDATE_DATE) 
							  values	($CP_ID, '$CP_NAME', '$UPDATE_DATE', $CP_CYCLE, '$CP_START_DATE', '$CP_END_DATE', $CP_BUDGET, 
							  $search_org_id, $DEPARTMENT_ID, '".$SF_CODE_O . "#". implode(':',$val_sf_code[O])."',
							  '".$SF_CODE_K . "#". implode(':',$val_sf_code[K])."', '".$SF_CODE_D . "#". implode(':',$val_sf_code[D])."',
							  '".$SF_CODE_M . "#". implode(':',$val_sf_code[M])."', $CP_RESULT, '$O_QTY_TOTAL', '$O_SALARY_TOTAL', 
							  '$K_QTY_TOTAL', '$K_SALARY_TOTAL', '$D_QTY_TOTAL', '$D_SALARY_TOTAL', '$M_QTY_TOTAL', 
							  '$M_SALARY_TOTAL', '$QTY_KEY_SUM_STR', '$SALARY_KEY_SUM_STR', '$HOLD_SALARY', $SESS_USERID, 
							  '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$CP_ID." : ".trim($CP_NAME)." : ".$CP_CYCLE." : ".$CP_BUDGET."]");

			$cmd = " select 	AL_CODE, AL_NAME , AL_POINT_MIN , AL_POINT_MAX, AL_PERCENT 
							  from PER_ASSESS_LEVEL
							  where AM_CODE <> '1' $where_org 
							  order by AL_CODE DESC ";
			$db_dpis->send_cmd($cmd);
			while ($data = $db_dpis->get_array()) {
				$AL_CODE = $data[AL_CODE];
				$AL_CODE_JOIN .= $AL_CODE . ":";
				$AL_NAME[$AL_CODE] = $data[AL_NAME];
				$AL[$AL_CODE][MIN] = $data[AL_POINT_MIN];
				$AL[$AL_CODE][MAX] = $data[AL_POINT_MAX];
				$AL[$AL_CODE][PERCENT] = $data[AL_PERCENT];
			}
			// insert dtl
			$cmd = " select max(CD_ID) as MAX_ID from PER_COMPENSATION_TEST_DTL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CD_ID = $data[MAX_ID] + 1;
			
			$cmd = " select a.PER_ID, PER_SALARY, LEVEL_NO, TOTAL_SCORE, POS_ID 
							from PER_PERSONAL a, PER_KPI_FORM b
                            where a.PER_ID = b.PER_ID and KF_START_DATE = '$CP_START_DATE' 
							and b.DEPARTMENT_ID = '$DEPARTMENT_ID' $where
							and KF_END_DATE =  '$CP_END_DATE' and LEVEL_NO in ('".$_select_level_no_str."') 
							order by a.PER_ID " ;
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) {
				$PER_ID = $data[PER_ID];
				$PER_SALARY = $data[PER_SALARY];
				$LEVEL_NO = $data[LEVEL_NO];
				$TOTAL_SCORE = $data[TOTAL_SCORE];
				$POS_ID = $data[POS_ID];
				$tmp_PER_SALARY = $PER_SALARY;

				$cmd = " select LAYER_TYPE from PER_POSITION a, PER_LINE b where POS_ID = $POS_ID and a.PL_CODE = b.PL_CODE ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$LAYER_TYPE = $data1[LAYER_TYPE] + 0;
	
				$cmd = " select LAYER_SALARY_MAX from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$LAYER_SALARY_MAX = $data1[LAYER_SALARY_MAX] + 0;
	
				foreach($AL as $al_code => $point_range) {
					if($TOTAL_SCORE >= $point_range[MIN] && $TOTAL_SCORE <= $point_range[MAX])	{
							$temp_level = $LEVEL_NO[0];
							
							if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && 
								$PER_SALARY <= $SALARY_POINT[$LEVEL_NO][full]) {
								$LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][full];
								$SALARY_POINT_MID = $SALARY_POINT[$LEVEL_NO][extra];
								$SALARY_POINT_MID1 = $SALARY_POINT[$LEVEL_NO][extra1];
								$SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][extra2];
							} else {
								$SALARY_POINT_MID = $SALARY_POINT[$LEVEL_NO][mid];
								$SALARY_POINT_MID1 = $SALARY_POINT[$LEVEL_NO][mid1];
								$SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][mid2];
							}

							if($SALARY_POINT_MID > $PER_SALARY) {
								$PER_SALARY = $SALARY_POINT_MID1;
							} else {
								$PER_SALARY = $SALARY_POINT_MID2;
							}
							
							$CD_PERCENT = $sf_code[$temp_level][$al_code];
							$CD_SALARY = (ceil($PER_SALARY * (($sf_code[$temp_level][$al_code]/100))  /10))*10 ;
							$CD_EXTRA_SALARY = 0;
							if ($tmp_PER_SALARY + $CD_SALARY > $LAYER_SALARY_MAX) {
								$CD_EXTRA_SALARY = $tmp_PER_SALARY + $CD_SALARY - $LAYER_SALARY_MAX;
								$CD_SALARY = $CD_SALARY - $CD_EXTRA_SALARY;
							}
							
							$cmd = " insert into PER_COMPENSATION_TEST_DTL (CD_ID, CP_ID, PER_ID, LEVEL_NO, AL_CODE, CD_SALARY, 
											CD_PERCENT, CD_EXTRA_SALARY, UPDATE_USER, UPDATE_DATE) 
											values ($CD_ID, $CP_ID, $PER_ID, '$LEVEL_NO','$al_code', $CD_SALARY, $CD_PERCENT, 
											$CD_EXTRA_SALARY, $SESS_USERID, '$UPDATE_DATE') ";
							$db_dpis1->send_cmd($cmd);
//							$db_dpis1->show_error();
							$CD_ID++;
					}
				}
			}    

		}else{
			$data = $db_dpis->get_array();
			$CP_NAME = $data[CP_NAME];
			
			$err_text = "ชื่อข้อมูลซ้ำ";
		} // endif
	}

	if($command == "UPDATE" && trim($CP_ID) ){
			$HOLD_SALARY_STR = str_replace(",","",$HOLD_SALARY);
			//$QTY_KEY_SUM_STR = implode(':',$QTY_KEY_SUM);
			foreach($QTY_KEY_SUM as $key => $value) $QTY_KEY_SUM_STR .= "$key=$value:";
			$QTY_KEY_SUM_STR = substr($QTY_KEY_SUM_STR,0,-1);
			
			//$SALARY_KEY_SUM_STR = implode(':',$SALARY_KEY_SUM);
			foreach($SALARY_KEY_SUM as $key => $value) $SALARY_KEY_SUM_STR .= "$key=$value:";
			$SALARY_KEY_SUM_STR = substr($SALARY_KEY_SUM_STR,0,-1);
			
			$cmd = " update PER_COMPENSATION_TEST set 
								CP_CYCLE=$CP_CYCLE,
								CP_NAME='$CP_NAME', 
								CP_BUDGET=$CP_BUDGET, 
								CP_RESULT=$CP_RESULT, 
								SF_CODE_O='".$SF_CODE_O . "#". implode(':',$val_sf_code[O])."',
								SF_CODE_K='".$SF_CODE_K . "#". implode(':',$val_sf_code[K])."',
								SF_CODE_D='".$SF_CODE_D . "#". implode(':',$val_sf_code[D])."',
								SF_CODE_M='".$SF_CODE_M . "#". implode(':',$val_sf_code[M])."',
								O_QTY='$O_QTY_TOTAL',
								O_SALARY='$O_SALARY_TOTAL',
								K_QTY='$K_QTY_TOTAL',
								K_SALARY='$K_SALARY_TOTAL',
								D_QTY='$D_QTY_TOTAL',
								D_SALARY='$D_SALARY_TOTAL',
								M_QTY='$M_QTY_TOTAL',
								M_SALARY='$M_SALARY_TOTAL',
								SUM_QTY='$QTY_KEY_SUM_STR',
								SUM_SALARY='$SALARY_KEY_SUM_STR',
								HOLD_SALARY='$HOLD_SALARY_STR',
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where CP_ID=$CP_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$CP_ID." : ".trim($CP_NAME)." : ".$CP_CYCLE." : ".$CP_BUDGET."]");
			
			// insert DTL
			$cmd = " select 	AL_CODE, AL_NAME , AL_POINT_MIN , AL_POINT_MAX, AL_PERCENT 
							  from PER_ASSESS_LEVEL 
							  where AM_CODE <> '1' $where_org 
							  order by AL_CODE DESC ";
			$db_dpis->send_cmd($cmd);
			while ($data = $db_dpis->get_array()) {
				$AL_CODE = $data[AL_CODE];
				$AL_CODE_JOIN .= $AL_CODE . ":";
				$AL_NAME[$AL_CODE] = $data[AL_NAME];
				$AL[$AL_CODE][MIN] = $data[AL_POINT_MIN];
				$AL[$AL_CODE][MAX] = $data[AL_POINT_MAX];
				$AL[$AL_CODE][PERCENT] = $data[AL_PERCENT];
			}
			// insert dtl
			$cmd = " delete from PER_COMPENSATION_TEST_DTL where CP_ID = $CP_ID ";
			$db_dpis1->send_cmd($cmd);
			
			$cmd = " select max(CD_ID) as MAX_ID from PER_COMPENSATION_TEST_DTL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CD_ID = $data[MAX_ID] + 1;
			
			$cmd = " select a.PER_ID, PER_SALARY, LEVEL_NO, TOTAL_SCORE, POS_ID 
							from PER_PERSONAL a, PER_KPI_FORM b
                            where a.PER_ID = b.PER_ID and KF_START_DATE = '$CP_START_DATE' 
							and b.DEPARTMENT_ID = '$DEPARTMENT_ID' $where
							and KF_END_DATE =  '$CP_END_DATE' and LEVEL_NO in ('".$_select_level_no_str."') 
							order by a.PER_ID " ;
			//echo $cmd . "<br>";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()) {
				$PER_ID = $data[PER_ID];
				$PER_SALARY = $data[PER_SALARY];
				$LEVEL_NO = $data[LEVEL_NO];
				$TOTAL_SCORE = $data[TOTAL_SCORE];
				$POS_ID = $data[POS_ID];
				$CD_SALARY = $PER_SALARY;

				$cmd = " select LAYER_TYPE from PER_POSITION a, PER_LINE b where POS_ID = $POS_ID and a.PL_CODE = b.PL_CODE ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$LAYER_TYPE = $data1[LAYER_TYPE] + 0;
	
				$cmd = " select LAYER_SALARY_MAX from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$LAYER_SALARY_MAX = $data1[LAYER_SALARY_MAX] + 0;
	
				foreach($AL as $al_code => $point_range) {
					if($TOTAL_SCORE >= $point_range[MIN] && $TOTAL_SCORE <= $point_range[MAX])	{
							$temp_level = $LEVEL_NO[0];
							
							if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && 
								$PER_SALARY <= $SALARY_POINT[$LEVEL_NO][full]) {
								$LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][full];
								$SALARY_POINT_MID = $SALARY_POINT[$LEVEL_NO][extra];
								$SALARY_POINT_MID1 = $SALARY_POINT[$LEVEL_NO][extra1];
								$SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][extra2];
							} else {
								$SALARY_POINT_MID = $SALARY_POINT[$LEVEL_NO][mid];
								$SALARY_POINT_MID1 = $SALARY_POINT[$LEVEL_NO][mid1];
								$SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][mid2];
							}

							if($SALARY_POINT_MID > $PER_SALARY) {
								$PER_SALARY = $SALARY_POINT_MID1;
							} else {
								$PER_SALARY = $SALARY_POINT_MID2;
							}
							
							$CD_PERCENT = $sf_code[$temp_level][$al_code];
							$CD_SALARY = (ceil($PER_SALARY * (($sf_code[$temp_level][$al_code]/100))  /10))*10 ;
							$CD_EXTRA_SALARY = 0;
							if ($tmp_PER_SALARY + $CD_SALARY > $LAYER_SALARY_MAX) {
								$CD_EXTRA_SALARY = $tmp_PER_SALARY + $CD_SALARY - $LAYER_SALARY_MAX;
								$CD_SALARY = $CD_SALARY - $CD_EXTRA_SALARY;
							}
							
							$cmd = " insert into PER_COMPENSATION_TEST_DTL (CD_ID, CP_ID, PER_ID, LEVEL_NO, AL_CODE, CD_SALARY, 
											CD_PERCENT, CD_EXTRA_SALARY, UPDATE_USER, UPDATE_DATE) 
											values ($CD_ID, $CP_ID, $PER_ID, '$LEVEL_NO','$al_code', $CD_SALARY, $CD_PERCENT, 
											$CD_EXTRA_SALARY, '$SESS_USERID', '$UPDATE_DATE') ";
							$db_dpis1->send_cmd($cmd);
//							$db_dpis1->show_error();
							$CD_ID++;
					}
				}

			}    
			
	}
	
// ============================================================
	// เมื่อมีการยืนยันแบบทดสอบการบริหารค่าตอบแทน
	if( $command == "CONFIRM" && trim($CP_ID) ) {
		$cmd = " select 	CP_START_DATE, CP_END_DATE, CP_CYCLE, ORG_ID 
						  from PER_COMPENSATION_TEST where	CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_START_DATE = $data[CP_START_DATE];
		$CP_END_DATE = $data[CP_END_DATE];
		$CP_CYCLE = $data[CP_CYCLE];
		$ORG_ID = $data[ORG_ID];

		$cmd = " update PER_COMPENSATION_TEST set  
						  CP_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						  where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);		
		//$db_dpis->show_error();

		$cmd = " update PER_COMPENSATION_TEST set CP_CONFIRM=0
						  where CP_ID!=$CP_ID and CP_START_DATE='$CP_START_DATE' and CP_END_DATE = '$CP_END_DATE' and
						  CP_CYCLE = $CP_CYCLE and ORG_ID = $ORG_ID ";
		$db_dpis->send_cmd($cmd);		
		//$db_dpis->show_error();
	}		// 	if( $command == "CONFIRM" && trim($CP_ID) ) 	
	
	if($command == "DELETE" && trim($CP_ID)){
		$cmd = " delete from PER_COMPENSATION_TEST_DTL where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_COMPENSATION_TEST where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [". $CP_ID ." : ".$CP_NAME." ".$CP_CYCLE." ".$CP_BUDGET."]");
	}
	
	if($UPD || $VIEW){
		
		$cmd = " select 	CP_NAME, CP_DATE, CP_CYCLE, CP_BUDGET, SF_CODE_O, SF_CODE_K, SF_CODE_D, SF_CODE_M, ORG_ID
				  		 from 		PER_COMPENSATION_TEST
						 where 	CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_NAME = $data[CP_NAME];
		$CP_DATE = $data[CP_DATE];
		$CP_CYCLE = $data[CP_CYCLE];
		$CP_BUDGET = $data[CP_BUDGET];
		$SF_CODE_O = $data[SF_CODE_O];
		$SF_CODE_K = $data[SF_CODE_K];
		$SF_CODE_D = $data[SF_CODE_D];
		$SF_CODE_M = $data[SF_CODE_M];
		$search_org_id = $data[ORG_ID];
		if ($search_org_id) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $search_org_id ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$search_org_name = $data1[ORG_NAME];
		}
		unset($_select_level_no);
		$cmd = " select 	DISTINCT LEVEL_NO from PER_COMPENSATION_TEST_DTL where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) {
			$_select_level_no[] = $data[LEVEL_NO];
		}
		
		//print_r($_select_level_no);
		// create init div 
		unset($init_al_code);unset($init_al_name);
		$cmd = " select AL_CODE, AL_NAME from PER_ASSESS_LEVEL where AM_CODE <> '1' $where_org order by AL_CODE DESC ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) {
			$init_al_code[] = $data[AL_CODE] . ",";
			$init_al_name[] =  "<input name=code[]_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='";
		}

		list($SF_CODE_O,$list_code_o) = explode('#',$SF_CODE_O);
		list($SF_CODE_K,$list_code_k) = explode('#',$SF_CODE_K);
		list($SF_CODE_D,$list_code_d) = explode('#',$SF_CODE_D);
		list($SF_CODE_M,$list_code_m) = explode('#',$SF_CODE_M);

		$INIT_O = $list_code_o . ":";
		$INIT_K = $list_code_k . ":";
		$INIT_D = $list_code_d . ":";
		$INIT_M = $list_code_m . ":";

		$temp_str = str_replace(":","'>  ",$INIT_O);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_o = str_replace("[]","_o",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_K);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_k = str_replace("[]","_k",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_D);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_d = str_replace("[]","_d",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_M);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_m = str_replace("[]","_m",$temp_1);

		$init_div_o = $temp_o;
		$init_div_k = $temp_k;
		$init_div_d = $temp_d;
		$init_div_m = $temp_m;
	} 
	//echo $Testing;
	if(!($UPD||$VIEW) && !$Testing){
		$_select_level_no = array("O1","O2","O3","O4","K1","K2","K3","K4","K5","D1","D2","M1","M2");
	}
	
	if(date("Y-m-d") <= date("Y")."-10-01") $CP_YEAR = date("Y") + 543;
	else $CP_YEAR = (date("Y") + 543) + 1;
	$CP_START_DATE_1 = "01/10/". ($CP_YEAR - 1);
	$CP_END_DATE_1 = "31/03/". $CP_YEAR;
	$CP_START_DATE_2 = "01/04/". $CP_YEAR;
	$CP_END_DATE_2 = "30/09/". $CP_YEAR;

	if( (!$UPD && !$DEL && !$VIEW && !$err_text && !$Testing) ){
		unset($O_QTY_TOTAL);
		unset($O_SALARY_TOTAL);
		unset($K_QTY_TOTAL);
		unset($K_SALARY_TOTAL);
		unset($D_QTY_TOTAL);
		unset($D_SALARY_TOTAL);
		unset($M_QTY_TOTAL);
		unset($M_SALARY_TOTAL);
		unset($QTY_KEY_SUM_STR);
		unset($SALARY_KEY_SUM_STR);
		
		unset($QTY_GRANDTOTAL);
		unset($SALARY_GRANDTOTAL);
		unset($CP_RESULT);
		
		unset($CP_CYCLE);
		unset($CP_ID);
		unset($CP_NAME);
		unset($CP_BUDGET);
		unset($SF_CODE_O);unset($SF_CODE_K);unset($SF_CODE_D);unset($SF_CODE_M);
		unset($_POST);
//		unset($search_org_id);
//		unset($search_org_name);
		unset($search_org_id_1);
		unset($search_org_name_1);
	} // end if
	
	// create init div 
	unset($init_al_code);unset($init_al_name);
	$cmd = " select AL_CODE, AL_NAME from PER_ASSESS_LEVEL where AM_CODE <> '1' $where_org order by AL_CODE DESC ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) {
		$init_al_code[] = $data[AL_CODE] . ",";
		$init_al_name[] = "<input name=code[]_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='";
	}
	
	//echo "$command  && $command  && $VIEW && $UPD && $Testing";
	
	if(($command != "ADD" && $command != "UPDATE" && $VIEW && $UPD) || $Testing) {
		$INIT_O = implode(':',$val_sf_code[O]) . ":";
		$INIT_K = implode(':',$val_sf_code[K]) . ":";
		$INIT_D = implode(':',$val_sf_code[D]) . ":";
		$INIT_M = implode(':',$val_sf_code[M]) . ":";
		//echo "$INIT_O $INIT_K $INIT_D $INIT_M <br>";
		
		$temp_str = str_replace(":","'>  ",$INIT_O);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_o = str_replace("[]","_o",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_K);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_k = str_replace("[]","_k",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_D);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_d = str_replace("[]","_d",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_M);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_m = str_replace("[]","_m",$temp_1);
		
		$init_div_o = $temp_o;
		$init_div_k = $temp_k;
		$init_div_d = $temp_d;
		$init_div_m = $temp_m;
		
	}

	// create temp div 
	$cmd = " select AL_CODE, AL_NAME, AL_PERCENT from PER_ASSESS_LEVEL where AM_CODE <> '1' $where_org order by AL_CODE DESC ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) {
//		$temp_al_code[] = $data[AL_CODE] . ",";
		$temp_xx_code = $data[AL_CODE];
		$temp_o = "<input name=code_o_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
		$temp_k = "<input name=code_k_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
		$temp_d = "<input name=code_d_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
		$temp_m = "<input name=code_m_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
		$div_sf_code_o[$temp_xx_code] = $temp_o;
		$div_sf_code_k[$temp_xx_code] = $temp_k;
		$div_sf_code_d[$temp_xx_code] = $temp_d;
		$div_sf_code_m[$temp_xx_code] = $temp_m;
	}
	
?>